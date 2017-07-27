<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Bican\Roles\Traits\HasRoleAndPermission;


class Merchant extends BaseModel
{
    use SoftDeletes;
    
    protected $connection = 'tyreapi';
    protected $table = 'merchants';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function getDates()
    {
        return [];
    }

    public function ae()
	{
		return $this->belongsTo('App\Models\User', 'ae');
	}

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
}
