<?php
/**
 * Medias Controller class in Cms module
 *
 * @author Nikola Ristivojevic <nikola.ristivojevic@golive.rs>
 * 
 * @version  August 2013
 */
class Cms_MediasController extends Zend_Controller_Action
{

    private $medias_model;
    private $media_libraries_model;
    private $media_libraries_medias_model;

    public function init()
    {
        $this->medias_model                 = new Cms_Model_Medias();
        $this->media_libraries_model        = new Cms_Model_MediaLibraries();
        $this->media_libraries_medias_model = new Cms_Model_MediaLibrariesMedias();
    }

    public function indexAction()
    {
        $this->view->form = new Cms_Form_MediasFilter;
        // Get sort from $_GET for pagination, default is id asc
        $sort = $this->_getParam('sort_by', 'id') . ' ' . $this->_getParam('sort_type', 'desc');

        // Param to filter sql query
        $where = array(
            'id' => $this->_getParam('id'),
            'title' => $this->_getParam('title'),
            'created' => $this->_getParam('created'),
            'file_name' => $this->_getParam('file_name'),
        );

        $this->view->form->setDefaults($where);

        $this->view->media_library = $this->media_libraries_model->getById($this->_getParam('library_id'));

        if (!isset($this->view->media_library->id) || empty($this->view->media_library->id)) {
            My_Utilities::fmsg('Library does not exist.', 'warning');
            $this->_redirect('/cms/media-libraries/index');
        }

        //Get select for pagination, create and configure pagination object
        $pagination_select = $this->medias_model->getSelectForPagination($where, $sort, $this->view->media_library->id);

        $this->view->paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($pagination_select));
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber((int) $this->_getParam('page', 1));

        //Header for html table
        $this->view->header = array(
            array('id', 'Id', 0),
            array('title', 'Title', 0),
            array('file_name', 'File name', 0),
            array('created', 'Created', 0),
            array('', '', 200)
        );

        $this->view->page = (int) $this->_getParam('page', 1);
    }

    public function newAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $media_library = $this->media_libraries_model->getById($this->_getParam('library_id'));

        if (!empty($_FILES)) {
            $file_info = pathinfo($_FILES['file']['name']);
            $tempFile = $_FILES['file']['tmp_name'];
            $new_name = md5(rand(1000, 10000) . time() . $_FILES['file']['name']) . "." . preg_replace('/^.*\.([^.]+)$/D', '$1', $_FILES['file']['name']);

            $this->uploadAndSaveImage($new_name, $tempFile, $media_library->path);

            $default_values = array(
                "file_name" => $new_name,
                "title" => preg_replace("/[^a-z0-9\.]/", " ", strtolower($file_info['filename'])),
                "media_type_id" => Zend_Registry::get('image_media_type_id')
            );

            $media_id = $this->medias_model->doSave($default_values);

            $this->media_libraries_medias_model->saveByMediaLibraryId($media_id, $media_library->id);
        }
    }

    public function editAction()
    {
        // Try to find record by id
        $this->view->existing = $this->medias_model->getById((int) $this->_getParam('id', 0));
        
        // If row does not exist, redirect to list with appropriate message
        if (!isset($this->view->existing->id) || empty($this->view->existing->id)) {
            My_Utilities::fmsg('Zapis nije nađen.', 'warning');
            $this->_redirect('/cms/media-libraries/index');
        }
        
        $file_name = $this->view->existing->file_name;

        // Create an instance of form and set defaults
        $this->view->form = new Cms_Form_Medias(array('file_name' => $file_name, 'media_library_path'=>$this->view->existing->path));
        $this->view->form->setDefaults($this->view->existing->toArray());
        
        
        // Check is post and is posted data valid
        if ($this->_request->isPost() && $this->view->form->isValid($_POST)) {
            $values = $this->view->form->getValues();

            $values['url'] = str_replace('/watch?v=', '/v/', $values['url']).'?autoplay=1';

            unset($values['original']);
            $this->medias_model->doSave($values, $this->view->existing->id);

            if ($values > 0) {
                My_Utilities::fmsg('Podaci su uspešno sačuvani.', 'success');
            } else {
                My_Utilities::fmsg('Greška. Podaci nisu uspešno sačuvani.', 'error');
            }

            $this->_redirect('/cms/medias/index/library_id/'.$this->view->existing->media_library_id.'/page/' . $this->_getParam('page', 1));
        }
    }

    public function deleteAction()
    {
        $media_id = (int) $this->_getParam('id', 0);
        $media = $this->medias_model->getById($media_id);
        
        if ($this->deleteMedia($media_id)) {
            My_Utilities::fmsg('Zapis je uspešno obrisan.', 'success');
        } else {
            My_Utilities::fmsg('Zapis nije obrisan.', 'error');
        }
        $this->_redirect('/cms/medias/index/library_id/'.$media->media_library_id.'/page/' . $this->_getParam('page', 1));
    }

    public function deleteRemovedFileAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $media_id = (int) $this->_getParam('media_id', 0);

        if ($this->deleteMedia($media_id)) {
            $this->_response->setBody(json_encode(array('success' => 'true')));
        } else {
            $this->_response->setBody(json_encode(array('success' => 'false')));
        }
    }

    public function getLastUploadedImageAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $title = preg_replace("/[^a-z0-9\.]/", " ", strtolower($this->_getParam('title')));

        if (empty($title)) {
            $this->_response->setBody(json_encode(array('success' => 'false')));
            return;
        }

        $file = $this->medias_model->getLastByTitle($title);
        if (!$file) {
            $this->_response->setBody(json_encode(array('success' => 'false')));
            return;
        }

        $this->_response->setBody(json_encode(array('success' => 'true', 'file_name' => $file->toArray())));
    }

    private function uploadAndSaveImage($new_name, $tempFile, $media_library_path, $width = null, $height = null)
    {
        $image = new Imagick($tempFile);

        if ($height && $width) {
            $image->cropThumbnailImage($width, $height);
            $folder = $width.'x'.$height;
        } elseif ($width && !$height) {
            $image->scaleImage($width, 0);
            $folder = 'master';
        } elseif ($height && !$width) {
            $image->scaleImage(0, $height);
            $folder = 'master';
        } else {
            $folder = 'original';
        }

        $targetPath = My_Utilities::getUploadMediaPathDiffSizes($new_name, $media_library_path, $folder);
        $targetFile = $targetPath . '/' . $new_name;
        return $image->writeImage($targetFile);
    }
    


    private function deleteMedia($media_id)
    {
        $media = $this->medias_model->getById($media_id);

        $array = array('original');
        array_pop($array);
         
        if ($this->medias_model->doDelete($media['id'])) {
            foreach ($array as $one) {
                $image_path = substr(My_Utilities::getUploadMediaPathDiffSizes($media['file_name'], $media['path'], $one).'/'.$media['file_name'], 0, -4);

                foreach (glob($image_path.'.*') as $file_name) {
                    if (is_file($file_name)) {
                        unlink($file_name);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
