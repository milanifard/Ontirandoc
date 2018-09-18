<?
class be_FileTypeUserPermissions
{
	public $FileTypeUserPermissionID;		//
	public $FileTypeID;		//نوع پرونده
	public $PersonID;		//کد شخصی کاربر مجاز
	public $AccessRange;		//محدوده دسترسی کاربر بر اساس مکان (واحدهای سازمانی خاص/زیر واحدهای سازمانی خاص/گروه های آموزشی خاص)
	public $DefineAccessPermission;		//مجوز تعریف نحوه دسترسی
	public $AddPermission;		//مجوز اضافه کرن پرونده
	public $RemovePermission;		//مجوز حذف پرونده
	public $UpdatePermission;		//مجوز بروزرسانی مشخصات اصلی
	public $ContentUpdatePermission;		//مجوز بروزرسانی محتوای غیر فرمی پرونده
	public $ViewPermission;		//مجوز مشاهده پرونده
	public $TemporarySendPermission;		//مجوز ارسال موقت (امانت) یک پرونده

	public $PersonName; // نام فرد
	function be_FileTypeUserPermissions() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute("select * from FileTypeUserPermissions
													LEFT JOIN hrms_total.persons using (PersonID) 
													where FileTypeUserPermissionID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$this->FileTypeID=$rec["FileTypeID"];
			$this->PersonID=$rec["PersonID"];
			$this->AccessRange=$rec["AccessRange"];
			$this->DefineAccessPermission=$rec["DefineAccessPermission"];
			$this->AddPermission=$rec["AddPermission"];
			$this->RemovePermission=$rec["RemovePermission"];
			$this->UpdatePermission=$rec["UpdatePermission"];
			$this->ContentUpdatePermission=$rec["ContentUpdatePermission"];
			$this->ViewPermission=$rec["ViewPermission"];
			$this->TemporarySendPermission=$rec["TemporarySendPermission"];
			$this->PersonName = $rec["pfname"]." ".$rec["plname"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FileTypeUserPermissionID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>نوع پرونده </td><td>".$this->FileTypeID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد شخصی کاربر مجاز </td><td>".$this->PersonID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>محدوده دسترسی کاربر بر اساس مکان (واحدهای سازمانی خاص/زیر واحدهای سازمانی خاص/گروه های آموزشی خاص) </td><td>".$this->AccessRange."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز تعریف نحوه دسترسی </td><td>".$this->DefineAccessPermission."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز اضافه کرن پرونده </td><td>".$this->AddPermission."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز حذف پرونده </td><td>".$this->RemovePermission."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز بروزرسانی مشخصات اصلی </td><td>".$this->UpdatePermission."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز بروزرسانی محتوای غیر فرمی پرونده </td><td>".$this->ContentUpdatePermission."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز مشاهده پرونده </td><td>".$this->ViewPermission."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز ارسال موقت (امانت) یک پرونده </td><td>".$this->TemporarySendPermission."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FileTypeUserPermissions
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select count(FileTypeUserPermissionID) as TotalCount from FileTypeUserPermissions';
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
		$query = 'select max(FileTypeUserPermissionID) as MaxID from FileTypeUserPermissions';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FileTypeID, $PersonID, $AccessRange, $DefineAccessPermission, $AddPermission, $RemovePermission, $UpdatePermission, $ContentUpdatePermission, $ViewPermission, $TemporarySendPermission)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FileTypeUserPermissions (FileTypeID
				, PersonID
				, AccessRange
				, DefineAccessPermission
				, AddPermission
				, RemovePermission
				, UpdatePermission
				, ContentUpdatePermission
				, ViewPermission
				, TemporarySendPermission
				) values ('".$FileTypeID."'
				, '".$PersonID."'
				, '".$AccessRange."'
				, '".$DefineAccessPermission."'
				, '".$AddPermission."'
				, '".$RemovePermission."'
				, '".$UpdatePermission."'
				, '".$ContentUpdatePermission."'
				, '".$ViewPermission."'
				, '".$TemporarySendPermission."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در مجوزهای کاربران روی انواع پرونده با کد ".manage_FileTypeUserPermissions::GetLastID());
	}
	static function Update($UpdateRecordID, $PersonID, $AccessRange, $DefineAccessPermission, $AddPermission, $RemovePermission, $UpdatePermission, $ContentUpdatePermission, $ViewPermission, $TemporarySendPermission)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FileTypeUserPermissions set PersonID='".$PersonID."'
				, AccessRange='".$AccessRange."'
				, DefineAccessPermission='".$DefineAccessPermission."'
				, AddPermission='".$AddPermission."'
				, RemovePermission='".$RemovePermission."'
				, UpdatePermission='".$UpdatePermission."'
				, ContentUpdatePermission='".$ContentUpdatePermission."'
				, ViewPermission='".$ViewPermission."'
				, TemporarySendPermission='".$TemporarySendPermission."'
				where FileTypeUserPermissionID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در مجوزهای کاربران روی انواع پرونده");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "delete from FileTypeUserPermissions where FileTypeUserPermissionID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از مجوزهای کاربران روی انواع پرونده");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FileTypeUserPermissions LEFT JOIN hrms_total.persons using (PersonID) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FileTypeUserPermissions();
			$ret[$k]->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$ret[$k]->FileTypeID=$rec["FileTypeID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->AccessRange=$rec["AccessRange"];
			$ret[$k]->DefineAccessPermission=$rec["DefineAccessPermission"];
			$ret[$k]->AddPermission=$rec["AddPermission"];
			$ret[$k]->RemovePermission=$rec["RemovePermission"];
			$ret[$k]->UpdatePermission=$rec["UpdatePermission"];
			$ret[$k]->ContentUpdatePermission=$rec["ContentUpdatePermission"];
			$ret[$k]->ViewPermission=$rec["ViewPermission"];
			$ret[$k]->TemporarySendPermission=$rec["TemporarySendPermission"];
			$ret[$k]->PersonName=$rec["pfname"]." ".$rec["plname"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FileTypeUserPermissions ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
