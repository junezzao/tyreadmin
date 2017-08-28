<?php 

namespace App\Http\Controllers;

use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;

class JobsheetController extends AdminController
{
    use GuzzleClient;

    protected $user;

    public function __construct()
    {
        $this->user = \Auth::user();
    }

    public function index()
    {
        $data = array();
        $data['templateUrl'] = 'templates/jobsheet.jpg';
        $data['templateFileName'] = 'pro_tyre_admin_jobsheet_template';

        return view('jobsheet.index', $data);
    }

    public function downloadTemplate(Request $request)
    {
        $link = $request->get('link');
        $filename = $request->get('filename');
        $ext = 'jpg';

        if (($handle = fopen($link, "r")) === FALSE) 
            return null;
        
        header('Content-Type: application/' . $ext);
        header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
        header('Pragma: no-cache');
        readfile($link);
    }
}
