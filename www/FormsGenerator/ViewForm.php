<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormFields.class.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FieldsItemList.class.php");
include_once("classes/FormUtils.class.php");
include_once("classes/SecurityManager.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
include_once("classes/FormsDetailTables.class.php");
require_once('../organization/classes/ChartServices.class.php');
HTMLBegin();
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<br>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<br>
<?
	// کد مرحله مربوط به رکورد داده را بدست می آورد
	$CurStepID = FormUtils::GetCurrentStepID($_REQUEST["RelatedRecordID"], $_REQUEST["SelectedFormStructID"]);
	
	// برای اطمینان از امنیت بایستی یک بار دیگر در صفحه چک شود آیا دسترسی کاربر به این رکورد داده مجاز است یا خیر 
	if(!SecurityManager::HasUserAccessToThisRecord($_SESSION["PersonID"], $CurStepID, $_REQUEST["RelatedRecordID"]))
	{
		echo ":)";
		die();
	}
	//echo date("l");
	$CurForm = new be_FormsStruct();
	$CurForm->LoadDataFromDatabase($_REQUEST["SelectedFormStructID"]);
	$CurStep = new be_FormsFlowSteps();
	$CurStep->LoadDataFromDatabase($CurStepID);
	if(isset($_REQUEST["ActionType"]))
	{
		if($_REQUEST["ActionType"]=="SEND")
		{
			$CurForm->UpdateData($_REQUEST["RelatedRecordID"], $CurStep->FormsFlowStepID, $_SESSION["PersonID"]);
			// اگر مرحله انتخابی کاربر غیر از مرحله فعلی داده باشد تغییر مرحله هم اتفاق می افتد
			// ممکن است مراحل بازگشتی هم داشته باشیم
			if($_REQUEST["NewStepID"]!="0")
			{
				$CurForm->SendData($_REQUEST["RelatedRecordID"], $CurStepID, $_REQUEST["NewStepID"], $_SESSION["PersonID"]);
				$CurStep->LoadDataFromDatabase($_REQUEST["NewStepID"]);
			}
			echo "<script>document.getElementById('MessageSpan').innerHTML='<font color=green>اطلاعات ذخیره و به مرحله مورد نظر ارسال شد</font>';</script>";
			echo "<script>setTimeout(\"document.getElementById('MessageSpan').innerHTML='';\", 1500);</script>";
		}
	}
	
	// چک می کند کاربر به این مرحله دسترسی دارد یا خیر دلیل چک دوباره این است که بعد از بروز شدن و تغییر مرحله ممکن است کاربر به مرحله جدید دسترسی نداشته باشد
	// زیرا سیستم به صورت اتومات بعد از ارسال فرم به مرحله بعد سعی می کند دوباره آن را در مرحله جدید و در مود ویرایش باز کند
	if(!SecurityManager::HasUserAccessToThisRecord($_SESSION["PersonID"], $CurStep->FormsFlowStepID, $_REQUEST["RelatedRecordID"]))
	{
		die();
	}
	//echo "+".$CurStep->FormsFlowStepID."+";
	// نمایش اینترفیس فرم که داده مورد نظر را با توجه به مرحله آن نمایش می دهد
	echo $CurForm->CreateUserInterface($CurStep->FormsFlowStepID, $_SESSION["PersonID"], $_REQUEST["RelatedRecordID"]);
?>
</html>