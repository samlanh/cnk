<?php

class Section_CustomerController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/section';
    public function init()
    {    	
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	
	}

    public function indexAction()
    {
    	try{
			if($this->getRequest()->isPost()){
				$_data=$this->getRequest()->getPost();
				$search = array(
						'search' => $_data['search'],
                    );
			}else{
				$search = array(
                    'search' => '',
                );
			}
 			$db = new Section_Model_DbTable_DbCustomer();
            $this->view->rs= $db->getAllCustomer($search);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
        

        $form = new Section_Form_FrmSupplier();
		$formAdd = $form->FrmAddSupplier();
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);

    }

   public function addAction()
    {
        if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
            $sms="INSERT_SUCCESS";
			try{
              
				$sms="INSERT_SUCCESS";
				$_dbmodel = new Section_Model_DbTable_DbCustomer();
			    $_dbmodel->addCustomer($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/section/customer");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}		
		}
    }

    public function editAction()
    {
        if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
            $sms="EDIT_SUCCESS";
			try{
				$sms="EDIT_SUCCESS";
				$_dbmodel = new Section_Model_DbTable_DbCustomer();
			    $_dbmodel->updateCustomer($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/section/customer");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}		
		}

        $id=$this->getRequest()->getParam("id");
		$db = new Section_Model_DbTable_DbCustomer();
		$rs=$db->getCustomerById($id);
		$this->view->rs = $rs;

    }
}







