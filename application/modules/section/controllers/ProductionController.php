<?php

class Section_ProductionController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/section/production';
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
					'isVoid' => -1,
					'startDate'  => date('Y-m-d'),
					'endDate'    => date('Y-m-d'),
                );
			}
			$db = new Section_Model_DbTable_DbProduction();
            $this->view->rs= $db->getAllProduction($search);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}

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
				Application_Form_FrmMessage::Sucessfull($sms,"/section/production");
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
        
    }
	function voidAction(){
		$db = new Section_Model_DbTable_DbProduction();
		$id=$this->getRequest()->getParam('id');
		if (!empty($id)){
			try{
				$row = $db->getProductionById($id);
				if (empty($row)){
					Application_Form_FrmMessage::Sucessfull("គ្មានទិន្ន័យ",self::REDIRECT_URL."/index");
				}else if ($row['isVoid']==1){
					Application_Form_FrmMessage::Sucessfull("មោឃៈរួចរាល់ !",self::REDIRECT_URL."/index");
				}else{
					$db->VoidProdution($row);
					Application_Form_FrmMessage::Sucessfull("មោឃៈជោគជ័យ",self::REDIRECT_URL."/index");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Void Faile");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}else{
			Application_Form_FrmMessage::message("No Product to void! please check again.");
			$this->_redirect("/section/production");
	
		}
			
	}
	
}







