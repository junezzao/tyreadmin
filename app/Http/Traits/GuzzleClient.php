<?php

namespace App\Http\Traits;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Session;

trait GuzzleClient
{
    public function authenticateUser(Request $request)
    {
        $client = new Client();
        
        $response = $client->request('POST', config('app.api_url').'oauth/access_token', [
                                            'form_params' => [
                                                'username' => $request->input('email'),
                                                'password' => $request->input('password'),
                                                'client_id' => env('TYREAPI_CLIENT_ID'),
                                                'client_secret' => env('TYREAPI_CLIENT_SECRET'),
                                                'grant_type' => 'password'
                                            ]
                                    ]);
        
        return $response->getBody()->getContents();
    }

    public function logoutUser($access_token)
    {
        $client = new Client();

        $response = $client->request('GET', config('app.api_url').'oauth/logout', [
                            'headers' => ['Authorization' => 'Bearer '.$access_token]
                        ]);
        return $response->getBody()->getContents();
    }

    public function newGuzzleClient()
    {
        $token = $this->getSessionToken();

        if (false !== $token) {
            $client = new Client(['headers' => ['Authorization' => 'Bearer '.$token]]);
            return $client;
        } else {
            return new \GuzzleHttp\Client;
        }
    }

    public function getGuzzleClient($params = [], $url = '')
    {
        $url = (isset($url) ? config('app.api_url').$url : config('app.api_url'));

        $client = $this->newGuzzleClient();
        $response = $client->request('GET', $url, [
            'query' => $params
        ]);
        return $response;
    }

    public function postGuzzleClient($params = [], $url = '', $type = 'form_params')
    {
        $url = (isset($url) ? config('app.api_url').$url : config('app.api_url'));

        $client = $this->newGuzzleClient();
        $response = $client->request('POST', $url, [
            $type => $params
        ]);
        return $response;
    }

    public function putGuzzleClient($params = [], $url = '', $type = 'form_params')
    {
        $url = (isset($url) ? config('app.api_url').$url : config('app.api_url'));

        $client = $this->newGuzzleClient();

        $response = $client->request('PUT', $url, [
            $type =>  $params
        ]);

        return $response;
    }

    public function deleteGuzzleClient($params = [], $url = '')
    {
        $url = (isset($url) ? config('app.api_url').$url : config('app.api_url'));

        $client = $this->newGuzzleClient();
        $response = $client->request('DELETE', $url, [
            'query' => $params
        ]);
        return $response;
    }
    
    private function getSessionToken()
    {
        if (Session::has('tyreapi')) {
            $session = Session::get('tyreapi');
            $access_token = $session['access_token'];
            return $access_token;
        }
        return false;
    }
}
