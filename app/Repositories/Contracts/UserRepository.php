<?php 

namespace App\Repositories\Contracts;

interface UserRepository extends Repository
{
    public function getStatusList($prepend = ['' => ''], $toStr = false);
}
