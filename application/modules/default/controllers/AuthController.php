<?php

class AuthController extends Zend_Controller_Action
{   
    public function loginAction()
    {
        $form = new Form_Login();
    	$success = false;
    	$message = '';
    	$token = '';
    	$errors = array();
        if ($form->isValid($this->getRequest()->getParams())) {
 			$auth = Zend_Auth::getInstance();
    		// Set up the authentication adapter
 			$authAdapter = new Model_Auth(array(
 			      'email' => $form->getElement('email')->getValue()
 			    , 'password' => $form->getElement('password')->getValue()
 			));
			$result = $auth->authenticate($authAdapter);
            if($result->isValid()) {
                $success = true;
                $getUser = new Model_User();
            	$getUser->setEmail($form->getElement('email')->getValue());
            	$getUser->load();
                $user = $getUser->toArray();
            	
            	$getToken = new Model_Token(array(
            	    'userId' => $getUser->getUserId()
            	));
            	$token = $getToken->generate();
            } else {
            	$message = 'Wrong Email/Password';
            }
        }
        $this->_helper->json(array(
        	'success' => $success,
            'token' => $token,
            'user' => $user,
            'message' => $message,
            'errors' => $form->getFormErrors() 
        ));
    }
}
