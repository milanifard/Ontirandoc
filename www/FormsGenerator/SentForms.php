<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormFields.class.php");
include("classes/FormsFlowSteps.class.php");
include("classes/FieldsItemList.class.php");
include("classes/FormUtils.class.php");
include("classes/SecurityManager.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
require_once('../organization/classes/ChartServices.class.php');
HTMLBegin();

$FromRec = 0;
$ItemsPerPage = 10;
$PageNumber = 0;
$TotalCount = FormUtils::GetSentFormsCount($_SESSION["PersonID"]);
if(isset($_REQUEST["PageNumber"]))
{
	$FromRec = $_REQUEST["PageNumber"]*$ItemsPerPage;
        $PageNumber = $_REQUEST["PageNumber"];
}
else
{
	$FromRec = 0;
}
$list = FormUtils::GetSentForms($_SESSION["PersonID"], $FromRec, $ItemsPerPage);
?>
<form id="f1" name="f1" method=post>
    <input type=hidden name='PageNumber' id='PageNumber' value=0>
<br><table width=80% align=center border=1 cellspacing=0 cellpadding=5>
<tr class=HeaderOfTable><td colspan=7><b>فرمهای ارسالی</b></td></tr>
<tr bgcolor=#cccccc><td width=1%>کد</td><td>فرم</td><td>ایجاد کننده</td>
<!-- <td>از مرحله</td><td>به مرحله</td><td>زمان ارسال</td> -->
<td>مرحله فعلی</td><td>سابقه</td></tr>
<?php 
	for($j=0; $j<count($list); $j++)
	{
		echo "<tr>";
		echo "<td width=10%>";
		echo $list[$j]->RecID;
		echo "</td>";
		echo "<td nowrap>".$list[$j]->FormTitle."</td>";
		echo "<td nowrap>".$list[$j]->CreatorName."</td>";
		//echo "<td nowrap>".$list[$j]->FromStep."</td>";
		//echo "<td nowrap>".$list[$j]->ToStep."</td>";
		//echo "<td nowrap>".$list[$j]->SendDate."</td>";
		echo "<td nowrap>".$list[$j]->CurrentStep."</td>";
		echo "<td nowrap><a target=_blank href='ShowFormFlowHistory.php?SelectedFormStructID=".$list[$j]->FormsStructID."&RelatedRecordID=".$list[$j]->RecID."'>سابقه</a></td>";
		echo "</tr>";
	}
?>
<tr bgcolor="#cccccc"><td colspan="9" align="right">
<?
for($k=0; $k< $TotalCount/$ItemsPerPage; $k++)
{
	if($PageNumber!=$k)
		echo "<a href='javascript: ShowPage(".($k).")'>";
	echo ($k+1);
	if($PageNumber!=$k)
		echo "</a>";
	echo " ";
}
?>

</td></tr>
</table></form>
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
        function ShowPage(PageNumber)
{
        document.f1.PageNumber.value=PageNumber;
	document.f1.submit();
}
</script>
</html>