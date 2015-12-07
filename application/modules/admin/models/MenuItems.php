<?php
/* MenuItems Model class
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
* @version  may 2014
*/
class Admin_Model_MenuItems extends My_Db_Table{
	protected $_name	 = 'admin_menu_items';
	protected $_primary	 = 'id';

	public function getById($id){
		$select = $this->select()->from($this->_name);
		$select->setIntegrityCheck(false);
		$select->joinLeft('menu_items_icons as mii', 'mii.id = admin_menu_items.icon_id', 'icon');
		$select->where('admin_menu_items.id = ?', (int)$id);
		return $this->fetchRow($select);
	}
	
	public function getForDropDown() {
		$select = $this->select();
		$result = $this->fetchAll($select);
		$return = array();
		foreach ($result as $one_row) {
			$return[$one_row->id] = $one_row->title;
		}
		return $return;
	}

	public function doSave($data, $id = null) {
		if ($id) {
			return $this->update($data, array('id = ?' => (int) $id));
		} else {
			$data['order_id'] = $this->getLastOrderId() + 1;
			return $this->insert($data);
		}
	}
	
	public function getMenuItems($role_id){
		$select = $this->select()->from($this->_name);
		$select->setIntegrityCheck(FALSE)
				->joinLeft('roles_admin_menu_items as rami', 'admin_menu_items.id = rami.admin_menu_item_id','')
				->joinLeft('menu_items_icons as mii', 'admin_menu_items.icon_id = mii.id','icon')
				->where('role_id = ?', $role_id)
				->order('order_id');
		
		$result = $this->fetchAll($select);
		if(is_object($result)){
			$manu_items = $result->toArray();
			$packed_manu_items = array();
	        foreach ($manu_items as $key => $item){
	        	$tmp_array = array();
	            if($item['parent_id'] == null){
        			$params = array();
                    if($item['params']){
                        foreach(explode(',', $item['params']) as $one){
                            $exploaded = explode(':', trim($one));
                            $params[$exploaded[0]] = $exploaded[1];
                        }
                    }
	                $manu_items[$key]['url'] = array_merge(array('module' => $item['module'], 'controller' => $item['controller'], 'action' => $item['action']), $params);
	                $tmp_array = $manu_items[$key];

	                $tmp_child_array = array();
	                foreach ($manu_items as $c_key => $child_item){	                	
	                    if ($child_item['parent_id'] == $item['id']) {
	                        $params = array();
	                        if($child_item['params']){
	                            foreach(explode(',', $child_item['params']) as $one){
	                                $exploaded = explode(':', trim($one));
	                                $params[$exploaded[0]] = $exploaded[1];
	                            }
	                        }
	                        $manu_items[$c_key]['url'] = array_merge(array('module' => $child_item['module'], 'controller' => $child_item['controller'], 'action' => $child_item['action']), $params);
	                    	$tmp_child_array[] = $manu_items[$c_key];
	                    }
	                }
	                $tmp_array['child'] = $tmp_child_array;
	                $packed_manu_items[] = $tmp_array;
	         	} 
	       	} 
		}

		return $packed_manu_items;	
	}
	
	public function getLastOrderId() {
		$select = $this->select()->from($this->_name, 'order_id')->order('order_id desc')->limit(1);
		return $this->fetchRow($select)->order_id;
	}
	

	public function doDelete($id){
		return $this->delete(array('id = ?' => (int)$id)) > 0;
	}
	
	public function getSelectForPagination($where = array(), $order, $limit = null){
		$select = $this->select()->from($this->_name);
	
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
						$select->where('text('.$this->_name.'.'.$on_where.') ILIKE ?', '%'.$on_value.'%');
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
		
		$result = $this->fetchAll($select)->toArray();
		foreach ($result as $key => $child) {			
			if($child['parent_id'] !== null){
				$parent = $this->getById($child['parent_id']);
				$result[$key]['parent'] = $parent['name'];
			}
			else{
				$result[$key]['parent'] = '';
			}
		}
		
		return $result;
	}

	public function totalNumber(){
		$select = $this->select()
				->from($this->_name, 'count(*) as count');
				
		return $this->fetchRow($select)->count;
	}
}