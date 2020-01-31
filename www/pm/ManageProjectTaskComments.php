<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : یادداشتها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTaskComments.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/ProjectTasksSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if (isset($_REQUEST["UpdateID"])) {
	$obj = new be_ProjectTaskComments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectTaskID);
} else
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if ($ppc->GetPermission("Add_ProjectTaskComments") == "YES")
	$HasAddAccess = true;
if (isset($_REQUEST["UpdateID"])) {
	if ($ppc->GetPermission("Update_ProjectTaskComments") == "PUBLIC")
		$HasUpdateAccess = true;
	else if ($ppc->GetPermission("Update_ProjectTaskComments") == "PRIVATE" && $_SESSION["PersonID"] == $obj->CreatorID)
		$HasUpdateAccess = true;
	if ($ppc->GetPermission("View_ProjectTaskComments") == "PUBLIC")
		$HasViewAccess = true;
	else if ($ppc->GetPermission("View_ProjectTaskComments") == "PRIVATE" && $_SESSION["PersonID"] == $obj->CreatorID)
		$HasViewAccess = true;
} else {
	$HasViewAccess = true;
}
if (!$HasViewAccess) {
	echo C_NO_PERMISSION;
	die();
}
if (isset($_REQUEST["Save"])) {
	if (isset($_REQUEST["ProjectTaskID"]))
		$Item_ProjectTaskID = $_REQUEST["ProjectTaskID"];
	if (isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID = $_REQUEST["Item_CreatorID"];
	if (isset($_REQUEST["Item_CreateTime"]))
		$Item_CreateTime = $_REQUEST["Item_CreateTime"];
	if (isset($_REQUEST["Item_CommentBody"]))
		$Item_CommentBody = $_REQUEST["Item_CommentBody"];
	if (!isset($_REQUEST["UpdateID"])) {
		if ($HasAddAccess)
			manage_ProjectTaskComments::Add(
				$Item_ProjectTaskID,
				$Item_CommentBody
			);
	} else {
		if ($HasUpdateAccess)
			manage_ProjectTaskComments::Update(
				$_REQUEST["UpdateID"],
				$Item_CommentBody
			);
	}
	echo SharedClass::CreateMessageBox(C_STORED);
}
$LoadDataJavascriptCode = '';
$CommentBody = "";
if (isset($_REQUEST["UpdateID"])) {
	$obj = new be_ProjectTaskComments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);

	if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$CommentBody =  htmlentities($obj->CommentBody, ENT_QUOTES, 'UTF-8');
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_CommentBody').innerHTML='" . htmlentities($obj->CommentBody, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
}
?>
<form method="post" id="f1" name="f1">
	<?
	if (isset($_REQUEST["UpdateID"])) {
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
	}
	echo manage_ProjectTasks::ShowSummary($_REQUEST["ProjectTaskID"]);
	echo manage_ProjectTasks::ShowTabs($_REQUEST["ProjectTaskID"], "ManageProjectTaskComments");
	?>
	<br>
	<table class="table mw-100 align-content-center border-1" cellspacing="0">
		<thead class="thead-light">
			<tr>
				<td> <?php echo C_MY_TITLE_PROJECT_COMMENTS ?> </td>
			</tr>
		</thead>
		<thead>
			<tr>
				<td>
					<table class="table" border="0">
						<?
						if (!isset($_REQUEST["UpdateID"])) {
						?>
							<input type="hidden" name="ProjectTaskID" id="ProjectTaskID" value='<? if (isset($_REQUEST["ProjectTaskID"])) echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>'>
						<? } ?>
						<tr>
							<td width="1%" nowrap>
								<font class="text-danger">*</font> <? echo C_TEXT ?>
							</td>
							<td nowrap>
								<? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
									<textarea name="Item_CommentBody" id="Item_CommentBody" cols="80" rows="5"><?php echo $CommentBody ?></textarea>
								<? } else { ?>
									<span id="Item_CommentBody" name="Item_CommentBody"></span>
								<? } ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</thead>
		<tfoot class="align-content-center">
			<tr>
				<td>
					<? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess)) {
					?>
						<input class="btn btn-primary" type="button" onclick="javascript: ValidateForm();" value="<? echo C_STORE ?>">
					<? } ?>
					<input class="btn btn-success" type="button" onclick="javascript: document.location='ManageProjectTaskComments.php?ProjectTaskID=<?php echo $_REQUEST["ProjectTaskID"]; ?>'" value="<? echo C_STORE ?>">
				</td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="Save" id="Save" value="1">
