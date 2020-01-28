<?php 
/*
 صفحه عملیاتی کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/projects.class.php");
include_once("classes/ProjectTasksSecurity.class.php");
include_once("classes/ProjectTaskAssignedUsers.class.php");

HTMLBegin();
$NumberOfRec = 30;
$k=0;
$ShowAll = false;
if (isset($_REQUEST["ShowAll"])) $ShowAll = true;
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
	$OrderByFieldName = "CreateDate";
	$OrderType = "DESC";
	$ProjectID='';
}
//if ($_SESSION["PersonID"] == 401371457) 
//$res = manage_ProjectTasks::GetTasksOfPerson($ProjectID, 401366188, "CREATOR", "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType, true); 
//else
$res = manage_ProjectTasks::GetTasksOfPerson($ProjectID, $_SESSION["PersonID"], "CREATOR", "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType, $ShowAll); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->ProjectTaskID])  && $res[$k]->CanRemoveByCaller) 
	{
		manage_ProjectTasks::Remove($res[$k]->ProjectTaskID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_ProjectTasks::GetTasksOfPerson($ProjectID, $_SESSION["PersonID"], "CREATOR", "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
?>
<form id="SearchForm" name="SearchForm" method=post> 
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
<br>
<?php echo manage_ProjectTasks::CreateKartableHeader("LastCreatedTasks"); ?>

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
<td colspan="10" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
</td>
</tr> 
<tr class="HeaderOfTable">
	<td width="1%">&nbsp;</td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td width=1%><a href="javascript: Sort('ProjectID', 'ASC');">پروژه مربوطه</a></td>
	<td width=1% nowrap><a href="javascript: Sort('TaskPeriority', 'ASC');">اولویت</a></td>
	<td><a href="javascript: Sort('title', 'ASC');">عنوان</a></td>
	<td nowrap width=1%><a href="javascript: Sort('CreatorID', 'ASC');">ایجاد کننده</a></td>
	<td nowrap width=1%><a href="javascript: Sort('TaskStatus', 'ASC');">وضعیت</a></td>
	<td nowrap width=1%><a href="javascript: Sort('CreateDate', 'ASC');">زمان ایجاد</a></td>
	<td nowrap width=1%>
	مجری
	</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	
	if($res[$k]->TaskStatus=="PROGRESSING")
		echo "<tr bgcolor=#8BC7A1>";
	//else if($res[$k]->TaskStatus=="DONE")
		//echo "<tr bgcolor=#DADADD>";
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
	echo "	<td nowrap>&nbsp;".$res[$k]->ProjectID_Desc."</td>";
	echo "	<td nowrap>&nbsp;".$res[$k]->TaskPeriority."</td>";
	echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
	$St = $res[$k]->TaskStatus_Desc;
	echo "	<td nowrap>".(($St == 'اقدام شده')? "<b>" . $St . "</b>": $St)."</td>";
	echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
	$executors = manage_ProjectTaskAssignedUsers::GetList($res[$k]->ProjectTaskID, "PersonID", "");
	echo "	<td nowrap>";
	for($m=0; $m<count($executors); $m++)
	{
		if($m>0)
			echo "<br>";
		echo $executors[$m]->PersonID_FullName;
	}
	echo "&nbsp;</td>";
	
	/*
	echo "<td nowrap>";
	echo "<a target=\"_blank\" href='ManageProjectTaskAssignedUsers.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/members.gif' border='0' title='کاربران منتسب به کار'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskActivities.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/activity.gif' border='0' title='اقدامات'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskComments.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/comment.gif' border='0' title='یادداشتها'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskDocuments.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/document.gif' border='0' title='اسناد کارها'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskRequisites.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/chain.gif' border='0' title='پیشنیازها'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskHistory.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/history.gif' border='0' title='تاریخچه'>";
	echo "</a>  ";
	echo "</td>";
	*/
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="10" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="10" align="right">
<?

//if ($_SESSION["PersonID"] == 401371457)
//	$All = manage_ProjectTasks::GetTasksOfPersonCount($ProjectID, 401366188, "CREATOR", "", true);
//else
	$All = manage_ProjectTasks::GetTasksOfPersonCount($ProjectID, $_SESSION["PersonID"], "CREATOR", "", $ShowAll);

for($k=0; $k < $All/$NumberOfRec; $k++)
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
<br />

<?php
	echo "<a style='margin: 17px; text-decoration: none;' href='javascript: ";
	if ($ShowAll == false)
		echo "ShowAllHistory()'>نمایش کارهای قدیمی</a>";
	else
		echo "NormalShow()'>فقط نمایش کارهای دو ماه اخیر</a>";
?>

<br />
<br />
<br />
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

function ShowAllHistory()
{
	window.location = window.location.pathname + "?ShowAll";
}

function NormalShow()
{
	window.location = window.location.pathname;
}

function Sort(OrderByFieldName, OrderType)
{
	SearchForm.OrderByFieldName.value=OrderByFieldName; 
	SearchForm.OrderType.value=OrderType; 
	SearchForm.submit();
}
</script>
</html>
