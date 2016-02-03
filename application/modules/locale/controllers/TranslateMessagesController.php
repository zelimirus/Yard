<?php
/**
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * @version  october 2014
 */
class Locale_TranslateMessagesController extends Zend_Controller_Action
{
    
    private $_model_t_messages;
    private $_model_t_keys;
    private $_model_countries;
    
    public function init()
    {
        $this->_model_t_messages = new Locale_Model_TranslateMessages();
        $this->_model_t_keys     = new Locale_Model_TranslateKeys();
        $this->_model_countries  = new Locale_Model_Languages();
    }
    
    public function indexAction()
    {
        mb_internal_encoding("UTF-8");
                        
        $this->view->translates    = $this->_model_t_messages->getForHtml();
        $this->view->keys        = $this->_model_t_keys->getAll();
        $this->view->countries    = $this->_model_countries->getAll();
        
        //Header for html table
        $this->view->header        = array();
        $this->view->header[]    = array('', 'Key / Country', 220);
        foreach ($this->view->countries as $country) {
            $this->view->header[] = array('', $country->name, '');
        }
    }

    public function newAction()
    {
        //If we have key_id and country_id in $_GET
        $add_translate_only            = ($this->_getParam('k') && $this->_getParam('c'));
        
        if ($add_translate_only) {
            $key_id                    = $this->_getParam('k');
            $country_id                = $this->_getParam('c');
            $defaults['key_id']        = $this->_model_t_keys->getById($key_id)->key;
            $defaults['language_id'] = $this->_model_countries->getById($country_id)->name;
            
            $this->view->form        = new Locale_Form_TranslateMessages2();
            $this->view->form->setDefaults($defaults);
        } else {
            $this->view->form        = new Locale_Form_TranslateMessages();
        }
        
        //Check is posted data valid, if is it than insert user
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $this->_model_t_messages    = new Locale_Model_TranslateMessages();
            $values        = $this->view->form->getValues();
            if ($add_translate_only) {
                $values['language_id']    = $country_id;
                $values['key_id']        = $key_id;
            }

            $result = $this->_model_t_messages->doSave($values);

            if ($result > 0) {
                My_Utilities::fmsg('Data successfully saved.', 'success');
                $this->deleteCache();
            } else {
                My_Utilities::fmsg('Error. Data not saved.', 'error');
            }

            $this->_redirect('/locale/translate-messages/index/'.$this->_getParam('page', 1));
        }
    }

    public function editAction()
    {
        $this->view->existing = $this->_model_t_messages->getById($this->_getParam('id', 0));
        
        if (!isset($this->view->existing->id) || empty($this->view->existing->id)) {
            My_Utilities::fmsg('Record not found.', 'warning');
            $this->_redirect('/locale/translate-messages/index/'.$this->_getParam('page', 1));
        }
        
        //Create an instance of form and set defaults
        $defaults                = $this->view->existing->toArray();
        $defaults['language_id'] = $this->_model_countries->getById($defaults['language_id'])->name;
        $defaults['key_id']        = $this->_model_t_keys->getById($defaults['key_id'])->key;
        $this->view->form = new Locale_Form_TranslateMessages2();
        $this->view->form->setDefaults($defaults);
        
        //Check is post and is posted data valid
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $values = $this->view->form->getValues();
            unset($values['key_id']);
            unset($values['language_id']);
            $result = $this->_model_t_messages->doSave($values, $this->view->existing->id);

            if ($result > 0) {
                My_Utilities::fmsg('Data successfully saved.', 'success');
                $this->deleteCache();
            } else {
                My_Utilities::fmsg('Error. Data not saved.', 'error');
            }

            $this->_redirect('/locale/translate-messages/index/'.$this->_getParam('page', 1));
        }
    }

    public function deleteAction()
    {
        if ($this->_model_t_messages->doDelete($this->_getParam('id', 0))) {
            My_Utilities::fmsg('Data successfully deleted.', 'success');
            $this->deleteCache();
        } else {
            My_Utilities::fmsg('Error. Record not deleted.', 'error');
        }
        $this->_redirect('/locale/translate-messages/index/'.$this->_getParam('page', 1));
    }
    
    /**
     * Download all records from adopters DB table
     * @return new header, download CVS file
     */
    public function downloadCsvAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        //Get all content from DB in string
        $countries    = $this->_model_countries->getAll();
        $keys        = $this->_model_t_keys->getAll();
        $empty        = $this->_getParam('empty');
        $content    =  My_Utilities::getCsv($this->_model_t_messages->getForCsv(), $countries, $keys, isset($empty));
        $file        = 'prevod.csv';
        
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        $this->_response->setBody($content);
    }
    
    /**
     * Upload CSV file. Delete all records from DB table and insert them again
     */
    public function uploadAction()
    {
        $this->view->form = new Locale_Form_UploadTranslate();
        
        //We don't want to save file but only read content
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $data = My_Utilities::readCsv($_FILES['file']['tmp_name']);

            if ($data === false || !count($data)) {
                My_Utilities::fmsg("Error, data not uploaded.", 'error');
                return;
            }

            $result = $this->_model_t_messages->doSaveUpload($data);
            if ($result['success']) {
                My_Utilities::fmsg("Translation succsessfully uploaded.");
                $this->deleteCache();
                $this->_redirect('/locale/translate-messages/index/'.$this->_getParam('page', 1));
            } else {
                My_Utilities::fmsg("Writting into DB not accomplished.".$result['error'], 'error');
            }
        }
    }
    
    public function showAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $result    = $this->_model_t_messages->getById($this->_getParam('id'))->toArray();

        $this->_response->setBody(json_encode($result));
    }
    
    /**
     * delete translated data from cache (APC)
     */
    private function deleteCache()
    {
        $frontendOptions = array('automatic_serialization' => true);
        $cache             = Zend_Cache::factory('Core', 'Apc', $frontendOptions, array());
        
        Zend_Translate::setCache($cache);
        Zend_Translate::clearCache();
        
        $this->_redirect('/locale/translate-messages/index/'.$this->_getParam('page', 1));
    }
}
