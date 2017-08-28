<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\UserRepository;
use App\Http\Controllers\Controller;
use App\Http\Traits\GuzzleClient;

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

    public function editUser()
    {
        return $this->edit($this->admin->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->admin->id == $id) {
            $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$id)->getBody()->getContents());
            $data['user'] = $user;
            $data['id'] = $id;
            $data['countryList'] = config('globals.countryList');

            return view('users.edit', $data);
        } else {
            return redirect()->route('data.index');
        }
    }

    public function updateUser(Request $request)
    {
        return $this->update($request, $this->admin->id);
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
            'email.exists' => trans('passwords.user')
        );

        $this->validate($request, $rules, $errorMessages);
        
        $response = json_decode($this->putGuzzleClient($request->all(), 'admin/users/'.$id)->getBody()->getContents());
    
        if ($response->success) {
            flash()->success('Your profile has been updated successfully.');
            return redirect()->route('user.editUser');
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
            return redirect()->route('user.subscription');
        } else {
            flash()->error('An error has occurred while subscribing.');
            return back()->withInput()->withErrors($response->errors);
        }
    }

    public function changePassword()
    {
        $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$this->admin->id)->getBody()->getContents());
        $data['user'] = $user;
        return view('users.change-password', $data);
    }

    public function changePasswordSubmit(Request $request)
    {
        $rules = array(
            'old_password'  => 'required',
            'new_password'  => 'required|regex:"^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d$@$!%*?&]{6,}"|confirmed'
        );

        $errorMessages = array(
            'new_password.regex' => trans('passwords.format')
        );

        $this->validate($request, $rules, $errorMessages);
        
        $response = json_decode($this->putGuzzleClient($request->all(), 'admin/users/'.$this->admin->id)->getBody()->getContents());
    
        if ($response->success) {
            flash()->success('Your password has been changed successfully.');
            return redirect()->route('user.changePassword');
        } else {
            return back()->withErrors($response->errors);
        }
    }
}
