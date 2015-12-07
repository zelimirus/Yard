<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_Form_UploadTranslate extends My_Form_Base{
	
	public function init(){
		$file = new Zend_Form_Element_File('file');
		$file->setRequired(true)
			->addValidator('Extension', false, 'csv')
			->addValidator('Size', false, 1048576 * 2) //2MB
			->addValidator('Count', false, 1);
		
		$cancel = new Zend_Form_Element_Button('cancel');
		$cancel->setLabel('Cancel');
		$cancel->setAttrib('class', 'btn btn-gold')->setAttrib('style', 'color:black');
		$cancel->setAttrib("onClick", "window.location = window.location.origin+'/locale/translate-messages/'");
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Upload CSV file');
		
		$this->setAttrib('enctype', 'multipart/form-data')
				->setMethod('post')
				->addElement($file)
				->addElement($cancel)
				->addElement($submit);
	}
}