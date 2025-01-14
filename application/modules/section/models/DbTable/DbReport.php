<?php class Section_Model_DbTable_DbReport extends Zend_Db_Table_Abstract{

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
			s.`supplierName`,
			p.`productName`,
			(SELECT u.user_name FROM `rms_users` AS u WHERE u.id = pc.`userId`) AS userName
			FROM ie_purchase AS pc
			LEFT JOIN `ie_supplier` AS s ON s.`id` = pc.`supplierId`
			LEFT JOIN `ie_product` AS p ON p.`id`= pc.`productId`

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
		if(!empty($search['productId'])){
			$where .=' AND pc.productId= '.$search['productId'];
		}
		if($search['isPaid'] > -1 ){
			$where .=' AND pc.isPaid= '.$search['isPaid'];
		}
		$order = ' ORDER BY pc.id DESC ';
		return $db->fetchAll($sql.$where.$order);
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
				,re.note
				,(SELECT cat.`title` FROM `ie_expense_category` AS cat WHERE cat.`categoryType`=2 AND cat.id = re.`categoryId` LIMIT 1) AS cateTitle
				,(SELECT bk.`bankName` FROM `ie_bankaccount` AS bk WHERE bk.id = re.bankId LIMIT 1) AS bankName
				,(SELECT bk.`accountName` FROM `ie_bankaccount` AS bk WHERE bk.id = re.bankId LIMIT 1) AS accountName
				,(SELECT bk.`accountNumber` FROM `ie_bankaccount` AS bk WHERE bk.id = re.bankId LIMIT 1) AS accountNumber
				,(SELECT u.user_name FROM `rms_users` AS u WHERE u.id = re.`userId`) AS userName
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

	function getAllExpense($search)
	{
		$db = $this->getAdapter();
		$sql = "
			SELECT 
				re.id
				,re.`title`
				,re.totalExpense AS `totalAmount`
				,re.expenseDate AS `forDate`
				,re.`createDate`
				,re.`modifyDate`
				,re.note
				,(SELECT cat.`title` FROM `ie_expense_category` AS cat WHERE cat.`categoryType`=1 AND cat.id = re.`categoryId` LIMIT 1) AS cateTitle
				,(SELECT bk.`bankName` FROM `ie_bankaccount` AS bk WHERE bk.id = re.bankId LIMIT 1) AS bankName
				,(SELECT bk.`accountName` FROM `ie_bankaccount` AS bk WHERE bk.id = re.bankId LIMIT 1) AS accountName
				,(SELECT bk.`accountNumber` FROM `ie_bankaccount` AS bk WHERE bk.id = re.bankId LIMIT 1) AS accountNumber
				,(SELECT u.user_name FROM `rms_users` AS u WHERE u.id = re.`userId`) AS userName
				,re.`status`
			";
	
		$sql .= " FROM `ie_expense` AS re  ";

		$where = ' WHERE 1 ';
		$startDate = (empty($search['startDate'])) ? '1' : "re.`expenseDate` >= '" . date('Y-m-d',strtotime($search['startDate'])) . " 00:00:00'";
		$endDate = (empty($search['endDate'])) ? '1' : "re.`expenseDate` <= '" . date('Y-m-d',strtotime($search['endDate'])) . " 23:59:59'";
		$where .= " AND " . $startDate . " AND " . $endDate;

		if (!empty($search['advSearch'])) {
			$s_where = array();
			$s_search = addslashes(trim($search['advSearch']));
			$s_where[] = " re.`title` LIKE '%{$s_search}%'";
			$s_where[] = " re.`totalExpense` LIKE '%{$s_search}%'";
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
	
}
