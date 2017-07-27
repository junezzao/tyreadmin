<?php
use Page\LoginPage;
use Step\Functional\UserSteps;


$I = new UserSteps($scenario);
$I->am('a registered user');
$I->wantTo('Merchant Admin manage merchant account details [HA-1058 : Rachel]');

$I->login('Merchant Admin');

$I->seeInCurrentUrl('/dashboard');
$I->click('Account Details');

// check that client accont is tied to a merchant account
$user = $I->grabRecord('App\Models\User', array('email' => $I->getUsername('Merchant Admin').'@hubwire.com'));
if(is_null($user['merchant_id'])){
	$I->seeInCurrentUrl('/dashboard');
	$I->see('Your account is not linked to any merchant account. Kindly contact your Account Manager or Customer Support for assistance.');
}else{
	$I->seeInCurrentUrl('/account_details');

	$I->see('Account Details');

	$I->fillField('name', 'Codeception Test');
	$I->fillField('address', 'Codeception test address');
	$I->fillField('contact', '01678998765');
	$I->fillField('email', '123@abc.com');
	$I->selectOption('form select[name=timezone]', 'Pacific/Midway');
	$I->selectOption('form select[name=currency]', 'MYR');
	$I->click('Update Account Details');

	$I->see('Your account details have been updated.');
}

$I->logout();
$I->dontSeeAuthentication();

