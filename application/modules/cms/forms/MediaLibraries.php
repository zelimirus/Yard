<?php

class Cms_Form_MediaLibraries extends My_Form_Base {

    public function init() {

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name');
        $name->setRequired(true);
        $name->addFilter('StringTrim');
        $name->addValidator('Alnum', false, array('allowWhiteSpace' => true)); 
        $this->addElement($name);        

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Potvrdi');

        $this->setAction('')->setMethod('post')
                ->addElement($submit);
        
        $cancel = new Zend_Form_Element_Button('cancel');
        $cancel->setLabel('Cancel');
        $cancel->setAttrib('class', 'btn btn-gold')->setAttrib('style', 'color:black');
        $cancel->setAttrib("onClick", "window.location = window.location.origin+'/cms/media-libraries/'");
        $this->addElement($cancel);
    }

}