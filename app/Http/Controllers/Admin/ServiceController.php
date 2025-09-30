<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminServiceDeleteRequest;
use App\Http\Resources\Admin\AdminServiceDeletePreviewResource;
use App\Http\Resources\Admin\AdminServiceListResource;
use App\Http\Resources\Admin\AdminServiceDeleteResponse;
use App\Http\Services\Admin\ServiceAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function __construct(private readonly ServiceAdminService $service)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Services/Index');
    }

    public function list(Request $request): JsonResponse
    {
        $result = $this->service->list($request->all());
        return response()->json(new AdminServiceListResource($result));
    }

    public function deletePreview(int $id): JsonResponse
    {
        $preview = $this->service->getDeletePreview($id);
        return response()->json(new AdminServiceDeletePreviewResource($preview));
    }

    public function destroy(int $id, AdminServiceDeleteRequest $request): JsonResponse
    {
        $summary = $this->service->deleteServiceAndCascade($id);
        return response()->json(new AdminServiceDeleteResponse($summary));
    }

    public function bulkDeletePreview(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        $preview = $this->service->getBulkDeletePreview($ids);
        return response()->json(new AdminServiceDeletePreviewResource($preview));
    }

    public function bulkDestroy(AdminServiceDeleteRequest $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        $summary = $this->service->deleteServicesAndCascade($ids);
        return response()->json(new AdminServiceDeleteResponse($summary));
    }
}
