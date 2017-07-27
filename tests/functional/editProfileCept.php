<?php 
use Page\LoginPage;
use Step\Functional\UserSteps;

// use Illuminate\Foundation\Testing\DatabaseTransactions;

$I = new UserSteps($scenario);
$I->am('a registered user');
$I->wantTo('test the edit profile function');
$I->amOnPage('/');
$user = factory('App\Models\User')->create();

$I->fillField('email', $user->email);
$I->fillField('password', 'Hubwiretest!');
$I->click('.signin-btn');

//$I->login($user->email, $user->password);
//$I->login('Administrator');

$I->seeInCurrentUrl('/dashboard');
$I->click('Edit Profile');
$I->see('Edit Profile');

// edit details
$I->fillField('first_name', 'Ann Nee');
$I->fillField('last_name', 'Lau');
$I->fillField('contact_no', '322');
$I->fillField('address', '10 Pallet Town');
$I->selectOption('form select[name=timezone]', 'Pacific/Midway');
$I->selectOption('form select[name=currency]', 'MYR');
$I->click('Update Profile');

$I->seeElement('.alert-success');
$I->seeInFormFields('form.form-horizontal', [
    'first_name' => 'Ann Nee',
    'last_name' => 'Lau',
    'contact_no' => '322',
    'address' => '10 Pallet Town',
]);

// test change password
$I->fillField('new_password', 'abcde12345');
$I->fillField('confirm_new_password', 'abcde12345');
$I->click('Update Profile');
$I->seeElement('.alert-success');


// test validation first name and last name
$I->fillField('first_name', '');
$I->fillField('last_name', '');
$I->click('Update Profile');
$I->seeElement('.alert-danger');
$I->seeFormErrorMessage('first_name');
$I->seeFormErrorMessage('last_name');

// test validation password
$I->fillField('first_name', 'Ann Nee');
$I->fillField('last_name', 'Lau');
$I->fillField('new_password', 'asdfgea');
$I->fillField('confirm_new_password', 'dsa');
$I->click('Update Profile');
$I->seeFormErrorMessage('new_password');
$I->seeFormErrorMessage('confirm_new_password');

$I->logout();
$I->dontSeeAuthentication();

$I->amOnPage('/');
$I->fillField('email', $user->email);
$I->fillField('password', 'abcde12345');
$I->click('.signin-btn');

$I->seeInCurrentUrl('/dashboard');
