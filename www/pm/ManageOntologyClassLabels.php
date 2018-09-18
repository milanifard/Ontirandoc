<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : برچسب کلاسها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyClassLabels.class.php");
include ("classes/OntologyClasses.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["OntologyClassID"]))
		$Item_OntologyClassID=$_REQUEST["OntologyClassID"];
	if(isset($_REQUEST["Item_label"]))
		$Item_label=$_REQUEST["Item_label"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_OntologyClassLabels::Add($Item_OntologyClassID
				, $Item_label
				);
	}	
	else 
	{	
		manage_OntologyClassLabels::Update($_REQUEST["UpdateID"] 
				, $Item_label
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$label = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_OntologyClassLabels();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$label = htmlentities($obj->label, ENT_QUOTES, 'UTF-8');; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_OntologyClasses::ShowSummary($_REQUEST["OntologyClassID"]);
echo manage_OntologyClasses::ShowTabs($_REQUEST["OntologyClassID"], "ManageOntologyClassLabels");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش برچسب کلاسها</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="OntologyClassID" id="OntologyClassID" value='<? if(isset($_REQUEST["OntologyClassID"])) echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 برچسب
	</td>
	<td nowrap>
	<textarea name="Item_label" id="Item_label" cols="80" rows="5"><? echo $label; ?></textarea>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageOntologyClassLabels.php?OntologyClassID=<?php echo $_REQUEST["OntologyClassID"]; ?>'" value="جدید">
 <input type="button" onclick="javascript: window.close();" value="بستن">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$res = manage_OntologyClassLabels::GetList($_REQUEST["OntologyClassID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->OntologyClassLabelID])) 
	{
		manage_OntologyClassLabels::Remove($res[$k]->OntologyClassLabelID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_OntologyClassLabels::GetList($_REQUEST["OntologyClassID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_OntologyClassID" name="Item_OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="4">
	برچسب کلاسها
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>برچسب</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyClassLabelID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageOntologyClassLabels.php?UpdateID=".$res[$k]->OntologyClassLabelID."&OntologyClassID=".$_REQUEST["OntologyClassID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->label, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="4" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewOntologyClassLabels.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyClassID" name="OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
