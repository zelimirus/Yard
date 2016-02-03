<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version may 2014
 */
class Admin_Form_MenuItems extends My_Form_Base
{

    public function init()
    {
        $menu_items_model = new Admin_Model_MenuItems();
        $menu_items_icons_model = new Admin_Model_MenuItemsIcons();

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name');
        $name->setAttrib("class", "form-control");
        $name->setAttrib("style", "width:200px");
        $name->setRequired(true);
        $this->addElement($name);
        
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title');
        $title->setAttrib("class", "form-control");
        $title->setAttrib("style", "width:200px");
        $title->setRequired(true);
        $title->addValidator('Alnum', false, array('allowWhiteSpace' => true));
        $this->addElement($title);
        
        $parent = new Zend_Form_Element_Select('parent_id');
        $parent->setLabel('Parent menu item')->setRequired(false);
        $parent->setAttrib("class", "select2")->setAttrib("style", "width:200px");
        $parent->setDescription('If menu item isn\'t a child element, choose None');
        $parent->addMultiOption(0, 'None');
        foreach ($menu_items_model->getForDropDown() as $key => $v) {
            $parent->addMultiOption($key, $v);
        }
        $this->addElement($parent);
        
        $module = new Zend_Form_Element_Text('module');
        $module->setLabel('Module');
        $module->setAttrib("class", "form-control");
        $module->setAttrib("style", "width:200px");
        $module->setRequired(false);
        $module->addValidator('Alnum', false, array('allowWhiteSpace' => true));
        $this->addElement($module);
        
        $controller = new Zend_Form_Element_Text('controller');
        $controller->setLabel('Controller');
        $controller->setAttrib("class", "form-control");
        $controller->setAttrib("style", "width:200px");
        $controller->setRequired(false);
        $this->addElement($controller);
        
        $action = new Zend_Form_Element_Text('action');
        $action->setLabel('Action');
        $action->setAttrib("class", "form-control");
        $action->setAttrib("style", "width:200px");
        $action->setRequired(false);
        $this->addElement($action);
        
        $params = new Zend_Form_Element_Text('params');
        $params->setLabel('Params');
        $params->setDescription('Insert params in format: key1:value1,key2:value2 ...');
        $params->setAttrib("class", "form-control");
        $params->setAttrib("style", "width:200px");
        $params->setRequired(false);
        $this->addElement($params);

        $icon_id = new Zend_Form_Element_Hidden('icon_id');
        $icon_id->setRequired(false);
        $this->addElement($icon_id);

        $cancel = new Zend_Form_Element_Button('cancel');
        $cancel->setLabel('Cancel');
        $cancel->setAttrib('class', 'btn btn-gold')->setAttrib('style', 'color:black');
        $cancel->setAttrib("onClick", "window.location = window.location.origin+'/admin/menu-items/'");
        $this->addElement($cancel);
        
        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Confirm');

        $this->setAction('')->setMethod('post')->addElement($submit);
    }
}
