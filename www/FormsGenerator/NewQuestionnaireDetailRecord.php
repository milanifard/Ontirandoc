<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormFields.class.php");
include("classes/FormsFlowSteps.class.php");
include("classes/FieldsItemList.class.php");
include("classes/FormUtils.class.php");
include("classes/SecurityManager.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
include("classes/FormsDetailTables.class.php");
require_once('../organization/classes/ChartServices.class.php');
HTMLBegin();
$mysql = pdodb::getInstance();
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<?
	// برای اینکه در مود بروزرسانی فرم نمایش داده شود باید کد رکورد جزییات مربوطه به صفحه پاس شده باشد در غیر اینصورت در مود جدید صفحه باز خواهد شد
	$DetailRecordID = 0;
	if(isset($_REQUEST["RelatedRecordID"]))
		$DetailRecordID = $_REQUEST["RelatedRecordID"];
	
	// نام فیلد کلید خارجی در جدول جزییات
	$RelationField = FormUtils::GetRelationField($_REQUEST["MasterFormsStructID"], $_REQUEST["SelectedFormStructID"]);
		
	$CurForm = new be_FormsStruct();
	// کد ساختار فرم جزییات به این نام به این صفحه پاس می شود
	$CurForm->LoadDataFromDatabase($_REQUEST["SelectedFormStructID"]);
	if(isset($_REQUEST["ActionType"]))
	{
		if($_REQUEST["ActionType"]=="SEND")
		{
			if($DetailRecordID==0)
			{
				// در مود ایجاد رکورد جزییات جدید:
				// ابتدا رکورد جزییات را در جدول خودش ثبت می کند
				$CurID = $CurForm->AddData(-1, $_SESSION["PersonID"]);
				// ثبت کلید خارجی
				$query = "update ".$CurForm->RelatedDB.".".$CurForm->RelatedTable." set ".$RelationField."='".$_REQUEST["MasterRecordID"]."' where ".$CurForm->KeyFieldName."='".$CurID."'";
				$mysql->Execute($query);
				
				// سپس در جدول ارتباط رکوردهای اصلی با رکوردهای جزییات رکوردی ثبت می کند تا ارتباط داده جدید اضافه شده با رکورد اصلی مشخص شود
				$query = "insert into formsgenerator.DetailFormRecords (MasterRecordID,MasterFormsStructID,DetailRecordID,DetailFormsStructID,CreatorID,CreateTime,LastUpdatedPersonID,LastUpdatedTime) 
						  values 
						  ('".$_REQUEST["MasterRecordID"]."','".$_REQUEST["MasterFormsStructID"]."', '".$CurID."', '".$CurForm->FormsStructID."', '".$_SESSION["PersonID"]."', now(), '".$_SESSION["PersonID"]."', now())";   
				$mysql->Execute($query);
				echo "<script>window.opener.document.location.reload(); window.close();</script>";
				die();
			}
			else
			{
				// در مود ویرایش رکورد جزییات:
				// ابتدا رکورد جزییات را در جدول خودش بروز رسانی می کند
				$CurForm->UpdateData($DetailRecordID, -1, $_SESSION["PersonID"]);
				// سپس در رکورد ارتباط رکوردهای جزییات با رکوردهای اصلی کد کاربر بروزرسانی کننده و تاریخ این بروزرسانی را بروز می کند
				$query = "update formsgenerator.DetailFormRecords set LastUpdatedPersonID='".$_SESSION["PersonID"]."' ,LastUpdatedTime=now() where 
							MasterRecordID='".$_REQUEST["MasterRecordID"]."' and MasterFormsStructID='".$_REQUEST["MasterFormsStructID"]."' and DetailRecordID='".$DetailRecordID."' and DetailFormsStructID='".$CurForm->FormsStructID."'";   
				$mysql->Execute($query);
				echo "<script>window.opener.document.location.reload(); window.close();</script>";
				die();
			}
			echo "<script>document.getElementById('MessageSpan').innerHTML='<font color=green>اطلاعات ذخیره و به مرحله مورد نظر ارسال شد</font>';</script>";
			echo "<script>setTimeout(\"document.getElementById('MessageSpan').innerHTML='';\", 1500);</script>";
		}
		else if($_REQUEST["ActionType"]=="REMOVE")
		{
			// بهتر است قبل از حذف یکبار دیگر دسترسی چک شود
			$CurForm->RemoveData($DetailRecordID);
			echo "<script>window.opener.document.location.reload(); window.close();</script>";
			die();
		}
	}
	echo $CurForm->CreateUserInterface(-1, $_SESSION["PersonID"], $DetailRecordID, $_REQUEST["MasterFormsStructID"], $_REQUEST["MasterRecordID"]);
?>
</html>