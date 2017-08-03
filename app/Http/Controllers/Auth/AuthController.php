<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Activity;
use App\Http\Traits\GuzzleClient;
use Session;
use Carbon\Carbon;
use App\Http\Controllers\Admin\ConfigurationsController;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */
    use AuthenticatesAndRegistersUsers, ThrottlesLogins, GuzzleClient;

    protected $redirectPath = '/data';

    protected $config;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(ConfigurationsController $config)
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->config = $config;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    protected function resetPassword(Request $request)
    {
        $email = $request->input('password_reset_email');
        // [call event to send out email here]

        flash()->message('A password reset link has been sent to '.$email);

        return redirect()->back();
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);

        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }
        $credentials = $this->getCredentials($request);
        $authorized_status = ['Active', 'Unverified'];

        foreach ($authorized_status as $status) {
             $credentials['status'] = $status;

            if (\Auth::attempt($credentials, $request->has('remember'))) {
                // get access token
                $response = $this->authenticateUser($request);
                $response = json_decode($response);
                
                $session['access_token'] = $response->access_token;
                $session['token_type'] = $response->token_type;
                $session['expires_on'] = Carbon::now()->addminutes($response->expires_in);
                Session::put('tyreapi', $session);

                $user = User::where('email', strtolower($credentials['email']))->first();
                $userSession['user_id'] = $user->id;
                Session::put('user', $userSession);

                if (strcasecmp(\Auth::user()->status, 'Unverified') == 0) {
                    $this->redirectPath = '/password/reset';
                }

                // store modules
                $modules = $this->config->getModulesStatus();
                Session::put('modules', $modules);

                return $this->handleUserWasAuthenticated($request, $throttles);
            }
        }
       

        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect($this->loginPath())
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }

    public function getLogout()
    {
        $access_token = Session::get('tyreapi');
        $response = $this->logoutUser($access_token['access_token']);
        if ($response == 'logout successful') {
            Session::forget('tyreapi');
            \Auth::logout();
            return redirect('/');
        }
    }

    public function forgot(Request $request)
    {
        $rules = [
            'reset_email' => 'required|email',
            'captcha' => 'required|captcha'
        ];

        $messages = [
            'reset_email.required' => trans('sentence.forgot_password_validation')
        ];

        $v = \Validator::make($request->input(), $rules, $messages);
        if ($v->fails()) {
            \Log::info('v... '.print_r($v->errors()->toArray(), true));
            flash()->error('Email Address is not given or invalid CAPTCHA. Please try again.');
            return back();

            // return ['error'=>$v->errors()->toArray()];
            // throw new ValidationException($v);
        }

        $inputs = array();
        /*if(empty($inputs['email'])) {
            flash()->error(trans('sentence.forgot_password_validation'));
            return back()->withInput();
        }*/

        $input['email'] = $request->input('reset_email');
        $inputs['url'] = config('app.url');

        /*$response = $this->postGuzzleClient($inputs, 'users/forgot');
        if ($response->getStatusCode() == 200) {
            $content = json_decode($response->getBody()->getContents(), true);

            if ($content['success']) {
                flash()->success(trans('sentence.forgot_password_success', ['email'=>$inputs['email']]));
            }
            else {
                flash()->error($content['error']);
            }
            
            return redirect()->back();
        } else {
            flash()->error('Something wrong happens');
            return back()->withInput();
        }*/

    }
}
