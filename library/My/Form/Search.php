<?php
/**
 * Setup global decoration for zend forms, form is in one line for search
 *
 * @author tasmaniski <tasmaniski@gmail.com>
 * @version  December 2012
 */
class My_Form_Search extends Zend_Form
{
    protected $_element_decorators = array(
    array('viewHelper', array('tag' => 'span')),
    array('HtmlTag', array('tag' => 'span')),
    );

    public function loadDefaultDecorators()
    {
        // Set decorator for form itself
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setDecorators(array('FormElements', 'Form'));
        $this->setElementDecorators(array('ViewHelper', 'Errors'));
        $this->setAttrib('class', 'form-inline pull-left input-append');

        // Set path for the custom decorators
        $this->addElementPrefixPath('My_Decorator', 'My/Decorator/', 'decorator');

        // Any elements added to the form will use these decorators
        $this->setElementDecorators($this->_element_decorators);
        return $this;
    }
}
