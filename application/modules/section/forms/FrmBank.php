<?php

class Section_Form_FrmBank extends Zend_Dojo_Form
{
	protected  $tr;

    public function init()
    {
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();	
    }
    function FrmAddBank($data=array()){
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	$_dbuser = new Application_Model_DbTable_DbUsers();
    	$userid = $_dbgb->getUserId();
    	$userinfo = $_dbuser->getUserInformationById($userid);

		$search = new Zend_Form_Element_Text("search");
		$search->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("SEARCH"),
		));
    	
    	$bankName = new Zend_Form_Element_Text("bankName");
		$bankName->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("BANK_NAME"),
				'required'=>'required'
		));
		$accountName = new Zend_Form_Element_Text("accountName");
		$accountName->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("ACCOUNT_NAME"),
				'required'=>'required'
		));
		
		$accountNumber = new Zend_Form_Element_Text("accountNumber");
		$accountNumber->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("ACCOUNT_NUMBER"),
				'required'=>'required'
		));
		$totalBalance = new Zend_Form_Element_Text("totalBalance");
		$totalBalance->setAttribs(array(
				'class'=>'form-control form-control-number',
				'placeholder'=>$this->tr->translate("TOTAL_BALANCE"),
				'required'=>'required'
		));

		$id = new Zend_Form_Element_Hidden("id");
		
    	
    	if(!empty($data)){
    		$bankName->setValue($data["bankName"]);
    		$accountName->setValue($data["accountName"]);
    		$accountNumber->setValue($data["accountNumber"]);
    		$totalBalance->setValue($data["totalBalance"]);
    		$id->setValue($data["id"]);
    	}
    	
    	$this->addElements(array(
				$search,
    			$bankName,
				$accountName,
				$accountNumber ,
				$totalBalance ,
				$id
    			
    		));
    	return $this;
    }
}