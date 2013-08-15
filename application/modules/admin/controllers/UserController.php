<?php
class Admin_UserController extends Inventory_Controller_Action
{
    public function getUserAction()
    {
        $user = new Model_User(array(
            'userId' => $this->getRequest()->getParam('userId')
        ));
        $user->load();
        $this->_helper->json(array(
            'success' => true,
            'user' => $user->toArray() 
        ));
    }
    
    public function viewEmployeeAction()
    {
        $users = new Model_Users();
        $users->getUserByType(
            Model_User::USER_TYPE_EMPLOYEE
          , $this->getRequest()->getParam('active', true)
          , $this->getRequest()->getParam('sort')
          , $this->getRequest()->getParam('offset')
          , $this->getRequest()->getParam('limit')
        );
        $this->_helper->json(array(
            'success' => true,
            'users' => $users->toArray()
        ));
    }
    
    public function viewCustomerAction()
    {
        $users = new Model_Users();
        $users->getUserByType(
            Model_User::USER_TYPE_CUSTOMER
          , $this->getRequest()->getParam('active', true)
          , $this->getRequest()->getParam('sort')
          , $this->getRequest()->getParam('offset')
          , $this->getRequest()->getParam('limit')
        );
        $this->_helper->json(array(
            'success' => true,
            'users' => $users->toArray()
        ));
    }
    
    public function editEmployeeAction()
    {
        $form = new Admin_Form_User();
        $success = false;
        $errors = array();
        if ($form->isValid($this->getRequest()->getParams())) {     
            $user = new Model_User(array(
                'userId' => $form->getElement('userId')->getValue()
              , 'firstName' => $form->getElement('firstName')->getValue()
              , 'lastName' => $form->getElement('lastName')->getValue()
              , 'email' => $form->getElement('email')->getValue()
              , 'userTypeId' => Model_User::USER_TYPE_EMPLOYEE
              , 'active' => $form->getElement('active')->getValue()
            
            ));
            if(is_numeric($form->getElement('userId')->getValue())) {
                $user->update();
            } else {
                $user->insert($user->getTemporaryPassword());
            }
            $success = true;
            $userId = $user->getUserId();      	
        }
        $this->_helper->json(array(
            'success' => $success,
            'userId' => $userId,
            'errors' => $form->getFormErrors()
        ));
    }
    
    public function editCustomerAction()
    {
        $form = new Admin_Form_Customer(
            $this->getRequesterUserId(),
            !is_numeric($this->getRequest()->getParam('userId'))
        );
        $success = false;
        $errors = array();
        if ($form->isValid($this->getRequest()->getParams())) {     
            $user = new Model_User(array(
                'userId' => $form->getElement('userId')->getValue()
              , 'firstName' => $form->getElement('firstName')->getValue()
              , 'lastName' => $form->getElement('lastName')->getValue()
              , 'email' => $form->getElement('email')->getValue()
              , 'userTypeId' => Model_User::USER_TYPE_CUSTOMER
              , 'active' => $form->getElement('active')->getValue()
            
            ));
            if(is_numeric($form->getElement('userId')->getValue())) {
                $user->update();
            } else {
                $user->insert($user->getTemporaryPassword());
                $userLocation = new Model_UserLocation(array(
                      'userId' => $user->getUserId()
                    , 'locationId' => $form->getElement('locationId')->getValue()   
                ));
                $userLocation->insert();
            }
            $success = true;
            $userId = $user->getUserId();      	
        }
        $this->_helper->json(array(
            'success' => $success,
            'userId' => $userId,
            'errors' => $form->getFormErrors()
        ));
    }
    
    public function viewUserLocationsAction()
    {
        $userLocations = new Model_UserLocations(array(
        	'userId' => $this->getRequest()->getParam('userId')
        ));
        $userLocations->getUserLocations();
        $this->_helper->json(array(
            'success' => true,
            'userLocations' => $userLocations->toArray()
        ));
    }
    
    public function editUserLocationsAction()
    {
        $success = false;
        $error = array();
        $locations = $this->getRequest()->getParam('locationId', array());
        if(!is_array($locations) || count($locations) < 1) {
            $error[] = 'You must have at least one location';
        }
        if(empty($error)) {
            $userLocations = new Model_UserLocations(array(
            	'userId'=>$this->getRequest()->getParam('userId')
            ));
            $userLocations->setUserLocations($locations);
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $error
        ));
    }
}