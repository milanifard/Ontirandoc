<?php 
/*
 صفحه  کلاس مدیریت امنیت مربوط به : جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-30
*/
class security_UniversitySessions
{
	// متدی که دسترسی برای یک فیلد روی یک رکورد را برای یک کاربر ذخیره می کند
	function SaveFieldPermission($RecID, $FieldName, $SelectedPersonID, $AccessType)
	{
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.PersonPermissionsOnFields (TableName, PersonID, RecID, FieldName, AccessType) values ('UniversitySessions', ?, ?, ?, ?)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($SelectedPersonID, $RecID, $FieldName, $AccessType));
	}

	// متدی که دسترسی برای یک جدول جزییات روی یک رکورد را برای یک کاربر ذخیره می کند
	function SaveDetailTablePermission($RecID, $DetailTableName, $SelectedPersonID, $AddAccessType, $RemoveAccessType, $UpdateAccessType, $ViewAccessType)
	{
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.PersonPermissionsOnTable (TableName, PersonID, RecID, DetailTableName, AddAccessType, RemoveAccessType, UpdateAccessType, ViewAccessType) values ('UniversitySessions', ?, ?, ?, ?, ?, ?, ?)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($SelectedPersonID, $RecID, $DetailTableName, $AddAccessType, $RemoveAccessType, $UpdateAccessType, $ViewAccessType));
	}

// متدی که دسترسی برای یک فیلد روی یک رکورد را برای یک کاربر برمی گرداند
	function ReadFieldPermission($RecID, $FieldName, $SelectedPersonID)
	{
		$mysql = pdodb::getInstance();
		$query = "select AccessType from sessionmanagement.PersonPermissionsOnFields where TableName='UniversitySessions' and PersonID=? and RecID=? and FieldName=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($SelectedPersonID, $RecID, $FieldName));
		if($rec=$res->fetch())
			return $rec["AccessType"];
		return "NONE";
	}

// متدی که دسترسی برای یک جدول جزییات روی یک رکورد را برای یک کاربر برمی گرداند
// AccessType: Add, Update, Remove, View
	function ReadDetailTablePermission($RecID, $DetailTableName, $SelectedPersonID, $AccessType)
	{
		$mysql = pdodb::getInstance();
		$query = "select ".$AccessType."AccessType from sessionmanagement.PersonPermissionsOnTable where TableName='UniversitySessions' and PersonID=? and RecID=? and DetailTableName=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($SelectedPersonID, $RecID, $DetailTableName));
		if($rec=$res->fetch())
			return $rec[$AccessType."AccessType"];
		return "NONE";
	}

// متدی که تنظیمات دسترسی کاربر به یک رکورد را حذف می کند
	function ResetRecordFieldsPermission($RecID, $SelectedPersonID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.PersonPermissionsOnFields where TableName='UniversitySessions' and PersonID=? and RecID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($SelectedPersonID, $RecID));
	}

// متدی که تنظیمات دسترسی کاربر به جداول جزییات یک رکورد را حذف می کند
	function ResetRecordDetailTablesPermission($RecID, $SelectedPersonID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.PersonPermissionsOnTable where TableName='UniversitySessions' and PersonID=? and RecID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($SelectedPersonID, $RecID));
	}

	static function LoadUserPermissions($PersonID, $RecID)
	{
		$ret = new PermissionsContainer();
		if(!is_numeric($RecID))
			return $ret;
		
		$mysql = pdodb::getInstance();
		
		$query = "select AccessType, FieldName from sessionmanagement.PersonPermissionsOnFields where TableName='UniversitySessions' and PersonID=? and RecID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID, $RecID));
		while($rec = $res->fetch())
		{
			$ret->Add($rec["FieldName"], $rec["AccessType"]);
			if($rec["AccessType"]=="WRITE")
				$ret->HasWriteAccessOnOneItemAtLeast=true;
		}
		$query = "select * from sessionmanagement.PersonPermissionsOnTable where TableName='UniversitySessions' and PersonID=? and RecID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID, $RecID));
		while($rec = $res->fetch())
		{
			$ret->Add("Add_".$rec["DetailTableName"], $rec["AddAccessType"]);
			$ret->Add("Remove_".$rec["DetailTableName"], $rec["RemoveAccessType"]);
			$ret->Add("Update_".$rec["DetailTableName"], $rec["UpdateAccessType"]);
			$ret->Add("View_".$rec["DetailTableName"], $rec["ViewAccessType"]);
		}
		return $ret;
	}
}
?>