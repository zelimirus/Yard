<?php
/* RolesAdminMenuItems Model class
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
* @version  may 2014
*/
class Admin_Model_RolesAdminMenuItems extends My_Db_Table
{
    protected $_name     = 'roles_admin_menu_items';
    protected $_primary     = 'id';

    public function getById($id)
    {
        $select = $this->select()->where('id = ?', (int)$id);
        return $this->fetchRow($select);
    }
    
    public function getForDropDown($id)
    {
        $select = $this->select()->where('role_id =?', $id);
        $result = $this->fetchAll($select);
        $return = array();
        foreach ($result as $one_row) {
            $return[$one_row->id] = $one_row->admin_menu_item_id;
        }
        return $return;
    }
    
    public function doSave($data, $id = null)
    {
        foreach ($data as $row) {
            $this->insert(array('role_id'=>$id, 'admin_menu_item_id'=>$row), array('id = ?' => (int)$id));
        }
        return true;
    }
    
    public function doDeleteByRoleId($id)
    {
        return $this->delete(array('role_id = ?' => (int)$id)) > 0;
    }
}
