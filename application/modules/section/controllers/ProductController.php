<?php

class Section_ProductController extends Zend_Controller_Action
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
 			$db = new Section_Model_DbTable_DbProduct();
            $this->view->rs= $db->getAllProduct($search);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
        
        $form = new Section_Form_FrmProduct();
		$formAdd = $form->FrmAddProduct();
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
				$_dbmodel = new Section_Model_DbTable_DbProduct();
			    $_dbmodel->addProduct($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/section/product");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}		
		}
		$form = new Section_Form_FrmProduct();
		$formAdd = $form->FrmAddProduct();
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
				$_dbmodel = new Section_Model_DbTable_DbProduct();
			    $_dbmodel->updateProduct($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/section/product");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}		
		}

        $id=$this->getRequest()->getParam("id");
		$db = new Section_Model_DbTable_DbProduct();
		$rs=$db->getProductById($id);
		$rsdetail=$db->getProductMaterialById($id);
		$this->view->rs = $rs;
		$this->view->rsdetail = $rsdetail;

		$form = new Section_Form_FrmProduct();
		$formAdd = $form->FrmAddProduct($rs);
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    }
	public function getMaterialInfoAction(){
    	if($this->getRequest()->isPost()){
    		$post=$this->getRequest()->getPost();
			$db = new Section_Model_DbTable_DbProduct();
    		$materialId =  $post['materialId'];
    		$result = $db->getMaterialById($materialId);
			print_r(Zend_Json::encode($result));
			exit();
    	}
    	
    }
}







