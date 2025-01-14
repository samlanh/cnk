<?php

class Section_Form_FrmSupplier extends Zend_Dojo_Form
{
	protected  $tr;

    public function init()
    {
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();	
    }
    function FrmAddSupplier($data=array()){
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	$_dbuser = new Application_Model_DbTable_DbUsers();
    	$userid = $_dbgb->getUserId();
    	$userinfo = $_dbuser->getUserInformationById($userid);

		$search = new Zend_Form_Element_Text("search");
		$search->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("SEARCH"),
				'autocomplete'=>'off'
		));
    	
    	$supplierName = new Zend_Form_Element_Text("supplierName");
		$supplierName->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("SUPPLIER"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		$contactName = new Zend_Form_Element_Text("contactName");
		$contactName->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("CONTACT_NAME"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		
		$contactTel = new Zend_Form_Element_Text("contactTel");
		$contactTel->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("TEL"),
				'required'=>'required',
				'autocomplete'=>'off'
		));

		$status = new Zend_Form_Element_Select("status");
		$status->setAttribs(array(
				'class'=>'form-control-select',
				'required'=>'required'
		));
		$opt = array('1'=>$this->tr->translate("ACTIVE"),'0'=>$this->tr->translate("DEACTIVE"));
		$status->setMultiOptions($opt);

		$id = new Zend_Form_Element_Hidden("id");
    	
    	if(!empty($data)){
    		$supplierName->setValue($data["supplierName"]);
    		$contactName->setValue($data["contactName"]);
    		$contactTel->setValue($data["contactTel"]);
    		$status->setValue($data["status"]);
    		$id->setValue($data["id"]);
    	}
    	
    	$this->addElements(array(
				$search,
    			$supplierName,
				$contactName,
				$contactTel ,
				$status,
				$id
    			
    		));
    	return $this;
    }
}