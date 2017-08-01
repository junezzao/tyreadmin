<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Merchant;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\DataRepositoryContract as DataRepository;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Admin\AdminController;
use Form;
use Log;
use Excel;

class DataController extends AdminController
{
    use GuzzleClient;

    protected $userRepo;
    protected $admin;

    public function __construct(UserRepository $userRepo, DataRepository $dataRepo)
    {
        /* $this->middleware('permission:view.user', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.user', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.user', ['only' => ['destroy']]); */
        $this->userRepo = $userRepo;
        $this->dataRepo = $dataRepo;
        $this->admin = \Auth::user();
    }

    public function index()
    {
        $data = array();
        $data['templateUrl'] = 'templates/data.xlsx';

        $data['sheet'] = json_decode($this->getGuzzleClient(array(), 'data/sheet/'.$this->admin->id)->getBody()->getContents(), true);
        //\Log::info('data sheet... '.print_r($data['sheet'], true));

        return view('data.index', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    public function getTableData()
    {
        /*if ($this->admin->is('clientadmin')) {
            $users = User::with('merchant')->where('merchant_id', $this->admin->merchant->id)->get();
        }
        else {
            $users = User::with('merchant')->get();
        }*/

        $filters = request()->all();
        if($this->admin->is('clientadmin'))
        {
            $filters['merchant_id'] = $this->admin->merchant_id;
        }
        $filters['with_trashed'] = true;
        $users = json_decode($this->getGuzzleClient($filters, 'admin/users')->getBody()->getContents())->users;

        $data = array();

        foreach ($users as $user) {
            $user = User::with('merchant')->find($user->id);

            // only return users who are of lesser level or return all users if the user is a super admin
            if (($this->admin->is('superadministrator') || ($this->admin->level() >= $user->level() && is_null($user->deleted_at)))) {
                $editButton = '';
                $deleteButton = '';

                if (empty($user->deleted_at) && $this->admin->can('edit.user|delete.user') && $this->admin->id != $user->id) {
                    $editUrl = route('admin.users.edit', [$user->id]);
                    $deleteUrl = route('admin.users.destroy', [$user->id]);

                    $editButton = $this->admin->can('edit.user') ? Form::open(array('url'=> $editUrl, 'method' => 'get', 'class' => 'form-inline')) . '<button type="submit" class="btn btn-link no-padding">Edit</button>' . Form::close() : '';
                    $editButton .= ($this->admin->can('edit.user') && $this->admin->can('delete.user')) ? ' | ' : '';
                    $deleteButton = $this->admin->can('delete.user') ? Form::open(array('url'=> $deleteUrl, 'method' => 'delete', 'class' => 'form-inline')) . '<button type="submit" class="btn btn-link no-padding confirmation">Delete</button>' . Form::close() : '';
                }

                $dataArray = ["user_id" => $user->id,
                          "name" => $user->first_name . " " . $user->last_name,
                          "email" => $user->email,
                          "contact" => $user->contact_no,
                          "user_role" => $user->category,
                          "status" => $user->status,
                          "actions" => $editButton . $deleteButton,
                          "client_name" => (!empty($user->merchant_id) ? $user->merchant->name : ''),
                      ];

                $data[] = $dataArray;
            }
        }

        return json_encode(array("data" => $data));
    }

    public function create()
    {
        $roles = Role::select('slug', 'name')->where('status', '=', 'Active');
        if ($this->admin->level() < 100) {
            $roles = $roles->where('level', '<', $this->admin->level());
        }

        $roles = $roles->orderBy('level', 'desc')->get();

        $data['roles'] = array();
        foreach ($roles as $role) {
            $data['roles'][$role->slug] = $role->name;
        }

        $data['merchants'] = array();

        if ($this->admin->is('clientadmin')) {
            $data['selected_merchant'] = $this->admin->merchant->slug;
            $data['merchants'][$this->admin->merchant->slug] = $this->admin->merchant->name;
        } else {
            $response = $this->getGuzzleClient(array(), 'admin/merchants');
            $merchants = json_decode($response->getBody()->getContents())->merchants;

            foreach ($merchants as $merchant) {
                $data['merchants'][$merchant->slug] = $merchant->name;
            }
        }

        $data['timezones'] = $this->generate_timezone_list();
        $data['currencies'] = config('globals.currency_list');

        return view('admin.users.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, array(
            'first_name'        => 'required|max:255',
            'last_name'            => 'required|max:255',
            'email'                => 'required|email|max:255|unique:hapi.users',
            'user_category'        => 'required|exists:hapi.roles,slug,status,Active',
            'merchant'            => 'required_if:user_category,clientadmin,clientuser,mobilemerchant',
            'default_timezone'    => 'required_if:user_category,superadministrator,administrator,finance,accountexec,partner',
            'default_currency'    => 'required_if:user_category,superadministrator,administrator,finance,accountexec,partner'
        ));

        $inputs = $request->input();
        $inputs['url'] = config('app.url');

        $response = $this->postGuzzleClient($inputs, 'admin/users/create');

        if ($response->getStatusCode() == 200) {
            $message = 'User ' . $request->input('first_name') . ' ' . $request->input('last_name') . ' (' . $request->input('email') . ') has been successfully created.';

            flash()->success($message);
            return redirect()->route('admin.users.index');
        } else {
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$id)->getBody()->getContents());
        if (is_null($user->deleted_at) && ($this->admin->is('superadministrator') || $this->admin->level() > $user->level)) {
            $data['user'] = $user;
            $roles = Role::select('name', 'slug', 'status');

            if ($this->admin->level() < 100) {
                $roles = $roles->where('level', '<', $this->admin->level());
            }

            $roles = $roles->orderBy('status', 'asc')->orderBy('level', 'desc')->get();

            $data['roles'] = array();
            foreach ($roles as $role) {
                $data['roles'][$role->status][$role->slug] = $role->name;
            }

            $data['id'] = $id;
            $data['statuses'] = $this->userRepo->getStatusList(null);
            $data['merchants'] = array();
            if ($this->admin->is('clientadmin')) {
                $data['merchants'][$this->admin->merchant->slug] = $this->admin->merchant->name;
            }
            else {
                $response = $this->getGuzzleClient(array(), 'admin/merchants');
                $merchants = json_decode($response->getBody()->getContents())->merchants;

                foreach ($merchants as $merchant) {
                    $data['merchants'][$merchant->slug] = $merchant->name;
                }
            }

            $data['timezones'] = $this->generate_timezone_list();
            $data['currencies'] = config('globals.currency_list');
            $data['channels'] = json_decode($this->getGuzzleClient(array(), 'channels/channel')->getBody()->getContents());
            $data['user_channels']  = $user->channels;

            return view('admin.users.edit', $data);
        }
        else {
            flash()->error(trans('permissions.unauthorized'));
            return redirect('data');
        }
    }

