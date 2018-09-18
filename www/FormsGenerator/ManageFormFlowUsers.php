<?php
include("header.inc.php");
include("classes/FormsFlowSteps.class.php");

include("classes/StepPermittedPersons.class.php");
HTMLBegin();
$ParentObj = new be_FormsFlowSteps();
$ParentObj->LoadDataFromDatabase($_REQUEST["FormFlowStepID"]);
if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_StepPermittedPersons::Add($_REQUEST["FormFlowStepID"]
				, $_REQUEST["Item_PersonID"]
				);
	}	
	else 
	{	
		manage_StepPermittedPersons::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_PersonID"]
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_StepPermittedPersons();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_PersonID.value='".$obj->PersonID."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.getElementById('MySpan').innerHTML='".$obj->PersonName."'; \r\n ";
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
<tr class=HeaderOfTable><td align=center>تعریف افراد مجاز به دسترسی به مرحله <b><?php echo $ParentObj->StepTitle ?></b></td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=FormFlowStepID id=FormFlowStepID value='<? echo $_REQUEST["FormFlowStepID"]; ?>'>
<tr id=tr_PersonID name=tr_PersonID style='display:'>
<td width=1% nowrap>
	فرد مجاز: 
</td>
<td nowrap>

	<input type=hidden name=Item_PersonID id=Item_PersonID>
	<span id=MySpan name=MySpan></span>
	<a href='../organization/SelectStaff.php?InputName=Item_PersonID&SpanName=MySpan' target=_blank>[انتخاب]</a>
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

$res = manage_StepPermittedPersons::GetList($ListCondition); 
echo "<form id=f2 name=f2 method=post>"; 
?>
<input type=hidden name=FormFlowStepID id=FormFlowStepID value='<? echo $_REQUEST["FormFlowStepID"]; ?>'>
<?php 
if(isset($_REQUEST["PageNumber"]))
	echo "<input type=hidden name=PageNumber value=".$_REQUEST["PageNumber"].">"; 
echo "<br><table width=80% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%> </td>";
echo "	<td>نام فرد مجاز برای دسترسی</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->StepPermittedPersonsID])) 
	{
		manage_StepPermittedPersons::Remove($res[$k]->StepPermittedPersonsID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->StepPermittedPersonsID."></td>";
		echo "	<td>".$res[$k]->PersonName."</td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=4 align=center><input type=submit value='حذف'></tr>";
echo "</table>";
echo "</form>";
?>
</html>