<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormFields.class.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FieldsItemList.class.php");
include_once("classes/FormUtils.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
include_once("classes/FormsDetailTables.class.php");

HTMLBegin();
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<br>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<?php 
	$FormStructID = 47; // کد ساختار فرمی که قرار است داده های آن اضافه یا ویرایش و یا حذف شود
	$CurForm = new be_FormsStruct();
	$CurForm->LoadDataFromDatabase($FormStructID);

	$RelatedRecordID = 0;
	if(isset($_REQUEST["RelatedRecordID"]))
		$RelatedRecordID = $_REQUEST["RelatedRecordID"];
	
	if(isset($_REQUEST["ActionType"]))
	{
		if($_REQUEST["ActionType"]=="REMOVE")
		{
			$CurForm->RemoveData($RelatedRecordID, $_SESSION["PersonID"]);
			echo "<script>window.opener.document.location.reload();</script>";
			echo "<script>window.close();</script>";
		}
		if($_REQUEST["ActionType"]=="SEND")
		{
			if($RelatedRecordID==0)
			{
				$RecID = $CurForm->AddData(-1, $_SESSION["PersonID"]);
				echo "<script>document.getElementById('MessageSpan').innerHTML='<font color=green>اطلاعات ذخیره شد</font>';</script>";
				echo "<script>setTimeout(\"document.getElementById('MessageSpan').innerHTML='';\", 1500);</script>";
			}
			else
			{
				$CurForm->UpdateData($RelatedRecordID, -1, $_SESSION["PersonID"]);
				echo "<script>window.opener.document.location.reload();</script>";
				echo "<script>window.close();</script>";
			}
		}
		else if($_REQUEST["ActionType"]=="SAVE_GO_EDIT")
		{
			$RecID = $CurForm->AddData(-1, $_SESSION["PersonID"]);
			?>
			<form id=f2 name=f2 method=post action='NewDataRecord.php'>
				<input type=hidden name='RelatedRecordID' id='RelatedRecordID' value='<?php echo $RelatedRecordID ?>'>
				<input type=hidden name='SelectedFormStructID' id='SelectedFormStructID' value=0>
			</form>
			<script>
				function ViewForm(FormsStructID, RelatedRecordID)
				{
					document.f2.RelatedRecordID.value=RelatedRecordID;
					document.f2.SelectedFormStructID.value=FormsStructID;
					f2.submit();
				}
				ViewForm(<?php echo $CurForm->FormsStructID ?>, <?php echo $RecID ?>);
			</script>
		<?php 
		}
	}
	echo $CurForm->CreateUserInterface(-1, $_SESSION["PersonID"], $RelatedRecordID);
?>
</html>