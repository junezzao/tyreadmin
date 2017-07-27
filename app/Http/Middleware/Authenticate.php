<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Session;
use Request;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('auth/login');
            }
        }
        else {
            $user = $this->auth->user();
            if($user->status=='Unverified'){
                $allowed = ['users.verify','users.show_verify','logout'];
                if(!in_array($request->route()->getName(), $allowed)) {
                    flash()->error('Please reset your account password before proceeding.');
                    return redirect()->route('users.show_verify');
                }
            }

            if ($user->category == 'Mobile Merchant'){
                flash()->error('Your account cannot login to ARC Enterprise.');
                return redirect()->route('logout');
            }

            $modules = Session::get('modules');
            $actions = $request->route()->getAction();
            
            if (!empty($actions['module']) && !$modules[$actions['module']]['enabled']) {
                flash()->error('The ' . $modules[$actions['module']]['name'] . ' module is disabled.');
                return redirect()->route('data.index');
            }
        }

        // to check if channel_id in URL belongs to a channel that the login user (channel manager) has access to
        if($user->is('channelmanager')) {
            $paths = explode('/', Request::path());
            if($paths[0] == 'byChannel') {
                $channelId = $paths[1];
                $validChannel = false;
                foreach($user->channels() as $channel) {
                    if($channel->id == $channelId) {
                        $validChannel = true;
                    }
                }

                if($validChannel === false) {
                    flash()->error('Manipulate Channel ID in the URL is prohibited.');
                    return redirect()->route('main.dashboard');
                }
            }
        }

        return $next($request);
    }
}
