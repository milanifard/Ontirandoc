<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : انواع سند پروژه ها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectDocumentTypes.class.php");
include_once("classes/projects.class.php");
include_once("classes/projectsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if (isset($_REQUEST["UpdateID"])) {
	$obj = new be_ProjectDocumentTypes();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectID);
} else
	$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if ($ppc->GetPermission("Add_ProjectDocumentTypes") == "YES")
	$HasAddAccess = true;
if (isset($_REQUEST["UpdateID"])) {
	if ($ppc->GetPermission("Update_ProjectDocumentTypes") == "PUBLIC")
		$HasUpdateAccess = true;
	else if ($ppc->GetPermission("Update_ProjectDocumentTypes") == "PRIVATE" && $_SESSION["PersonID"] == $obj->CreatorID)
		$HasUpdateAccess = true;
	if ($ppc->GetPermission("View_ProjectDocumentTypes") == "PUBLIC")
		$HasViewAccess = true;
	else if ($ppc->GetPermission("View_ProjectDocumentTypes") == "PRIVATE" && $_SESSION["PersonID"] == $obj->CreatorID)
		$HasViewAccess = true;
} else {
	$HasViewAccess = true;
}
if (!$HasViewAccess) {
	echo C_NO_PERMISSION;
	die();
}
if (isset($_REQUEST["Save"])) {
	if (isset($_REQUEST["Item_title"]))
		$Item_title = $_REQUEST["Item_title"];
	if (isset($_REQUEST["ProjectID"]))
		$Item_ProjectID = $_REQUEST["ProjectID"];
	if (isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID = $_REQUEST["Item_CreatorID"];
	if (!isset($_REQUEST["UpdateID"])) {
		if ($HasAddAccess)
			manage_ProjectDocumentTypes::Add(
				$Item_title,
				$Item_ProjectID
			);
	} else {
		if ($HasUpdateAccess)
			manage_ProjectDocumentTypes::Update(
				$_REQUEST["UpdateID"],
				$Item_title
			);
	}
	echo SharedClass::CreateMessageBox(C_DATA_STORED);
}
$LoadDataJavascriptCode = '';
if (isset($_REQUEST["UpdateID"])) {
	$obj = new be_ProjectDocumentTypes();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_title.value='" . htmlentities($obj->title, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_title').innerHTML='" . htmlentities($obj->title, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
}
?>
<form method="post" id="f1" name="f1">
	<?
	if (isset($_REQUEST["UpdateID"])) {
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
	}
	echo manage_projects::ShowSummary($_REQUEST["ProjectID"]);
	echo manage_projects::ShowTabs($_REQUEST["ProjectID"], "ManageProjectDocumentTypes");
	?>
	<br>
	<div class="row">
		<div class="col-1"></div>
		<div class="col-10">
		<table class="table table-bordered table-sm table-striped" border="1" cellspacing="0" align="center">
		<tr class="table-info">
			<td align="center"><?php echo C_MY_TITLE_PROJECTDOCUMENTTYPES ?></td>
		</tr>
		<tr>
			<td>
				<table class="table table-bordered table-sm table-striped" width="100%" border="0">
					<tr>
						<td width="1%" nowrap>
						<?php echo C_MY_TITLE_PROJECTDOCUMENTTYPES_TITLE ?>
						</td>
						<td nowrap>
							<? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
								<input type="text" name="Item_title" id="Item_title" maxlength="200" size="40">
							<? } else { ?>
								<span id="Item_title" name="Item_title"></span>
							<? } ?>
						</td>
					</tr>
					<?
					if (!isset($_REQUEST["UpdateID"])) {
					?>
						<input type="hidden" name="ProjectID" id="ProjectID" value='<? if (isset($_REQUEST["ProjectID"])) echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
					<? } ?>
				</table>
			</td>
		</tr>
		<tr class="FooterOfTable">
			<td align="center">
				<? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess)) {
				?>
					<input class="btn btn-primary" type="button" onclick="javascript: ValidateForm();" value="<?php echo C_STORE ?>">
				<? } ?>
				<input class="btn btn-primary" type="button" onclick="javascript: document.location='ManageProjectDocumentTypes.php?ProjectID=<?php echo $_REQUEST["ProjectID"]; ?>'" value="<?php echo C_NEW ?>">
			</td>
		</tr>
	</table>
		</div>
		<div class="col-1"></div>
	</div>
	<input type="hidden" name="Save" id="Save" value="1">
