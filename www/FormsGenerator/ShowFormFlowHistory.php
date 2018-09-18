<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormFields.class.php");
include("classes/FormsFlowSteps.class.php");
include("classes/FieldsItemList.class.php");
include("classes/FormUtils.class.php");
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