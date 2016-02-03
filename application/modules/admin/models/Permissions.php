<?php
/**
 * Permissions Model class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2013
 */
class Admin_Model_Permissions extends My_Db_Table
{
    protected $_name     = 'permissions';
    protected $_primary     = 'id';
    protected $_sequence = 'permissions_id_seq';

    /**
     * Well known get all method
     *
     * @return Zend_Db_Table
     */
    public function getAll()
    {
        $select = $this->select();
        return $this->fetchAll($select);
    }

    /**
     * Get one row from table by given id
     *
     * @param int $id - id of row
     * @return Zend_Db_Table
     */
    public function getById($id)
    {
        $select = $this->select()->where('id = ?', (int)$id);
        return $this->fetchRow($select);
    }

    /**
     * Get by role_id and resources_id nad where action != ''
     *
     * @param int $role_id
     * @param int $resource_id
     * @return Zend_Db_Table
     */
    public function getByRoleAndResourceSpecial($role_id, $resource_id)
    {
        $select = $this->select()
                        ->where('role_id = ?', (int)$role_id)
                        ->where('resource_id = ?', (int)$resource_id)
                        ->where('action IS NOT NULL');
        return $this->fetchAll($select);
    }

    /**
     * Well-known insert or update function
     * @param array $data
     * @param int id - if it is set than run update pf given id, if it's not than do insert
     * @return int id of inserted row or boolean of updated row
     */
    public function doSave($data, $id = null)
    {
        if ($id) {
            return $this->update($data, array('id = ?' => (int)$id));
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Return all resources for providen role_id in format: array(id1, id2, ...);
     * But return only which haven't action (action == '')
     *
     * @param int $role_id role id for match where clausule
     * @return array
     */
    public function getArrayResourcesByRoleNonSpecial($role_id)
    {
        $select = $this->select()
                        ->from($this->_name, 'resource_id')
                        ->where('role_id = ?', (int)$role_id)
                        ->where('action IS NULL');
        $result         = $this->fetchAll($select);
        $resources     = array();
        foreach ($result as $r) {
            $resources[] = $r->resource_id;
        }
        return $resources;
    }

    /**
     * Well-known delete function
     *
     * @param int $id id to match where clausule
     * @return boolean if success or not
     */
    public function doDelete($id)
    {
        return $this->delete(array('id = ?' => (int)$id)) > 0;
    }

    /**
     * Delete records by providen role_id
     *
     * @param int $id id to match where clausule
     * @return boolean if success or not
     */
    public function doDeleteByRoleId($role_id)
    {
        return $this->delete(array('role_id = ?' => (int)$role_id)) > 0;
    }

    /**
     * Delete only permissions which dont have special perrmisions (action == '')
     *
     * @param int $role_id
     * @return boolean
     */
    public function doDeleteNonSpecialByRole($role_id)
    {
        return $this->delete(array('role_id = ?' => (int)$role_id, "action IS NULL")) > 0;
    }

    /**
     * Delete only permissions which have special perrmisions (action != '')
     *
     * @param int $role_id
     * @return boolean
     */
    public function doDeleteSpecialPermisionsByRole($role_id)
    {
        return $this->delete(array("action IS NOT NULL", 'role_id = ?' => (int)$role_id)) > 0;
    }
}
