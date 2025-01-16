<?php class Section_Model_DbTable_DbPurchase extends Zend_Db_Table_Abstract{

	protected $_name = 'ie_purchase';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    function getAllPurchase($search){
		$db = $this->getAdapter();

		$startdatetimestamp = strtotime($search['startDate']);
		$startDateFormat = date('Y-m-d', $startdatetimestamp);

		$enddatetimestamp = strtotime($search['endDate']);
		$endDateFormat = date('Y-m-d', $enddatetimestamp);
		

		$sql = "SELECT pc.*,
			s.`supplierName`
			FROM ie_purchase AS pc
			LEFT JOIN `ie_supplier` AS s ON s.`id` = pc.`supplierId`

			WHERE 1 ";
		$where = '';
		$startDate = (empty($startDateFormat)) ? '1' : "pc.`poDate` >= '" . $startDateFormat . " 00:00:00'";
		$endDate = (empty($endDateFormat)) ? '1' : "pc.`poDate` <= '" . $endDateFormat . " 23:59:59'";
		$where .= " AND " . $startDate . " AND " . $endDate;
		if(!empty($search['advSearch'])){
			$s_where = array();
			$s_search = addslashes(trim($search['advSearch']));
			$s_where[] = " pc.invoiceNo LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		if(!empty($search['supplierId'])){
			$where .=' AND pc.supplierId= '.$search['supplierId'];
		}
		// if(!empty($search['productId'])){
		// 	$where .=' AND pc.productId= '.$search['productId'];
		// }
		if($search['isPaid'] > -1 ){
			$where .=' AND pc.isPaid= '.$search['isPaid'];
		}
		$order = ' ORDER BY pc.id DESC ';
		// echo $sql . $where . $order;
		// exit();
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
	public function addPurchase($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
	  		$arr = array(
				'supplierId'	=> $_data['supplierId'],
				'invoiceNo'		=> $_data['invoiceNo'],
				'purchaseNo'	=> $_data['purchaseNo'],
				'poDate'		=> date('Y-m-d', strtotime($_data['poDate'])),
				'totalAmount'	=> $_data['totalPrice'],
				'totalBalanceAfter'	 => $_data['totalPrice'],
				'note'			=> $_data['note'],
				'createDate' 	=> date("Y-m-d"),
				'status'		=> 1,
				'userId'		=> $this->getUserId()
	  		);
			
			$this->_name="ie_purchase";
			$poId = $this->insert($arr);

			$this->_name="ie_purchase_detail";
			$ids = explode(',',$_data['selectedIndexes']);
			if (!empty($ids)) {
				foreach($ids as $id){
					$arr = array(
							'purchaseId'=>$poId,
							'productId'=>$_data['id_'.$id],
							'qty'=>$_data['quantity_'.$id],
							'unitPrice'=>$_data['unitPrice_'.$id],
							'totalPrice'=>$_data['totalPrice_'.$id],
	
					);
					$this->insert($arr);
				}
			}
			$db->commit();

		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	public function updatePurchase($_data){
		$db = $this->getAdapter();
		$status = empty($_data["status"]) ?0:1;	
		try{
	  		$arr = array(
	  			'supplierId'	=> $_data['supplierId'],
				'productId'		=> $_data['productId'],
				'invoiceNo'		=> $_data['invoiceNo'],
				'purchaseNo'	=> $_data['purchaseNo'],
				'poDate'		=> date('Y-m-d', strtotime($_data['poDate'])),
				'qty'			=> $_data['qty'],
				'unitPrice'		=> $_data['unitPrice'],
				'totalPrice'	=> $_data['totalPrice'],
				'litterPrice'	=> $_data['litterPrice'],
				'outstandingBalance'	 => $_data['totalPrice'],
				'outstandingBalanceAfter'=> $_data['totalPrice'],
				'status'		=> $status,
				'note'			=> $_data['note'],
				'modifyDate' 	=> date("Y-m-d"),
				'userId'		=> $this->getUserId()
	  		);
			$where=$this->getAdapter()->quoteInto("id=?", $_data["id"]);
			$this->_name="ie_purchase";
			$this->update($arr,$where);
		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}

	function getPurchaseById($id){
		$db = $this->getAdapter();
		$sql = "SELECT  * FROM `ie_purchase`  WHERE 1 AND id= ".$id;
		return $db->fetchRow($sql);
	}
	function getPurchaseDetailById($id){
		$db = $this->getAdapter();
		$sql = "SELECT  
					pd.productId,
					pd.qty,
					pd.unitPrice,
					pd.totalPrice, 
			(SELECT p.productName FROM `ie_product` p WHERE p.id=pd.productId LIMIT 1) as productName
		FROM `ie_purchase_detail` pd  WHERE pd.purchaseId= ".$id;
		return $db->fetchAll($sql);
	}
	
}
