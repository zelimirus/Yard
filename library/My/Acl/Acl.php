<?php
/**
 * Class for initialize The ACL
 * Setting up resources, roles and permisions
 *
 * @category Class
 * @package  App
 * @author   Aleksandar Varnicic | tasmaniski@gmail.com
 *
 */
class My_Acl_Acl extends Zend_Acl{

	private $acl = null;

	function __construct(){
		$this->acl = new Zend_Acl();
		$this->_initRoles();
		$this->_initResources();
		$this->_initPermissions();
	}


	function isAllowed($role_id = null, $resource_id = null, $privilege = null){
		return $this->acl->isAllowed($role_id, $resource_id, $privilege);
	}

	public function hasResource($resource){
		return $this->acl->has($resource);
	}

	public function hasRole($role){
		return $this->acl->hasRole($role);
	}

	private function _initRoles(){
		$roles_table = new Admin_Model_Roles();
		$roles = $roles_table->getAll();

		foreach($roles as $role){
			$this->acl->addRole(new Zend_Acl_Role($role->id));
		}
	}

	private function _initResources(){
		$resources_table = new Admin_Model_Resources();
		$resources = $resources_table->getAll();

		foreach($resources as $resource){
			$this->acl->addResource(new Zend_Acl_Resource($resource->id));
		}
	}

	private function _initPermissions(){
		$permissions_table = new Admin_Model_Permissions();
		$permissions = $permissions_table->getAll();

		foreach($permissions as $permission){
			if($permission->is_allowed){
				$this->acl->allow($permission->role_id, $permission->resource_id, $permission->action);
			}
			else{
				$this->acl->deny($permission->role_id, $permission->resource_id, $permission->action);
			}
		}
	}

}