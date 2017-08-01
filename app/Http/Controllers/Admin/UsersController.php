<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Merchant;
use App\Repositories\Contracts\UserRepository;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Admin\AdminController;
use Form;
use Log;

class UsersController extends AdminController
{
    use GuzzleClient;

    protected $userRepo;
    protected $admin;

    public function __construct(UserRepository $userRepo)
    {
        $this->middleware('permission:view.user', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.user', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.user', ['only' => ['destroy']]);
        $this->userRepo = $userRepo;
        $this->admin = \Auth::user();
    }

    public function index($channel_id=null)
    {
        return view('admin.users.list', ['channel_id'=>$channel_id]);
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
            $user = User::find($user->id);

            if($user->id == 230) \Log::info(print_r($user, true));
            // only return users who are of lesser level or return all users if the user is a super admin
            if (($this->admin->is('superadministrator') || ($this->admin->level() >= $user->level() && is_null($user->deleted_at)))) {
                $editButton = '';
                $deleteButton = '';

                if (empty($user->deleted_at) && $this->admin->can('edit.user|delete.user') && $this->admin->id != $user->id) {
                    $editUrl = route('admin.users.edit', [$user->id]);
                    $deleteUrl = route('admin.users.destroy', [$user->id]);

                    $editButton = $this->admin->can('edit.user') ? Form::open(array('url'=> $editUrl, 'method' => 'get', 'class' => 'form-inline')) . '<button type="submit" class="btn btn-link no-padding">Edit</button>' . Form::close() : '';
                    //$editButton .= ($this->admin->can('edit.user') && $this->admin->can('delete.user')) ? ' | ' : '';
                    //$deleteButton = $this->admin->can('delete.user') ? Form::open(array('url'=> $deleteUrl, 'method' => 'delete', 'class' => 'form-inline')) . '<button type="submit" class="btn btn-link no-padding confirmation">Delete</button>' . Form::close() : '';
                }

                $dataArray = [
                    "id" => $user->id,
                    "name" => $user->first_name . " " . $user->last_name,
                    "email" => $user->email,
                    "contact_no" => $user->contact_no,
                    "company_name" => $user->company_name,
                    "status" => $user->status,
                    "user_type" => $user->category,
                    "actions" => $editButton
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
            'email'                => 'required|email|max:255|unique:tyreapi.users',
            'user_category'        => 'required|exists:tyreapi.roles,slug,status,Active',
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
            /*if ($this->admin->is('clientadmin')) {
                $data['merchants'][$this->admin->merchant->slug] = $this->admin->merchant->name;
            }
            else {
                $response = $this->getGuzzleClient(array(), 'admin/merchants');
                $merchants = json_decode($response->getBody()->getContents())->merchants;

                foreach ($merchants as $merchant) {
                    $data['merchants'][$merchant->slug] = $merchant->name;
                }
            }*/

            $data['timezones'] = $this->generate_timezone_list();
            $data['currencies'] = config('globals.currency_list');
            //$data['channels'] = json_decode($this->getGuzzleClient(array(), 'channels/channel')->getBody()->getContents());
            $data['channels'] = array();
            //$data['user_channels']  = $user->channels;
            $data['user_channels'] = array();

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
                'category'      => 'required|exists:tyreapi.roles,slug,status,Active',
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
}
