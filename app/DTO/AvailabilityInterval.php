<?php

namespace App\DTO;

use Carbon\Carbon;

/**
 * Simple value object representing an interval with start and end timestamps.
 */
class AvailabilityInterval
{
    public Carbon $start;

    public Carbon $end;

    /**
     * @param  string  $startIso  ISO-8601 or any Carbon-parsable datetime string
     * @param  int  $durationMinutes  Duration of interval in minutes
     */
    public function __construct(string $startIso, int $durationMinutes)
    {
        $this->start = Carbon::parse($startIso);
        $this->end = $this->start->copy()->addMinutes($durationMinutes);
    }
}
