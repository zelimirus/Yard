<?php
/**
 * LayoutSelector plugin.
 *
 * @category Class
 * @package  App
 * @author   Aleksandar Dragojlovic | <aleksandar@funtelecom.rs>
 * @version  Release:    |         | 1.0
 *
 */
class My_Plugin_LayoutSelector extends Zend_Controller_Plugin_Abstract {
	/**
	 * Called before an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$module = $request->getModuleName();
		$layout = Zend_Layout::getMvcInstance();
		// check module and automatically set layout
		$layoutsDir = $layout->getLayoutPath();
		// check if module layout exists else use default
		if($module == 'default'){
			$layout->setLayout("layout");	
		} else{
			$layout->setLayout("admin");
		}
	}
}