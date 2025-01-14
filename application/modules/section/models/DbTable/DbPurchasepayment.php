<?php class Section_Model_DbTable_DbPurchasepayment extends Zend_Db_Table_Abstract{

	protected $_name = 'ie_purchase';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    function getAllPurchasePayment($search){
		$db = $this->getAdapter();
		$startdatetimestamp = strtotime($search['startDate']);
		$startDateFormat = date('Y-m-d', $startdatetimestamp);

		$enddatetimestamp = strtotime($search['endDate']);
		$endDateFormat = date('Y-m-d', $enddatetimestamp);
		

		$sql = "SELECT pp.*,
			s.`supplierName`,
			b.bankName,
			b.accountNumber,
			b.accountName
			FROM ie_purchasepayment AS pp
			LEFT JOIN `ie_supplier` AS s ON s.`id` = pp.`supplierId`
			LEFT JOIN `ie_bankaccount` AS b ON b.`id` = pp.`bankId`
			WHERE 1 ";
		$where = '';
		$startDate = (empty($startDateFormat)) ? '1' : "pp.`expenseDate` >= '" . $startDateFormat . " 00:00:00'";
		$endDate = (empty($endDateFormat)) ? '1' : "pp.`expenseDate` <= '" . $endDateFormat . " 23:59:59'";
		$where .= " AND " . $startDate . " AND " . $endDate;
		if(!empty($search['advSearch'])){
			$s_where = array();
			$s_search = addslashes(trim($search['advSearch']));
			$s_where[] = " s.`supplierName` LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		if(!empty($search['supplierId'])){
			$where .=' AND pp.supplierId= '.$search['supplierId'];
		}
		
		if($search['isVoid'] > -1 ){
			$where .=' AND pp.isVoid= '.$search['isVoid'];
		}
		if($search['status'] > -1 ){
			$where .=' AND pp.status= '.$search['status'];
		}
		$order = ' ORDER BY pp.id DESC ';
		//echo $sql.$where.$order;//exit();
		return $db->fetchAll($sql.$where.$order);
	}
	public function getInvoiceNo(){
    	$db = $this->getAdapter();
    	$sql="SELECT count(id)  FROM ie_purchase where 1 LIMIT 1 ";
    	$invoiceNo = $db->fetchOne($sql);
		$invNo = $invoiceNo +1;
		$pre="N-";
		$lenghtFormat = 4;
    	$acc_length = strlen((int)$invNo+1);
    	for($i = $acc_length;$i<$lenghtFormat;$i++){
    		$pre.='0';
    	}
    	return $pre.$invNo;
    }
	public function addPurchasePayment($_data){
		$db = $this->getAdapter();
		try{
		//	print_r($_data);
			//Insert Purchase Payment
			$previusBalance = floatval(str_replace(',', '', $_data['previusBalance']));
			$totalExpense = floatval(str_replace(',', '', $_data['totalExpense']));
			$outstandingBalance = floatval(str_replace(',', '', $_data['outstandingBalance']));
			$_dbGb = new Application_Model_DbTable_DbGlobal();
	  		$arr = array(
				'supplierId'		=> $_data['supplierId'],
				'purchaseIdList'	=> $_data['purchaseIds'],
				'previusBalance'	=> $previusBalance,
				'expenseDate'		=> date('Y-m-d', strtotime($_data['expenseDate'])),
				'totalExpense'		=> $totalExpense, 
				'outstandingBalance'=> $outstandingBalance,
				'bankId'			=> $_data['bankId'],
				'note'				=> $_data['note'],
				'createDate' 		=> date("Y-m-d"),
				'status'			=> 1,
				'userId'			=> $this->getUserId()
	  		);
			$this->_name="ie_purchasepayment";
			$this->insert($arr);

			//Update Bank Balance
			$_data['bankId'] = empty($_data['bankId']) ? 0 : $_data['bankId'];
			$_data["totalAmount"] = empty($totalExpense) ? 0 : "-".$totalExpense;
			$_dbGb->doBankAccountBalance($_data);

			//update purchase
			if($_data['rowIndex']!=""){
				$ids=explode(',',$_data['rowIndex']);
				foreach ($ids as $i)
				{
					$isPaid = 1;
					$outstandingBalanceAfter = floatval(str_replace(',', '', $_data['totalbalance_'.$i]));
					if($outstandingBalanceAfter >0 ){
						$isPaid = 0;
					}
					$dataPurchase= array(
						'outstandingBalanceAfter'=>$outstandingBalanceAfter,
						'isPaid' 	=>$isPaid,
					);
					$this->_name="ie_purchase";
					$where=" id = ".$_data['puchaseid_'.$i];
					$this->update($dataPurchase, $where);
				 }
			}

		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}

	public function VoidPurchasePayment($_data){
		//print_r($_data);exit();
		$db = $this->getAdapter();
		$_dbGb = new Application_Model_DbTable_DbGlobal();
		try{
	  		$arr = array(
				'isVoid'	=> 1,
				'userId'	=> $this->getUserId()
	  		);
			$where=$this->getAdapter()->quoteInto("id=?", $_data["id"]);
			$this->_name="ie_purchasepayment";
			$this->update($arr,$where);

			//Reverse Bank
			$arrReverse = array(
				'bankId'		=>$_data['bankId'],
				'totalAmount'	=>$_data['totalExpense'],
			);
			$_dbGb->doBankAccountBalance($arrReverse);

			if($_data['purchaseIdList']!=""){
				$ids=explode(',',$_data['purchaseIdList']);
				foreach ($ids as $purchaseId)
				{
					$dataPurchase= array(
						
						'isPaid' 	=>0,
					);
					$this->_name="ie_purchase";
					$where=" id = ".$purchaseId;
					$this->update($dataPurchase, $where);

					$connection = $this->getAdapter();
					$sql = " UPDATE ie_purchase
							SET isPaid = 0, outstandingBalanceAfter = totalPrice
							WHERE id= ".$purchaseId;
					$result = $connection -> query($sql);
				 }
			}

		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}

	function getPurchasePaymentById($id){
		$db = $this->getAdapter();
		$sql = "SELECT  * FROM `ie_purchasepayment`  WHERE 1 AND id = ".$id;
		return $db->fetchRow($sql);
	}
	function getAllPurchaseInfo($supplierId){
		$db = $this->getAdapter();
		$sql = "SELECT pc.*,
			FORMAT(pc.totalPrice,2) AS purchasePrice,
			FORMAT(pc.outstandingBalanceAfter,2) AS totalbalance,
			s.`supplierName`,
			p.`productName`
			FROM ie_purchase AS pc
			LEFT JOIN `ie_supplier` AS s ON s.`id` = pc.`supplierId`
			LEFT JOIN `ie_product` AS p ON p.`id`= pc.`productId`
			WHERE 1 AND pc.status=1 AND isPaid=0 AND pc.supplierId= ".$supplierId;
		$order = ' ORDER BY pc.id DESC ';
		return $db->fetchAll($sql.$order);
	}
}
