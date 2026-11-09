<?php

namespace App\Services\Timezone;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TimezoneService
{

    // return date
    public function convertFromUTCToUserTimezone($time)
    {
        $timezone = $this->getUserTimezone();
        return Carbon::createFromFormat('Y-m-d H:i:s', $time, 'UTC')
        ->setTimezone($timezone)
        ->format('Y-m-d h:i a');
    }

    // store data
    public function convertFromUserTimezoneToUTC($time)
    {
        $timezone = $this->getUserTimezone();
        return Carbon::createFromFormat('Y-m-d H:i:s', $time, $timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
    }

    protected function getUserTimezone()
    {
        return Auth::user()->timezone;
    }

    public function convertTimeFormat($time, $from, $to, $format)
    {
        return Carbon::createFromFormat($format, $time, $from)->setTimezone($to);
    }
}
