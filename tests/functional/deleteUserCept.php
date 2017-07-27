<?php 
use Page\LoginPage;
use Step\Functional\UserSteps;

// use Illuminate\Foundation\Testing\DatabaseTransactions;

$I = new UserSteps($scenario);
$I->am('a registered user');
$I->wantTo('test the delete user function');
$I->amOnPage('/');
$user = factory('App\Models\User')->create();

$I->login('Super Administrator');

//$I->login($user->email, $user->password);
//$I->login('administrator');
$I->seeInCurrentUrl('/dashboard');
$I->click('User Management');
$I->click('Users');
$I->seeInCurrentUrl('/admin/users');

//
//$I->fillField("input[type=search]", $user->id);
$I->click('.delete-'.$user->id);
$I->click('OK');

$I->seeElement('.alert-success');

$I->logout();
$I->dontSeeAuthentication();
