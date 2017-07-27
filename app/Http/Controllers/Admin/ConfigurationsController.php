<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use App\Http\Traits\GuzzleClient;
use Session;

class ConfigurationsController extends AdminController
{
	use GuzzleClient;

	protected $headers = ['Name', 'Slug', 'Description', 'Status', 'Action'];
	protected $module;

	public function __construct()
	{
		//
	}

	public function index()
    {
    	$headers = $this->headers;
    	$modules = $this->getGuzzleClient(['type' => ''], 'modules');
    	
		$modules = json_decode($modules->getBody()->getContents(), true);
        for ($i = 0; $i < count($modules); $i++) {
            $action = ( $modules[$i]['status'] == 'Enabled' ? 'Disable' : 'Enable');
            $modules[$i]['action'] =  $action;
        }
		return view('admin.configurations.list', compact('headers', 'modules'));
    }

    public function getModuleInformation($slug)
    {
    	$module = $this->getGuzzleClient(['slug' => $slug], 'modules/getModuleDetails')->getBody()->getContents();
    	return json_decode($module);
    }

    public function getModulesStatus() {
    	$response = json_decode($this->getGuzzleClient(['type' => ''], 'modules')->getBody()->getContents());
    	$modules = [];

        foreach ($response as $module) {
            $modules[$module->slug] = array(
                    'name'      => $module->name,
                    'enabled'   => (strcasecmp($module->status, 'Enabled') == 0) ? true : false
                );
        }

    	return $modules;
    }

    public function disableModule(Request $request)
    {
        $slug = $request->input('slug');
        
        $response = json_decode($this->getGuzzleClient(['slug' => $slug], 'modules/disable')->getBody()->getContents());
        $status = ($response->module->enabled == true ? 'enabled' : 'disabled');

        $modules = $request->session()->get('modules');
        $modules[$slug]['enabled'] = 0;

        //$modules = array( array( 'name' => 'channels', 'enabled' => 0 ), array( 'name' => 'fulfillment', 'enabled' => 0 ), array( 'name' => 'thirdparty', 'enabled' => 0));
        $request->session()->forget('modules');
        $request->session()->put('modules', $modules);

        flash()->info('The '.$response->module->name.' module is now disabled.');
        return response()->json($status);
    }

    public function enableModule(Request $request)
    {
        $slug = $request->input('slug');
        
        $response = json_decode($this->getGuzzleClient(['slug' => $slug], 'modules/enable')->getBody()->getContents());
        $status = ($response->module->enabled == true ? 'enabled' : 'disabled');
        
        $modules = $request->session()->get('modules');
        $modules[$slug]['enabled'] = 1;

        $request->session()->forget('modules');
        $request->session()->put('modules', $modules);

        flash()->info('The '.$response->module->name.' module is now enabled.');
        return response()->json($status);
    }
}
