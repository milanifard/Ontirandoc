<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/

include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/ProjectTaskTypes.class.php");
include_once("classes/ProjectTaskGroups.class.php");
include_once("classes/projects.class.php");
include_once("classes/ProjectTasksSecurity.class.php");
include_once("classes/ProjectTaskAssignedUsers.class.php");
include_once("classes/ProjectTaskDocuments.class.php");
include_once("classes/ProjectTaskComments.class.php");
include_once("classes/ProjectTaskActivities.class.php");

ini_set('display_errors','off');

HTMLBegin();

$mysql = pdodb::getInstance();
$CurProjectID = 0;
if(isset($_REQUEST["UpdateID"])) 
	$pc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UpdateID"]);
else
	$pc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], 0);

// Task Referring System: Processing User's Request ...

if ($pc->GetPermission("Add_TaskRefers") == 'YES' && isset($_POST["Executor"]) && isset($_POST["Remove"]))
{
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);
	error_reporting(0);

	manage_ProjectTasks::DoRefer($_POST["TaskID"], $_SESSION["User"]->PersonID, $_POST["Executor"], $_POST["Description"], $_POST["Remove"]);
}
// ...

if(isset($_REQUEST["Save"])) 
{
	$Item_ProjectID = $Item_ProjectTaskTypeID = $Item_title = $Item_description = $Item_CreatorID = $Item_PeriodType = $Item_CountOfDone = $Item_EstimatedStartTime = $Item_RealStartTime = "";
	$Item_EstimatedRequiredTimeDay = $Item_EstimatedRequiredTimeHour = $Item_EstimatedRequitedTimeMin = $Item_TaskPeriority = $Item_TaskStatus = $UpdateReason = "";
	$Item_HasExpireTime = $Item_ExpireTime = "";
	$Item_ProgramLevelID = "";
	$Item_TaskGroupID = 0;
	$Item_ControllerID = 0;
	$study = "NO";

	$LetterNumber = '';
	$LetterType = '';
	$LetterDate = '1000-01-01';
	if(isset($_REQUEST["LetterNumber"]))
		$LetterNumber = $_REQUEST["LetterNumber"];
	if(isset($_REQUEST["LetterType"]))
		$LetterType = $_REQUEST["LetterType"];
	if(isset($_REQUEST["LetterDate_MONTH"]))
		$LetterDate = SharedClass::ConvertToMiladi($_REQUEST["LetterDate_YEAR"], $_REQUEST["LetterDate_MONTH"], $_REQUEST["LetterDate_DAY"]);

	if(isset($_REQUEST["Item_TaskGroupID"]))
		$Item_TaskGroupID=$_REQUEST["Item_TaskGroupID"];
	if(isset($_REQUEST["Item_ControllerID"]))
		$Item_ControllerID=$_REQUEST["Item_ControllerID"];
		
	if(isset($_REQUEST["Item_ProjectID"]))
		$Item_ProjectID=$_REQUEST["Item_ProjectID"];
	if(isset($_REQUEST["Item_ProgramLevelID"]))
		$Item_ProgramLevelID=$_REQUEST["Item_ProgramLevelID"];
	if(isset($_REQUEST["Item_ProjectTaskTypeID"]))
		$Item_ProjectTaskTypeID=$_REQUEST["Item_ProjectTaskTypeID"];
	if(isset($_REQUEST["Item_title"]))
		$Item_title=$_REQUEST["Item_title"];
	if(isset($_REQUEST["Item_description"]))
		$Item_description=$_REQUEST["Item_description"];

	if(isset($_REQUEST["study"])) $study = "YES";

	if(isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID=$_REQUEST["Item_CreatorID"];
	if(isset($_REQUEST["Item_PeriodType"]))
		$Item_PeriodType=$_REQUEST["Item_PeriodType"];
	if(isset($_REQUEST["Item_CountOfDone"]))
		$Item_CountOfDone=$_REQUEST["Item_CountOfDone"];
	if(isset($_REQUEST["EstimatedStartTime_DAY"]))
	{
		$Item_EstimatedStartTime = SharedClass::ConvertToMiladi($_REQUEST["EstimatedStartTime_YEAR"], $_REQUEST["EstimatedStartTime_MONTH"], $_REQUEST["EstimatedStartTime_DAY"]);
	}
	if(isset($_REQUEST["RealStartTime_DAY"]))
	{
		$Item_RealStartTime = SharedClass::ConvertToMiladi($_REQUEST["RealStartTime_YEAR"], $_REQUEST["RealStartTime_MONTH"], $_REQUEST["RealStartTime_DAY"]);
	}
	if(isset($_REQUEST["Item_EstimatedRequiredTimeDay"]))
		$Item_EstimatedRequiredTimeDay=$_REQUEST["Item_EstimatedRequiredTimeDay"];
	if(isset($_REQUEST["Item_EstimatedRequiredTimeHour"]))
		$Item_EstimatedRequiredTimeHour=$_REQUEST["Item_EstimatedRequiredTimeHour"];
	if(isset($_REQUEST["Item_EstimatedRequitedTimeMin"]))
		$Item_EstimatedRequitedTimeMin=$_REQUEST["Item_EstimatedRequitedTimeMin"];
	if(isset($_REQUEST["Item_HasExpireTime"]))
		$Item_HasExpireTime=$_REQUEST["Item_HasExpireTime"];
	if(isset($_REQUEST["ExpireTime_DAY"]))
	{
		$Item_ExpireTime = SharedClass::ConvertToMiladi($_REQUEST["ExpireTime_YEAR"], $_REQUEST["ExpireTime_MONTH"], $_REQUEST["ExpireTime_DAY"]);
	}
	if(isset($_REQUEST["Item_TaskPeriority"]))
		$Item_TaskPeriority=$_REQUEST["Item_TaskPeriority"];
	if(isset($_REQUEST["Item_TaskStatus"]))
		$Item_TaskStatus=$_REQUEST["Item_TaskStatus"];
	//if(isset($_REQUEST["Item_ParentID"]))
	//	$Item_ParentID=$_REQUEST["Item_ParentID"];
	if(isset($_REQUEST["UpdateReason"]))
		$UpdateReason = $_REQUEST["UpdateReason"];
	if(isset($_REQUEST["TaskGroupID"]))
		$Item_TaskGroupID=$_REQUEST["TaskGroupID"];
	$Item_ParentID = 0; // فعلا فرض می کنیم همه کارها در یک سطح هستند ولی سیستم امکان ایجاد کارهای سلسله مراتبی را دارد
	if(!isset($_REQUEST["UpdateID"])) 
	{
		if (trim($LetterNumber) != "" || trim($LetterDate) != "1000-01-01")
			$Item_ProjectID = 771; // کارهای ارسالی از سامانه‌ی مکاتبات
		else
			$LetterType = '';

		$ProjectTaskID = manage_ProjectTasks::Add($Item_ProjectID
				, $Item_ProgramLevelID
				, $Item_ProjectTaskTypeID
				, $Item_title
				, $Item_description
				, $Item_PeriodType
				, $Item_CountOfDone
				, $Item_EstimatedStartTime
				, $Item_RealStartTime
				, $Item_EstimatedRequiredTimeDay
				, $Item_EstimatedRequiredTimeHour
				, $Item_EstimatedRequitedTimeMin
				, $Item_HasExpireTime
				, $Item_ExpireTime
				, $Item_TaskPeriority
				, $Item_TaskStatus
				, $Item_ParentID
				, $Item_TaskGroupID
				, $Item_ControllerID
				,'00:00','00:00'
				, $study
				, $pc
				, $LetterNumber
				, $LetterType
				, $LetterDate
				);
		if($_REQUEST["Item_PersonID"]!="0" && $_REQUEST["Item_PersonID"]!=""){
			manage_ProjectTaskAssignedUsers::Add($ProjectTaskID, $_REQUEST["Item_PersonID"], "", 100, "EXECUTOR",0);
		}
		$Item_FileContent = "";
		$Item_FileName = "";
	if (trim($_FILES['Item_FileContent']['name']) != '')
	{
		if ($_FILES['Item_FileContent']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_FileContent']['error'];
		}
		else
		{
			$_size = $_FILES['Item_FileContent']['size'];
			$_name = $_FILES['Item_FileContent']['tmp_name'];
			$Item_FileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
			
			//$st = split ( '\.', $_FILES ['Item_FileContent'] ['name'] );
                        $st =  preg_split( "/\./", $_FILES ['Item_FileContent'] ['name'] );

			$extension = $st [count ( $st ) - 1];	
			
			$Item_FileName = $extension;
		}
	}

		if(isset($_REQUEST["Item_FileName"]))
			$Item_FileName=$_REQUEST["Item_FileName"];
		//echo $Item_FileName;
		if($Item_FileName!="")
		$TaskDocID=manage_ProjectTaskDocuments::Add($ProjectTaskID
				, ""
				, $Item_FileContent
				, $Item_FileName
				);

	$Item_FileContent = "";
	if (isset ( $_FILES ['Item_FileContent'] ) && trim ( $_FILES ['Item_FileContent'] ['tmp_name'] ) != '') 
	{
		$st = split ( '\.', $_FILES ['Item_FileContent'] ['name'] );
		$extension = $st [count ( $st ) - 1];	
		$fp = fopen("/mystorage/PlanAndProjectDocuments/TaskDocuments/" .$TaskDocID . "." . $extension, "w");
		fwrite ($fp, fread ( fopen ( $_FILES ['Item_FileContent'] ['tmp_name'], 'r' ), $_FILES ['Item_FileContent']['size']));
		fclose ($fp);			
		$Item_FileContent = $extension;
	}	
		if($Item_TaskStatus=="DONE" || $Item_TaskStatus=="REPLYED")
		{
			?>
<form method="post" action="NewProjectTaskActivities.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<?php echo $ProjectTaskID ?>">
</form>
<br>			
<table width=80% align=center border=1 cellspacing=1 cellpadding=5>
<tr>
<td>
وضعیت کار را تغییر داده اید بنابراین کار از کارتابل شما خارج می شود. لطفا چنانچه اقدامی برای این کار ثبت نکرده اید
<b> 
<a href='#' onclick="javascript: document.getElementById('NewRecordForm').submit();">اینجا</a>
</b>
 را کلیک کنید. 
<input type=button value='بستن پنجره' onclick='javascript: try { window.opener.document.location.reload(); } catch(err){} window.close();'>
</td>
</tr>
</table>
<?php 
			die();
		}
		echo "<script>try { window.opener.document.location.reload(); } catch(err){} window.close();</script>";
		die();
	}	
	else 
	{	
		$obj = new be_ProjectTasks();
		$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);

		manage_ProjectTasks::Update($_REQUEST["UpdateID"]
				, $Item_ProjectID
				, $Item_ProgramLevelID
				, $Item_ProjectTaskTypeID
				, $Item_title
				, $Item_description
				, $Item_PeriodType
				, $Item_CountOfDone
				, $Item_EstimatedStartTime
				, $Item_RealStartTime
				, $Item_EstimatedRequiredTimeDay
				, $Item_EstimatedRequiredTimeHour
				, $Item_EstimatedRequitedTimeMin
				, $Item_HasExpireTime
				, $Item_ExpireTime
				, $Item_TaskPeriority
				, $Item_TaskStatus
				, $Item_ParentID
				, $UpdateReason
				, $Item_TaskGroupID
				, $Item_ControllerID
				, $study
				, $pc
				, $LetterNumber
				, $LetterType
				, $LetterDate
				);

		if(($Item_TaskStatus=="DONE" || $Item_TaskStatus=="REPLYED") && $obj->TaskStatus!=$Item_TaskStatus)
		{
			?>
<form method="post" action="NewProjectTaskActivities.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<?php echo $obj->ProjectTaskID ?>">
</form>
<br>			
<table width=80% align=center border=1 cellspacing=1 cellpadding=5>
<tr>
<td>
وضعیت کار را تغییر داده اید بنابراین کار از کارتابل شما خارج می شود. لطفا چنانچه اقدامی برای این کار ثبت نکرده اید
<b> 
<a href='#' onclick="javascript: document.getElementById('NewRecordForm').submit();">اینجا</a>
</b>
 را کلیک کنید. 
<input type=button value='بستن پنجره' onclick='javascript: try { window.opener.document.location.reload(); } catch(err){} window.close();'>
</td>
</tr>
</table>
<?php 
			die();
		}

		echo "<script>try { window.opener.document.location.reload(); } catch(err){} window.close();</script>";
		die();
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$TaskDescription = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ProjectTasks();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	
	$CurProjectID = $obj->ProjectID;
	$obj->title = str_replace("\r\n", " " , $obj->title);
	//$obj->title = str_replace('\r', ' ' , $obj->title);
	
	$FieldPermission = $pc->GetPermission("ProjectID");

	$LoadDataJavascriptCode .= "document.getElementById('Span_PersonID2_FullName').innerHTML='".htmlspecialchars($obj->ControllerID_FullName, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_ControllerID.value='".htmlspecialchars($obj->ControllerID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	if($FieldPermission=="WRITE")
	{
		if($obj->study=="YES")
			$LoadDataJavascriptCode .= "document.getElementById('study').checked='true'; \r\n ";
	}

	if($FieldPermission=="WRITE")
	{
		$LoadDataJavascriptCode .= "document.f1.Item_ProjectID.value='".htmlspecialchars($obj->ProjectID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
		$LoadDataJavascriptCode .= "updateTaskTypesList(".$obj->ProjectID."); \r\n";
		$LoadDataJavascriptCode .= "updateTaskGroupsList(".$obj->ProjectID."); \r\n";
		$LoadDataJavascriptCode .= "updateProgramLevelList(".$obj->ProjectID."); \r\n";
	} 
	/*else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_ProjectID').innerHTML='".htmlspecialchars($obj->ProjectID_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n ";*/
	else if($FieldPermission=="READ"){
            
                if($CurProjectID!="0"){
            
                        $LoadDataJavascriptCode .= "document.getElementById('Item_ProjectID').innerHTML='".htmlspecialchars($obj->ProjectID_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n ";
                }
                 else if($CurProjectID=="0"){
                        $LoadDataJavascriptCode .= "document.f1.Item_ProjectID.value='".htmlspecialchars($obj->ProjectID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
                        $LoadDataJavascriptCode .= "updateTaskTypesList(".$obj->ProjectID."); \r\n";
                        $LoadDataJavascriptCode .= "updateTaskGroupsList(".$obj->ProjectID."); \r\n";
                        $LoadDataJavascriptCode .= "updateProgramLevelList(".$obj->ProjectID."); \r\n";
 
                 }
                
        }


	$FieldPermission = $pc->GetPermission("ProgramLevelID");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_ProgramLevelID.value='".htmlspecialchars($obj->ProgramLevelID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_ProgramLevelID').innerHTML='".htmlspecialchars($obj->ProgramLevelID_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
		
	$FieldPermission = $pc->GetPermission("ProjectTaskTypeID");
	if($FieldPermission=="WRITE")
	{
		$LoadDataJavascriptCode .= "document.f1.Item_ProjectTaskTypeID.value='".htmlspecialchars($obj->ProjectTaskTypeID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	} 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_ProjectTaskTypeID').innerHTML='".htmlspecialchars($obj->ProjectTaskTypeID_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 

	$FieldPermission = $pc->GetPermission("TaskGroupID");
	{
		if($FieldPermission=="WRITE")
			$LoadDataJavascriptCode .= "document.f1.Item_TaskGroupID.value='".htmlspecialchars($obj->TaskGroupID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
		else if($FieldPermission=="READ")
			$LoadDataJavascriptCode .= "document.getElementById('Item_TaskGroupID').innerHTML='".htmlspecialchars($obj->TaskGroupName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	}	
	$FieldPermission = $pc->GetPermission("title");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_title.value=decodeHTMLEntities('".htmlspecialchars($obj->title, ENT_QUOTES, 'UTF-8')."'); \r\n ";
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_title').innerHTML='".htmlspecialchars($obj->title, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$FieldPermission = $pc->GetPermission("description");

	$TaskDescription = "";
	if($FieldPermission=="WRITE" || $FieldPermission=="READ")
		$TaskDescription = htmlspecialchars($obj->description, ENT_QUOTES, 'UTF-8'); 
	
	$FieldPermission = $pc->GetPermission("PeriodType");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_PeriodType.value='".htmlspecialchars($obj->PeriodType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_PeriodType').innerHTML='".htmlspecialchars($obj->PeriodType_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$FieldPermission = $pc->GetPermission("CountOfDone");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_CountOfDone.value='".htmlspecialchars($obj->CountOfDone, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_CountOfDone').innerHTML='".htmlspecialchars($obj->CountOfDone, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$FieldPermission = $pc->GetPermission("EstimatedStartTime");
	if($obj->EstimatedStartTime_Shamsi!="date-error") 
	{
		if($FieldPermission=="WRITE")
		{
			$LoadDataJavascriptCode .= "document.f1.EstimatedStartTime_YEAR.value='".substr($obj->EstimatedStartTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.EstimatedStartTime_MONTH.value='".substr($obj->EstimatedStartTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.EstimatedStartTime_DAY.value='".substr($obj->EstimatedStartTime_Shamsi, 8, 2)."'; \r\n "; 
		}
		else if($FieldPermission=="READ")
		{
			$LoadDataJavascriptCode .= "document.getElementById('EstimatedStartTime_YEAR').innerHTML='".substr($obj->EstimatedStartTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('EstimatedStartTime_MONTH').innerHTML='".substr($obj->EstimatedStartTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('EstimatedStartTime_DAY').innerHTML='".substr($obj->EstimatedStartTime_Shamsi, 8, 2)."'; \r\n "; 
		}
	}
	$FieldPermission = $pc->GetPermission("RealStartTime");
	if($obj->RealStartTime_Shamsi!="date-error") 
	{
		if($FieldPermission=="WRITE")
		{
			$LoadDataJavascriptCode .= "document.f1.RealStartTime_YEAR.value='".substr($obj->RealStartTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.RealStartTime_MONTH.value='".substr($obj->RealStartTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.RealStartTime_DAY.value='".substr($obj->RealStartTime_Shamsi, 8, 2)."'; \r\n "; 
		}
		else if($FieldPermission=="READ")
		{
			$LoadDataJavascriptCode .= "document.getElementById('RealStartTime_YEAR').innerHTML='".substr($obj->RealStartTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('RealStartTime_MONTH').innerHTML='".substr($obj->RealStartTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('RealStartTime_DAY').innerHTML='".substr($obj->RealStartTime_Shamsi, 8, 2)."'; \r\n "; 
		}
	}
	$FieldPermission = $pc->GetPermission("EstimatedRequiredTimeDay");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_EstimatedRequiredTimeDay.value='".htmlspecialchars($obj->EstimatedRequiredTimeDay, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_EstimatedRequiredTimeDay').innerHTML='".htmlspecialchars($obj->EstimatedRequiredTimeDay, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$FieldPermission = $pc->GetPermission("EstimatedRequiredTimeHour");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_EstimatedRequiredTimeHour.value='".htmlspecialchars($obj->EstimatedRequiredTimeHour, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_EstimatedRequiredTimeHour').innerHTML='".htmlspecialchars($obj->EstimatedRequiredTimeHour, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$FieldPermission = $pc->GetPermission("EstimatedRequitedTimeMin");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_EstimatedRequitedTimeMin.value='".htmlspecialchars($obj->EstimatedRequitedTimeMin, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_EstimatedRequitedTimeMin').innerHTML='".htmlspecialchars($obj->EstimatedRequitedTimeMin, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$FieldPermission = $pc->GetPermission("HasExpireTime");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_HasExpireTime.value='".htmlspecialchars($obj->HasExpireTime, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_HasExpireTime').innerHTML='".htmlspecialchars($obj->HasExpireTime_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$FieldPermission = $pc->GetPermission("ExpireTime");
	if($obj->ExpireTime_Shamsi!="date-error") 
	{
		if($FieldPermission=="WRITE")
		{
			$LoadDataJavascriptCode .= "document.f1.ExpireTime_YEAR.value='".substr($obj->ExpireTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.ExpireTime_MONTH.value='".substr($obj->ExpireTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.ExpireTime_DAY.value='".substr($obj->ExpireTime_Shamsi, 8, 2)."'; \r\n "; 
		}
		else if($FieldPermission=="READ")
		{
			$LoadDataJavascriptCode .= "document.getElementById('ExpireTime_YEAR').innerHTML='".substr($obj->ExpireTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('ExpireTime_MONTH').innerHTML='".substr($obj->ExpireTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('ExpireTime_DAY').innerHTML='".substr($obj->ExpireTime_Shamsi, 8, 2)."'; \r\n "; 
		}
	}
	$FieldPermission = $pc->GetPermission("TaskStatus");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_TaskStatus.value='".htmlspecialchars($obj->TaskStatus, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ"){
		if($obj->study=="NO"){
		$LoadDataJavascriptCode .= "document.getElementById('Item_TaskStatus').innerHTML='".htmlspecialchars($obj->TaskStatus_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n ";
		}
		else {	$LoadDataJavascriptCode .= "document.f1.Item_TaskStatus.value='".htmlspecialchars($obj->TaskStatus, ENT_QUOTES, 'UTF-8')."'; \r\n "; }
 		}

 
	$DoneDate = $obj->DoneDate_Shamsi;
	$CreateDate = $obj->CreateDate_Shamsi;  
	$Creator_FullName = $obj->CreatorID_FullName;
}
$query="SELECT * FROM projectmanagement.ProjectMembers where PersonID='".$_SESSION["PersonID"]."' and ProjectID='".$CurProjectID."' ";
$resm=$mysql->Execute($query);
$member = $resm->fetch();

?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data">
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		echo manage_ProjectTasks::ShowSummary($_REQUEST["UpdateID"]);
		echo manage_ProjectTasks::ShowTabs($_REQUEST["UpdateID"], "NewProjectTasks");
	}
?>

<br><table width="96%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center" width="50%">ایجاد/ویرایش کار</td>
<?php if(isset($_REQUEST["UpdateID"])){ ?>
<td rowspan=3 width="50%" style="background-color: rgb(248, 248, 244); vertical-align: top;">
<div style="overflow-y: scroll; height: 450px; vertical-align: top;">
<div class="HeaderOfTable" style="text-align: center; border-bottom: 1px outset antiquewhite;">یادداشت‌ها، اسناد، و اقدامات</div>
<?php 
if(isset($_REQUEST["UpdateID"]))
{
	$res = manage_ProjectTaskComments::GetList($_REQUEST["UpdateID"]); 
	if(count($res)>0)
	{
?>
<table align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="4">
	یادداشت‌ها
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td>متن</td>	
	<td width=5% >ایجاد کننده</td>
	<td width=5% >زمان ایجاد</td>
</tr>
<?
	for($k=0; $k<count($res); $k++)
	{
		if($k%2==0)
			echo "<tr class=\"OddRow\">";
		else
			echo "<tr class=\"EvenRow\">";
		echo "<td>".($k+1)."</td>";
		echo "	<td>".str_replace("\r", "<br>", htmlspecialchars($res[$k]->CommentBody, ENT_QUOTES, 'UTF-8'))."</td>";
		echo "	<td>".$res[$k]->CreatorID_FullName."</td>";
		echo "	<td>".$res[$k]->CreateTime_Shamsi."</td>";
		echo "</tr>";
	}
?>
</table>
<?php
	}  
	$res = manage_ProjectTaskDocuments::GetList($_REQUEST["UpdateID"]); 
	if(count($res)>0)
	{
?>
<table align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="5">
	اسناد
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td width=5% nowrap>ضمیمه</td>
	<td>شرح</td>
	<td width=5% nowrap>ایجاد کننده</td>
	<td width=5% nowrap>زمان ایجاد</td>
</tr>
<?
	for($k=0; $k<count($res); $k++)
	{
		if($k%2==0)
			echo "<tr class=\"OddRow\">";
		else
			echo "<tr class=\"EvenRow\">";
		echo "<td>".($k+1)."</td>";
		//echo "	<td><a href='DownloadFile.php?FileType=TaskDocument&RecID=".$res[$k]->ProjectTaskDocumentID."'><img src='images/Download.gif'></a></td>";	
               echo "<td>";
               if ($res[$k]->FileName != "")
                   echo "<a target='_blank' href=\"ReciptFile.php?PtdID=" . $res[$k]->ProjectTaskDocumentID . "&FName_ptdID=" . $res[$k]->FileName . "\"><img border=0 src='images/Download.gif' id='fileimg' title='دریافت فایل'></a>";
               else
                   echo "ندارد";
	        echo "</td>";
		echo "	<td>&nbsp;".str_replace("\r", "<br>", htmlspecialchars($res[$k]->DocumentDescription, ENT_QUOTES, 'UTF-8'))."</td>";
		echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
		echo "	<td nowrap>".$res[$k]->CreateTime_Shamsi."</td>";
		echo "</tr>";
	}
?>
</table>
<?php }

$OrderByFieldName = "ActivityDate";
$OrderType = "DESC";
if(isset($_REQUEST["OrderByFieldName"]))
{
	$OrderByFieldName = $_REQUEST["OrderByFieldName"];
	$OrderType = $_REQUEST["OrderType"];
}
$res = manage_ProjectTaskActivities::GetList($_REQUEST["UpdateID"], $OrderByFieldName, $OrderType); 

	if(count($res)>0)
	{
?>
<table align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	اقدامات
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td width=1% >نوع اقدام</td>
	<td  width=1% >زمان مصرفی</td>
	<td width=1% >درصد پیشرفت</td>
	<td>شرح</td>
	<td width=1% nowrap>ضمیمه</td>
	<td width=1% >ایجاد کننده</td>
	<td width=1% nowrap>تاریخ اقدام</td>
</tr>
<?
	for($k=0; $k<count($res); $k++)
	{
		if($k%2==0)
			echo "<tr class=\"OddRow\">";
		else
			echo "<tr class=\"EvenRow\">";
		echo "<td>".($k+1)."</td>";
                echo "	<td>".$res[$k]->ProjectTaskActivityTypeID_Desc."</td>";
                echo "	<td nowrap>".floor($res[$k]->ActivityLength/60).":".($res[$k]->ActivityLength%60)."</td>";
                echo "	<td>".htmlspecialchars($res[$k]->ProgressPercent, ENT_QUOTES, 'UTF-8')."</td>";
                echo "	<td>&nbsp;".str_replace("\r", "<br>", htmlspecialchars($res[$k]->ActivityDescription, ENT_QUOTES, 'UTF-8'))."</td>";
                /*if($res[$k]->FileName!="")
                        echo "	<td><a href='DownloadFile.php?FileType=ActivityFile&RecID=".$res[$k]->ProjectTaskActivityID."'><img src='images/Download.gif'></a></td>";
                else
                        echo "	<td>-</td>";*/
echo "<td>";
if ($res[$k]->FileName != "")
	echo "<a target='_blank' href=\"ReciptFile.php?AID=" . $res[$k]->ProjectTaskActivityID . "&FileName_AID=" . $res[$k]->FileName . "\"><img border=0 src='images/Download.gif' id='fileimg' title='دریافت فایل'></a>";
else
	echo "ندارد";
echo "</td>";

                echo "	<td>".$res[$k]->CreatorID_FullName."</td>";
                echo "	<td nowrap>".$res[$k]->ActivityDate_Shamsi."</td>";
		echo "</tr>";
	}
?>
</table>
<?php }

}?>
</div>
</td>
<?php } ?>
</tr>
<tr>
<td>
<table width="100%" border="0">
<?php
	$ret = manage_ProjectTasks::GetTaskProgressPercentAndUsedTime($_REQUEST["UpdateID"]);
	if($pc->GetPermission("ProjectID")!="NONE") {
?>
<tr>
	<td width="1%" nowrap>
 پروژه مربوطه:
	</td>
	<td nowrap>
	<? if($pc->GetPermission("ProjectID")=="WRITE") { ?>
	<select name="Item_ProjectID" id="Item_ProjectID" onchange='javascript: updateTaskTypesList(this.value); updateTaskGroupsList(this.value); updateProgramLevelList(this.value);'>
	<option value=0>-
	<? echo manage_projects::GetUserProjectsOptions($_SESSION["PersonID"], $_SESSION["UserID"]); ?>	</select>
	<? }
else if($pc->GetPermission("ProjectID")=="READ" ) { 

if($CurProjectID!="0"){

?>
	<span id="Item_ProjectID" name="Item_ProjectID"></span> <? }
        else if($CurProjectID=="0"){?>

	<select name="Item_ProjectID" id="Item_ProjectID" onchange='javascript: updateTaskTypesList(this.value); updateTaskGroupsList(this.value); updateProgramLevelList(this.value); '>
	<option value=0>-
	<? echo manage_projects::GetUserProjectsOptions($_SESSION["PersonID"], $_SESSION["UserID"]); ?>	</select>

<? }
} ?>
 
	</td>
</tr>
<? } ?>

 </table>

<style>
.greenText{
color: #0D756B;
font-weight: bold;
}
.blueText{color: #0D6EB2;font-weight: bold;}


</style>

<table>

<? if($pc->GetPermission("title")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 عنوان:
	</td>
	<td>
	<? if($pc->GetPermission("title")=="WRITE") { ?>
	
	<input type="text" name="Item_title" id="Item_title" maxlength="1000" size="77">
	<? } else if($pc->GetPermission("title")=="READ") { ?>
	<span id="Item_title" name="Item_title"></span> 
	<? } ?>
	<?php if(isset($_REQUEST["UpdateID"]) && $obj->BugID>0) { ?>
	<a href='ShowBugDetail.php?BugID=<?php echo $obj->BugID ?>' target=_blank>توضیحات بیشتر ارسال کننده و مشخصات اتصال</a>
	<?php } ?>
	</td>
</tr>
<? } 
if($pc->GetPermission("description")!="NONE") { ?>
<tr>
	<td width="1%" nowrap style="vertical-align: top;">
 شرح:
	</td>
	<td >
	<? if($pc->GetPermission("description")=="WRITE") { ?>
	<textarea name="Item_description" id="Item_description" cols="67" rows="5"><?php echo $TaskDescription ?></textarea>
	<? } else if($pc->GetPermission("description")=="READ") { ?>
<div style="overflow-y: scroll; vertical-align: top; max-height: 152px; min-height: 152px;">
	<span id="Item_description" name="Item_description"><?php echo str_replace("\r", "<br>", $TaskDescription); ?></span>
</div>
	<? } ?>
	</td>
</tr>
<? } ?>


<? /*if($_SESSION["UserID"]=="gholami-a") { 
echo $pc->GetPermission("ProjectID");echo $obj->study; echo $_SESSION["UserID"]; echo $member["AccessType"];
echo $pc->GetPermission("study");
}*/?>

<?php
if($pc->HasWriteAccessOnOneItemAtLeast)
{
	$LtDay = '';
	$LtMonth = '';
	$LtYear = '';
	if (trim($obj->LetterDate) != "date-error") 
	{
		$LtDay = substr($obj->LetterDate, 8, 2);
		$LtMonth = substr($obj->LetterDate, 5, 2);
		$LtYear = substr($obj->LetterDate, 2, 2);
	}

?>
<tr>
	<td colspan=2>	
		<span title="تعیین شماره‌ی نامه یا تاریخ نامه سبب تغییر پروژه به 'کارهای ارسالی از سامانه‌ی مکاتبات' می‌شود.">شماره‌ی نامه:</span>
		<input maxlength="25" id="LetterNumber"  name="LetterNumber" type="text" size="8" value="<?php echo $obj->LetterNumber ?>">&emsp;
		نوع نامه:
		<select id="LetterType" name="LetterType">
		  <option value="INTERNAL" <?php if ($obj->LetterType == "INTERNAL") echo "selected"; ?> >داخلی</option>
		  <option value="ISSUED" <?php if ($obj->LetterType == "ISSUED") echo "selected"; ?> >صادره</option>
		</select>&emsp;
		<span title="تعیین شماره‌ی نامه یا تاریخ نامه سبب تغییر پروژه به 'کارهای ارسالی از سامانه‌ی مکاتبات' می‌شود.">تاریخ نامه:</span>
		<input maxlength="2" id="LetterDate_DAY"  name="LetterDate_DAY" type="text" style="width: 35px;" value="<?php echo $LtDay; ?>"> /
		<input maxlength="2" id="LetterDate_MONTH"  name="LetterDate_MONTH" type="text" style="width: 35px;" value="<?php echo $LtMonth; ?>"> /
		<input maxlength="2" id="LetterDate_YEAR" name="LetterDate_YEAR" type="text" style="width: 35px;" value="<?php echo $LtYear; ?>"> 
		<a href=# onclick='javascript: window.open("calendar.php?FormName=f1&DayInputName=LetterDate_DAY&MonthInputName=LetterDate_MONTH&YearInputName=LetterDate_YEAR")'><img style='vertical-align: middle;' title='انتخاب از تقویم' width=20 src='images/calendar.gif' border=0></a>
	</td>
</tr>
<?php } ?>

<?php
if(isset($_REQUEST["UpdateID"]) && $pc->HasWriteAccessOnOneItemAtLeast)
if(($member["AccessType"]!='PMMANAGER') && ($member["AccessType"]!='MEMBER')  && ($member["AccessType"]!='VIEWER') )
{ ?>
<tr>

	<td colspan=2>	
	<input type=checkbox name='study' id='study'   <? if($study=="YES" ) echo "checked"; ?> >
                                                                                                                                                  نیاز به بررسی توسط کارشناس مدیریت فرآیندها				
	</td>
	

</tr>
<?php } ?>



</table>
<fieldset class="x-fieldset x-form-label-left"  style="border-color: #99BBE8;width:85%;curser:pointer;margin-right:20px;">
<legend >
<table><tr>
<td class='blueText'><div id='imgCloseB' style='cursor:pointer' onclick='CloseBField();'>&nbsp;گزینه های بیشتر<img  src='images/up.gif'    width='15px' height='15px' align='middle'></div>
    <div id='imgOpenB' style='cursor:pointer' onclick='OpenBField();'>&nbsp;گزینه های بیشتر<img  src='images/down.gif'   width='15px' height='15px' align='middle'></div>
</td></tr></table>
</legend>

<div id='B'>
<table  width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 برنامه مرتبط
	</td>
	<td nowrap>
	<span id=ProgramLevelSpan name=ProgramLevelSpan>
	<? if($pc->GetPermission("ProgramLevelID")=="WRITE") { ?>
		<select name="Item_ProgramLevelID" id="ProgramLevelID">
		<option value=0>-
		<? echo manage_ProjectTasks::GetRelatedProgramLevels($CurProjectID); ?>	
		</select>
	<? } else if($pc->GetPermission("ProjectTaskTypeID")=="READ") { ?>
	<span id="Item_ProgramLevelID" name="Item_ProgramLevelID"></span> 	<? } ?>
	</span>
	</td>
</tr>
<? if($pc->GetPermission("TaskGroupID")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 گروه کار
	</td>
	<td nowrap>
	<? if($pc->GetPermission("TaskGroupID")=="WRITE") { ?>
	<span id=TaskGroupIDSpan name=TaskGroupIDSpan>
		<select name="Item_TaskGroupID" id="Item_TaskGroupID">
		<option value=0>-
		<? echo manage_ProjectTaskGroups::CreateSelectOptions(0); ?>	
		</select>
	</span>
	<? } else if($pc->GetPermission("TaskGroupID")=="READ") { ?>
	<span id=TaskGroupIDSpan name=TaskGroupIDSpan><span id="Item_TaskGroupID" name="Item_TaskGroupID"></span></span> 	<? } ?>
	</td>
</tr>
<? } ?>
<?if($pc->GetPermission("PeriodType")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 پریود انجام
	</td>
	<td nowrap>
	<? if($pc->GetPermission("PeriodType")=="WRITE") { ?>
	<select name="Item_PeriodType" id="Item_PeriodType" >
		<option value='ONCE'>یکبار</option>
		<option value='EVERYDAY'>روزانه</option>
		<option value='EVERYWEEK'>هفتگی</option>
		<option value='EVERYMONTH'>ماهانه</option>
	</select>
	<? } else if($pc->GetPermission("PeriodType")=="READ") { ?>
	<span id="Item_PeriodType" name="Item_PeriodType"></span> 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("CountOfDone")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 تعداد دفعات انجام
	</td>
	<td nowrap>
	<? if($pc->GetPermission("CountOfDone")=="WRITE") { ?>
	<input type="text" name="Item_CountOfDone" id="Item_CountOfDone" maxlength="3" size="3" value="1">
	<? } else if($pc->GetPermission("CountOfDone")=="READ") { ?>
	<span id="Item_CountOfDone" name="Item_CountOfDone"></span> 
	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("EstimatedStartTime")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 زمان تخمینی شروع
	</td>
	<td nowrap>
	<? if($pc->GetPermission("EstimatedStartTime")=="WRITE") { ?>
	<input maxlength="3" id="EstimatedStartTime_DAY"  name="EstimatedStartTime_DAY" type="text" size="2">/
	<input maxlength="2" id="EstimatedStartTime_MONTH"  name="EstimatedStartTime_MONTH" type="text" size="2" >/
	<input maxlength="2" id="EstimatedStartTime_YEAR" name="EstimatedStartTime_YEAR" type="text" size="2" > 
	<a href=# onclick='javascript: window.open("calendar.php?FormName=f1&DayInputName=EstimatedStartTime_DAY&MonthInputName=EstimatedStartTime_MONTH&YearInputName=EstimatedStartTime_YEAR")'><img style='vertical-align: middle;' title='انتخاب از تقویم' width=20 src='images/calendar.gif' border=0></a>
	<? } else if($pc->GetPermission("EstimatedStartTime")=="READ") { ?>
	<span id="EstimatedStartTime_DAY" name="EstimatedStartTime_DAY"></span>/
 	<span id="EstimatedStartTime_MONTH" name="EstimatedStartTime_MONTH"></span>/
 	<span id="EstimatedStartTime_YEAR" name="EstimatedStartTime_YEAR"></span>
 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("EstimatedRequiredTimeDay")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 زمان مورد نیاز
	</td>
	<td nowrap>
	<? if($pc->GetPermission("EstimatedRequiredTimeDay")=="WRITE") { ?>
	<input type="text" name="Item_EstimatedRequiredTimeDay" id="Item_EstimatedRequiredTimeDay" maxlength="2" size="2"> روز و 
	<input type="text" name="Item_EstimatedRequiredTimeHour" id="Item_EstimatedRequiredTimeHour" maxlength="3" size="3"> ساعت و
	<input type="text" name="Item_EstimatedRequitedTimeMin" id="Item_EstimatedRequitedTimeMin" maxlength="2" size="2"> دقیقه
	<? } else if($pc->GetPermission("EstimatedRequiredTimeDay")=="READ") { ?>
	<span id="Item_EstimatedRequiredTimeDay" name="Item_EstimatedRequiredTimeDay"></span>  روز و
	<span id="Item_EstimatedRequiredTimeHour" name="Item_EstimatedRequiredTimeHour"></span> ساعت و
	<span id="Item_EstimatedRequitedTimeMin" name="Item_EstimatedRequitedTimeMin"></span> دقیقه 
	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("RealStartTime")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 زمان واقعی شروع
	</td>
	<td nowrap>
	<? if($pc->GetPermission("RealStartTime")=="WRITE") { ?>
	<input maxlength="2" id="RealStartTime_DAY"  name="RealStartTime_DAY" type="text" size="2">/
	<input maxlength="2" id="RealStartTime_MONTH"  name="RealStartTime_MONTH" type="text" size="2" >/
	<input maxlength="2" id="RealStartTime_YEAR" name="RealStartTime_YEAR" type="text" size="2" >
	<a href=# onclick='javascript: window.open("calendar.php?FormName=f1&DayInputName=RealStartTime_DAY&MonthInputName=RealStartTime_MONTH&YearInputName=RealStartTime_YEAR")'><img style='vertical-align: middle;' title='انتخاب از تقویم' width=20 src='images/calendar.gif' border=0></a>
	<? } else if($pc->GetPermission("RealStartTime")=="READ") { ?>
	<span id="RealStartTime_DAY" name="RealStartTime_DAY"></span>/
 	<span id="RealStartTime_MONTH" name="RealStartTime_MONTH"></span>/
 	<span id="RealStartTime_YEAR" name="RealStartTime_YEAR"></span>
 	<? } ?>
	</td>
</tr>
<? } ?>

<? if($pc->GetPermission("HasExpireTime")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 مهلت اقدام دارد؟
	</td>
	<td nowrap>
	<? if($pc->GetPermission("HasExpireTime")=="WRITE") { ?>
	<select name="Item_HasExpireTime" id="Item_HasExpireTime" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>		
	</select>
	<? } else if($pc->GetPermission("HasExpireTime")=="READ") { ?>
	<span id="Item_HasExpireTime" name="Item_HasExpireTime"></span> 	
	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("ExpireTime")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 مهلت اقدام
	</td>
	<td nowrap>
	<? if($pc->GetPermission("ExpireTime")=="WRITE") { ?>
	<input maxlength="2" id="ExpireTime_DAY"  name="ExpireTime_DAY" type="text" size="2">/
	<input maxlength="2" id="ExpireTime_MONTH"  name="ExpireTime_MONTH" type="text" size="2" >/
	<input maxlength="2" id="ExpireTime_YEAR" name="ExpireTime_YEAR" type="text" size="2" >
	<a href=# onclick='javascript: window.open("calendar.php?FormName=f1&DayInputName=ExpireTime_DAY&MonthInputName=ExpireTime_MONTH&YearInputName=ExpireTime_YEAR")'><img style='vertical-align: middle;' title='انتخاب از تقویم' width=20 src='images/calendar.gif' border=0></a>
	<? } else if($pc->GetPermission("ExpireTime")=="READ") { ?>
	<span id="ExpireTime_DAY" name="ExpireTime_DAY"></span>/
 	<span id="ExpireTime_MONTH" name="ExpireTime_MONTH"></span>/
 	<span id="ExpireTime_YEAR" name="ExpireTime_YEAR"></span>
 	<? } ?>
	</td>
</tr>
<? } ?>

</table>
</div>
</fieldset>
<table <?php if(!isset($_REQUEST["UpdateID"])) echo "width='600px'"; ?>>
<tr>
	<td width="1%" nowrap>
<?php
	$SelectedItem = ((int) $obj->TaskPeriority == 0)? '3': (int) $obj->TaskPeriority;
	if($pc->GetPermission("TaskPeriority") != "NONE") echo "اولویت:";
?>
	</td>
	<td>
		<table>
		<tr>
			<td nowrap>
<?php
	if($pc->GetPermission("TaskPeriority") == "WRITE")
	{
?>
	<select name="Item_TaskPeriority" id="Item_TaskPeriority" title="عدد کوچکتر، اولویت بیشتر را نشان می‌دهد.">
		<?php
			for ($i = 1; $i <= 30; $i++)
				if ($i == $SelectedItem)
					echo "<option value='$i' selected='selected'>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
				else
					echo "<option value='$i'>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
		?>
	</select>
<?php
	} else	if($pc->GetPermission("TaskPeriority") != "NONE")
			echo "$SelectedItem <span id='Item_TaskPeriority' name='Item_TaskPeriority'></span>";
?>
			</td>
			<td width="1%" nowrap style="padding-right: 65px;">
<?php
	if($pc->GetPermission("ProjectTaskTypeID") != "NONE") echo "نوع کار:";
?>
			</td>
			<td nowrap>
<?php if($pc->GetPermission("ProjectTaskTypeID") == "WRITE") { ?>
			<span id=TaskTypesSpan name=TaskTypesSpan>
				<select name="Item_ProjectTaskTypeID" id="Item_ProjectTaskTypeID">
				<option value=0>-
				<? echo manage_ProjectTaskTypes::CreateSelectOptions(0, 0); ?>	
				</select>
			</span>
<?php } else echo "<span id=TaskTypesSpan name=TaskTypesSpan><div name='Item_ProjectTaskTypeID' id='Item_ProjectTaskTypeID'></div></span>"; ?>
			</td>
   </tr>
  </table>
 </td>
<?php if(isset($_REQUEST["UpdateID"])){ ?>
	<td style="padding-right: 20px;">
	تاریخ ایجاد: 
	</td>
	<td>
		<?php echo $CreateDate; ?>
	</td>
	<td rowspan="4" style="padding-right: 20px;">
	ایجاد کننده: 
	</td>
	<td rowspan="4">
		<img width=80 src='ShowPersonPhoto.php?PersonID=<?php echo $obj->CreatorID ?>'>
		<?php echo "<br /><div style='text-align: center;'>" . $Creator_FullName . "</div>"; ?>
	</td>
<?php } ?>
</tr>
<? if($pc->GetPermission("TaskStatus")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 وضعیت:
	</td>
	<td nowrap>
	<? if($pc->GetPermission("TaskStatus")=="WRITE" ) {?>
	<select name="Item_TaskStatus" id="Item_TaskStatus" onchange='javascript: if(document.getElementById("Item_TaskStatus").value!="NOT_START") document.getElementById("SendTR").style.display=""; else document.getElementById("SendTR").style.display="none"; '>
		<option value='NOT_START'>اقدام نشده</option>
		<option value='PROGRESSING'>در دست اقدام</option>
		<option value='READY_FOR_TEST'>آماده برای کنترل</option>
		<option value='DONE'>اقدام شده</option>
		<option value='SUSPENDED'>معلق</option>
		<option value='REPLYED'>پاسخ داده شده</option>
		<!--<option value='CONFWAIT'>منتظرتایید</option>		
		<option value='EXECUTECONF'>تاییدجهت اجرا</option>
		<option value='NOCONF'>عدم تایید</option>-->


	</select>
	<? } else if($pc->GetPermission("TaskStatus")=="READ") { 
		if($obj->study=="NO"){ //echo $obj->study;?>
	<span id="Item_TaskStatus" name="Item_TaskStatus"></span> 	
	<? }
else if($obj->study=="YES"){//echo 'TEST';
?>
	<select name="Item_TaskStatus" id="Item_TaskStatus" onchange='javascript: if(document.getElementById("Item_TaskStatus").value!="NOT_START") document.getElementById("SendTR").style.display=""; else document.getElementById("SendTR").style.display="none"; '>
		<option value='NOT_START'>اقدام نشده</option>
		<option value='PROGRESSING'>در دست اقدام</option>
		<option value='READY_FOR_TEST'>آماده برای کنترل</option>
		<option value='DONE'>اقدام شده</option>
		<option value='SUSPENDED'>معلق</option>
		<option value='REPLYED'>پاسخ داده شده</option>
		<option value='CONFWAIT'>منتظرتایید</option>		
		<option value='EXECUTECONF'>تاییدجهت اجرا</option>
		<option value='NOCONF'>عدم تایید</option>

	</select>

<?}
} ?>


	</td>
<?php if(isset($_REQUEST["UpdateID"])){ ?>
	<td style="padding-right: 20px;">
	تاریخ اتمام: 
	</td>
	<td>
		<?php echo $DoneDate; ?>
	</td>
<?php } ?>
</tr>
<tr id="SendTR" style="display: none">
	<td width="1%" nowrap colspan=2>
 	<input type=checkbox name=SendLetter> ارسال نامه آگاهی دهنده برای ایجاد کننده کار
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
	کنترل کننده:
	</td>
	<td nowrap>
	<input type=hidden name="Item_ControllerID" id="Item_ControllerID" value="<?php echo $_SESSION["PersonID"] ?>">
	<span id="Span_PersonID2_FullName" name="Span_PersonID2_FullName"><b>خودم</b></span>
	<? if($pc->GetPermission("ControllerID")=="WRITE") { ?>
	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_ControllerID&SpanName=Span_PersonID2_FullName<?php if($CurProjectID!="0") echo "&ProjectID=".$CurProjectID ?>");'>[انتخاب]</a>
	<? } ?>
	</td>
<?php if(isset($_REQUEST["UpdateID"])){ ?>
	<td style="padding-right: 20px;">
	زمان مصرفی: 
	</td>
	<td>
		<?php echo floor($ret["TotalTime"]/60).":".str_pad($ret["TotalTime"]%60, 2, '0', STR_PAD_LEFT); ?>
	</td>
<?php } ?>
</tr>

<?php if(!isset($_REQUEST["UpdateID"])) { ?>
<tr>
	<td>مجری: </td>
	<td>
	<input type=hidden name="Item_PersonID" id="Item_PersonID" value="<?php echo $_SESSION["PersonID"] ?>" >
	<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"><b>خودم</b></span>
	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName&ProjectID="+document.getElementById("Item_ProjectID").value);'>[انتخاب]</a>
	</td>
</tr>
<tr>
	<td>سند ضمیمه: </td>
	<td>
	<input type="file" name="Item_FileContent" id="Item_FileContent">
	</td>
</tr>

<?php } ?> 
<? } ?>
<? if(isset($_REQUEST["UpdateID"]) && $pc->HasWriteAccessOnOneItemAtLeast)	{ ?>
<tr>
	<td  width="11%" nowrap>
	دلیل بروزرسانی:
	</td>
	<td>
		<input type="text" name="UpdateReason" id="UpdateReason" value="" size=30>
	</td>
	<td style="padding-right: 20px;">
	درصد پیشرفت: 
	</td>
	<td>
		<?php echo min((int) $ret["TotalProgress"], 100); ?>
	</td>
</tr>
<?php } ?>
<?php 
if(isset($_REQUEST["UpdateID"]))
{
?>

<?php } ?>

</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<? if($pc->HasWriteAccessOnOneItemAtLeast) { ?>
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
<? } ?>
 <input type="button" onclick="javascript: window.close();" value="بستن">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>

function decodeHTMLEntities(text) {
    var entities = [
        ['amp', '&'],
        ['apos', '\''],
        ['#x27', '\''],
        ['#x2F', '/'],
        ['#39', '\''],
        ['#47', '/'],
        ['lt', '<'],
        ['gt', '>'],
        ['nbsp', ' '],
        ['quot', '"']
    ];

    for (var i = 0, max = entities.length; i < max; ++i) 
        text = text.replace(new RegExp('&'+entities[i][0]+';', 'g'), entities[i][1]);

    return text;
}

	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}

	function getHTTPObject() 
	{
	  var xmlhttp;
	  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') 
	  {
	    try 
	    {
	      xmlhttp = new XMLHttpRequest();
	    } 
	    catch (e) 
	    {
	      xmlhttp = false;
	    }
	  }
	   return xmlhttp;
	}
	
	var http; // We create the HTTP Object
	var SelectedID;
	
	function handleHttpResponse()
	{
	  if(this.readyState==4)
	    {
	    	document.getElementById('TaskTypesSpan').innerHTML = this.responseText;
	    	//alert(http.responseText);
	    }
	}

	function handleHttpResponse2()
	{
	  if(this.readyState==4)
	    {
	    	document.getElementById('ProgramLevelSpan').innerHTML = this.responseText;
	    	//alert(http.responseText);
	    }
	}

	function handleHttpResponse3()
	{
	  if(this.readyState==4)
	    {
	    	document.getElementById('TaskGroupIDSpan').innerHTML = this.responseText;
	    	//alert(http.responseText);
	    }
	}
	
	function updateTaskTypesList(ProjectID) 
	{
	  http = getHTTPObject();
	  http.open("GET", "GetTaskTypesList.php?<?php if(isset($_REQUEST["UpdateID"])) echo "CurValue=".$obj->ProjectTaskTypeID."&"; ?>ProjectID="+ProjectID, true);
	  http.onreadystatechange = handleHttpResponse;
	  http.send(null);
	}

	function updateTaskGroupsList(ProjectID) 
	{
	  http = getHTTPObject();
	  http.open("GET", "GetTaskGroupsList.php?<?php if(isset($_REQUEST["UpdateID"])) echo "CurValue=".$obj->TaskGroupID."&"; ?>ProjectID="+ProjectID, true);
	  http.onreadystatechange = handleHttpResponse3;
	  http.send(null);
	}
	
	function updateProgramLevelList(ProjectID) 
	{
	  http = getHTTPObject();
	  http.open("GET", "GetProjectRelatedPrograms.php?<?php if(isset($_REQUEST["UpdateID"])) echo "CurValue=".$obj->ProgramLevelID."&"; ?>ProjectID="+ProjectID, true);
	  http.onreadystatechange = handleHttpResponse2;
	  http.send(null);
	}
	
	/*document.getElementById('A').style.display = 'none';
        document.getElementById('imgCloseA').style.display = 'none';
        document.getElementById('imgOpenA').style.display = 'block';*/
        
        document.getElementById('B').style.display = 'none';
        document.getElementById('imgCloseB').style.display = 'none';
        document.getElementById('imgOpenB').style.display = 'block';

        
        /*function CloseAField()
        {
                document.getElementById('A').style.display = 'none';
                document.getElementById('imgCloseA').style.display = 'none';
                document.getElementById('imgOpenA').style.display = 'block';
        } 
        function OpenAField()
        {
                document.getElementById('A').style.display = 'block';	
                document.getElementById('imgCloseA').style.display = 'block';
                document.getElementById('imgOpenA').style.display = 'none';
        } */
        function CloseBField()
        {
                document.getElementById('B').style.display = 'none';
                document.getElementById('imgCloseB').style.display = 'none';
                document.getElementById('imgOpenB').style.display = 'block';
        } 
        function OpenBField()
        {
                document.getElementById('B').style.display = 'block';	
                document.getElementById('imgCloseB').style.display = 'block';
                document.getElementById('imgOpenB').style.display = 'none';
        }



</script>

<?php
//------------------------------
// * Task Referring System *
// Programmer:	Masoud Shariati
// Creation Date:	95-09
//------------------------------

// ($_SESSION["User"]->PersonID == 401371457 || $_SESSION["User"]->PersonID == 201309 || $_SESSION["User"]->PersonID ==401371430) && 
if(($pc->GetPermission("View_TaskRefers") == 'YES' /*|| $_SESSION["User"]->PersonID == 401371457*/) && isset($obj->ProjectTaskID) && trim($obj->ProjectTaskID) != '')
{
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);
	error_reporting(0);

// Showing Refer Graph ...

?>

	<style>
	/* Tooltip container */
	.tooltip {
	    position: relative;
	    display: inline-block;
	    //border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
	}
	
	/* Tooltip text */
	.tooltip .tooltiptext {
	    visibility: hidden;
	    width: 500px;
	    background-color: #418A89;
	    color: #fff;
	    text-align: justify;
	    padding: 10px;
	    border-radius: 6px;
	 
	    /* Position the tooltip text - see examples below! */
	    position: absolute;
	    right: 120px;
	    top: 20px;
	    z-index: 1;
	    border: 1px solid goldenrod;
	}
	
	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltip:hover .tooltiptext {
	    visibility: visible;
	}
	</style>

<?php

	// Getting the sorted list ...

	$RefersList = manage_ProjectTasks::GetTaskRefers($obj->ProjectTaskID);

	if (count($RefersList) > 0)
	{
		class PLStack
		{
			public $List;
			public $LastReferedTimes;

			public function PLStack()
			{
				$this->List = array();
				$this->LastReferedTimes = array();
			}
			
			public function Push($PL)
			{
				$this->List[count($this->List)] = $PL;

				if (!isset($this->LastReferedTimes[$PL->PersonID]))
					$this->LastReferedTimes[$PL->PersonID] = 0;
				$DateTime = strtotime($PL->DateTime);
				if ($this->LastReferedTimes[$PL->PersonID] <= $DateTime)
 					$this->LastReferedTimes[$PL->PersonID] = $DateTime;
			}

			public function Pop()
			{
				if (count($this->List) > 0)
				{
					$PL = $this->List[count($this->List) - 1];
					array_splice($this->List, -1);
					return $PL;
				}
				else
					return false;
			}

			public function Top()
			{
				if (count($this->List) > 0)
					return $this->List[count($this->List) - 1];
				else
					return false;
			}

			public function LastRefered($PL)
			{
				return strtotime($PL->DateTime) == $this->LastReferedTimes[$PL->PersonID];
			}
		}

		$PLStack = new PLStack();

		class PersonLevel
		{
			public $PersonID;
			public $FullName;
			public $Level;
			public $ReferrerPersonID;
			public $DateTime;
			public $Date;
			public $Time;
			public $Description;
			public $Visited;

			public function PersonLevel($PersonID, $Level, $ReferrerPersonID, $DateTime, $Date, $Time, $Description)
			{
				$this->PersonID = $PersonID;
				$this->Level = $Level;
				$this->ReferrerPersonID = $ReferrerPersonID;
				$this->DateTime = $DateTime;
				$this->Date = $Date;
				$this->Time= $Time;
				$this->Description = $Description;
			}
		}

		$PLStack->Push(new PersonLevel($RefersList[0]['FromPerson'], 0, 0, "", "", "", "شروع کننده"));
		
		for($ReferIndex = 0; $ReferIndex < count($RefersList); $ReferIndex++)
		{
			$TempPLStack = new PLStack();
			while ($PLStack->Top() && ($PLStack->Top()->PersonID != $RefersList[$ReferIndex]['FromPerson'] || !$PLStack->LastRefered($PLStack->Top())))
				$TempPLStack->Push($PLStack->Pop());
			if ($PLStack->Top() === false) $PLStack->Push(new PersonLevel($RefersList[$ReferIndex]['FromPerson'], 0, 0, "", "", "", "شروع کننده"));
			
			$PersonLevel = new PersonLevel($RefersList[$ReferIndex]['ToPerson'], $PLStack->Top()->Level + 1, $RefersList[$ReferIndex]['FromPerson'], $RefersList[$ReferIndex ]['DateTime'], $RefersList[$ReferIndex ]['Date'], $RefersList[$ReferIndex ]['Time'], $RefersList[$ReferIndex]['Description']);

			while ($TempPLStack->Top() && $TempPLStack->Top()->Level >= $PersonLevel->Level)
				$PLStack->Push($TempPLStack->Pop());
			
			$PLStack->Push($PersonLevel);
			
			while ($TempPLStack->Top())
				$PLStack->Push($TempPLStack->Pop());
				
		}
	}


// Showing Task Referring UI ...

	if ($pc->GetPermission("Add_TaskRefers") == 'YES')
	{
	?>

<script>

	function TR_Save()
	{
		var TR_Exec = document.f2.Item_PersonID.value.trim();
		var TR_Desc = document.f2.TRDesc.value.trim();
		var TR_Rmv = document.f2.RemoveFromCartable.checked;

		if (TR_Exec != '')
		{
			if (TR_Exec != <?php echo $_SESSION["User"]->PersonID; ?>)
				if (TR_Desc != '' || confirm("شرح ارجاع را مشخص نکرده‌اید. آیا خالی بماند؟"))
					{ Refer(<?php echo $obj->ProjectTaskID; ?>, TR_Exec, TR_Desc, TR_Rmv); }
			else
				alert("فردی غیر از خود را به عنوان مجری انتخاب کنید.");
		}
		else
			alert("مجری ارجاع را مشخص نکرده‌اید!");
	}

	function Refer(TaskID, Executor, Description, Remove)
	{
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200)
			{
				try { window.opener.location.href = window.opener.location.href; } catch(err) {};
				if (document.f2.RemoveFromCartable.checked)
					window.close();
				else
					window.location = window.location.pathname + "?UpdateID=" + <?php echo $obj->ProjectTaskID; ?> + "&TRDesc=" + document.f2.TRDesc.value.trim();
			}
		};
		xhttp.open("POST", "NewProjectTasks.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		var Params = "TaskID=" + TaskID + "&Executor=" + Executor + "&Description=" + Description + "&Remove=" + Remove;
		xhttp.send(Params);
	}

</script>

<br />
<table width="96%" align="center" border=1 cellspacing=1 cellpadding=5>
<tr class="HeaderOfTable">
<td style="text-align: center;">
ارجاع کار
</td>
</tr>
<tr>
	<td><br /><form name="f2" method="post">
	مجری: 
	<input type=hidden name="Item_PersonID" id="Item_PersonID">
	<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span>
	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&FormName=f2&SpanName=Span_PersonID_FullName&ProjectID="+document.getElementById("Item_ProjectID").value);'>[انتخاب]</a>
	<br /><br />
	<div style="">شرح ارجاع:</div>
	<textarea name="TRDesc" rows="3" cols="40" maxlength=315><?php echo (isset($_REQUEST["TRDesc"]))? htmlspecialchars($_REQUEST["TRDesc"]) : ''; ?></textarea>
	<br /><div style="color: rgb(114, 106, 106); padding-right: 4px;">* شرح ارجاع محدودیت 315 حرفی دارد.</div><br /><br />
	<input type="checkbox" name="RemoveFromCartable" checked>حذف از کارتابل
	</form>
	</td>
</tr>
<tr class="FooterOfTable">
<td style="text-align: center;">
	<input onclick="javascript: TR_Save();" value="ارجاع" type="button">
</td>
</tr>
</table>
	<?php
	}
}

?>

<script>

function SetVisited()
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200)
			try {if (window.opener.location.href.indexOf("ReferredTasks") > -1) window.opener.location.href = window.opener.location.href; } catch(err) {}
	};
	xhttp.open("POST", "NewProjectTasks.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	var Params = "TaskID=<?php echo $obj->ProjectTaskID; ?>&Visited=YES";
	xhttp.send(Params);
}

var SVT = setTimeout(SetVisited, 5000);
window.onbeforeunload = function() {
    clearTimeout(SVT);
    return null;
}

</script>

</html>