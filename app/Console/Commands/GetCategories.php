<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Traits\GuzzleClient;
use File;
use Carbon\Carbon;

class GetCategories extends Command
{
    use GuzzleClient;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GetCategories {channel : Enter the marketplace to pull categories.} {country?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve latest third party categories list';

    protected $categories_list;

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
        $channel = strtolower($this->argument('channel'));
        $country = $this->argument('country');
        
        if(empty($channel)){
            $this->info("Please specify the channel");
            return false;
        }
        $this->info("Running Category Update for channel : ". ucfirst($channel) . " " . strtoupper($country));
        $this->info('Start time: '.Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('H:i:s d-m-Y P'));
        $file = $channel . (!empty($country) ? '-'.$country : '');
        $filepath = config_path() .'/categories/' . $file . '.php';
        
        $response = $this->getGuzzleClient(array(), 'thirdparty/'.$channel.'/getCategories/'.$file);
	$this->info('Response Received: '.Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('H:i:s d-m-Y P'));
	if(empty($response) || $response->getStatusCode() != 200){
            $this->info('Error Encountered while getting categories ! '. print_r($response, true));
            return false;
        }
	
        $this->categories_list = json_decode($response->getBody()->getContents(), true);
        
        $categories = array();
	$this->info('Processing '.sizeof($this->categories_list).' categories : '.Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('H:i:s d-m-Y P'));
        $bar = $this->output->createProgressBar(count($this->categories_list));
	$count = 0 ;
	foreach ($this->categories_list as $cat) {
            $bar->advance();
	    $parent = array();
            if(array_key_exists("LeafCategory",$cat)){
                $name = $cat['CategoryName'];
                for($i = $cat['CategoryLevel']; $i >= 1; $i--){
                    // get parent category name
                    $parent_id = (empty($parent) ? $cat['CategoryParentID'] : $parent['CategoryParentID']);
                    $parent = $this->getParentCategory($parent_id);
                    $name = $parent['CategoryName'].'/'.$name;
                }
                $categories[$name] = $cat['CategoryID'];
            } 
        }
	$bar->finish();
	$this->info('');
	$this->info('Writing File: '.Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('H:i:s d-m-Y P'));
        $handle = fopen($filepath, "w");

        $content = "<?php\nreturn array(\n\"Select ".ucfirst($channel)." ".strtoupper($country)." Category\" => 0,\n";
        fwrite($handle, $content);
        foreach ($categories as $k => $v){
            $k = htmlentities($k);
            $content = "\"".$k."\" => ".$v.",\n";
            fwrite($handle, $content);
        }
        $content = " ); ";
        fwrite($handle, $content);

        fclose($handle);
        //\Log::info($categories);
        $this->info('Done updating '.ucfirst($channel).' '.strtoupper($country). ' categories.');
    }

    private function getParentCategory($category_id) {
        
        if($this->categories_list){
            foreach($this->categories_list as $category){
                if($category_id == $category['CategoryID']){
                    return $category;
                }
            }
        }
        return false;
    }

}
