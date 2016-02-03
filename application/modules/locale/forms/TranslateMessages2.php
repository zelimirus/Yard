<?php
/**
 * Form where we have selected country and key
 * 
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_Form_TranslateMessages2 extends My_Form_Base
{
    
    public function init()
    {
        $key_id = new Zend_Form_Element_Note('key_id');
        $key_id->setLabel('Key');
        $this->addElement($key_id);
        
        $country_id = new Zend_Form_Element_Note('language_id');
        $country_id->setLabel('Language');
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

class Zend_Form_Element_Note extends Zend_Form_Element_Xhtml
{
    public $helper = 'formNote';
}
