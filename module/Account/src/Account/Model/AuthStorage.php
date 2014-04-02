<?php
namespace Account\Model;

use Zend\Authentication\Storage\Session;

class AuthStorage extends Session{
	public function setRememberMe($rememberMe = 1, $time = 2419200){
		if($rememberMe == 1) $this->session->getManager()->rememberMe($time);
	}
	
	public function forgetMe(){
		$this->session->getManager()->forgetMe();
	}
}
