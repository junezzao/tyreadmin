<?php namespace App\Repositories\Contracts;

interface UserRepository extends Repository
{
    public function getStatusList($prepend = ['' => ''], $toStr = false);

    public function getMerchantList($user_id, $prepend = ['' => ''], $toStr = false);

    public function getAEList($prepend = ['' => ''], $toStr = false);
}
