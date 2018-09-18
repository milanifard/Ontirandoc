<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/
include("header.inc.php");
//include("../sharedClasses/SharedClass.class.php");
include("classes/SystemFacilities.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_FacilityName"]))
		$Item_FacilityName=$_REQUEST["Item_FacilityName"];
	if(isset($_REQUEST["Item_GroupID"]))
		$Item_GroupID=$_REQUEST["Item_GroupID"];
	if(isset($_REQUEST["Item_OrderNo"]))
		$Item_OrderNo=$_REQUEST["Item_OrderNo"];
	if(isset($_REQUEST["Item_PageAddress"]))
		$Item_PageAddress=$_REQUEST["Item_PageAddress"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_SystemFacilities::Add($Item_FacilityName
				, $Item_GroupID
				, $Item_OrderNo
				, $Item_PageAddress
				);
	}	
	else 
	{	
		manage_SystemFacilities::Update($_REQUEST["UpdateID"] 
				, $Item_FacilityName
				, $Item_GroupID
				, $Item_OrderNo
				, $Item_PageAddress
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_SystemFacilities();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_FacilityName.value='".htmlentities($obj->FacilityName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_GroupID.value='".htmlentities($obj->GroupID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_OrderNo.value='".htmlentities($obj->OrderNo, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_PageAddress.value='".htmlentities($obj->PageAddress, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش امکانات</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 عنوان
	</td>
	<td nowrap>
	<input type="text" name="Item_FacilityName" id="Item_FacilityName" maxlength="245" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 گروه
	</td>
	<td nowrap>
	<select name="Item_GroupID" id="Item_GroupID">
	<option value=0>-
	<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.SystemFacilityGroups", "GroupID", "GroupName", "GroupName"); ?>	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 ترتیب
	</td>
	<td nowrap>
	<input type="text" name="Item_OrderNo" id="Item_OrderNo" maxlength="20" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 آدرس صفحه
	</td>
	<td nowrap>
	<input type="text" name="Item_PageAddress" id="Item_PageAddress" maxlength="345" size="40">
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageSystemFacilities.php';" value="جدید">
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
$res = manage_SystemFacilities::GetList(); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FacilityID])) 
	{
		manage_SystemFacilities::Remove($res[$k]->FacilityID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_SystemFacilities::GetList(); 
?>
<form id="ListForm" name="ListForm" method="post"> 
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="9">
	لیست امکانات سیستم
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">&nbsp;</td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>عنوان</td>
	<td>گروه</td>
	<td>ترتیب</td>
	<td>آدرس صفحه</td>
	<td width=1% nowrap>کاربران</td>
	<td width=1% nowrap>صفحات</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->FacilityID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageSystemFacilities.php?UpdateID=".$res[$k]->FacilityID."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".htmlentities($res[$k]->FacilityName, ENT_QUOTES, 'UTF-8')."</td>";
		echo "	<td>".$res[$k]->GroupID_Desc."</td>";
	echo "	<td>".htmlentities($res[$k]->OrderNo, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->PageAddress, ENT_QUOTES, 'UTF-8')."</td>";
	echo "<td width=1% nowrap><a  target=\"_blank\" href='ManageUserFacilities.php?FacilityID=".$res[$k]->FacilityID ."'>کاربران</a></td>";
	echo "<td width=1% nowrap><a  target=\"_blank\" href='ManageFacilityPages.php?FacilityID=".$res[$k]->FacilityID ."'>صفحات</a></td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="9" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewSystemFacilities.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