</form>
<script>
	<? echo $LoadDataJavascriptCode; ?>

	function ValidateForm() {
		if (document.getElementById('Item_CommentBody')) {
			if (document.getElementById('Item_CommentBody').value == '') {
				alert(C_NO_TEXT_ENTERED);
				return;
			}
		}
		document.f1.submit();
	}
</script>
<?php
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if ($ppc->GetPermission("Add_ProjectTaskComments") == "YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectTaskComments");
$UpdateType = $ppc->GetPermission("Update_ProjectTaskComments");
$NumberOfRec = 30;
$k = 0;
$PageNumber = 0;
if (isset($_REQUEST["PageNumber"])) {
	$FromRec = $_REQUEST["PageNumber"] * $NumberOfRec;
	$PageNumber = $_REQUEST["PageNumber"];
} else {
	$FromRec = 0;
}
$res = manage_ProjectTaskComments::GetList($_REQUEST["ProjectTaskID"], $FromRec, $NumberOfRec);
$SomeItemsRemoved = false;
for ($k = 0; $k < count($res); $k++) {
	if (isset($_REQUEST["ch_" . $res[$k]->ProjectTaskCommentID])) {
		if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"])) {
			manage_ProjectTaskComments::Remove($res[$k]->ProjectTaskCommentID);
			$SomeItemsRemoved = true;
		}
	}
}
if ($SomeItemsRemoved)
	$res = manage_ProjectTaskComments::GetList($_REQUEST["ProjectTaskID"], $FromRec, $NumberOfRec);
?>
<form id="ListForm" name="ListForm" method="post">
	<input type="hidden" id="Item_ProjectTaskID" name="Item_ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
	<? if (isset($_REQUEST["PageNumber"]))
		echo "<input type=\"hidden\" name=\"PageNumber\" value=" . $_REQUEST["PageNumber"] . ">"; ?>
	<br>
	<table class="table mw-100 align-content-center border-1" cellspacing="0">
		<thead class="thead-light">
			<tr>
				<td colspan="6">
					<? echo C_COMMENTS?>
				</td>
			</tr>
		</thead>
		<thead <tr class="HeaderOfTable">
			<td width="1%"> </td>
			<td width="1%"><? echo C_ROW?></td>
			<td width="2%"><? echo C_EDIT?></td>
			<td><? echo C_TEXT?></td>
			<td width=5% nowrap><?php echo C_CREATOR?></td>
			<td width=5% nowrap><?php echo C_CREATED_AT?></td>
			</tr>
		</thead>
		<?
		for ($k = 0; $k < count($res); $k++) {
			if ($k % 2 == 0)
				echo "<tr class=\"OddRow\">";
			else
				echo "<tr class=\"EvenRow\">";
			echo "<td>";
			if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"]))
				echo "<input type=\"checkbox\" name=\"ch_" . $res[$k]->ProjectTaskCommentID . "\">";
			else
				echo " ";
			echo "</td>";
			echo "<td>" . ($k + $FromRec + 1) . "</td>";
			echo "	<td><a href=\"ManageProjectTaskComments.php?UpdateID=" . $res[$k]->ProjectTaskCommentID . "&ProjectTaskID=" . $_REQUEST["ProjectTaskID"] . "\"><img src='images/edit.gif' title='ویرایش'></a></td>";
			echo "	<td>" . str_replace("\r\n", "<br>", htmlentities($res[$k]->CommentBody, ENT_QUOTES, 'UTF-8')) . "</td>";
			echo "	<td nowrap>" . $res[$k]->CreatorID_FullName . "</td>";
			echo "	<td nowrap>" . $res[$k]->CreateTime_Shamsi . "</td>";
			echo "</tr>";
		}
		?>
		<tfoot class="align-content-center">
			<tr>
				<td colspan="6" align="center">
					<? if ($RemoveType != "NONE") { ?>
						<input type="button" onclick="javascript: ConfirmDelete();" value="<? echo C_REMOVE?>">
					<? } ?>
				</td>
			</tr>
		</tfoot>
		<tr class="thead-light align-content-right">
			<td colspan="6" >
				<?
				for ($k = 0; $k < manage_ProjectTaskComments::GetCount($_REQUEST["ProjectTaskID"]) / $NumberOfRec; $k++) {
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
<form target="_blank" method="post" action="NewProjectTaskComments.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
	<input type="hidden" name="PageNumber" id="PageNumber" value="0">
</form>
<script>
	function ConfirmDelete() {
		if (confirm(C_ARE_YOU_SURE)) document.ListForm.submit();
	}

	function ShowPage(PageNumber) {
		f2.PageNumber.value = PageNumber;
		f2.submit();
	}
</script>

</html>