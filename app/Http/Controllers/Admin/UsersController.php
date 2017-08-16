<?php 

namespace App\Http\Controllers\Admin;

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

    public function index()
    {
        return view('admin.users.list');
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
        $users = json_decode($this->getGuzzleClient([], 'admin/users')->getBody()->getContents());

        $data = array();
        foreach ($users as $user) {
            $user = User::find($user->id);

            // only return users who are of lower level or return all users if the user is a super admin
            if (($this->admin->is('superadministrator') || ($this->admin->level() >= $user->level() && is_null($user->deleted_at)))) {
                $editButton = '';
                $deleteButton = '';

                if ($this->admin->can('edit.user') && $this->admin->id != $user->id) {
                    $editUrl = route('admin.users.edit', [$user->id]);
                    $editButton = Form::open(array('url'=> $editUrl, 'method' => 'get', 'class' => 'form-inline')) . '<button type="submit" class="btn btn-link no-padding">Edit</button>' . Form::close();
                }

                $data[] = [
                    "id"            => $user->id,
                    "name"          => $user->first_name . " " . $user->last_name,
                    "email"         => $user->email,
                    "contact_no"    => $user->contact_no,
                    "company_name"  => $user->company_name,
                    "status"        => $user->status,
                    "user_type"     => $user->category,
                    "actions"       => $editButton
                ];
            }
        }

        return json_encode(["data" => $data]);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function edit($id)
    {
        $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$id)->getBody()->getContents());
        if(is_null($user->deleted_at) && ($this->admin->is('superadministrator') || $this->admin->level() > $user->level)) {
            $data['user'] = $user;
            $data['statuses'] = $this->userRepo->getStatusList(null);
            $data['countryList'] = config('globals.countryList');
            $data['operationTypes'] = [
                'Tyre Service Centre' => 'Tyre Service Centre', 
                'Fleet Operation Team' => 'Fleet Operation Team'
            ];

            return view('admin.users.edit', $data);
        }
        else {
            flash()->error(trans('permissions.unauthorized'));
            return redirect()->route('admin.users.index');
        }
    }

    public function update(Request $request, $id)
    {
        $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$id)->getBody()->getContents());

        if ($this->admin->is('superadministrator') || ($this->admin->can('edit.user') && ($this->admin->level() > $user->level))) {
            $this->validate($request, [
                'first_name'        => 'required|max:255',
                'last_name'         => 'required|max:255',
                'contact_no'        => 'required|max:255',
                'company_name'      => 'required|max:255',
                'address_line_1'    => 'required|max:255',
                'address_line_2'    => 'sometimes|max:255',
                'address_city'      => 'required|max:255',
                'address_postcode'  => 'required|max:255',
                'address_state'     => 'required|max:255',
                'address_country'   => 'required|max:2',
                'operation_type'    => 'required|max:255',
                'status'            => 'required'
            ]);

            $inputs = array(
                'editUser'          => true,
                'email'             => $request->input('email'),
                'first_name'        => $request->input('first_name'),
                'last_name'         => $request->input('last_name'),
                'company_name'      => $request->input('company_name'),
                'contact_no'        => $request->input('contact_no'),
                'address_line_1'    => $request->input('address_line_1'),
                'address_line_2'    => $request->input('address_line_2'),
                'address_city'      => $request->input('address_city'),
                'address_postcode'  => $request->input('address_postcode'),
                'address_state'     => $request->input('address_state'),
                'address_country'   => $request->input('address_country'),
                'status'            => $request->input('status'),
                'operation_type'    => $request->input('operation_type')
            );

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
    }
}
