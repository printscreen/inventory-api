<?php 
class Inventory_Validate_EditUser extends Zend_Validate_Abstract
{   
	const CANT_EDIT = 'cantedit';
	protected $_token;
	protected $_userId;
	
	protected $_messageTemplates = array(
    	self::CANT_EDIT => "You do not have permission to edit this user"
    );
    
	public function __construct($token, $userId)
    {
        $this->_token = $token;
        $this->_userId = $userId;
    }
    
    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
        $userToEditId = isset($context[$this->_token]) ? $context[$this->_token] : null;
    	if(empty($userToEditId) || !is_numeric($userToEditId)) {
    	    return false;
    	}
    	
    	$user = new Model_User(array('userId'=>$this->_userId)); 	
    	if(!$user->canEditUser($userToEditId)) {

    	    $this->_error(self::CANT_EDIT);
            return false;
    	}
    	return true;
    }
}
