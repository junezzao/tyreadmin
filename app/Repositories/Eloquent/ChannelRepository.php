<?php namespace App\Repositories\Eloquent;

use App\Models\Channel;
use App\Repositories\Contracts\ChannelRepository as ChannelRepositoryInterface;
use App\Http\Traits\GuzzleClient;

class ChannelRepository extends Repository implements ChannelRepositoryInterface
{
	use GuzzleClient;

    /**
     * @param channel $model
     */
    public function __construct(Channel $model)
    {
        $this->model = $model;
    }

    public function find($id, $columns = array())
    {   
        return json_decode($this->getGuzzleClient(array(), 'channels/channel/'.$id)->getBody()->getContents());
    }

    public function all($columns = array())
    {   
        return json_decode($this->getGuzzleClient(array(), 'channels/channel')->getBody()->getContents());
    }

}