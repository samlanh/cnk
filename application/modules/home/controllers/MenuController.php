<?php

class Home_MenuController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/home';
    public function init()
    {    	
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	
	}

    public function indexAction()
    {
		$dbDash = new Home_Model_DbTable_DbDashboard();
		$this->view->productDisplay = $dbDash->getProductDisplay();
		$this->view->getAllCustomerBalance = $dbDash->getAllCustomerBalance();
		$this->view->supplierBalance = $dbDash->getAllSupplierBalance();
    	
    }

   public function testAction()
    {
    	
    }


}







