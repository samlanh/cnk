<?php 
Class Application_Form_FrmCombineSearchGlobal extends Zend_Dojo_Form {
	
	function FormSearchRevenue($search=null)
	{
		$frm = new Application_Form_FrmSearchGlobal();
		
		$controlSearch = $frm->controlTextSearch($search);
		$startDate = $frm->getStartDateSearch($search);
		$endDate = $frm->getEndDateSearch($search);
		$status = $frm->getStatusSearch($search);
		$search = array(
			"categoryType" => 2
		);
		$category = $frm->getInvCategorySearch($search);
		$bankId = $frm->getBankAccountSearch($search);
		
		$this->addElements(array(
			$controlSearch,
			$startDate,
			$endDate,
			$category,
			$bankId,
			$status,
		));
		return $this;
	}
	
	function FormSearchExpense($search=null)
	{
		$frm = new Application_Form_FrmSearchGlobal();
		
		$controlSearch = $frm->controlTextSearch($search);
		$startDate = $frm->getStartDateSearch($search);
		$endDate = $frm->getEndDateSearch($search);
		$status = $frm->getStatusSearch($search);
		$search = array(
			"categoryType" => 1
		);
		$category = $frm->getInvCategorySearch($search);
		$bankId = $frm->getBankAccountSearch($search);
		
		$this->addElements(array(
			$controlSearch,
			$startDate,
			$endDate,
			$category,
			$bankId,
			$status,
		));
		return $this;
	}
	function FormSearchPurchase($search=null)
	{
		$frm = new Application_Form_FrmSearchGlobal();
		
		$controlSearch = $frm->controlTextSearch($search);
		$startDate = $frm->getStartDateSearch($search);
		$endDate = $frm->getEndDateSearch($search);
		$status = $frm->getStatusSearch($search);

		$supplier = $frm->getSupllierSearch($search);
		$product = $frm->getProductSearch($search);
		$isPaid = $frm->getPaidStatusSearch($search);
		
		$this->addElements(array(
			$controlSearch,
			$startDate,
			$endDate,
			$status,
			$supplier,
			$product,
			$isPaid,
		));
		return $this;
	}
	function FormSearchPurchasePayment($search=null)
	{
		$frm = new Application_Form_FrmSearchGlobal();
		
		$controlSearch = $frm->controlTextSearch($search);
		$startDate = $frm->getStartDateSearch($search);
		$endDate = $frm->getEndDateSearch($search);
		$status = $frm->getStatusSearch($search);
		$supplier = $frm->getSupllierSearch($search);
		$isVoid = $frm->getIsVoidSearch($search);
		
		$this->addElements(array(
			$controlSearch,
			$startDate,
			$endDate,
			$status,
			$supplier,
			$isVoid,
		));
		return $this;
	}
}