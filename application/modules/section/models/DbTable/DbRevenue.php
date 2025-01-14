<?php class Section_Model_DbTable_DbRevenue extends Zend_Db_Table_Abstract{

	protected $_name = 'rms_student_test';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    
	function getAllRevenue($search)
	{
		$db = $this->getAdapter();
		$sql = "
			SELECT 
				re.id
				,re.`revenueTitle`
				,re.`totalAmount`
				,re.`forDate`
				,re.`createDate`
				,re.`modifyDate`
				,(SELECT cat.`title` FROM `ie_expense_category` AS cat WHERE cat.`categoryType`=2 AND cat.id = re.`categoryId` LIMIT 1) AS cateTitle
				,(SELECT bk.`bankName` FROM `ie_bankaccount` AS bk WHERE bk.id = re.bankId LIMIT 1) AS bankName
				,(SELECT bk.`accountName` FROM `ie_bankaccount` AS bk WHERE bk.id = re.bankId LIMIT 1) AS accountName
				,(SELECT bk.`accountNumber` FROM `ie_bankaccount` AS bk WHERE bk.id = re.bankId LIMIT 1) AS accountNumber
				,re.`status`
			";
	
		$sql .= " FROM `ie_revenue` AS re  ";

		$where = ' WHERE 1 ';
		
		$startDate = (empty($search['startDate'])) ? '1' : "re.`forDate` >= '" . date('Y-m-d',strtotime($search['startDate'])) . " 00:00:00'";
		$endDate = (empty($search['endDate'])) ? '1' : "re.`forDate` <= '" . date('Y-m-d',strtotime($search['endDate'])) . " 23:59:59'";
		
		$where .= " AND " . $startDate . " AND " . $endDate;

		if (!empty($search['advSearch'])) {
			$s_where = array();
			$s_search = addslashes(trim($search['advSearch']));
			$s_where[] = " re.`revenueTitle` LIKE '%{$s_search}%'";
			$s_where[] = " re.`totalAmount` LIKE '%{$s_search}%'";
			$where .= ' AND (' . implode(' OR ', $s_where) . ')';
		}
		if (!empty($search['categoryId'])) {
			$where .= ' AND re.`categoryId`=' . $search['categoryId'];
		}
		if (!empty($search['bankId'])) {
			$where .= ' AND re.`bankId`=' . $search['bankId'];
		}
		$order =  ' ORDER BY re.`id` DESC ';
		return $db->fetchAll($sql . $where . $order);
	}
	
	public function createNewRevenue($_data){
		$_db= $this->getAdapter();		
		$_db->beginTransaction();
			try{
				$_dbGb = new Application_Model_DbTable_DbGlobal();
				$_data['bankId'] = empty($_data['bankId']) ? 0 : $_data['bankId'];
				$rs = $_dbGb->getBankBalanceInfo($_data);
				$totalBalanceBefore=0;
				$totalBalanceAfter=0;
				if(!empty($rs)){
					$totalBalanceBefore=$rs["totalBalance"];
					$transitionAmt = empty($_data["totalAmount"]) ? 0 : $_data["totalAmount"];
					$totalBalanceAfter = ((float) $rs["totalBalance"]) + ((float) $transitionAmt);
				}
				
				$_arr= array(
						'forDate'	=>date("Y-m-d",strtotime($_data['forDate'])),
						'revenueTitle'	=>$_data['revenueTitle'],
						'categoryId'	=>$_data['categoryId'],
						
						'totalAmount'	=>$_data['totalAmount'],
						'bankId'		=>$_data['bankId'],
						'note'			=>$_data['note'],
						'modifyDate'	=>date("Y-m-d H:i:s"),
						'createDate'	=>date("Y-m-d H:i:s"),
						'status'		=>1,
						'userId'				=>$this->getUserId(),
						
						'totalBalanceBefore'	=>$totalBalanceBefore,
						'totalBalanceAfter'		=>$totalBalanceAfter,
				);
				$this->_name = 'ie_revenue';
				$revenueId = $this->insert($_arr);
				
				$_dbGb->doBankAccountBalance($_data);
				
				$_db->commit();
			return $revenueId;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$_db->rollBack();
			
		}
	}
	
	public function getRevenueById($id)
	{
		$db = $this->getAdapter();
		$sql = "SELECT g.* FROM ie_revenue as g WHERE g.id = " . $db->quote($id);

		$sql .= " LIMIT 1";
		$row = $db->fetchRow($sql);
		return $row;
	}
	
	
	public function updateRevenue($_data){
		$_db= $this->getAdapter();		
		$_db->beginTransaction();
			try{
				$_dbGb = new Application_Model_DbTable_DbGlobal();
				$_data['bankId'] = empty($_data['bankId']) ? 0 : $_data['bankId'];
				
				$revenueId = empty($_data["id"]) ? 0 : $_data["id"];
				$status = empty($_data['status'])	? 0 : 1;
				
				//reverse balance
				$row = $this->getRevenueById($revenueId);
				if(!empty($row)){
					$arrReverse = array(
						'bankId'	=>$row['bankId'],
						'totalAmount'	=>"-".$row['totalAmount'],
					);
					$_dbGb->doBankAccountBalance($arrReverse);
				}
				
				if($status==1){
					$rs = $_dbGb->getBankBalanceInfo($_data);
					$totalBalanceBefore=0;
					$totalBalanceAfter=0;
					if(!empty($rs)){
						$totalBalanceBefore=$rs["totalBalance"];
						$transitionAmt = empty($_data["totalAmount"]) ? 0 : $_data["totalAmount"];
						$totalBalanceAfter = ((float) $rs["totalBalance"]) + ((float) $transitionAmt);
					}
					$_arr= array(
						'forDate'	=>date("Y-m-d",strtotime($_data['forDate'])),
						'revenueTitle'	=>$_data['revenueTitle'],
						'categoryId'	=>$_data['categoryId'],
						
						'totalAmount'	=>$_data['totalAmount'],
						'bankId'		=>$_data['bankId'],
						'note'			=>$_data['note'],
						'modifyDate'	=>date("Y-m-d H:i:s"),
						'status'		=>$status,
						'userId'				=>$this->getUserId(),
						
						'totalBalanceBefore'	=>$totalBalanceBefore,
						'totalBalanceAfter'		=>$totalBalanceAfter,
					);
					$this->_name = 'ie_revenue';
					$where = 'id = '.$revenueId;
					$this->update($_arr, $where);
					
					$_dbGb->doBankAccountBalance($_data);
				
				}else{
					$_arr= array(
						'modifyDate'	=>date("Y-m-d H:i:s"),
						'status'		=>$status,
						'userId'		=>$this->getUserId(),
					);
					$this->_name = 'ie_revenue';
					$where = 'id = '.$revenueId;
					$this->update($_arr, $where);
				}
				$_db->commit();
			return $revenueId;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$_db->rollBack();
			
		}
	}
}
