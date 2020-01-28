<?php
include("header.inc.php");
include_once("classes/FormLabels.class.php");
include_once("classes/FormFields.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FormLabels::Add($_REQUEST["Item_LabelDescription"]
				, $_REQUEST["Item_LocationType"]
				, $_REQUEST["Item_RelatedFieldID"]
				, $_REQUEST["Item_ShowType"]
				, $_REQUEST["Item_ShowHorizontalLine"]
				);
	}	
	else 
	{	
		manage_FormLabels::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_LabelDescription"]
				, $_REQUEST["Item_LocationType"]
				, $_REQUEST["Item_RelatedFieldID"]
				, $_REQUEST["Item_ShowType"]
				, $_REQUEST["Item_ShowHorizontalLine"]
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
	echo "<script>window.opener.document.location='ManageQuestionnaireFields.php?FormsStructID=".$_REQUEST["Item_FormsStructID"]."'</script>";
}
$list = manage_FormFields::GetList($_REQUEST["Item_FormsStructID"]);
$FieldOptions = "";
for($i=0; $i<count($list); $i++)
{
	
	$FieldOptions .= "<option value='".$list[$i]->FormFieldID."'>".$list[$i]->FieldTitle;
}

$LoadDataJavascriptCode = '';
$LabelDescription =  "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FormLabels();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LabelDescription = $obj->LabelDescription;
	$LoadDataJavascriptCode .= "document.f1.Item_LocationType.value='".$obj->LocationType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_RelatedFieldID.value='".$obj->RelatedFieldID."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ShowType.value='".$obj->ShowType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ShowHorizontalLine.value='".$obj->ShowHorizontalLine."'; \r\n "; 
}	
?>
<form method=post id=f1 name=f1>
<input type=hidden name=Item_FormsStructID id=Item_FormsStructID value='<?php echo $_REQUEST["Item_FormsStructID"] ?>'>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش برچسبهای یک فرم</td></tr>
<tr><td>
<table width=100% border=0>
<tr id=tr_LabelDescription name=tr_LabelDescription style='display:'>
<td width=1% nowrap>
	شرح
</td>
<td nowrap>
	<textarea name=Item_LabelDescription id=Item_LabelDescription rows=5 cols=70><?php echo $LabelDescription; ?></textarea>
</td>
</tr>
<tr id=tr_LocationType name=tr_LocationType style='display:'>
<td width=1% nowrap>
	محل قرار گرفتن
</td>
<td nowrap>
	<select name=Item_LocationType id=Item_LocationType>
		<option value='BEFORE'>قبل از
		<option value='AFTER'>بعد از
	</select>
	<select name=Item_RelatedFieldID id=Item_RelatedFieldID>
	<?php echo $FieldOptions	?>
	</select>
</td>
</tr>
<tr id=tr_ShowType name=tr_ShowType style='display:'>
<td width=1% nowrap>
	نحوه نمایش
</td>
<td nowrap>
	<select name=Item_ShowType id=Item_ShowType>
	<option value='SIMPLE'>ساده
	<option value='BOLD'>توپر
	<option value='ITALIC'>زیر خط دار
	</select>
</td>
</tr>
<tr id=tr_ShowHorizontalLine name=tr_ShowHorizontalLine style='display:'>
<td width=1% nowrap>
	خط افقی در زیر برچسب
</td>
<td nowrap>
	<select name=Item_ShowHorizontalLine id=Item_ShowHorizontalLine>
	<option value='NO'>کشیده نشود
	<option value='YES'>کشیده شود
	</select>
</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center>
<input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
&nbsp;
<input type=button onclick='javascript: window.close();' value='بستن'>
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
