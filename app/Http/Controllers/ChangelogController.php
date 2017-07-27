<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\GuzzleClient;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ChangeLog;
use Form;
use Activity;
use PDF;
use Carbon\Carbon;

class ChangelogController extends Controller
{
    use GuzzleClient;
    protected $admin;

    public function __construct()
    {
        $this->middleware('permission:view.changelog', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.changelog', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.changelog', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.changelog', ['only' => ['destroy']]);
        $this->admin = \Auth::user();
       
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['changelogs'] = [];
        $data['admin'] = $this->admin;
        $data['changelogs'] = $this->getChangeLogs();
        return view('changelog.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('changelog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['content'] = $this->trimHTML($request['content']);
        
        $this->validate($request, array(
            'title'=>'required',
            'content'=>'required',
        ));
        
        $changelog = ChangeLog::create([
                        'title' => $request->input('title'),
                        'content' => $request->input('content'),
                    ]);

        Activity::log('Changelog ('.$changelog->id.') was created', $this->admin->id);
        
        flash()->success('The changelog was successfully created.');
        return redirect()->route('changelog.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data['changelog'] = ChangeLog::find($id);
        return view('changelog.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $request['content'] = $this->trimHTML($request['content']);

        $this->validate($request, array(
            'title'=>'required',
            'content'=>'required',
        ));
        
        $changelog = ChangeLog::find($id);
        $changelog->title = $request->input('title');
        $changelog->content = $request->input('content');
        $changelog->save();

        Activity::log('Changelog ('.$changelog->id.') was updated', $this->admin->id);
        
        flash()->success('The changelog was successfully updated.');
        return redirect()->route('changelog.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ChangeLog::destroy($id);
        Activity::log('Changelog ('.$id.') was deleted', $this->admin->id);
        flash()->success('The changelog was successfully deleted.');
        return redirect()->route('changelog.index');
    }

    private function sortByDate($a, $b)
    {
        $t1 = strtotime($a['created_at']);
        $t2 = strtotime($b['created_at']);
        return $t2 - $t1;
    }    

    private function getChangeLogs()
    {
        $data = array();
        ChangeLog::chunk(100, function($changelogs) use(&$data){
            foreach ($changelogs as $changelog) {
                $actions = '';

                if ($this->admin->is('superadministrator')) {
                    $actions = Form::open(array('role'=>'form', 'class'=>'form-inline', 'method' => 'GET')) . '<a href="'.route('changelog.edit', $changelog->id).'" class="btn btn-link no-padding">Edit</a>'.Form::close()." | ";

                    $actions .= Form::open(array('url' => route('changelog.destroy',$changelog->id), 'role'=>'form', 'class'=>'form-inline', 'method' => 'DELETE')) . '<button type="submit" class="btn btn-link no-padding confirmation">Delete</button>'.Form::close();
                }

                $data[] = [
                    "id" => $changelog->id,
                    "title" => $changelog->title,
                    "content" => $changelog->content,  
                    "created_at" => $changelog->created_at,        
                    "actions" => $actions,
                ];
            }
        });
        $hapiChangeLogs = json_decode($this->getGuzzleClient(array('type' => 'admin'), 'changelog_data')->getBody()->getContents());
        
        if(!empty($hapiChangeLogs->changelog)){
            foreach ($hapiChangeLogs->changelog as $hapiChangeLog) {
                $data[] = [
                    "id" => $hapiChangeLog->id,
                    "title" => 'API '.$hapiChangeLog->title,
                    "content" => $hapiChangeLog->content, 
                    "created_at" => $hapiChangeLog->created_at,
                    "actions" => '',
                ];
            }
        }
        

        usort($data, array($this, "sortByDate"));
        return $data;
    }

    public function export()
    {
        $data = $this->getChangeLogs();
        $pdf = PDF::loadView('changelog.export', compact('data'));
        return $pdf->download('Arc changelog '.Carbon::today()->format('Ymd').'.pdf');
    }
}
