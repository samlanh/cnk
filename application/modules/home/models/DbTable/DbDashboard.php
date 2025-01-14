<?php class Home_Model_DbTable_DbDashboard extends Zend_Db_Table_Abstract{

	protected $_name = 'ie_bankaccount';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    public function getBankAccountBalance($search=array()){
    	$db = $this->getAdapter();
    
    	$date=date("Y-m-d");
    	$sql="
		SELECT 
			bacc.*
			,COALESCE((SELECT SUM(re.totalAmount) FROM `ie_revenue` AS re WHERE re.bankId = bacc.`id` AND re.`forDate` = DATE_FORMAT('$date','%Y-%m-%d') LIMIT 1 ),0) AS totalIncomeToday
			,COALESCE((SELECT SUM(ex.`totalExpense`) FROM `ie_expense` AS ex WHERE ex.bankId = bacc.`id` AND ex.`expenseDate` = DATE_FORMAT('$date','%Y-%m-%d') LIMIT 1 ),0) AS totalExpenseToday
			,COALESCE((SELECT SUM(prPmt.`previusBalance`) FROM `ie_purchasepayment` AS prPmt WHERE prPmt.bankId = bacc.`id` AND prPmt.`expenseDate` = DATE_FORMAT('$date','%Y-%m-%d') LIMIT 1 ),0) AS totalPurPmtToday
		FROM `ie_bankaccount` AS bacc 
			WHERE 1
		";
    	$orderby = " ORDER BY bacc.bankName DESC ";
    	$where="";
    	if(!empty($search['advanceSearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['advance_search']));
    		$s_search = str_replace(' ', '', addslashes(trim($search['advance_search'])));
    		$s_where[] = " REPLACE(d.request_name,' ','') LIKE '%{$s_search}%'";
    		
    		$sql .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	return $db->fetchAll($sql.$where.$orderby);
    }
	public function getTotalDebtBySupplier($search=array()){
    	$db = $this->getAdapter();
		$sql="
		SELECT 
			sup.id AS supplierId
			,sup.`supplierName` AS supplierName
			,sup.`contactName`
			,COALESCE(SUM(pur.`outstandingBalanceAfter`),0) AS totalDebt
		FROM `ie_supplier` AS sup
			LEFT JOIN `ie_purchase` AS pur ON sup.id = pur.`supplierId` AND pur.`status` = 1 AND pur.`isPaid` = 0
		WHERE sup.`status` = 1
		";
		
    	$where="";
    	if(!empty($search['advanceSearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['advance_search']));
    		$s_search = str_replace(' ', '', addslashes(trim($search['advance_search'])));
    		$s_where[] = " REPLACE(sup.`supplierName`,' ','') LIKE '%{$s_search}%'";
    		
    		$sql .=' AND ( '.implode(' OR ',$s_where).')';
    	}
		$sql.=" GROUP BY sup.id ";
    	$orderby = " ORDER BY sup.id ASC ";
    	return $db->fetchAll($sql.$where.$orderby);
    }
	
	
    
}
