<?php
include("header.inc.php");
include("classes/FileTypeForms.class.php");
include("classes/FileTypes.class.php");
include("classes/FormUtils.class.php");
HTMLBegin();
$ParentObj = new be_FileTypes();
$ParentObj->LoadDataFromDatabase($_REQUEST["FileTypeID"]);
$mandatory = "NO";
if(isset($_REQUEST["mandatory"]))
	$mandatory = "YES";
if(isset($_REQUEST["Save"]))
{
	manage_FileTypeForms::Add($_REQUEST["FileTypeID"]
			, $_REQUEST["Item_UnitID"]
			, $mandatory
			);
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
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
<tr class=HeaderOfTable><td align=center>تعریف فرمهای مجاز برای <b><?php echo $ParentObj->FileTypeName ?></b></td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=FileTypeID id=FileTypeID value='<? echo $_REQUEST["FileTypeID"]; ?>'>
<tr id=tr_PersonID name=tr_PersonID style='display:'>
<td width=1% nowrap>
	فرم: 
</td>
<td nowrap>
	<select name=Item_UnitID id=Item_UnitID>
		<?php echo FormUtils::CreateItemsListAccordingToQuery("select FormsStructID, FormTitle from FormsStruct", ""); ?>
	</select>
</td>
</tr>
<tr>
	<td colspan=2>
	<input type=checkbox name=mandatory id=mandatory> وجود این فرم در پرونده الزامی است
	</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
&nbsp;
<input type=button onclick='javascript: document.location="ManageFileTypes.php";' value='بازگشت'>
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
$res = manage_FileTypeForms::GetList($_REQUEST["FileTypeID"]); 
echo "<form id=f2 name=f2 method=post>"; 
?>
<input type=hidden name=id id=id value='<? echo $_REQUEST["FileTypeID"]; ?>'>
<?php 
echo "<br><table width=80% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%> </td>";
echo "	<td>فرم مجاز</td>";
echo "<td>وجود فرم در پرونده</tD>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FileTypeFormID])) 
	{
		manage_FileTypeForms::Remove($res[$k]->FileTypeFormID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FileTypeFormID."></td>";
		echo "	<td>".$res[$k]->FormTitle."</td>";
		
		echo "<td>";
		if($res[$k]->mandatory=="YES")
			echo "الزامی";
		else
			echo "اختیاری";
		echo "</td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=4 align=center><input type=submit value='حذف'></tr>";
echo "</table>";
echo "</form>";
?>
</html>