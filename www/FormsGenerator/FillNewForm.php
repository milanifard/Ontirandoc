<?php
include("header.inc.php");

include_once("classes/FormsStruct.class.php");
include_once("classes/FormFields.class.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FieldsItemList.class.php");
include_once("classes/FormUtils.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
include_once("classes/FormsDetailTables.class.php");
include_once("classes/SecurityManager.class.php");
require_once('../organization/classes/ChartServices.class.php');
/*
if($_SESSION["UserID"]!="omid")
{
	echo "در دست آماده سازی";
	die();
}
*/
HTMLBegin();
if(!isset($_REQUEST["SelectedFormStructID"]))
{
$res = SecurityManager::GetUserPermittedFormsForStart($_SESSION["PersonID"]);
echo "<br><table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
echo "<tr bgcolor=#cccccc><td><b>انتخاب فرم جهت پر کردن و ارسال</b></td></tr>";
for($i=0; $i<count($res); $i++)
{
	echo "<tr><td><a href='#' onclick='javascript: SendForm(".$res[$i]->FormsStructID.");'>";
	echo $res[$i]->FormTitle;
	echo "</a></td></tr>";
}
echo "</table>";
?>
<form id=f2 name=f2>
	<input type=hidden name='SelectedFormStructID' id='SelectedFormStructID' value=0>
</form>
<script>
	function SendForm(FormID)
	{
		document.f2.SelectedFormStructID.value=FormID;
		f2.submit();
	}
</script>
<?php
	die(); 
	}
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<br>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<br>
<?
	$CurForm = new be_FormsStruct();
	$CurForm->LoadDataFromDatabase($_REQUEST["SelectedFormStructID"]);
	$StepList = SecurityManager::GetUserPermittedStepsInAFormForStart($_SESSION["PersonID"], $_REQUEST["SelectedFormStructID"]);
	if(count($StepList)==1)
	{
		$CurStep = $StepList[0];
		if(isset($_REQUEST["ActionType"]))
		{
			if($_REQUEST["ActionType"]=="SEND")
			{
				$RecID = $CurForm->AddData($CurStep->FormsFlowStepID, $_SESSION["PersonID"]);
				$NewStepID = $_REQUEST["NewStepID"];
				if($NewStepID=="0")
					$NewStepID = $StepList[0]->FormsFlowStepID; // در صورتیکه مقدار پاس شده برای مرحله جدید صفر باشد یعنی همین مرحله جاری مد نظر است
				// چون مرحله ایجاد است باید الزاما به مرحله ای هم ارسال شود
				$StepList = SecurityManager::GetUserPermittedStepsInAFormForStart($_SESSION["PersonID"], $_REQUEST["SelectedFormStructID"]);
				$CurForm->SendData($RecID, 0, $NewStepID, $_SESSION["PersonID"]);
				echo "<script>document.getElementById('MessageSpan').innerHTML='<font color=green>اطلاعات ذخیره و به مرحله مورد نظر ارسال شد</font>';</script>";
				echo "<script>setTimeout(\"document.getElementById('MessageSpan').innerHTML='';\", 1500);</script>";
			}
			else if($_REQUEST["ActionType"]=="SAVE_GO_EDIT")
			{
				$RecID = $CurForm->AddData($CurStep->FormsFlowStepID, $_SESSION["PersonID"]);
				// به صورت اتومات فرم را در همین مرحله فعلی - مرحله شروع - قرار می دهد
				$CurForm->SendData($RecID, 0, $CurStep->FormsFlowStepID, $_SESSION["PersonID"]);
				?>
				<form id=f2 name=f2 method=post action='ViewForm.php'>
					<input type=hidden name='FormFlowStepID' id='FormFlowStepID' value=0>
					<input type=hidden name='RelatedRecordID' id='RelatedRecordID' value=0>
					<input type=hidden name='SelectedFormStructID' id='SelectedFormStructID' value=0>
				</form>
				<script>
					function ViewForm(FormsStructID, FormFlowStepID, RelatedRecordID)
					{
						document.f2.FormFlowStepID.value=FormFlowStepID;
						document.f2.RelatedRecordID.value=RelatedRecordID;
						document.f2.SelectedFormStructID.value=FormsStructID;
						f2.submit();
					}
					ViewForm(<?php echo $CurForm->FormsStructID ?>, <?php echo $CurStep->FormsFlowStepID ?>, <?php echo $RecID ?>);
				</script>
			<?php 
			}
		}
		echo $CurForm->CreateUserInterface($CurStep->FormsFlowStepID, $_SESSION["PersonID"], 0);
	}
	else
	{
		// در صورتیکه بیش از یک مرحله شروع داشت باید یکی از آن مراحل انتخاب شود
		// این بخش بعدا تکمیل شود
		echo "این فرم بیش از یک مرحله برای ایجاد دارد";
	}
?>
</html>