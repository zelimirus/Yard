<?php

/**
 * junction table articles_categories model class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 *
 * @version  august 2013
 */
class Admin_Model_AdminUsersLanguages extends My_Db_Table {

    protected $_name = 'admin_users_languages';
    protected $_primary = 'id';


    /**
     * Well-known insert or update function
     *
     * @param array $data
     * @param int id - if it is set than run update of given id, if it's not, than do insert
     * @return int id of inserted od updated row
     */
    public function doSave($data, $id = null) {
        if ($id) {
            return $this->update($data, array('id = ?' => (int) $id));
        } else {
            return $this->insert($data);
        }
    }

    public function getIdsByUserIdArray($user_id) {
        $select = $this->select()->where('admin_user_id = ?', (int) $user_id);
        $result = $this->fetchAll($select);
        $return = array();
        foreach ($result as $one_row) {
            $return[] = $one_row->language_id;
        }
        return $return;
    }

    /**
     * Insert multi values to table
     * 
     * @param array $cat_ids, categories to insert into table
     * @param int $article_id, id of article for all categories
     */
    public function doSaveArray($language_ids, $user_id) {
        foreach ($language_ids as $id) {
            $this->doSave(array('language_id' => $id, 'admin_user_id' => $user_id));
        }
    }

    /**
     * Well-known delete function
     *
     * @param int $article_id 
     * @return boolean if success or not
     */
    public function doDeleteByUserId($user_id) {
        return $this->delete(array('admin_user_id = ?' => (int) $user_id)) > 0;
    }
}