<?php 
/*
 صفحه  کلاس مدیریت امنیت مربوط به : کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
class security_ProjectTasks
{
	public static $Exceptions = array(200522, 201309, 401366284, 401371457, 401367373, 542); // حسینی سنو - میلانی - مهدی‌‌پور - شریعتی - حسینی - بی‌مکر

	// مشخص می کند نوع دسترسی کاربر به کار به چه صورتی است
	// مقداری که برگشت داده می شد:
	// NONE: کاربر به این کار دسترسی ندارد
	// EXECUTOR: کاربر مجری کار است
	// VIEWER: کاربر ناظر کار است
	// MANAGER: کاربر مدیر کار است
	// PMMANAGER: کاربر کارشناس مدیریت فرآیند است
	// INTERNAL_OWNER: کاربر مالک کار است
	// EXTERNAL_OWNER: کاربر مالک کار است ولی عضوی از پروژه نیست
	static function GetPersonRoleOnATask($PersonID, $ProjectTaskID)
	{
		$ret = "NONE";
		if (in_array($_SESSION["PersonID"], self::$Exceptions)) $ret = "PMMANAGER";

		$mysql = pdodb::getInstance();
		$query = "select ProjectTasks.*, ProjectMembers.ProjectmemberID,ProjectMembers.AccessType  from projectmanagement.ProjectTasks
					LEFT JOIN projectmanagement.ProjectMembers on (ProjectMembers.ProjectID=ProjectTasks.ProjectID and ProjectMembers.PersonID=ProjectTasks.CreatorID)  
					where ProjectTaskID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectTaskID));
		if($rec = $res->fetch())
		{
			$ProjectID = $rec["ProjectID"];
			if($rec["CreatorID"]==$PersonID && $rec["AccessType"]!='PMMANAGER')
			{
				// اگر کار مربوط به یک پروژه مشخص بود و ایجاد کننده هم عضوی از آن پروژه نبود
				if($rec["ProjectID"]!="0" && $rec["ProjectmemberID"]=="")
					$ret = "EXTERNAL_OWNER";
				else
					$ret = "INTERNAL_OWNER";
			}	
			$query = "select * from projectmanagement.ProjectMembers where ProjectID=? and PersonID=?";
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array($ProjectID, $PersonID));
			if($rec = $res->fetch())
			{
				if($rec["AccessType"]=="MANAGER") // اگر فرد جزو مدیران پروژه باشد به همه کارهای آن پروژه دسترسی مدیریتی دارد
					return "MANAGER";
				else if($ret=="INTERNAL_OWNER" || $ret=="EXTERNAL_OWNER")
					return $ret;
				else if($rec["AccessType"]=="VIEWER") //  اگر شخص ناظر پروژه باشد به همه کارها دسترسی نظارت دارد مگر کارهایی که به او انتساب یافته باشد
					$ret = "VIEWER";
				else if($rec["AccessType"]=="PMMANAGER") //  
					return "PMMANAGER";

			}
			else
			{
				if($ret=="INTERNAL_OWNER" || $ret=="EXTERNAL_OWNER")
					return $ret;
			}
			$query = "select * from projectmanagement.ProjectTaskAssignedUsers where ProjectTaskID=? and PersonID=?";
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array($ProjectTaskID, $PersonID));
			if($rec = $res->fetch())
			{
				return $rec["AssignType"]; // EXECUTOR/VIEWER
			}
			if($rec["ControllerID"]==$PersonID)
				$ret = "CONTROLLER";
			
		}

		if ($ret == "NONE")
		{
			$query = "select case when count(*) > 0 then 'Yes' else 'No' end as Referred from projectmanagement.ProjectTaskRefers where TaskID = ? and (FromPerson = ? or ToPerson = ?);";
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array($ProjectTaskID, $PersonID, $PersonID));

			if($rec = $res->fetch())
				if ($rec["Referred"] == 'Yes')
					return "VIEWER";
		}

		return $ret;		
	}
	
	static function LoadUserPermissions($PersonID, $ProjectTaskID)
	{
		require_once("ProjectTasks.class.php");
		$ret = new PermissionsContainer();
		if(!is_numeric($ProjectTaskID))
			return $ret;
		$task = new be_ProjectTasks();
		$task->LoadDataFromDatabase($ProjectTaskID);
		
		if($ProjectTaskID>0)
			$PersonRole = security_ProjectTasks::GetPersonRoleOnATask($PersonID, $ProjectTaskID);
		else
		{
			$ret->Add("CreatorID", "WRITE");
			$PersonRole = "MANAGER";
		}
/*if($_SESSION["UserID"]=="gholami-a"){
echo $PersonRole;
}*/
		if($PersonRole=="MANAGER")
		{		
			$ret->HasWriteAccessOnOneItemAtLeast=true;
			$ret->Add("ProjectID", "WRITE");
			$ret->Add("ControllerID", "WRITE");
			$ret->Add("ProgramLevelID", "WRITE");
			$ret->Add("ProjectTaskTypeID", "WRITE");
			$ret->Add("TaskGroupID", "WRITE");
			$ret->Add("title", "WRITE");
			$ret->Add("Letter", "WRITE");
			$ret->Add("description", "WRITE");
			$ret->Add("PeriodType", "WRITE");
			$ret->Add("CountOfDone", "WRITE");
			$ret->Add("EstimatedStartTime", "WRITE");
			$ret->Add("RealStartTime", "WRITE");
			$ret->Add("EstimatedRequiredTimeDay", "WRITE");
			$ret->Add("EstimatedRequiredTimeHour", "WRITE");
			$ret->Add("EstimatedRequitedTimeMin", "WRITE");
			$ret->Add("HasExpireTime", "WRITE");
			$ret->Add("ExpireTime", "WRITE");
			$ret->Add("TaskPeriority", "WRITE");
			$ret->Add("TaskStatus", "WRITE");
			$ret->Add("ParentID", "WRITE");
 			$ret->Add("DoneDate", "WRITE");
			$ret->Add("StartTime", "WRITE");
			$ret->Add("EndTime", "WRITE");
			$ret->Add("study", "WRITE");

			$ret->Add("Add_ProjectTaskActivities", "YES");
			$ret->Add("Remove_ProjectTaskActivities", "PUBLIC");
			$ret->Add("Update_ProjectTaskActivities", "PUBLIC");
			$ret->Add("View_ProjectTaskActivities", "PUBLIC");
			$ret->Add("Add_ProjectTaskAssignedUsers", "YES");
			$ret->Add("Remove_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Update_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("View_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Add_ProjectTaskComments", "YES");
			$ret->Add("Remove_ProjectTaskComments", "PUBLIC");
			$ret->Add("Update_ProjectTaskComments", "PUBLIC");
			$ret->Add("View_ProjectTaskComments", "PUBLIC");
			$ret->Add("Add_ProjectTaskDocuments", "YES");
			$ret->Add("Remove_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("Update_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("View_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("Add_ProjectTaskHistory", "YES");
			$ret->Add("Remove_ProjectTaskHistory", "PUBLIC");
			$ret->Add("Update_ProjectTaskHistory", "PUBLIC");
			$ret->Add("View_ProjectTaskHistory", "PUBLIC");
			$ret->Add("Add_ProjectTaskRequisites", "YES");
			$ret->Add("Remove_ProjectTaskRequisites", "PUBLIC");
			$ret->Add("Update_ProjectTaskRequisites", "PUBLIC");
			$ret->Add("View_ProjectTaskRequisites", "PUBLIC");
			$ret->Add("View_TaskRefers", "YES");
			$ret->Add("Add_TaskRefers", "YES");
			$ret->Add("Undo_TaskRefers", "YES");
		}
		else if($PersonRole=="INTERNAL_OWNER")
		{
			if($task->TaskStatus=="FINISHED")
			{
				$ret->HasWriteAccessOnOneItemAtLeast=false;
				$ret->Add("ControllerID", "READ");
				$ret->Add("ProjectID", "READ");
				$ret->Add("ProgramLevelID", "READ");
				$ret->Add("ProjectTaskTypeID", "READ");
				$ret->Add("TaskGroupID", "READ");
				$ret->Add("title", "READ");
				$ret->Add("Letter", "READ");
				$ret->Add("description", "READ");
				$ret->Add("PeriodType", "READ");
				$ret->Add("CountOfDone", "READ");
				$ret->Add("EstimatedStartTime", "READ");
				$ret->Add("RealStartTime", "READ");
				$ret->Add("EstimatedRequiredTimeDay", "READ");
				$ret->Add("EstimatedRequiredTimeHour", "READ");
				$ret->Add("EstimatedRequitedTimeMin", "READ");
				$ret->Add("HasExpireTime", "READ");
				$ret->Add("ExpireTime", "READ");
				$ret->Add("TaskPeriority", "READ");
				$ret->Add("TaskStatus", "READ");
				$ret->Add("ParentID", "READ");
 				$ret->Add("DoneDate","READ");
				$ret->Add("StartTime","READ");
				$ret->Add("EndTime","READ");
				$ret->Add("study", "READ");
				$ret->Add("View_TaskRefers", "YES");
			}
			else
			{
				$ret->HasWriteAccessOnOneItemAtLeast=true;
				$ret->Add("ControllerID", "WRITE");
				$ret->Add("ProjectID", "WRITE");
				$ret->Add("ProgramLevelID", "WRITE");
				$ret->Add("ProjectTaskTypeID", "WRITE");
				$ret->Add("TaskGroupID", "WRITE");
				$ret->Add("title", "WRITE");
				$ret->Add("Letter", "WRITE");
				$ret->Add("description", "WRITE");
				$ret->Add("PeriodType", "WRITE");
				$ret->Add("CountOfDone", "WRITE");
				$ret->Add("EstimatedStartTime", "WRITE");
				$ret->Add("RealStartTime", "WRITE");
				$ret->Add("EstimatedRequiredTimeDay", "WRITE");
				$ret->Add("EstimatedRequiredTimeHour", "WRITE");
				$ret->Add("EstimatedRequitedTimeMin", "WRITE");
				$ret->Add("HasExpireTime", "WRITE");
				$ret->Add("ExpireTime", "WRITE");
				$ret->Add("TaskPeriority", "WRITE");
				$ret->Add("TaskStatus", "WRITE");
				$ret->Add("ParentID", "WRITE");
 				$ret->Add("DoneDate","WRITE");
				$ret->Add("StartTime","WRITE");
				$ret->Add("EndTime","WRITE");
				$ret->Add("study","READ");
				$ret->Add("View_TaskRefers", "YES");
				$ret->Add("Add_TaskRefers", "YES");
				$ret->Add("Undo_TaskRefers", "YES");
			}
			$ret->Add("Add_ProjectTaskActivities", "YES");
			$ret->Add("Remove_ProjectTaskActivities", "PUBLIC");
			$ret->Add("Update_ProjectTaskActivities", "PUBLIC");
			$ret->Add("View_ProjectTaskActivities", "PUBLIC");
			$ret->Add("Add_ProjectTaskAssignedUsers", "YES");
			$ret->Add("Remove_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Update_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("View_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Add_ProjectTaskComments", "YES");
			$ret->Add("Remove_ProjectTaskComments", "PUBLIC");
			$ret->Add("Update_ProjectTaskComments", "PUBLIC");
			$ret->Add("View_ProjectTaskComments", "PUBLIC");
			$ret->Add("Add_ProjectTaskDocuments", "YES");
			$ret->Add("Remove_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("Update_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("View_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("Add_ProjectTaskHistory", "YES");
			$ret->Add("Remove_ProjectTaskHistory", "PUBLIC");
			$ret->Add("Update_ProjectTaskHistory", "PUBLIC");
			$ret->Add("View_ProjectTaskHistory", "PUBLIC");
			$ret->Add("Add_ProjectTaskRequisites", "YES");
			$ret->Add("Remove_ProjectTaskRequisites", "PUBLIC");
			$ret->Add("Update_ProjectTaskRequisites", "PUBLIC");
			$ret->Add("View_ProjectTaskRequisites", "PUBLIC");
		}
		else if($PersonRole=="EXTERNAL_OWNER")
		{
			if($task->TaskStatus=="FINISHED")
			{
				$ret->HasWriteAccessOnOneItemAtLeast=false;
				$ret->Add("ProjectID", "READ");
				$ret->Add("ControllerID", "READ");
				$ret->Add("ProgramLevelID", "READ");
				$ret->Add("ProjectTaskTypeID", "READ");
				$ret->Add("TaskGroupID", "READ");
				$ret->Add("title", "READ");
				$ret->Add("Letter", "READ");
				$ret->Add("description", "READ");
				$ret->Add("PeriodType", "READ");
				$ret->Add("CountOfDone", "READ");
				$ret->Add("EstimatedStartTime", "READ");
				$ret->Add("RealStartTime", "READ");
				$ret->Add("EstimatedRequiredTimeDay", "READ");
				$ret->Add("EstimatedRequiredTimeHour", "READ");
				$ret->Add("EstimatedRequitedTimeMin", "READ");
				$ret->Add("HasExpireTime", "READ");
				$ret->Add("ExpireTime", "READ");
				$ret->Add("TaskPeriority", "READ");
				$ret->Add("TaskStatus", "READ");
				$ret->Add("ParentID", "READ");
				$ret->Add("DoneDate", "READ");
				$ret->Add("StartTime", "READ");
				$ret->Add("EndTime", "READ");
				$ret->Add("study","READ");
				$ret->Add("View_TaskRefers", "YES");
			}
			else
			{
				$ret->HasWriteAccessOnOneItemAtLeast=true;
				$ret->Add("ControllerID", "WRITE");
				$ret->Add("ProjectID", "READ");
				$ret->Add("ProgramLevelID", "READ");
				$ret->Add("ProjectTaskTypeID", "READ");
				$ret->Add("TaskGroupID", "READ");
				$ret->Add("title", "WRITE");
				$ret->Add("Letter", "WRITE");
				$ret->Add("description", "WRITE");
				$ret->Add("PeriodType", "READ");
				$ret->Add("CountOfDone", "READ");
				$ret->Add("EstimatedStartTime", "READ");
				$ret->Add("RealStartTime", "READ");
				$ret->Add("EstimatedRequiredTimeDay", "READ");
				$ret->Add("EstimatedRequiredTimeHour", "READ");
				$ret->Add("EstimatedRequitedTimeMin", "READ");
				$ret->Add("HasExpireTime", "READ");
				$ret->Add("ExpireTime", "READ");
				$ret->Add("TaskPeriority", "READ");
				$ret->Add("TaskStatus", "READ");
				$ret->Add("ParentID", "READ");
				$ret->Add("DoneDate", "READ");
				$ret->Add("StartTime", "READ");
				$ret->Add("EndTime", "READ");
				$ret->Add("study","READ");
				$ret->Add("View_TaskRefers", "YES");
				$ret->Add("Add_TaskRefers", "YES");
				$ret->Add("Undo_TaskRefers", "YES");
			}
			$ret->Add("Add_ProjectTaskActivities", "YES");
			$ret->Add("Remove_ProjectTaskActivities", "PRIVATE");
			$ret->Add("Update_ProjectTaskActivities", "PRIVATE");
			$ret->Add("View_ProjectTaskActivities", "PUBLIC");
			$ret->Add("Add_ProjectTaskAssignedUsers", "NO");
			$ret->Add("Remove_ProjectTaskAssignedUsers", "NONE");
			$ret->Add("Update_ProjectTaskAssignedUsers", "NONE");
			$ret->Add("View_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Add_ProjectTaskComments", "YES");
			$ret->Add("Remove_ProjectTaskComments", "PRIVATE");
			$ret->Add("Update_ProjectTaskComments", "PRIVATE");
			$ret->Add("View_ProjectTaskComments", "PUBLIC");
			$ret->Add("Add_ProjectTaskDocuments", "YES");
			$ret->Add("Remove_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("Update_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("View_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("Add_ProjectTaskHistory", "YES");
			$ret->Add("Remove_ProjectTaskHistory", "NONE");
			$ret->Add("Update_ProjectTaskHistory", "NONE");
			$ret->Add("View_ProjectTaskHistory", "PUBLIC");
			$ret->Add("Add_ProjectTaskRequisites", "NO");
			$ret->Add("Remove_ProjectTaskRequisites", "NONE");
			$ret->Add("Update_ProjectTaskRequisites", "NONE");
			$ret->Add("View_ProjectTaskRequisites", "PUBLIC");
		}
		else if($PersonRole=="EXECUTOR")
		{
			$ret->HasWriteAccessOnOneItemAtLeast=true;
			$ret->Add("ControllerID", "WRITE");
			$ret->Add("ProjectID", "READ");
			$ret->Add("ProgramLevelID", "READ");
			$ret->Add("ProjectTaskTypeID", "READ");
			$ret->Add("TaskGroupID", "READ");
			$ret->Add("title", "READ");
			$ret->Add("Letter", "READ");
			$ret->Add("description", "READ");
			$ret->Add("PeriodType", "READ");
			$ret->Add("CountOfDone", "READ");
			$ret->Add("EstimatedStartTime", "READ");
			$ret->Add("RealStartTime", "WRITE");
			$ret->Add("EstimatedRequiredTimeDay", "READ");
			$ret->Add("EstimatedRequiredTimeHour", "READ");
			$ret->Add("EstimatedRequitedTimeMin", "READ");
			$ret->Add("HasExpireTime", "READ");
			$ret->Add("ExpireTime", "READ");
			$ret->Add("TaskPeriority", "READ");
			$ret->Add("TaskStatus", "WRITE");
			$ret->Add("ParentID", "READ");
			$ret->Add("DoneDate", "READ");
			$ret->Add("StartTime", "READ");
			$ret->Add("EndTime", "READ");
			$ret->Add("study","READ");
			$ret->Add("Add_ProjectTaskActivities", "YES");
			$ret->Add("Remove_ProjectTaskActivities", "PRIVATE");
			$ret->Add("Update_ProjectTaskActivities", "PRIVATE");
			$ret->Add("View_ProjectTaskActivities", "PUBLIC");
			$ret->Add("Add_ProjectTaskAssignedUsers", "YES");
			$ret->Add("Remove_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Update_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("View_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Add_ProjectTaskComments", "YES");
			$ret->Add("Remove_ProjectTaskComments", "PRIVATE");
			$ret->Add("Update_ProjectTaskComments", "PRIVATE");
			$ret->Add("View_ProjectTaskComments", "PUBLIC");
			$ret->Add("Add_ProjectTaskDocuments", "YES");
			$ret->Add("Remove_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("Update_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("View_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("Add_ProjectTaskHistory", "YES");
			$ret->Add("Remove_ProjectTaskHistory", "NONE");
			$ret->Add("Update_ProjectTaskHistory", "NONE");
			$ret->Add("View_ProjectTaskHistory", "PUBLIC");
			$ret->Add("Add_ProjectTaskRequisites", "NO");
			$ret->Add("Remove_ProjectTaskRequisites", "NONE");
			$ret->Add("Update_ProjectTaskRequisites", "NONE");
			$ret->Add("View_ProjectTaskRequisites", "PUBLIC");
			$ret->Add("View_TaskRefers", "YES");
			$ret->Add("Add_TaskRefers", "YES");
			$ret->Add("Undo_TaskRefers", "YES");
		}
		else if($PersonRole=="VIEWER")
		{
			$ret->HasWriteAccessOnOneItemAtLeast=false;
			$ret->Add("ControllerID", "READ");
			$ret->Add("ProjectID", "READ");
			$ret->Add("ProgramLevelID", "READ");
			$ret->Add("ProjectTaskTypeID", "READ");
			$ret->Add("TaskGroupID", "READ");
			$ret->Add("title", "READ");
			$ret->Add("Letter", "READ");
			$ret->Add("description", "READ");
			$ret->Add("PeriodType", "READ");
			$ret->Add("CountOfDone", "READ");
			$ret->Add("EstimatedStartTime", "READ");
			$ret->Add("RealStartTime", "READ");
			$ret->Add("EstimatedRequiredTimeDay", "READ");
			$ret->Add("EstimatedRequiredTimeHour", "READ");
			$ret->Add("EstimatedRequitedTimeMin", "READ");
			$ret->Add("HasExpireTime", "READ");
			$ret->Add("ExpireTime", "READ");
			$ret->Add("TaskPeriority", "READ");
			$ret->Add("TaskStatus", "READ");
			$ret->Add("ParentID", "READ");
			$ret->Add("DoneDate", "READ");
			$ret->Add("StartTime", "READ");
			$ret->Add("EndTime", "READ");
			$ret->Add("study","READ");
			$ret->Add("Add_ProjectTaskActivities", "YES");
			$ret->Add("Remove_ProjectTaskActivities", "PRIVATE");
			$ret->Add("Update_ProjectTaskActivities", "PRIVATE");
			$ret->Add("View_ProjectTaskActivities", "PUBLIC");
			$ret->Add("Add_ProjectTaskAssignedUsers", "NO");
			$ret->Add("Remove_ProjectTaskAssignedUsers", "NONE");
			$ret->Add("Update_ProjectTaskAssignedUsers", "NONE");
			$ret->Add("View_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Add_ProjectTaskComments", "YES");
			$ret->Add("Remove_ProjectTaskComments", "PRIVATE");
			$ret->Add("Update_ProjectTaskComments", "PRIVATE");
			$ret->Add("View_ProjectTaskComments", "PUBLIC");
			$ret->Add("Add_ProjectTaskDocuments", "YES");
			$ret->Add("Remove_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("Update_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("View_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("Add_ProjectTaskHistory", "YES");
			$ret->Add("Remove_ProjectTaskHistory", "NONE");
			$ret->Add("Update_ProjectTaskHistory", "NONE");
			$ret->Add("View_ProjectTaskHistory", "PUBLIC");
			$ret->Add("Add_ProjectTaskRequisites", "NO");
			$ret->Add("Remove_ProjectTaskRequisites", "NONE");
			$ret->Add("Update_ProjectTaskRequisites", "NONE");
			$ret->Add("View_ProjectTaskRequisites", "PUBLIC");
			$ret->Add("View_TaskRefers", "YES");
		}
		else if($PersonRole=="PMMANAGER")
		{
			$ret->HasWriteAccessOnOneItemAtLeast=true;
			$ret->Add("ControllerID", "READ");
			$ret->Add("ProjectID", "READ");
			$ret->Add("ProgramLevelID", "READ");
			$ret->Add("ProjectTaskTypeID", "READ");
			$ret->Add("TaskGroupID", "READ");
			$ret->Add("title", "READ");
			$ret->Add("Letter", "READ");
			$ret->Add("description", "READ");
			$ret->Add("PeriodType", "READ");
			$ret->Add("CountOfDone", "READ");
			$ret->Add("EstimatedStartTime", "READ");
			$ret->Add("RealStartTime", "READ");
			$ret->Add("EstimatedRequiredTimeDay", "READ");
			$ret->Add("EstimatedRequiredTimeHour", "READ");
			$ret->Add("EstimatedRequitedTimeMin", "READ");
			$ret->Add("HasExpireTime", "READ");
			$ret->Add("ExpireTime", "READ");
			$ret->Add("TaskPeriority", "READ");
			$ret->Add("TaskStatus", "READ");
			$ret->Add("ParentID", "READ");
			$ret->Add("DoneDate", "READ");
			$ret->Add("StartTime", "READ");
			$ret->Add("EndTime", "READ");
			$ret->Add("study","READ");
			$ret->Add("Add_ProjectTaskActivities", "YES");
			$ret->Add("Remove_ProjectTaskActivities", "PRIVATE");
			$ret->Add("Update_ProjectTaskActivities", "PRIVATE");
			$ret->Add("View_ProjectTaskActivities", "PUBLIC");
			$ret->Add("Add_ProjectTaskAssignedUsers", "NO");
			$ret->Add("Remove_ProjectTaskAssignedUsers", "NONE");
			$ret->Add("Update_ProjectTaskAssignedUsers", "NONE");
			$ret->Add("View_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Add_ProjectTaskComments", "YES");
			$ret->Add("Remove_ProjectTaskComments", "PRIVATE");
			$ret->Add("Update_ProjectTaskComments", "PRIVATE");
			$ret->Add("View_ProjectTaskComments", "PUBLIC");
			$ret->Add("Add_ProjectTaskDocuments", "YES");
			$ret->Add("Remove_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("Update_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("View_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("Add_ProjectTaskHistory", "YES");
			$ret->Add("Remove_ProjectTaskHistory", "NONE");
			$ret->Add("Update_ProjectTaskHistory", "NONE");
			$ret->Add("View_ProjectTaskHistory", "PUBLIC");
			$ret->Add("Add_ProjectTaskRequisites", "NO");
			$ret->Add("Remove_ProjectTaskRequisites", "NONE");
			$ret->Add("Update_ProjectTaskRequisites", "NONE");
			$ret->Add("View_ProjectTaskRequisites", "PUBLIC");
			$ret->Add("View_TaskRefers", "YES");
			$ret->Add("Add_TaskRefers", "YES");
			$ret->Add("Undo_TaskRefers", "YES");
		}

		else if($PersonRole=="CONTROLLER")
		{
			$ret->HasWriteAccessOnOneItemAtLeast=true;
			$ret->Add("ControllerID", "READ");
			$ret->Add("ProjectID", "READ");
			$ret->Add("ProgramLevelID", "READ");
			$ret->Add("ProjectTaskTypeID", "READ");
			$ret->Add("TaskGroupID", "READ");
			$ret->Add("title", "READ");
			$ret->Add("Letter", "READ");
			$ret->Add("description", "READ");
			$ret->Add("PeriodType", "READ");
			$ret->Add("CountOfDone", "READ");
			$ret->Add("EstimatedStartTime", "READ");
			$ret->Add("RealStartTime", "WRITE");
			$ret->Add("EstimatedRequiredTimeDay", "READ");
			$ret->Add("EstimatedRequiredTimeHour", "READ");
			$ret->Add("EstimatedRequitedTimeMin", "READ");
			$ret->Add("HasExpireTime", "READ");
			$ret->Add("ExpireTime", "READ");
			$ret->Add("TaskPeriority", "READ");
			$ret->Add("TaskStatus", "WRITE");
			$ret->Add("ParentID", "READ");
			$ret->Add("DoneDate", "READ");
			$ret->Add("StartTime", "READ");
			$ret->Add("EndTime", "READ");
			$ret->Add("study","READ");
			$ret->Add("Add_ProjectTaskActivities", "YES");
			$ret->Add("Remove_ProjectTaskActivities", "PRIVATE");
			$ret->Add("Update_ProjectTaskActivities", "PRIVATE");
			$ret->Add("View_ProjectTaskActivities", "PUBLIC");
			$ret->Add("Add_ProjectTaskAssignedUsers", "NO");
			$ret->Add("Remove_ProjectTaskAssignedUsers", "NONE");
			$ret->Add("Update_ProjectTaskAssignedUsers", "NONE");
			$ret->Add("View_ProjectTaskAssignedUsers", "PUBLIC");
			$ret->Add("Add_ProjectTaskComments", "YES");
			$ret->Add("Remove_ProjectTaskComments", "PRIVATE");
			$ret->Add("Update_ProjectTaskComments", "PRIVATE");
			$ret->Add("View_ProjectTaskComments", "PUBLIC");
			$ret->Add("Add_ProjectTaskDocuments", "YES");
			$ret->Add("Remove_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("Update_ProjectTaskDocuments", "PRIVATE");
			$ret->Add("View_ProjectTaskDocuments", "PUBLIC");
			$ret->Add("Add_ProjectTaskHistory", "YES");
			$ret->Add("Remove_ProjectTaskHistory", "NONE");
			$ret->Add("Update_ProjectTaskHistory", "NONE");
			$ret->Add("View_ProjectTaskHistory", "PUBLIC");
			$ret->Add("Add_ProjectTaskRequisites", "NO");
			$ret->Add("Remove_ProjectTaskRequisites", "NONE");
			$ret->Add("Update_ProjectTaskRequisites", "NONE");
			$ret->Add("View_ProjectTaskRequisites", "PUBLIC");
			$ret->Add("View_TaskRefers", "YES");
		}
		else if($PersonRole=="NONE")
		{
			$ret->HasWriteAccessOnOneItemAtLeast=false;
		}
		
		return $ret;
	}

}
?>
