<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Bican\Roles\Traits\HasRoleAndPermission;

class Brand extends BaseModel
{
    use SoftDeletes;
    
    protected $connection = 'tyreapi';
    protected $table = 'brands';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function getDates()
    {
        return [];
    }    
}
