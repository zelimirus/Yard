<?php
/**
 * ErrorControllerSelector plugin.
 *
 * @category Class
 * @package  App
 * @author   Aleksandar Dragojlovic | <aleksandar@funtelecom.rs>
 * @version  Release:    |         | 1.0
 *
 */
class My_Plugin_ErrorControllerSelector extends Zend_Controller_Plugin_Abstract
{
    /**
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        // if the ErrorHandler plugin is not registered, bail out
        if (!($front->getPlugin('Zend_Controller_Plugin_ErrorHandler') instanceof Zend_Controller_Plugin_ErrorHandler)) {
            return;
        }
        $error = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        // Generate a test request to use to determine if the error controller in our module exists
        $testRequest = new Zend_Controller_Request_HTTP();
        $testRequest->setModuleName($request->getModuleName())
                    ->setControllerName($error->getErrorHandlerController())
                    ->setActionName($error->getErrorHandlerAction());
        // Does the controller even exist?
        if ($front->getDispatcher()->isDispatchable($testRequest)) {
            $error->setErrorHandlerModule($request->getModuleName());
        }
    }
}
