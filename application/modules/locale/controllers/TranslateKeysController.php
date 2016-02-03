<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_TranslateKeysController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->view->form = new Locale_Form_TranslateKeysFilter();

        // Get sort from $_GET for pagination, default is id asc
        $sort = $this->_getParam('sort_by', 'id').' '.$this->_getParam('sort_type', 'desc');

        // Param to filter sql query
        $where = array(
            'id'            => $this->_getParam('id'),
            'key'            => $this->_getParam('key'),
            'description'    => $this->_getParam('description')
        );

        $this->view->form->setDefaults($where);

        //Get select for pagination, create and configure pagination object
        $model_translate_keys     = new Locale_Model_TranslateKeys();
        $pagination_select         = $model_translate_keys->getSelectForPagination($where, $sort);
        $this->view->paginator     = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($pagination_select));
        $this->view->paginator->setItemCountPerPage(200);
        $this->view->paginator->setCurrentPageNumber((int)$this->_getParam('page', 1));

        //Header for html table
        $this->view->header = array(
            array('id', 'Id', 50),
            array('key', 'Key', 150),
            array('description', 'Description', 0),
            array('', '', 100)
        );

        $this->view->page = (int)$this->_getParam('page', 1);
    }

    public function newAction()
    {
        $this->view->form = new Locale_Form_TranslateKeys();

        // Check is posted data valid, if is it than insert user
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $model_translate_keys     = new Locale_Model_TranslateKeys();
            $result             = $model_translate_keys->doSave($this->view->form->getValues());

            if ($result > 0) {
                My_Utilities::fmsg('Data successfully saved.', 'success');
            } else {
                My_Utilities::fmsg('Error. Data not saved.', 'error');
            }

            $this->_redirect('/locale/translate-messages/index/'.$this->_getParam('page', 1));
        }
    }

    public function editAction()
    {
        // Try to find record by id
        $model_translate_keys     = new Locale_Model_TranslateKeys();
        $this->view->existing     = $model_translate_keys->getById((int)$this->_getParam('id', 0));

        // If row does not exist, redirect to list with appropriate message
        if (!isset($this->view->existing->id) || empty($this->view->existing->id)) {
            My_Utilities::fmsg('Record not found.', 'warning');
            $this->_redirect('/locale/translate-messages/index/'.$this->_getParam('page', 1));
        }

        // Create an instance of form and set defaults
        $this->view->form = new Locale_Form_TranslateKeys();
        $this->view->form->setDefaults($this->view->existing->toArray());

        // Check is post and is posted data valid
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $values = $this->view->form->getValues();
            $result = $model_translate_keys->doSave($values, $this->view->existing->id);

            if ($result > 0) {
                My_Utilities::fmsg('Data successfully saved.', 'success');
            } else {
                My_Utilities::fmsg('Error. Data not saved.', 'error');
            }

            $this->_redirect('/locale/translate-keys/index/'.$this->_getParam('page', 1));
        }
    }

    public function deleteAction()
    {
        $model_translate_keys = new Locale_Model_TranslateKeys();

        if ($model_translate_keys->doDelete((int)$this->_getParam('id', 0))) {
            My_Utilities::fmsg('Data successfully deleted.', 'success');
        } else {
            My_Utilities::fmsg('Error. Record not deleted.', 'error');
        }
        $this->_redirect('/locale/translate-keys/index/'.$this->_getParam('page', 1));
    }

    public function showAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $id             = (int)$this->_getParam('id');
        $model_translate_keys     = new Locale_Model_TranslateKeys();
        $result         = $model_translate_keys->getById($id)->toArray();

        $this->_response->setBody(json_encode($result));
    }
}
