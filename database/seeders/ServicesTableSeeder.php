<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Car Service & Repair
            'car_service',           // СТО - загальне обслуговування
            'car_repair',            // Ремонт автомобілів
            'engine_repair',         // Ремонт двигуна
            'transmission_repair',   // Ремонт трансмісії
            'electrical_repair',     // Електроремонт
            'diagnostics',           // Діагностика

            // Tire Services
            'tire_service',          // Шиномонтаж
            'tire_balancing',        // Балансування коліс
            'tire_alignment',        // Розвал-сходження

            // Oil Services
            'oil_change',            // Заміна масла

            // Additional Services
            'car_glass',             // Автоскло
            'car_audio',             // Автозвук
            'car_alarm',             // Автосигналізація
            'car_painting',          // Покраска автомобіля
            'car_body_repair',       // Кузовний ремонт
            'car_air_conditioning',  // Кондиціонування
        ];

        foreach ($services as $name) {
            Service::firstOrCreate(['name' => $name]);
        }
    }
}
