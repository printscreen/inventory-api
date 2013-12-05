<?php
class Inventory_Validate_IsValidPassword extends Zend_Validate_Abstract
{
    const INVALID_PASSWORD = 'invalidpassword
    ';
    protected $_userId;

    protected $_messageTemplates = array(
        self::INVALID_PASSWORD => "Incorrect Password"
    );

    public function __construct($userId)
    {
        $this->_userId = $userId;
    }

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);

        $user = new Model_User(array('userId' => $this->_userId));
        $user->load();

        $auth = new Model_Auth(array(
            'email' => $user->getEmail(),
            'password' => $value
        ));

        if(!$auth->isValidPassword()) {
            $this->_error(self::INVALID_PASSWORD);
            return false;
        }
        return true;
    }
}
