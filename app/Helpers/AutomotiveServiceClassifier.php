<?php

namespace App\Helpers;

use App\Models\Service;

class AutomotiveServiceClassifier
{
    /**
     * Determine service id for an automotive service based on its name and types.
     */
    public static function guessServiceId(string $name, array $types = []): ?int
    {
        $nameLower = mb_strtolower($name);

        // Tire Services
        if (self::contains($nameLower, ['шиномонтаж', 'шини', 'колеса', 'tire', 'wheel', 'шиномонтаж', 'балансировка'])) {
            if (self::contains($nameLower, ['баланс', 'balancing'])) {
                return self::idByName('tire_balancing');
            }
            if (self::contains($nameLower, ['развал', 'сходження', 'alignment', 'сходження'])) {
                return self::idByName('tire_alignment');
            }
            return self::idByName('tire_service');
        }

        // Car Service & Repair
        if (self::contains($nameLower, ['сто', 'сервіс', 'service', 'ремонт', 'repair', 'техобслуговування'])) {
            if (self::contains($nameLower, ['двигун', 'engine', 'мотор'])) {
                return self::idByName('engine_repair');
            }
            if (self::contains($nameLower, ['трансмісія', 'transmission', 'коробка', 'gearbox'])) {
                return self::idByName('transmission_repair');
            }
            if (self::contains($nameLower, ['електрика', 'electrical', 'електро', 'electric'])) {
                return self::idByName('electrical_repair');
            }
            if (self::contains($nameLower, ['діагностика', 'diagnostics', 'комп\'ютерна'])) {
                return self::idByName('diagnostics');
            }
            if (self::contains($nameLower, ['ремонт', 'repair'])) {
                return self::idByName('car_repair');
            }
            return self::idByName('car_service');
        }

        // Oil Change
        if (self::contains($nameLower, ['масло', 'oil', 'заміна масла', 'oil change'])) {
            return self::idByName('oil_change');
        }

        // Car Glass
        if (self::contains($nameLower, ['скло', 'glass', 'лобове', 'вітрове', 'windshield'])) {
            return self::idByName('car_glass');
        }

        // Car Audio
        if (self::contains($nameLower, ['звук', 'audio', 'музика', 'music', 'динаміки', 'speakers'])) {
            return self::idByName('car_audio');
        }

        // Car Alarm
        if (self::contains($nameLower, ['сигналізація', 'alarm', 'охорона', 'security'])) {
            return self::idByName('car_alarm');
        }

        // Car Painting & Body Repair
        if (self::contains($nameLower, ['покраска', 'painting', 'фарбування', 'color'])) {
            return self::idByName('car_painting');
        }
        if (self::contains($nameLower, ['кузов', 'body', 'кузовний'])) {
            return self::idByName('car_body_repair');
        }

        // Car Air Conditioning
        if (self::contains($nameLower, ['кондиціонер', 'air conditioning', 'клімат', 'climate'])) {
            return self::idByName('car_air_conditioning');
        }

        // Default to general car service
        return self::idByName('car_service');
    }

    private static function idByName(string $name): ?int
    {
        return Service::where('name', $name)->value('id');
    }

    private static function contains(string $haystack, array $keywords): bool
    {
        foreach ($keywords as $k) {
            if (str_contains($haystack, $k)) {
                return true;
            }
        }
        return false;
    }
}
