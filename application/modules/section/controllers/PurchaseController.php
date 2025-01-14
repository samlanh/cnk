<?php

class Section_PurchaseController extends Zend_Controller_Action
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
			$param = $this->getRequest()->getParams();
			if(isset($param['btn-search'])){
				$search=$param;
			}else{
				$search = array(
                    'search' => '',
                    'supplierId' => '',
                    'productId' => '',
                    'isPaid' => -1,
					'status' => 0,
					'startDate' => date('Y-m-d'),
					'endDate' => date('Y-m-d'),
                );
			}
 			$db = new Section_Model_DbTable_DbPurchase();
            $this->view->rs= $db->getAllPurchase($search);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$form = new Application_Form_FrmCombineSearchGlobal();
		$forms = $form->FormSearchPurchase();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->formSearch = $form;

    }
   public function addAction()
    {
        if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
            $sms="INSERT_SUCCESS";
			try{
              
				$sms="INSERT_SUCCESS";
				$_dbmodel = new Section_Model_DbTable_DbPurchase();
			    $_dbmodel->addPurchase($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/section/purchase");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage(); exit();
			}		
		}

		$dbp = new Section_Model_DbTable_DbPurchase();
		$this->view->product= $dbp->getAllProductList();
	
		$form = new Section_Form_FrmPurchase();
		$formAdd = $form->FrmAddPurchase();
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    }
    public function editAction()
    {
        if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
            $sms="EDIT_SUCCESS";
			try{
				$sms="EDIT_SUCCESS";
				$_dbmodel = new Section_Model_DbTable_DbPurchase();
			    $_dbmodel->updatePurchase($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/section/purchase");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}		
		}

		$dbp = new Section_Model_DbTable_DbPurchase();
		$this->view->product= $dbp->getAllProductList();

        $id=$this->getRequest()->getParam("id");
		$db = new Section_Model_DbTable_DbPurchase();
		$rs=$db->getPurchaseById($id);
		if($rs['isPaid']==1 OR $rs['outstandingBalanceAfter'] < $rs['outstandingBalance']){
			Application_Form_FrmMessage::Sucessfull("Already Paid, Can't Edit!","/section/purchase");
		}
		$this->view->rs = $rs;
		$form = new Section_Form_FrmPurchase();
		$formAdd = $form->FrmAddPurchase($rs);
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    }
}







