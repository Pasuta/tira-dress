<?php require_once dirname(__FILE__).'/../../goldcut/boot.php';

define('DEBUG_SQL',TRUE);
define('DEBUG_WRBAC',TRUE);
define('ENABLE_WRBAC',TRUE);

/**
add global UserServed. system will auto set it as $user owner for objects
add entity "owned" type - need UserServed for 

realms - user can have own DB - realm VS+ every realm has own db with own users (inner realm) 
*/

class AccessManagedTest implements TestCase
{
	//public $fixtures = true;
	
	function prepareWRBAC()
	{	
		$u2 = new WRBACUser();
		$u2->id = 902;
		$u2->roles []= WRBAC::instance()->roles['roleRegistered'];
		WRBAC::instance()->users[902] = $u2;

		$u3 = new WRBACUser();
		$u3->id = 903;
		$u3->roles []= WRBAC::instance()->roles['roleAdminShop'];
		WRBAC::instance()->users[903] = $u3;
		
		$u4 = new WRBACUser();
		$u4->id = 904;
		$u4->roles []= WRBAC::instance()->roles['roleAdminShop'];
		WRBAC::instance()->users[904] = $u4;
		
		$g1 = WRBAC::instance()->groups['g1'];		
		$u4->groups []= WRBAC::instance()->groups['g1'];
		WRBAC::instance()->groups['g1']->users []= $u4;
		/*
		print "<pre>";
		print_r(WRBAC::instance()->users);
		print "<hr>";
		print_r(WRBAC::instance()->roles);
		print "<hr>";
		print_r(WRBAC::instance()->ownersRoles);
		print "</pre>";
		*/
	}
	
	function debugRoles()
	{
		foreach (WRBAC::allRolesToStringsArray() as $rolestr)
		{
			println($rolestr);
		}
	}
	
	function debugUsersRoles()
	{
		WRBAC::allUsersRolesDebug();
	}
	
	function createAdManaged()
	{
		$m = new ManagedMessage();
		$m->action = 'create';
		$m->urn = 'urn-ad';
		$m->id = 123;
		$m->user = 'urn-user-902';
		$r = $m->deliver();
	}

	function loadAdManagedOwn()
	{
		$m = new ManagedMessage();
		$m->action = 'load';
		$m->urn = 'urn-ad-123';
		$m->user = 'urn-user-902';
		$ds = $m->deliver();
		assertURN($ds->urn);
	}
	
	function loadAdManaged903Hack()
	{
		$m = new ManagedMessage();
		$m->action = 'load';
		$m->urn = 'urn-ad-123';
		$m->user = 'urn-user-903';
		assertThrowsException($m);
		//$r = $m->deliver();
	}

	function createAdManaged903()
	{
		//var_dump(WRBAC::instance()->users);
		$m = new ManagedMessage();
		$m->action = 'create';
		$m->urn = 'urn-ad';
		$m->id = 9003;
		$m->user = 'urn-user-903';
		assertThrowsException($m);
	}
	
	function loadAdManagedThinkOwnButNotCreated903()
	{
		$m = new ManagedMessage();
		$m->action = 'load';
		$m->urn = 'urn-ad-9003';
		$m->user = 'urn-user-903';
		assertThrowsException($m);
	}
	
	function createShopManaged902Hack()
	{
		$m = new ManagedMessage();
		$m->action = 'create';
		$m->urn = 'urn-shop';
		$m->id = 123;
		$m->user = 'urn-user-902';
		assertThrowsException($m);
	}
	
	function createShopManaged903()
	{
		$m = new ManagedMessage();
		$m->action = 'create';
		$m->urn = 'urn-shop';
		$m->id = 125;
		$m->user = 'urn-user-903';
		$ds = $m->deliver();
		assertURN($ds->urn);
	}
	
	function createShopManaged904ByGroupRole()
	{
		$m = new ManagedMessage();
		$m->action = 'create';
		$m->urn = 'urn-shop';
		$m->id = 128;
		$m->user = 'urn-user-904';
		$shop = $m->deliver();
		assertURN($shop->urn);
	}


}
?>