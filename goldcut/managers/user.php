<?php
/**
TODO Start transaction. Create only base here. Delegate profile creation to Local Site
FAST ONLINE SESSION
fail count on login, session id fase
one time pass from link in mail
SESSION HASH - LOC IN MEMMAP, SHARD ID, UNIQ
ACTIVATE ON FIRST LOGIN. TODO active as logged at least once != blocked by admin

MQ:
user.onfirstlogin
user.login

*/

if (NEWUSERMODEL === true)
{

class User extends EManager
{
	
	protected function config()
	{
		$this->behaviors[] = 'general_crud';
		$this->behaviors[] = 'general_graph';
		$this->behaviors[] = 'general_list';
	}
	
	function register($m)
	{
		//dprintln('NEW USER MODEL', 1, TERM_GRAY);
		
		if (!strlen($m->email)) return new Message('{"error": "blank_email", "text": "Не указан email"}');
		
		// check email
		$user = new Message('{"action": "load", "urn": "urn-user"}');
		$user->email = $m->email;
		$r = $user->deliver();
		if (count($r)) return new Message('{"error": "user_exists", "text": "Пользователь с таким email существует"}');

		// create user
		$user = new Message('{"action": "create", "urn": "urn-user"}');
		$user->email = $m->email;
		
		$E = Entity::ref('user');
		foreach ($E->usereditfields as $fname)
			$user->$fname = $m->$fname;
		
		$user->wallet = INITIAL_DEPOSIT;
		$user->bonus = INITIAL_BONUS;
		
		// activate default?
		$user->active = false; // TODO CONFIGURABLE
		
		// gen pass or use provided
		if ($m->providedpassword) // TODO CHECK - AND password IN $E->usereditfields
		{
			if ($m->providedpassword == $m->providedpasswordcopy)
			{
				$user->password = $m->providedpassword;
			}
			else return new Message('{"error": "password_mismatch", "text": "Пароль в повторном введении не совпадает"}');
		}
		else
        {
            if (SIMPLEPASSWORDS === true)
                $user->password = Security::generateSimplePassword();
            else
			    $user->password = Security::generatePassword();
        }
		$plainPassword = $user->password;
		$m->password = $plainPassword;
		$m->dynamicsalt = mt_rand(1,2147483647);
		$user->dynamicsalt = $m->dynamicsalt;
		$hashedSaltedPassword = sha1($m->dynamicsalt . $user->password . SECURITY_SALT_STATIC);
		$user->password = $hashedSaltedPassword;

		if ($m->id)
        {
            if (ENV == 'DEVELOPMENT' || ENV == 'TEST')
                $user->id = $m->id;
            else
                throw new SecurityException('Register user with provided id failed');
        }


		$r = $user->deliver();
		$r->password = $plainPassword;
		unset($r->dynamicsalt);
		Broker::instance()->send($r, "MANAGERS", "user.onregister");

        if ($m->autologin)
        {
            // auto login, redirect to /account
            $l = new Message('{"action": "authentificate", "urn": "urn-user"}');
            $l->email = $user->email;
            $l->password = $plainPassword;
            $autologgedin = $l->deliver();
            if ($autologgedin->error)
                throw new Exception('Autologin failed '.$autologgedin->error);
            $r->autologgedin = 'yes';
        }
		return $r;
	}
	
	// password send on forgot
	public function forgot($m)
	{
		$c = new Message('{"action": "load", "urn": "urn-user"}');
		$c->email = $m->email;
		$user = $c->deliver();
		if (count($user))
		{
			// generate password
            if (SIMPLEPASSWORDS === true)
                $newpassword = Security::generateSimplePassword();
            else
                $newpassword = Security::generatePassword();
			// HASH user password with sha1
			$update_user = new Message();
			$update_user->action = "update";
			$update_user->urn = $user->urn;
			$dynamicsalt = mt_rand(1,2147483647);
			$update_user->dynamicsalt = $dynamicsalt;
			$hashedSaltedPassword = sha1($dynamicsalt . $newpassword . SECURITY_SALT_STATIC);
			$update_user->password = $hashedSaltedPassword;
			$update_user->deliver();
			
			$user->newpassword = $newpassword;
			
			// send email, update related passworded (ftp etc)
			Broker::instance()->send($user, "MANAGERS", "user.onforgot");
			$m = new Message(array("notify" => "Password sent"));
			if (ENV == 'DEVELOPMENT') $m->newpassword = $newpassword;
			return $m;
		}
		else
			return new Message(array("error" => "email not registered"));
	}
	
	/**
	Аутентификация по логину и паролю
	TODO если сессия уже есть (другой браузер или закрытие без логаута) - вернуть ее же
	TODO limit online sessions to 10. + drop old (last login > 5 days)
	*/
	public function authentificate($m)
	{
		if (!strlen($m->email) || !strlen($m->password))
		{
			$auth = new Message();
			$auth->error = "bad_request";
			$auth->message = "Неполный запрос";
			return $auth;
		}
		
		$auth = new Message();
		
		$load = new Message('{"action": "load", "urn": "urn-user"}');
		$load->email = $m->email;
		$user = $load->deliver();
		if ($user->isempty())
		{
			$auth->error = "early_check_error"; // TODO in DEV ret noemail, else return 'user OR password are incorrect'
			$auth->message = "Неверный логин или пароль";
			return $auth;
		}
		//println($user->current(),1,TERM_RED);
		//$load = new Message('{"action": "load", "urn": "urn-user"}');
		//$load->email = $m->email;
		$hashedSaltedPassword = sha1($user->dynamicsalt . $m->password . SECURITY_SALT_STATIC);
		//$load->password = $hashedSaltedPassword;
		//$user = $load->deliver();
		$auth = new Message();
		//if (!$user->isempty())
		if ($hashedSaltedPassword == $user->password)
		{
			if (!$user->active && $user->lastlogin)
			{
				$auth->error = "user_valid_but_inactive";
				$auth->message = "Юзер не активен";
				return $auth;
			}
			
			// set Active, Lastlogin
			$update_user = new Message();
			$update_user->action = "update";
			$update_user->urn = $user->urn;
			if (!$user->active) $update_user->active = true; // Activate by first login
			$update_user->lastlogin = time();
			$update_user->deliver();
			
			if (!$user->lastlogin)
			{
				Broker::instance()->send($user->current(), "MANAGERS", "user.onfirstlogin");
			}
			Broker::instance()->send($user->current(), "MANAGERS", "user.onlogin");
			
			// Online save
			$su = new Message();
			$su->action = 'create';
			$su->urn = "urn-online";
			$su->hash = Security::genLoginHashNumber();
			$su->ip = ip2long($_SERVER["REMOTE_ADDR"]);
			$su->user = $user->urn;
			$createdOnlineSession = $su->deliver();
			
			// Session init
			$hash = (string) $createdOnlineSession->urn->uuid;
			$auth->user = $user->urn;
			$auth->urn = $user->urn;
			$auth->hash = $hash;
			
			Session::put("hash", (string) $hash); // INT ID of Online row
			Session::put("user", (string) $user->urn->uuid());
		}
		else
		{
			$auth->message = "Неверный пароль";
			$auth->error = "incorrect_password";
		}
		
		return $auth;
	}
	
	/**
	Used in ajax check who am I
	rename to session_by_direct_hash (non cookie)
	*/
	public function session_by_cookie($m)
	{
		$s = new Message();
		if ($m->hash)
		{
			$su = new Message();
			$su->urn = 'urn-online';
			$su->action = 'load';
			$su->id = (int) $m->hash;
			$lu = $su->deliver();
			if (count($lu))
			{
				$s->user = $lu->user->urn;
				$s->hash = $lu->hash;
				$s->created = $lu->created;
				$s->ip = long2ip($lu->ip);
			}
			else
				$s->warning = "anonymous"; 
		}
		else
		{
			$s->error = "no hash provided";
		}
		return $s;
	}

	public function session($m)
	{
		$s = new Message();
		$su = new Message();
		$su->urn = 'urn-online';
		$su->action = 'load';
		if ($hash = Session::get("hash")) // by hash in cookie
		{
			$su->id = (int) $hash;
		}
		else if (strlen($m->hash)>0) // by hash in message
		{
			$su->id = (int) $m->hash;
		}
		else
		{
			return new Message('{"error": "no_hash_in_message_or_cookie"}');
		}
		$lu = $su->deliver();
		if ($lu && count($lu) == 1) // lu - urn-online
		{
			// IS LOGGED IN
			$useronline = $lu->user;
			if (!count($useronline))
			{
				return new Message('{"error": "stale online db record. user associated user deleted"}');
			}
			$s->user = $useronline->urn;
			$s->hash = $lu->hash;
			$s->created = $lu->created;
			$s->ip = long2ip($lu->ip);
			$s->online = $lu->urn;
			
			/**
			IP CHANGED IN ACTIVE SESSION
			$curip = ip2long($_SERVER["REMOTE_ADDR"]);
			if ($lu->ip != $curip)
			{
				// Session ok but ip changed
				$s = new Message();
				$s->warning = "anonymous";
				$s->reason = "cookie hash exists but user changed ip";
				$s->hash = $hash;
			}
			*/
		}
		else
		{
			// ANON
			$s->warning = "anonymous";
			$s->reason = "cookie hash exists but has no online record";
			$s->hash = $hash;
		}
		return $s;
	}
	
	public function logout($m)
	{
		$m = null;
		$us = new Message('{"urn":"urn-user", "action":"session"}');
		$sess = $us->deliver();
		if (count($sess))
		{
			//dprintln($sess);
			$su = new Message('{"action": "delete"}');
			$su->urn = $sess->online;
			$lu = $su->deliver();
		}
		//printlnd($lu);
		Session::destroy();
		return new Message('{"warning": "logged_out"}');
	}
	
	// code from callback to token trade (http req to ouath provider)
	/**
	by token get userid64, email(opt) (http req)
	if has user by userid64 (in oauth links):
		if ?userid64 registered/linked > create session +
	else if no such userid64 link:
		if user NOT exists by ?email > create user, link user to oauth, create session +
		if user exists by ?email in ext ouath profile > link oauth with user
		
	just link (when logged in) - no need to emails be equal	
	*/
	/**
	DUPLICATE WITH APP OAUTHv2/v1???
	function oauthlogin($m) // with autoregister
	{
		$oauthcode = $m->code;
		
		//$facebookid = $facebook->getUser();
		
		if ($facebookid > 0)
		{
			$load = new Message('{"action": "load", "urn": "urn-user"}');
			$load->facebookid = $facebookid;
			$load->last = 1;
			$user = $load->deliver();
			
			if (!count($user)) // no user with such linked fb id
			{
				try 
				{
					$user_profile = $facebook->api('/me');
					//print_r($user_profile);
				}
				catch (FacebookApiException $e) 
				{ // old (inactive) auth token etc 
					echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
					//$this->redirect("/member/facebooklogin");
					$user = null;
				}
				
				$load = new Message('{"action": "load", "urn": "urn-user"}');
				$load->email = $user_profile['email'];
				$load->last = 1;
				//println($load);
				$user = $load->deliver();
				
				if (!count($user)) // no user in db with such email
				{
					$m = new Message('{"action": "create", "urn": "urn:user"}');
					$m->email = $user_profile['email'];
					$m->name = $user_profile['name'];
					$m->facebookid = $facebookid; // $user_profile['id'];
					$m->active = true;
					$user = $m->deliver(); // CREATED NEW USER
					
					$account = new Message('{"action": "create", "urn": "urn-account"}');
					$account->user = $user->urn;
					$account->name = $user_profile['name'];
					$account->wallet = INITIAL_DEPOSIT;
					$account = $account->deliver();
					// assign account to user
					$update_user = new Message();
					$update_user->action = "update";
					$update_user->urn = $user->urn;
					$update_user->account = $account->urn;
					$update_user->deliver();
					
				}
				else // OLD USER NOW LINK WITH FB
				{
					$m = new Message('{"action": "update"}');
					$m->urn = $user->urn;
					$m->facebookid = $facebookid;
					$m->name = $user_profile['name'];
					$m->deliver();
				}
			}
			
			//print '<h1>init session for exists user/h1>';
			// Online save
			$su = new Message();
			$su->action = 'create';
			$su->urn = "urn-online";
			$su->hash = Security::genLoginHash();
			$su->ip = ip2long($_SERVER["REMOTE_ADDR"]);
			$su->user = $user->urn;
			$createdOnlineSession = $su->deliver();
			
			// Session init
			$hash = (string) $createdOnlineSession->urn->uuid;
			$auth = new Message();
			$auth->user = $user->urn;
			$auth->urn = $user->urn;
			$auth->hash = $hash;
			
			Session::put("hash", (string) $hash);
			Session::put("user", (string) $user->urn->uuid());
			
			//println($createdOnlineSession);
			$this->redirect("/account");
			
		}
	}
	*/

}
	
} // if NEWUSERMODEL end


if (NEWUSERMODEL !== true)
{

	if (ALLOWOLDUSERMODEL !== true) throw new Exception('define ALLOWOLDUSERMODEL true to use old user model manager');
	include "userold.php";	

}

?>