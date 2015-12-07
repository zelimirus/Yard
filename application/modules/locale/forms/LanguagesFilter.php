<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_Form_LanguagesFilter extends My_Form_Search{

	public function init(){
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$href	 = $request->getBaseUrl().'/'.$request->getModuleName().'/'.$request->getControllerName().'/'.$request->getActionName();

		$id = new Zend_Form_Element_Text('id');
		$this->addElement($id);

		$country_code = new Zend_Form_Element_Text('country_code');
		$this->addElement($country_code);

		$name = new Zend_Form_Element_Text('name');
		$this->addElement($name);

		$submit = new Zend_Form_Element_Button('Search');
		$submit->setAttrib('type', 'submit');
		$this->addElement($submit);

		$reset = new Zend_Form_Element_Button('Reset');
		$reset->setAttrib('type', 'reset')->setAttrib('href', $href);
		$this->addElement($reset);

		$this->setAction($href)->setMethod('post');
	}
}