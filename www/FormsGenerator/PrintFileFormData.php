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
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<br>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<br>
<?
	// کد مرحله مربوط به رکورد داده را بدست می آورد
	$CurStepID = FormUtils::GetCurrentStepID($_REQUEST["RelatedRecordID"], $_REQUEST["SelectedFormStructID"]);
	$CurForm = new be_FormsStruct();
	$CurForm->LoadDataFromDatabase($_REQUEST["SelectedFormStructID"]);
	$CurStep = new be_FormsFlowSteps();
	$CurStep->LoadDataFromDatabase($CurStepID);
	// نمایش نسخه قابل چاپ فرم که داده مورد نظر را با توجه به مرحله آن نمایش می دهد
	echo $CurForm->CreatePrintableVersion($CurStep->FormsFlowStepID, $_SESSION["PersonID"], $_REQUEST["RelatedRecordID"], 0, 0, $_REQUEST["FileTypeUserPermittedFormID"], 0);
?>
</html>