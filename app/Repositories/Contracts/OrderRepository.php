<?php namespace App\Repositories\Contracts;

interface OrderRepository extends Repository
{
	public function getItems($id);

	public function getPaidStatusList();

	public function getOrderMemberDetails($memberId);	

	public function getStatusList($status);

	public function cancelOrder($order_id);

	public function getPromotionCodes($orderId);
}