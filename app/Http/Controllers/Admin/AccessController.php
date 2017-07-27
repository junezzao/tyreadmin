<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;

use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Models\User;

use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;

use Validator;
use Form;
use Log;

class AccessController extends AdminController
{
    use GuzzleClient;

    public function __construct()
    {
        $this->middleware('permission:view.roles', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.roles', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.roles', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.roles', ['only' => ['destroy']]);

        $this->admin = \Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::where('level', '<=', $this->admin->level())->get();

        foreach ($roles as $key => $role) {
            $roles[$key]->users_count = User::whereHas('roles', function ($q) use ($role) {
                                                    $q->where('roles.id', $role->id);
                                                })->count();

            if ($role->slug == 'superadministrator' || $this->admin->level() <= $role->level) {
                $roles[$key]->actions = '';
                continue;
            }

            $editUrl = route('admin.roles.edit', [$role->id]);
            $deleteUrl = route('admin.roles.destroy', [$role->id]);

            $editButton = $this->admin->can('edit.roles') ? '<a href="' . $editUrl . '" class="btn btn-link no-padding">Edit</a>' : '';
            $editButton .= ($this->admin->can('edit.roles') && $this->admin->can('delete.roles')) ? ' | ' : '';
            $deleteButton = $this->admin->can('delete.roles') ? Form::open(array('url'=> $deleteUrl, 'method' => 'delete', 'class' => 'form-inline')) . '<button type="submit" class="btn btn-link no-padding ' . (($role->status == 'Inactive') ? 'confirm-activate' : 'confirm-deactivate') . '">' . (($role->status == 'Inactive') ? 'Activate' : 'Deactivate') . '</button>' . Form::close() : '';

            $roles[$key]->actions = $editButton . $deleteButton;
        }

        $data['data'] = json_encode($roles->toArray());

        return view('admin.roles.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['statuses'] = array(
            'Active'    => 'Active',
            'Inactive'  => 'Inactive'
        );
        $data['permissions'] = $this->getPermissions();

        return view('admin.roles.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'name'          => 'required|max:255|unique:roles,name',
            'slug'          => 'required|max:255|unique:roles,slug',
            'level'         => 'required|integer|digits_between:1,' . ($this->admin->level() - 1),
            'status'        => 'required|in:Active,Inactive',
            'description'   => 'max:255'
        ));

        $inputs = $request->input();

        $role = Role::create(array(
            'name'          => $inputs['name'],
            'slug'          => $inputs['slug'],
            'level'         => $inputs['level'],
            'status'        => $inputs['status'],
            'description'   => !empty($inputs['description']) ? $inputs['description'] : ''
        ));

        $inputs['permission_ids'] = !empty($inputs['permission_ids']) ? $inputs['permission_ids'] : array();
        foreach ($inputs['permission_ids'] as $p) {
            $role->attachPermission($p);
        }

        flash()->success('New role ' . $role->name . ' has been successfully created.');
        return redirect()->route('admin.roles.index');
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
        $role = Role::find($id);

        if (is_null($role) || $role->slug == 'superadministrator' || $this->admin->level() <= $role->level) {
            flash()->error(trans('permissions.unauthorized'));
            return redirect()->route('admin.roles.index');
        }

        $data['statuses'] = array(
            'Active'    => 'Active',
            'Inactive'  => 'Inactive'
        );

        $data['role'] = $role;
        $data['permissions'] = $this->getPermissions();
        $data['role_permissions'] = array_column($role->permissions()->get()->toArray(), 'id');

        return view('admin.roles.edit', $data);
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
        $this->validate($request, array(
            'level'         => 'required|integer|digits_between:1,' . ($this->admin->level() - 1),
            'status'        => 'sometimes|in:Active,Inactive',
            'description'   => 'max:255'
        ));

        $inputs = $request->input();

        $role = Role::find($id);
        $role->level = $inputs['level'];
        if (!empty($inputs['status'])) {
            $role->status = $inputs['status'];

            if ($inputs['status'] == "Inactive") {
                User::whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })->update(['status' => 'Inactive']);
            }
        }
        $role->description = !empty($inputs['description']) ? $inputs['description'] : '';
        $role->save();

        $inputs['permission_ids'] = !empty($inputs['permission_ids']) ? $inputs['permission_ids'] : array();

        $currentPermissions = array_column($role->permissions()->get()->toArray(), 'id');
        $newPermissions = array_diff($inputs['permission_ids'], $currentPermissions);
        $deletedPermissions = array_diff($currentPermissions, $inputs['permission_ids']);

        if (count($newPermissions) > 0) {
            $role->permissions()->attach($newPermissions);
        }

        if (count($deletedPermissions) > 0) {
            $role->permissions()->detach($deletedPermissions);
        }

        flash()->success('Role ' . $role->name . ' has been successfully updated.');
        return redirect()->route('admin.roles.edit', $id);
    }

    /**
     * Deactivate role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (is_null($role) || $role->slug == 'superadministrator' || $this->admin->level() <= $role->level) {
            flash()->error(trans('permissions.unauthorized'));
            return redirect()->route('admin.roles.index');
        }

        $newStatus = $role->status == 'Active' ? 'Inactive' : 'Active';

        $role->status = $newStatus;
        $role->save();

        if ($newStatus == 'Inactive') {
            User::whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role->name);
            })->update(['status' => 'Inactive']);
        }

        flash()->success('The role ' . $role->name . (($newStatus == 'Inactive') ? ' and all its users has been deactivated.' : ' has been activated.'));
        return redirect()->route('admin.roles.index');
    }

    public function getPermissions() {
        // if ($this->admin->is('superadministrator')) {
        //     $permissions = Permission::get();
        // }
        // else {
        //     $permissions = Permission::whereNotIn('slug', ['create.roles', 'delete.roles'])->get();
        // }

        // only get permissions the current logged in user has
        $permissions = $this->admin->getPermissions();
        $data = array();

        foreach ($permissions as $permission) {
            $index = ucwords(substr($permission->name, (strpos($permission->name, ' ') + 1)));

            $data[$index][] = $permission->toArray();
        }

        ksort($data);
        return $data;
    }
}
