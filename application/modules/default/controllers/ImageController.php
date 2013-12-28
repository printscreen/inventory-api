<?php

class ImageController extends Inventory_Controller_Action
{
    public function getAction()
    {
        $success = false;
        $form = new Form_AccessItemImage($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $image = new Model_Image(array(
                'itemImageId' => $form->getElement('itemImageId')->getValue()
            ));
            $success = $image->load();
        }
        if($success) {
            $this->_helper->viewRenderer->setNoRender(true);
            header('Content-type: image/jpeg');
            echo $image->getImage();
        } else {
            $this->_helper->json(array(
                'success' => $success,
                'errors' => $form->getFormErrors()
            ));
        }
    }

    public function viewAction()
    {
        $success = false;
        $images = array();
        $form = new Form_AccessItem($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getImages = new Model_Images();
            $getImages->getItemImages(
                $form->getElement('itemId')->getValue()
              , $this->getRequest()->getParam('isThumbnail', false)
              , $this->getRequest()->getParam('sort')
              , $this->getRequest()->getParam('offset')
              , $this->getRequest()->getParam('limit')
            );
            $images = $getImages->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'images' => $images,
            'errors' => $form->getFormErrors()
        ));
    }

    public function addAction()
    {
        $success = false;
        $form = new Form_ItemImage($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $image = new Model_Image(array(
                'itemId' => $form->getElement('itemId')->getValue(),
                'userId' => $this->getRequesterUserId()
            ));
            $image->insert(
                $form->getElement('image')->getFileName()
            );

            $thumbnail = new Model_Image(array(
                'itemId' => $form->getElement('itemId')->getValue()
              , 'userId' => $this->getRequesterUserId()
              , 'isThumbnail' => true
            ));
            $thumbnail->insert(
                $form->getElement('image')->getFileName()
            );
            $img = $image->toArray();
            $thumb = $thumbnail->toArray();
            $success = true;

        }
        $this->_helper->json(array(
            'success' => $success,
            'image' => $img,
            'thumbnail' => $thumb,
            'errors' => $form->getFormErrors()
        ));
    }

    public function makeDefaultAction()
    {
        $success = false;
        $form = new Form_AccessItemImage($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $image = new Model_Image(array(
                'itemImageId' => $form->getElement('itemImageId')->getValue()
              , 'defaultImage' => true
            ));
            $success = $image->update();
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ));
    }

    public function deleteAction()
    {
        $success = false;
        $form = new Form_AccessItemImage($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $image = new Model_Image(array(
                'itemImageId' => $form->getElement('itemImageId')->getValue()
            ));
            $success = $image->delete();
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ));
    }
}