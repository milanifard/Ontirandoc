<?php
include("header.inc.php");
include_once("classes/FieldsItemList.class.php");
HTMLBegin();
$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
$FormsStructID = $_REQUEST["FormsStructID"];
$FormFieldID = "0";
if(isset($_REQUEST["FormFieldID"]))
{
	$FormFieldID = $_REQUEST["FormFieldID"];
}
if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FieldsItemList::Add($_REQUEST["Item_FormFieldID"]
				, $_REQUEST["Item_ItemValue"]
				, $_REQUEST["Item_ItemDescription"]
				);
	}	
	else 
	{	
		manage_FieldsItemList::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_ItemValue"]
				, $_REQUEST["Item_ItemDescription"]
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
if(isset($_REQUEST["CopyFieldID"]))
{
	$mysql->Prepare("select * from FieldsItemList where FormFieldID='".$_REQUEST["CopyFieldID"]."'");
	$res = $mysql->ExecuteStatement(array());
	while($rec = $res->fetch())
	{
		manage_FieldsItemList::Add($_REQUEST["Item_FormFieldID"],
									$rec["ItemValue"],
									$rec["ItemDescription"]);
	}	
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FieldsItemList();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_ItemValue.value='".$obj->ItemValue."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ItemDescription.value='".$obj->ItemDescription."'; \r\n "; 
}	
?>
<form method=post id=f1 name=f1>
<input type=hidden name=FormsStructID id=FormsStructID value='<?php echo $_REQUEST["FormsStructID"] ?>'>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width=50% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش آیتمهای لیست برای گزینه های از نوع لیستی</td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=Item_FormFieldID id=Item_FormFieldID value='<? echo $_REQUEST["FormFieldID"]; ?>'>
<tr id=tr_ItemValue name=tr_ItemValue style='display:'>
<td width=1% nowrap>
	مقدار آیتم
</td>
<td nowrap>
	<input type=text name=Item_ItemValue id=Item_ItemValue>
</td>
</tr>
<tr id=tr_ItemDescription name=tr_ItemDescription style='display:'>
<td width=1% nowrap>
	شرح آیتم
</td>
<td nowrap>
	<input type=text name=Item_ItemDescription id=Item_ItemDescription>
</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center>
<input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
&nbsp;
<input type=button onclick='javascript: window.close();' value='بستن'>
</td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form>
<?php
 $k=0;
$ListCondition = " FormFieldID='".$_REQUEST["FormFieldID"]."' ";
$res = manage_FieldsItemList::GetList($ListCondition); 
echo "<form id=f3 name=f3 method=post>";
echo "<input type=hidden name=FormsStructID value=".$_REQUEST["FormsStructID"].">";
echo "<input type=hidden name=FormFieldID value=".$_REQUEST["FormFieldID"].">"; 
echo "<br><table width=50% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%>&nbsp;</td>";
echo "	<td width=2%>کد</td>";
echo "	<td width=10%>مقدار </td>";
echo "	<td>شرح </td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FieldItemListID])) 
	{
		manage_FieldsItemList::Remove($res[$k]->FieldItemListID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FieldItemListID."></td>";
		echo "	<td><a href='CreateSelectOptions.php?FormFieldID=".$_REQUEST["FormFieldID"]."&UpdateID=".$res[$k]->FieldItemListID."'>".$res[$k]->FieldItemListID."</a></td>";
		echo "	<td>".$res[$k]->ItemValue."</td>";
		echo "	<td>".$res[$k]->ItemDescription."</td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=5 align=center><input type=submit value='حذف'></tr>";
echo "</table>";
echo "</form>";
echo "<br>";
$CopyOption = "";
$mysql->Prepare("select FormFieldID, substring(FieldTitle, 1, 80) as FieldTitle from FormFields where FieldType=3 and CreatingListType='STATIC_LIST' and FormsStructID=".$FormsStructID." and FormFieldID<>'".$FormFieldID."' order by FormFieldID DESC");
$res = $mysql->ExecuteStatement(array());
while($rec = $res->fetch())
{
	$CopyOption .= "<option value='".$rec["FormFieldID"]."'>".$rec["FieldTitle"];
}
if($CopyOption!="")
{
	echo "<table width=50% align=center border=1 cellspacing=0><tr><td>";
	echo "<form method=post id=CopyForm name=CopyForm>";
	echo "<input type=hidden name=FormsStructID id=FormsStructID value=".$_REQUEST["FormsStructID"].">";
	echo "<input type=hidden name=Item_FormFieldID id=Item_FormFieldID value=".$_REQUEST["FormFieldID"].">";
	echo "کپی آیتمهای از لیست مربوط به گزینه ";
	echo "<select name=CopyFieldID id=CopyFieldID onchange='document.CopyForm.submit();' style='width: 300px'>";
	echo "<option value=0>-";
	echo $CopyOption;
	echo "</select>";
	echo "</form></td></tr></table>";
}
?>
<script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		if(document.f1.Item_ItemDescription.value=="")
		{
			alert('در بخش شرح مقداری وارد نمایید');
			return;
		}
		document.f1.submit();
	}
</script>
</body>
</html>