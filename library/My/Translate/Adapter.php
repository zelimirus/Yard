<?php 
/**
 * Class for hendling translate
 * Get data from DB by given country_code and return to translate adapter
 * 
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @version July 2013
 */
class My_Translate_Adapter{
	/**
	 * Return all data by given country code
	 * 
	 * @param string $country_code
	 * @return array Translated data in array key-value form
	 */
	public static function getTranslate($country_code){		
		$model_t_messages = new Locale_Model_TranslateMessages();
		return $model_t_messages->getTranslate($country_code);
	}
}