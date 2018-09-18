<?
class be_FormsDetailTables
{
	public $FormsDetailTableID;		//
	public $FormStructID;		//کد فرم اصلی
	public $DetailFormStructID;		//کد فرم/جدول جزییات
	public $RelatedField;		//نام فیلد ارتباطی جدول جزییات با کلید جدول اصلی
	public $OrderNo;		//شماره ترتیب جدول جزییات
	
	public $FormTitle; // عنوان جدول جزییات که از روی کد آن استخراج می شود

	function be_FormsDetailTables() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute("select * from FormsDetailTables
									LEFT JOIN FormsStruct  on (DetailFormStructID=FormsStructID) 
									where FormsDetailTableID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FormsDetailTableID=$rec["FormsDetailTableID"];
			$this->FormStructID=$rec["FormStructID"];
			$this->DetailFormStructID=$rec["DetailFormStructID"];
			$this->RelatedField=$rec["RelatedField"];
			$this->OrderNo=$rec["OrderNo"];
			$this->FormTitle=$rec["FormTitle"];
		}
	}
	
}
class manage_FormsDetailTables
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select count(FormsDetailTableID) as TotalCount from FormsDetailTables';
		if($WhereCondition!="")
		{
			$query .= ' where '.$WhereCondition;
		}
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	static function GetLastID()
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select max(FormsDetailTableID) as MaxID from FormsDetailTables';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormStructID, $DetailFormStructID, $RelatedField, $OrderNo)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FormsDetailTables (FormStructID
				, DetailFormStructID
				, RelatedField
				, OrderNo
				) values ('".$FormStructID."'
				, '".$DetailFormStructID."'
				, '".$RelatedField."'
				, '".$OrderNo."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ایجاد جدول جزییات [".manage_FormsDetailTables::GetLastID()."]");
	}
	static function Update($UpdateRecordID, $DetailFormStructID, $RelatedField, $OrderNo)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FormsDetailTables set DetailFormStructID='".$DetailFormStructID."'
				, RelatedField='".$RelatedField."'
				, OrderNo='".$OrderNo."'
				where FormsDetailTableID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروزرسانی جدول جزییات [".$UpdateRecordID."]");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "delete from FormsDetailTables where FormsDetailTableID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف جدول جزییات [".$RemoveRecordID."]");
	}
	static function GetList($MasterFormStructID)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormsDetailTables LEFT JOIN FormsStruct on (DetailFormStructID=FormsStructID) ";
		$query .= " where FormsDetailTables.FormStructID=".$MasterFormStructID." order by OrderNo";
		//echo $query;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FormsDetailTables();
			$ret[$k]->FormsDetailTableID=$rec["FormsDetailTableID"];
			$ret[$k]->FormStructID=$rec["FormStructID"];
			$ret[$k]->DetailFormStructID=$rec["DetailFormStructID"];
			$ret[$k]->RelatedField=$rec["RelatedField"];
			$ret[$k]->OrderNo=$rec["OrderNo"];
			$ret[$k]->FormTitle=$rec["FormTitle"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormsDetailTables ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
	
	// نوع دسترسی به جدول جزییات را در یک مرحله ثبت می کند
	static function SetFieldAccessType($FormsDetailTableID, $FormFlowStepID, $EditAccessType, $AddAccessType, $RemoveAccessType)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Execute("delete from DetailTablesAccessType where FormsDetailTableID='".$FormsDetailTableID."' and FormFlowStepID='".$FormFlowStepID."'");
		$mysql->Execute("insert into DetailTablesAccessType (FormsDetailTableID, FormFlowStepID, EditAccessType, AddAccessType, RemoveAccessType) values ('".$FormsDetailTableID."','".$FormFlowStepID."','".$EditAccessType."','".$AddAccessType."','".$RemoveAccessType."')");
	}
	
	// نوع دسترسی به جدول جزییات در یک مرحله را بر می گرداند
	// خروجی در یک آرایه برگشت داده می شود
	static function GetFieldAccessType($FormsDetailTableID, $FormFlowStepID)
	{
		$ret = array();
		// چنانچه دسترسی تعریف نشده باشد دسترسی کامل را برای هر سه مورد در نظر می گیرد
		$ret["AddAccessType"] = "ACCESS";
		$ret["EditAccessType"] = "ALL";
		$ret["RemoveAccessType"] = "ALL";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute("select * from DetailTablesAccessType where FormsDetailTableID='".$FormsDetailTableID."' and FormFlowStepID='".$FormFlowStepID."'");
		
		if($rec=$res->FetchRow())
		{
			$ret["AddAccessType"] = $rec["AddAccessType"];
			$ret["EditAccessType"] = $rec["EditAccessType"];
			$ret["RemoveAccessType"] = $rec["RemoveAccessType"];
		}
		return $ret;
	}
}
?>
