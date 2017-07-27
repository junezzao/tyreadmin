<?php
use Page\LoginPage;
use Step\Acceptance\UserSteps;

$I = $my = new UserSteps($scenario);
//$I->am('registered user');
$I->wantTo('test the login function');

$I->amGoingTo('submit login form with invalid password');
$I->login('Merchant User', 'dummy@email', 'invalidpassword');
$I->see('Incorrect username and/or password.');

$I->amGoingTo('submit admin login form with valid credential');
$I->login('Merchant User');
$I->seeInCurrentUrl('/dashboard');

//$I->amGoingTo('logout');
$I->click('//html/body/div/header/nav/div/ul/li[4]/a/span');
$I->wait(2);
$I->click('//html/body/div/header/nav/div/ul/li[4]/ul/li[3]/div[2]/a');
$I->wait(2);
$I->canSeeInCurrentUrl('/');
$I->wait(2);
