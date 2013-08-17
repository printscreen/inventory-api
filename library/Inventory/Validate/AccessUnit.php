<?php 
class Inventory_Validate_AccessUnit extends Zend_Validate_Abstract
{   
	const CANT_EDIT = 'cantedit';
	protected $_token;
	protected $_userId;
	
	protected $_messageTemplates = array(
    	self::CANT_EDIT => "You do not have permission to access this unit"
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
	    $editUnit = new Model_Unit(array(
                'userId' => $this->_userId,
                'unitId' => $value
            ));
        if(!$editUnit->canEditUnit()) {
            $this->_error(self::CANT_EDIT);
            return false;
        }
    	return true;
    }
}
