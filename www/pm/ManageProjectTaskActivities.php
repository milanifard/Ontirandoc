<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اقدامات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/


//Adel Aboutalebi Pirnaeimi
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTaskActivities.class.php");
include("classes/ProjectTasks.class.php");
include("classes/ProjectTasksSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if ($ppc->GetPermission("Add_ProjectTaskActivities") == "YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectTaskActivities");
$UpdateType = $ppc->GetPermission("Update_ProjectTaskActivities");
$OrderByFieldName = "ProjectTaskActivityID";
$OrderType = "";
if (isset($_REQUEST["OrderByFieldName"])) {
	$OrderByFieldName = $_REQUEST["OrderByFieldName"];
	$OrderType = $_REQUEST["OrderType"];
}
$res = manage_ProjectTaskActivities::GetList($_REQUEST["ProjectTaskID"], $OrderByFieldName, $OrderType);
$SomeItemsRemoved = false;
for ($k = 0; $k < count($res); $k++) {
	if (isset($_REQUEST["ch_" . $res[$k]->ProjectTaskActivityID])) {
		if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"])) {
			manage_ProjectTaskActivities::Remove($res[$k]->ProjectTaskActivityID);
			$SomeItemsRemoved = true;
		}
	}
}
if ($SomeItemsRemoved)
	$res = manage_ProjectTaskActivities::GetList($_REQUEST["ProjectTaskID"], $OrderByFieldName, $OrderType);
echo manage_ProjectTasks::ShowSummary($_REQUEST["ProjectTaskID"]);
echo manage_ProjectTasks::ShowTabs($_REQUEST["ProjectTaskID"], "ManageProjectTaskActivities");
?>
<form id="ListForm" name="ListForm" method="post">

	<div class="container">
		<div class="card">
			<div class="card-header">
				<td width="100%"><?php echo C_ACTIONS; ?></th>
			</div>
			<div class="card-body">
				<div class="card-column align-middle text-center" style="margin-bottom: 20px">
					<? if ($HasAddAccess) { ?>
						<input type="button" class="btn btn-success" onclick='javascript: NewRecordForm.submit();' value="<? echo C_CREATE; ?>">
					<? } ?>
					<? if ($RemoveType != "NONE") { ?>
						<input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<? echo C_DELETE; ?>">
					<? } ?>
				</div>

				<table class="table table-bordered table-sm table-striped">
					<thead class="table-info">

						<tr>
							<td width="1%"> </td>
							<th width="1%"><?php echo C_ROW; ?></th>
							<th width="1%"><?php echo C_EDIT; ?></th>
							<th width="7%"><a href="javascript: Sort('ProjectTaskActivityTypeID', 'ASC');"><?php echo C_ACTION_TYPE; ?></a></th>
							<th width="9%"><a href="javascript: Sort('ActivityLength', 'ASC');"><?php echo C_USAGE_TIME; ?></a></th>
							<th width="10%"><a href="javascript: Sort('ProgressPercent', 'ASC');"><?php echo C_Progress; ?></a></th>
							<th><?php echo C_DESCRIPTION; ?></th>
							<th width="1%"><?php echo C_ATTACHMENTS; ?></th>
							<th width="8%"><a href="javascript: Sort('CreatorID', 'ASC');"><?php echo C_CREATOR; ?></a></th>
							<th width="8%"><a href="javascript: Sort('ActivityDate', 'ASC');"><?php echo C_ACTION_DATE; ?></a></th>
						</tr>
					</thead>
					<tbody>

						<?
						for ($k = 0; $k < count($res); $k++) {
							echo "<tr>";
							echo "<td>";
							if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"]))
								echo "<input type=\"checkbox\" name=\"ch_" . $res[$k]->ProjectTaskActivityID . "\">";
							else
								echo " ";
							echo "</td>";
							echo "<td class='text-center align-middle'>" . ($k + 1) . "</td>";
							echo "	<td class='text-center align-middle'>";
							echo "<a target=\"_blank\" href=\"NewProjectTaskActivities.php?UpdateID=" . $res[$k]->ProjectTaskActivityID . "\">";
							if ($UpdateType == "PUBLIC" || ($UpdateType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"]))
								echo "<i class='fas fa-edit'></i>";
							else
								echo "<i class='fas fa-eye'></i>";
							echo "</a></td>";
							echo "	<td nowrap>" . $res[$k]->ProjectTaskActivityTypeID_Desc . "</td>";
							echo "	<td nowrap>" . floor($res[$k]->ActivityLength / 60) . ":" . ($res[$k]->ActivityLength % 60) . "</td>";
							echo "	<td>" . htmlentities($res[$k]->ProgressPercent, ENT_QUOTES, 'UTF-8') . "</td>";
							echo "	<td>&nbsp;" . str_replace("\r", "<br>", htmlentities($res[$k]->ActivityDescription, ENT_QUOTES, 'UTF-8')) . "</td>";
							/*if($res[$k]->FileName!="")
							echo "	<td><a href='DownloadFile.php?FileType=ActivityFile&RecID=".$res[$k]->ProjectTaskActivityID."'><img src='images/Download.gif'></a></td>";
							else
							echo "	<td>-</td>";*/

							echo "<td class='text-center align-middle'>";
							if ($res[$k]->FileName != "")
								echo "<a target='_blank' href=\"ReciptFile.php?AID=" . $res[$k]->ProjectTaskActivityID . "&FileName_AID=" . $res[$k]->FileName . "\">
								<i class='fas fa-download'></i></a>";
							else
								echo C_NOT_EXIST;
							echo "</td>";

							echo "	<td nowrap>" . $res[$k]->CreatorID_FullName . "</td>";
							echo "	<td nowrap>" . $res[$k]->ActivityDate_Shamsi . "</td>";
							echo "</tr>";
						}
						?>


					</tbody>
				</table>

				<div class="card-column align-middle text-center">
					<? if ($HasAddAccess) { ?>
						<input type="button" class="btn btn-success" onclick='javascript: NewRecordForm.submit();' value="<? echo C_CREATE; ?>">
					<? } ?>
					<? if ($RemoveType != "NONE") { ?>
						<input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<? echo C_DELETE; ?>">
					<? } ?>
				</div>
			</div>
		</div>
	</div>
</form>

<form target="_blank" method="post" action="NewProjectTaskActivities.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
	<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
	<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
</form>
<script>
	function ConfirmDelete() {
		if (confirm('<?php echo C_ARE_YOU_SURE;?>')) document.ListForm.submit();
	}

	function Sort(OrderByFieldName, OrderType) {
		f2.OrderByFieldName.value = OrderByFieldName;
		f2.OrderType.value = OrderType;
		f2.submit();
	}
</script>

</html>