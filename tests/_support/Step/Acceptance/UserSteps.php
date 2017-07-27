<?php
namespace Step\Acceptance;

use Page\LoginPage;
use Step\Acceptance\UserSteps;

class UserSteps extends \AcceptanceTester
{
    public $user;
    public $adminUser;

    public function login($role, $username = '', $password = 'Hubwire!')
    {
        if (empty($username)) {
            $username = $this->getUsername($role).'@hubwire.com';
        }
        $I = $this;
        $I->amOnPage(LoginPage::$URL);
        $I->fillField(LoginPage::$usernameField, $username);
        $I->fillField(LoginPage::$passwordField, $password);
        $I->click(LoginPage::$loginBtn);
    }

    public function logout()
    {
        $I = $this;
        $I->amOnPage('/auth/logout');
    }

    public function getUsername($role)
    {
        switch ($role) {
            case 'Super Administrator':
                return 'superadmin';
            case 'Administrator':
                return 'admin';
            case 'Finance':
                return 'finance';
            case 'Merchant Admin':
                return 'client_admin';
            default:
                return 'client_user';
        }
    }
}
