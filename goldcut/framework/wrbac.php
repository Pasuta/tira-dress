<?php

// http://hitachi-id.com/identity-manager/docs/beyond-roles.html
// Audit - actions, grants, acl check fails

/*
Business case - hosting control
SCOPE - client_id. client has subusers. 
Account Owner delegates access to whole email subsystem (role EmailManager)
System groups, user owned groups (role canCreateGroup) with scope - needed field client_id scope set
roles: AccountRoot(urn-account-id, scope?), 
	WebSiteManager, EmailManager, DNSManager, PRParkingServiceManager - all (many websites etc) in account scope
	те юзеров или сабюзеров много, а realm/scope у всех один
delegate with/without right to delegate next
ROLE condition - can delete invoice if status is unpayed and selected payment method != bank (mark as deleted, real delete in 7 days) bank id = userid/invoiceid

can ==> gain access, power, permission
+ add check multiple actions at once

! file upload perm
! media convert perm ==? create photo, create video, create audio?

return array which urn-entity i have any actions access
return what i can do with urn-entity

XML store of instance conf can be shared with node.js, python, java

::registerUser
::registerGroup
::addUserToGroup(user, group)
::removeUserFromGroup(user, group)
::registerRole(role)
::addPermissionToRole(perm, role)
::registerPermission(perm)
::assignRoleToUser(role, user) or userActAsRole(user, role)
::unassignRoleFromUser(role, user) userLoseRole(user, role)

{
	role has [permissions] on [urns]
	user has roles
	groups has roles
	user may belongs to group

	permission allow actions and filter fields on urns defined by role
	
	Special array of roles for each urn-entity _owner_ 
		WRBAC::instance->ownersRoles['urn-ad'][]
		dynamic aquiring of roles is per user in memory and not stored - NEED cleanup of type 2 roles from users OR sync after rbac admin ops (atomic, not mixed with dyn role aruire requests) on instance but never save back modified instance (and good for long requests to not lock instance!)
		instance wrbac change is singe threaded locked operation
}

linkexists access? object, with-subject? ownership of object or subject

*/

class WRBAC
{
	public $users = array();
	public $groups = array();
	public $roles = array();
	public $ownersRoles = array();
	//public $managersRoles = array(); // whole entity manager
	//public $delegatedRoles = array(); // potentially big list
	public $permissions = array();
	private static $instance;
	private function __construct() {}
		
	public static function instance()
	{
		if (!self::$instance) self::$instance = new WRBAC(); 
		return self::$instance; 
	}
	
	public static function replaceInstance($instance)
	{
		self::$instance = $instance; 
	}
	
	public static function &getUserByID($userID)
	{
		return self::instance()->users[$userID];
	}
	
	public static function allRolesToStringsArray()
	{	
		$rs = array();	
		foreach (WRBAC::instance()->roles as $role)
		{
			foreach ($role->permissions as $perm)
			{
				$permActionsStr = join(', ', $perm->actionsAllowed);
			}
			$rs[] = "{$role->name} (".join(', ',$role->urns). ") $permActionsStr";
		}
		foreach (WRBAC::instance()->ownersRoles as $urn => $ownerroles)
		{
			foreach ($ownerroles as $role)
			{
				foreach ($role->permissions as $perm)
				{
					$permActionsStr = join(', ', $perm->actionsAllowed);
				}
				$rs[] = "{$role->name} @($urn) $permActionsStr";
			}
		}
		return $rs;
	}
	
	public static function allUsersRolesDebug()
	{
		foreach (WRBAC::instance()->users as $user)
		{
			foreach ($user->groups as $group)
			{
				$groupNames []= $group->name;
			}
			$userStr = "{$user->id} [".join(', ',$groupNames)."]";
			println($userStr);
			foreach ($user->roles as $role)
			{
				foreach ($role->permissions as $perm)
				{
					$permActionsStr = join(', ', $perm->actionsAllowed);
				}
				println("{$role->name} (".join(', ',$role->urns). ") $permActionsStr",2);
			}
		}
	}
	
}

class WRBACUser
{
	//public $userRolen;
	public $id;
	public $groups = array();
	public $roles = array();
	
	public function getRoles()
	{
		$roles = array();
		foreach($this->roles as $role) $roles []= $role;
		foreach($this->groups as $group)
		{
			foreach($group->roles as $role) $roles []= $role;
		}
		return $roles;
	}
	
