<?php
include("header.inc.php");
include("classes/FormsFlowSteps.class.php");
include("classes/FormsFlowStepRelations.class.php");
include("classes/FormUtils.class.php");
include("classes/FormsStruct.class.php");
HTMLBegin();
$ParentObj = new be_FormsFlowSteps();
$ParentObj->LoadDataFromDatabase($_REQUEST["FormFlowStepID"]);
$ParentObj2 = new be_FormsStruct();
$ParentObj2->LoadDataFromDatabase($ParentObj->FormsStructID);

if($ParentObj2->CreatorUser!=$_SESSION["UserID"] && !$ParentObj2->HasThisPersonAccessToManageStruct($_SESSION["PersonID"]))
{
	echo "You don't have permission";
	die();
}

if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FormsFlowStepRelations::Add($_REQUEST["FormFlowStepID"]
				, $_REQUEST["Item_NextStepID"]
				);
	}	
	else 
	{	
		manage_FormsFlowStepRelations::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_NextStepID"]
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FormsFlowStepRelations();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_NextStepID.value='".$obj->NextStepID."'; \r\n "; 
}	
?>
<script>
<? echo PersiateKeyboard() ?>
</script>
<form method=post id=f1 name=f1>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width=80% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>تعریف مراحل بعد از مرحله <b><?php echo $ParentObj->StepTitle ?></b></td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=FormFlowStepID id=FormFlowStepID value='<? echo $_REQUEST["FormFlowStepID"]; ?>'>
<tr id=tr_UnitID name=tr_UnitID style='display:'>
<td width=1% nowrap>
	ایجاد شرایط برای رفتن به مرحله‌:
</td>
<td nowrap>
	<select name=Item_NextStepID id=Item_NextStepID>
		<option value=0>-
		<?php echo manage_FormsFlowSteps::CreateListOptions(" FormsStructID='".$ParentObj->FormsStructID."' "); ?>
	</select>
</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
&nbsp;
<input type=button onclick='javascript: document.location="ManageFormFlow.php?Item_FormStructID=<?php echo $ParentObj->FormsStructID ?>";' value='بازگشت'>
</td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php
function ShowRangeName($Value)
{
	if($Value=="HIM")
		return "خود کاربر";
	if($Value=="UNIT")
		return "واحد ";
	if($Value=="SUB_UNIT")
		return "زیر واحد ";
	if($Value=="EDU_GROUP")
		return "گروه آموزشی";
	if($Value=="BELOW_IN_CHART_ALL_LEVEL")
		return "زیر مجموعه در تمام سطوح";
	if($Value=="BELOW_IN_CHART_LEVEL1")
		return "زیر مجموعه سطح اول";
	if($Value=="BELOW_IN_CHART_LEVEL2")
		return "زیر مجموعه سطح دوم";
	if($Value=="BELOW_IN_CHART_LEVEL3")
		return "زیر مجموعه سطح سوم";
	if($Value=="UNDER_MANAGEMENT")
		return "زیر مجموعه تحت مدیریت";
	return "همه";	
}	

 $k=0;
$ListCondition = " FormFlowStepID='".$_REQUEST["FormFlowStepID"]."' ";

$res = manage_FormsFlowStepRelations::GetList($ListCondition); 
echo "<form id=f2 name=f2 method=post>"; 
?>
<input type=hidden name=FormFlowStepID id=FormFlowStepID value='<? echo $_REQUEST["FormFlowStepID"]; ?>'>
<?php 
echo "<br><table width=80% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%> </td>";
echo "	<td>مرحله</td>";
echo "<td>شرایط</td>";
echo "	<td>محدوده دسترسی کاربران</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FormFlowStepRelationID])) 
	{
		manage_FormsFlowStepRelations::Remove($res[$k]->FormFlowStepRelationID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FormFlowStepRelationID."></td>";
		echo "	<td>".$res[$k]->NextStepName."</td>";
                echo "<td><a href='ManageFormFlowStepRelationDetails.php?FormFlowStepRelationID=".$res[$k]->FormFlowStepRelationID."'>ایجاد شرط</a></td>";
		echo "	<td>".ShowRangeName($res[$k]->NextStepUserAccessRange)."</td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=4 align=center><input type=submit value='حذف'></tr>";
echo "</table>";
echo "</form>";
?>
</html>