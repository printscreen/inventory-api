<?php

class AuthController extends Zend_Controller_Action
{
    public function loginAction()
    {
        $form = new Form_Login();
    	$success = false;
    	$message = '';
    	$token = '';
        $user = array();
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

    public function forgotPasswordAction()
    {
        $form = new Form_ForgotPassword();
        $success = false;
        if($form->isValid($this->getRequest()->getParams())) {
            $user = new Model_User(array(
                'email' => $form->getElement('email')->getValue()
            ));
            if($user->load()) {
                $link = sprintf('%s?token=%s&email=%s',
                    $form->getElement('url')->getValue(),
                    $user->getResetPasswordToken(),
                    $form->getElement('email')->getValue()
                );

                $mail = new Zend_Mail();
                $mail->setBodyText("To reset you password go here. $link")
                     ->setBodyHtml("<a href=\"$link\">Click here to reset your password</a>")
                     ->setFrom(
                        Zend_Registry::get(SYSTEM_EMAIL_ADDRESS),
                        Zend_Registry::get(SYSTEM_NAME)
                    )
                     ->addTo($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName())
                     ->setSubject('Reset Password')
                     ->send(Zend_Registry::get(SYSTEM_MAILER));

                $success = true;
            } else {
                $message = 'No account with that email address';
            }
        } else {
            $message = 'Invalid Email Address';
        }

        $this->_helper->json(array(
            'success' => $success,
            'message' => $message
        ));
    }

    public function resetPasswordAction()
    {
        $form = new Form_ResetPassword();
        $success = false;
        if($form->isValid($this->getRequest()->getParams())) {
            $user = new Model_User(array(
                'email' => $form->getElement('email')->getValue()
            ));
            if($user->load()) {
                $user->updatePassword(
                    $form->getElement('password')->getValue()
                );
                $success = true;
            }
        } else {
            $message = current($form->getFormErrors());
        }

        $this->_helper->json(array(
            'success' => $success,
            'message' => $message
        ));
    }
}
