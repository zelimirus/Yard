<?php
/**
 * Override zend flash messenger helper class
 *
 * @author Aleksandar Varnicic 	 <aleksandar.varnicic@golive.rs>
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 *
 * @version may 2013
 */
class Application_Layouts_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract{

	/**
	 * Return flash messages if we have it
	 * Posible type is: error, info, alert, success
	 *
	 * @return String message with style
	 *
	 */
	public function flashMessenger(){
		$output	 = '';
		$current = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getCurrentMessages();

		if(!empty($current)){
			$messages = $current;
		}
		else{
			$messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
		}

		if(!empty($messages)){
			// Posible type: error, info, alert, success
			foreach($messages as $key => $message){
				$output .=
						'<div class="wrapper_'.$key.'" data-message="'.current($message).'" data-type="'.key($message).'"><div class="alert alert-'.key($message).'">'.current($message).'</div></div>';
			}
			Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->clearMessages();
		}
		return $output;
	}

}

?>