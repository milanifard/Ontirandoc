<?php
/*
 صفحه عملیاتی کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/
include("header.inc.php");
//include_once("SharedClass.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/projects.class.php");
HTMLBegin();

$now = date("Y-m-d");
$NumberOfRec = 20;
$k = 0;
$PageNumber = 0;
if (isset($_REQUEST["PageNumber"])) {
	$FromRec = $_REQUEST["PageNumber"] * $NumberOfRec;
	$PageNumber = $_REQUEST["PageNumber"];
} else {
	$FromRec = 0;
}
if (isset($_REQUEST["SearchAction"])) {
	$OrderByFieldName = "ProjectTaskID";
	$OrderType = "";
	if (isset($_REQUEST["OrderByFieldName"])) {
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		$OrderType = $_REQUEST["OrderType"];
	}
	$ProjectID = htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8');
} else {
	$OrderByFieldName = "TaskPeriority ASC, ProjectTaskID ";
	$OrderType = "";
	$ProjectID = '';
}

$res = manage_ProjectTasks::GetViewerTasks($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
$SomeItemsRemoved = false;
for ($k = 0; $k < count($res); $k++) {
	if (isset($_REQUEST["ch_" . $res[$k]->ProjectTaskID])  && $res[$k]->CanRemoveByCaller) {
		manage_ProjectTasks::Remove($res[$k]->ProjectTaskID);
		$SomeItemsRemoved = true;
	}
}
if ($SomeItemsRemoved)
	$res = manage_ProjectTasks::GetViewerTasks($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
?>


<form id="SearchForm" name="SearchForm" method=post>
	<input type="hidden" name="PageNumber" id="PageNumber" value="<?php echo $PageNumber ?>">
	<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
	<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
	<input type="hidden" name="SearchAction" id="SearchAction" value="1">
	<br>
	<?php echo manage_ProjectTasks::CreateKartableHeader("ViewerTasks"); ?>

	<div class="container">
		<div class="row" id='SearchTr'>
			<div class="col-12">
				<div class="row">
					<div class="col-md-1 col-xs-6" nowrap>
						<? echo C_RELATED_PROJECT ?>
					</div>
					<div class="col-md-11 col-xs-6" nowrap>
						<select name="Item_ProjectID" id="Item_ProjectID" class="custom-select" onchange='javascript: document.SearchForm.submit();'>
							<option value=0>-
								<? echo manage_projects::GetUserProjectsOptions($_SESSION["PersonID"]); ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?
	if (isset($_REQUEST["SearchAction"])) {
	?>
		<script>
			document.SearchForm.Item_ProjectID.value = '<? echo htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8'); ?>';
		</script>
	<?
	}
	?>
	<table class="table table-striped table-bordered table-hover">
		<thead class="thead-light">
			<tr>
				<th colspan="12" class="text-center">
					<input type="button" class="btn btn-danger mx-3" onclick="javascript: ConfirmDelete();" value="<? echo C_DELETE ?>">
					<input type="button" class="btn btn-primary mx-3" onclick='javascript: NewRecordForm.submit();' value='<? echo C_CREATE ?>'>
				</th>
			</tr>
			<tr class="">
				<th width="1%">&nbsp;</th>
				<th width="1%"><? echo C_ROW ?></th>
				<th width="1%"><? echo C_EDIT ?></th>
				<th width=1%><a href="javascript: Sort('ProjectID', 'ASC');"><? echo C_RELATED_PROJECT ?></a></th>
				<th width=1% nowrap><a href="javascript: Sort('TaskPeriority', 'ASC');"><? echo C_PRIORITY ?></a></th>
				<th width=95%><a href="javascript: Sort('title', 'ASC');"><? echo C_TITLE ?></a></th>
				<th nowrap width=1%><a href="javascript: Sort('CreatorID', 'ASC');"><? echo C_CREATOR ?></a></th>
				<th nowrap width=1%><a href="javascript: Sort('CreateDate', 'ASC');"><? echo C_CREATED_TIME ?></a></th>
			</tr>
		</thead>
		<?
		for ($k = 0; $k < count($res); $k++) {
			if ($res[$k]->HasExpireTime == "YES" && $res[$k]->ExpireTime != "0000-00-00 00:00:00" && substr($res[$k]->ExpireTime, 0, 10) < $now)
				echo "<tr bgcolor=#ffC7C7>";
			else if ($res[$k]->TaskStatus == "PROGRESSING")
				echo "<tr bgcolor=#8BC7A1>";
			else
				echo "<tr>";
			echo "<td>";
			if ($res[$k]->CanRemoveByCaller)
				echo "<input type=\"checkbox\" class=\"form-check-input position-static\" name=\"ch_" . $res[$k]->ProjectTaskID . "\">";
			else
				echo "&nbsp;";
			echo "</td>";
			echo "<td>" . ($k + $FromRec + 1) . "</td>";
			echo "	<td>";
			echo "<a target=\"_blank\" href=\"NewProjectTasks.php?UpdateID=" . $res[$k]->ProjectTaskID . "\">";
			echo "<i class='fa fa-edit'></i>";
			echo "</a></td>";
			echo "	<td nowrap>&nbsp;";
			echo $res[$k]->ProjectID_Desc . "</td>";
			echo "	<td nowrap>&nbsp;" . $res[$k]->TaskPeriority . "</td>";
			echo "	<td>";
			if ($res[$k]->HasExpireTime == "YES" && $res[$k]->ExpireTime != "0000-00-00 00:00:00")
				echo " <img border=0 src='images/deadline.jpg' title='" . C_DEADLINE . ": " . $res[$k]->ExpireTime_Shamsi . "'> ";
			echo htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8') . "</td>";
			echo "	<td nowrap>" . $res[$k]->CreatorID_FullName . "</td>";
			echo "	<td nowrap>" . $res[$k]->CreateDate_Shamsi . "</td>";
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
		<tfoot>
			<tr class="FooterOfTable">
				<td colspan="9" align="center">
					<input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<? echo C_DELETE ?>">
					<input type="button" class="btn btn-primary" onclick='javascript: NewRecordForm.submit();' value='<? echo C_CREATE ?>'>
				</td>
			</tr>
		</tfoot>
		<tr bgcolor="#cccccc">
			<td colspan="9" align="right">
				<?
				for ($k = 0; $k < manage_ProjectTasks::GetViewerTasksCount($ProjectID) / $NumberOfRec; $k++) {
					if ($PageNumber != $k)
						echo "<a href='javascript: ShowPage(" . ($k) . ")'>";
					echo ($k + 1);
					if ($PageNumber != $k)
						echo "</a>";
					echo " ";
				}
				?>

			</td>
		</tr>
	</table>
</form>

<form target="_blank" method="post" action="NewProjectTasks.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
	function ConfirmDelete() {
		if (confirm('<? echo C_T_AREUSURE ?>')) document.SearchForm.submit();
	}

	function ShowPage(PageNumber) {
		SearchForm.PageNumber.value = PageNumber;
		SearchForm.submit();
	}

	function Sort(OrderByFieldName, OrderType) {
		SearchForm.PageNumber.value = 0;
		SearchForm.OrderByFieldName.value = OrderByFieldName;
		SearchForm.OrderType.value = OrderType;
		SearchForm.submit();
	}
</script>

</html>