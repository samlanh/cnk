<?php

class Section_PurchasepaymentController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/section/purchasepayment';
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
                    'search'     => '',
                    'supplierId' => '',
					'status' 	 => -1,
					'isVoid' 	 => -1,
					'startDate'  => date('Y-m-d'),
					'endDate'    => date('Y-m-d'),
                );
			}
 			$db = new Section_Model_DbTable_DbPurchasepayment();
            $this->view->rs= $db->getAllPurchasePayment($search);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$form = new Application_Form_FrmCombineSearchGlobal();
		$forms = $form->FormSearchPurchasePayment();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->formSearch = $form;

    }
   public function addAction()
    {
        if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			//print_r($_data); exit();
            $sms="INSERT_SUCCESS";
			try{
				$sms="INSERT_SUCCESS";
				$_dbmodel = new Section_Model_DbTable_DbPurchasepayment();
			    $_dbmodel->addPurchasePayment($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/section/purchasepayment");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage(); exit();
			}		
		}
	
		$form = new Section_Form_FrmPurchasepayment();
		$formAdd = $form->FrmAddPurchasePayment();
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    }
	function voidAction(){
		$db = new Section_Model_DbTable_DbPurchasepayment();
		$id=$this->getRequest()->getParam('id');
		if (!empty($id)){
			try{
				$row = $db->getPurchasePaymentById($id);
				//print_r($row);exit();
				if (empty($row)){
					Application_Form_FrmMessage::Sucessfull("No Record",self::REDIRECT_URL."/index");
				}else if ($row['isVoid']==1){
					Application_Form_FrmMessage::Sucessfull("This Record already void",self::REDIRECT_URL."/index");
				}else{
					$db->VoidPurchasePayment($row);
					Application_Form_FrmMessage::Sucessfull("Void Successfully",self::REDIRECT_URL."/index");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Void Payment Faile");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}else{
			Application_Form_FrmMessage::message("No Payment to void! please check again.");
			$this->_redirect("/section/purchasepayment");
	
		}
			
	}

	public function getPurchaseInfoAction(){
    	if($this->getRequest()->isPost()){
    		$post=$this->getRequest()->getPost();
    		$db = new Section_Model_DbTable_DbPurchasepayment();
    		$supplierId =  $post['supplierId'];
    		$result = $db->getAllPurchaseInfo($supplierId);
			print_r(Zend_Json::encode($result));
			exit();
    	}
    	
    }
}







