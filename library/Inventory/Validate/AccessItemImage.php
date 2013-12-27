<?php
class Inventory_Validate_AccessItemImage extends Zend_Validate_Abstract
{
    const CANT_ACCESS = 'cantaccess';
    protected $_userId;

    protected $_messageTemplates = array(
        self::CANT_ACCESS => "You do not have permission to access this image"
    );

    public function __construct($userId)
    {
        $this->_userId = $userId;
    }

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
        if(empty($value)) {
            return true;
        }
        $canUserAccessImage = new Model_Image(array(
            'itemImageId' => $value
        ));
        if(!$canUserAccessImage->canAccessImage($this->_userId)) {
            $this->_error(self::CANT_ACCESS);
            return false;
        }
        return true;
    }
}
