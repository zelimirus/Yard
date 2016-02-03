<?php

class Cms_Form_Medias extends My_Form_Base
{

    private $type = 'medias';

    public function init()
    {
        $media_library_model = new Cms_Model_MediaLibraries();
        $media_library = $media_library_model->getByPath($this->_attribs['media_library_path']);

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title');
        $title->setRequired(true);
        $title->addFilter('StringTrim');
        $title->addValidator('Alnum', false, array('allowWhiteSpace' => true));
        $this->addElement($title);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description');
        $description->addFilter('StringTrim');
        $description->addValidator('Alnum', false, array('allowWhiteSpace' => true));
        $this->addElement($description);

        $original = new Zend_Form_Element_File('original');
        $original->addValidator('Count', false, 1);
        $original->addValidator('Extension', false, 'jpeg,jpg,png');
        $original->addFilter('Rename', $this->_attribs['file_name']);
        $original->setDestination(My_Utilities::getUploadMediaPathDiffSizes($this->_attribs['file_name'], $this->_attribs['media_library_path'], 'original'));
        $original->setLabel('Image:');
        
        $this->addElement($original);

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Potvrdi');

        $this->setAction('')->setMethod('post')
                ->addElement($submit);
        
        $cancel = new Zend_Form_Element_Button('cancel');
        $cancel->setLabel('Cancel');
        $cancel->setAttrib('class', 'btn btn-gold')->setAttrib('style', 'color:black');
        $cancel->setAttrib("onClick", "window.location = window.location.origin+'/cms/medias/index/library_id/".$media_library->id."'");
        $this->addElement($cancel);
    }
}
