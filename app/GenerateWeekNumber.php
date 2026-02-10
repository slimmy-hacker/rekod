<?php

namespace App;

use Carbon\Carbon;

class GenerateWeekNumber
{
   
    public function __construct()
    {
        
    }

    
    public function weekId(string $date): string
    {
        $carbonDate = Carbon::parse($date);

        $isoYear = $carbonDate->isoWeekYear();
        $isoWeek = $carbonDate->isoWeek();


        $weekId = $isoYear . str_pad($isoWeek, 2, '0', STR_PAD_LEFT);

        return $weekId;
    }

    
    public function weekRangeFromId(string $weekId): array
    {
        
        $isoYear = substr($weekId, 0, 4);

       
        $isoWeek = substr($weekId, 4, 2);

        
        $startOfWeek = Carbon::now()
            ->setISODate($isoYear, (int)$isoWeek)
            ->startOfWeek(Carbon::MONDAY);

       
        $endOfWeek = (clone $startOfWeek)->endOfWeek(Carbon::SUNDAY);

        return [
            'start' => $startOfWeek->toDateString(),
            'end'   => $endOfWeek->toDateString(),
        ];
    }

}

