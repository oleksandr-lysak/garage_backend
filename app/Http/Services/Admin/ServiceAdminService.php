<?php

namespace App\Http\Services\Admin;

use App\Models\Master;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ServiceAdminService
{
    public function list(array $params): array
    {
        $sortBy = in_array(($params['sort_by'] ?? ''), ['name', 'masters_count'], true)
            ? $params['sort_by']
            : 'name';
        $sortDir = strtolower($params['sort_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        // Build aggregated query with counts
        $query = Service::query()
            ->leftJoin('master_services as ms', 'ms.service_id', '=', 'services.id')
            ->select('services.id', 'services.name', DB::raw('COUNT(ms.master_id) as masters_count'))
            ->groupBy('services.id', 'services.name');

        if (! empty($params['search'])) {
            $query->where('services.name', 'like', '%'.$params['search'].'%');
        }

        if ($sortBy === 'masters_count') {
            $query->orderBy(DB::raw('masters_count'), $sortDir);
        } else {
            $query->orderBy('services.name', $sortDir);
        }

        $services = $query->get();

        return [
            'items' => $services->map(fn ($s) => [
                'id' => (int) $s->id,
                'name' => (string) $s->name,
                'masters_count' => (int) $s->masters_count,
            ])->values(),
        ];
    }

    public function getDeletePreview(int $serviceId): array
    {
        $service = Service::findOrFail($serviceId);
        $masters = DB::table('master_services')
            ->join('masters', 'masters.id', '=', 'master_services.master_id')
            ->where('master_services.service_id', $serviceId)
            ->select('masters.id', 'masters.name')
            ->get();

        // Masters that only have this one service
        $singleServiceMasters = DB::table('master_services as ms')
            ->select('ms.master_id')
            ->where('ms.service_id', $serviceId)
            ->groupBy('ms.master_id')
            ->havingRaw('COUNT(*) = 1')
            ->pluck('master_id')
            ->toArray();

        $mastersToDelete = Master::whereIn('id', $singleServiceMasters)->get(['id', 'name']);

        return [
            'service' => ['id' => (int) $service->id, 'name' => (string) $service->name],
            'affected_masters_count' => (int) $masters->count(),
            'masters_to_detach' => $masters->map(fn ($m) => ['id' => (int) $m->id, 'name' => (string) $m->name])->values(),
            'masters_to_delete' => $mastersToDelete->map(fn ($m) => ['id' => (int) $m->id, 'name' => (string) $m->name])->values(),
        ];
    }

    public function getBulkDeletePreview(array $serviceIds): array
    {
        $serviceIds = array_values(array_unique(array_map('intval', $serviceIds)));
        if (empty($serviceIds)) {
            return [
                'services' => [],
                'affected_masters_count' => 0,
                'masters_to_delete' => [],
            ];
        }

        $services = Service::whereIn('id', $serviceIds)->get(['id', 'name'])
            ->map(fn ($s) => ['id' => (int) $s->id, 'name' => (string) $s->name])->values();

        // Distinct masters that have any of these services
        $affectedMasters = DB::table('master_services')
            ->whereIn('service_id', $serviceIds)
            ->distinct()
            ->pluck('master_id');

        if ($affectedMasters->isEmpty()) {
            return [
                'services' => $services,
                'affected_masters_count' => 0,
                'masters_to_delete' => [],
            ];
        }

        // Total service count per affected master
        $totalCounts = DB::table('master_services')
            ->select('master_id', DB::raw('COUNT(*) as total'))
            ->whereIn('master_id', $affectedMasters)
            ->groupBy('master_id')
            ->pluck('total', 'master_id');

        // Count of services to be removed per affected master
        $toRemoveCounts = DB::table('master_services')
            ->select('master_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('service_id', $serviceIds)
            ->whereIn('master_id', $affectedMasters)
            ->groupBy('master_id')
            ->pluck('cnt', 'master_id');

        $mastersToDeleteIds = [];
        foreach ($affectedMasters as $masterId) {
            $total = (int) ($totalCounts[$masterId] ?? 0);
            $remove = (int) ($toRemoveCounts[$masterId] ?? 0);
            if ($total > 0 && $total === $remove) {
                $mastersToDeleteIds[] = (int) $masterId;
            }
        }

        $mastersToDelete = Master::whereIn('id', $mastersToDeleteIds)->get(['id', 'name'])
            ->map(fn ($m) => ['id' => (int) $m->id, 'name' => (string) $m->name])->values();

        return [
            'services' => $services,
            'affected_masters_count' => (int) $affectedMasters->count(),
            'masters_to_delete' => $mastersToDelete,
        ];
    }

    public function deleteServiceAndCascade(int $serviceId): array
    {
        return DB::transaction(function () use ($serviceId) {
            $preview = $this->getDeletePreview($serviceId);

            // Delete masters that only had this service
            $masterIdsToDelete = collect($preview['masters_to_delete'])->pluck('id')->all();
            if (! empty($masterIdsToDelete)) {
                // Reuse MasterAdminService-like cleanup
                $masters = Master::with(['services', 'reviews'])->whereIn('id', $masterIdsToDelete)->get();
                foreach ($masters as $master) {
                    if (method_exists($master, 'reviews')) {
                        $master->reviews()->delete();
                    }
                    if (method_exists($master, 'appointments')) {
                        $master->appointments()->delete();
                    }
                    if (method_exists($master, 'galleryPhotos')) {
                        $master->galleryPhotos()->delete();
                    }
                    if (method_exists($master, 'services')) {
                        $master->services()->detach();
                    }
                    $master->delete();
                }
            }

            // Detach service from remaining masters
            DB::table('master_services')->where('service_id', $serviceId)->delete();

            // Delete the service
            Service::where('id', $serviceId)->delete();

            return [
                'deleted_service_id' => (int) $serviceId,
                'detached_from_masters' => (int) $preview['affected_masters_count'],
                'deleted_masters' => count($masterIdsToDelete),
            ];
        });
    }

    public function deleteServicesAndCascade(array $serviceIds): array
    {
        $serviceIds = array_values(array_unique(array_map('intval', $serviceIds)));
        return DB::transaction(function () use ($serviceIds) {
            $preview = $this->getBulkDeletePreview($serviceIds);

            // Masters to delete
            $masterIdsToDelete = collect($preview['masters_to_delete'])->pluck('id')->all();
            if (! empty($masterIdsToDelete)) {
                $masters = Master::with(['services', 'reviews'])->whereIn('id', $masterIdsToDelete)->get();
                foreach ($masters as $master) {
                    if (method_exists($master, 'reviews')) {
                        $master->reviews()->delete();
                    }
                    if (method_exists($master, 'appointments')) {
                        $master->appointments()->delete();
                    }
                    if (method_exists($master, 'galleryPhotos')) {
                        $master->galleryPhotos()->delete();
                    }
                    if (method_exists($master, 'services')) {
                        $master->services()->detach();
                    }
                    $master->delete();
                }
            }

            // Detach and delete services
            if (! empty($serviceIds)) {
                DB::table('master_services')->whereIn('service_id', $serviceIds)->delete();
                Service::whereIn('id', $serviceIds)->delete();
            }

            return [
                'deleted_service_ids' => $serviceIds,
                'detached_from_masters' => (int) $preview['affected_masters_count'],
                'deleted_masters' => count($masterIdsToDelete),
            ];
        });
    }
}
