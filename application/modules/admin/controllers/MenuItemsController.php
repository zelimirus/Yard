<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2014
 */
class Admin_MenuItemsController extends Zend_Controller_Action{

	private $admin_menu_items_model;
	
	public function init(){        		
		$this->admin_menu_items_model = new Admin_Model_MenuItems();
	}
	
	public function indexAction(){
		$this->view->page = (int)$this->_getParam('page', 1);
		// Get sort from $_GET for pagination, default is id asc
		$this->view->sort_by = $this->_getParam('sort_by') ? $this->_getParam('sort_by') : 'id';
		$this->view->sort_type = $this->_getParam('sort_type') ? $this->_getParam('sort_type') : 'asc';
		
		$sort = $this->view->sort_by.' '.$this->view->sort_type;
		//Get select for pagination, create and configure pagination object
		$pagination_select		 = $this->admin_menu_items_model->getSelectForPagination(array(), $sort);
		$this->view->paginator	 = new Zend_Paginator(new Zend_Paginator_Adapter_Array($pagination_select));
		$count_per_page = 30;
        $this->view->paginator->setItemCountPerPage($count_per_page);
        $this->view->count_per_page = $count_per_page;

        $this->view->paginator->setCurrentPageNumber($this->view->page);

		//Header for html table
		$this->view->header = array(
			array('id', 'Id', 50),
			array('name', 'Name', 0),
			array('title', 'Title', 0),
			array('', 'Parent', 0),
			array('module', 'Module', 0),
			array('controller', 'Controller', 0),
			array('action', 'Action', 0),
			array('params', 'Params', 0),
			array('order_id', 'Order Id', 0),
			array('is_active', 'Active', 0),
			array('', '', 100)
		);
		
		$this->view->page = (int) $this->_getParam('page', 1);
		$this->view->total_number = $this->admin_menu_items_model->totalNumber();
	}

	public function newAction(){		
		$this->view->form = new Admin_Form_MenuItems();
		// Check is posted data valid, if is it than insert user
		if($this->_request->isPost() && $this->view->form->isValid($_POST)){
			$form_values = $this->view->form->getValues();
			$form_values['parent_id'] = ($form_values['parent_id'] === '0') ? null : $form_values['parent_id'];	
			if(!$form_values['icon_id']){
				$form_values['icon_id'] = null;
			}
			if($this->admin_menu_items_model->doSave($form_values) > 0){
				My_Utilities::fmsg('Data is succesfully saved.', 'success');
			}else{
				My_Utilities::fmsg('Error. Data is not saved.', 'error');
			}
			$this->_redirect('admin/menu-items/');
		}
	}

	public function editAction(){
		// Try to find record by id
		$this->view->existing	 = $this->admin_menu_items_model->getById((int)$this->_getParam('id', 0));
		// If row does not exist, redirect to list with appropriate message
		if(!isset($this->view->existing->id) || empty($this->view->existing->id)){
			My_Utilities::fmsg('Record not found.', 'warning');
			$this->_redirect('admin/menu-items/'.$this->_getParam('page', 1));
		}

		// Create an instance of form and set defaults
		$this->view->form = new Admin_Form_MenuItems();
		$this->view->form->setDefaults($this->view->existing->toArray());

		// Check is post and is posted data valid
		if($this->_request->isPost() && $this->view->form->isValid($_POST)){
			$form_values = $this->view->form->getValues();
			
			if(!$form_values['icon_id']){
				$form_values['icon_id'] = null;
			}
			if($this->admin_menu_items_model->doSave($form_values, $this->view->existing->id) > 0){
				My_Utilities::fmsg('Data is succesfully saved.', 'success');
			}else{
				My_Utilities::fmsg('Error. Data is not saved.', 'error');
			}
			$this->_redirect('admin/menu-items/');
		}
	}

	public function deleteAction(){
		if($this->admin_menu_items_model->doDelete((int)$this->_getParam('id', 0))){
			My_Utilities::fmsg('Record is succesfully deleted.', 'success');
		}else{
			My_Utilities::fmsg('Record is not deleted.', 'error');
		}
		$this->_redirect('admin/menu-items/index/page/'.$this->_getParam('page', 1));
	}
	
	public function updateOrderAction(){
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $page = $this->_getParam('page');
        $count_per_page = $this->_getParam('count');
        $rows = $this->_getParam('row');
        $direction = $this->_getParam('direction');
        $total = $this->_getParam('total');

        $count =  ($direction === 'asc') ? $count_per_page *($page-1) +1 : $total -($count_per_page*($page-1));

        if (is_array($rows)) {
            foreach ($rows as $row) {
                $result =  $this->admin_menu_items_model->doUpdateOrder($row,$count);
                $direction === 'asc' ? $count++ : $count-- ;
            }
            $this->_response->setBody(json_encode(array('success' => true)));
        }else{
            $this->_response->setBody(json_encode(array('success' => false)));

        }       
    } 
}