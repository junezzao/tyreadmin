<?php

namespace App\Http\Controllers;

use App\Http\Traits\GuzzleClient;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DateTimeZone;
use DateTime;
use Carbon\Carbon;
use App\Services\MediaService as MediaService;


abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function generate_timezone_list()
    {
        static $regions = array(
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        );

        $timezones = array();
        foreach ($regions as $region) {
            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
        }

        $timezone_offsets = array();
        foreach ($timezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
        }

		// sort timezone by offset
	    asort($timezone_offsets);
		$timezone_list = array();
	    foreach( $timezone_offsets as $timezone => $offset )
	    {
	        $offset_prefix = $offset < 0 ? '-' : '+';
	        $offset_formatted = gmdate( 'H:i', abs($offset) );
			$pretty_offset = "UTC${offset_prefix}${offset_formatted}";
			$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
	    }
		return $timezone_list;
	}

    protected function getImageWH($type='sm')
    {
        $products = array(
            'xl'=> array('width'=>800, 'height'=>1148),
            'lg'=>array('width'=>370, 'height'=>531),
            'md'=>array('width'=>230, 'height'=>330),
            'md-sm'=>array('width'=>160, 'height'=>230),
            'sm'=>array('width'=>110, 'height'=>158),
            'xs'=>array('width'=>55, 'height'=>79)
        );
        return $products[$type]['width'].'x'.$products[$type]['height'];
    }

    public function getImageWHHapi(){
        return json_decode($this->getGuzzleClient(array(), 'thirdparty/getImageDimensions')->getBody()->getContents(), true);
    }

    public function generateDateRange(Carbon $startDate, Carbon $endDate)
    {
        $dates = [];

        for($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    public function removeExtensionFromMediaURL($url, $key){
        $keyArray = explode('.', $key);

        if(count($keyArray) > 1){
            // pop the file extension from the array
            $extension = array_pop($keyArray);
        }

        // recombine the key string
        $key = implode('', $keyArray);

        // get the position of the key string in the URL
        $keyPos = strpos($url, $key);

        // remove the old key portion in the URL
        $url = substr($url, 0, $keyPos);

        // add in the new key into the URL
        $url = $url . $key;

        return $url;
    }

    public function removeExtensionFromKey($key){
        $keyArray = explode('.', $key);

        if(count($keyArray) > 1){
            // pop the file extension from the array
            $extension = array_pop($keyArray);
        }

        // recombine the key string
        $key = implode('', $keyArray);

        return $key;
    }

    public function trimHTML($str){
        // only works for <p> element
        return preg_replace("/<p[^>]*>[\s|&nbsp;|<br>]*<\/p>/", '', $str);
    }

    public function downloadFile($link, $filename, $ext) {
        if (($handle = fopen($link, "r")) === FALSE) 
            return null;
        
        header('Content-Type: application/' . $ext);
        header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
        header('Pragma: no-cache');
        readfile($link);
    }

    public function getProductSheetTemplate($type = 'create')
    {
        if($type == 'create'){
            $filename = 'new_product_form';
        }elseif($type == 'restock'){
            $filename = 'replenishment_form';
        }elseif($type == 'stock-out'){
            $filename = 'stock-out';
        }
        $s3 = new MediaService();
        $link = $s3->checkFileInS3("templates/".$filename.".csv");
        
        if($link){
            return $link;
        }else{
            return 'templates/'.$filename.'.csv';
        }
    }
}
