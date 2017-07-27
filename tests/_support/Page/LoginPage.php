<?php
namespace Page;

class LoginPage
{
    // include url of current page
    public static $URL = '/auth/login';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $usernameField = 'form input[name=email]';
    public static $passwordField = 'form input[name=password]';
    public static $loginBtn = 'form input[type=submit]';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }
}
