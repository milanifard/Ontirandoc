<?
class be_FileTypeUserPermittedForms
{
	public $FileTypeUserPermittedFormID;		//
	public $FileTypeUserPermissionID;		//کد رکورد دسترسی کاربر
	public $FormsStructID;		//کد ساختار فرم مربوطه
	public $AddFormPermission;		//مجوز اضافه کردن فرم
	public $RemoveFormPermission;		//مجوز حذف فرم
	public $ViewFormPermission;		//مجوز مشاهده/ویرایش فرم
	public $FormTitle;

	function be_FileTypeUserPermittedForms() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from FileTypeUserPermittedForms JOIN FormsStruct using (FormsStructID) where FileTypeUserPermittedFormID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FileTypeUserPermittedFormID=$rec["FileTypeUserPermittedFormID"];
			$this->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$this->FormsStructID=$rec["FormsStructID"];
			$this->AddFormPermission=$rec["AddFormPermission"];
			$this->RemoveFormPermission=$rec["RemoveFormPermission"];
			$this->ViewFormPermission=$rec["ViewFormPermission"];
			$this->FormTitle=$rec["FormTitle"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FileTypeUserPermittedFormID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد رکورد دسترسی کاربر </td><td>".$this->FileTypeUserPermissionID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد ساختار فرم مربوطه </td><td>".$this->FormsStructID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز اضافه کردن فرم </td><td>".$this->AddFormPermission."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز حذف فرم </td><td>".$this->RemoveFormPermission."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مجوز مشاهده/ویرایش فرم </td><td>".$this->ViewFormPermission."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FileTypeUserPermittedForms
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(FileTypeUserPermittedFormID) as TotalCount from FileTypeUserPermittedForms';
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
		$query = 'select max(FileTypeUserPermittedFormID) as MaxID from FileTypeUserPermittedForms';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FileTypeUserPermissionID, $FormsStructID, $AddFormPermission, $RemoveFormPermission, $ViewFormPermission)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into FileTypeUserPermittedForms (FileTypeUserPermissionID
				, FormsStructID
				, AddFormPermission
				, RemoveFormPermission
				, ViewFormPermission
				) values ('".$FileTypeUserPermissionID."'
				, '".$FormsStructID."'
				, '".$AddFormPermission."'
				, '".$RemoveFormPermission."'
				, '".$ViewFormPermission."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در فرمهای مجاز و نحوه دسترسی به آنها در انواع پرونده با کد ".manage_FileTypeUserPermittedForms::GetLastID());
	}
	static function Update($UpdateRecordID, $FormsStructID, $AddFormPermission, $RemoveFormPermission, $ViewFormPermission)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update FileTypeUserPermittedForms set FormsStructID='".$FormsStructID."'
				, AddFormPermission='".$AddFormPermission."'
				, RemoveFormPermission='".$RemoveFormPermission."'
				, ViewFormPermission='".$ViewFormPermission."'
				where FileTypeUserPermittedFormID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در فرمهای مجاز و نحوه دسترسی به آنها در انواع پرونده");
	}
			static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from FileTypeUserPermittedForms where FileTypeUserPermittedFormID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از فرمهای مجاز و نحوه دسترسی به آنها در انواع پرونده");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileTypeUserPermittedForms  JOIN FormsStruct using (FormsStructID) JOIN FileTypeUserPermissions using (FileTypeUserPermissionID) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FileTypeUserPermittedForms();
			$ret[$k]->FileTypeUserPermittedFormID=$rec["FileTypeUserPermittedFormID"];
			$ret[$k]->FileTypeUserPermissionID=$rec["FileTypeUserPermissionID"];
			$ret[$k]->FormsStructID=$rec["FormsStructID"];
			$ret[$k]->AddFormPermission=$rec["AddFormPermission"];
			$ret[$k]->RemoveFormPermission=$rec["RemoveFormPermission"];
			$ret[$k]->ViewFormPermission=$rec["ViewFormPermission"];
			$ret[$k]->FormTitle=$rec["FormTitle"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileTypeUserPermittedForms ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
	
	// نوع دسترسی به فیلد را در یک دسترسی می کند
	static function SetFieldAccessType($FormFieldID, $FileTypeUserPermittedFormID, $AccessType)
	{
		$mysql = dbclass::getInstance();
		$mysql->Execute("delete from FileTypeUserPermittedFormDetails where FormFieldID='".$FormFieldID."' and FileTypeUserPermittedFormID='".$FileTypeUserPermittedFormID."'");
		$mysql->Execute("insert into FileTypeUserPermittedFormDetails (FormFieldID, FileTypeUserPermittedFormID, AccessType) values ('".$FormFieldID."','".$FileTypeUserPermittedFormID."','".$AccessType."')");
	}
	
	// نوع دسترسی به فیلد در یک دسترسی را بر می گرداند
	static function GetFieldAccessType($FormFieldID, $FileTypeUserPermittedFormID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from FileTypeUserPermittedFormDetails where FormFieldID='".$FormFieldID."' and FileTypeUserPermittedFormID='".$FileTypeUserPermittedFormID."'");
		// چنانچه دسترسی برای فیلد تعریف نشده باشد آن را خواندنی در نظر می گیرد
		if($rec=$res->FetchRow())
		{
			return $rec["AccessType"];
		}
		return "READ";
	}
	
}
?>
