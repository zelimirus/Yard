<?php
/**
 * Roles Model class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2013
 */
class Admin_Model_Roles extends My_Db_Table
{
    protected $_name     = 'roles';
    protected $_primary     = 'id';
    protected $_sequence = 'roles_id_seq';

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
        $select = $this->select()
        ->where('id = ?', $id);

        return $this->fetchRow($select);
    }

    /**
     * Get one row from table by given id but with other tabloes data
     *
     * @param int $id - id of row
     * @return Zend_Db_Table
     */
    public function getByIdExtended($id)
    {
        $select = $this->select()
        ->from(array('r' => 'roles'), array('r.id', 'r.name'))
        ->setIntegrityCheck(false)
        ->joinLeft(array('p' => 'permissions'), 'r.id = p.role_id', array('is_allowed', 'action'))
        ->joinLeft(array('re' => 'resources'), 'p.resource_id = re.id', array('name as r_name', 'description'))
        ->where('r.id = ?', $id);

        return $this->fetchAll($select);
    }

    /**
     * Get select object for pagination
     *
     * @param array $where conditions
     * @param string $order column name
     * @return Zend_Db_Select select object
     */
    public function getSelectForPagination($where = array(), $order = "id asc", $limit = null)
    {
        $select = $this->select();
        if (count($where) > 0) {
            foreach ($where as $one_where) {
                if (!isset($one_where[1])) {
                    $one_where[1] = null;
                }
                $select->where($one_where[0], $one_where[1]);
            }
        }
        if (!empty($order)) {
            $select->order($order);
        }
        if (!empty($limit)) {
            $select->limit($limit);
        }

        return $select;
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

    /**
     * Get an array to populate Zend_Form_Element_MultiSelect
     *
     * @return array(id=>name, ...)
     */
    public function getForDropdown()
    {
        $select = $this->select();
        $result = $this->fetchAll($select);
        $return = array();
        foreach ($result as $res) {
            $return[$res->id] = $res->name;
        }
        return $return;
    }
}
