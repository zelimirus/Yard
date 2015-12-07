<?php
/**
 * Matchmaker Controller class in Services module
 * 
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 * 
 * @version  april 2014
 */
class Locale_ErrorController extends Zend_Controller_Action{
	/**
	 * Action error
	 *
	 * @return void
	 */
	public function errorAction(){
		$errors = $this->_getParam('error_handler');

		switch($errors->type){
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->headTitle()->prepend('Page not found');
				$this->view->message = 'Page not found';
				break;
			default:
				// Application error
				$this->getResponse()->setHttpResponseCode(500);
				$this->view->headTitle()->prepend('Application error');
				$this->view->message = 'Application error';
				break;
		}

		// Log exception, if logger available
		$log = $this->getLog();
		if(!empty($log)){
			$log->crit($this->view->message, $errors->exception);
		}

		// Conditionally display exceptions
		if($this->getInvokeArg('displayExceptions') == true){
			$this->view->exception = $errors->exception;
		}

		$this->view->request = $errors->request;		
	}
	/**
	 * Get logger
	 *
	 * @return mixed
	 */
	public function getLog(){
		$bootstrap = $this->getInvokeArg('bootstrap');
		if(!$bootstrap->hasPluginResource('Log')){
			return false;
		}
		$log = $bootstrap->getResource('Log');
		return $log;
	}
}