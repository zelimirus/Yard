<?php
/**
 * Resources Model class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2013
 */
class Admin_Model_Resources extends Zend_Db_Table
{
    protected $_name     = 'resources';
    protected $_primary     = 'id';
    protected $_sequence = 'resources_id_seq';

    /**
     * Get one row from table by given resource name
     *
     * @param int $id - id of row
     * @return Zend_Db_Table
     */
    public function getByName($name)
    {
        $select = $this->select()->where('name = ?', $name);
        return $this->fetchRow($select);
    }

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
        $select = $this->select()->where('id = ?', $id);
        return $this->fetchRow($select);
    }

    /**
     * Well-known insert or update function
     * @param array $data
     * @param int id - if it is set than run update pf given id, if it's not than do insert
     * @return int id of inserted od updated row
     */
    public function doSave($data, $id = null)
    {
        if ($id) {
            return $this->update($data, array('id = ?' => $id));
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Well-known delete function
     *
     * @param int $id id to match where clausule
     * @return boolean if success or not
     */
    public function doDelete($id)
    {
        return $this->delete(array('id = ?' => $id)) > 0;
    }
}
