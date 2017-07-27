<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Bican\Roles\Traits\HasRoleAndPermission;


class Supplier extends BaseModel
{
    use SoftDeletes;
    
    protected $connection = 'tyreapi';
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function getDates()
    {
        return [];
    }

    public function merchant()
	{
		return $this->belongsTo('App\Models\Merchant', 'merchant_id');
	}

    
}
