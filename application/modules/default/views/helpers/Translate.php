<?php
/**
 * Override translate helper
 *
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 *
 * @version november 2014
 */
class Zend_View_Helper_Translate extends Zend_View_Helper_Abstract
{

    /**
     * If there is no translation for current language get for default one
     *
     * @return String translate value
     */
    public function translate($key, $strip_tags = true)
    {
        $translator = Zend_Registry::get('Zend_Translate');

        if (!$translator->isTranslated($key)) {
            $translate = $translator->translate($key, Zend_Registry::get('default_locale'));
        } else {
            $translate = $translator->translate($key);
        }
        
        if ($strip_tags) {
            $translate = strip_tags($translate);
        }

        return $translate;
    }
}
