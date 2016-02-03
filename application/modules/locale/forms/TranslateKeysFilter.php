<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_Form_TranslateKeysFilter extends My_Form_Search
{

    public function init()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $href     = $request->getBaseUrl().'/'.$request->getModuleName().'/'.$request->getControllerName().'/'.$request->getActionName();

        $id = new Zend_Form_Element_Text('id');
        $this->addElement($id);

        $key = new Zend_Form_Element_Text('key');
        $this->addElement($key);

        $description = new Zend_Form_Element_Text('description');
        $this->addElement($description);
        
        $submit = new Zend_Form_Element_Button('Search');
        $submit->setAttrib('type', 'submit');
        $this->addElement($submit);

        $reset = new Zend_Form_Element_Button('Reset');
        $reset->setAttrib('type', 'reset')->setAttrib('href', $href);
        $this->addElement($reset);

        $this->setAction($href)->setMethod('post');
    }
}
