<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/
include("header.inc.php");
include("classes/UserFacilities.class.php");
include ("classes/SystemFacilities.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_UserID"]))
		$Item_UserID=$_REQUEST["Item_UserID"];
	if(isset($_REQUEST["FacilityID"]))
		$Item_FacilityID=$_REQUEST["FacilityID"];
	manage_UserFacilities::Add($Item_UserID, $Item_FacilityID);
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
?>
<form method="post" id="f1" name="f1" >
<?
echo manage_SystemFacilities::ShowSummary($_REQUEST["FacilityID"]);
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">اضافه کردن دسترسی کاربر</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 کاربر
	</td>
	<td nowrap>
	<select name="Item_UserID" id="Item_UserID">
	<option value=0>-
	<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.AccountSpecs", "UserID", "UserID", "UserID"); ?>	</select>
	</td>
</tr>
<input type="hidden" name="FacilityID" id="FacilityID" value='<? if(isset($_REQUEST["FacilityID"])) echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>'>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
<input type="button" onclick="window.close();" value="بستن">
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
$res = manage_UserFacilities::GetList($_REQUEST["FacilityID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FacilityPageID])) 
	{
		manage_UserFacilities::Remove($res[$k]->FacilityPageID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_UserFacilities::GetList($_REQUEST["FacilityID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_FacilityID" name="Item_FacilityID" value="<? echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="5">
	کاربران دارای دسترسی
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">&nbsp;</td>
	<td width="1%">ردیف</td>
	<td>کاربر</td>
	<td>امکان</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->FacilityPageID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td>".$res[$k]->UserID_Desc."</td>";
	echo "	<td>".htmlentities($res[$k]->FacilityID, ENT_QUOTES, 'UTF-8')."</td>";
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
<form target="_blank" method="post" action="NewUserFacilities.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="FacilityID" name="FacilityID" value="<? echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