</form>
<script>
	<? echo $LoadDataJavascriptCode; ?>

	function ValidateForm() {
		document.f1.submit();
	}
</script>
<?php
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if ($ppc->GetPermission("Add_ProjectDocumentTypes") == "YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectDocumentTypes");
$UpdateType = $ppc->GetPermission("Update_ProjectDocumentTypes");
$res = manage_ProjectDocumentTypes::GetList($_REQUEST["ProjectID"]);
$SomeItemsRemoved = false;
for ($k = 0; $k < count($res); $k++) {
	if (isset($_REQUEST["ch_" . $res[$k]->ProjectDocumentTypeID])) {
		if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"])) {
			manage_ProjectDocumentTypes::Remove($res[$k]->ProjectDocumentTypeID);
			$SomeItemsRemoved = true;
		}
	}
}
if ($SomeItemsRemoved)
	$res = manage_ProjectDocumentTypes::GetList($_REQUEST["ProjectID"]);
?>
<form id="ListForm" name="ListForm" method="post">
	<input type="hidden" id="Item_ProjectID" name="Item_ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
	<br>
	<div class="row">
		<div class="col-1"></div>
		<div class="col-10">
		<table align="center" border="1" cellspacing="0">
		<tr class="table-info" bgcolor="#cccccc">
			<td colspan="5">
				<? echo C_MY_TITLE_PROJECTDOCUMENTTYPES_TITLE ?>
			</td>
		</tr>
		<tr class="HeaderOfTable">
			<td width="2%"> </td>
			<td width="2%"><? echo C_ROW ?></td>
			<td width="2%"><? echo C_EDIT ?></td>
			<td><? echo C_TITLE?></td>
			<td width=2% nowrap><? echo C_DOCUMENTS_COUNT ?></td>
		</tr>
		<?
		for ($k = 0; $k < count($res); $k++) {
			if ($k % 2 == 0)
				echo "<tr class=\"OddRow\">";
			else
				echo "<tr class=\"EvenRow\">";
			echo "<td>";
			if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"])) {
				if ($res[$k]->RelatedDocumentsCount == 0)
					echo "<input type=\"checkbox\" name=\"ch_" . $res[$k]->ProjectDocumentTypeID . "\">";
				else
					echo "&nbsp;";
			} else
				echo "&nbsp;";
			echo "</td>";
			echo "<td>" . ($k + 1) . "</td>";
			echo "	<td><a href=\"ManageProjectDocumentTypes.php?UpdateID=" . $res[$k]->ProjectDocumentTypeID . "&ProjectID=" . $_REQUEST["ProjectID"] . "\"><img src='images/edit.gif' title='ویرایش'></a></td>";
			echo "	<td>" . htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8') . "</td>";
			echo "	<td>" . $res[$k]->RelatedDocumentsCount . "</td>";
			echo "</tr>";
		}
		?>
		<tr class="FooterOfTable">
			<td colspan="5" align="center">
				<? if ($RemoveType != "NONE") { ?>
					<input class="btn btn-danger" type="button" onclick="javascript: ConfirmDelete();" value="حذف">
				<? } ?>
			</td>
		</tr>
	</table>
		</div>
		<div class="col-1"></div>
	</div>
</form>
<form target="_blank" method="post" action="NewProjectDocumentTypes.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectID" name="ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
	function ConfirmDelete() {
		if (confirm('<? echo C_ARE_YOU_SURE ?>')) document.ListForm.submit();
	}
</script>

</html>