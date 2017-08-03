<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\UserRepository;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\GuzzleClient;
use Bican\Roles\Models\Role;
use Session;
use Config;
use DateTimeZone;
use DateTime;
use Validator;
use App\Services\MediaService as MediaService;
use App\Models\Merchant;
use App\Models\User;

class UsersController extends Controller
{
    use GuzzleClient;

    protected $userRepo;
    protected $admin;

    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->middleware('auth');
        $this->middleware('role:clientadmin', ['only' => ['getAccountDetails']]);
        $this->userRepo = $userRepo;
        $this->admin = \Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return view('users.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        if ($this->admin->id == $id) {
            $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$id)->getBody()->getContents());
            $data['user'] = $user;
            $data['currency'] = Config::get('globals.currency_list');
            $data['timezone'] = $this->generate_timezone_list();
            $data['id'] = $id;
            $data['countryList'] = config('globals.countryList');

            return view('users.edit', $data);
        } else {
            return redirect()->route('data.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array(
            'operation_type'    => 'required|max:255',
            'first_name'        => 'required|max:255',
            'last_name'         => 'required|max:255',
            'new_password'      => 'regex:"^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d$@$!%*?&]{6,}"|confirmed',
            'contact_no'        => 'required|max:255',
            'company_name'      => 'required|max:255',
            'address_line_1'    => 'required|max:255',
            'address_line_2'    => 'sometimes|max:255',
            'address_city'      => 'required|max:255',
            'address_postcode'  => 'required|max:255',
            'address_state'     => 'required|max:255',
            'address_country'   => 'required|max:2'
        );

        $errorMessages = array(
            'new_password.regex' => trans('passwords.format'),
            'email.exists' => trans('passwords.user')
        );

        $this->validate($request, $rules, $errorMessages);
        
        $response = json_decode($this->putGuzzleClient($request->all(), 'admin/users/'.$id)->getBody()->getContents());
    
        if ($response->success) {
            flash()->success('Your profile has been updated successfully.');
            return redirect()->route('users.edit', [$id]);
        } else {
            flash()->error('An error has occurred while updating your profile.');
            return back()->withInput()->withErrors($response->errors);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function showVerify()
    {
        if (strcasecmp($this->admin->status, "Unverified") !== 0) {
            flash()->error(trans('permissions.unauthorized'));
            return redirect()->route('data.index');
        }

        $user_id = $this->admin->id;
        return view('users.verify', array('user_id' => $user_id));
    }

    public function verify(Request $request)
    {
        if (strcasecmp($this->admin->status, "Unverified") !== 0) {
            flash()->error(trans('permissions.unauthorized'));
            return redirect()->route('data.index');
        }
        
        $rules = array(
            'password' => 'regex:"^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d$@$!%*?&]{6,}"|confirmed|required'
        );

        $errorMessages = array(
            'password.regex' => trans('passwords.format')
        );

        $this->validate($request, $rules, $errorMessages);

        $response = $this->postGuzzleClient($request->input(), 'users/verify');

        if ($response->getStatusCode() == 200) {
            //flash()->success('Your account has been verified.');
            return redirect()->route('data.index');
        } else {
            flash()->error('Error verifying account.');
            return back()->withInput();
        }
    }

    public function getAccountDetails()
    {
        if (!is_null($this->admin->merchant)) {
            $merchant = $this->admin->merchant;
            $ae = $merchant->ae()->first()->first_name.' '.$merchant->ae()->first()->last_name;
            $supported_currencies = json_decode($merchant->forex_rate, true);
            $currencies_arr = config('currencies');
            $timezones = Controller::generate_timezone_list();
            return view('users.account', compact('merchant', 'ae', 'supported_currencies', 'currencies_arr', 'timezones'));
        }
        flash()->info('Your account is not linked to any merchant account. Kindly contact your Account Manager or Customer Support for assistance.');
        return redirect('/data');
    }

    public function updateAccountDetails(Request $request)
    {
        $rules = array(
            'name' => 'sometimes|required|string',
            'address' => 'sometimes|required',
            'contact' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'gst_reg_no' => 'required_if:self_invoicing,true',
            'timezone' => 'required',
            'currency' => 'required',
        );

        $messages = array();

        foreach ($request->input('currencies') as $key => $val) {
            foreach ($request->input('rate') as $key => $rate_val) {
                $rules['currencies.'.$key] = 'required_with:rate.'.$key.'|not_in:'.$request->input('currency');
                $rules['rate.'.$key] = 'required_with:currencies.'.$key.'|numeric|min:0.0001';
                $messages['currencies.'.$key.'.required_with'] = 'Currency is required.';
                $messages['currencies.'.$key.'.not_in'] = 'Currency cannot be the same with default.';
                $messages['rate.'.$key.'.required_with'] = 'Currency rate is required.';
                $messages['rate.'.$key.'.numeric'] = 'Currency rate must be in numeric.';
                $messages['rate.'.$key.'.min'] = 'Currency rate minimum is :min.';
            }
        }

        $v = \Validator::make($request->input(), $rules, $messages);

        if ($v->fails()) {
            flash()->error('There was an error in updating your account details. Please review the fields below and ensure that the details are correct.');
            return redirect()->back()->withErrors($v)->withInput();
        }

        if ($request->hasFile('logo')) {
            $mediaService = new MediaService();
            $response = $mediaService->uploadFile($request->file('logo'), 'logo', null, null, array(), 10000, 'logo-'.$request->input('slug'), 'merchants/'.$request->input('slug'));
            if (isset($response->errors)) {
                flash()->error('There was an error in uploading the logo image. Please try uploading the image again.');
                return redirect()->back();
            }
            $request->merge(array( 'logo_url' => str_replace('https://', 'http://', $response->media_url) ));
        }

        $response = json_decode($this->putGuzzleClient($request->except(['_token', 'merchant_id']), 'admin/merchants/'.$request->input('id'))->getBody()->getContents());
        
        if (isset($response->code)) {
            flash()->error('There was an error in updating your account details. Please review the fields below and ensure that the details are correct.');
            return redirect()->back()->withErrors($response->error)->withInput();
        } else {
            flash()->success('Your account details have been updated.');
            return redirect()->back();
        }
    }

    public function subscription()
    {
        $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$this->admin->id)->getBody()->getContents());
        $data['user'] = $user;
        $data['subscriptionType'] = array(
            '3' => 'Elite'
        );
        $data['typeColor'] = array(
            'Lite' => '#c7b199',
            'Elite' => '#57a501'
        );
        $data['days'] = 0;

        if(!empty($user->active_plan)) {
            $today          = date_create(date('Y-m-d'));
            $activeEndDate  = date_create($user->active_plan->end_date);
            $diff           = date_diff($today, $activeEndDate);

            $data['days'] = $diff->format("%a");
        }
        
        // \Log::info('user... '.print_r($user, true));
        return view('users.subscription', $data);
    }

    public function subscribe(Request $request)
    {
        $this->validate($request, array(
            'subscription_type' => 'required'
        ));

        $response = json_decode($this->postGuzzleClient($request->all(), 'admin/users/subscribe/'.$this->admin->id)->getBody()->getContents());
    
        if ($response->success) {
            flash()->success('New subscription has been made successfully.');
            return redirect()->route('users.subscription', [$this->admin->id]);
        } else {
            flash()->error('An error has occurred while subscribing.');
            return back()->withInput()->withErrors($response->errors);
        }
    }
}
