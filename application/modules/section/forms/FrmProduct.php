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
			$param= array(
				'type' => 2,
			);
		$rsProduct = $dbProduct->getAllMaterialList(	$param);
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

		$note = new Zend_Form_Element_Textarea("note");
		$note->setAttribs(array(
				'class'=>'form-control',
				'rows'=>'4',
				'placeholder'=>$this->tr->translate("NOTE"),
		));

		$id = new Zend_Form_Element_Hidden("id");
    	
    	if(!empty($data)){
    		$productName->setValue($data["productName"]);
    		$outstandingQty->setValue($data["outstandingQty"]);
    		$costPrice->setValue($data["costPrice"]);
    		$proType->setValue($data["proType"]);
    		$measure->setValue($data["measure"]);
    		$note->setValue($data["note"]);
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
				$note,
				$id
    			
    		));
    	return $this;
    }

	function FrmAddProduction($data=array()){
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	$_dbuser = new Application_Model_DbTable_DbUsers();
    	$userid = $_dbgb->getUserId();
    	$userinfo = $_dbuser->getUserInformationById($userid);

		$todayDate = date("d-m-Y");
		$productionDate = new Zend_Form_Element_Text("productionDate");
		$productionDate->setAttribs(array(
				'class'=>'form-control datepicker',
				'placeholder'=>$this->tr->translate("ថ្ងៃផលិត"),
				'required'=>'required'
		));
		$productionDate->setValue($todayDate);

		$note = new Zend_Form_Element_Textarea("note");
		$note->setAttribs(array(
				'class'=>'form-control',
				'rows'=>'4',
				'placeholder'=>$this->tr->translate("NOTE"),
		));
	
		$productId = new Zend_Form_Element_Select("productId");
		$productId->setAttribs(array(
				'class'=>'form-control-select',
				'onChange' => 'addRow()'
		));

		$dbProduct = new Section_Model_DbTable_DbProduct();
			$param= array(
				'type' => 1,
			);
		$rsProduct = $dbProduct->getAllMaterialList($param);
		$optSup = array(''=>$this->tr->translate("ជ្រើសរើសផលិតផល"),);
		if(!empty($rsProduct)) foreach($rsProduct AS $rs){
			$optSup[$rs['id']]=$rs['name'];
		}
		$productId->setMultiOptions($optSup);

		$counter = new Zend_Form_Element_Text("counter");
		$counter->setAttribs(array(
			'class'=>'form-control',
			'placeholder'=>$this->tr->translate("អ្នករាប់"),
			'autocomplete'=>'off'
		));

		$id = new Zend_Form_Element_Hidden("id");
    	
    	if(!empty($data)){
    	
    		$productionDate->setValue($data["productionDate"]);
    		$note->setValue($data["note"]);
    		$counter->setValue($data["counter"]);
    		$id->setValue($data["id"]);
    	}
    	
    	$this->addElements(array(
				$productionDate,
				$note ,
				$productId,
				$counter,
				$id
    			
    		));
    	return $this;
    }
}