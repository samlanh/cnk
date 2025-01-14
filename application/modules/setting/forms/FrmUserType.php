<?php

class Setting_Form_FrmUserType extends Zend_Dojo_Form
{
	protected  $tr;

    public function init()
    {
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();	
    }
    function FrmAddUserType($data=array()){
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	
    	$_dbGb = new Application_Model_DbTable_DbGlobal();
    	$_dbuser = new Application_Model_DbTable_DbUsers();
    	$userId = $_dbGb->getUserId();
    	$userinfo = $_dbuser->getUserInformationById($userId);
    	
		
    	
		
		$title = new Zend_Form_Element_Text("title");
		$title->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("TITLE"),
				'required'=>'required',
				'autocomplete'=>'off'
		));
		
		$parent = new Zend_Form_Element_Select("parent");
		$parent->setAttribs(array(
				'class'=>'form-control-select',
		));
		$rsCat = $_dbGb->getAllUserType();
		$optCate = array(''=>$this->tr->translate('PARENT'),);
		if(!empty($rsCat)) foreach($rsCat AS $cat){
			$optCate[$cat['id']]=$cat['name'];
		}
		$parent->setMultiOptions($optCate);
    	
		$id = new Zend_Form_Element_hidden('id');
		
    	if(!empty($data)){
    		$title->setValue($data["userTypeTitle"]);
    		$parent->setValue($data["parent_id"]);
    		$id->setValue($data["userTypeId"]);
    	}
    	
    	$this->addElements(array(
    			$id,
    			$title,
    			$parent,
    			
    			));
    	return $this;
    }
}