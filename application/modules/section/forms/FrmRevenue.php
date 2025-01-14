<?php

class Section_Form_FrmRevenue extends Zend_Dojo_Form
{
	protected  $tr;

    public function init()
    {
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();	
    }
    function FrmAddRevenue($data=array()){
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	
    	$_dbGb = new Application_Model_DbTable_DbGlobal();
    	$_dbuser = new Application_Model_DbTable_DbUsers();
    	$userId = $_dbGb->getUserId();
    	$userinfo = $_dbuser->getUserInformationById($userId);
    	
		$todayDate = date("d-m-Y");
		$forDate = new Zend_Form_Element_Text("forDate");
		$forDate->setAttribs(array(
				'class'=>'form-control datepicker',
				'placeholder'=>$this->tr->translate("DATE"),
				'required'=>'required'
		));
		$forDate->setValue($todayDate);
		
    	$revenueTitle = new Zend_Form_Element_Text("revenueTitle");
		$revenueTitle->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("TITLE"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		
		$totalAmount = new Zend_Form_Element_Text("totalAmount");
		$totalAmount->setAttribs(array(
				'class'=>'form-control form-control-number text-success fw-bold',
				'placeholder'=>$this->tr->translate("TOTAL_AMOUNT"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		$totalAmount->setValue(0);
    	
		$note = new Zend_Form_Element_Textarea("note");
		$note->setAttribs(array(
				'class'=>'form-control',
				'rows'=>'4',
				'placeholder'=>$this->tr->translate("NOTE"),
		));
		
		$categoryId = new Zend_Form_Element_Select("categoryId");
		$categoryId->setAttribs(array(
				'class'=>'form-control-select',
		));
		$arrFilter = array(
			"categoryType"=>2
		);
		$rsCat = $_dbGb->getAllCategoryList($arrFilter);
		$optCate = array();
		if(!empty($rsCat)) foreach($rsCat AS $cat){
			$optCate[$cat['id']]=$cat['name'];
		}
		$categoryId->setMultiOptions($optCate);
		
		$bankId = new Zend_Form_Element_Select("bankId");
		$bankId->setAttribs(array(
				'class'=>'form-control-select',
		));
		$rsBank = $_dbGb->getAllBankAccountList();
		$optBank = array();
		if(!empty($rsBank)) foreach($rsBank AS $ba){
			$optBank[$ba['id']]=$ba['name'];
		}
		$bankId->setMultiOptions($optBank);
    	
		$id = new Zend_Form_Element_hidden('id');
    	if(!empty($data)){
			$forDate->setValue(date("d-m-Y",strtotime($data["forDate"])));
    		$revenueTitle->setValue($data["revenueTitle"]);
    		$totalAmount->setValue($data["totalAmount"]);
    		$note->setValue($data["note"]);
    		$categoryId->setValue($data["categoryId"]);
    		$bankId->setValue($data["bankId"]);
    		$id->setValue($data["id"]);
    	}
    	
    	$this->addElements(array(
    			$id,
    			$forDate,
    			$revenueTitle,
    			$totalAmount,
    			$note,
    			$categoryId,
    			$bankId,
    			
    			));
    	return $this;
    }
}