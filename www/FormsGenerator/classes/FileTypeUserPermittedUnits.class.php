<?
class be_FileTypeUserPermittedUnits
{
	public $FileTypeUserPermittedUnitID;		//
	public $FileTypeUserPermissionID;		//کلید به جدول مجوز دسترسی کاربران به انوع پرونده
	public $ouid;		//واحد سازمانی
	public $UnitName;
	function be_FileTypeUserPermittedUnits() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute("select * from FileTypeUserPermittedUnits LEFT JOIN hrms_total.org_units using (ouid) where FileTypeUserPermittedUnitID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FileTypeUserPermittedUnitID=$rec["FileTypeUserPermittedUnitID"];
			$this->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$this->ouid=$rec["ouid"];
			$this->UnitName=$rec["ptitle"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FileTypeUserPermittedUnitID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کلید به جدول مجوز دسترسی کاربران به انوع پرونده </td><td>".$this->FileTypeUserPermissionID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>واحد سازمانی </td><td>".$this->ouid."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FileTypeUserPermittedUnits
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select count(FileTypeUserPermittedUnitID) as TotalCount from FileTypeUserPermittedUnits';
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
		$query = 'select max(FileTypeUserPermittedUnitID) as MaxID from FileTypeUserPermittedUnits';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FileTypeUserPermissionID, $ouid)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FileTypeUserPermittedUnits (FileTypeUserPermissionID
				, ouid
				) values ('".$FileTypeUserPermissionID."'
				, '".$ouid."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در واحدهای سازمانی مجاز برای دسترسی کاربر روی انواع پرونده با کد ".manage_FileTypeUserPermittedUnits::GetLastID());
	}
	static function Update($UpdateRecordID, $ouid)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FileTypeUserPermittedUnits set ouid='".$ouid."'
				where FileTypeUserPermittedUnitID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در واحدهای سازمانی مجاز برای دسترسی کاربر روی انواع پرونده");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "delete from FileTypeUserPermittedUnits where FileTypeUserPermittedUnitID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از واحدهای سازمانی مجاز برای دسترسی کاربر روی انواع پرونده");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FileTypeUserPermittedUnits LEFT JOIN hrms_total.org_units using (ouid) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FileTypeUserPermittedUnits();
			$ret[$k]->FileTypeUserPermittedUnitID=$rec["FileTypeUserPermittedUnitID"];
			$ret[$k]->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$ret[$k]->ouid=$rec["ouid"];
			$ret[$k]->UnitName = $rec["ptitle"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FileTypeUserPermittedUnits ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
