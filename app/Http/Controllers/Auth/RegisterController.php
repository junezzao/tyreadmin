<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Traits\GuzzleClient;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    use GuzzleClient;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    protected $userRepo, $merchantRepo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        $data = array();
        $data['countryList'] = config('globals.countryList');

        return view('auth.register', $data);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $this->validate($request, array(
            'operation_type'    => 'required|max:255',
            'first_name'        => 'required|max:255',
            'last_name'         => 'required|max:255',
            'email'             => 'required|email|max:255|unique:tyreapi.users',
            'contact_no'        => 'required|max:255',
            'company_name'      => 'required|max:255',
            'address_line_1'    => 'required|max:255',
            'address_line_2'    => 'sometimes|max:255',
            'address_city'      => 'required|max:255',
            'address_postcode'  => 'required|max:255',
            'address_state'     => 'required|max:255',
            'address_country'   => 'required|max:2'
        ));

        $response = $this->postGuzzleClient($request->input(), 'auth/register');
        if($response->getStatusCode() == 200) {
            $message = trans('sentence.account_registration_success');

            flash()->success($message);
            return redirect()->route('login');
        } else {
            return back()->withInput();
        }
    }
}
