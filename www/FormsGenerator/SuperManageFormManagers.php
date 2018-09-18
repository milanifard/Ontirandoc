<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormManagers.class.php");
HTMLBegin();

$ParentObj = new be_FormsStruct();
$ParentObj->LoadDataFromDatabase($_REQUEST["Item_FormStructID"]);

if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FormManagers::Add($_REQUEST["Item_FormStructID"]
				, $_REQUEST["Item_PersonID"]
				, $_REQUEST["AccessType"]
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
?>
<script>
<? echo PersiateKeyboard() ?>
</script>
<form method=post id=f1 name=f1>
<br><table width=80% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>تعریف مدیران فرم  <b><?php echo $ParentObj->FormTitle ?></b></td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=Item_FormStructID id=Item_FormStructID value='<? echo $_REQUEST["Item_FormStructID"]; ?>'>
<tr id=tr_PersonID name=tr_PersonID style='display:'>
<td width=1% nowrap>
	نام مدیر: 
</td>
<td nowrap>

	<input type=hidden name=Item_PersonID id=Item_PersonID>
	<span id=MySpan name=MySpan></span>
	<a href='../organization/SelectStaff.php?InputName=Item_PersonID&SpanName=MySpan' target=_blank>[انتخاب]</a>
</td>
</tr>
<tr>
	<td nowrap>
	نوع دسترسی
	</td>
	<td>
		<select name=AccessType id=AccessType>
			<option value='FULL'>مدیریت ساختار و داده ها
			<option value='DATA'>مدیریت داده
			<option value='STRUCT'>مدیریت ساختار
		</select>
	</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
&nbsp;
<input type=button onclick='javascript: document.location="ManageFormsStruct.php";' value='بازگشت'>
</td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	function ValidateForm()
	{
		if(document.f1.Item_PersonID.value=='0' || document.f1.Item_PersonID.value=='')
			alert('باید شخصی انتخاب شود');
		else 
			document.f1.submit();
	}
</script>
<?php
 $k=0;
$ListCondition = " FormsStructID='".$_REQUEST["Item_FormStructID"]."' ";

$res = manage_FormManagers::GetList($ListCondition); 
echo "<form id=f2 name=f2 method=post>"; 
?>
<input type=hidden name=Item_FormStructID id=Item_FormStructID value='<? echo $_REQUEST["Item_FormStructID"]; ?>'>
<?php 
echo "<br><table width=80% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%> </td>";
echo "	<td>نام مدیر</td>";
echo "	<td>نوع دسترسی</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FormManagerID])) 
	{
		manage_FormManagers::Remove($res[$k]->FormManagerID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FormManagerID."></td>";
		echo "	<td>".$res[$k]->PersonName."</td>";
		echo "	<td>";
		if($res[$k]->AccessType=="FULL")
			echo "مدیریت ساختار و داده ها";
		else if($res[$k]->AccessType=="DATA")
			echo "مدیریت داده ها";
		else if($res[$k]->AccessType=="STRUCT")
			echo "مدیریت ساختار";
		echo "</td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=4 align=center><input type=submit value='حذف'></tr>";
echo "</table>";
echo "</form>";
?>
</html>