<?php
/**
 * 
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * 
 */
class Zend_View_Helper_Image extends Zend_View_Helper_Abstract{

	public function image($file_name, $width = null){
		if(!$width){
			$all_widths	= explode(',', Zend_Registry::get('responsive_breakpoints'));
			$width = end($all_widths);
		}

		$src = Zend_Registry::get('img_path').strtolower(Zend_Registry::get('Zend_Locale')).'/'.$width.'/'.$file_name;
		if(!file_exists(Zend_Registry::get('app_path').$src)){
			$src = Zend_Registry::get('img_path').strtolower(Zend_Registry::get('default_locale')).'/'.$width.'/'.$file_name;
		}

		return Zend_Registry::get('server_url').$src;
	}
}