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
			echo "<p align=center><font color=red>برای این پروژه پاسخگویی تعیین نشده است</font></p>";
		}
	}
	else
	{
		echo "<p align=center><font color=red>کد سیستم نامشخص است</font></p>";			
	}
}
?>
<script src="../stuoffice/Scripts/General.js"></script>
<form method=post name=f1 id=f1  enctype='multipart/form-data'>
<table class=ListTable>
	<tr class=HeaderOfTable><td colspan=2 align=center>درخواست انجام كار</td></tr>
	<tr>
		<td><font color=red>*</font>&nbsp;عنوان</td><td>
		<input type=text name=title size=80 value=''>
		</td>
	</tr>
	<tr>
		<td>شرح</td>
		<td>
			<textarea cols=80 rows=5 name=description></textarea>
		</td>
	</tr>
	<tr class=FooterOfTable>
	<td colspan=2 align=center>
		<input type=button onclick='javascript: CheckValidity();' value='ذخيره'>
	</td>
	</tr>
</table>
<br>
<a href='NewDataChangeRequest.php'><b>[در صورتیکه درخواست به منظور ایجاد تغییرات دستی بر روی داده های بانک اطلاعاتی است اینجا را کلیک کنید]
</b></a>
</form>
</body>
<script>
	function CheckValidity()
	{
		if(f1.title.value=='')
		{
			alert("لطفا عنواني وارد نمائيد.");
		}
		else
			f1.submit();
	}
</script>

</html>