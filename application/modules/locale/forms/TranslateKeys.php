<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_Form_TranslateKeys extends My_Form_Base
{

    public function init()
    {
        $key = new Zend_Form_Element_Text('key');
        $key->setLabel('Key');
        $key->setRequired(true);
        $key->setDescription('Key must contain small letters. Blank space replace with _');
        $this->addElement($key);
        
        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description');
        $description->setRequired(true);
        $this->addElement($description);
        
        $cancel = new Zend_Form_Element_Button('cancel');
        $cancel->setLabel('Cancel');
        $cancel->setAttrib('class', 'btn btn-gold')->setAttrib('style', 'color:black');
        $cancel->setAttrib("onClick", "window.location = window.location.origin+'/locale/translate-keys/'");
        $this->addElement($cancel);
    
        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Confirm');
        
        $this->setAction('')->setMethod('post')->addElement($submit);
    }
}
