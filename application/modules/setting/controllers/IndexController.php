<?php

class Setting_IndexController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/setting';
    public function init()
    {    	
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	
	}

	public function indexAction()
    {
    	$dbDash = new Setting_Model_DbTable_DbUser();
		$this->view->userList = $dbDash->getAllUser();
    }

   public function addAction()
    {
    	
		$dbRev = new Setting_Model_DbTable_DbUser();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$sms="INSERT_SUCCESS";
				$id = $dbRev->createNewUser($_data);
				Application_Form_FrmMessage::Sucessfull($sms,self::REDIRECT_URL);
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
			
		}
		
		$form = new Setting_Form_FrmUser();
		$formAdd = $form->FrmAddUser();
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    }
	
	public function editAction()
    {
    	$id=$this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		
		$dbRev = new Setting_Model_DbTable_DbUser();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$sms="UPDATE_SUCCESS";
				$id = $dbRev->updateUser($_data);
				Application_Form_FrmMessage::Sucessfull($sms,self::REDIRECT_URL);
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("UPDATE_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
			
		}
		
		$row = $dbRev->getUserById($id);
		if (empty($row)) {
			Application_Form_FrmMessage::Sucessfull("NO_RECORD", self::REDIRECT_URL . "/index");
		}
		$this->view->row = $row;
		
		$form = new Setting_Form_FrmUser();
		$formAdd = $form->FrmAddUser($row);
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
    }
	
	function checkTitleAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Setting_Model_DbTable_DbUser();
			$return= $db->checkTitle($data);
			print_r(Zend_Json::encode($return));
			exit();
		}
	}
	
	
	function changepasswordAction(){
		
		$dbRev = new Setting_Model_DbTable_DbUser();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$sms="SUCCESS_CHANGE_PASSWORD";
				$id = $dbRev->changePassword($_data);
				Application_Form_FrmMessage::Sucessfull($sms,"/home");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("UPDATE_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$form = new Setting_Form_FrmUser();
		$formAdd = $form->FrmChangePassword();
		$this->view->frm = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
	}


}