    public function update(Request $request, $id)
    {
        $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$id)->getBody()->getContents());

        if ($this->admin->is('superadministrator') || ($this->admin->can('edit.user') && ($this->admin->level() > $user->level))) {
            $inputs = $request->all();
            $inputs['editUser'] = true;

            $this->validate($request, [
                'first_name'    => 'required|max:255',
                'last_name'     => 'required|max:255',
                'category'      => 'required|exists:hapi.roles,slug,status,Active',
                'merchant'      => 'required_if:category,clientadmin,clientuser,mobilemerchant',
                'timezone'      => 'required_if:category,superadministrator,administrator,finance,accountexec,partner,channelmanager',
                'currency'      => 'required_if:category,superadministrator,administrator,finance,accountexec,partner,channelmanager',
                'status'        => 'required'
            ]);

            $inputs = array(
                'editUser'      => true,
                'merchant'      => $request->input('merchant'),
                'first_name'    => $request->input('first_name'),
                'last_name'     => $request->input('last_name'),
                'email'         => $request->input('email'),
                'contact_no'    => $request->input('contact_no'),
                'address'       => $request->input('address'),
                'category'      => $request->input('category'),
                'timezone'      => $request->input('timezone'),
                'currency'      => $request->input('currency'),
                'status'        => $request->input('status')
            );

            if($request->input('category') == 'channelmanager')
                $inputs['channels'] = $request->input('merchant_id');

            $response = json_decode($this->putGuzzleClient($inputs, 'admin/users/'.$id)->getBody()->getContents());
            if ($response->success) {
                flash()->success('User '.$inputs['email'].' has been successfully updated.');
                return redirect()->back();
            } else {
                flash()->error('An error has occurred while editing user '.$inputs['email'].'.');
                return back()->withInput()->withErrors($response->errors);
            }
        } else {
            flash()->error(trans('permissions.unauthorized'));
            return redirect()->route('admin.users.index');
        }
    }

    public function destroy(Request $request, $id)
    {
        // make sure the user isn't deleting him/herself and has permission to delete this user
        $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$id)->getBody()->getContents());
        if ($this->admin->id != $id && ($this->admin->is('superadministrator') || ($this->admin->can('delete.user') && ($this->admin->level() > $user->level)))) {
            $response = json_decode($this->deleteGuzzleClient([], 'admin/users/'.$id)->getBody()->getContents());
            if ($response->success) {
                flash()->success('The user has been successfully deleted.');
            } else {
                flash()->error('An error has occurred while deleting user '.$user->email.'.');
            }
        } else {
            flash()->error(trans('permissions.unauthorized'));
        }
        return redirect()->route('admin.users.index');
    }

    public function downloadTemplate(Request $request)
    {
        $link = $request->get('link');
        $filename = $request->get('filename');
        $ext = 'xlsx';

        if (($handle = fopen($link, "r")) === FALSE) 
            return null;
        
        header('Content-Type: application/' . $ext);
        header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
        header('Pragma: no-cache');
        readfile($link);
    }

    // data sheet upload functions
    public function upload(Request $request)
    {
        if($request->hasFile('data_sheet')){
            set_time_limit(300);

            $file = $request->file('data_sheet');
            $extension = $file->getClientOriginalExtension();
            $allowed = array('xlsx');
            $return = array();
            if(!$file->isValid() || !in_array($extension,$allowed))
            {
                $return['success'] = false;
                $return['error'] = array('messages'=>array('File is invalid! <b>*.'.$extension.'</b>'));
                return json_encode($return);
            }
            $path = $file->getRealPath();

            $data = Excel::selectSheets('Sheet1')->load($path, function($reader) {})->get();
            //\Log::info('Data length '.count($data));

            $postData = array();
            $postData['filename'] = $file->getClientOriginalName();
            
            $count = 1;
            if(!empty($data) && $data->count()){
                foreach ($data->toArray() as $key => $v) {
                    if(!empty($v)){
                        // \Log::info('Line '.$count++.'... '.print_r($v, true));

                        $postData['items'][] = array(
                            'line_number'           => $v['ref'],
                            // 'jobsheet_date'      => !empty($v['date']) ? $v['date']->format('Y-m-d') : '',
                            'jobsheet_date'         => (array)$v['date'],
                            'jobsheet_no'           => $v['jobsheet_no'],
                            'inv_no'                => $v['invoice_no'],
                            'inv_amt'               => $v['invoice_amount'],
                            'jobsheet_type'         => strtoupper($v['yardbreakdown']),
                            'customer_name'         => $v['customer'],
                            'truck_no'              => $v['truck'],
                            'pm_no'                 => $v['pm'],
                            'trailer_no'            => $v['trailer'],
                            'odometer'              => $v['odometer'],
                            'position'              => $v['position'],
                            'in_attr'               => strtoupper($v['tyre_in_attribute']),
                            'in_price'              => $v['tyre_in_price'],
                            'in_size'               => $v['tyre_in_size'],
                            'in_brand'              => $v['tyre_in_brand'],
                            'in_pattern'            => $v['tyre_in_pattern'],
                            'in_retread_brand'      => $v['tyre_in_retread_brand'],
                            'in_retread_pattern'    => $v['tyre_in_retread_pattern'],
                            'in_serial_no'          => $v['tyre_in_serial_no'],
                            'in_job_card_no'        => $v['tyre_in_job_card_no'],
                            'out_reason'            => $v['tyre_out_reason'],
                            'out_size'              => $v['tyre_out_size'],
                            'out_brand'             => $v['tyre_out_brand'],
                            'out_pattern'           => $v['tyre_out_pattern'],
                            'out_retread_brand'     => $v['tyre_out_retread_brand'],
                            'out_retread_pattern'   => $v['tyre_out_retread_pattern'],
                            'out_serial_no'         => $v['tyre_out_serial_no'],
                            'out_job_card_no'       => $v['tyre_out_job_card_no'],
                            'out_rtd'               => $v['tyre_out_rtd']
                        );
                    }
                } 
            }

            $itemChunks = array_chunk($postData['items'], 1000, true);
            \DB::beginTransaction();
            $chunkSize = count($itemChunks);
            foreach($itemChunks as $index=>$itemChunk) {

                $postData['items'] = $itemChunk;
                $indicator = '';
                if($index == 0) {
                    $indicator = 'start';
                } elseif ($index+1 == $chunkSize) {
                    $indicator = 'end';
                }
                $postData['indicator'] = $indicator;
                $response = $this->dataRepo->create($postData);
                //$response = json_decode($this->postGuzzleClient($postData, 'data')->getBody()->getContents(), true);
                //break;
            }
            \DB::commit();

            /*if(isset($response['exceed_limit']) && $response['exceed_limit'] == true) {
                return ['success'=>false, 'exceed_limit'=>true];
            }
            
            if(empty($response['error'])){
                \Log::info('Sheet uploaded successfully.');
            }else{
                \Log::info('Sheet upload error... '.print_r($response['error'], true));
            }*/
            
            return ['success'=>true];
        }
    }

    public function list() {
        /*$rows = $this->dataRepo->withTrashAll();

        $data['data'] = $rows;

        return json_encode($data);*/

        $data = array();

        $data['data'] = json_decode($this->getGuzzleClient(array(), 'data/'.$this->admin->id)->getBody()->getContents(), true);

        //\Log::info(print_r($data, true));die();
        return json_encode($data);
    }

    public function printDiagnostic() {
        $data = array();

        $data['sheet'] = json_decode($this->getGuzzleClient(array(), 'data/sheet/'.$this->admin->id)->getBody()->getContents(), true);

        return view('data.diagnostic', $data);
    }
}
