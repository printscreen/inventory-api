<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $message = 'Application error';
                break;
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;

        $errors = array(
            'success' => false,
            'message' => $message,
            
        );
        if($exception) {
            $errors = array_merge($errors, array(
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
                'params' => $this->getRequest()->getParams()
            ));
        }
        $this->_helper->json($errors);   
        
    }
}
