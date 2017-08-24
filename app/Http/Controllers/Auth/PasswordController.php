<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use Session;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords, GuzzleClient;

    protected $redirectPath = '/data';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function getActivateAccount($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        return view('auth.activate')->with('token', $token);
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function getResetPassword($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        return view('users.reset-password')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(Request $request)
    {
        $rules = array(
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            // 'password' => 'regex:"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}"|confirmed|required',
            'password' => 'regex:"^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d$@$!%*?&]{6,}"|confirmed|required'
        );

        $errorMessages = array(
            'password.regex' => trans('passwords.format'),
            'email.exists' => trans('passwords.user')
        );

        $this->validate($request, $rules, $errorMessages);

        $passwordReset = \DB::table('password_resets')->where('email', $request->input('email'))->where('token', $request->input('token'))->first();
        if(empty($passwordReset)) {
            \Log::info('error');
            return redirect()->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => trans('passwords.invalid_reset_link')]);
        }

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        // updates the password_updated_date column in users table
        $u = User::where('email', '=', $request->input('email'))->first();
        $u->status = 'Active';
        $u->save();
        // \Log::info('proceed');
        // get access token
        $resp = $this->authenticateUser($request);
        $resp = json_decode($resp);
        
        $session['access_token'] = $resp->access_token;
        $session['token_type'] = $resp->token_type;
        $session['expires_on'] = Carbon::now()->addminutes($resp->expires_in);
        Session::put('tyreapi', $session);

        $userSession['user_id'] = $u->id;
        Session::put('user', $userSession);

        switch ($response) {
            case Password::PASSWORD_RESET:
                return redirect($this->redirectPath())->with('status', trans($response));
            default:
                return redirect()->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => trans($response)]);
        }
    }
}
