<?php 
/*
 صفحه عملیاتی کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTasks.class.php");
include("classes/projects.class.php");
HTMLBegin();
/*$mysql = pdodb::getInstance();
if($_SESSION["UserID"]=="gholami-a") {
$query="delete from projectmanagement.ProjectTasks   where ProjectTaskID='46530'";
$res=$mysql->Execute($query);
echo $query;
}*/

$now = date("Y-m-d");
$NumberOfRec = 30;
 $k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
	$FromRec = $_REQUEST["PageNumber"]*$NumberOfRec;
	$PageNumber = $_REQUEST["PageNumber"];
}
else
{
	$FromRec = 0; 
}
if(isset($_REQUEST["SearchAction"])) 
{
	$OrderByFieldName = "ProjectTaskID";
	$OrderType = "";
	if(isset($_REQUEST["OrderByFieldName"]))
	{
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		$OrderType = $_REQUEST["OrderType"];
	}
	$ProjectID=htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8');
} 
else
{ 
	$OrderByFieldName = "TaskPeriority ASC, ProjectTaskID ";
	$OrderType = "";
	$ProjectID='';
}

$res = manage_ProjectTasks::GetTasksForControl($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
?>

<form id="SearchForm" name="SearchForm" method=post> 
<input type="hidden" name="PageNumber" id="PageNumber" value="<?php echo $PageNumber ?>">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
<br>
<?php echo manage_ProjectTasks::CreateKartableHeader("TasksForControl"); ?>

<table width="98%" align="center" border="1" cellspacing="0">
<tr id='SearchTr'>
<td>
<table width="100%" align="center" border="0" cellspacing="0">
<tr>
	<td width="1%" nowrap>
 پروژه مربوطه
	</td>
	<td nowrap>
	<select name="Item_ProjectID" id="Item_ProjectID" onchange='javascript: document.SearchForm.submit();'>
	<option value=0>-
	<? echo manage_projects::GetUserProjectsOptions($_SESSION["PersonID"]); ?>
	</select> 
	</td>
</tr>
</table>
</td>
</tr>
</table>
<? 
if(isset($_REQUEST["SearchAction"])) 
{ 
?>
<script>
		document.SearchForm.Item_ProjectID.value='<? echo htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8'); ?>';
</script>
<?
}
?>
<table width="98%" align="center" border="1" cellspacing="0">
<tr class="FooterOfTable">
<td colspan="9" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
</td>
</tr> 
<tr class="HeaderOfTable">
	<td width="1%">&nbsp;</td>
	<td width="1%">ردیف</td>
	<td width="1%">ویرایش</td>
	<td width=1%><a href="javascript: Sort('ProjectID', 'ASC');">پروژه مربوطه</a></td>
	<td width=1% nowrap><a href="javascript: Sort('TaskPeriority', 'ASC');">اولویت</a></td>
	<td width=95%><a href="javascript: Sort('title', 'ASC');">عنوان</a></td>
	<td nowrap width=1%><a href="javascript: Sort('CreatorID', 'ASC');">ایجاد کننده</a></td>
	<td nowrap width=1%><a href="javascript: Sort('CreateDate', 'ASC');">زمان ایجاد</a></td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($res[$k]->HasExpireTime=="YES" && $res[$k]->ExpireTime!="0000-00-00 00:00:00" && substr($res[$k]->ExpireTime, 0, 10)<$now)
		echo "<tr bgcolor=#ffC7C7>";
	else if($res[$k]->TaskStatus=="PROGRESSING")
		echo "<tr bgcolor=#8BC7A1>";
	else if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	if($res[$k]->CanRemoveByCaller)
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectTaskID."\">";
	else
		echo "&nbsp;";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"NewProjectTasks.php?UpdateID=".$res[$k]->ProjectTaskID."\">";
	echo "<img src='images/edit.gif' title='ویرایش'>";
	echo "</a></td>";
	echo "	<td nowrap>&nbsp;";
	echo $res[$k]->ProjectID_Desc."</td>";
	echo "	<td nowrap>&nbsp;".$res[$k]->TaskPeriority."</td>";
	echo "	<td>";
	if($res[$k]->HasExpireTime=="YES" && $res[$k]->ExpireTime!="0000-00-00 00:00:00")
		echo " <img border=0 src='images/deadline.jpg' title='مهلت انجام: ".$res[$k]->ExpireTime_Shamsi."'> ";
	echo htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
	echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="9" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="9" align="right">
<?
for($k=0; $k<manage_ProjectTasks::GetTasksCountForControl($ProjectID)/$NumberOfRec; $k++)
{
	if($PageNumber!=$k)
		echo "<a href='javascript: ShowPage(".($k).")'>";
	echo ($k+1);
	if($PageNumber!=$k)
		echo "</a>";
	echo " ";
}
?>

</td></tr>
</table>
</form>
<form target="_blank" method="post" action="NewProjectTasks.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.SearchForm.submit();
}
function ShowPage(PageNumber)
{
	SearchForm.PageNumber.value=PageNumber; 
	SearchForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	SearchForm.PageNumber.value=0;
	SearchForm.OrderByFieldName.value=OrderByFieldName; 
	SearchForm.OrderType.value=OrderType; 
	SearchForm.submit();
}
</script>
</html>
