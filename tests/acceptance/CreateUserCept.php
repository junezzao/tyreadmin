<?php 
use Page\LoginPage;
use Step\Acceptance\UserSteps;

$I = new UserSteps($scenario);
$I->wantTo('create a new user');

//sign in as administrator
$I->amGoingTo('sign in as administrator');
$I->login('Administrator');

$I->wait(2);

$I->seeInCurrentUrl('/dashboard');

//go to create user page
$I->amGoingTo('go create new user page');
$I->click('User Management');
$I->click('Users');
$I->wait(2);
$I->seeInCurrentUrl('/admin/users');
$I->click('#new_user');

$I->wait(2);
$I->seeInCurrentUrl('/admin/users/create');

//test empty form
$I->amGoingTo('submit the form with without filling in the fields');
$I->click('#btn_create_new_user');

$I->wait(2);

$I->see('The first name field is required.');
$I->see('The last name field is required.');
$I->see('The email field is required.');

//test user_category and merchant field
$I->amGoingTo('submit the form with user category set to client admin');
$I->selectOption('user_category', 'clientadmin');
$I->seeElement('select', ['name' => 'merchant']);
$I->click('#btn_create_new_user');

$I->wait(2);

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

$I->wait(2);

$I->cantSeeElement('.error');
$I->canSeeInCurrentUrl('/admin/users');
$I->cantSeeInCurrentUrl('/create');
$I->see('User John Doe (johndoe@test.com) has been created!');

//Search (filter)
$I->fillField('//*[@id="user_table_filter"]/label/input', 'John Doe');
$I->wait(2);

//Check if user has been created
$I->canSee('John Doe', '#user_table');
$I->canSee('johndoe@test.com', '#user_table');
$I->canSee('Unverified', '#user_table');

$I->wait(2);

//Logout
$I->click('/html/body/div/header/nav/div/ul/li[4]/a');
$I->wait(2);
$I->click('/html/body/div/header/nav/div/ul/li[4]/ul/li[3]/div[2]/a');
$I->wait(2);
$I->canSeeInCurrentUrl('/');
$I->wait(2);
