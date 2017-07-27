<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Bican\Roles\Traits\HasRoleAndPermission;

class Order extends BaseModel
{
    public static $statusCode = array(
        'Failed'        => 11,
        'Pending'       => 12,
        'New'           => 21,
        'Paid'          => 21,
        'Picking'       => 22,
        'Packing'       => 23,
        'ReadyToShip'   => 24,
        'Shipped'       => 31,
        'Completed'     => 32,
    );

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
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function getStatus($value)
    {
        return array_search($value, static::$statusCode);
    }

    public function getStatusCode($value)
    {
        return static::$statusCode[$value];
    }

    /**
     *
     * Relationships
     *
     */
    

    /**
     *
     * Scopes
     *
     */

    public function getStatusList($status)
    {   
        $statusArr = array();
        $array = static::$statusCode;
        //$currentKey = $status;
        //$statusArr = [$array[$status] => $status];
        $statusArr =  [$status => array_search($status, $array)];
        $nextStatus = '';

        
        $currentKey = key($array);
        while ($currentKey !== null) {
            if($currentKey == 'New')
                $next = next($array);
            if($currentKey == array_search($status, $array)){
                $next = next($array);
                break;
            }
            $next = next($array);
            $currentKey = key($array);
        }
        
        return array_add($statusArr, $next, array_search($next, $array));

    }

}