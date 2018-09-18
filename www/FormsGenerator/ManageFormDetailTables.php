<?php
include("header.inc.php");
include("classes/FormsDetailTables.class.php");
include("classes/FormsStruct.class.php");
HTMLBegin();
echo "*";
$ParentObj = new be_FormsStruct();
$ParentObj->LoadDataFromDatabase($_REQUEST["Item_FormStructID"]);
if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FormsDetailTables::Add($_REQUEST["Item_FormStructID"]
				, $_REQUEST["Item_DetailFormStructID"]
				, $_REQUEST["Item_RelatedField"]
				, $_REQUEST["Item_OrderNo"]
				);
	}	
	else 
	{	
		manage_FormsDetailTables::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_DetailFormStructID"]
				, $_REQUEST["Item_RelatedField"]
				, $_REQUEST["Item_OrderNo"]
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FormsDetailTables();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_DetailFormStructID.value='".$obj->DetailFormStructID."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_RelatedField.value='".$obj->RelatedField."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_OrderNo.value='".$obj->OrderNo."'; \r\n "; 
}	
?>

<form method=post id=f1 name=f1>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>جداول جزییات مربوط به <b><?php echo $ParentObj->FormTitle ?></b></td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=Item_FormStructID id=Item_FormStructID value='<? echo $_REQUEST["Item_FormStructID"]; ?>'>
<tr id=tr_DetailFormStructID name=tr_DetailFormStructID style='display:'>
<td width=1% nowrap>
	جدول جزییات
</td>
<td nowrap>
	<select name=Item_DetailFormStructID id=Item_DetailFormStructID>
		<?php echo manage_FormsStruct::CreateFormsStructOptions(); ?>
	</select>
</td>
</tr>
<tr id=tr_RelatedField name=tr_RelatedField style='display:'>
<td width=1% nowrap>
	<span style="vertical-align:middle; font-size:12px;">نام فیلد ارتباطی با کلید جدول اصلی</span>
</td>
<td nowrap>
	<span style="vertical-align:middle; font-size:12px;">
	<input dir=ltr type=text name=Item_RelatedField id=Item_RelatedField>
	</span>
	<a href=# onclick='javascript: window.open("SelectFormItem.php?FormStructID="+document.f1.Item_DetailFormStructID.value);' >
	<img style="vertical-align:middle;" src='images/SelectField.gif' width=30 title='انتخاب' border=0 valign=buttom></a>
</td>
</tr>
<tr id=tr_OrderNo name=tr_OrderNo style='display:'>
<td width=1% nowrap>
	شماره ترتیب
</td>
<td nowrap>
	<input type=text name=Item_OrderNo id=Item_OrderNo dir=ltr size=3 maxlength=3>
</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
&nbsp;<input type=button value='جدید' onclick='javascript: document.location="ManageFormDetailTables.php?Item_FormStructID=<?php echo $_REQUEST["Item_FormStructID"] ?>";'>
&nbsp;<input type=button value='بازگشت' onclick='javascript: document.location="ManageFormsStruct.php";'>
</td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form>
<script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?
$res = manage_FormsDetailTables::GetList($_REQUEST["Item_FormStructID"]); 
echo "<form id=f2 name=f2 method=post>"; 
echo "<input type=hidden name=Item_FormStructID id=Item_FormStructID value='".$_REQUEST["Item_FormStructID"]."'>"; 
echo "<br><table width=90% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%> </td>";
echo "	<td width=2%>کد</td>";
echo "	<td>جدول</td>";
echo "	<td nowrap>نام فیلد ارتباطی با کلید جدول اصلی</td>";
echo "	<td width=1%>ترتیب</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FormsDetailTableID])) 
	{
		manage_FormsDetailTables::Remove($res[$k]->FormsDetailTableID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FormsDetailTableID."></td>";
		echo "	<td><a href='ManageFormDetailTables.php?Item_FormStructID=".$_REQUEST["Item_FormStructID"]."&UpdateID=".$res[$k]->FormsDetailTableID."'>".$res[$k]->FormsDetailTableID."</a></td>";
		echo "	<td>".$res[$k]->FormTitle."</td>";
		echo "	<td>".$res[$k]->RelatedField."</td>";
		echo "	<td>".$res[$k]->OrderNo."</td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=6 align=center><input type=submit value='حذف'></tr>";
echo "</table>";
echo "</form>";
?>
</html>