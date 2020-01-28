<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormFields.class.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FieldsItemList.class.php");
include_once("classes/SecurityManager.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
include_once("classes/FormsDetailTables.class.php");
//require_once('../organization/classes/ChartServices.class.php');
HTMLBegin();
$mysql = pdodb::getInstance();
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<br>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<br>
<?
	// باید چک شود ایجاد کننده فرم فقط بتواند این صفحه را فراخوانی کند
	$CurForm = new be_FormsStruct();
	$CurForm->LoadDataFromDatabase($_REQUEST["SelectedFormStructID"]);
	$CurStep = new be_FormsFlowSteps();
	$CurStep->LoadDataFromDatabase(-1);
	if(!manage_FormsStruct::HasUserCreateThisQuestionnaire($_REQUEST["SelectedFormStructID"], $_REQUEST["RelatedRecordID"]))
	{
		echo "به پرسشنامه دسترسی ندارید";
		die();
	}
	if(isset($_REQUEST["ActionType"]))
	{
		if($_REQUEST["ActionType"]=="SEND" || $_REQUEST["ActionType"]=="CONFIRM")
		{
			$CurForm->UpdateData($_REQUEST["RelatedRecordID"], -1, $_SESSION["PersonID"]);
			echo "<script>document.getElementById('MessageSpan').innerHTML='<font color=green>اطلاعات ذخیره شد</font>';\r\n";
			echo "setTimeout(\"document.getElementById('MessageSpan').innerHTML='';\", 3000)";
			echo "</script>";
			
		}
		if($_REQUEST["ActionType"]=="CONFIRM")
		{
			echo "<script>setTimeout(\"document.location='login.php?logout=1';\", 2000);</script>";
			$mysql->Prepare("update formsgenerator.TemporaryUsersAccessForms set filled='YES' where WebUserID='".$_SESSION["UserID"]."' and FormsStructID=?");
			$mysql->ExecuteStatement(array($_REQUEST["SelectedFormStructID"]));
			die();
		}
	}
	echo $CurForm->CreateUserInterface(-1, $_SESSION["PersonID"], $_REQUEST["RelatedRecordID"]);
	echo "<p align=center><input type=button value='خروج از سیستم' onclick='javascript: document.location=\"login.php?logout=1\"'></p>";
?>
</html>