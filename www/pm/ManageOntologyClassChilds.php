<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : سلسله مراتب کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyClassHirarchy.class.php");
include ("classes/OntologyClasses.class.php");
HTMLBegin();
$mysql = pdodb::getInstance();
$obj = new be_OntologyClasses();
$obj->LoadDataFromDatabase($_REQUEST["OntologyClassID"]);
$OntologyID = $obj->OntologyID;
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["OntologyClassID"]))
		$Item_OntologyClassID=$_REQUEST["OntologyClassID"];
	if(isset($_REQUEST["Item_OntologyClassParentID"]))
		$Item_OntologyClassParentID=$_REQUEST["Item_OntologyClassParentID"];
	manage_OntologyClassHirarchy::Add($Item_OntologyClassID
				, $Item_OntologyClassParentID
				);
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
?>
<form method="post" id="f1" name="f1" >
<?
echo manage_OntologyClasses::ShowSummary($_REQUEST["OntologyClassID"]);
echo manage_OntologyClasses::ShowTabs($_REQUEST["OntologyClassID"], "ManageOntologyClassHirarchy");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">اضافه کردن کلاس فرزند</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<input type="hidden" name="OntologyClassID" id="OntologyClassID" value='<? if(isset($_REQUEST["OntologyClassID"])) echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>'>
<tr>
	<td width="1%" nowrap>
 کلاس فرزند
	</td>
	<td nowrap>
	<select name="Item_OntologyClassParentID" id="Item_OntologyClassParentID">
	<option value=0>-
	  <?
	    $list = manage_OntologyClasses::GetList($obj->OntologyID);
	    for($i=0; $i<count($list); $i++)
	    {
	      echo "<option value='".$list[$i]->OntologyClassID."'>";
	      echo $list[$i]->label." (".$list[$i]->ClassTitle.")";
	    }
	?>
	</select>
	<a onclick='javascript: window.open("ShowOntologyClassTree.php?ReturnID=1&InputName=Item_OntologyClassParentID&OntologyID=<? echo $OntologyID;  ?>")' href="#">انتخاب</a>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="اضافه">
 <input type="button" onclick="javascript: window.close();" value="بستن">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$res = manage_OntologyClassHirarchy::GetList($_REQUEST["OntologyClassID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->OntologyClassHirarchyID])) 
	{
		manage_OntologyClassHirarchy::Remove($res[$k]->OntologyClassHirarchyID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
  $res = manage_OntologyClassHirarchy::GetList($_REQUEST["OntologyClassID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_OntologyClassID" name="Item_OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="5">
	کلاسهای فرزند
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td>کلاس</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyClassHirarchyID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td>".$res[$k]->OntologyClassParentID_Desc."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="5" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewOntologyClassHirarchy.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyClassID" name="OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
