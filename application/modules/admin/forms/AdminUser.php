<?php
/**
 * AdminUsers Form class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  october 2014
 */
class Admin_Form_AdminUser extends My_Form_Base{

	public function init(){
		$user = Zend_Auth::getInstance()->getIdentity();

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email')->setRequired(true)->addValidator('EmailAddress')
				->setAttrib("class", "form-control")->setAttrib("style", "width:200px")->addErrorMessage('Email address not valid');

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password')->setAttrib("class", "form-control")->setAttrib("style", "width:200px")->setAttrib('autocomplete', 'off')->setRequired(true);

		$name = new Zend_Form_Element_Text('first_name');
		$name->setLabel('First name')->setAttrib("class", "form-control")->setAttrib("style", "width:200px")->setRequired(true);

		$lastname = new Zend_Form_Element_Text('last_name');
		$lastname->setLabel('Last name')->setAttrib("class", "form-control")->setAttrib("style", "width:200px")->setRequired(true);

		$role_name	 = new Zend_Form_Element_Select('role_id');
		$role_name->setLabel('Account type')->setRequired(true)->setRegisterInArrayValidator(false);
		$role_name->setAttrib("class", "select2")->setAttrib("style", "width:200px");
		$roles_table = new Admin_Model_Roles();
		foreach($roles_table->getAll() as $v){
			$role_name->addMultiOption($v->id, $v->name);
		}			

		$img = new Zend_Form_Element_File('image');
		$img->setDestination(WEB_PATH.Zend_Registry::get('upload_profile_img_path'));
		$img->addValidator('Count', false, 1);
		$img->addValidator('Extension', false, 'jpeg,jpg,png,gif');
		if($img->getFileName()){
			$new_name = md5(rand(1000, 10000).time().$img->getFileName()).".".preg_replace('/^.*\.([^.]+)$/D', '$1', $img->getFileName());
			$img->addFilter('Rename', $new_name);
		}
		$img->setLabel('Image');

		$is_active = new Zend_Form_Element_Checkbox("is_active");
		$is_active->setLabel('Active')->setAttrib("class", "icheckbox_square");
	
		$cancel = new Zend_Form_Element_Button('cancel');
		$cancel->setLabel('Cancel');
		$cancel->setAttrib('class', 'btn btn-gold')->setAttrib('style', 'color:black');
		$cancel->setAttrib("onClick", "window.location = window.location.origin+'/admin/admin-users/'");

		$submit = new Zend_Form_Element_Submit('save');
		$submit->setAttrib('class', 'btn btn-primary');
		$submit->setLabel('Confirm');

		$this->setAction('')
			->setMethod('post')
			->addElement($email)
			->addElement($password)
			->addElement($name)
			->addElement($lastname)
			->addElement($role_name)
			->addElement($img)
			->addElement($is_active)
			->addElement($cancel)
			->addElement($submit);
	}
}