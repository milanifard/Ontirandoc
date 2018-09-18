<?php
include("header.inc.php");

include("classes/FormsStruct.class.php");
include("classes/FormFields.class.php");
include("classes/FormsFlowSteps.class.php");
include("classes/FieldsItemList.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
include("classes/FormsDetailTables.class.php");
include("classes/SecurityManager.class.php");
//require_once('../organization/classes/ChartServices.class.php');
HTMLBegin();
$mysql = pdodb::getInstance();
if(!isset($_REQUEST["SelectedFormStructID"]))
{
	echo "SelectedFormStructID=?";
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

	$query = "select * from formsgenerator.TemporaryUsersAccessForms where WebUserID='".$_SESSION["UserID"]."' and FormsStructID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($_REQUEST["SelectedFormStructID"]));
	if($rec = $res->fetch())
	{
		if($rec["filled"]=="YES") // در حالتیکه پرسشنامه پر و تایید شده باشد
		{
			echo "قبلا پرسشنامه را پر کرده اید";
			die();
		}
		else
		{
			$res = $mysql->Execute("select * from formsgenerator.QuestionnairesCreators where UserID='".$_SESSION["UserID"]."' and FormsStructID='".$CurForm->FormsStructID."'");
			if($rec = $res->fetch()) 			// اگر از قبل پرسشنامه تکمیل نشده داشته باشد کاربر را به صفحه ویرایش پرسشنامه می فرستد
			{
				$RecID = $rec["RelatedRecordID"]; 
			?>
			<form id=f2 name=f2 method=post action='ViewQuestionnaire.php'>
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
				ViewForm(<?php echo $CurForm->FormsStructID ?>, -1, <?php echo $RecID ?>);
			</script>
			<?php
			} 
		}
	}
	else
	{
		echo "مجوز پر کردن این پرسشنامه را ندارید";
		die();
	}
	
	if(isset($_REQUEST["ActionType"]))
	{
		// وقتی کاربر تایید نهایی کرده باشد فرم ارسال شده و دفعه بعدی که کاربر وارد شود نمی تواند آن را تغییر دهد
		if($_REQUEST["ActionType"]=="CONFIRM")
		{
			$mysql->Prepare("update formsgenerator.TemporaryUsersAccessForms set filled='YES' where WebUserID='".$_SESSION["UserID"]."' and FormsStructID=?");
			$mysql->ExecuteStatement(array($_REQUEST["SelectedFormStructID"]));
		}
		
		if($_REQUEST["ActionType"]=="CONFIRM")
		{
			$RecID = $CurForm->AddData(-1, $_SESSION["PersonID"]);

			$mysql->Prepare("insert into formsgenerator.QuestionnairesCreators (UserID, FormsStructID, RelatedRecordID) values('".$_SESSION["UserID"]."', ?, '".$RecID."')");
			$mysql->ExecuteStatement(array($_REQUEST["SelectedFormStructID"]));
			
			echo "<script>document.getElementById('MessageSpan').innerHTML='<font color=green>اطلاعات ذخیره شد</font>';</script>";
			//echo "<script>setTimeout(\"document.getElementById('MessageSpan').innerHTML='';\", 1500);</script>";
			echo "<p align=center><a href='login.php'>خروج از سیستم</a></p>";
			die();
		}
		else if($_REQUEST["ActionType"]=="SAVE_GO_EDIT" || $_REQUEST["ActionType"]=="SEND")
		{
			$RecID = $CurForm->AddData(-1, $_SESSION["PersonID"]);
			$CurForm->SendData($RecID, 0, -1, $_SESSION["PersonID"]);
			$mysql->Prepare("insert into formsgenerator.QuestionnairesCreators (UserID, FormsStructID, RelatedRecordID) values('".$_SESSION["UserID"]."', ?, '".$RecID."')");
			$mysql->ExecuteStatement(array($_REQUEST["SelectedFormStructID"]));
			
			?>
			<form id=f2 name=f2 method=post action='ViewQuestionnaire.php'>
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
				ViewForm(<?php echo $CurForm->FormsStructID ?>, -1, <?php echo $RecID ?>);
			</script>
		<?php 
		}
}
echo $CurForm->CreateUserInterface(-1, $_SESSION["PersonID"], 0);
	echo "<p align=center><input type=button value='خروج از سیستم' onclick='javascript: document.location=\"login.php?logout=1\"'></p>";
?>
</html>