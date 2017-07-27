<?php 
use Step\Functional\ReturnSteps;

$I = new ReturnSteps($scenario);
$I->wantTo('test restocking and rejecting returns');

//sign in as administrator
$I->amGoingTo('sign in as administrator');
$I->login('Administrator');

$I->amGoingTo('insert test records to return_log table');
$I->createTestData();

$data_ids = $I->getDataIds();

$I->amGoingTo('check for "In Transit" test record');
$I->seeRecord('return_log', array(
		'order_id'      => $data_ids['orders'],
        'order_item_id' => $data_ids['order_items'],
        'status'        => 'In Transit'
	));

$I->amGoingTo('restock the return');
$I->request('PUT', '/admin/return/' . $data_ids['return_log'], array('action' => 'restock'));
$I->seeRecord('return_log', array(
		'order_id'      => $data_ids['orders'],
        'order_item_id' => $data_ids['order_items'],
        'status'        => 'Restocked'
	));

$I->refreshReturnRecord();
$data_ids = $I->getDataIds();

$I->amGoingTo('reject the return');
$I->request('PUT', '/admin/return/' . $data_ids['return_log'], array('action' => 'reject'));
$I->seeRecord('return_log', array(
		'order_id'      => $data_ids['orders'],
        'order_item_id' => $data_ids['order_items'],
        'status'        => 'Rejected'
	));

$I->clearTestData();
$I->logout();