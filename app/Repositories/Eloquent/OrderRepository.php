<?php 

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\OrderRepository as OrderRepositoryInterface;
use App\Models\Order;
use App\Http\Traits\GuzzleClient;

class OrderRepository extends Repository implements OrderRepositoryInterface
{
	use GuzzleClient;

	/**
     * @param User $model
     */
    public function __construct(Order $model)
    {
        $this->model = $model;
        //$this->role = $role;
    }

    public function find($id, $columns = array())
    {
   		return json_decode($this->getGuzzleClient(array(), 'orders/'.$id)->getBody()->getContents()); 	
    }

    public function update(array $data, $id)
    {
        return json_decode($this->postGuzzleClient($data, 'orders/update/'.$id)->getBody()->getContents());
    }

    public function getItems($id)
    {
    	return json_decode($this->getGuzzleClient(array(), 'orders/getitems/'.$id)->getBody()->getContents());
    }

    public function getNotes($id)
    {
        return json_decode($this->getGuzzleClient(array(), 'orders/getnotes/'.$id)->getBody()->getContents());
    }

    public function getHistory($id)
    {
        return json_decode($this->getGuzzleClient(array(), 'orders/gethistory/'.$id)->getBody()->getContents());
    }

    public function getOrderMemberDetails($memberId)
    {
    	return json_decode($this->getGuzzleClient(array(), 'member/'.$memberId)->getBody()->getContents());
    }

    public function getPaidStatusList()
    {
    	return array(false => 'Unpaid', true => 'Paid');
    }

    public function getStatusList($status)
    {
        $list = $this->model->getStatusList($status);
        return $list;
    }

    public function cancelOrder($order_id)
    {
        return json_decode($this->postGuzzleClient(array(), 'fulfillment/order/cancel/'.$order_id)->getBody()->getContents());
    }

    public function create(array $data)
    {
        return $this->postGuzzleClient($data, 'orders/create');
    }

    public function getReturnsAndCancelledItems($order_id)
    {
        return json_decode($this->getGuzzleClient(array(), 'orders/get_return_log/'.$order_id)->getBody()->getContents());
    }

    public function getStatus($statusCode)
    {
        return $this->model->getStatus($statusCode);
    }

    public function createNote(array $data, $id)
    {
        return $this->postGuzzleClient($data, 'orders/'.$id.'/createnote');
    }

    public function hasShipped($statusCode)
    {
        return ($statusCode >= $this->model->getStatusCode('ReadyToShip') );
    }

    public function getPromotionCodes($orderId)
    {
        return json_decode($this->getGuzzleClient(array(), 'orders/get_promotion_codes/'.$orderId)->getBody()->getContents());
    }

    public function getOrderSheetInfo($orderId)
    {
        return json_decode($this->getGuzzleClient(array(), 'orders/get_order_sheet_info/'.$orderId)->getBody()->getContents());
    }

    public function getReturnSlipInfo($orderId)
    {
        return json_decode($this->getGuzzleClient(array(), 'orders/get_return_slip_info/'.$orderId)->getBody()->getContents());
    }
}