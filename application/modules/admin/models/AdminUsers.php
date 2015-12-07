<?php
/**
 * AdminUsers Model class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2013
 */
class Admin_Model_AdminUsers extends My_Db_Table{
	protected $_name	 = 'admin_users';
	protected $_primary	 = 'id';
	protected $_sequence = 'admin_users_id_seq';

	/**
	 * Well known get all method
	 *
	 * @return Zend_Db_Table
	 */
	public function getAll(){
		$select = $this->select();
		return $this->fetchAll($select);
	}

	/**
	 * Get one row from table by given id
	 *
	 * @param int $id - id of row
	 * @return Zend_Db_Table
	 */
	public function getById($id){
		$select = $this->select()->from($this->_name);
		$select->setIntegrityCheck(FALSE)
		->joinLeft('roles', 'roles.id = admin_users.role_id', 'name as role_name')
		->where("admin_users.id = $id");
		return $this->fetchRow($select);
	}

	/**
	 * Get select object for pagination
	 *
	 * @param array $where conditions
	 * @param string $order column name
	 * @return Zend_Db_Select select object
	 */
	public function getSelectForPagination($where = array(), $order = 'id asc', $limit = null){
		$select = $this->select()->from($this->_name);
		$select->setIntegrityCheck(FALSE)
		->joinLeft('roles', 'roles.id = admin_users.role_id', 'name as role_name');

		if(count($where) > 0){
			foreach($where as $on_where => $on_value){
				if($on_value){
					if(is_array($on_value)){
						$or_where = '';
						foreach($on_value as $count => $v){
							$or_where .= $this->_db->quoteInto('text(admin_users.'.$on_where.') ILIKE ?', '%'.$v.'%').' OR ';
						}
						$select->where(substr($or_where, 0, -3)); //remove 'OR '
					}
					else{
						$select->where('text(admin_users.'.$on_where.') ILIKE ?', '%'.$on_value.'%');
					}
				}
			}
		}

		if(!empty($order)){
			$select->order($order);
		}
		if(!empty($limit)){
			$select->limit($limit);
		}

		return $select;
	}

	/**
	 * Well-known insert or update function, set md5() on password ig exist
	 *
	 * @param array $data
	 * @param int id - if it is set than run update pf given id, if it's not than do insert
	 * @return int id of inserted od updated row
	 */
	public function doSave($data, $id = null){
		if(isset($data['password'])){
			$data['password'] = md5($data['password']);
		}
		if($id){
			return $this->update($data, array('id = ?' => $id));
		}
		else{
			return $this->insert($data);
		}
	}

	/**
	 * Well-known delete function
	 *
	 * @param int $id id to match where clausule
	 * @return boolean if success or not
	 */
	public function doDelete($id){
		return $this->delete(array('id = ?' => $id)) > 0;
	}

	/**
	 * Check if email already exist in db, for new and edit actions
	 *
	 * @param string $email email to check
	 * @param int $user_id if user wants to edit info and submit same email
	 * @return Zend_Db_Table_Row
	 */
	public function ifEmailExist($email, $user_id = null){
		$select = $this->select()->where("email = ?", $email);
		if($user_id){
			$select->where("id != ?", (int)$user_id);
		}
		return $this->fetchRow($select);
	}
	
	/**
	 * Delete records by providen role_id
	 *
	 * @param int $id id to match where clausule
	 * @return boolean if success or not
	 */
	public function doDeleteByRoleId($role_id){
		return $this->delete(array('role_id = ?' => (int)$role_id)) > 0;
	}
	
	/**
	 * get all id-name pairs to populate dropdown element on form
	 *
	 */
	public function getForDropdown(){
		$select = $this->select()->from($this->_name, array('id', 'email'));
		$result = $this->fetchAll($select);
		$return = array();
		foreach($result as $one_row){
			$return[$one_row->id] = $one_row->email;
		}
		return $return;
	}
	
	public function getUsersByRoleId($role_id){
		$select = $this->select()->from($this->_name, array('id', 'email'))
		->where('role_id =?', $role_id);
		
		return  $this->fetchAll($select);
	}
	
	public function getUsersByServiceId($service_id){
		$select = $this->select()->from($this->_name)
					->joinLeft('services_users as su', 'su.user_id = admin_users.id', '')
					->where('su.service_id =?', (int)$service_id);
		
		return  $this->fetchAll($select);
	}
}