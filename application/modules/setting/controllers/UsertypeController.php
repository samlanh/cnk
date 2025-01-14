<?php

class Setting_UsertypeController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/setting/usertype';
    public function init()
    {    	
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	
	}

	public function indexAction()
    {
    	$dbDash = new Setting_Model_DbTable_DbUserType();
		$this->view->userTypeList = $dbDash->getAllUserType();
    }

   public function addAction()
    {
    	$dbRev = new Setting_Model_DbTable_DbUserType();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$sms="INSERT_SUCCESS";
				$id = $dbRev->createNewUserType($_data);
				Application_Form_FrmMessage::Sucessfull($sms,self::REDIRECT_URL);
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
			
		}
		
		$form = new Setting_Form_FrmUserType();
		$formAdd = $form->FrmAddUserType();
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    }
	
	public function editAction()
    {
    	$id=$this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		
		$dbRev = new Setting_Model_DbTable_DbUserType();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$sms="UPDATE_SUCCESS";
				$id = $dbRev->updateUserType($_data);
				Application_Form_FrmMessage::Sucessfull($sms,self::REDIRECT_URL);
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("UPDATE_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
			
		}
		
		$row = $dbRev->getUserTypeById($id);
		if (empty($row)) {
			Application_Form_FrmMessage::Sucessfull("NO_RECORD", self::REDIRECT_URL . "/index");
		}
		$this->view->row = $row;
		
		$form = new Setting_Form_FrmUserType();
		$formAdd = $form->FrmAddUserType($row);
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    }


}







