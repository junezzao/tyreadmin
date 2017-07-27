<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use PDO;
use App\Sales;
use App\Channels;
use App\Http\Traits\DocumentGeneration;

class GenerateBulkTaxInvoice extends Command
{
    use DocumentGeneration;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GenerateBulkTaxInvoice {startdate : Enter Y-m-d (2016-01-31) date in malaysia timezone.} {enddate : Enter Y-m-d (2016-01-31) date in malaysia timezone.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command allows you to generate bulk tax invoice for a channel type in a specific time period.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // retrieve all arguments
        $startdate = $this->argument('startdate');
        $enddate = $this->argument('enddate');

        $startdate_utc = Carbon::createFromFormat('Y-m-d H:i:s', $this->argument('startdate').' 00:00:00', 'Asia/Kuala_Lumpur')->setTimezone('UTC')->format('Y-m-d H:i:s');
        $enddate_utc = Carbon::createFromFormat('Y-m-d H:i:s', $this->argument('enddate').' 23:59:59', 'Asia/Kuala_Lumpur')->setTimezone('UTC')->format('Y-m-d H:i:s');
        //dd($startdate_utc.'--'.$enddate_utc);
        //
        $channels = array(4,8,12,15,19,20,22,27,28,29,33,34,35,36,37,38,39,40,41,42,49,51,52,54);
        //$channel = $this->argument('channel');
        $count = 0;
        foreach ($channels as $channel) {
            \Log::info("====== Begin generating tax invoice for channel ".$channel." ====== \r\nStart time: ".Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('H:i:s d-m-Y P'));
            $this->info('Start time: '.Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('H:i:s d-m-Y P'));
            
            /*
            $channel_types = config('globals.channel_type');
            $channel_type = '';
            foreach ($channel_types as $key => $value) {
                if($value == $channel)
                    $channel_type = $key;
            }
            */

            //$channel_id = array();
            //$channel_ids = DB::connection('mysql')->table('channels')->select('channel_id')->where('channel_name', '=', $channel)->get();
            //$channel_ids = Channels::select('channel_id')->where('channel_name', '=', $channel)->get();
            //foreach ($channel_ids as $ch){
            //    foreach($ch as $key => $value) {
            //        $channel_id[] = $value; 
            //    }
            //}

            $excludeStatus = array('Failed', 'Pending','Cancelled');
            
            $sales = DB::connection('mysql2')->table('sales')->select('sale_id')
                                             ->whereDate('created_at', '>=', $startdate_utc)
                                             ->whereDate('created_at', '<=', $enddate_utc)
                                             ->where('channel_id', '=', $channel)
                                             ->whereNotIn('sale_status', $excludeStatus)
                                             ->get();
            /*DB::connection('mysql2')->setFetchMode(PDO::FETCH_NUM);
            $sales = Sales::select('sale_id')
                     ->whereDate('created_at', '>=', $startdate_utc)
                     ->whereDate('created_at', '<=', $enddate_utc)
                     ->whereIn('channel_id', $channel_id)
                    //->whereNotIn('sale_status', $excludeStatus)
                    ->get()->toArray();
            DB::connection('mysql2')->setFetchMode(PDO::FETCH_OBJ);
            */
            
            foreach ($sales as $sale) {
                $count++;
                $this->generateTaxInvoice($sale->sale_id);
                \Log::info($sale->sale_id);
            }
            \Log::info("Successfully generated ".$count." tax invoice for all channel ".$channel." orders from ".$startdate." to ".$enddate.".");
        }
        
        \Log::info("End time: ".Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('H:i:s d-m-Y P')."\r\n====== End generating tax invoice for ".$channel.". ======\r\n");
        $this->info('End time: '.Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('H:i:s d-m-Y P'));
        $this->info('Total Tax Invoice Generated:'.$count);
    }
}
