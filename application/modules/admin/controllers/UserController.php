<?php
class Admin_UserController extends Inventory_Controller_Action
{
    public function getAction()
    {
        $success = false;
        $form = new Form_AccessUser($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getUser = new Model_User(array(
            	'userId' => $this->getRequest()->getParam('userId')
            ));
            $getUser->load();
            $user = $getUser->toArray();
            $success = true;  
        }
        $this->_helper->json(array(
            'success' => $success,
            'user' => $user,
            'errors' => $form->getFormErrors() 
        ), $this->getRequest()->getParam('callback'));
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
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function viewCustomerAction()
    {
        $users = new Model_Users();
        $users->getCustomers(
            $this->getRequesterUserId()
          , $this->getRequest()->getParam('active', true)
          , $this->getRequest()->getParam('sort')
          , $this->getRequest()->getParam('offset')
          , $this->getRequest()->getParam('limit')
        );
        $this->_helper->json(array(
            'success' => true,
            'users' => $users->toArray()
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function editEmployeeAction()
    {
        $form = new Admin_Form_User($this->getRequesterUserId());
        $success = false;
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
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function editCustomerAction()
    {
        $form = new Admin_Form_Customer(
            $this->getRequesterUserId(),
            !is_numeric($this->getRequest()->getParam('userId'))
        );
        $success = false;
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
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function viewUserLocationAction()
    {
        $success = false;
        $form = new Form_AccessUser($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getUserLocations = new Model_UserLocations(array(
            	'userId' => $this->getRequest()->getParam('userId')
            ));
            $getUserLocations->getUserLocations();
            $userLocations = $getUserLocations->toArray();
            $success = true;  
        }
        $this->_helper->json(array(
            'success' => $success,
            'userLocations' => $userLocations,
            'errors' => $form->getFormErrors() 
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function addUserLocationAction()
    {
        $success = false;
        $form = new Form_UserLocation($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $userLocations = new Model_UserLocations(array(
            	'userId' => $form->getElement('userId')->getValue()
            ));
            $userLocations->addUserLocations(
                !is_array($form->getElement('locationId')->getValue()) ?
                array($form->getElement('locationId')->getValue()) :
                $form->getElement('locationId')->getValue()
            );
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function deleteUserLocationAction()
    {
        $success = false;
        $form = new Form_UserLocation($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $userLocations = new Model_UserLocations(array(
            	'userId' => $form->getElement('userId')->getValue()
            ));
            $userLocations->deleteUserLocations(
                !is_array($form->getElement('locationId')->getValue()) ?
                array($form->getElement('locationId')->getValue()) :
                $form->getElement('locationId')->getValue()
            );
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ), $this->getRequest()->getParam('callback'));
    }
}