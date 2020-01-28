<?php 
/*
 صفحه عملیاتی کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
	edited by navidbeta
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/projects.class.php");
// include("../shares/definitions.php");
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
<div class="container">
<form id="SearchForm" name="SearchForm" method=post> 
<input type="hidden" name="PageNumber" id="PageNumber" value="<?php echo $PageNumber ?>">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
<br>
<div class="row">
<!-- <div class="col-md-1"></div> -->
<div class="col-md-12">
<?php echo manage_ProjectTasks::CreateKartableHeader("TasksForControl"); ?>
<!-- <div class="col-md-1"></div> -->
</div>
</div>
<div class="row">
<!-- <div class="col-md-1"></div> -->
<div class="col-md-12">
<table width="100%" class="table-bordered">
<tr id='SearchTr'>
<td>
<table width="100%" class="table-borderless">
<tr>
	<td width="1%" nowrap>
  <?php echo C_RELATED_PROJECT?>
	</td>
	<td nowrap>
	<select class="browser-default custom-select" name="Item_ProjectID" id="Item_ProjectID" onchange='javascript: document.SearchForm.submit();'>
	<option value=0>-
	<? echo manage_projects::GetUserProjectsOptions($_SESSION["PersonID"]); ?>
	</select> 
	</td>
</tr>

</table>
</td>
</tr>
<tr class="table-borderless">
<td colspan="9" >
	<div class="text-center">
	<input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<? echo C_REMOVE ?>">
	 <input type="button" class="btn btn-success" onclick='javascript: NewRecordForm.submit();' value="<? echo C_CREATE ?>">
	 </div>
</td>
</tr> 
</table>
<!-- <div class="col-md-1"></div> -->
</div>
</div>
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
<div class="row">
<div class="col-md-12">
<table width="100%" class="table-bordered">

<tr class="table-primary">
	<td width="1%">&nbsp;</td>‌
	<td width="1%"><?php echo C_ROW?></td>
	<td width="1%">‌‌<?php echo C_EDIT?></td>
	<td width=1%><a href="javascript: Sort('ProjectID', 'ASC');"><?php echo C_RELATED_PROJECT?></a></td>
	<td width=1% nowrap><a href="javascript: Sort('TaskPeriority', 'ASC');"><?php echo C_PRIORITY?></a></td>
	<td width=95%><a href="javascript: Sort('title', 'ASC');"><?php echo C_T_TITLE?></a></td>
	<td nowrap width=1%><a href="javascript: Sort('CreatorID', 'ASC');"><?php echo C_CREATOR?></a></td>
	<td nowrap width=1%><a href="javascript: Sort('CreateDate', 'ASC');"><?php echo C_CREATED_TIME ?></a></td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($res[$k]->HasExpireTime=="YES" && $res[$k]->ExpireTime!="0000-00-00 00:00:00" && substr($res[$k]->ExpireTime, 0, 10)<$now)
		echo '<tr class="table-danger">';
	else if($res[$k]->TaskStatus=="PROGRESSING")
		echo '<tr class="table-success">';
	else if($k%2==0)
		echo "<tr class=\"table-secondary\">";
	else
		echo "<tr class=\"table-default\">";
	echo "<td>";
	if($res[$k]->CanRemoveByCaller)
		echo '<div class="checkbox.checkbox-inline">'.'<input type="checkbox" name="ch_'.$res[$k]->ProjectTaskID.'"></div>';
	else
		echo "&nbsp;";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"NewProjectTasks.php?UpdateID=".$res[$k]->ProjectTaskID."\">";
	echo '<i class="fas fa-edit" title='.C_EDIT.'></i>';
	echo "</a></td>";
	echo "	<td nowrap>&nbsp;";
	echo $res[$k]->ProjectID_Desc."</td>";
	echo "	<td nowrap>&nbsp;".$res[$k]->TaskPeriority."</td>";
	echo "	<td>";
	if($res[$k]->HasExpireTime=="YES" && $res[$k]->ExpireTime!="0000-00-00 00:00:00")
		echo '<i class="fas fa-calendar-times" title="'.C_DEADLINE.': '.$res[$k]->ExpireTime_Shamsi.'"></i>';
	echo htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
	echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
	echo "</tr>";
}
?>
<tr >
<td colspan="9" >
	<div class="text-center">
	<input class="btn btn-danger" type="button" onclick="javascript: ConfirmDelete();" value="<? echo C_REMOVE ?>">
	 <input class="btn btn-success" type="button" onclick='javascript: NewRecordForm.submit();' value="<? echo C_CREATE?>">
	 </div>
</td>
</tr>
<tr class="table-secondary"><td colspan="9" >
<div class="text-right">
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
</div>
</td></tr>
</table>
</div>
</div>
</form>
</div>
<form target="_blank" method="post" action="NewProjectTasks.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm("<? echo C_T_AREUSURE ?>")) document.SearchForm.submit();
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
