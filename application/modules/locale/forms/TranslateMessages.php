<?php
/**
 * Form to add new translate
 * 
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_Form_TranslateMessages extends My_Form_Base{

	public function init(){
		$model_countries = new Locale_Model_Languages();
		$model_t_keys	 = new Locale_Model_TranslateKeys();

		$key_id = new Zend_Form_Element_Select('key_id');
		$key_id->addValidator(new Zend_Validate_Digits(), true);
		$key_id->setLabel('Key');
		$key_id->setRequired(true);
		$key_id->setMultiOptions($model_t_keys->getIdAndKeyArray(TRUE));
		$this->addElement($key_id);

		$country_id = new Zend_Form_Element_Select('language_id');
		$country_id->addValidator(new Zend_Validate_Digits(), true);
		$country_id->setLabel('Language');
		$country_id->setRequired(true);
		$country_id->setMultiOptions($model_countries->getIdAndNameArray());
		$this->addElement($country_id);
		
		$value = new My_Form_Element_CKEditor('value');
		$value->setLabel('Translation');
		$value->setDescription('Tags for dynamic values: {1}, {2}, {3}...Double quotes (")are not allowed');
		$this->addElement($value);
		
		$cancel = new Zend_Form_Element_Button('cancel');
		$cancel->setLabel('Cancel');
		$cancel->setAttrib('class', 'btn btn-gold')->setAttrib('style', 'color:black');
		$cancel->setAttrib("onClick", "window.location = window.location.origin+'/locale/translate-messages/'");
		$this->addElement($cancel);

		$submit = new Zend_Form_Element_Submit('save');
		$submit->setAttrib('class', 'btn btn-primary');
		$submit->setLabel('Confirm');

		$this->setAction('')->setMethod('post')->addElement($submit);
	}
}