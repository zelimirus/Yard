<?php
/**
 * Usergroups Controller class in Admin module
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2013
 */
class Admin_AdminUsersgroupsController extends Zend_Controller_Action
{

    private $roles_model;
    private $permissions_model;
    private $admin_users_model;
    private $roles_admin_menu_items;
        
    public function init()
    {
        $this->roles_model                = new Admin_Model_Roles();
        $this->permissions_model        = new Admin_Model_Permissions();
        $this->admin_users_model        = new Admin_Model_AdminUsers();
        $this->roles_admin_menu_items    = new Admin_Model_RolesAdminMenuItems();
    }
    
    public function indexAction()
    {
        $this->view->page = (int)$this->_getParam('page', 1);
        // Get sort from $_GET for pagination, default is id asc
        $sort = $this->_getParam('sort_by', 'id').' '.$this->_getParam('sort_type', 'asc');

        //Get select for pagination, create and configure pagination object
        $pagination_select         = $this->roles_model->getSelectForPagination(null, $sort);
        $this->view->paginator     = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($pagination_select));
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($this->view->page);

        //Header for html table
        $this->view->header = array(
            array('id', 'Id', 50),
            array('name', 'User group', 0),
            array('', '', 200)
        );
    }

    public function newAction()
    {
        $this->view->form = new Admin_Form_AdminUsersgroups();
        //required_resources:default:error,default:index,auth:auth
        $required_resources = array(1, 2, 3);
        //set check and disabled values for required_resources:default:error,default:index,auth:auth
        $this->view->form->permit->setValue($required_resources);
        $this->view->form->permit->setAttrib('disable', $required_resources);
        
        // Check is posted data valid, if is it than insert user
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $values = $this->view->form->getValues();
            $menu_items_ids = $values['admin_menu_item_id'];
            unset($values['admin_menu_item_id']);
            
            $result = $this->roles_model->doSave(array('name' => $this->view->form->getValue('name')));

            if ($result > 0) {
                $resources = $required_resources;
                
                if ($this->view->form->getValue('permit')) {
                    $resources = array_merge($this->view->form->getValue('permit'), $required_resources);
                }
                foreach ($resources as $resource) {
                    $this->permissions_model->doSave(array('role_id' => $result, 'resource_id' => $resource, 'is_allowed' => true));
                }
                
                $this->roles_admin_menu_items->doSave($menu_items_ids, $result);
                
                My_Utilities::fmsg('Data is succesfully saved.');
            } else {
                My_Utilities::fmsg('Error. Data is not saved.', 'error');
            }
            $this->_redirect('admin/admin-usersgroups');
        }
    }

    public function editAction()
    {
        // Try to find record by user id
        $this->view->existing     = $this->roles_model->getById((int)$this->_request->getParam('id', 0));

        // If row does not exist, redirect to usersgroups
        if (!isset($this->view->existing->id) || empty($this->view->existing->id)) {
            My_Utilities::fmsg('Record not found.', 'warning');
            $this->_redirect('admin/admin-usersgroups');
        }

        // Create an instance of form and set defaults
        $this->view->form     = new Admin_Form_AdminUsersgroups();
        $this->view->form->setDefaults($this->view->existing->toArray());
        $permit                 = array('permit' => $this->permissions_model->getArrayResourcesByRoleNonSpecial($this->view->existing->id));
        $this->view->form->setDefaults($permit);
        
        $this->view->form->admin_menu_item_id->setValue($this->roles_admin_menu_items->getForDropDown($this->view->existing->id));
        // Check is post and is posted data valid
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $values = $this->view->form->getValues();
            $menu_items_ids = $values['admin_menu_item_id'];
            unset($values['admin_menu_item_id']);
            
            $this->roles_model->doSave(array('name' => $this->view->form->getValue('name')), $this->view->existing->id);
                
            $this->roles_admin_menu_items->doDeleteByRoleId($this->view->existing->id);
            $this->roles_admin_menu_items->doSave($menu_items_ids, $this->view->existing->id);
            
            $this->permissions_model->doDeleteNonSpecialByRole($this->view->existing->id);
            foreach ($this->view->form->getValue('permit') as $permit) {
                $data = array('role_id'         => $this->view->existing->id, 'resource_id'     => $permit, 'is_allowed'     => true);
                $this->permissions_model->doSave($data);
            }

            My_Utilities::fmsg('Data is succesfully saved.');

            $this->_redirect('admin/admin-usersgroups/index/page/'.$this->_getParam('page', 1));
        }
    }

    public function deleteAction()
    {
        $id    = (int)$this->_getParam('id', 0);

        if ($this->roles_model->doDelete($id)) {
            $this->permissions_model->doDeleteByRoleId($id);
            $this->roles_admin_menu_items->doDeleteByRoleId($id);
            $this->admin_users_model->doDeleteByRoleId($id);
            My_Utilities::fmsg('Record is succesfully deleted.');
        } else {
            My_Utilities::fmsg('Record is not deleted.', 'error');
        }
        $this->_redirect('admin/admin-usersgroups/index/page/'.$this->_getParam('page', 1));
    }

    public function privilegesAction()
    {
        // Try to find record by user id
        $this->view->existing     = $this->roles_model->getById((int)$this->_request->getParam('id', 0));
        // If row does not exist, redirect to usersgroups
        if (!isset($this->view->existing->id) || empty($this->view->existing->id)) {
            My_Utilities::fmsg('Record not found.', 'warning');
            $this->_redirect('admin/admin-usersgroups');
        }

        $this->view->form = new Admin_Form_Privileges(array('roleid' => $this->view->existing->id));

        // Check is post and is posted data valid
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            // First delete than insert
            $this->permissions_model->doDeleteSpecialPermisionsByRole($this->view->existing->id);
            foreach ($this->view->form->getValues() as $key => $rec) {
                if (strstr($key, '_')) {
                    $exp = explode('_', $key);
                    foreach (explode(';', $rec) as $action) {
                        if (!$action) {
                            continue;
                        }
                        $data = array(
                            'role_id'         => $this->view->existing->id,
                            'resource_id'     => $exp[0],
                            'action'         => trim($action),
                            'is_allowed'     => $exp[1] == 'allow' ? 't' : 'f'
                            );
                        $this->permissions_model->doSave($data);
                    }
                }
            }
            My_Utilities::fmsg('Changes are saved.');
            $this->_redirect('admin/admin-usersgroups/index/page/'.$this->_getParam('page', 1));
        }
    }

    /**
     * Called by Ajax request
     *
     * @return JSON
     */
    public function showAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $roles         = $this->roles_model->getByIdExtended((int)$this->_getParam('id'))->toArray();
        $this->_response->setBody(json_encode($roles));
    }
}
