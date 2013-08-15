<?php

class Inventory_Controller_Action extends Zend_Controller_Action
{
    protected function getRequesterUserId()
    {
        return Zend_Registry::get(TOKEN)->getUserId();
    }
}