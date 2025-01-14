<?php

class Section_Form_FrmExpenseCategory extends Zend_Dojo_Form
{
	protected  $tr;

    public function init()
    {
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();	
    }
    function FrmAddExpenseCagetory($data=array()){
    	
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
    	
    	$title = new Zend_Form_Element_Text("title");
		$title->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("TITLE"),
				'required'=>'required'
		));
		
		$categoryType = new Zend_Form_Element_Select("categoryType");
		$categoryType->setAttribs(array(
				'class'=>'form-control-select',
				'required'=>'required'
		));
		$opt = array(
			''=>$this->tr->translate("SELECT_TYPE"),
			'1'=>$this->tr->translate("EXPENSE"),
			'2'=>$this->tr->translate("INCOME")
		);
		$categoryType->setMultiOptions($opt);

		$id = new Zend_Form_Element_Hidden("id");
    	
    	if(!empty($data)){
    		$title->setValue($data["title"]);
    		$categoryType ->setValue($data["categoryType"]);
			$categoryType->setAttribs(array(
				'disabled '=>'disabled ',
			));
    		$id->setValue($data["id"]);
    	}
    	
    	$this->addElements(array(
				$search,
    			$title,
				$categoryType,
				$id
    			
    		));
    	return $this;
    }
}