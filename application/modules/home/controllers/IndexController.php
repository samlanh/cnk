<?php

class Home_IndexController extends Zend_Controller_Action
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
		$this->view->bankAcc = $dbDash->getBankAccountBalance();
		
		$this->view->supplierDept = $dbDash->getTotalDebtBySupplier();
    	
    }

   public function testAction()
    {
    	
    }


}







