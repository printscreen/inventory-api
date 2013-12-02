<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDefines()
	{
	    defined('INVENTORY_DB') || define('INVENTORY_DB', 'Inventory_Database');
		defined('TOKEN') || define('TOKEN', 'User_Token');
		defined('SALT') || define('SALT', 'With_my_last_breath_i_curse_Zoidberg!');
		defined('TOKEN_CREATION_RETRY_COUNT') || define('TOKEN_CREATION_RETRY_COUNT',
		    intval($this->getOption('token_creation_retry_count')));
	}

	protected function _initAutoload()
	{
		$autoLoader = Zend_Loader_Autoloader::getInstance();
		$autoLoader->registerNamespace('Inventory_');
		$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                            'basePath'        => APPLICATION_PATH,
                            'namespace'     => '',
                            'resourceTypes'    => array(
                                                'form'    => array(
                                                            'path'        => '/modules/default/views/forms/',
                                                            'namespace'    => 'Form_'
                                                        ),
                                                'model'    => array(
                                                            'path'        => '/models/',
                                                            'namespace'    => 'Model_'
                                                        )
                                                )
                            ));
		return $autoLoader;
	}

	protected function _initApplication()
	{
	    date_default_timezone_set($this->getOption('default_time_zone'));
	}

	protected function _initDb()
	{
		$db = $this->getPluginResource('db')->getDbAdapter();
		$db->setFetchMode(Zend_Db::FETCH_OBJ);
		Zend_Registry::set(INVENTORY_DB, $db);
	}

    protected function _initPlugins()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new Inventory_Controller_Plugin_Acl());
    }
}