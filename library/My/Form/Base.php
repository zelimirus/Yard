<?php
/**
 * Setup global decoration for zend forms, form is in html table for new/edit
 *
 * @author tasmaniski <tasmaniski@gmail.com>
 * @version  December 2012
 */
class My_Form_Base extends Zend_Form{
	protected $description_decorators = array(
        'viewHelper',
	array('Description', array('tag' => '', 'separator' => ' ', 'escape' => false)),
	array(array('divWrapper' => 'HtmlTag'), array('tag' => 'div',
                'class' => 'input-append')),
	array('Label', array('tag' => 'p', 'escape' => false)),
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
	array(array('data' => 'HtmlTag'), array('tag' => 'td')),
	array('label', array('tag' => 'td')),
	array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);

	protected $element_decorators = array(
        'viewHelper',
        'Description',
	array('Description', array('class' => 'description')),
	//array('Description', array('tag' => '', 'separator' => ' ', 'escape' => false)),
	array('Label', array('tag' => 'p', 'escape' => false)),
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
	array(array('data' => 'HtmlTag'), array('tag' => 'td')),
	array('label', array('tag' => 'td')),
	array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);

	public $button_decorators = array(
        'ViewHelper',
	array('Description', array('tag' => '', 'separator' => ' ', 'escape' => false)),
	array(array('data' => 'HtmlTag'), array('tag' => 'td',
                'class' => 'element')),
	array(array('label' => 'HtmlTag'), array('tag' => 'submit',
                'placement' => 'prepend')),
	array(array('row' => 'Errors'), array('tag' => 'tr')),
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
	array(array('data' => 'HtmlTag'), array('tag' => 'td')),
	array('Label', array('tag' => 'th')),
	array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
	);

	public function loadDefaultDecorators(){
		$this->setAttrib('enctype', 'multipart/form-data');

		// Set the decorator for the form itself
		$this->setDecorators(array(
            'FormElements',
		array('HtmlTag', array('tag' => 'table', 'class' => 'form')),
            'Form',
		));

		// Decorator for all elements
		$this->setElementDecorators($this->element_decorators);

		// Set different decorator for submit btn
		foreach($this->getElements() as $element){
			if($element instanceof Zend_Form_Element_Submit){
				$element->setDecorators($this->button_decorators);
			}
			// Set different decorator for File
			if($element instanceof Zend_Form_Element_File){
				$element->setDecorators($this->file_decorators);
			}
		}

		return $this;
	}
}