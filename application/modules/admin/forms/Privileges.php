<?php
/**
 * Privileges Form class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2013
 */
class Admin_Form_Privileges extends My_Form_Base{

	protected $_roleid;

	public function getRoleid(){
		return $this->_roleid;
	}

	public function setRoleid($roleid){
		$this->_roleid = $roleid;
	}

	public function init(){
		$resources_table = new Admin_Model_Resources();
		foreach($resources_table->getAll() as $resource){
			// select permissions for this resource for current role_id, but only where action != ''
			$permissions	 = new Admin_Model_Permissions();
			$all_permissions = $permissions->getByRoleAndResourceSpecial($this->getRoleid(), $resource->id);

			// Set allowed and denied value for text fild
			$allowed = $denied	 = '';
			foreach($all_permissions as $perm){
				if($perm->is_allowed == 't'){
					$allowed .= $perm->action.';';
				}
				else{
					$denied .= $perm->action.';';
				}
			}
			$allowed = trim($allowed, ';');
			$denied	 = trim($denied, ';');

			//Set elements
			$parent = new Zend_Form_Element_Select((string)$resource->id);
			$parent->setLabel('Resource ')
			->addMultiOption((string)$resource->id, $resource->name)
			->setAttrib('disabled', 'disabled');

			$allow = new Zend_Form_Element_Text($resource->id.'_allow');
			$allow->setLabel('Allow ')->setValue($allowed);

			$deny = new Zend_Form_Element_Text($resource->id.'_deny');
			$deny->setLabel('Deny ')->setValue($denied);

			$this->addElement($parent);
			$this->addElement($allow);
			$this->addElement($deny);
		}

		$submit = new Zend_Form_Element_Submit('save');
		$submit->setAttrib('class', 'btn btn-primary');
		$submit->setLabel('Confirm');

		$this->setAction('')
		->setMethod('post')
		->addElement($submit);
	}

}