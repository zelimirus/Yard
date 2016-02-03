<?php
/**
 * Class for ACL controll
 *
 * @author   Aleksandar Varnicic | tasmaniski@gmail.com
 */
class My_Plugin_AclControl extends Zend_Controller_Plugin_Abstract
{

    protected $_auth    = null;
    protected $_acl        = null;

    /**
     * Constructor
     *
     * @param Instance of Zend_Auth $auth
     * @param Instance of Zend_Acl $acl
     */
    public function __construct(Zend_Auth $auth, Zend_Acl $acl)
    {
        $this->_auth = $auth;
        $this->_acl     = $acl;
    }

    /**
     * ACL Access Check in preDispatch method
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!$this->_auth->hasIdentity()) {
            $request->setModuleName('admin');
            $request->setControllerName('auth');
            $request->setActionName('login');
            return;
        }
                
        // What is user(role) loged in
        $role           = $this->_auth->getIdentity()->role_id;

        // What is the current Resource
        $resource_table = new Admin_Model_Resources();
        $resource_name    = $request->getModuleName().':'.$request->getControllerName();
        $resource    = $resource_table->getByName(strtolower($resource_name));
        $resource_id    = isset($resource->id) ? $resource->id : null;

        // Get current privilage ( == action )
        $action         = strtolower($request->getActionName());
        
        if (!$this->_acl->hasRole($role)) {
            throw new Exception("Role not found in Database.", 404);
        } elseif (!$this->_acl->hasResource($resource_id)) {
            throw new Exception("Resource not found in Database.", 404);
        } elseif (!$this->_acl->isAllowed($role, $resource_id, $action)) {
            throw new Exception("You dont have permission for this page.", 404);
        }
    }
}
