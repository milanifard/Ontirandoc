<?php
include("header.inc.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FormsStruct.class.php");
HTMLBegin();
$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
if(isset($_REQUEST["SourceID"]))
{
	$mysql->Execute("delete from FieldsAccessType where FormFlowStepID='".$_REQUEST["FormFlowStepID"]."'");
	$mysql->Execute("insert into FieldsAccessType (FormFieldID, FormFlowStepID, AccessType) select FormFieldID, '".$_REQUEST["FormFlowStepID"]."', AccessType from FieldsAccessType where FormFlowStepID='".$_REQUEST["SourceID"]."'");
	//echo "insert into FieldsAccessType (FormFieldID, FormFlowStepID, AccessType) select FormFieldID, '".$_REQUEST["FormFlowStepID"]."', AccessType from FieldsAccessType where FormFlowStepID='".$_REQUEST["SourceID"]."'";
	echo "<script>document.location='ManageFormFlowFieldsAccess.php?FormFlowStepID=".$_REQUEST["FormFlowStepID"]."'</script>";
	die();
}
$k=0;
$ListCondition = " FormsStructID='".$_REQUEST["FormsStructID"]."' "; 
$res = manage_FormsFlowSteps::GetList($ListCondition); 
echo "<br><table width=50% align=center border=1 cellspacing=0 cellpadding=4>";
echo "<tr class=HeaderOfTable>";
echo "	<td width=2%>کد</td>";
echo "	<td>عنوان</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FormsFlowStepID])) 
	{
		manage_FormsFlowSteps::Remove($res[$k]->FormsFlowStepID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "	<td><a href='CopyAccess.php?SourceID=".$res[$k]->FormsFlowStepID."&FormsStructID=".$_REQUEST["FormsStructID"]."&FormFlowStepID=".$_REQUEST["FormFlowStepID"]."'>".$res[$k]->FormsFlowStepID."</a></td>";
		echo "	<td><a href='CopyAccess.php?SourceID=".$res[$k]->FormsFlowStepID."&FormsStructID=".$_REQUEST["FormsStructID"]."&FormFlowStepID=".$_REQUEST["FormFlowStepID"]."'>&nbsp;".$res[$k]->StepTitle."</a></td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable>";
echo "<td colspan=3 align=center><input type=button value='بازگشت' onclick='javascript: document.location=\"ManageFormFlowFieldsAccess.php?FormFlowStepID=".$_REQUEST["FormFlowStepID"]."\"'></td>";
echo "</tr>";
echo "</table>";
?>
</html>
