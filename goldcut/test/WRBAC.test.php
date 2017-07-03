<?php require_once dirname(__FILE__).'/../goldcut/boot.php';

define('DEBUG_SQL',TRUE);
define('NOMIGRATE',TRUE);
define('DEBUG_WRBAC',TRUE);
define('DEBUG_SQL',TRUE);


class WRBACTest implements TestCase
{
	private $vars = array();
	public $fixtures = array('category','ad');
	
	/**
	u1 in g1, roles [Ads administrator, Shops administrator(Group1Role!)] u902 in g2, roles [Registered user]
	*/
	function instantiateWRBAC()
	{
		WRBAC::instance();
		
		$u1 = new WRBACUser();
		$u2 = new WRBACUser();
		$g1 = new WRBACGroup();
		$g2 = new WRBACGroup();
		
		$u1->id = 1;
		$u1->groups []= $g1;
		$g1->users []= $u1;
		
		$roleAdminAd = new WRBACRole();
		$roleAdminAd->name = 'Ads administrator';
		$roleAdminAd->urns []= 'urn-ad';
		
		$roleAdminShop = new WRBACRole();
		$roleAdminShop->name = 'Shops administrator';
		$roleAdminShop->urns []= 'urn-shop';
		$roleAdminShop->urns []= 'urn-shopitem';

		// USER 1 ROLES + GROUP 1 ROLES
		$u1->roles []= $roleAdminAd;
		$g1->roles []= $roleAdminShop;

		$roleRegistered = new WRBACRole();
		$roleRegistered->name = 'Registered user';
		$roleRegistered->urns []= 'urn-ad';
		$roleRegistered->urns []= 'urn-category';

		// 902 ROLES (+ not a group member)
		$u2->id = 902;
		$u2->roles []= $roleRegistered;

		// PERMS (5)
		$permCRUD = new WRBACPermission();
		$permCRUD->actionsAllowed = array('load','create','update','delete');
		$permCRUD->fieldsProtected = array('password');
		$permCRUD->name = 'allow any crud on objects defined in a role';

		$permReadOnly = new WRBACPermission();
		$permReadOnly->actionsAllowed = array('load');
		$permReadOnly->fieldsProtected = array('password');
		$permReadOnly->name = 'allow only read on objects defined in a role';

		$permHOLD = new WRBACPermission();
		$permHOLD->actionsAllowed = array('hold');
		$permHOLD->fieldsProtected = array();
		$permHOLD->name = 'allow do hold on objects defined in a role';
		
		$permMaintain = new WRBACPermission();
		$permMaintain->actionsAllowed = array('maintain');
		$permMaintain->fieldsProtected = array();
		$permMaintain->name = 'allow do maintain on objects defined in a role';
		
		// DYN ROLE 1 OBJECT OWNER
		$roleAdOwner = new WRBACRole();
		$roleAdOwner->name = 'Ad Object owner main';
		$roleAdOwner->urns []= 'urn-ad'; // note - double linked - role to ad, ownersRoles['urn-ad'] back to this role
		$roleAdOwner->type = 2; // ?
		// DYN ROLE 2 OBJECT OWNER
		$roleAdOwner2 = new WRBACRole();
		$roleAdOwner2->name = 'Ad Object owner srv';
		$roleAdOwner2->urns []= 'urn-ad';
		$roleAdOwner2->type = 2; // ?
		
		
		$permUD = new WRBACPermission();
		$permUD->actionsAllowed = array('update','delete','some');
		$permUD->fieldsProtected = array('password');
		$permUD->name = 'allow update, delete on objects defined in a role';
		
		// urn-ad dynr1 owner perms
		$roleAdOwner->permissions []= $permUD;
		$roleAdOwner->permissions []= $permHOLD;
		// urn-ad dynr2 owner perms
		$roleAdOwner2->permissions []= $permMaintain;
		
		// general roles perms
		$roleAdminAd->permissions []= $permCRUD;
		$roleAdminShop->permissions []= $permCRUD;
		
		$roleRegistered->permissions []= $permReadOnly;
		//$roleRegistered->permissions []= $permCRUD; // allow registered any crud on ad, category
		
		// SAVE ALL
		WRBAC::instance()->users[1] = $u1; // by uuid
		WRBAC::instance()->users[902] = $u2;
		WRBAC::instance()->groups []= $g1;
		WRBAC::instance()->groups []= $g2;
		WRBAC::instance()->roles []= $roleRegistered;
		WRBAC::instance()->roles []= $roleAdminShop;
		WRBAC::instance()->roles []= $roleAdminAd;
		
		WRBAC::instance()->roles []= $roleAdOwner;
		WRBAC::instance()->roles []= $roleAdOwner2;
		WRBAC::instance()->ownersRoles['urn-ad'] []= $roleAdOwner; // can add several roles for urn-entity owner
		WRBAC::instance()->ownersRoles['urn-ad'] []= $roleAdOwner2; // can add several roles for urn-entity owner
		
		WRBAC::instance()->permissions []= $permReadOnly;
		WRBAC::instance()->permissions []= $permCRUD;
		WRBAC::instance()->permissions []= $permHOLD;
		WRBAC::instance()->permissions []= $permMaintain;
		WRBAC::instance()->permissions []= $permUD;

		print '<pre>';
		print_r($roleRegistered); 
		print '</pre>';

	}
	
