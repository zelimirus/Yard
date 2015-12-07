<?php
/**
 * Users Controller class in Admin module
 * 
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * 
 * @version  may 2013
 */
class Admin_AdminUsersController extends Zend_Controller_Action{
	
	private $admin_users_model;
	private $admin_users_languages_model;
	private $admin_actions_model;
	private $user;
	
	public function init(){        		
		$this->admin_users_model 			= new Admin_Model_AdminUsers();
		$this->admin_users_languages_model 	= new Admin_Model_AdminUsersLanguages();
		$this->admin_actions_model 			= new Admin_Model_LogAdminActions();
		$this->user 						= Zend_Auth::getInstance()->getIdentity();
	}
	public function indexAction(){
		$this->view->form = new Admin_Form_AdminUsersSearch;
		$this->view->page = (int)$this->_getParam('page', 1);	
		// Get sort from $_GET for pagination, default is id asc
		$sort = $this->_getParam('sort_by', 'id').' '.$this->_getParam('sort_type', 'asc');

		//Get select for pagination, create and configure pagination object
		$pagination_select		 = $this->admin_users_model->getSelectForPagination(array(), $sort);
		$this->view->paginator	 = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($pagination_select));
		$this->view->paginator->setItemCountPerPage(20);
		$this->view->paginator->setCurrentPageNumber($this->view->page);

		//Header for html table
		$this->view->header = array(
			array('first_name', 'Name', 200),
			array('email', 'Email address', 0),
			array('role_name', 'Usergroup', 0),
			array('is_active', 'Active', 50),
			array('', '', 200)
		);
	}

	public function newAction(){
		$this->view->headTitle()->prepend('Creating new user');
		$this->view->form = new Admin_Form_AdminUser();

		// Check is posted data valid, if is it than insert user
		if($this->_request->isPost() && $this->view->form->isValid($_POST)){
			if($this->admin_users_model->ifEmailExist($this->_getParam('email'))){
				My_Utilities::fmsg('Email is already registered.', 'error');
				return;
			}
			$values = $this->view->form->getValues();

			$result = $this->admin_users_model->doSave($values);
			if($result > 0){
				My_Utilities::fmsg('Data is succesfully saved.');
			}else{
				My_Utilities::fmsg('Error. Data is not saved.', 'error');
			}
			$this->_redirect('admin/admin-users/index');
		}
	}

	public function editAction(){
		$this->view->headTitle()->prepend('Edit User');

		// Try to find record by user id
		$this->view->existing	 = $this->admin_users_model->getById((int)$this->_getParam('id', 0));

		if($this->user->role_id != Zend_Registry::get('superadmin_role_id') && $this->view->existing->id != $this->user->id){
			My_Utilities::fmsg('Permission denied: You can perform edit operation only on your own account.', 'warning');
			$this->_redirect('admin/index');
		}
			
		// If row does not exist, redirect to list with appropriate message
		if(!isset($this->view->existing->id) || empty($this->view->existing->id)){
			My_Utilities::fmsg('Record not found.', 'warning');
			$this->_redirect('admin/admin-users/index');
		}

		// Create an instance of form and set defaults
		$this->view->form = new Admin_Form_AdminUser();

		$this->view->form->setDefaults($this->view->existing->toArray());

		if($this->user->role_id != Zend_Registry::get('superadmin_role_id')){
			$this->view->form->removeElement('role_id');
			$this->view->form->removeElement('is_active');
		}

		// Check is post and is posted data valid
		if($this->_request->isPost() && $this->view->form->isValid($_POST)){
			if($this->admin_users_model->ifEmailExist($this->_getParam('email'), $this->view->existing->id)){
				My_Utilities::fmsg('Email is already registered.', 'error');
				return;
			}

			$values = $this->view->form->getValues();

			if(empty($values['image'])){
				unset($values['image']);	
			}else{
				if(file_exists(WEB_PATH.Zend_Registry::get('upload_profile_img_path').$this->view->existing->image))
					unlink(WEB_PATH.Zend_Registry::get('upload_profile_img_path').$this->view->existing->image);
			}

			if($this->admin_users_model->doSave($values, $this->view->existing->id) > 0){
				// If changed user is current, change session data

				$user = Zend_Auth::getInstance()->getIdentity();
				if($user->id == $this->_getParam('id')){
					$user->email	 = $this->_getParam('email');
					Zend_Auth::getInstance()->getStorage()->write($user);
				}
				My_Utilities::fmsg('Data is succesfully saved.');
				
			}else{
				My_Utilities::fmsg('Error. Data is not saved.', 'error');
			}
			$this->_redirect('admin/admin-users/index/page/'.$this->_getParam('page', 1));
		}
	}

	public function activateAction(){
		$this->view->existing = $this->admin_users_model->getById((int)$this->_getParam('id', 0));
		
		if($this->admin_users_model->doSave(array('is_active' => true), (int)$this->_getParam('id', 0))){
			My_Utilities::fmsg('Record is activated.');
		}else{
			My_Utilities::fmsg('Record is not succesfully activated.', 'error');
		}
		$this->_redirect('admin/admin-users/index/page/'.$this->_getParam('page', 1));
	}

	public function deactivateAction(){
		$this->view->existing = $this->admin_users_model->getById((int)$this->_getParam('id', 0));
		
		if($this->admin_users_model->doSave(array('is_active' => false), (int)$this->_getParam('id', 0))){
			My_Utilities::fmsg('Record is deactivated.');
		}else{
			My_Utilities::fmsg('Record is not succesfully deactivated.', 'error');
		}
		$this->_redirect('admin/admin-users/index/page/'.$this->_getParam('page', 1));
	}
	
	public function actionLogAction(){
		$this->view->form = new Admin_Form_LogAdminActionsFilter();
		$this->view->page = (int)$this->_getParam('page', 1);
		//get filter params
		$where = array(
			'users' => $this->_getParam('users', null),
			'roles' => $this->_getParam('roles', null),
			'actions' => $this->_getParam('actions', null),
			'affected_table' => $this->_getParam('affected_table', null),	
			'start_date' => $this->_getParam('start_date', date('d.m.Y')),
			'end_date' => $this->_getParam('end_date', date('d.m.Y', strtotime("+1 day")))
		);
		$this->view->form->setDefaults($where);	
		// Get sort from $_GET for pagination, default is id asc
		$sort = $this->_getParam('sort_by', 'date').' '.$this->_getParam('sort_type', 'desc');
		//format date to be suitable for database
		$where['start_date'] = date('Y-m-d', strtotime($where['start_date']));
		$where['end_date'] = date('Y-m-d', strtotime($where['end_date']));

		$admin_actions = $this->admin_actions_model->getLogDbActions($where, $sort);
		$this->view->paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($admin_actions));
		$this->view->paginator->setItemCountPerPage(200);
		$this->view->paginator->setCurrentPageNumber($this->view->page);
		//Header for html table
		$this->view->header = array(
				array('email', 'User email', 180),
				array('name', 'Usergroup', 80),
				array('date', 'Action time', 150),
				array('action', 'Action', 30),
				array('affected_table', 'Affected Table', 50),
				array('params', 'params', 0)
		);
	}
}