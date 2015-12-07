<?php
/**
 * LogAdminActions Model class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic <milan.marjanovic@golive.rs>
 *
 * @version  november 2014
 */
class Admin_Model_LogAdminActions extends Zend_Db_Table_Abstract{
	protected $_name = 'log_admin_actions';
	protected $_primary = 'id';
	protected $_sequence = 'log_admin_actions_id_seq';

	/**
	 * Get log records with filter
	 *
	 * @param array $where_params
	 *
	 * @return Zend_Db_Table
	 */
	public function getLogDbActions($where_params, $order = 'id desc'){
		$select = $this->select()
						->from(array('laa' => 'log_admin_actions'), 'laa.*')
						->setIntegrityCheck(false)
						->joinLeft(array('us' => 'admin_users'), 'laa.user_id = us.id', array('email' => 'us.email'))
						->joinLeft(array('ro' => 'roles'), 'us.role_id = ro.id', array('role_name' => 'ro.name'));

		if($where_params['users']){
			if(!is_array($where_params['users'])){
				$where_params['users'] = array($where_params['users']);
			}
			foreach($where_params['users'] as $count => $user){
				if(!$count){
					$where = "us.id = $user";
				}else{
					$where .= " OR us.id = $user";
				}
			}
			$select->where($where);
		}
		if($where_params['roles']){
			$where = '';
			if(!is_array($where_params['roles'])){
				$where_params['roles'] = array($where_params['roles']);
			}
			foreach($where_params['roles'] as $count => $role){
				if(!$count){
					$where = "ro.id = $role";
				}else{
					$where .= " OR ro.id = $role";
				}
			}
			$select->where($where);
		}
		if($where_params['actions']){
			$where = '';
			if(!is_array($where_params['actions'])){
				$where_params['actions'] = array($where_params['actions']);
			}
			foreach($where_params['actions'] as $count => $action){
				if(!$count){
					$where = "laa.action = '".$action."'";
				}else{
					$where .= " OR laa.action = '".$action."'";
				}
			}
			$select->where($where);
		}

		if($where_params['affected_table']){
			$select->where('affected_table = ? ', $where_params['affected_table']);
		}

		$select->where('date > ? ', $where_params['start_date']);
		$select->where('date < ? ', $where_params['end_date']);
		$select->order($order);

		return $select;
	}

	/**
	 * Log action
	 *
	 * @param string sql query
	 * @param string user_id
	 */
	public function logDbAction($changed_table_name, $user_action, $params, $row_id=null){
		$user = Zend_Auth::getInstance()->getIdentity();
		//log only admin user's actions - the one that have role_id param
		if(!isset($user->id) || !isset($user->role_id)){
			return;
		}
		$string_params = '';
		//format params
		foreach($params as $field => $param){
			if($field == 'password'){
				continue;
			}
			$string_params .= $field.':'.$param.'; ';
		}
		if($user_action == 'UPDATE'){
			$string_params .= 'WHERE:'.$row_id;
		}

		$data = array('user_id' => $user->id, 'action' => $user_action,
			 		'affected_table' => $changed_table_name, 'params' => $string_params);
		
		return $this->insert($data);
	}
}
