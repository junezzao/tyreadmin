<?php 
use Page\LoginPage;
use Step\Functional\UserSteps;

$I = new UserSteps($scenario);
$I->wantTo('create a new user');

//sign in as administrator
$I->amGoingTo('sign in as administrator');
$I->login('Administrator');
$I->seeInCurrentUrl('/dashboard');

//go to create user page
$I->amGoingTo('go create new user page');
$I->click('User Management');
$I->click('Users');
$I->seeInCurrentUrl('/admin/users');
$I->amOnPage('/admin/users/create');
$I->seeInCurrentUrl('/admin/users/create');

//test empty form
$I->amGoingTo('submit the form with without filling in the fields');
$I->click('#btn_create_new_user');

$I->see('The first name field is required.');
$I->see('The last name field is required.');
$I->see('The email field is required.');

//test user_category and merchant field
$I->amGoingTo('submit the form with user category set to client admin');
$I->selectOption('user_category', 'clientadmin');
$I->seeElement('select', ['name' => 'merchant']);
$I->click('#btn_create_new_user');

$I->see('The merchant field is required when user category is clientadmin.');

//test complete form
$I->amGoingTo('submit a complete form for creating a new user');
$I->fillField('first_name', 'John');
$I->fillField('last_name', 'Doe');
$I->fillField('email', 'johndoe@test.com');
$I->fillField('contact_no', '0123456789');
$I->fillField('address', '123, Jalan 1/23.');
$I->selectOption('user_category', 'clientadmin');
$I->selectOption('merchant', 'testmerchant1');
$I->selectOption('default_timezone', 'Asia/Kuala_Lumpur');
$I->selectOption('default_currency', 'MYR');
$I->click('#btn_create_new_user');

$I->cantSeeElement('.error');
$I->canSeeInCurrentUrl('/admin/users');
$I->cantSeeInCurrentUrl('/create');

//Check new user created in database
$I->seeRecord('App\Models\User', array(
        'first_name'    => 'John',
        'last_name'        => 'Doe',
        'email'            => 'johndoe@test.com',
        'status'        => 'Unverified'
    ));

//Logout
$I->logout();
$I->dontSeeAuthentication();
