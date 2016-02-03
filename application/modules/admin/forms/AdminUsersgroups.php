<?php
/**
 * Usersgroup Form class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2013
 */
class Admin_Form_AdminUsersgroups extends My_Form_Base
{
    

    public function init()
    {
        $menu_items_model = new Admin_Model_MenuItems();
        
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Usergroup name')->setRequired(true)
        ->setAttrib("class", "form-control")->setAttrib("style", "width:200px");

        $menu_items = new Zend_Form_Element_Multiselect('admin_menu_item_id');
        $menu_items->addValidator(new Zend_Validate_Digits(), true);
        $menu_items->setLabel('Menu Items: ');
        $menu_items->setAttrib("class", "select2");
        $menu_items->setAttrib("data-placeholder", "Choose...");
        $menu_items->setAttrib("style", "width:200px");
        $menu_items->addMultiOptions($menu_items_model->getForDropDown());
        
        $permit = new Zend_Form_Element_MultiCheckbox('permit');
        $permit->setLabel('Available resources ');
        $resources_table = new Admin_Model_Resources();
        foreach ($resources_table->getAll() as $resource) {
            $permit->addMultiOption((string)$resource->id, ' '.$resource->name);
        }
        
        $cancel = new Zend_Form_Element_Button('cancel');
        $cancel->setLabel('Cancel');
        $cancel->setAttrib('class', 'btn btn-gold')->setAttrib('style', 'color:black');
        $cancel->setAttrib("onClick", "window.location = window.location.origin+'/admin/admin-usersgroups/'");

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Confirm');

        $this->setAction('')
            ->setMethod('post')
            ->addElement($name)
            ->addElement($menu_items)
            ->addElement($permit)
            ->addElement($cancel)
            ->addElement($submit);
    }
}
