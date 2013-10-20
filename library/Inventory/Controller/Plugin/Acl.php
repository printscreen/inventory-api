<?php
class Inventory_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	protected $_publicModules;
	protected $_publicControllers;
	protected $_publicActions;

	public function __construct()
	{
		$this->_publicModules = array();
		$this->_publicControllers = array('default:error');
		$this->_publicActions = array(
				  'default:auth:login'
				, 'default:auth:forgot-password'
				, 'default:auth:reset-password'
				, 'default:auth:logout'
				, 'default:error:error');
	}

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
	    //If not dispatchable
		if(!(Zend_Controller_Front::getInstance()->getDispatcher()->isDispatchable($request))) {
            return false;
        }

	    $reqModule = $this->getRequest()->getModuleName();
		$reqController = $this->getRequest()->getControllerName();
		$reqAction = $this->getRequest()->getActionName();
		$reqModuleStr = $reqModule;
		$reqControllerStr = $reqModule.':'.$reqController;
		$reqActionStr = $reqModule.':'.$reqController.':'.$reqAction;

		if( in_array($reqControllerStr, $this->_publicControllers) ||
			in_array($reqActionStr, $this->_publicActions)) {
			//If module, controller, or action is publically open, don't run it through ACL
			return;
		}

		$token = new Model_Token(array(
		    'token' => $this->getRequest()->getParam('token')
		));
		if(!$token->load()){
		    throw new Inventory_Acl_Exception('You must pass a valid token');
		}
		Zend_Registry::set(TOKEN, $token);

		//Check if the module-controller-action is valid
		$dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
		if ($dispatcher->isDispatchable($request)) {
			$userAcl = new Model_Acl(
			    array('userTypeId' => $token->getUserTypeId())
			);
			$userAcl->initAcl();
			$denied = true;
			try {
				if($userAcl->isAllowed($userAcl->getUserTypeId(), $reqActionStr, 'access')) {
					$denied = false;
				}
			} catch(Zend_Exception $e) {
				$denied = true;
			}
			if($denied) {
				throw new Inventory_Acl_Exception('Resource Denied');
			}
		}
	}
}
