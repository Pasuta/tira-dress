<?php

class MemberApp extends WebApplication implements ApplicationFreeAccess, ApplicationUserOptional 
{
	function init()
	{
		$this->register_widget('left', 'logo');
	}
			
	function login()
	{
        if ($returnto = $_GET['returnto'])
        {
            Session::put("AuthedReturnUrl", $returnto);
        }
		/**
		если что-то не работает:
		проверить время на сервере (если часы отстают, куки поставится в прошлое и сразу удалится - сессия не начнется)
		*/
		if ($flash = Session::pop('flash'))
		{
			$this->context['flash'] = "<div class='flash'>{$flash}</div>";
		}
		$this->context['defaultlogin'] = $_GET['login'];
		//echo 'Current role: '.$this->role.'<br>';
		//echo 'Return to: '.Session::get("AuthedReturnUrl");
	}	

	function session()
	{
		
		printlnd(Session::get("hash"));
		printlnd(Session::get("user"));
		
		printlnd($this->user);

		if ($this->role == 'USER')
		{
			//$this->metadata->modified = mysqldate2timestamp('2011-01-01 10:10:10');
			//$this->metadata->modified = time();
			println('$this->role == USER');
			println($this->user);
		}
		else
		{
			println('$this->role != USER');
			println($this->role);
		}
		// Session already loaded in Application
		$us = new Message('{"urn":"urn:user","action":"session"}');
		//$us->hash = $_POST['hash'];
		$sess = $us->deliver();
		println('SESSION BY COOKIE RELOAD (user есть в Application): ');
		println($sess);
		
		$us = new Message('{"urn":"urn:user","action":"session"}');
		$us->hash = $_GET['hash'];
		$sess = $us->deliver();
		println('SESSION MANUAL HASH RELOAD (?hash=) : ');
		println($sess);
		
	}
	
	function changepassword() 
	{
		// $this->register_widget('left', 'logo');
	}
	
	function register() 
	{
	}
	
	function forgot() 
	{
	}


	function logout()
	{
		$l = new Message('{"action": "logout"}');
		$l->urn = $this->user->urn;
		assertURN($l->urn);
		$r = $l->deliver();	

		$this->redirect('/member/login');
		return $m;
	}
	
}
?>
