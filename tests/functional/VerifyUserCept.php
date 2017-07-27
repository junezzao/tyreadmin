<?php 
use Page\LoginPage;
use Step\Functional\UserSteps;

$I = new UserSteps($scenario);
$I->wantTo('verify a user');

//get password
$user = $I->grabRecord('App\Models\User', array(
        'first_name'    => 'John',
        'last_name'        => 'Doe',
        'email'            => 'johndoe@test.com',
        'status'        => 'Unverified'
    ));

$user->password = bcrypt('Hubwire!');
$user->save();

//sign in
$I->amGoingTo('sign in with the given password in the email');
$I->login($user->category, $user->email);

$I->seeInCurrentUrl('/verify');
$I->cantSeeInCurrentUrl('/dashboard');

//test non matching password
$I->amGoingTo('submit with unmatching password');
$I->fillField('password', 'JohnDoePassword');
$I->fillField('password_confirmation', 'PasswordJohnDoe');
$I->click('Confirm');

$I->see('The password confirmation does not match.');

//test matching password
$I->amGoingTo('submit again with matching password');
$I->fillField('password', 'JohnDoePassword');
$I->fillField('password_confirmation', 'JohnDoePassword');
$I->click('Confirm');

$I->cantSeeElement('.error');
$I->canSeeInCurrentUrl('/dashboard');
$I->cantSeeInCurrentUrl('/verify');

//check that user is verified
$I->seeRecord('App\Models\User', array(
        'first_name'    => 'John',
        'last_name'        => 'Doe',
        'email'            => 'johndoe@test.com',
        'status'        => 'Active'
    ));

//sign out
$I->logout();
$I->dontSeeAuthentication();

//sign in again with new password
$I->amGoingTo('sign in with the newly set password');
$I->login($user->category, $user->email, 'JohnDoePassword');

$I->cantSeeInCurrentUrl('/verify');
$I->canSeeInCurrentUrl('/dashboard');

//sign out
$I->logout();
$I->dontSeeAuthentication();
