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
//require_once('../organization/classes/ChartServices.class.php');
HTMLBegin();
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<?
	$CurForm = new be_FormsStruct();
	$CurForm->LoadDataFromDatabase($_REQUEST["DetailFormID"]);
	if(isset($_REQUEST["ForPrint"]))
	{
		echo $CurForm->CreateListOfDetailData($_REQUEST["RelatedRecordID"], $_REQUEST["SelectedFormStructID"], $_SESSION["PersonID"], TRUE);
	}
	else
	{
		echo $CurForm->CreateListOfDetailData($_REQUEST["RelatedRecordID"], $_REQUEST["SelectedFormStructID"], $_SESSION["PersonID"]);
		echo "<table width=100%><tr><td align=center><input type=button value='ایجاد' onclick='document.f3.submit();'></td></tr></table>";
	}
?>
<form method=post name=f3 id=f3 target=_blank action='NewQuestionnaireDetailRecord.php'>
	<input type=hidden name='MasterFormsStructID' id='MasterFormsStructID' value='<?php echo $_REQUEST["SelectedFormStructID"] ?>'>
	<input type=hidden name='SelectedFormStructID' id='SelectedFormStructID' value='<?php echo $_REQUEST["DetailFormID"] ?>'>
	<input type=hidden name='MasterRecordID' id='MasterRecordID' value='<?php echo $_REQUEST["RelatedRecordID"] ?>'>
</form>
<script>
function ResizeIFrame()
{
		try
		{
		    if (window.parent) // has parent
		    {
			    ifobj = window.frameElement;
			    if (ifobj) // iframe object exists
			    {
			        ifobj.style.height = document.body.scrollHeight;
				    ifobj.height = document.body.scrollHeight;
			    }
		    }
		}
		catch(err)
		{
			
		}
}
//setTimeout("ResizeIFrame()", 10);
ResizeIFrame();
</script>
</html>