	/**
	test u1 can do [create] on urn-ad. $u1->can('create', 'urn-ad') 
	test u1 can not do [some] on urn-ad
	*/
	function testAdCreateByUser1InRolesAdAdmin()
	{
		$u1 = WRBAC::instance()->users[1];
		assertEqual($u1->id, 1);
		
		$canCreateUrnAd = $u1->can('create', 'urn-ad');
		assertNotFalse($canCreateUrnAd);
		print '<pre>';
		print_r($canCreateUrnAd); 
		print '</pre>';
		
		$canDeleteUrnAd = $u1->can('delete', 'urn-ad');
		assertNotFalse($canDeleteUrnAd);
		
		$canSomeUrnAd = $u1->can('some', 'urn-ad');
		assertFalse($canSomeUrnAd);
		
		$canCreateUrnCategory = $u1->can('create', 'urn-category');
		assertFalse($canCreateUrnCategory);
		
	}
	
	/**
	test u1 can do [create] on urn-categpry by group access
	*/
	function testShopItemCreateByUser1InGroupRoleShopAdmin()
	{
		$u1 = WRBAC::instance()->users[1];
		assertEqual($u1->id, 1);
		
		// $userRoles = $u1->getRoles();
		// foreach($userRoles as $userRole) println($userRole->name);
		
		$canCreateUrnShopItem = $u1->can('create', 'urn-shopitem');
		assertNotFalse($canCreateUrnShopItem);
		print '<pre>';
		print_r($canCreateUrnShopItem); 
		print '</pre>';
		
		$canCreateUrnShopItem = $u1->can('nonex', 'urn-shopitem');
		assertFalse($canCreateUrnShopItem);
	}

	/**
	test u2 can not create ad.
	test u2 can read category.
	*/	
	function testAdCreateByUser902inRoleRegistered()
	{
		$u2 = WRBAC::instance()->users[902];
		assertEqual($u2->id, 902);
		//$userRoles = $u2->getRoles();
		//foreach($userRoles as $userRole) println($userRole->name);
		$canCreateUrnAd = $u2->can('create', 'urn-ad');
		assertFalse($canCreateUrnAd);
		print '<pre>';
		print_r($canCreateUrnAd); 
		print '</pre>';
		$canDeleteUrnAd = $u2->can('delete', 'urn-ad');
		assertFalse($canDeleteUrnAd);
		$canSomeUrnAd = $u2->can('some', 'urn-ad');
		assertFalse($canSomeUrnAd);
		
		// assertUserCanNot($user, 'create', 'urn-category')
		// assertUserCan($user, 'load', 'urn-category')
		
		$canCreateUrnCategory = $u2->can('create', 'urn-category');
		assertFalse($canCreateUrnCategory);
		
		$canCreateUrnCategory = $u2->can('load', 'urn-category');
		assertNotFalse($canCreateUrnCategory);
		print '<pre>';
		print_r($canCreateUrnCategory); 
		print '</pre>';
	}
	

