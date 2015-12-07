<?php
/*
 * Index controller in default module
 *
 */
class IndexController extends Zend_Controller_Action {
	
	private $medias_model;
	private $translate_messages_model;

	public function init(){
		$front = Zend_Controller_Front::getInstance(); 
        $request = $front->getRequest();
        $action_name = $request->getActionName();

        $this->medias_model = new Default_Model_Medias();
        $this->translate_messages_model = new Locale_Model_TranslateMessages();

		$shared_media_id = $this->_getParam('img');
		$this->view->autoshow_image = (bool) $shared_media_id;

        switch($action_name){
        	case 'gallery': $page = 'foto-galerija'; break;
			case 'menu': $page = 'jelovnik'; break;
			case 'rooms': $page = 'sobe'; break;
			default : $page = 'index';        	
        }

        $shared_media = $this->medias_model->getById($shared_media_id);
        if($shared_media){
        	$this->view->fb_title = $shared_media->title;
			$this->view->fb_description = $shared_media->description;
            $this->view->fb_image = Zend_Registry::get('server_url').My_Utilities::getFullMediaPathDiffSizes($shared_media,'original');
            $this->view->fb_url =  Zend_Registry::get('server_url')."/".$page."/".$shared_media_id;
        }else{
			$this->view->fb_title =  $this->translate_messages_model->getTranslateForLocale('meta_fb_title');
			$this->view->fb_description =  $this->translate_messages_model->getTranslateForLocale('meta_fb_description');        	
            $this->view->fb_image = Zend_Registry::get('server_url').'/images/'.strtolower(Zend_Registry::get('Zend_Locale')).'/1170/index-zupska-avlija-logo.png';
            $this->view->fb_url = Zend_Registry::get('server_url');
        }
	}

	public function indexAction(){
	}

	public function aboutAction(){	
	}

	public function contactAction(){
	}

	public function galleryAction(){
		$this->view->restorant_gallery = $this->medias_model->getByLibraryId(Zend_Registry::get('restorant_gallery_id'));		
		$this->view->creations_gallery = $this->medias_model->getByLibraryId(Zend_Registry::get('creations_gallery_id'));		
		$this->view->tradition_gallery = $this->medias_model->getByLibraryId(Zend_Registry::get('tradition_gallery_id'));		
		$this->view->garden_gallery    = $this->medias_model->getByLibraryId(Zend_Registry::get('garden_gallery_id'));
		$this->view->recipes_gallery   = $this->medias_model->getByLibraryId(Zend_Registry::get('recipes_gallery_id'));	
	}

	public function menuAction(){
		$this->view->menu_gallery = $this->medias_model->getByLibraryId(Zend_Registry::get('menu_gallery_id'));		
	}

	public function roomsAction(){	
		$this->view->rooms_gallery   = $this->medias_model->getByLibraryId(Zend_Registry::get('rooms_gallery_id'));	
	}
}