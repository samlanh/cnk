<?php

class Section_Form_FrmPurchase extends Zend_Dojo_Form
{
	protected  $tr;

    public function init()
    {
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();	
    }
    function FrmAddPurchase($data=array()){
    	
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

		$supplierId = new Zend_Form_Element_Select("supplierId");
		$supplierId->setAttribs(array(
				'class'=>'form-control-select',
		));
		$dbSupplier = new Section_Model_DbTable_DbSupplier();
		$rsSupplier = $dbSupplier->getAllSupplierList();
		$optSup = array();
		if(!empty($rsSupplier)) foreach($rsSupplier AS $rs){
			$optSup[$rs['id']]=$rs['name'];
		}
		$supplierId->setMultiOptions($optSup);

		$productId = new Zend_Form_Element_Select("productId");
		$productId->setAttribs(array(
				'class'=>'form-control-select',
		));

		$dbProduct = new Section_Model_DbTable_DbProduct();
		$rsProduct = $dbProduct->getAllProductList();
		$optSup = array();
		if(!empty($rsProduct)) foreach($rsProduct AS $rs){
			$optSup[$rs['id']]=$rs['name'];
		}
		$productId->setMultiOptions($optSup);

    	$invoiceNo = new Zend_Form_Element_Text("invoiceNo");
		$invoiceNo->setAttribs(array(
				'class' => 'form-control', // Base class
				'style' => 'color: red;',  // CSS to make the text red
				'placeholder' => $this->tr->translate("INVOICE_NO"),
				'required' => 'required',
		));
		$purchaseNo = new Zend_Form_Element_Text("purchaseNo");
		$purchaseNo->setAttribs(array(
				'class' => 'form-control', // Base class
				'style' => 'color: red;',  // CSS to make the text red
				'placeholder' => $this->tr->translate("PURCHASE_NO"),
				'required' => 'required',
				'readonly' => 'readonly'  
		));
		$dbPurchase = new Section_Model_DbTable_DbPurchase();
		$purcNo = $dbPurchase->getInvoiceNo();
		$purchaseNo->setValue($purcNo);

		$todayDate = date("d-m-Y");
		$poDate = new Zend_Form_Element_Text("poDate");
		$poDate->setAttribs(array(
				'class'=>'form-control datepicker',
				'placeholder'=>$this->tr->translate("DATE"),
				'required'=>'required'
		));
		$poDate->setValue($todayDate);
		
    	$qty = new Zend_Form_Element_Text("qty");
		$qty->setAttribs(array(
			'class'=>'form-control form-control-number',
			'placeholder'=>$this->tr->translate("QTY"),
			'required'=>'required',
			'autocomplete'=>'off',
			'onkeyup' => 'calculateTotalPrice()'
	
		));

		$unitPrice = new Zend_Form_Element_Text("unitPrice");
		$unitPrice->setAttribs(array(
			'class'=>'form-control form-control-number',
			'placeholder'=>$this->tr->translate("UNIT_PRICE"),
			'required'=>'required',
			'autocomplete'=>'off',
			 'onkeyup' => 'calculateTotalPrice()'
		));
		
		$totalPrice = new Zend_Form_Element_Text("totalPrice");
		$totalPrice->setAttribs(array(
			'class'=>'form-control form-control-number',
			'placeholder'=>$this->tr->translate("TOTAL_PRICE"),
			'readonly' => 'readonly' 
		));
	
		$litterPrice = new Zend_Form_Element_Text("litterPrice");
		$litterPrice->setAttribs(array(
			'class'=>'form-control form-control-number',
			'placeholder'=>$this->tr->translate("LIITER_PRICE"),
			'readonly' => 'readonly' 
		));

		$note = new Zend_Form_Element_Textarea("note");
		$note->setAttribs(array(
				'class'=>'form-control',
				'rows'=>'4',
				'placeholder'=>$this->tr->translate("NOTE"),
		));

		$id = new Zend_Form_Element_Hidden("id");
    	if(!empty($data)){
    		$supplierId->setValue($data["supplierId"]);
    		$productId->setValue($data["productId"]);
    		$litterPrice->setValue($data["litterPrice"]);
    		$totalPrice->setValue($data["totalPrice"]);
    		$unitPrice->setValue($data["unitPrice"]);
    		$qty->setValue($data["qty"]);
    		$invoiceNo->setValue($data["invoiceNo"]);
    		$purchaseNo->setValue($data["purchaseNo"]);
    		$poDate->setValue($data["poDate"]);
    		$note->setValue($data["note"]);
    		$id->setValue($data["id"]);
    	}
    	
    	$this->addElements(array(
				$search,
				$supplierId,
				$productId,
				$litterPrice,
				$totalPrice,
				$unitPrice,
				$qty,
				$invoiceNo,
				$purchaseNo,
				$poDate,
				$note ,
				$id
				
    		));
    	return $this;
    }
}