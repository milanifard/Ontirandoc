<?php 
/*
 صفحه  کلاس مدیریت امنیت مربوط به : پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-15
*/
class security_projects
{
	// متدی که دسترسی برای یک فیلد روی یک رکورد را برای یک کاربر ذخیره می کند
	function SaveFieldPermission($RecID, $FieldName, $SelectedPersonID, $AccessType)
	{
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.PersonPermissionsOnFields (TableName, PersonID, RecID, FieldName, AccessType) values ('projects', ?, ?, ?, ?)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($SelectedPersonID, $RecID, $FieldName, $AccessType));
	}

	// متدی که دسترسی برای یک جدول جزییات روی یک رکورد را برای یک کاربر ذخیره می کند
	function SaveDetailTablePermission($RecID, $DetailTableName, $SelectedPersonID, $AddAccessType, $RemoveAccessType, $UpdateAccessType, $ViewAccessType)
	{
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.PersonPermissionsOnTable (TableName, PersonID, RecID, DetailTableName, AddAccessType, RemoveAccessType, UpdateAccessType, ViewAccessType) values ('projects', ?, ?, ?, ?, ?, ?, ?)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($SelectedPersonID, $RecID, $DetailTableName, $AddAccessType, $RemoveAccessType, $UpdateAccessType, $ViewAccessType));
	}

// متدی که دسترسی برای یک فیلد روی یک رکورد را برای یک کاربر برمی گرداند
	function ReadFieldPermission($RecID, $FieldName, $SelectedPersonID)
	{
		$mysql = pdodb::getInstance();
		$query = "select AccessType from projectmanagement.PersonPermissionsOnFields where TableName='projects' and PersonID=? and RecID=? and FieldName=?";
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
		$query = "select ".$AccessType."AccessType from projectmanagement.PersonPermissionsOnTable where TableName='projects' and PersonID=? and RecID=? and DetailTableName=?";
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
		$query = "delete from projectmanagement.PersonPermissionsOnFields where TableName='projects' and PersonID=? and RecID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($SelectedPersonID, $RecID));
	}

// متدی که تنظیمات دسترسی کاربر به جداول جزییات یک رکورد را حذف می کند
	function ResetRecordDetailTablesPermission($RecID, $SelectedPersonID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.PersonPermissionsOnTable where TableName='projects' and PersonID=? and RecID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($SelectedPersonID, $RecID));
	}

	static function LoadUserPermissions($PersonID, $RecID)
	{
		//if($PersonID=="201309")
		//	return security_projects::SetPermissionToFullControl();
		$ret = new PermissionsContainer();
		if(!is_numeric($RecID))
			return $ret;
		if($RecID==0)
			return security_projects::SetPermissionToFullControl();
		$mysql = pdodb::getInstance();

		// کنترل می کند اگر فرد مدیر سطح بالای آن واحد بود - مجاز به مدیریت کل پروژه های آن واحد - کنترل دسترسی را کامل می دهد
		$query = "select * from projectmanagement.projects 
									JOIN projectmanagement.UserProjectScopes on (projects.ouid=PermittedUnitID) 
									JOIN projectmanagement.AccountSpecs on (WebUserID=UserProjectScopes.UserID) 
									where ProjectID=? and PersonID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($RecID, $PersonID));
		if($rec = $res->fetch())
		{
			return security_projects::SetPermissionToFullControl();
		}
		
		$query = "select AccessType, FieldName from projectmanagement.PersonPermissionsOnFields where TableName='projects' and PersonID=? and RecID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID, $RecID));
		while($rec = $res->fetch())
		{
			$ret->Add($rec["FieldName"], $rec["AccessType"]);
			if($rec["AccessType"]=="WRITE")
				$ret->HasWriteAccessOnOneItemAtLeast=true;
		}
		$query = "select * from projectmanagement.PersonPermissionsOnTable where TableName='projects' and PersonID=? and RecID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID, $RecID));
		//echo $query;
		//echo "<br>".$PersonID."<br>".$RecID."<br>";
		while($rec = $res->fetch())
		{
			$rec["DetailTableName"]."<br>";
			$ret->Add("Add_".$rec["DetailTableName"], $rec["AddAccessType"]);
			$ret->Add("Remove_".$rec["DetailTableName"], $rec["RemoveAccessType"]);
			$ret->Add("Update_".$rec["DetailTableName"], $rec["UpdateAccessType"]);
			$ret->Add("View_".$rec["DetailTableName"], $rec["ViewAccessType"]);
		}
		return $ret;
	}
	
	static function SetPermissionToFullControl()
	{
		$ret = new PermissionsContainer();
		$ret->HasWriteAccessOnOneItemAtLeast = true;
		$ret->Add("ouid", "WRITE");
		$ret->Add("ProjectGroupID", "WRITE");
		$ret->Add("title", "WRITE");
		$ret->Add("description", "WRITE");
		$ret->Add("StartTime", "WRITE");
		$ret->Add("EndTime", "WRITE");
		$ret->Add("SysCode", "WRITE");
		$ret->Add("ProjectPriority", "WRITE");
		$ret->Add("ProjectStatus", "WRITE");
		$ret->Add("Add_ProjectDocumentTypes", "YES");
		$ret->Add("Remove_ProjectDocumentTypes", "PUBLIC");
		$ret->Add("Update_ProjectDocumentTypes", "PUBLIC");
		$ret->Add("View_ProjectDocumentTypes", "PUBLIC");				

		$ret->Add("Add_ProjectDocuments", "YES");
		$ret->Add("Remove_ProjectDocuments", "PUBLIC");
		$ret->Add("Update_ProjectDocuments", "PUBLIC");
		$ret->Add("View_ProjectDocuments", "PUBLIC");				
		
		$ret->Add("Add_ProjectMembers", "YES");
		$ret->Add("Remove_ProjectMembers", "PUBLIC");
		$ret->Add("Update_ProjectMembers", "PUBLIC");
		$ret->Add("View_ProjectMembers", "PUBLIC");				

		$ret->Add("Add_ProjectExternalMembers", "YES");
		$ret->Add("Remove_ProjectExternalMembers", "PUBLIC");
		$ret->Add("Update_ProjectExternalMembers", "PUBLIC");
		$ret->Add("View_ProjectExternalMembers", "PUBLIC");				
		
		$ret->Add("Add_ProjectMilestones", "YES");
		$ret->Add("Remove_ProjectMilestones", "PUBLIC");
		$ret->Add("Update_ProjectMilestones", "PUBLIC");
		$ret->Add("View_ProjectMilestones", "PUBLIC");				

		$ret->Add("Add_ProjectTaskActivityTypes", "YES");
		$ret->Add("Remove_ProjectTaskActivityTypes", "PUBLIC");
		$ret->Add("Update_ProjectTaskActivityTypes", "PUBLIC");
		$ret->Add("View_ProjectTaskActivityTypes", "PUBLIC");				

		$ret->Add("Add_ProjectTaskTypes", "YES");
		$ret->Add("Remove_ProjectTaskTypes", "PUBLIC");
		$ret->Add("Update_ProjectTaskTypes", "PUBLIC");
		$ret->Add("View_ProjectTaskTypes", "PUBLIC");		

		$ret->Add("Add_ProjectTaskGroups", "YES");
		$ret->Add("Remove_ProjectTaskGroups", "PUBLIC");
		$ret->Add("Update_ProjectTaskGroups", "PUBLIC");
		$ret->Add("View_ProjectTaskGroups", "PUBLIC");		
		
		return $ret;		
	}
}
?>
