<?
class be_FileTypeUserPermittedEduGroups
{
	public $FileTypeUserPermittedEduGroupID;		//
	public $FileTypeUserPermissionID;		//کلید به جدول مجوز دسترسی کاربران به انوع پرونده
	public $EduGrpCode;		//گروه آموزشی
	public $GroupName;

	function be_FileTypeUserPermittedEduGroups() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from FileTypeUserPermittedEduGroups JOIN EducationalGroups using (EduGrpCode) where FileTypeUserPermittedEduGroupID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FileTypeUserPermittedEduGroupID=$rec["FileTypeUserPermittedEduGroupID"];
			$this->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$this->EduGrpCode=$rec["EduGrpCode"];
			$this->GroupName=$rec["PEduName"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FileTypeUserPermittedEduGroupID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کلید به جدول مجوز دسترسی کاربران به انوع پرونده </td><td>".$this->FileTypeUserPermissionID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>گروه آموزشی </td><td>".$this->EduGrpCode."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FileTypeUserPermittedEduGroups
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(FileTypeUserPermittedEduGroupID) as TotalCount from FileTypeUserPermittedEduGroups';
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
		$query = 'select max(FileTypeUserPermittedEduGroupID) as MaxID from FileTypeUserPermittedEduGroups';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FileTypeUserPermissionID, $EduGrpCode)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into FileTypeUserPermittedEduGroups (FileTypeUserPermissionID
				, EduGrpCode
				) values ('".$FileTypeUserPermissionID."'
				, '".$EduGrpCode."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در گروه های آموزشی مجاز برای دسترسی کاربر روی انواع پرونده با کد ".manage_FileTypeUserPermittedEduGroups::GetLastID());
	}
	static function Update($UpdateRecordID, $EduGrpCode)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update FileTypeUserPermittedEduGroups set EduGrpCode='".$EduGrpCode."'
				where FileTypeUserPermittedEduGroupID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در گروه های آموزشی مجاز برای دسترسی کاربر روی انواع پرونده");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from FileTypeUserPermittedEduGroups where FileTypeUserPermittedEduGroupID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از گروه های آموزشی مجاز برای دسترسی کاربر روی انواع پرونده");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileTypeUserPermittedEduGroups  JOIN EducationalGroups using (EduGrpCode) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FileTypeUserPermittedEduGroups();
			$ret[$k]->FileTypeUserPermittedEduGroupID=$rec["FileTypeUserPermittedEduGroupID"];
			$ret[$k]->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$ret[$k]->EduGrpCode=$rec["EduGrpCode"];
			$ret[$k]->GroupName=$rec["PEduName"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileTypeUserPermittedEduGroups ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
