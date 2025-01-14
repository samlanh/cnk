<?php

class Section_RevenueController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/section/revenue';
    public function init()
    {    	
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	
	}

    public function indexAction()
    {
    	try {
			$param = $this->getRequest()->getParams();
			if(isset($param['btn-search'])){
				$search=$param;
			}
			else{
				$search = array(
					'advSearch' 	=> '',
					'categoryId' => 0,
					'bankId' => 0,
					'status' => 0,
					'startDate' => date('Y-m-d'),
					'endDate' => date('Y-m-d'),
				);
			}
			
			//'startDate' => date('Y-m-d'),
			$this->view->search = $search;
			$db = new Section_Model_DbTable_DbRevenue();
			$rows = $db->getAllRevenue($search);
			$this->view->rows = $rows;
			
		} catch (Exception $e) {
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			Application_Form_FrmMessage::message("Application Error");
		}
		$form = new Application_Form_FrmCombineSearchGlobal();
		$forms = $form->FormSearchRevenue();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->formSearch = $form;
    }

	public function addAction()
    {
		$dbRev = new Section_Model_DbTable_DbRevenue();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$sms="INSERT_SUCCESS";
				$id = $dbRev->createNewRevenue($_data);
				Application_Form_FrmMessage::Sucessfull($sms,self::REDIRECT_URL);
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
			
		}
		$form = new Section_Form_FrmRevenue();
		$formAdd = $form->FrmAddRevenue();
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    	
    }
	
	public function editAction()
    {
		
		$id=$this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		
		$dbRev = new Section_Model_DbTable_DbRevenue();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$sms="UPDATE_SUCCESS";
				$id = $dbRev->updateRevenue($_data);
				Application_Form_FrmMessage::Sucessfull($sms,self::REDIRECT_URL);
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("UPDATE_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
			
		}
		
		$row = $dbRev->getRevenueById($id);
		if (empty($row)) {
			Application_Form_FrmMessage::Sucessfull("NO_RECORD", self::REDIRECT_URL . "/index");
		}
		if ($row["status"]==0) {
			Application_Form_FrmMessage::messageAlert("ALREADY_VOID", self::REDIRECT_URL . "/index");
		}
		$this->view->row = $row;
		
		$form = new Section_Form_FrmRevenue();
		$formAdd = $form->FrmAddRevenue($row);
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    	
    }


}







