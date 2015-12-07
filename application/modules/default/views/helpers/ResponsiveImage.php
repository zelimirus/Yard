<?php
/**
 * 
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * 
 */
class Zend_View_Helper_ResponsiveImage extends Zend_View_Helper_Abstract{

	public function responsiveImage($file_name){
		$html = '';

		$all_widths	= explode(',', Zend_Registry::get('responsive_breakpoints'));
		foreach ($all_widths as $count => $width) {
			$src = Zend_Registry::get('img_path').strtolower(Zend_Registry::get('Zend_Locale')).'/'.$width.'/'.$file_name;
			if(!file_exists(Zend_Registry::get('app_path').$src)){
				$src = Zend_Registry::get('img_path').strtolower(Zend_Registry::get('default_locale')).'/'.$width.'/'.$file_name;
			}

			if($count == 0){
				$html .= 'src="'.$src.'" ';
			}else{
				$html .= Zend_Registry::get('responsive_prefix').$width.'="'.$src;
			}
		}
		
		echo $html;
	}
}