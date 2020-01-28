<?php
include("header.inc.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FormsStruct.class.php");
HTMLBegin();
echo "<font family='tahoma' size=1px>";
$ShowAccessList = FALSE;
if(isset($_REQUEST["ShowType"]) && $_REQUEST["ShowType"]=="1")
	$ShowAccessList = TRUE;
$CurStruct = new be_FormsStruct();
$CurStruct->LoadDataFromDatabase($_REQUEST["FormsStructID"]);
?>
<table width=90% align=center border=1 cellspacing=0 cellpadding=5>
<tr>
	<td>
		جریان کاری مربوطه به <b><?php echo $CurStruct->FormTitle ?></b> 
		<select name=ShowType onchange='javascript: document.location="PrintFormFlowChart.php?FormsStructID=<?php  echo $_REQUEST["FormsStructID"]; ?>&ShowType="+this.value; '>
			<option value='0'>بدون نمایش شکل دسترسی
			<option value='1' <?php  if(isset($_REQUEST["ShowType"]) && $_REQUEST["ShowType"]=="1") echo "selected"; ?> >با نمایش شکل دسترسی
		</select>	
	</td>
</tr>
</table>
<?
manage_FormsFlowSteps::PrintTree(manage_FormsFlowSteps::GetStartStepID($_REQUEST["FormsStructID"]), $ShowAccessList);
?><!--  -->
</html>