<?php

class Section_ProductionController extends Zend_Controller_Action
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
                    'advSearch' => '',
                    'proType' => '',
					'status' => -1,
					'startDate' => date('Y-m-d'),
					'endDate' => date('Y-m-d'),
                );
			}
			$db = new Section_Model_DbTable_DbProduction();
            $this->view->rs= $db->getAllProduct($search);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
        
        $form = new Section_Form_FrmProduct();
		$formAdd = $form->FrmAddProduct();
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);

		$form = new Application_Form_FrmCombineSearchGlobal();
		$forms = $form->FormSearchProduct();
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
				$_dbmodel = new Section_Model_DbTable_DbProduction();
			    $_dbmodel->addProduction($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/section/product");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}		
		}
		$form = new Section_Form_FrmProduct();
		$formAdd = $form->FrmAddProduction();
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
				$_dbmodel = new Section_Model_DbTable_DbProduction();
			    $_dbmodel->updateProduct($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/section/product");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}		
		}

        $id=$this->getRequest()->getParam("id");
		$db = new Section_Model_DbTable_DbProduction();
		$rs=$db->getProductById($id);
		$rsdetail=$db->getProductMaterialById($id);
		$this->view->rs = $rs;
		$this->view->rsdetail = $rsdetail;

		$form = new Section_Form_FrmProduct();
		$formAdd = $form->FrmAddProduct($rs);
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    }
	
}







