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
			'placeholder'=>$this->tr->translate("ឈ្មោះទំនិញ/សម្ភារៈ"),
			'required'=>'required',
			'autocomplete'=>'off'
		));
		$outstandingQty = new Zend_Form_Element_Text("outstandingQty");
		$outstandingQty->setAttribs(array(
			'class'=>'form-control form-control-number',
			'placeholder'=>$this->tr->translate("ចំនួន"),
			'required'=>'required',
			'autocomplete'=>'off'
		));
		$costPrice = new Zend_Form_Element_Text("costPrice");
		$costPrice->setAttribs(array(
			'class'=>'form-control form-control-number',
			'placeholder'=>$this->tr->translate("ថ្លៃដើម"),
			'required'=>'required',
			'autocomplete'=>'off'
		));

		$proType = new Zend_Form_Element_Select("proType");
		$proType->setAttribs(array(
			'class'    =>'form-control-select',
			'required' =>'required',
			'onChange' =>'changeType()'
		));
		$opt = array(
			''=>$this->tr->translate("ជ្រើសរើសប្រភេទ"),
			'1'=>$this->tr->translate("ទំនិញលក់"),
			'2'=>$this->tr->translate("វត្ថុធាតុដើម"),
		);
		$proType->setMultiOptions($opt);
	
		$materialId = new Zend_Form_Element_Select("materialId");
		$materialId->setAttribs(array(
				'class'=>'form-control-select',
				'onChange' => 'addRow()'
		));

		$dbProduct = new Section_Model_DbTable_DbProduct();
		$rsProduct = $dbProduct->getAllMaterialList();
		$optSup = array(''=>$this->tr->translate("ជ្រើសរើសវត្ថុធាតុដើម"),);
		if(!empty($rsProduct)) foreach($rsProduct AS $rs){
			$optSup[$rs['id']]=$rs['name'];
		}
		$materialId->setMultiOptions($optSup);

		$measure = new Zend_Form_Element_Text("measure");
		$measure->setAttribs(array(
			'class'=>'form-control',
			'placeholder'=>$this->tr->translate("ខ្នាត"),
			'autocomplete'=>'off'
		));

		$id = new Zend_Form_Element_Hidden("id");
    	
    	if(!empty($data)){
    		$productName->setValue($data["productName"]);
    		$outstandingQty->setValue($data["outstandingQty"]);
    		$costPrice->setValue($data["costPrice"]);
    		$proType->setValue($data["proType"]);
    		$measure->setValue($data["measure"]);
    		$id->setValue($data["id"]);
    	}
    	
    	$this->addElements(array(
				$search,
    			$productName,
				$outstandingQty,
				$costPrice,
				$proType,
				$materialId,
				$measure,
				$id
    			
    		));
    	return $this;
    }
}