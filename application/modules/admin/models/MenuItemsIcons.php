<?php
/* MenuItems Model class
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic <milan.marjanovic@golive.rs>
* @version  february 2015
*/
class Admin_Model_MenuItemsIcons extends My_Db_Table
{
    protected $_name     = 'menu_items_icons';
    protected $_primary     = 'id';

    public function getById($id)
    {
        $select = $this->select()->where('id = ?', (int)$id);
        return $this->fetchRow($select);
    }

    public function getAll()
    {
        $select = $this->select();
        return $this->fetchAll($select);
    }
    
    public function getForDropDown()
    {
        $select = $this->select();
        $result = $this->fetchAll($select);
        $return = array();
        foreach ($result as $one_row) {
            $return[$one_row->id] = $one_row->icon;
        }
        return $return;
    }
}
