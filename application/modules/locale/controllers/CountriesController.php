<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_CountriesController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->view->form = new Locale_Form_CountriesFilter();

        // Get sort from $_GET for pagination, default is id asc
        $sort = $this->_getParam('sort_by', 'id').' '.$this->_getParam('sort_type', 'desc');

        // Param to filter sql query
        $where = array(
            'id'            => $this->_getParam('id'),
            'country_code'    => $this->_getParam('country_code'),
            'name'            => $this->_getParam('name'),
        );

        $this->view->form->setDefaults($where);

        //Get select for pagination, create and configure pagination object
        $countries     = new Locale_Model_Countries();
        $pagination_select         = $countries->getSelectForPagination($where, $sort);
        $this->view->paginator     = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($pagination_select));
        $this->view->paginator->setItemCountPerPage(20);
        $this->view->paginator->setCurrentPageNumber((int)$this->_getParam('page', 1));

        //Header for html table
        $this->view->header = array(
            array('id', 'Id', 50),
            array('name', 'Country name', 0),
            array('country_code', 'Country code', 0),
            array('calling_code', 'Calling code', 0),
            array('language', 'Language', 0),
            array('is_active', 'Active', 100),
            array('', '', 100)
        );

        $this->view->page = (int)$this->_getParam('page', 1);
    }

    public function newAction()
    {
        $this->view->form = new Locale_Form_Countries();

        // Check is posted data valid, if is it than insert user
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $values = $this->view->form->getValues();
            
            $countries = new Locale_Model_Countries();
            $result     = $countries->doSave($values);

            if ($result > 0) {
                My_Utilities::fmsg('Data successfully saved', 'success');
            } else {
                My_Utilities::fmsg('Error. Data not saved', 'error');
            }

            $this->_redirect('/locale/countries');
        }
    }

    public function editAction()
    {
        // Try to find record by id
        $countries   = new Locale_Model_Countries();
        
        $this->view->existing     = $countries->getById((int)$this->_getParam('id', 0));

        // If row does not exist, redirect to list with appropriate message
        if (!isset($this->view->existing->id) || empty($this->view->existing->id)) {
            My_Utilities::fmsg('Record not found', 'warning');
            $this->_redirect('/locale/countries/index');
        }

        // Create an instance of form and set defaults
        $this->view->form = new Locale_Form_Countries();
        $this->view->form->setDefaults($this->view->existing->toArray());

        // Check is post and is posted data valid
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $values = $this->view->form->getValues();
            
            $result = $countries->doSave($values, $this->view->existing->id);

            if ($result > 0) {
                My_Utilities::fmsg('Data successfully saved', 'success');
            } else {
                My_Utilities::fmsg('Error. Data not saved', 'error');
            }

            $this->_redirect('/locale/countries');
        }
    }

    public function deleteAction()
    {
        $id                     = (int)$this->_getParam('id', 0);
        
        $countries   = new Locale_Model_Countries();
        $country = $countries->getById($id);
        
        if ($countries->doDelete($id)) {
            My_Utilities::fmsg('Data successfully deleted', 'success');
        } else {
            My_Utilities::fmsg('Error. Record not deleted', 'error');
        }
        $this->_redirect('/locale/countries/index/page/'.$this->_getParam('page', 1));
    }

    public function activateAction()
    {
        $countries = new Locale_Model_Countries();

        if ($countries->doSave(array('is_active' => true), (int)$this->_getParam('id', 0))) {
            My_Utilities::fmsg('Record activated');
        } else {
            My_Utilities::fmsg('Error. Record not activated', 'error');
        }

        $this->_redirect('locale/countries/index/page/'.$this->_getParam('page', 1));
    }

    public function deactivateAction()
    {
        $countries = new Locale_Model_Countries();

        if ($countries->doSave(array('is_active' => false), (int)$this->_getParam('id', 0))) {
            My_Utilities::fmsg('Record deactivated');
        } else {
            My_Utilities::fmsg('Error. Record not deactivated', 'error');
        }

        $this->_redirect('locale/countries/index/page/'.$this->_getParam('page', 1));
    }
    
    public function showAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $id             = (int)$this->_getParam('id');
        $db_model     = new Locale_Model_Countries();
        $result         = $db_model->getById($id)->toArray();

        $this->_response->setBody(json_encode($result));
    }
}
