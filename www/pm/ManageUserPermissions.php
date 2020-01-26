<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
	edited by navidbeta
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

	echo SharedClass::CreateMessageBox(C_DATA_STORED);
}
?>
<div class="container">
<form method="post" id="f1" name="f1" >
<input type="hidden" name="Item_UserID" id="Item_UserID" value='<? echo $_REQUEST["Item_UserID"]; ?>'>
<br>

<div class ="row">
<div class="col-md-1"></div>
<div class="col-md-10">
<table class="table-bordered" width="100%">
<tr class="table-primary">
<td>
<div class="text-center"><? echo C_USER_ACCESSES. ":".$_REQUEST["Item_UserID"]; ?></div>
</td>
</tr>
<tr>
<td>
<table class="table-borderless" width="100%">
<tr>
  <td>
  <?
    echo "<b>". C_MENUS ."</b><br>";
	$list = manage_SystemFacilities::GetList();
	for($i=0; $i<count($list); $i++)
	{
	  echo '<div class="checkbox.checkbox-inline">'."<label><input type=checkbox name=ch_".$list[$i]->FacilityID;
	//   '<label>'.$list[$i]->GroupID_Desc." - ".$list[$i]->FacilityName.'<label>'.
	  if(manage_UserFacilities::HasAccess($_REQUEST["Item_UserID"], $list[$i]->FacilityID))
	    echo " checked ";
	  echo ">".$list[$i]->GroupID_Desc." - ".$list[$i]->FacilityName.'<label></div>';
	//   echo ;
	  echo "<br>";
	}
  ?>
  </td>
  
</tr>
</table class="table-bordered">
</td>
</tr>
<tr >

<td>
<div class="text-center">
	<input class="btn btn-success" type="button" onclick="javascript: ValidateForm();" value="<?echo C_SAVE?>">
    <input class="btn btn-secondary" type="button" onclick="document.location='ManageAccountSpecs.php';" value="<?echo C_RETURN?>">
</div>
</td>
</div>
</tr>
</table>
</table>
<div class="col-md-1"></div>
</div>

<input type="hidden" name="Save" id="Save" value="1">
</form>
</div>
<script>
	<? //echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
</html>
