<?php 
Class Application_Form_FrmSearchGlobal extends Zend_Dojo_Form {
	
	protected $tr;
	protected $db;
	protected $request;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->request=Zend_Controller_Front::getInstance()->getRequest();
		$this->db = new Application_Model_DbTable_DbGlobal();
	}

	public function controlTextSearch($_data=null){//used
		
		$advSearch = new Zend_Dojo_Form_Element_TextBox('advSearch');
		$advSearch->setAttribs(array(
				'class'=>'form-control',
				'placeholder'=>$this->tr->translate("SEARCH")));
		$advSearch->setValue($this->request->getParam("advSearch"));
		return $advSearch;
	}
	function getStartDateSearch($_data=null){
		$_startDateSearch=  new Zend_Dojo_Form_Element_TextBox('startDate');
		$_startDateSearch->setAttribs(array(
				'class'=>'form-control datepicker',
				'placeholder'=>$this->tr->translate("START_DATE"),
				'data-date-format' => 'yyyy-mm-dd',
		));
		$_date = $this->request->getParam("startDate");
		if(empty($_date)){
			$_date = date("d-m-Y");
		}
		$_date = date("d-m-Y",strtotime($_date));
		$_startDateSearch->setValue($_date);
		return $_startDateSearch;
	}
	function getEndDateSearch($_data=null){
		$_endDateSearch=  new Zend_Dojo_Form_Element_TextBox('endDate');
		$_endDateSearch->setAttribs(array(
				'class'=>'form-control datepicker',
				'placeholder'=>$this->tr->translate("END_DATE"),
		));
		$_date = $this->request->getParam("endDate");
		if(empty($_date)){
			$_date = date("d-m-Y");
		}
		$_date = date("d-m-Y",strtotime($_date));
		$_endDateSearch->setValue($_date);
		return $_endDateSearch;
	}
	function getStatusSearch($_data=null){
		$_statusSearch=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_statusSearch->setAttribs(array(
			'class'=>'form-control-select',
		));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DEACTIVE"));
		$_statusSearch->setMultiOptions($_status_opt);
		$_statusSearch->setValue($this->request->getParam("status"));

		return $_statusSearch;
	}
	public function getInvCategorySearch($_data=null){//used
		$_dbGb = new Application_Model_DbTable_DbGlobal();
		$categoryId = new Zend_Form_Element_Select("categoryId");
		$categoryId->setAttribs(array(
				'class'=>'form-control-select',
		));
		$categoryType = empty($_data["categoryType"]) ? 1 : $_data["categoryType"];
		$arrFilter = array(
			"categoryType"=>$categoryType
		);
		$rsCat = $_dbGb->getAllCategoryList($arrFilter);
		$optCate = array(''=>$this->tr->translate("CATEGORY"));
		if(!empty($rsCat)) foreach($rsCat AS $cat){
			$optCate[$cat['id']]=$cat['name'];
		}
		$categoryId->setMultiOptions($optCate);
		$categoryId->setValue($this->request->getParam('categoryId'));

		return $categoryId;
	}
	
	public function getBankAccountSearch($_data=null){//used
		$_dbGb = new Application_Model_DbTable_DbGlobal();
		$bankId = new Zend_Form_Element_Select("bankId");
		$bankId->setAttribs(array(
				'class'=>'form-control-select',
		));
		$rsCat = $_dbGb->getAllBankAccountList();
		$optCate = array(''=>$this->tr->translate("BANK"));
		if(!empty($rsCat)) foreach($rsCat AS $cat){
			$optCate[$cat['id']]=$cat['name'];
		}
		$bankId->setMultiOptions($optCate);
		$bankId->setValue($this->request->getParam('bankId'));
		return $bankId;
	}

	
	public function getSupllierSearch($_data=null){//used
		$supplierId = new Zend_Form_Element_Select("supplierId");
		$supplierId->setAttribs(array(
				'class'=>'form-control-select',
		));
		$dbSupplier = new Section_Model_DbTable_DbSupplier();
		$rsSupplier = $dbSupplier->getAllSupplierList();
		$optSup = array(
			''=>$this->tr->translate('SELECT_SUPPLIER'),
		);
		if(!empty($rsSupplier)) foreach($rsSupplier AS $rs){
			$optSup[$rs['id']]=$rs['name'];
		}
		$supplierId->setMultiOptions($optSup);
		return $supplierId;
	}
	
	public function getProductSearch($_data=null){//used
		$productId = new Zend_Form_Element_Select("productId");
		$productId->setAttribs(array(
				'class'=>'form-control-select',
		));

		$dbProduct = new Section_Model_DbTable_DbProduct();
		$rsProduct = $dbProduct->getAllProductList();
		$optSup = array(
			''=>$this->tr->translate('SELECT_PRODUCT'),
		);
		if(!empty($rsProduct)) foreach($rsProduct AS $rs){
			$optSup[$rs['id']]=$rs['name'];
		}
		$productId->setMultiOptions($optSup);
		return $productId;
	}
	
	function getPaidStatusSearch($_data=null){
		$isPaid=  new Zend_Dojo_Form_Element_FilteringSelect('isPaid');
		$isPaid->setAttribs(array(
			'class'=>'form-control-select',
		));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL"),
				1=>$this->tr->translate("PAID"),
				0=>$this->tr->translate("UNPAID"));
		$isPaid->setMultiOptions($_status_opt);
		$isPaid->setValue($this->request->getParam("isPaid"));
		return $isPaid;
	}
	function getIsVoidSearch($_data=null){
		$isVoid=  new Zend_Dojo_Form_Element_FilteringSelect('isVoid');
		$isVoid->setAttribs(array(
			'class'=>'form-control-select',
		));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL"),
				1=>$this->tr->translate("VOID"),
				0=>$this->tr->translate("NORMAL")
			);
		$isVoid->setMultiOptions($_status_opt);
		$isVoid->setValue($this->request->getParam("isVoid"));
		return $isVoid;
	}
}