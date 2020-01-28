<?php
include("header.inc.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/StepPermittedEduGrps.class.php");
include_once("classes/FormUtils.class.php");
HTMLBegin();
$ParentObj = new be_FormsFlowSteps();
$ParentObj->LoadDataFromDatabase($_REQUEST["FormFlowStepID"]);
if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_StepPermittedEduGroups::Add($_REQUEST["FormFlowStepID"]
				, $_REQUEST["Item_EduGrpCode"]
				);
	}	
	else 
	{	
		manage_StepPermittedEduGroups::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_EduGrpCode"]
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_StepPermittedEduGroups();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_EduGrpCode.value='".$obj->EduGrpCode."'; \r\n "; 
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
<tr class=HeaderOfTable><td align=center>تعریف زیرگروه های مجاز به دسترسی به مرحله <b><?php echo $ParentObj->StepTitle ?></b></td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=FormFlowStepID id=FormFlowStepID value='<? echo $_REQUEST["FormFlowStepID"]; ?>'>
<tr id=tr_UnitID name=tr_UnitID style='display:'>
<td width=1% nowrap>
	گروه آموزشی: 
</td>
<td nowrap>
	<select name=Item_EduGrpCode id=Item_EduGrpCode>
		<option value=0>-
		<?php echo FormUtils::CreateEduGrpsOptions(""); ?>
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
 $k=0;
$ListCondition = " FormFlowStepID='".$_REQUEST["FormFlowStepID"]."' ";

$res = manage_StepPermittedEduGroups::GetList($ListCondition." order by PEduName"); 
echo "<form id=f2 name=f2 method=post>"; 
?>
<input type=hidden name=FormFlowStepID id=FormFlowStepID value='<? echo $_REQUEST["FormFlowStepID"]; ?>'>
<?php 
echo "<br><table width=80% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%> </td>";
echo "	<td>گروه آموزشی</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->StepPermittedEduGroupsID])) 
	{
		manage_StepPermittedEduGroups::Remove($res[$k]->StepPermittedEduGroupsID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->StepPermittedEduGroupsID."></td>";
		echo "	<td>".$res[$k]->EduGrpName."</td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=4 align=center><input type=submit value='حذف'></tr>";
echo "</table>";
echo "</form>";
?>
</html>