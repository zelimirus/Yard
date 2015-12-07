
<?php
/**
 * TranslateMessages Model class for translate messages
 * 
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_Model_TranslateMessages extends My_Db_Table {
    protected $_name	= 'translate_messages';
    protected $_primary = 'id';

    private $language_model;
    private $t_keys_model;

    public function init(){
    	$this->language_model 	= new Locale_Model_Languages;
		$this->t_keys_model	 	= new Locale_Model_TranslateKeys;
    }
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
        $select = $this->select()->where('id = ?', (int)$id);
        return $this->fetchRow($select);
    }

    /**
     * Get select object for pagination
     *
     * @param array $where conditions
     * @param string $order column name
     * @return Zend_Db_Select select object
     */
    public function getSelectForPagination($where=array(), $order='id asc', $limit=null){
        $select = $this->select()->from('translate_messages as tm');
        if(count($where) > 0){
            foreach($where as $on_where => $on_value){
                if($on_value){
                    if(is_array($on_value)){
                        $or_where = '';
                        foreach ($on_value as $count => $v){
                            $or_where .= $this->_db->quoteInto('text('.$on_where.') ILIKE ?', '%'.$v.'%').' OR ';
                        }
                        $select->where(substr($or_where, 0, -3));
                    }else{
                        $select->where('text('.$on_where.') ILIKE ?', '%'.$on_value.'%');
                    }
                }
            }
        }
		
		$select->setIntegrityCheck(false);
		$select->joinLeft('translate_keys as tk', 'tm.key_id = tk.id', array('key'));
		$select->joinLeft('languages as l', 'tm.language_id = l.id', array('name as country_name'));
		
        if(!empty($order)){
            $select->order($order);
        }
        if(!empty($limit)){
            $select->limit($limit);
        }
		
        return $select;
    }

    public function getTranslateForLocale($key){
    	$select = $this->select()->from('translate_messages as tm', array('value'));
		$select->setIntegrityCheck(false)
			->joinLeft('translate_keys as tk', 'tm.key_id = tk.id','')
			->joinLeft('languages as l', 'tm.language_id = l.id','')
			->where('l.country_code = ?', Zend_Registry::get('Zend_Locale'))
			->where('tk.key =?', $key);
		
		$result = $this->fetchRow($select);
				
		return $result->value;
    }

    /**
     * Well-known insert or update function
     *
     * @param array $data
     * @param int id - if it is set than run update of given id, if it's not, than do insert
     * @return int id of inserted od updated row
     */
    public function doSave($data, $id=null){
        if($id){
            return $this->update($data, array('id = ?' => (int)$id));
        }else{
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
	 * 
	 * @param array $datas - Keys in array: value, country_name, key
	 * @return boolean
	 */
	public function doSaveUpload($datas){

		
		$this->_db->beginTransaction();
		try{
			$this->delete("1=1");
			foreach($datas as $one_row){	
				$error_if_happens = $one_row['country_name'].' : '.$one_row['key'].' : '.$one_row['value'];			

				$country 	= $this->language_model->getByCountryName($one_row['country_name']);
				$key 		= $this->t_keys_model->getByKey($one_row['key']);
				
				//dont allow double quotes
				if(strpos($one_row['value'], '"')){
					return array('success' => false, 'error' => $error_if_happens);
				}
				//if value is '.' replace with ''
				if($one_row['value'] == '.'){
					$one_row['value'] = ' ';	
				}
				
				//if key dont exists, create it
				if($key == null){
					$this->t_keys_model->doSave(array('key' => $one_row['key'], 'description' => $one_row['key']));	
					$key = $this->t_keys_model->getByKey($one_row['key']);
				}elseif($country == null || $one_row['value'] == ''){
					continue;	
				}
				
				$one_row['language_id'] 	= $country->id;
				$one_row['key_id'] 		= $key->id;
								
				unset($one_row['country_name']);
				unset($one_row['key']);
						
				$this->doSave($one_row);
			}
			
			$this->_db->commit();
		}
		catch(Exception $e){
			$this->_db->rollBack();
			return array('success' => false, 'error' => $error_if_happens);
		}
		
		return array('success' => true, 'error' => $error_if_happens);;
	}
	
	/**
	 * Get data to populate CSV file for downloading
	 * 
	 * @return array in format array(key=> array(country=>value), key=>...)
	 */
	public function getForCsv(){
		$select = $this->select()->from('translate_messages as tm', array());
		$select->setIntegrityCheck(false)
			->joinLeft('translate_keys as tk', 'tm.key_id = tk.id', array())
			->joinLeft('languages as l', 'tm.language_id = l.id', array())
			->order('country_code asc')
			->columns(array('l.name', 'tk.key', 'value'));
		
		$csv_array = array();
		foreach($this->fetchAll($select) as $r){
			$csv_array[$r->key][$r->name] = $r->value;
		}
		
		return $csv_array;
	}
	
	/**
	 * Return array in special format to show in html table
	 * 
	 * @return array 
	 */
	public function getForHtml(){
		$select = $this->select()->from('translate_messages as tm', array());
		$select->setIntegrityCheck(false)
			->joinLeft('translate_keys as tk', 'tm.key_id = tk.id', array())
			->joinLeft('languages as l', 'tm.language_id = l.id', array())
			->order('country_code asc')
			->columns(array('l.name', 'tk.key', 'value', 'tm.id'));
		
		$csv_array = array();
		foreach($this->fetchAll($select) as $r){
			$csv_array[$r->key][$r->name] = array('value' => $r->value, 'id' => $r->id);
		}
		
		return $csv_array;
	}
	
	/**
	 * check of there is any translation for given country code
	 * 
	 * @param $code , country code
	 * 
	 * Return true if has translation
	 * 
	 * @return  > 0 if has translation 
	 */
	public function hasTranslation($country_code){	
		$select = $this->select()->from($this->_name)
				->setIntegrityCheck(FALSE)
				->joinLeft('languages as l', 'l.id = translate_messages.language_id', array('country_code'))
				->where('l.country_code = ?', $country_code);
									
		return (count($this->fetchAll($select)) > 0) ? true : false;	
	}		
	
	/*
	 * get translation by country code
	 * 
	 * @param $country_code
	 * 
	 * return array
	 * 
	 */
	public function getTranslate($country_code){
		$select = $this->select()->from('translate_messages as tm', array('value'));
		$select->setIntegrityCheck(false)
			->joinLeft('translate_keys as tk', 'tm.key_id = tk.id', array('key'))
			->joinLeft('languages as l', 'tm.language_id = l.id', array())
			->where('l.country_code = ?', $country_code);
		
		$return = array();
		foreach($this->fetchAll($select) as $r){
			$return[$r->key] = $r->value;
		}
		
		return $return;
	}

	public function addTranslation($key, $language_id, $value){
		$key_id = $this->t_keys_model->addKeyIfNotExist($key);
		return $this->doSave(array('key_id' => $key_id, 'language_id' => $language_id, 'value' => $value));
	}

	public function updateTranslation($key, $language_id, $value){
		$key = $this->t_keys_model->getByKey($key);
		return $this->update(array('value' => $value), array('language_id = ?' => (int)$language_id, 'key_id = ?' => $key->id));
	}

	public function getTranslationByKeyAndLanguageId($key, $language_id){
		$select = $this->select()->from('translate_messages as tm', array('value'));
		$select->setIntegrityCheck(false)
				->joinLeft('translate_keys as tk', 'tm.key_id = tk.id', '')
				->where('tk.key = ?', $key)
				->where('tm.language_id = ?', (int)$language_id);
        $result = $this->fetchRow($select);

		return (is_object($result)) ? $result->value : '';
	}

	public function doDeleteByKeyId($key_id){
        return $this->delete(array('key_id = ?' => (int)$key_id)) > 0;
    }
}