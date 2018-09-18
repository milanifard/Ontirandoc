<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اقدامات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTaskActivities.class.php");
include ("classes/ProjectTasks.class.php");
include("classes/ProjectTasksSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_ProjectTaskActivities")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectTaskActivities");
$UpdateType = $ppc->GetPermission("Update_ProjectTaskActivities");
$OrderByFieldName = "ProjectTaskActivityID";
$OrderType = "";
if(isset($_REQUEST["OrderByFieldName"]))
{
	$OrderByFieldName = $_REQUEST["OrderByFieldName"];
	$OrderType = $_REQUEST["OrderType"];
}
$res = manage_ProjectTaskActivities::GetList($_REQUEST["ProjectTaskID"], $OrderByFieldName, $OrderType); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->ProjectTaskActivityID])) 
	{
		if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorID==$_SESSION["PersonID"]))
			{
			manage_ProjectTaskActivities::Remove($res[$k]->ProjectTaskActivityID); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_ProjectTaskActivities::GetList($_REQUEST["ProjectTaskID"], $OrderByFieldName, $OrderType); 
echo manage_ProjectTasks::ShowSummary($_REQUEST["ProjectTaskID"]);
echo manage_ProjectTasks::ShowTabs($_REQUEST["ProjectTaskID"], "ManageProjectTaskActivities");
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_ProjectTaskID" name="Item_ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="10">
	اقدامات
	</td>
</tr>
<tr class="FooterOfTable">
<td colspan="10" align="center">
<? if($RemoveType!="NONE") { ?>
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
<? } ?>
<? if($HasAddAccess) { ?>
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
<? } ?>
</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td width=1% nowrap><a href="javascript: Sort('ProjectTaskActivityTypeID', 'ASC');">نوع اقدام</a></td>
	<td  width=1% nowrap><a href="javascript: Sort('ActivityLength', 'ASC');">زمان مصرفی</a></td>
	<td width=1% nowrap><a href="javascript: Sort('ProgressPercent', 'ASC');">درصد پیشرفت</a></td>
	<td>شرح</td>
	<td width=1% nowrap>ضمیمه</td>
	<td width=1% nowrap><a href="javascript: Sort('CreatorID', 'ASC');">ایجاد کننده</a></td>
	<td width=1% nowrap><a href="javascript: Sort('ActivityDate', 'ASC');">تاریخ اقدام</a></td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorID==$_SESSION["PersonID"]))
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectTaskActivityID."\">";
	else
		echo " ";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"NewProjectTaskActivities.php?UpdateID=".$res[$k]->ProjectTaskActivityID."\">";
	if($UpdateType=="PUBLIC" || ($UpdateType=="PRIVATE" && $res[$k]->CreatorID==$_SESSION["PersonID"]))
			echo "<img src='images/edit.gif' title='ویرایش'>";
	else
		echo "<img src='images/read.gif' title='مشاهده'>";
	echo "</a></td>";
	echo "	<td nowrap>".$res[$k]->ProjectTaskActivityTypeID_Desc."</td>";
	echo "	<td nowrap>".floor($res[$k]->ActivityLength/60).":".($res[$k]->ActivityLength%60)."</td>";
	echo "	<td>".htmlentities($res[$k]->ProgressPercent, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>&nbsp;".str_replace("\r", "<br>", htmlentities($res[$k]->ActivityDescription, ENT_QUOTES, 'UTF-8'))."</td>";
	/*if($res[$k]->FileName!="")
		echo "	<td><a href='DownloadFile.php?FileType=ActivityFile&RecID=".$res[$k]->ProjectTaskActivityID."'><img src='images/Download.gif'></a></td>";
	else
		echo "	<td>-</td>";*/
		
	echo "<td>";
	if ($res[$k]->FileName != "")
		echo "<a target='_blank' href=\"ReciptFile.php?AID=" . $res[$k]->ProjectTaskActivityID . "&FileName_AID=" . $res[$k]->FileName . "\">
		 <img border=0 src='images/Download.gif' id='fileimg' title='دریافت فایل'></a>";
	else
		echo "ندارد";
	echo "</td>";

	echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
	echo "	<td nowrap>".$res[$k]->ActivityDate_Shamsi."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="10" align="center">
<? if($RemoveType!="NONE") { ?>
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
<? } ?>
<? if($HasAddAccess) { ?>
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
<? } ?>
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewProjectTaskActivities.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	f2.OrderByFieldName.value=OrderByFieldName; 
	f2.OrderType.value=OrderType; 
	f2.submit();
}
</script>
</html>
