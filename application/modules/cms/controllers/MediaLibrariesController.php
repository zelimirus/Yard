<?php

class Cms_MediaLibrariesController extends Zend_Controller_Action
{

    private $media_libraries_model;
    private $media_libraries_medias_model;
    private $medias_model;

    public function init()
    {
        $this->media_libraries_model        = new Cms_Model_MediaLibraries();
        $this->media_libraries_medias_model = new Cms_Model_MediaLibrariesMedias();
        $this->medias_model                 = new Cms_Model_Medias();
    }

    public function indexAction()
    {
        $this->view->form = new Cms_Form_MediaLibrariesFilter;
        $sort = $this->_getParam('sort_by', 'id') . ' ' . $this->_getParam('sort_type', 'desc');

        $where = array(
            'id' => $this->_getParam('id'),
            'name' => $this->_getParam('name')
        );

        $this->view->form->setDefaults($where);

        $pagination_select = $this->media_libraries_model->getSelectForPagination($where, $sort);

        $this->view->paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($pagination_select));
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber((int) $this->_getParam('page', 1));

        $this->view->header = array(
            array('id', 'Id', 0),
            array('name', 'Name', 0),
            array('', '', 200)
        );

        $this->view->page = (int) $this->_getParam('page', 1);
    }

    public function newAction()
    {
        $this->view->form = new Cms_Form_MediaLibraries();
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $values = $this->view->form->getValues();
            $values['path'] = md5($values['name']);

            $dir = Zend_Registry::get('upload_media_path').'/'.$values['path'];

            if (file_exists($dir)) {
                My_Utilities::fmsg('Greška. Postoji vec folder sa ovim imenom.', 'error');
                $this->_redirect('/cms/media-libraries/index');
            }

            if (!is_dir($dir)) {
                if (!mkdir($dir, 0777)) {
                    My_Utilities::fmsg('Greška. Nije kreiran folder', 'error');
                    $this->_redirect('/cms/media-libraries/index');
                }
            }

            if (file_exists($dir)) {
                $result = $this->media_libraries_model->doSave($values, $this->view->existing->id);
                if ($result > 0) {
                    My_Utilities::fmsg('Podaci su uspešno sačuvani.', 'success');
                } else {
                    My_Utilities::fmsg('Greška. Podaci nisu uspešno sačuvani.', 'error');
                }
                $this->_redirect('/cms/medias/index/library_id/'.$result);
            } else {
                My_Utilities::fmsg('Greška. Ne postoji folder', 'error');
            }
        }
    }

    public function editAction()
    {
        $this->view->existing = $this->media_libraries_model->getById((int) $this->_getParam('id', 0));

        if (!isset($this->view->existing->id) || empty($this->view->existing->id)) {
            My_Utilities::fmsg('Zapis nije nađen.', 'warning');
            $this->_redirect('/cms/media-libraries/index');
        }

        $this->view->form = new Cms_Form_MediaLibraries();
        $this->view->form->setDefaults($this->view->existing->toArray());

        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $values = $this->view->form->getValues();
            $values['path'] = md5($values['name']);

            $source = Zend_Registry::get('upload_media_path').'/'.$this->view->existing->path;
            $destination = Zend_Registry::get('upload_media_path').'/'.$values['path'];

            if (file_exists($destination)) {
                My_Utilities::fmsg('Greška. Postoji vec folder sa ovim imenom.', 'error');
                $this->_redirect('/cms/media-libraries/edit/id/'.$this->_getParam('id').'/page/' . $this->_getParam('page', 1));
            }

            if (!My_Utilities::copyDirectory($source, $destination) || !My_Utilities::deleteDirectory($source)) {
                My_Utilities::fmsg('Greška. Podaci nisu dobro prekopirani.', 'error');
                $this->_redirect('/cms/media-libraries/index/page/' . $this->_getParam('page', 1));
            }
            
            $result = $this->media_libraries_model->doSave($values, $this->view->existing->id);
            if ($result > 0) {
                My_Utilities::fmsg('Podaci su uspešno sačuvani.', 'success');
            } else {
                My_Utilities::fmsg('Greška. Podaci nisu uspešno sačuvani.', 'error');
            }

            $this->_redirect('/cms/media-libraries/index/page/' . $this->_getParam('page', 1));
        }
    }

    public function deleteAction()
    {
        $id = (int) $this->_getParam('id', 0);
        $media_ids = $this->medias_model->getIdsArrayByMediaLibId($id);

        $media_lib = $this->media_libraries_model->getById($id);
        $dir = Zend_Registry::get('upload_media_path').'/'.$media_lib['path'];
        if (!My_Utilities::deleteDirectory($dir)) {
            My_Utilities::fmsg('Greška. Nije izbrisan folder.', 'error');
            $this->_redirect('/cms/media-libraries/index/page/' . $this->_getParam('page', 1));
        }

        if ($this->media_libraries_model->doDelete($id)) {
            $this->media_libraries_medias_model->doDeleteByMediaLibraryId($id);
            $this->medias_model->doDeleteByIdsArray($media_ids);

            My_Utilities::fmsg('Zapis je uspešno obrisan.', 'success');
        } else {
            My_Utilities::fmsg('Zapis nije obrisan.', 'error');
        }
        $this->_redirect('/cms/media-libraries/index/page/' . $this->_getParam('page', 1));
    }
}
