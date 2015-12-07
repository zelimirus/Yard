<?php
/**
 * Languages Model class for translate countries
 * 
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_Model_Languages extends My_Db_Table{
	protected $_name	= 'languages';
	protected $_primary	= 'id';

	/**
	 * Well known get all method
	 *
	 * @return Zend_Db_Table
	 */
	public function getAll(){
		$select = $this->select()->order('id asc');
		return $this->fetchAll($select);
	}

	public function getAllExcept($language_ids){
		$select = $this->select()->where('id NOT IN (?)', $language_ids);
		return $this->fetchAll($select);
	}

	/**
	 * Get one row from table by given id
	 *
	 * @param int $id - id of row
	 * @return Zend_Db_Table
	 */
	public function getById($id){
		$select = $this->select()->where('id = ?', (int)$id);
		return $this->fetchRow($select);
	}
	
	/**
	 * Get one row from table by given country_code - this is unique column
	 *
	 * @param string $code - Country Code
	 * @return Zend_Db_Table_Row
	 */
	public function getByCountryCode($code){
		$select = $this->select()->where('country_code = ?', $code);
		return $this->fetchRow($select);
	}
	
	/**
	 * Get one row from table by given country_code - this is unique column
	 *
	 * @param string $code - Country Code
	 * @return Zend_Db_Table_Row
	 */
	public function getByCountryName($name){
		$select = $this->select()->where('name = ?', $name);
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
		$select = $this->select();
		if(count($where) > 0){
			foreach($where as $on_where => $on_value){
				if($on_value){
					if(is_array($on_value)){
						$or_where = '';
						foreach($on_value as $count => $v){
							$or_where .= $this->_db->quoteInto('text('.$on_where.') ILIKE ?', '%'.$v.'%').' OR ';
						}
						$select->where(substr($or_where, 0, -3));
					}
					else{
						$select->where('text('.$on_where.') ILIKE ?', '%'.$on_value.'%');
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
	 * Well-known insert or update function
	 *
	 * @param array $data
	 * @param int id - if it is set than run update of given id, if it's not, than do insert
	 * @return int id of inserted od updated row
	 */
	public function doSave($data, $id = null){
		if($id){
			return $this->update($data, array('id = ?' => (int)$id));
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
		return $this->delete(array('id = ?' => (int)$id)) > 0;
	}

	/**
	 * Get last id from table
	 *
	 * @return int
	 */
	public function getLastId(){
		$select = $this->select()->from($this->_name, 'id')->order('id desc')->limit(1);
		return $this->fetchRow($select)->id;
	}
	
	/**
	 * Return array for populating selectbox form element
	 * 
	 * @return array(id => name, ...)
	 */
	public function getIdAndNameArray(){
		$select = $this->select()->from($this->_name, array("id", "name"));
		$result	= $this->fetchAll($select);
		$return	= array();
		foreach($result as $one_row){
			$return[$one_row->id] = $one_row->name;
		}
		return $return;
	}
	
	/**
	 * Return array for populating selectbox form element
	 * 
	 * @param only_active - if true return only active countries
	 * 
	 * @return array(id => name, ...)
	 */
	public function getCCAndNameArray($only_active = false){
		$select = $this->select()->from($this->_name, array("country_code", "name"));
		
		if($only_active){
			$select->where('is_active = true');
		}
		
		$result	= $this->fetchAll($select);
		$return	= array();
		foreach($result as $one_row){
			$return[$one_row->country_code] = $one_row->name;
		}
		return $return;
	}
	
	/**
	 * Return array of country codes
	 * 
	 * @return array(country_code, ...)
	 */
	public function getCountryCodes(){
		$select = $this->select()->from($this->_name, array("country_code"))->where('is_active = true');
		$result	= $this->fetchAll($select);
		$return	= array();
		foreach($result as $one_row){
			$return[] = $one_row->country_code;
		}
		return $return;
	}
}