<?php

class Section_Form_FrmPurchasepayment extends Zend_Dojo_Form
{
	protected  $tr;

    public function init()
    {
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();	
    }
    function FrmAddPurchasePayment($data=array()){
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	$_dbuser = new Application_Model_DbTable_DbUsers();
    	$userid = $_dbgb->getUserId();
    	$userinfo = $_dbuser->getUserInformationById($userid);

	
		$supplierId = new Zend_Form_Element_Select("supplierId");
		$supplierId->setAttribs(array(
				'class'=>'form-control-select',
				'onChange' => 'getPurchaseInfo()'
		));
		$dbSupplier = new Section_Model_DbTable_DbSupplier();
		$rsSupplier = $dbSupplier->getAllSupplierList();
		$optSup = array(
			''=> $this->tr->translate('Select Supplier'),
		);
		if(!empty($rsSupplier)) foreach($rsSupplier AS $rs){
			$optSup[$rs['id']]=$rs['name'];
		}
		$supplierId->setMultiOptions($optSup);


		$todayDate = date("d-m-Y");
		$expenseDate = new Zend_Form_Element_Text("expenseDate");
		$expenseDate->setAttribs(array(
				'class'=>'form-control datepicker',
				'placeholder'=>$this->tr->translate("DATE"),
				'required'=>'required'
		));
		$expenseDate->setValue($todayDate);
		
    	$previusBalance = new Zend_Form_Element_Text("previusBalance");
		$previusBalance->setAttribs(array(
			'class'=>'form-control form-control-number',
			'placeholder'=>$this->tr->translate("Previus Balance"),
			'readonly' => 'readonly' ,
			'style' => 'font-weight: bold; color: red;'
		));

		$totalExpense = new Zend_Form_Element_Text("totalExpense");
		$totalExpense->setAttribs(array(
			'class'=>'form-control form-control-number',
			'placeholder'=>$this->tr->translate("Total Exspense"),
			'readonly' => 'readonly' ,
			'style' => 'font-weight: bold; color: red;'
		));
		
		$outstandingBalance = new Zend_Form_Element_Text("outstandingBalance");
		$outstandingBalance->setAttribs(array(
			'class'=>'form-control form-control-number',
			'placeholder'=>$this->tr->translate("Outstanding Balance"),
			'readonly' => 'readonly' ,
			'style' => 'font-weight: bold; color: red;'
		));

		$note = new Zend_Form_Element_Textarea("note");
		$note->setAttribs(array(
				'class'=>'form-control',
				'rows'=>'4',
				'placeholder'=>$this->tr->translate("NOTE"),
		));

		$bankId = new Zend_Form_Element_Select("bankId");
		$bankId->setAttribs(array(
				'class'=>'form-control-select',
		));
		$rsBank = $_dbgb->getAllBankAccountList();
		$optBank = array(
			''=> $this->tr->translate('Select Bank'),
		);
		if(!empty($rsBank)) foreach($rsBank AS $ba){
			$optBank[$ba['id']]=$ba['name'];
		}
		$bankId->setMultiOptions($optBank);
		$id = new Zend_Form_Element_Hidden("id");
    	if(!empty($data)){
    		
    	}
    	
    	$this->addElements(array(
				$supplierId,
				$previusBalance,
				$totalExpense,
				$outstandingBalance,
				$bankId,
				$expenseDate,
				$note ,
				$id
    		));
    	return $this;
    }
}