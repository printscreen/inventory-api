<?php
class Inventory_Validate_ItemNameDuplicate extends Zend_Validate_Abstract
{
    const IN_USE = 'inuse';
    protected $_userId;
    protected $_itemId;

    protected $_messageTemplates = array(
        self::IN_USE => "'%value%' is already in use by another item"
    );

    public function __construct($userId, $itemId = 'itemId')
    {
        $this->_userId = $userId;
        $this->_itemId = $itemId;
    }

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
        $itemId = isset($context[$this->_itemId]) ? $context[$this->_itemId] : null;

        $item = new Model_Item(array('name'=>$value));
        $item->load($this->_userId);
        $foundId = $item->getItemId();

        if(is_numeric($foundId) && $foundId != $itemId) {
            $this->_error(self::IN_USE);
            return false;
        }

        return true;
    }
}
