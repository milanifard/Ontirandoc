<?php
include("header.inc.php");
HTMLBegin();
$mysql = dbclass::getInstance();
$res = $mysql->Execute("select * from formsgenerator.FormManagers JOIN formsgenerator.FormsStruct using (FormsStructID) where PersonID='".$_SESSION["PersonID"]."' and (AccessType='FULL' or AccessType='DATA') order by FormTitle"); 
echo "<br>";
echo "<form id=f1 name=f1 method=post>"; 
echo "<table width=98% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";

echo "	<td width=40%>عنوان فرم</td>";
echo "	<td width=30%>عنوان مرحله</td>";
echo "	<td width=1% nowrap>تعداد فرم</td>";
echo "	<td width=1% nowrap>دریافت فرمها</td>";

echo "</tr>";
$k = 0;
while($rec = $res->FetchRow())
{
	$res2 = $mysql->Execute("select StepTitle, FormsFlowStepID, (select count(*) from FormsRecords where FormsRecords.FormFlowStepID=FormsFlowSteps.FormsFlowStepID) as RecordsCount from formsgenerator.FormsFlowSteps where FormsStructID='".$rec["FormsStructID"]."'");
	while($rec2 = $res2->FetchRow())
	{
		$k++;
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "	<td>".$rec["FormTitle"]."</td><td>".$rec2["StepTitle"];
		echo "	</td>";
		echo "	<td>".$rec2["RecordsCount"]."</td>";
		echo "	<td><a href='DownloadExcelFile.php?FormFlowStepID=".$rec2["FormsFlowStepID"]."'><img src='images/excel.jpg' border=0 title='دریافت فرمها'></a></td>";
		echo "</tr>";
	}
}
echo "</table>";
echo "</form>";
?>
</html>