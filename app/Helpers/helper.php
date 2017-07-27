<?php
namespace App\Helpers;

use Carbon\Carbon;
use Config;

class Helper {

	static function convertTimeToUTC($date, $format = 'Y-m-d H:i:s')
	{
		if(empty($date) || strtotime($date) == false) return $date;
	    return Carbon::createFromFormat('Y-m-d H:i:s', $date, $fromTimezone)->setTimezone('UTC')->format($format);
	}

	static function convertTimeToUserTimezone($date, $userTimezone, $format = 'Y-m-d H:i:s')
	{
		if(empty($date) || strtotime($date) == false) return $date;
	    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->setTimezone($userTimezone)->format($format);
	}
}