<?php 
/*
 صفحه درخواست انجام کار از سمت مشتری
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-30
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTasks.class.php");
include("classes/projects.class.php");
include("classes/ProjectResponsibles.class.php");
include("classes/ProjectTaskAssignedUsers.class.php");
HTMLBegin();

if(isset($_POST["title"]))
{
	$PC = new PermissionsContainer();
	$PC->Add("ProjectID", "WRITE");
	$PC->Add("ProjectTaskTypeID", "WRITE");
	$PC->Add("title", "WRITE");
	$PC->Add("description", "WRITE");
	$PC->Add("PeriodType", "WRITE");
	$PC->Add("CountOfDone", "WRITE");
	$PC->Add("RealStartTime", "WRITE");
	$PC->Add("EstimatedStartTime", "WRITE");
	$PC->Add("EstimatedRequiredTimeDay", "WRITE");
	$PC->Add("EstimatedRequiredTimeHour", "WRITE");
	$PC->Add("EstimatedRequitedTimeMin", "WRITE");
	$PC->Add("HasExpireTime", "WRITE");
	$PC->Add("ExpireTime", "WRITE");
	$PC->Add("TaskPeriority", "WRITE");
	$PC->Add("TaskStatus", "WRITE");
	$PC->Add("ParentID", "WRITE");
	$ProjectID = manage_projects::GetProjectID($_SESSION["SystemCode"]);
	if($ProjectID!="0")
	{	
		$ret = manage_ProjectResponsibles::GetList($ProjectID);
		if(count($ret)>0)
		{			
			$ProjectTaskID = manage_ProjectTasks::Add($ProjectID, 0, 0, $_POST["title"], $_POST["description"], 'ONCE', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 'NO', '0000-00-00 00:00:00', 2, "NOT_START", 0, 0, $PC);
			for($i=0; $i<count($ret); $i++)
			{
				manage_ProjectTaskAssignedUsers::Add($ProjectTaskID, $ret[$i]->PersonID, "انتساب اتومات", 100, "EXECUTOR");
			}
			echo "<script>document.location='NewProjectTasks.php?UpdateID=".$ProjectTaskID."';</script>";
		}
		else
		{
			echo "<p class=\" text-center text-danger \">".C_NO_RESPONSE_HAS_BEEN_DETERMINED_FOR_THIS_PROJECT."</p>";
		}
	}
	else
	{
		echo "<p class=\"text-center text-danger\">".C_UNKNOWN_SYSTEM_CODE."</p>";			
	}
}
?>
<script src="../stuoffice/Scripts/General.js"></script>
<form method=post name=f1 id=f1  enctype='multipart/form-data'>
<div class="row">
<div class="col-1"></div>
<div class="col-10">
<table class="table table-sm">
	<thead class="table-info">
		<tr>
			<td colspan=2 class="text-center"><? echo C_TASK_REQUEST; ?></td>
		</tr>
	</thead>
	<tr>
		<td><span class="text-danger">*</span>&nbsp;<? echo C_TITLE; ?></td><td>
		<input class="form-control col-md-6" type=text name=title  value='' required>
		</td>
	</tr>
	<tr>
		<td><? echo C_DESCRIPTION; ?></td>
		<td>
			<textarea class="form-control col-md-6" cols=80 rows=5 name=description></textarea>
		</td>
	</tr>
	<tr class="table-info">
	<td colspan=2 class="text-center">
		<input class="btn btn-success" type="submit" value='<? echo C_SAVE; ?>'>
	</td>
	</tr>
</table>
</div>
<div class="col-1"></div>
</div>
<br>
<a href='NewDataChangeRequest.php'><b><? echo C_IF_REQUEST_IS_ABOUT_CHANGING_ACCESS_TO_DATABASE_DATA_CLICK_HERE; ?>
</b></a>
</form>
</body>
</html>