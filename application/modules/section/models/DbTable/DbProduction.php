<?php class Section_Model_DbTable_DbProduction extends Zend_Db_Table_Abstract{

	protected $_name = 'ie_product';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    function getAllProduction($search){
		$db = $this->getAdapter();
		
		$startdatetimestamp = strtotime($search['startDate']);
		$startDateFormat = date('Y-m-d', $startdatetimestamp);

		$enddatetimestamp = strtotime($search['endDate']);
		$endDateFormat = date('Y-m-d', $enddatetimestamp);
	
		$sql = "SELECT *
		 FROM `ie_production`  WHERE 1 ";
		$where = '';
		$startDate = (empty($startDateFormat)) ? '1' : "productionDate >= '" . $startDateFormat . " 00:00:00'";
		$endDate = (empty($endDateFormat)) ? '1' : "productionDate <= '" . $endDateFormat . " 23:59:59'";
		$where .= " AND " . $startDate . " AND " . $endDate;
		
		if(!empty($search['advSearch'])){
			$s_where = array();
			$s_search = addslashes(trim($search['advSearch']));
			$s_where[] = " counter LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		$order = ' ORDER BY id DESC ';
		return $db->fetchAll($sql.$where.$order);
	}
	public function addProduction($_data){
		
		$db = $this->getAdapter();
		try{
	  		$arr = array(
				'productionDate' =>date('Y-m-d', strtotime($_data['productionDate'])),
				'counter'		 => $_data['counter'],
				'note'			 => $_data['note'],
				'createDate' 	 => date("Y-m-d"),
				'status'		 => 1,
				'userId'		 => $this->getUserId()
	  		);
			$this->_name="ie_production";
			$id = $this->insert($arr);
			//
			if($_data['selectedIndexes']!=""){
				$ids=explode(',',$_data['selectedIndexes']);
				foreach ($ids as $i)
				{
					$arrMaterial = array(
						'productionId'	 => $id,
						'productId' => $_data['id_'.$i],
						'quantity'   => $_data['quantity_'.$i],
					);
					$this->_name="ie_production_detail";
					$this->insert($arrMaterial);

					//update material qty

					$param = array(
						'productId' => $_data['id_'.$i],
						'quantity'   => $_data['quantity_'.$i],
					);
					$this->updateMaterialQty(	$param);
				 }
			}
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function updateMaterialQty($_data){
		$db = $this->getAdapter();
		$dbp= new Section_Model_DbTable_DbProduct;
		
		$rs= $dbp->getMaterialById($_data['productId']);
		$productQty = $_data['quantity'] + $rs['outstandingQty'];

		// update Product
		$arr = array(
			'outstandingQty'=> $productQty,
		);
		$where=$this->getAdapter()->quoteInto("id=?", $_data['productId']);
		$this->_name="ie_product";
		$this->update($arr,$where);

		// Update Material
		$rsDetail= $dbp->getProductMaterialById($_data['productId']);
		if(!empty($rsDetail)){ 
			foreach($rsDetail as $row){
				$rsMaterial= $dbp->getMaterialById($row['materialId']);

				$proQty = $row['usageQty']*$_data['quantity'];
				$newMaterialQty = $rsMaterial['outstandingQty'] - $proQty;

				$arr = array(
					'outstandingQty'=> $newMaterialQty,
				);
				$where=$this->getAdapter()->quoteInto("id=?", $row['materialId']);
				$this->_name="ie_product";
				$this->update($arr,$where);
			}
		} 

	}
}
