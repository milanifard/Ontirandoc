<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormFields.class.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FieldsItemList.class.php");
include_once("classes/FormUtils.class.php");
require_once('classes/FormsFlowStepRelations.class.php');

HTMLBegin();
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<br>
<?
	echo FormUtils::ShowFormFlowHistory($_REQUEST["SelectedFormStructID"], $_REQUEST["RelatedRecordID"]);
	echo "<br>";
	echo FormUtils::ShowUpdateHistory($_REQUEST["SelectedFormStructID"], $_REQUEST["RelatedRecordID"]);
	
?>
</html>