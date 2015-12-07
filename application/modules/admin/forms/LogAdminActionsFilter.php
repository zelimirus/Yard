<?php
/**
 * LogAdminActions  filter form
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * 
 * @version february 2014
 */
class Admin_Form_LogAdminActionsFilter extends My_Form_Base{

	public function init(){
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$href = $request->getBaseUrl().'/'. $request->getModuleName().'/'. $request->getControllerName().'/'. $request->getActionName();
		//get models
		$users_model = new Admin_Model_AdminUsers();
		$roles_model = new Admin_Model_Roles();

		$users = new Zend_Form_Element_Multiselect('users');
		$users->setLabel('Users:')
				->setAttrib("class", "select2")
				->setAttrib("data-placeholder", "Choose user...")
				->setAttrib("style", "width:200px")
				->addMultiOptions($users_model->getForDropdown());
		
		$roles = new Zend_Form_Element_Multiselect('roles');
		$roles->setLabel('Usergroup:')
				->setAttrib("class", "select2")
				->setAttrib("data-placeholder", "Choose usergroup...")
				->setAttrib("style", "width:200px")
				->addMultiOptions($roles_model->getForDropdown());

		$actions = new Zend_Form_Element_Multiselect('actions');
		$actions->setLabel('Actions:')
				->setAttrib("class", "select2")
				->setAttrib("data-placeholder", "Choose action...")
				->setAttrib("style", "width:200px");
		$actions->addMultiOption('INSERT', 'INSERT')
				->addMultiOption('UPDATE', 'UPDATE')
				->addMultiOption('DELETE', 'DELETE');

		$affected_table = new Zend_Form_Element_Text('affected_table');
		$affected_table->setLabel("Tabela")
					->setAttrib("class", "form-control")
					->setAttrib("style", "width:200px");
				
		$start_date = new Zend_Form_Element_Text('start_date');
		$start_date->setLabel("Date from")
				->setAttrib('class', 'datepicker');
				
		$end_date = new Zend_Form_Element_Text('end_date');
		$end_date->setLabel("Date to")
				->setAttrib('class', 'datepicker');

		$submit = new Zend_Form_Element_Submit('filter_submit');
		$submit->setAttrib('class', 'btn btn-primary');
		$submit->setLabel('Filter');

		$this->setAction($href)
				->setMethod('post')
				->addElement($users)
				->addElement($roles)
				->addElement($actions)
				->addElement($affected_table)
				->addElement($start_date)
				->addElement($end_date)
				->addElement($submit);
	}
}