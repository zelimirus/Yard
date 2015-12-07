<?php
/**
 * UsersSearch Form class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2013
 */
class Admin_Form_AdminUsersSearch extends My_Form_Search{

	public function init(){
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$href	 = $request->getBaseUrl().'/'.$request->getModuleName().
				'/'.$request->getControllerName().'/'.$request->getActionName();

		$id			 = new Zend_Form_Element_Text('id');
		$email		 = new Zend_Form_Element_Text('email');
		$submit		 = new Zend_Form_Element_Button('Search');
		$reset		 = new Zend_Form_Element_Button('Reset');
		$submit->setAttrib('type', 'submit');
		$reset->setAttrib('type', 'reset');
		$reset->setAttrib('href', $href);

		$this->setAction($href)
			->setMethod('post')
			->addElement($id)
			->addElement($email)
			->addElement($submit)
			->addElement($reset);
	}

}