<?php 
class Inventory_Validate_AccessUser extends Zend_Validate_Abstract
{   
	const CANT_EDIT = 'cantedit';
	protected $_userId;
	
	protected $_messageTemplates = array(
    	self::CANT_EDIT => "You do not have permission to access this user"
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
    	
    	$user = new Model_User(array('userId'=>$this->_userId)); 	
    	if(!$user->canEditUser($value)) {

    	    $this->_error(self::CANT_EDIT);
            return false;
    	}
    	return true;
    }
}
