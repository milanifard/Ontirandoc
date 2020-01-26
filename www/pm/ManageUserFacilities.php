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
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_UserID"]))
		$Item_UserID=$_REQUEST["Item_UserID"];
	if(isset($_REQUEST["FacilityID"]))
		$Item_FacilityID=$_REQUEST["FacilityID"];
	manage_UserFacilities::Add($Item_UserID, $Item_FacilityID);
	echo SharedClass::CreateMessageBox(C_DATA_STORED);
}
?>
<div class="conteiner">
<form method="post" id="f1" name="f1" >
<?
echo manage_SystemFacilities::ShowSummary($_REQUEST["FacilityID"]);
?>
<br>
<div class="row">
<div class="col-md-1"></div>
<div class="col-md-10">
<table class="table-bordered" width="100%">
<tr class="table-primary">
<td>
<div class="text-center"><?php echo C_ADD_USER_FACILITY?></div>
</td>
</tr>
<tr>
<td>
<table class="table-borderless" width="100%" >
<tr>
	<td width="1%" nowrap>
	<?php echo C_T_USER?>
	</td>
	<td nowrap>
	<select class="browser-default custom-select" name="Item_UserID" id="Item_UserID">
	<option value=0>-
	<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.AccountSpecs", "UserID", "UserID", "UserID"); ?>	</select>
	</td>
</tr>
<input type="hidden" name="FacilityID" id="FacilityID" value='<? if(isset($_REQUEST["FacilityID"])) echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>'>
</table>
</td>
</tr>
<tr >
<td>
<div class="text-center">
	<input class="btn btn-success" type="button" onclick="javascript: ValidateForm();" value="<? echo C_SAVE ?>">
	<input class="btn btn-secondary" type="button" onclick="window.close();" value="<?echo C_CLOSE?>">	
</div>
</td>
</tr>
</table>
</div>
<div class="col-md-1"></div>
</div>
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
<br>
<div class="row">
<div class="col-md-1"></div>
<div class="col-md-10">
<table class="table-bordered" width="100%"  >
<tr class="table-secondary">
	<td colspan="5">
	<?php  echo C_PRIVILEGED_USERS?>
	</td>
</tr>
<tr >
	<td width="1%">&nbsp;</td>
	<td width="1%"><?php echo C_ROW?></td>
	<td><?php echo C_T_USER ?></td>
	<td><?php echo C_POSSIBILITY?></td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"table-secondary\">";
	else
		echo "<tr class=\"table-default\">";
	echo "<td>";
	echo '<div class="checkbox.checkbox-inline">'.'<input type="checkbox" name="ch_'.$res[$k]->FacilityPageID.'"></div>';
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td>".$res[$k]->UserID_Desc."</td>";
	echo "	<td>".htmlentities($res[$k]->FacilityID, ENT_QUOTES, 'UTF-8')."</td>";
	echo "</tr>";
}
?>
<tr>
<td colspan="5" >
<div class="text-center">
	<input class="btn btn-danger" type="button" onclick="javascript: ConfirmDelete();" value="<? echo C_REMOVE ?>">
	 </div>
</td>
</tr>
</table>
</div>
<div class="col-md-1"></div>
</div>
</form>
</div>
<form target="_blank" method="post" action="NewUserFacilities.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="FacilityID" name="FacilityID" value="<? echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>

<script>
function ConfirmDelete()
{
	if(confirm(<? echo C_T_AREUSURE ?>)) document.ListForm.submit();
}
</script>
</html>
