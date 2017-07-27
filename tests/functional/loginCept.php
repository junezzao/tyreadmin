<?php 
use Page\LoginPage;
use Step\Functional\UserSteps;

// use Illuminate\Foundation\Testing\DatabaseTransactions;

$I = new UserSteps($scenario);
$I->am('a registered user');
$I->wantTo('test the login function');

//$user = factory('App\Models\User')->create();
//$I->login($user->email, $user->password);
$I->login('Administrator');

$I->seeInCurrentUrl('/dashboard');

$I->logout();
$I->dontSeeAuthentication();
