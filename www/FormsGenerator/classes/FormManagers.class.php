<?
class be_FormManager
{
	public $FormManagerID;		//
	public $FormsStructID;		//کد فرم
	public $PersonID;		//
	public $AccessType; // نوع دسترسی 

	public $PersonName;
	
	function be_FormManager() {}

	
	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute("select * from FormManagers
								LEFT JOIN hrmstotal.persons using (PersonID) 
								where FormManagerID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FormManagerID=$rec["FormManagerID"];
			$this->FormsStructID=$rec["FormsStructID"];
			$this->PersonID=$rec["PersonID"];
			$this->AccessType=$rec["AccessType"];
			$this->PersonName=$rec["pfname"]." ".$rec["plname"];
		}
	}
}
class manage_FormManagers
{
	static function GetLastID()
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select max(FormManagerID) as MaxID from FormManagers';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormsStructID, $PersonID, $AccessType)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FormManagers (FormsStructID
				, PersonID
				, AccessType
				) values ('".$FormsStructID."'
				, '".$PersonID."'
				, '".$AccessType."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در مدیران فرم با کد ".manage_FormManagers::GetLastID());
	}
	static function Update($UpdateRecordID, $PersonID, $AccessType)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FormManagers set PersonID='".$PersonID."', AccessType='".$AccessType."'
				where FormManagerID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در مدیران فرم");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from FormManagers where FormManagerID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از مدیران فرم");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormManagers LEFT JOIN hrmstotal.persons using (PersonID) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FormManager();
			$ret[$k]->FormManagerID=$rec["FormManagerID"];
			$ret[$k]->FormsStructID=$rec["FormsStructID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonName=$rec["pfname"]." ".$rec["plname"];
			$ret[$k]->AccessType=$rec["AccessType"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormManagers LEFT JOIN hrmstotal.persons using (PersonID)";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
