<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormFields.class.php");
include("classes/FormsFlowSteps.class.php");
include("classes/FieldsItemList.class.php");
include("classes/FormUtils.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
include("classes/FormsDetailTables.class.php");

HTMLBegin();
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<br>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<?php 
	$FormStructID = 47; // کد ساختار فرمی که قرار است داده های آن لیست شود
	$CurForm = new be_FormsStruct();
	$CurForm->LoadDataFromDatabase($FormStructID);
	$condition = ""; // در صورتیکه شرطی برای تولید لیست بود در اینجا گذاشته شود
	$ItemsPerPage = 5;
	$FromRec = 0;
	$PageNumber = 0;
	if(isset($_REQUEST["PageNumber"]))
	{
		$FromRec = $_REQUEST["PageNumber"]*$ItemsPerPage;
		$PageNumber = $_REQUEST["PageNumber"];
	}
	echo $CurForm->CreateListOfData("NewDataRecord.php", $condition, $FromRec, $ItemsPerPage);
	
	$ItemsCount = $CurForm->GetItemsCount($condition);
	echo "<table width=98% border=1 cellspacing=0 cellpadding=5 align=center>";
	echo "<tr><td dir=ltr>";
	for($k=0; $k<$ItemsCount/$ItemsPerPage; $k++)
	{
		if($PageNumber!=$k)
			echo "<a href='javascript: ShowPage(".$k.")'>".($k+1)."</a>";
		else
			echo "<b>".($k+1)."</b>";
		echo "&nbsp;";	
	}
	echo "</td></tr>";
	echo "</table>";
?>
<form method=post name=f2 id=f2>
<input type=hidden name=PageNumber id=PageNumber value=0>
</form>
<script>
	function ShowPage(PageNumber)
	{
		f2.PageNumber.value=PageNumber;
		f2.submit();
	}
</script>
</html>