<?
class be_FileTypeUserPermittedSubUnits
{
	public $FileTypeUserPermittedSubUnitID;		//
	public $FileTypeUserPermissionID;		//کلید به جدول مجوز دسترسی کاربران به انوع پرونده
	public $UnitID;
	public $SubUnitID;		
	public $UnitName;
	public $SubUnitName; 

	function be_FileTypeUserPermittedSubUnits() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select *, org_units.ptitle as UnitName, org_sub_units.ptitle as SubUnitName from FileTypeUserPermittedSubUnits
								JOIN hrms_total.org_units on (UnitID=ouid) 
								JOIN hrms_total.org_sub_units on (SubUnitID=sub_ouid)
								where FileTypeUserPermittedUnitID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FileTypeUserPermittedUnitID=$rec["FileTypeUserPermittedSubUnitID"];
			$this->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$this->UnitID=$rec["UnitID"];
			$this->SubUnitID=$rec["SubUnitID"];
			$this->UnitName = $rec["UnitName"];
			$this->SubUnitName = $rec["SubUnitName"];
		}
	}
}
class manage_FileTypeUserPermittedSubUnits
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(FileTypeUserPermittedSubUnitID) as TotalCount from FileTypeUserPermittedSubUnits';
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
		$mysql = dbclass::getInstance();
		$query = 'select max(FileTypeUserPermittedSubUnitID) as MaxID from FileTypeUserPermittedSubUnits';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FileTypeUserPermissionID, $UnitID, $SubUnitID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into FileTypeUserPermittedSubUnits (FileTypeUserPermissionID
				, UniTID
				, SubUnitID
				) values ('".$FileTypeUserPermissionID."'
				, '".$UnitID."'
				, '".$SubUnitID."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در زیر واحدهای سازمانی مجاز برای دسترسی کاربر روی انواع پرونده با کد ".manage_FileTypeUserPermittedSubUnits::GetLastID());
	}
	static function Update($UpdateRecordID, $UnitID, $SubUnitID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update FileTypeUserPermittedSubUnits set SubUnitID='".$SubUnitID."', UnitID='".$UnitID."'
				where FileTypeUserPermittedSubUnitID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در زیر واحدهای سازمانی مجاز برای دسترسی کاربر روی انواع پرونده");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from FileTypeUserPermittedSubUnits where FileTypeUserPermittedSubUnitID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از زیر واحدهای سازمانی مجاز برای دسترسی کاربر روی انواع پرونده");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select *, org_units.ptitle as UnitName, org_sub_units.ptitle as SubUnitName from FileTypeUserPermittedSubUnits 
					JOIN hrms_total.org_units on (UnitID=ouid) 
								JOIN hrms_total.org_sub_units on (SubUnitID=sub_ouid)";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FileTypeUserPermittedSubUnits();
			$ret[$k]->FileTypeUserPermittedSubUnitID=$rec["FileTypeUserPermittedSubUnitID"];
			$ret[$k]->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$ret[$k]->UnitID=$rec["UnitID"];
			$ret[$k]->SubUnitID=$rec["SubUnitID"];
			$ret[$k]->UnitName=$rec["UnitName"];
			$ret[$k]->SubUnitName=$rec["SubUnitName"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileTypeUserPermittedSubUnits ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
