<?php
/**
 * Auth Controller class in admin module
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  october 2014
 */
class Admin_AuthController extends Zend_Controller_Action{
	/**
	 * login Action
	 *
	 * @return mixed
	 */	
	public function loginAction(){
		// If user already logged in, just redirect
		if(Zend_Auth::getInstance()->hasIdentity()){
			$this->_redirect('admin/index');
		}
		$this->_helper->layout()->disableLayout();
		$this->_helper->layout()->getView()->headTitle('Login');

		if($this->_request->isPost()){
			$email = $this->_request->getParam('email');
			$password = $this->_request->getParam('password');
			if (!empty($email) && !empty($password) && strlen($email)>2 && strlen($password)>2 ) {
				$auth_adapter    = $this->getAuthAdapter();
				$auth_adapter->setIdentity($this->_request->getParam('email'));
				$auth_adapter->setCredential($this->_request->getParam('password'));
				// Does user a valid one
				if($auth_adapter->authenticate()->isValid()){
					// The default storage is a session with namespace Zend_Auth
					$auth        = Zend_Auth::getInstance();
					$authstorage = $auth->getStorage();
					$userinfo    = $auth_adapter->getResultRowObject(null, 'password');
					$authstorage->write($userinfo);
					
					//My_Utilities::logAction(Zend_Registry::get('login_action_id'));
					
					$this->_redirect('admin/index');

				}
			}
		}
	}

	/**
	 * logout Action. Clear session
	 *
	 * @return mixed
	 */
	public function logoutAction(){
		//My_Utilities::logAction(Zend_Registry::get('logout_action_id'));
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect('admin/auth/login');
	}

	/**
	 * Gets the adapter for authentication against a database table
	 *
	 * @return object
	 */
	protected function getAuthAdapter(){
		$auth_adapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
		$auth_adapter->setTableName('admin_users');
		$auth_adapter->setIdentityColumn('email');
		$auth_adapter->setCredentialColumn('password');
		$auth_adapter->setCredentialTreatment('MD5(?) AND is_active=TRUE');
		return $auth_adapter;
	}
}