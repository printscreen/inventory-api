<?php

class ProfileController extends Inventory_Controller_Action
{
    public function resetPasswordAction()
    {
        $success = false;
        $form = new Form_ChangePassword($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $user = new Model_User(array(
                'userId' => $this->getRequesterUserId()
            ));
            $user->updatePassword(
                $form->getElement('password')->getValue()
            );
            $success = true;
        }

        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ));
    }
}
