<?php

class Model_Acl extends Zend_Acl
{
	protected $_userTypeResources;
	protected $_moduleResources;
	protected $_userId;
	protected $_userTypeId;

	public function __construct(array $options = array())
	{
		$settings = array_merge(array(
			'userId' => null,
            'userTypeId' => null
            ), $options);
	    $this->_userTypeId = $settings['userTypeId'];
	}

	public function initAcl()
	{
		// TODO: cache the logic of this function to cut out repeated SQL calls
		$role = new Zend_Acl_Role($this->_userTypeId);
		if(!$this->hasRole($role)) {
			$this->addRole($role);
		}
		$getUserTypeResources = new Model_UserTypeResources(
			array(
				'userTypeId' => $this->_userTypeId
			)
		);
		$this->_userTypeResources = $getUserTypeResources->getUserTypeResources();
		foreach($this->_userTypeResources as $userTypeResource) {
			if(!$this->has($userTypeResource->getResourceName())) {
				$this->addResource($userTypeResource->getResourceName());
			}
            $this->allow($role, $userTypeResource->getResourceName(), 'access');
		}

		//Add allowed resources for location modules
		$getModuleResources = new Model_ModuleResources(
			array(
				'userId' => $this->_userId
			)
		);
		$this->_moduleResources = $getModuleResources->getModuleResources();
		foreach($this->_moduleResources as $moduleResource) {
			if(!$this->has($moduleResource->getResourceName())) {
				$this->addResource($moduleResource->getResourceName());
			}
            $this->allow($role, $moduleResource->getResourceName(), 'access');
		}

	}

	public function setUserTypeId($userTypeId){$this->_userTypeId = $userTypeId;}
    public function getUserTypeId(){return $this->_userTypeId;}
}