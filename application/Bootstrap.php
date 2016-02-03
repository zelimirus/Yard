<?php
/**
 * Standard Bootstrap class
 *
 * @version  february 2014
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Save all values from config to ZendRegistry
     */
    protected function _initLocalConfigs()
    {
        $allconfig     = new Zend_Config_Ini(APPLICATION_PATH.'/configs/config.ini');
        $envconfig     = $allconfig->toArray();
        $registry     = new Zend_Registry($envconfig[APPLICATION_ENV], ArrayObject::ARRAY_AS_PROPS);
        foreach ($registry as $key => $value) {
            Zend_Registry::set($key, $value);
        }
    }

    /*
     * init Zend_Session
     */
    public function _initSession()
    {
        Zend_Session::setOptions(array(
        'remember_me_seconds'    => 31536000, // one year
        'gc_maxlifetime'        =>  2592000, // one month
        ));
        Zend_Session::start();
    }

    /*
     * init database adapter
     *
     */
    protected function _initDb()
    {
        $resource = $this->getPluginResource('db');

        $db = $resource->getDbAdapter();
        Zend_Registry::set('db', $db);

        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
    }

    public function _initLocale()
    {
        $this->bootstrap('modules');
        $this->bootstrap('session');

        $lang_countries_model = new Locale_Model_Languages();
        $lang_countries = $lang_countries_model->getCountryCodes();

        $ter_countries_model = new Locale_Model_Countries();
        $ter_countries = $ter_countries_model->getCountryCodesAndLang();

        $country_code = false;

        //detect true locale, default is set in config
        $true_locale = $locale = Zend_Registry::get('default_locale');
        foreach ($ter_countries as $code => $lang) {
            $code_exploded = explode('_', $code);
            if ($code_exploded[1] == $country_code) {
                $true_locale = $code;
                if (isset($lang)) {
                    $locale = $lang;
                }
            }
        }

        //detect locale, default is set in config
        foreach ($lang_countries as $code) {
            if (strpos($code, $country_code) !== false) {
                $locale = $code;
            }
        }

        if (isset($_GET['lang']) && in_array($_GET['lang'], $lang_countries)) {
            $locale = $_SESSION['lang'] = $_GET['lang'];
        } elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $lang_countries)) {
            $locale = $_SESSION['lang'];
        }

        Zend_Registry::set('true_locale', $true_locale);
        Zend_Registry::set('Zend_Locale', $locale);
    }

    /*
     * define custom routes 
     */
    protected function _initRoute()
    {
        $this->bootstrap('locale');

        $router     = Zend_Controller_Front::getInstance()->getRouter();
        $router->addDefaultRoutes();
        $config     = new Zend_Config_Ini(APPLICATION_PATH.'/configs/routes.ini');
        $router->addConfig($config, 'routes');
    }
}
