<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{
	public function request($method, $url, $params = []) {
	    $data = $this->getModule('Laravel5')->_request($method, $url, $params);
	    return json_decode($data);
	}
}