	public function proveOwnership($urn)
	{
		$me = $this;
		$userID = $me->id;
		
		// user in db
		$m = new Message();
		$m->action = 'load';
		$m->urn = $urn;
		$object = $m->deliver();
		if (!count($object)) return null;
		
		if (DEBUG_WRBAC === true)
		{
			if ($object->user_id == $me->id)	
				dprintln('Object ownership APPROVED. Dynamic merge user roles with per urn owner role for current session',1,TERM_GRAY);
			else
				dprintln('Object ownership NOT APPROVED',1,TERM_GRAY);
		}
				
		// clear owner roles from prev test
		foreach($me->roles as $idx => &$roleAdOwner)
		{
			if ($roleAdOwner->type == 2) // owned type
				unset(WRBAC::instance()->users[$userID]->roles[$idx]);
		}
			
		// on ownership dyn add role to WRBAC instance (WITHOUT PERMANENT SAVING!)
		if ($object->user_id == $me->id)
		{
			$generalURN = (string) $object->urn->generalize();
			$rolesAdOwner = WRBAC::instance()->ownersRoles[$generalURN];  // array! // TODO urn->generalize() !!!!!!!!!!!!!!!!!!!!!!
			WRBAC::instance()->users[$userID]->roles = array_merge($me->roles, (array) $rolesAdOwner);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function acessibleEntities()
	{
		$hasAccessTo = array();
		$userRoles = $this->getRoles();
		foreach($userRoles as $userRole)
		{
			// dprintln("+ user has role {$userRole->name}",2,TERM_GRAY);
			// println($userRole->urns);
			$hasAccessTo = array_merge($hasAccessTo, $userRole->urns);
			// foreach($userRole->permissions as $perm) dprintln($perm->actionsAllowed);
		}
		return $hasAccessTo;
	}
	
	// + if owner on concrete urns
	public function acessibleActionsOnEntity($onUrn)
	{
		$onUrnObject = new URN($onUrn);
		if ($onUrnObject->is_concrete())
		{
			$own = $this->proveOwnership($onUrn);
			if ($own) $onUrn = $onUrnObject->generalize();
			else return false; // or array()?
		}
		$actionsAllowed = array();
		$userRoles = $this->getRoles();
		foreach($userRoles as $userRole)
		{
			// dprintln("+ user has role {$userRole->name}",2,TERM_GRAY);
			if (in_array($onUrn, $userRole->urns))
			{
				// println($userRole->urns);				
				foreach($userRole->permissions as $perm)
				{
					$actionsAllowed = array_merge($actionsAllowed, $perm->actionsAllowed);
					// dprintln($perm->actionsAllowed);
				}				
			}
		}
		return $actionsAllowed;
	}
	
	/*
	check action. all user groups. all roles of user & it groups.
	find role having perm for !urn-ad/create. 
	process to action with param(fieldsAllowed/Protected). warn on try use protected/unallowed
	*/
	function can($action, $onUrn)
	{
		//if (DEBUG_WRBAC === true) 
		dprintln("? user [{$this->id}] requests access to do [{$action}] on [{$onUrn}]",1,TERM_GRAY);
		$userRoles = $this->getRoles();
		foreach($userRoles as $userRole)
		{
			//if (DEBUG_WRBAC === true) 
			dprintln("? try role [{$userRole->name}] with urns ".json_encode($userRole->urns),1,TERM_GRAY);
			if (in_array($onUrn, $userRole->urns)) // to with urns this role related
			{
				//if (DEBUG_WRBAC === true) 
				dprintln("+ user has role [{$userRole->name}] that has relation (but not permission yet!) to [{$onUrn}]",2,TERM_GRAY);
				foreach($userRole->permissions as $perm)
				{
					if (in_array($action, $perm->actionsAllowed))
					{
						//if (DEBUG_WRBAC === true) d
						println("+ granted access for `{$action}` `{$onUrn}` by Perm (`{$perm->name}`) in role [{$userRole->name}]",3,TERM_GREEN);
						return $perm;						
					}
				}
			}
		}
		return false;
	}
}
class WRBACGroup
{
	public $name;
	public $users = array();
	public $roles = array();
}
/*
roles protected from deletion or change
selectable roles
system roles - owner (Scope of role)
delegated roles - internal, not selectable
*/
class WRBACRole
{
	public $name;
	public $type = 1;
	//public $userRolens = array();
	public $permissions = array();
}
// Atomic? actionsAllowed not array. Fields are actual not only for update but for any custom actions 
class WRBACPermission
{
	public $name;
	public $actionsAllowed = array();
	// IN
	public $fieldsOnlyAllowed;// = array();
	public $fieldsProtected;// = array();
	// OUT
	// TODO ADD OUT/RETURN FILTERING
}

class WRBACException extends Exception {}

?>