<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Bican\Roles\Traits\HasRoleAndPermission;

class Channel extends BaseModel
{
    use SoftDeletes;
    
    protected $connection = 'tyreapi';
    protected $table = 'channels';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function getDates()
    {
        return [];
    }    
    public function channel_detail()
    {
        return $this->hasOne('App\Models\ChannelDetails','channel_id');
    }
}
