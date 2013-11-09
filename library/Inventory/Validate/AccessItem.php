<?php
class Inventory_Validate_AccessItem extends Zend_Validate_Abstract
{
    const CANT_EDIT = 'cantedit';
    protected $_userId;

    protected $_messageTemplates = array(
        self::CANT_EDIT => "You do not have permission to access this item"
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
        $canUserAccessItem = new Model_Item(array(
            'itemId' => $value
        ));
        if(!$canUserAccessItem->canEditItem($this->_userId)) {
            $this->_error(self::CANT_EDIT);
            return false;
        }
        return true;
    }
}
