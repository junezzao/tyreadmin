<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeLog extends BaseModel
{
    protected $table = 'changelogs';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
