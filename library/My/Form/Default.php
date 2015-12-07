<?php

/**
 * Setup global decoration for zend forms
 *
 * @author 
 * @version  April 2014
 */
class My_Form_Default extends Zend_Form {

    protected $element_decorators = array(
        'viewHelper',
        array(
            array('openerror' => 'HtmlTag'),
            array('tag' => 'div', 'openOnly' => true, 'placement' => Zend_Form_Decorator_Abstract::APPEND),
            array('Errors', array('class' => 'alert alert-error'),
                array(
                    array('closeerror' => 'HtmlTag'),
                    array('tag' => 'div', 'closeOnly' => true, 'placement' => Zend_Form_Decorator_Abstract::APPEND)
                ),
            ),
        ),
    );
    public $button_decorators = array(
        'ViewHelper',
        array('Description', array('tag' => '', 'separator' => ' ', 'escape' => false)),
        array(array('data' => 'HtmlTag'), array('tag' => 'div',
                'class' => 'element')),
        array(array('label' => 'HtmlTag'), array('tag' => 'div',
                'placement' => 'prepend')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div')),
    );
    public $file_decorators = array(
        'File',
        array(
            array('openerror' => 'HtmlTag'),
            array('tag' => 'div', 'openOnly' => true, 'placement' =>
                Zend_Form_Decorator_Abstract::APPEND)
        ),
        array('Errors', array('class' => 'alert alert-error'),),
        array(
            array('closeerror' => 'HtmlTag'),
            array('tag' => 'div', 'closeOnly' => true, 'placement' =>
                Zend_Form_Decorator_Abstract::APPEND)
        ),
        array(array('data' => 'HtmlTag'), array('tag' => 'div')),
        array('Label', array('tag' => 'th')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div'))
    );

    public function loadDefaultDecorators() {
        $this->setAttrib('enctype', 'multipart/form-data');

// Set the decorator for the form itself
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group')),
            'Form',
        ));

// Decorator for all elements
        $this->setElementDecorators($this->element_decorators);

// Set different decorator for submit btn
        foreach ($this->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Submit) {
                $element->setDecorators($this->button_decorators);
            }
// Set different decorator for File
            if ($element instanceof Zend_Form_Element_File) {
                $element->setDecorators($this->file_decorators);
            }
        }

        return $this;
    }

}