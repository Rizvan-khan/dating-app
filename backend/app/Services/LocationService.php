<?php

namespace App\Services;

class LocationService
{
    public function distanceFromBareilly(float $lat, float $lng): float
    {
        $center = config('bareilly.center');
        $earthKm = 6371;
        $dLat = deg2rad($lat - $center['lat']);
        $dLng = deg2rad($lng - $center['lng']);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($center['lat'])) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;

        return round($earthKm * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }

    public function isInsideBareilly(float $lat, float $lng): bool
    {
        return $this->distanceFromBareilly($lat, $lng) <= config('bareilly.radius_km', 40);
    }
}
