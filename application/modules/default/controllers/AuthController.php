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
                $user = new Model_User();
            	$user->setEmail($form->getElement('email')->getValue());
            	$user->load();

            	$getToken = new Model_Token(array(
            	    'userId' => $user->getUserId()
            	));
            	$token = $getToken->generate();
            } else {
            	$message = 'Wrong Email/Password';
            }
        }
        $this->_helper->json(array(
        	'success' => $success,
            'token' => $token,
            'message' => $message,
            'errors' => $form->getFormErrors() 
        ));
    }
}
