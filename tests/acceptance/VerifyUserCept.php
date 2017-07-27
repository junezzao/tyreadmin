<?php 
use \Codeception\Util\Locator;

$I = new AcceptanceTester($scenario);
$I->wantTo('verify a user');
$I->amOnUrl('http://192.168.10.10:1080/');
$I->see('johndoe@test.com', '#messages');

$message = Locator::contains('td', 'johndoe@test.com');
$I->click($message);

$I->wait(2);

//get password
$text = $I->grabFromLastEmailTo('johndoe@test.com', '/(Password.*)(?=<\/li>)/');
$text = explode(':', $text);
$password = trim($text[1]);

//sign in
$I->amGoingTo('sign in with the given password in the email');
$I->amOnUrl('http://www.hapiadmin.dev');
$I->amOnPage('/auth/login');
$I->fillField('email', 'johndoe@test.com');
$I->fillField('password', $password);
$I->click('SIGN IN');

$I->wait(2);

$I->canSeeInCurrentUrl('/verify');
$I->cantSeeInCurrentUrl('/dashboard');

//test non matching password
$I->amGoingTo('submit with unmatching password');
$I->fillField('password', 'JohnDoePassword');
$I->fillField('password_confirmation', 'PasswordJohnDoe');
$I->click('Confirm');

$I->wait(2);

$I->see('The password confirmation does not match.');

//test matching password
$I->amGoingTo('submit again with matching password');
$I->fillField('password', 'JohnDoePassword');
$I->fillField('password_confirmation', 'JohnDoePassword');
$I->click('Confirm');

$I->wait(2);

$I->cantSeeElement('.error');
$I->canSeeInCurrentUrl('/dashboard');
$I->cantSeeInCurrentUrl('/verify');
$I->see('Your account has been verified.');

//sign out
$I->click('/html/body/div/header/nav/div/ul/li[4]/a');
$I->wait(2);
$I->click('/html/body/div/header/nav/div/ul/li[4]/ul/li[3]/div[2]/a');
$I->wait(2);
$I->canSeeInCurrentUrl('/');
$I->wait(2);

//sign in again with new password
$I->amGoingTo('sign in with the newly set password');
$I->fillField('email', 'johndoe@test.com');
$I->fillField('password', 'JohnDoePassword');
$I->click('SIGN IN');

$I->wait(2);

$I->cantSeeInCurrentUrl('/verify');
$I->canSeeInCurrentUrl('/dashboard');

$I->wait(2);

//sign out
$I->click('/html/body/div/header/nav/div/ul/li[4]/a');
$I->wait(2);
$I->click('/html/body/div/header/nav/div/ul/li[4]/ul/li[3]/div[2]/a');
$I->wait(2);
$I->canSeeInCurrentUrl('/');
$I->wait(2);
