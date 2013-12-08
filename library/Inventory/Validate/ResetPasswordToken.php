<?php
class Inventory_Validate_ResetPasswordToken extends Zend_Validate_Abstract
{
    const INVALID_TOKEN = 'invalidtoken';
    protected $_email;

    protected $_messageTemplates = array(
        self::INVALID_TOKEN => "Incorrect Token"
    );

    public function __construct($email)
    {
        $this->_email = $email;
    }

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);

        $user = new Model_User(array('email' => $context[$this->_email]));

        if(!$user->load() || ($user->getResetPasswordToken() != $value)) {
            $this->_error(self::INVALID_TOKEN);
            return false;
        }
        return true;
    }
}
