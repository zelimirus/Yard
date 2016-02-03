<?php
/**
 * Front controller plugin
 * dispatchLoopStartup method is trigered this before every call to controller
 *
 */
class My_Plugin_Front extends Zend_Controller_Plugin_Abstract
{
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        
        $request = $front->getRequest();

        $view->module_name   = $request->getModuleName();
        $view->controller_name = $request->getControllerName();
        $view->action_name   = $request->getActionName();

        $user_auth = Zend_Auth::getInstance();
        
        // ACL for admin module only, exept for web servis controllers
        if ($view->module_name != 'default') {
            $view->user = $user_auth->getIdentity();
            // Register ACL plugin
            $acl = new My_Acl_Acl();
            $front->registerPlugin(new My_Plugin_AclControl($user_auth, $acl));

            if ($view->user) {
                $admin_menu_item_model = new Admin_Model_MenuItems();
                $menu_items_icons_model = new Admin_Model_MenuItemsIcons();
                $view->menu_items = $admin_menu_item_model->getMenuItems($view->user->role_id);
                $view->menu_items_icons = $menu_items_icons_model->getAll();
            }
        }
    }
}
