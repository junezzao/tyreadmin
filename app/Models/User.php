<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Http\Traits\HasRoleAndPermission;
use Bican\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleAndPermissionContract
{
    use Authenticatable, CanResetPassword, HasRoleAndPermission;

    public static $statusUnverified = 'Unverified';
    public static $statusActive = 'Active';
    public static $statusInactive = 'Inactive';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'tyreapi';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'contact_no', 'status', 'operation_type', 'company_name', 'address_line_1', 'address_line_2', 'address_city', 'address_postcode', 'address_state', 'address_country'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     *
     * Relationships
     *
     */
    public function merchants()
    {
        return $this->hasMany('App\Models\Merchant', 'ae');
    }

    public function merchant()
    {
        return $this->belongsTo('App\Models\Merchant', 'merchant_id');
    }

    public function channel()
    {
        return $this->belongsToMany('App\Models\Channel')->withTimestamps();
    }

    /**
     *
     * Scopes
     *
    */
    public function scopeAE()
    {
        //return $query->with('roles')->where('role', 'accountexec');
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'Account Executive');
        })->get()->toArray();

        return $users;
    }

    public function scopeAEOE()
    {
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Account Executive', 'Operations Executive']);
        })->get()->toArray();

        return $users;
    }

    public function scopeWE()
    {
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'Warehouse Executive');
        })->get()->toArray();

        return $users;
    }

    /**
     * Custom functions
     */
    public static function statusList()
    {
        return [ static::$statusActive => static::$statusActive, static::$statusInactive => static::$statusInactive];
    }

    /*public static function merchantList($id)
    {
        $user = User::find($id);
        $merchants = $user->merchants();
        $list = [];
        foreach ($merchants as $merchant) {
            $list[] = [$merchant['slug'] => $merchant['name']];
        }

        return $list;
    }*/

    public static function aeList()
    {
        $ae = User::AE();
        $list = '';
        foreach ($ae as $a) {
            $list = array_add($list, $a['id'], $a['first_name'].' '.$a['last_name']);
        }
        
        return $list;
    }
}