	private function obtainUrnOwnerRoleDyn($userID, $urn)
	{
		// user in rbac
		$me = &WRBAC::getUserByID($userID);
		$own = $me->proveOwnership($urn);
		
		// var_dump($own);
		/*
		printH('rolesAdOwner');
		print '<pre>';
		foreach($me->roles as $roleAdOwner)
			print_r($roleAdOwner);
			//print_r($roleAdOwner->permissions);
		print '</pre>';
		*/
		
		// later check stage
		// $userRoles = $me->getRoles();
		
		$ownerCan = $me->can('load', 'urn-ad');
		// assertNotFalse($ownerCan);
				
		$ownerCan = $me->can('update', 'urn-ad');
		// assertNotFalse($ownerCan);
		
		$ownerCan = $me->can('delete', 'urn-ad');
		// assertNotFalse($ownerCan);
		
		$ownerCan = $me->can('maintain', 'urn-ad');
		// assertNotFalse($ownerCan);
		
		$ownerCan = $me->can('hold', 'urn-ad');
		// assertNotFalse($ownerCan);
		
		$ownerCanNot = $me->can('create', 'urn-ad');
		//assertFalse($ownerCanNot);
		var_dump($ownerCanNot);
	}
	
	/**
	user 902 is owner of urn-ad-2. can all except [create]
	*/	
	function obtainUrnOwnerRoleDynAD2of902()
	{
		$this->obtainUrnOwnerRoleDyn(902, 'urn-ad-2');
	}

	/**
	user 902 is NOT owner of urn-ad-7. As Registered can [load] but fail on [create, update]
	*/	
	function obtainUrnOwnerRoleDynAD7of902()
	{
		$this->obtainUrnOwnerRoleDyn(902, 'urn-ad-7');
	}
	
	/**
	user 1 can create any urn-ad (ad-7 owned by user-1) - Data is mine (for hold & maintain) & I'm the admin
	*/
	function obtainUrnOwnerRoleDynAD7of1()
	{
		// pendingTest();
		$this->obtainUrnOwnerRoleDyn(1, 'urn-ad-7');
	}
	
	/**
	user 1 can update any not self owning urn-ad (urn-ad-2 owner is urn-user-902) (but 1 can not do owner only actions - will fail on [maintain, hold])
	*/
	function obtainUrnOwnerRoleDynAD2of1()
	{
		// pendingTest();
		$this->obtainUrnOwnerRoleDyn(1, 'urn-ad-2');
	}
	
	function registeredCantReadProfileButOwnerCan()
	{
		pendingTest();
	}
	
	function obtainUrnNONOwnerRole()
	{
		// user in rbac
		$me = WRBAC::getUserByID(902);
		
		// user in db
		$m = new Message();
		$m->action = 'load';
		$m->urn = 'urn-ad-7';
		$object = $m->deliver();
		assertNOTEqual($object->user_id, $me->id);
	}
	
	function acessibleEntitiesForUser1()
	{
		$userID = 1;
		$user = WRBAC::getUserByID($userID);
		$es = $user->acessibleEntities();
		println($es);
	}
	
	function acessibleActionsForUser1OnEntityAd()
	{
		$userID = 1;
		$user = WRBAC::getUserByID($userID);
		$es = $user->acessibleActionsOnEntity('urn-ad');
		println($es);
	}
	
	// + if owner on concrete urns
	function acessibleActionsForUser902OnEntityAdOwned()
	{
		$userID = 902;
		$user = WRBAC::getUserByID($userID);
		$es = $user->acessibleActionsOnEntity('urn-ad-2');
		println($es);
	}
	
	private function testSerializeWRBACInstance()
	{
		$s = serialize(WRBAC::instance());
		//print_r($s);
		$this->vars['ser'] = $s;
	}
	
