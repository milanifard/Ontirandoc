<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/
include("header.inc.php");
include("classes/UserFacilities.class.php");
include ("classes/SystemFacilities.class.php");
include ("classes/SystemFacilityGroups.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	$Item_UserID=$_REQUEST["Item_UserID"];
	manage_UserFacilities::RemoveAllUserFacilities($Item_UserID);
	$list = manage_SystemFacilities::GetList();
	for($i=0; $i<count($list); $i++)
	{
	  if(isset($_REQUEST["ch_".$list[$i]->FacilityID]))
	    manage_UserFacilities::Add($Item_UserID, $list[$i]->FacilityID);
	}

	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
?>
<form method="post" id="f1" name="f1" >
<input type="hidden" name="Item_UserID" id="Item_UserID" value='<? echo $_REQUEST["Item_UserID"]; ?>'>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">دسترسی های کاربر: <? echo $_REQUEST["Item_UserID"]; ?></td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
  <td>
  <?
    echo "<b>منوها</b><br>";
	$list = manage_SystemFacilities::GetList();
	for($i=0; $i<count($list); $i++)
	{
	  echo "<input type=checkbox name=ch_".$list[$i]->FacilityID;
	  if(manage_UserFacilities::HasAccess($_REQUEST["Item_UserID"], $list[$i]->FacilityID))
	    echo " checked ";
	  echo ">";
	  echo $list[$i]->GroupID_Desc." - ".$list[$i]->FacilityName;
	  echo "<br>";
	}
  ?>
  </td>
  <td width=50% valign=top>
  </td>
</tr>

</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
<input type="button" onclick="document.location='ManageAccountSpecs.php';" value="بازگشت">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? //echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
</html>
