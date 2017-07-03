<?php

abstract class Application
{
	// used?
	protected $apptitle;
	// uri/parts/all
	private $uri = array();

	// move to access maanged 
	public $role;
	public $user;
    public $userrole;
	
	// ?
	public $path;
	
	// post data as message
	public $message;
	
	// mpve to webapp
	public $view;
	public $layout = 'general';
	public $widgets = array();
	public $widget_options = array();
	// view template context
	public $context = array();
	public $langprefix;
	
	public $metadata;

	public function metadata()
	{
		return $this->metadata;
	}
	
	function path()
	{
		return '/'.join('/',$this->uri);
	}
	
	function uri($n)
	{
		return $this->uri[$n];
	}
	
	function uriComponents()
	{
		return $this->uri;
	}

	function urisize()
	{
		return sizeof($this->uri);
	}

	function urimask($n_uri, $minsize = 2, $n = 1)
	{
		if ($this->urisize() >= $minsize && $this->uri($n) == $n_uri)
			return true;
		else
			return false;
	}
	
	function __construct($R, $uri)
	{
		$this->apptitle = $R['app'];
		$this->uri = $uri;
		if (IS_SYSTEM_APP === true)
			$this->path = SYSTEM_APPS_DIR.$this->apptitle;
		else
			$this->path = APPS_DIR.$this->apptitle;
		// make message from post
		// TODO unset REQUEST, GET, POST
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$this->message = new Message($_POST);
		}
		$this->metadata = new stdClass();
		if (SystemLocale::$REQUEST_LANG == SystemLocale::default_lang())
			$this->langprefix = '';
		else $this->langprefix = '/'.SystemLocale::$REQUEST_LANG;
	}

	function setUsage($role, $user)
	{
		$this->role = $role;
		$this->user = $user;
		$this->context['user'] = $this->user;
		$this->context['role'] = $this->role; 
	}
	
	// reguest = for non existent methods AND != app/index
	function non_existent_method()
	{
		throw new NoAppMethodException();
	}

	// deprecate. use throw directly
	// TODO redirect from App m1 to m2, not to /base	
	function redirect($uri)
	{
		throw new RedirectException($uri);
	}
	
	private function currentRoleUser()
	{
		if ($this->user !== null) return true;
		$us = new Message('{"urn": "urn-user", "action": "session"}');
		$sess = $us->deliver();
		if ($sess->warning || $sess->error)
		{
			$this->role = "ANONYMOUSE";
		}
		else if ($user_urn = $sess->user)
		{
			$user = $user_urn->resolve()->current();
			$this->role = "USER";
			$this->user = $user;
            // $this->userrole = $user->role; // Lazy load
		}
	}

    public function checkRoleUriHome()
    {
        if (ENV == 'DEVELOPMENT') return;
        $this->userrole = $this->user->role; // TODO PRI
        $uri = '/'.join('/',$this->uri);
        $chk = strpos($uri, $this->userrole->homeuri);
        if ($chk !== 0) throw new SecurityException('Role access error');
    }

	private function checkRouteAccess($uriar, $App)
	{
		// WHO AM I?
		$this->currentRoleUser();
		if ($this->role != 'USER')
		{
			if ($_SERVER['REQUEST_METHOD'] == 'GET')
			{
				$ru = join('/', $uriar);
				Session::put("AuthedReturnUrl", $ru);
			}
			else
			{
				//
			}
			if ($App instanceof WebApplication)
				throw new TempRedirectException('/member/login', 401);
			else if ($App instanceof AjaxApplication)
				throw new AjaxRedirectException('/member/login', 401);
			else
				throw new SecurityException('App access error');
		}	
	}
	
	
	
	function runApp($App, $R, $uriar, $base=null)
	{
		$App = $this;
		$named_method_exists = method_exists($App, $uriar[1]); // app/ACTION?
		$view_method_exists = method_exists($App, $uriar[2]);
		$default_method_exists = method_exists($App, 'request');
		$resource_method_exists = method_exists($App, 'resource');
		$exclusive_method_exists = method_exists($App, 'exclusive');
		$deep_method_exists = method_exists($App, 'deep');
		$params = null;
		
		if ($exclusive_method_exists or $named_method_exists or $view_method_exists or $default_method_exists or $resource_method_exists or $deep_method_exists) // TODO remove this (app with _action_ only cant work without request() etc)
		{
			// * params
			if ($exclusive_method_exists) // app/*/*
			{
				$routecode = 8;
				$App->view = 'exclusive';
				$cutapp = array_shift($uriar);
				$params = $uriar;
			}
			// 1
			else if (count($uriar) == 1 && $default_method_exists) // /app (INDEX)
			{
				$routecode = 1;
				$App->view = 'request';
			}
			// 2
			else if (count($uriar) == 2 && $resource_method_exists && !$named_method_exists) // /app/resource (UPDATE | RESOURCE)
			{
				$routecode = 2;
				$App->view = 'resource';
				$params = $uriar[1];
			}
			else if (count($uriar) == 2 && $named_method_exists) // /app/named (ACTION | VIEW)
			{
				$routecode = 3;
				$App->view = $uriar[1];
			}
			// 3
			else if (count($uriar) == 3 && $named_method_exists) // /app/named/resource (ACTION ON OBJECT (post) | PARAMETERIZED (SINGLE) VIEW (get))
			{
				$routecode = 6;
				$App->view = $uriar[1];
				$params = $uriar[2];
			}
			else if (count($uriar) == 3 && $view_method_exists) // /app/resource/view (ASPECT OF OBJECT)
			{
				$routecode = 9;
				$App->view = $uriar[2];
				$params = $uriar[1];
			}
			// > 3
			else if (count($uriar) >= 3 && !$deep_method_exists && $named_method_exists) // app/named/*/*  (PARAMETERIZED (DEEP) VIEW (get))
			{
				$routecode = 10;
				$App->view = $uriar[1];
				$cutapp = array_shift($uriar);
				$params = $uriar;
			}
			else if (count($uriar) >= 3 && $deep_method_exists) // app/*/* (PARAMETERIZED (DEEP, NAMED METHOD COMPATIBLE) VIEW (get))
			{
				$routecode = 11;
				$App->view = 'deep';
				$cutapp = array_shift($uriar);
				$params = $uriar;
			}
			else throw new Exception('INTERNAL NO ROUTE Unmatched routing pattern');
		}
		else
		{
			throw new Exception("OUTER NO ROUTE Method not exists - named_method_exists({$uriar[1]}) or default_method_exists(request) or OTHERS both false");
			//$result = $App->non_existent_method();
		}
		Log::debug("route $routecode, app {$R['app']}, fn/view {$App->view}", 'webrequest');
		
		// ACCESS
		// Check access to App with route access config
		// TODO юзер нужен опционально, те если логед - показать кто, иначе - не редиректить на логин, а пустить анонимом
		if ($App instanceof ApplicationUserOptional)
		{
			$this->currentRoleUser();
			$App->setUsage($this->role, $this->user);
		}
		// TODO !!! move down to late rsolve
		if ($App instanceof ApplicationAccessManaged)
		{
			$this->checkRouteAccess($uriar, $App); // TODO $uriar is quick hack 
			$App->setUsage($this->role, $this->user);
		}
        if ($App instanceof ApplicationRoleHomeAccessManaged)
        {
            $this->checkRoleUriHome();
        }

		
		// APP RUN
		$fn = $App->view;
		if (method_exists($App, $fn))
		{
			// TODO APP METHOD CAN ECHO > BUFFER IT!
			ob_start();
			if ($base) $App->setBase($base);
			$App->init();
			$result = $App->$fn($params);
			$result .= ob_get_clean();
		}
		else
		{
			throw new SecurityException("Sitemap route to {$fn}() dosnt exists. Route code [{$routecode}]");
		}
		
		// $this->view = $App->view;
		// $this->layout = $App->layout;
		// $this->widgets = $App->widgets;
		// $this->widget_options = $App->widget_options;
		
		// BY RET VAL POLY
		if ($result instanceof Message)
		{
			$this->layout = false;
			$this->view = false;
			$outbuffer = (string) $result; // to json
		}
		else // TODO  instanceof HTML
		{
			$outbuffer = $result;
			// OPEN BUFFER
			ob_start();
			// LOAD VIEW. View Context is setted up in $App
			if ($App instanceof WebApplication)
			{
				// TODO direct output is debug only. NOT PART OF RESULT HTML/JSON (и всегда будет возвращет валидный json с кодом ошибки)
				ob_start();
				$App->load_view();
				$outbuffer .= ob_get_clean();
				//ob_end_clean();
			}
			else if ($App instanceof AjaxApplication) 
			{
				// выше приложение уже отдало Message as result
			}
			else throw new Exception('Unknoun type of APP');
		}
		
		$HTML = $outbuffer;
		
		// WIDGETS (legacy save as $$name globals) (WebApp only)
		//Log::info(json_encode($this->widget_options),'widget');
		foreach ($this->widgets as $widgetc)
		{
			//Log::info(json_encode($widgetc),'widgets');
			$position = $widgetc['position'];
			$widget_name = $widgetc['widget_name'];
			$options = $widgetc['options'];
			
			$$position .= load_widget($widget_name, $options);
			//Log::info($$position, 'widgets');
		}
		//exit();
		// LAYOUT MAIN
		$outbuffer = '';
		ob_start();
		if ($this->layout) 
		{
			if (SystemLocale::$REQUEST_LANG == SystemLocale::default_lang())
				$_langprefix = '';
			else
			{
				$_langprefix = SystemLocale::$REQUEST_LANG;
				$_langprefix2 = $_langprefix.'/';
				$langpath = "{$_langprefix}/";
				$_langURI = '/'.SystemLocale::$REQUEST_LANG;
			}
			if (file_exists($layoutfile)) 
				$layoutfile = BASE_DIR."/views/layout/{$langpath}{$this->layout}.php";
			else
				$layoutfile = BASE_DIR."/views/layout/{$this->layout}.php";
			if (ENV === 'DEVELOPMENT')
			{
				if (file_exists($layoutfile)) 
					include $layoutfile;
				else
					throw new Exception("Layout file for `{$this->layout}` not exists");
			}
			else 
				include $layoutfile;
		}
		else 
		{
			echo $HTML;
		}
		//$outbuffer = ob_get_contents();
        //ob_end_flush();
        $outbuffer = ob_get_clean(); // Layout + app main widget (before widgets)
		ob_end_clean();
		
		
		// WIDGETS place (replace <{$position} />)
		foreach ($this->widgets as $widgetc)
		{
			$position = $widgetc['position'];
			$wperpos[$position] = $$position;
		}
		foreach ($wperpos as $position => $widget)
		{
			$outbuffer = str_replace("<{$position} />", $widget, $outbuffer);
		}
		// /WIDGETS
		
		return array("data" => $outbuffer, "metadata" => $App->metadata());	
	}
}

?>