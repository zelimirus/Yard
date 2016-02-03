<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MediasFilter
 *
 * @author nikola
 */
class Cms_Form_MediasFilter extends My_Form_Search
{

    public function init()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $href = $request->getBaseUrl() . '/' . $request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName() . '/library_id/' . $request->get('library_id');

        $id = new Zend_Form_Element_Text('id');
        $this->addElement($id);

        $name = new Zend_Form_Element_Text('title');
        $this->addElement($name);

        $submit = new Zend_Form_Element_Button('Search');
        $submit->setAttrib('type', 'submit');
        $this->addElement($submit);

        $reset = new Zend_Form_Element_Button('Reset');
        $reset->setAttrib('type', 'reset')->setAttrib('href', $href);
        $this->addElement($reset);

        $this->setAction($href)
                ->setMethod('post');
    }
}
