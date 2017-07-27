<?php namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepository as BrandRepositoryInterface;
use App\Http\Traits\GuzzleClient;

class BrandRepository extends Repository implements BrandRepositoryInterface
{
	use GuzzleClient;

    /**
     * @param channel $model
     */
    public function __construct(Brand $model)
    {
        $this->model = $model;
    }

    public function all($columns = array())
    {   
        return json_decode($this->getGuzzleClient(array(), 'admin/brands')->getBody()->getContents());
    }

}