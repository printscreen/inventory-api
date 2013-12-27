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
        defined('SYSTEM_NAME') || define('SYSTEM_NAME', 'System_Name');
        defined('SYSTEM_EMAIL_ADDRESS') || define('SYSTEM_EMAIL_ADDRESS', 'System_Email_Address');
        defined('SYSTEM_MAILER') || define('SYSTEM_MAILER', 'System_Emailer_Object');
        defined('IMAGE_FILE_PATH') || define('IMAGE_FILE_PATH', 'Image_File_Path');
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
        Zend_Registry::set(SYSTEM_NAME, $this->getOption('application_name'));
        Zend_Registry::set(IMAGE_FILE_PATH, $this->getOption('image_file_path'));
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

    protected function _initMailTransport()
    {
        $options = $this->getOption('mail');
        Zend_Registry::set(SYSTEM_EMAIL_ADDRESS, $options['system_address']);
        $mailer = new Zend_Mail_Transport_Smtp($options['server'], array(
                        'ssl' => 'tls',
                        'port' => 587,
                        'auth' => 'login',
                        'username' => $options['user_name'],
                        'password' => $options['password']
            )
        );
        Zend_Registry::set(SYSTEM_MAILER,$mailer);
    }
}