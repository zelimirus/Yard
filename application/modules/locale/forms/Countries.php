<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_Form_Countries extends My_Form_Base
{

    public function init()
    {
        $model_t_countries    = new Locale_Model_Languages();

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name');
        $name->setRequired(true);
        $this->addElement($name);
        
        $country_code = new Zend_Form_Element_Text('country_code');
        $country_code->setLabel('Country code');
        $country_code->setDescription('List of codes you can see here: http://framework.zend.com/manual/1.12/en/zend.locale.appendix.html');
        $country_code->setRequired(true);
        $this->addElement($country_code);
        
        $calling_code = new Zend_Form_Element_Text('calling_code');
        $calling_code->setLabel('Calling code');
        $calling_code->setRequired(true);
        $calling_code->addValidator('Digits', true);
        $this->addElement($calling_code);
        
        $t_country_id = new Zend_Form_Element_Select('language_id');
        $t_country_id->addValidator(new Zend_Validate_Digits(), true);
        $t_country_id->setLabel('Language');
        $t_country_id->setAttrib("data-placeholder", "Choose language...");
        $t_country_id->setMultiOptions(array('0' => 'none') + $model_t_countries->getIdAndNameArray());
        $this->addElement($t_country_id);
        
        $is_active = new Zend_Form_Element_Checkbox('is_active');
        $is_active->setLabel('Active');
        $is_active->setRequired(true);
        $this->addElement($is_active);
        
        $cancel = new Zend_Form_Element_Button('cancel');
        $cancel->setLabel('Cancel');
        $cancel->setAttrib('class', 'btn btn-gold')->setAttrib('style', 'color:black');
        $cancel->setAttrib("onClick", "window.location = window.location.origin+'/locale/countries/'");
        $this->addElement($cancel);
        
        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Confirm');

        $this->setAction('')->setMethod('post')->addElement($submit);
    }
}
