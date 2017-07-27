<?php namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepository as UserRepositoryInterface;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
        //$this->role = $role;
    }

    public function getStatusList($prepend = ['' => ''], $toStr = false)
    {
        $items = $this->model->statusList();

        return $this->getList($prepend, $toStr, $items);
    }

    public function getMerchantList($user_id, $prepend = ['' => ''], $toStr = false)
    {
        $items = $this->model->merchantList($user_id);
        
        return $this->getList($prepend, $toStr, $items);
    }

    public function getAEList($prepend = ['' => ''], $toStr = false)
    {
        $items = $this->model->aeList();
        
        return $this->getList($prepend, $toStr, $items);
    }
}
