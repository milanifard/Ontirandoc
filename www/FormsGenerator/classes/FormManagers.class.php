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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("select * from FormManagers
								LEFT JOIN projectmanagement.persons using (PersonID) 
								where FormManagerID='".$RecID."' ");
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select max(FormManagerID) as MaxID from FormManagers';
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormsStructID, $PersonID, $AccessType)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FormManagers (FormsStructID
				, PersonID
				, AccessType
				) values ('".$FormsStructID."'
				, '".$PersonID."'
				, '".$AccessType."'
				)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("ثبت داده جدید در مدیران فرم با کد ".manage_FormManagers::GetLastID());
	}
	static function Update($UpdateRecordID, $PersonID, $AccessType)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FormManagers set PersonID='".$PersonID."', AccessType='".$AccessType."'
				where FormManagerID='".$UpdateRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در مدیران فرم");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = '';
		$query .= "delete from FormManagers where FormManagerID='".$RemoveRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از مدیران فرم");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormManagers LEFT JOIN projectmanagement.persons using (PersonID) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormManagers LEFT JOIN projectmanagement.persons using (PersonID)";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		return $res->fetchall();
	}
}
?>
