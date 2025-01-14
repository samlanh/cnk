<?php

class Setting_Form_FrmUser extends Zend_Dojo_Form
{
	protected  $tr;

    public function init()
    {
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();	
    }
    function FrmAddUser($data=array()){
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	
    	$_dbGb = new Application_Model_DbTable_DbGlobal();
    	$_dbuser = new Application_Model_DbTable_DbUsers();
    	$userId = $_dbGb->getUserId();
    	$userinfo = $_dbuser->getUserInformationById($userId);
    	
		
    	$firstName = new Zend_Form_Element_Text("firstName");
		$firstName->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("FIRST_NAME"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		
		$lastName = new Zend_Form_Element_Text("lastName");
		$lastName->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("LAST_NAME"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		
		$userName = new Zend_Form_Element_Text("userName");
		$userName->setAttribs(array(
				'class'=>'form-control',
				'onKeyup'=>'checkDuplicateUser();',
				'placeholder'=>$this->tr->translate("USER_NAME"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		
		$password = new Zend_Form_Element_Password("password");
		$password->setAttribs(array(
				'class'=>'control-password form-control be-0',
				'placeholder'=>$this->tr->translate("PASSWORD"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		
		$userType = new Zend_Form_Element_Select("userType");
		$userType->setAttribs(array(
				'class'=>'form-control-select',
		));
		$rsCat = $_dbGb->getAllUserType();
		$optCate = array();
		if(!empty($rsCat)) foreach($rsCat AS $cat){
			$optCate[$cat['id']]=$cat['name'];
		}
		$userType->setMultiOptions($optCate);
    	
		$id = new Zend_Form_Element_hidden('id');
		$oldUserName = new Zend_Form_Element_hidden('oldUserName');
		$isDuplicate = new Zend_Form_Element_hidden('isDuplicate');
		$isDuplicate->setValue(0);
		
    	if(!empty($data)){
			
    		$firstName->setValue($data["first_name"]);
    		$lastName->setValue($data["last_name"]);
    		$userName->setValue($data["user_name"]);
    		$oldUserName->setValue($data["user_name"]);
    		$userType->setValue($data["user_type"]);
    		$id->setValue($data["id"]);
    	}
    	
    	$this->addElements(array(
    			$id,
    			$firstName,
    			$lastName,
    			$userName,
    			$password,
    			$userType,
    			$isDuplicate,
    			$oldUserName,
    			
    			));
    	return $this;
    }
	
	 function FrmChangePassword($data=array()){
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	
    	$_dbGb = new Application_Model_DbTable_DbGlobal();
    	$_dbuser = new Application_Model_DbTable_DbUsers();
    	$userId = $_dbGb->getUserId();
    	$userinfo = $_dbuser->getUserInformationById($userId);
		
		$password = new Zend_Form_Element_Password("password");
		$password->setAttribs(array(
				'class'=>'control-password form-control be-0',
				'placeholder'=>$this->tr->translate("PASSWORD"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		
		$confirmPassword = new Zend_Form_Element_Password("confirmPassword");
		$confirmPassword->setAttribs(array(
				'class'=>'control-password form-control be-0',
				'placeholder'=>$this->tr->translate("CONFIRM_PASSWORD"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
    	$this->addElements(array(
    			
    			$password,
    			$confirmPassword,
    			
    			));
    	return $this;
    }
}