	private function testUnSerializeWRBACInstance()
	{
		$s = unserialize($this->vars['ser']);
		WRBAC::replaceInstance($s);
		//print_r($s);
		$u1 = WRBAC::instance()->users[1];
	}
	
	function obtainAnonRoleByDefault()
	{
		pendingTest();
	}
	
	function obtainRegistedOnlineRole()
	{
		pendingTest();
	}
	
	/**
	передать можно не более, чем можешь сам!
	for grant list own role permissions. 
	grant whole role - add role to user + add roleId/UserId/GranterId to revokeLater table
	grant uniq combination from all roles permissions (меньше прав, чем при полном делегировании ролей)
	on any granted perm check if you can to it self
	?? OR just include user to same group for all roles "copy"?
	*/
	function delegateRole()
	{
		// delegate self full with revoke right
		pendingTest();
	}
	/**
	id owner or entity manager grant access to id or whole entity to some user.
	? access created as role with permission
	*/
	function grantPermission()
	{
		pendingTest();
		//[urn][] = GrantedRolePermission (whoGranted)
	}
	
	/**
	Access Request Objects.
	user in role AccessByRequest send request to owner or whole manager
	*/	
	function accessRequestObjects()
	{
		pendingTest();
	}
}

/**
PRELOAD

WRBAC::instance();
	
$roleAdminAd = new WRBACRole();
$roleAdminAd->name = 'Ads administrator';
$roleAdminAd->urns []= 'urn-ad';

$roleAdminShop = new WRBACRole();
$roleAdminShop->name = 'Shops administrator';
$roleAdminShop->urns []= 'urn-shop';

$roleRegistered = new WRBACRole();
$roleRegistered->name = 'Registered user';
$roleRegistered->urns []= 'urn-ad';
$roleRegistered->urns []= 'urn-category';
WRBAC::instance()->roles['roleRegistered'] = $roleRegistered;

// --
$u2 = new WRBACUser();
$u2->id = 902;
$u2->roles []= WRBAC::instance()->roles['roleRegistered'];
WRBAC::instance()->users[902] = $u2;

$u3 = new WRBACUser();
$u3->id = 903;
$u3->roles []= WRBAC::instance()->roles['roleRegistered'];
WRBAC::instance()->users[903] = $u3;
// --

// PERMS
$permCRUD = new WRBACPermission();
$permCRUD->actionsAllowed = array('load','create','update','delete');
$permCRUD->fieldsProtected = array('password');
$permCRUD->name = 'allow any crud on objects defined in a role';

$permCR = new WRBACPermission();
$permCR->actionsAllowed = array('load','create');
$permCR->fieldsProtected = array('password');
$permCR->name = 'allow Create or Load on objects defined in a role';

$permReadOnly = new WRBACPermission();
$permReadOnly->actionsAllowed = array('load');
$permReadOnly->fieldsProtected = array('password');
$permReadOnly->name = 'allow only read on objects defined in a role';

// DYN ROLE 1 OBJECT OWNER
$roleAdOwner = new WRBACRole();
$roleAdOwner->name = 'Ad Object owner main';
$roleAdOwner->urns []= 'urn-ad'; // note - double linked - role to ad, ownersRoles['urn-ad'] back to this role
$roleAdOwner->type = 2; // ?

// ROLE +link to+ PERMS
		
// urn-ad dynr1 owner perms
$roleAdOwner->permissions []= $permCRUD; // -C

// general roles perms
$roleAdminAd->permissions []= $permCRUD;
$roleAdminShop->permissions []= $permCRUD;

$roleRegistered->permissions []= $permCR;

// SAVE ALL



WRBAC::instance()->roles []= $roleAdminShop;
WRBAC::instance()->roles []= $roleAdminAd;

WRBAC::instance()->roles []= $roleAdOwner;

WRBAC::instance()->ownersRoles['urn-ad'] []= $roleAdOwner; // can add several roles for urn-entity owner

WRBAC::instance()->permissions []= $permReadOnly;
WRBAC::instance()->permissions []= $permCRUD;
WRBAC::instance()->permissions []= $permCR;

*/

?>