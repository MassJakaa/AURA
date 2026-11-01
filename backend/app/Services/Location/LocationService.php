<?php

namespace App\Services\Location;

use App\Models\VirtualLocation;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{
    /**
     * Get all available virtual locations.
     */
    public function getAvailableLocations(): Collection
    {
        return VirtualLocation::where('is_available', true)->orderBy('name')->get();
    }

    /**
     * Find location by code.
     */
    public function findByCode(string $code): ?VirtualLocation
    {
        return VirtualLocation::where('code', $code)->first();
    }

    /**
     * Validate if an activity category is allowed at a location.
     */
    public function isActivityAllowed(VirtualLocation $location, string $activityCategory): bool
    {
        $allowed = $location->allowed_activities ?? [];
        if (empty($allowed)) {
            return true;
        }

        return in_array($activityCategory, $allowed, true);
    }
}
