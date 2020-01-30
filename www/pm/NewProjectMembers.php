<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : اعضای پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-15
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectMembers.class.php");
include_once("classes/projects.class.php");
include_once("classes/projectsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if (isset($_REQUEST["UpdateID"])) {
	$obj = new be_ProjectMembers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectID);
} else
	$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if ($ppc->GetPermission("Add_ProjectMembers") == "YES")
	$HasAddAccess = true;
if (isset($_REQUEST["UpdateID"])) {
	if ($ppc->GetPermission("Update_ProjectMembers") == "PUBLIC")
		$HasUpdateAccess = true;
	else if ($ppc->GetPermission("Update_ProjectMembers") == "PRIVATE" && $_SESSION["PersonID"] == $obj->CreatorID)
		$HasUpdateAccess = true;
	if ($ppc->GetPermission("View_ProjectMembers") == "PUBLIC")
		$HasViewAccess = true;
	else if ($ppc->GetPermission("View_ProjectMembers") == "PRIVATE" && $_SESSION["PersonID"] == $obj->CreatorID)
		$HasViewAccess = true;
} else {
	$HasViewAccess = true;
}
if (!$HasViewAccess) {
	echo C_NO_PERMISSION;
	die();
}
if (isset($_REQUEST["Save"])) {
	if (isset($_REQUEST["ProjectID"]))
		$Item_ProjectID = $_REQUEST["ProjectID"];
	if (isset($_REQUEST["Item_PersonID"]))
		$Item_PersonID = $_REQUEST["Item_PersonID"];
	if (isset($_REQUEST["Item_AccessType"]))
		$Item_AccessType = $_REQUEST["Item_AccessType"];
	if (isset($_REQUEST["Item_ParticipationPercent"]))
		$Item_ParticipationPercent = $_REQUEST["Item_ParticipationPercent"];
	if (isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID = $_REQUEST["Item_CreatorID"];
	if (!isset($_REQUEST["UpdateID"])) {
		if ($HasAddAccess) {
			manage_ProjectMembers::Add(
				$Item_ProjectID,
				$Item_PersonID,
				$Item_AccessType,
				$Item_ParticipationPercent
			);
			$SelectedPersonID = $Item_PersonID;
			$LastID = $Item_ProjectID;
			if ($Item_AccessType == "MANAGER") {
				security_projects::SaveFieldPermission($LastID, 'title', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($LastID, 'description', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($LastID, 'StartTime', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($LastID, 'EndTime', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($LastID, 'SysCode', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($LastID, 'ProjectPriority', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($LastID, 'ProjectStatus', $SelectedPersonID, "WRITE");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectDocumentTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectDocuments', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectMembers', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectMilestones', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectTaskActivityTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectTaskTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
			} else {
				security_projects::SaveFieldPermission($LastID, 'title', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($LastID, 'description', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($LastID, 'StartTime', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($LastID, 'EndTime', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($LastID, 'SysCode', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($LastID, 'ProjectPriority', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($LastID, 'ProjectStatus', $SelectedPersonID, "READ");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectDocumentTypes', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectDocuments', $SelectedPersonID, "YES", "PRIVATE", "PRIVATE", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectMembers', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectMilestones', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectTaskActivityTypes', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
				security_projects::SaveDetailTablePermission($LastID, 'ProjectTaskTypes', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
			}
		}
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
	} else {
		if ($HasUpdateAccess)
			manage_ProjectMembers::Update(
				$_REQUEST["UpdateID"],
				$Item_PersonID,
				$Item_AccessType,
				$Item_ParticipationPercent
			);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
		die();
	}
	echo SharedClass::CreateMessageBox(C_DATA_STORED);
}
$LoadDataJavascriptCode = '';
if (isset($_REQUEST["UpdateID"])) {
	$obj = new be_ProjectMembers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$LoadDataJavascriptCode .= "document.getElementById('Span_PersonID_FullName').innerHTML='" . $obj->PersonID_FullName . "'; \r\n ";
	if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.getElementById('Item_PersonID').value='" . $obj->PersonID . "'; \r\n ";
	if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_AccessType.value='" . htmlentities($obj->AccessType, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_AccessType').innerHTML='" . htmlentities($obj->AccessType_Desc, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
	if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ParticipationPercent.value='" . htmlentities($obj->ParticipationPercent, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ParticipationPercent').innerHTML='" . htmlentities($obj->ParticipationPercent, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
}
?>
<form method="post" id="f1" name="f1">
	<?
	if (isset($_REQUEST["UpdateID"])) {
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
	}
	?>
	<br>
	<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-10">
			<table class="table table-bordered table-sm table-striped" cellspacing="0" align="center">
				<tr class="HeaderOfTable">
					<td align="center" class="table-info"><?php echo C_MY_TITLE_PROJECTMEMBERS ?></td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0">
							<?
							if (!isset($_REQUEST["UpdateID"])) {
							?>
								<input type="hidden" name="ProjectID" id="ProjectID" value='<? if (isset($_REQUEST["ProjectID"])) echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
							<? } ?>
							<tr>
								<td width="1%" nowrap>
									<?php echo C_USER_CODE ?>
								</td>
								<td nowrap>
									<? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
										<input type=hidden name="Item_PersonID" id="Item_PersonID">
										<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> <a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName");'>[انتخاب]</a>
									<? } else { ?>
										<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> <? } ?>
								</td>
							</tr>
							<tr>
								<td width="1%" nowrap>
									<?php echo C_PERMISSION_TYPE ?>
								</td>
								<td nowrap>
									<? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
										<select name="Item_AccessType" id="Item_AccessType">
											<option value='MEMBER'>عضو</option>
											<option value='VIEWER'>ناظر</option>
											<option value='MANAGER'>مدیر</option>
										</select>
									<? } else { ?>
										<span id="Item_AccessType" name="Item_AccessType"></span> <? } ?>
								</td>
							</tr>
							<tr>
								<td width="1%" nowrap>
									<?php echo C_USER_PARTNERSHIP_PERSENTAGE ?>
								</td>
								<td nowrap>
									<? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
										<input type="text" name="Item_ParticipationPercent" id="Item_ParticipationPercent" maxlength="3" size="3">%
									<? } else { ?>
										<span id="Item_ParticipationPercent" name="Item_ParticipationPercent"></span>
									<? } ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="FooterOfTable">
					<td align="center">
						<? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess)) {
						?>
							<input type="button" class="btn btn-primary" onclick="javascript: ValidateForm();" value="<?php echo C_STORE ?>">
						<? } ?>
						<input type="button" class="btn btn-danger" onclick="javascript: window.close();" value="<?php echo C_CLOSE ?>">
					</td>
				</tr>
			</table>
			<input type="hidden" name="Save" id="Save" value="1">
		</div>
		<div class="col-md-1"></div>
	</div>


</form>
<script>
	<? echo $LoadDataJavascriptCode; ?>

	function ValidateForm() {
		document.f1.submit();
	}
</script>

</html>