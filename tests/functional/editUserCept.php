<?php 
use Page\LoginPage;
use Step\Functional\UserSteps;

// use Illuminate\Foundation\Testing\DatabaseTransactions;

$I = new UserSteps($scenario);
$I->am('a registered user');
$I->wantTo('test the edit user function');
$I->amOnPage('/');
$user = factory('App\Models\User')->create();

$I->login('Super Administrator');

//$I->login($user->email, $user->password);
//$I->login('administrator');
$I->seeInCurrentUrl('/dashboard');
$I->click('User Management');
$I->click('Users');
$I->seeInCurrentUrl('/admin/users');
$I->amOnPage('/admin/users/'.$user->id.'/edit');
$I->see('Edit User');

// edit details
$I->seeInFormFields('form.edit-user', [
    'first_name' => $user->first_name,
    'last_name' => $user->last_name,
]);

$I->fillField('first_name', 'Ann Nee');
$I->fillField('last_name', 'Lau');
$I->fillField('contact_no', '322');
$I->fillField('address', '10 Pallet Town');
$I->selectOption('form select[name=timezone]', 'Pacific/Midway');
$I->selectOption('form select[name=currency]', 'MYR');
$I->selectOption('form select[name=category]', 'Administrator');
$I->click('Edit User');

$I->seeElement('.alert-success');
$I->seeInFormFields('form.edit-user', [
    'first_name' => 'Ann Nee',
    'last_name' => 'Lau',
    'contact_no' => '322',
    'address' => '10 Pallet Town',
    'category' => 'administrator'
]);

// test validation
$I->fillField('first_name', '');
$I->fillField('last_name', '');
$I->click('Edit User');

$I->seeElement('.alert-danger');
$I->seeFormErrorMessage('first_name');
$I->seeFormErrorMessage('last_name');


$I->logout();
$I->dontSeeAuthentication();
