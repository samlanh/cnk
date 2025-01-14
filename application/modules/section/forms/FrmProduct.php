<?php

class Section_Form_FrmProduct extends Zend_Dojo_Form
{
	protected  $tr;

    public function init()
    {
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();	
    }
    function FrmAddProduct($data=array()){
    	
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
    	
    	$productName = new Zend_Form_Element_Text("productName");
		$productName->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("PRODUCT_NAME"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		$litterUnit = new Zend_Form_Element_Text("litterUnit");
		$litterUnit->setAttribs(array(
				'class'=>'form-control form-control-number',
				'placeholder'=>$this->tr->translate("LITER_UNIT"),
				'required'=>'required',
				'autocomplete'=>'off'
		));

		$id = new Zend_Form_Element_Hidden("id");
    	
    	if(!empty($data)){
    		$productName->setValue($data["productName"]);
    		$litterUnit->setValue($data["litterUnit"]);
    		$id->setValue($data["id"]);
    	}
    	
    	$this->addElements(array(
				$search,
    			$productName,
				$litterUnit,
				$id
    			
    		));
    	return $this;
    }
}