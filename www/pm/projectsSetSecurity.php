<?php 
/*
 صفحه  تنظیم دسترسی به فیلدها و جداول جزییات مربوط به : پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-15
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/projects.class.php");
include("classes/projectsSecurity.class.php");
HTMLBegin();
function CreateYesNoSelectBox($SelectBoxName, $CurValue)
{
	$ret = "<select name='".$SelectBoxName."' id='".$SelectBoxName."'>";
	$ret .= "<option value='NO'>ندارد";
	$ret .= "<option value='YES' ";
	if($CurValue=="YES") $ret .= "selected"; 
	$ret .= ">دارد";
	$ret .= "</select>";
	return $ret;
}
function CreateReadWriteSelectBox($SelectBoxName, $CurValue)
{
	$ret = "<select name='".$SelectBoxName."' id='".$SelectBoxName."'>";
	$ret .= "<option value='READ'>فقط مشاهده";
	$ret .= "<option value='WRITE' ";
	if($CurValue=="WRITE") $ret .= "selected"; 
	$ret .= ">قابل ویرایش";
	$ret .= "<option value='NONE' ";
	if($CurValue=="NONE") $ret .= "selected"; 
	$ret .= ">عدم دسترسی";
	$ret .= "</select>";
	return $ret;
}
function CreatePublicPrivateSelectBox($SelectBoxName, $CurValue)
{
	$ret = "<select name='".$SelectBoxName."' id='".$SelectBoxName."'>";
	$ret .= "<option value='PUBLIC'>تمام آیتمها";
	$ret .= "<option value='PRIVATE' ";
	if($CurValue=="PRIVATE") $ret .= "selected"; 
	$ret .= ">فقط آیتمهایی که خود کاربر ایجاد کرده";
	$ret .= "<option value='NONE' ";
	if($CurValue=="NONE") $ret .= "selected"; 
	$ret .= ">عدم دسترسی";
	$ret .= "</select>";
	return $ret;
}
if(!isset($_REQUEST["SelectedPersonID"]))
	die();
$SelectedPersonID = $_REQUEST["SelectedPersonID"]; // کد شخصی که قرار است برای او نحوه دسترسی تعریف شود به این صفحه پاس می شود
if(isset($_REQUEST["RecID"]))
	$RecID = $_REQUEST["RecID"]; // کد رکوردی که دسترسی کاربر بر روی فیلدهای آن تعریف می شود.
else
	$RecID = 0;
$mysql = pdodb::getInstance();
$query = "select concat(pfname, ' ', plname) as FullName from projectmanagement.persons where PersonID=?";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($SelectedPersonID));
$PersonName = "";
if($rec=$res->fetch())
	$PersonName = $rec["FullName"];
$CurProject = new be_projects();
$CurProject->LoadDataFromDatabase($RecID);
if(isset($_REQUEST["Save"])) 
{
	security_projects::ResetRecordFieldsPermission($RecID, $SelectedPersonID);
	security_projects::ResetRecordDetailTablesPermission($RecID, $SelectedPersonID);
	security_projects::SaveFieldPermission($RecID, 'title', $SelectedPersonID, $_REQUEST["title"]);
	security_projects::SaveFieldPermission($RecID, 'ProjectGroupID', $SelectedPersonID, $_REQUEST["ProjectGroupID"]);
	security_projects::SaveFieldPermission($RecID, 'description', $SelectedPersonID, $_REQUEST["description"]);
	security_projects::SaveFieldPermission($RecID, 'StartTime', $SelectedPersonID, $_REQUEST["StartTime"]);
	security_projects::SaveFieldPermission($RecID, 'EndTime', $SelectedPersonID, $_REQUEST["EndTime"]);
	security_projects::SaveFieldPermission($RecID, 'SysCode', $SelectedPersonID, $_REQUEST["SysCode"]);
	security_projects::SaveFieldPermission($RecID, 'ProjectPriority', $SelectedPersonID, $_REQUEST["ProjectPriority"]);
	security_projects::SaveFieldPermission($RecID, 'ProjectStatus', $SelectedPersonID, $_REQUEST["ProjectStatus"]);
	security_projects::SaveDetailTablePermission($RecID, 'ProjectDocumentTypes', $SelectedPersonID, $_REQUEST["Add_ProjectDocumentTypes"], $_REQUEST["Remove_ProjectDocumentTypes"], $_REQUEST["Update_ProjectDocumentTypes"], $_REQUEST["View_ProjectDocumentTypes"]);
	security_projects::SaveDetailTablePermission($RecID, 'ProjectDocuments', $SelectedPersonID, $_REQUEST["Add_ProjectDocuments"], $_REQUEST["Remove_ProjectDocuments"], $_REQUEST["Update_ProjectDocuments"], $_REQUEST["View_ProjectDocuments"]);
	security_projects::SaveDetailTablePermission($RecID, 'ProjectMembers', $SelectedPersonID, $_REQUEST["Add_ProjectMembers"], $_REQUEST["Remove_ProjectMembers"], $_REQUEST["Update_ProjectMembers"], $_REQUEST["View_ProjectMembers"]);
	security_projects::SaveDetailTablePermission($RecID, 'ProjectExternalMembers', $SelectedPersonID, $_REQUEST["Add_ProjectExternalMembers"], $_REQUEST["Remove_ProjectExternalMembers"], $_REQUEST["Update_ProjectExternalMembers"], $_REQUEST["View_ProjectExternalMembers"]);
	security_projects::SaveDetailTablePermission($RecID, 'ProjectMilestones', $SelectedPersonID, $_REQUEST["Add_ProjectMilestones"], $_REQUEST["Remove_ProjectMilestones"], $_REQUEST["Update_ProjectMilestones"], $_REQUEST["View_ProjectMilestones"]);
	security_projects::SaveDetailTablePermission($RecID, 'ProjectTaskActivityTypes', $SelectedPersonID, $_REQUEST["Add_ProjectTaskActivityTypes"], $_REQUEST["Remove_ProjectTaskActivityTypes"], $_REQUEST["Update_ProjectTaskActivityTypes"], $_REQUEST["View_ProjectTaskActivityTypes"]);
	security_projects::SaveDetailTablePermission($RecID, 'ProjectTaskTypes', $SelectedPersonID, $_REQUEST["Add_ProjectTaskTypes"], $_REQUEST["Remove_ProjectTaskTypes"], $_REQUEST["Update_ProjectTaskTypes"], $_REQUEST["View_ProjectTaskTypes"]);
	security_projects::SaveDetailTablePermission($RecID, 'ProjectTaskGroups', $SelectedPersonID, $_REQUEST["Add_ProjectTaskGroups"], $_REQUEST["Remove_ProjectTaskGroups"], $_REQUEST["Update_ProjectTaskGroups"], $_REQUEST["View_ProjectTaskGroups"]);
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
?><form method=post>
<input type=hidden name='SelectedPersonID' value='<? echo $SelectedPersonID ?>'>
<input type=hidden name='RecID' value='<? echo $RecID ?>'>
<table width="80%" align="center" border="1" cellspacing="0">
<tr class="HeaderOfTable">
<td align=center>
تعریف دسترسی برای <? echo $PersonName ?> بر روی پروژه: <b> <? echo $CurProject->title; ?></b>
</td>
</tr>
<tr>
<td>
	<table width="100%" border="0">
	<tr>
		<td width="1%" nowrap>
		عنوان
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("title", security_projects::ReadFieldPermission($RecID, 'title', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		گروه پروژه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("ProjectGroupID", security_projects::ReadFieldPermission($RecID, 'ProjectGroupID', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		شرح
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("description", security_projects::ReadFieldPermission($RecID, 'description', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		شروع
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("StartTime", security_projects::ReadFieldPermission($RecID, 'StartTime', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		پایان
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("EndTime", security_projects::ReadFieldPermission($RecID, 'EndTime', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		سیستم مربوطه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SysCode", security_projects::ReadFieldPermission($RecID, 'SysCode', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		اولویت
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("ProjectPriority", security_projects::ReadFieldPermission($RecID, 'ProjectPriority', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		وضعیت
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("ProjectStatus", security_projects::ReadFieldPermission($RecID, 'ProjectStatus', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		انواع سند پروژه ها
		</td>
		<td>
		ایجاد: <? echo CreateYesNoSelectBox("Add_ProjectDocumentTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectDocumentTypes', $SelectedPersonID, "Add")) ?>
		حذف: <? echo CreatePublicPrivateSelectBox("Remove_ProjectDocumentTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectDocumentTypes', $SelectedPersonID, "Remove")) ?>
		ویرایش: <? echo CreatePublicPrivateSelectBox("Update_ProjectDocumentTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectDocumentTypes', $SelectedPersonID, "Update")) ?>
		مشاهده: <? echo CreatePublicPrivateSelectBox("View_ProjectDocumentTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectDocumentTypes', $SelectedPersonID, "View")) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		مستندات
		</td>
		<td>
		ایجاد: <? echo CreateYesNoSelectBox("Add_ProjectDocuments", security_projects::ReadDetailTablePermission($RecID, 'ProjectDocuments', $SelectedPersonID, "Add")) ?>
		حذف: <? echo CreatePublicPrivateSelectBox("Remove_ProjectDocuments", security_projects::ReadDetailTablePermission($RecID, 'ProjectDocuments', $SelectedPersonID, "Remove")) ?>
		ویرایش: <? echo CreatePublicPrivateSelectBox("Update_ProjectDocuments", security_projects::ReadDetailTablePermission($RecID, 'ProjectDocuments', $SelectedPersonID, "Update")) ?>
		مشاهده: <? echo CreatePublicPrivateSelectBox("View_ProjectDocuments", security_projects::ReadDetailTablePermission($RecID, 'ProjectDocuments', $SelectedPersonID, "View")) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		اعضای پروژه
		</td>
		<td>
		ایجاد: <? echo CreateYesNoSelectBox("Add_ProjectMembers", security_projects::ReadDetailTablePermission($RecID, 'ProjectMembers', $SelectedPersonID, "Add")) ?>
		حذف: <? echo CreatePublicPrivateSelectBox("Remove_ProjectMembers", security_projects::ReadDetailTablePermission($RecID, 'ProjectMembers', $SelectedPersonID, "Remove")) ?>
		ویرایش: <? echo CreatePublicPrivateSelectBox("Update_ProjectMembers", security_projects::ReadDetailTablePermission($RecID, 'ProjectMembers', $SelectedPersonID, "Update")) ?>
		مشاهده: <? echo CreatePublicPrivateSelectBox("View_ProjectMembers", security_projects::ReadDetailTablePermission($RecID, 'ProjectMembers', $SelectedPersonID, "View")) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		اعضای خارجی
		</td>
		<td>
		ایجاد: <? echo CreateYesNoSelectBox("Add_ProjectExternalMembers", security_projects::ReadDetailTablePermission($RecID, 'ProjectExternalMembers', $SelectedPersonID, "Add")) ?>
		حذف: <? echo CreatePublicPrivateSelectBox("Remove_ProjectExternalMembers", security_projects::ReadDetailTablePermission($RecID, 'ProjectExternalMembers', $SelectedPersonID, "Remove")) ?>
		ویرایش: <? echo CreatePublicPrivateSelectBox("Update_ProjectExternalMembers", security_projects::ReadDetailTablePermission($RecID, 'ProjectExternalMembers', $SelectedPersonID, "Update")) ?>
		مشاهده: <? echo CreatePublicPrivateSelectBox("View_ProjectExternalMembers", security_projects::ReadDetailTablePermission($RecID, 'ProjectExternalMembers', $SelectedPersonID, "View")) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		تاریخهای مهم
		</td>
		<td>
		ایجاد: <? echo CreateYesNoSelectBox("Add_ProjectMilestones", security_projects::ReadDetailTablePermission($RecID, 'ProjectMilestones', $SelectedPersonID, "Add")) ?>
		حذف: <? echo CreatePublicPrivateSelectBox("Remove_ProjectMilestones", security_projects::ReadDetailTablePermission($RecID, 'ProjectMilestones', $SelectedPersonID, "Remove")) ?>
		ویرایش: <? echo CreatePublicPrivateSelectBox("Update_ProjectMilestones", security_projects::ReadDetailTablePermission($RecID, 'ProjectMilestones', $SelectedPersonID, "Update")) ?>
		مشاهده: <? echo CreatePublicPrivateSelectBox("View_ProjectMilestones", security_projects::ReadDetailTablePermission($RecID, 'ProjectMilestones', $SelectedPersonID, "View")) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		انواع اقدامات
		</td>
		<td>
		ایجاد: <? echo CreateYesNoSelectBox("Add_ProjectTaskActivityTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskActivityTypes', $SelectedPersonID, "Add")) ?>
		حذف: <? echo CreatePublicPrivateSelectBox("Remove_ProjectTaskActivityTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskActivityTypes', $SelectedPersonID, "Remove")) ?>
		ویرایش: <? echo CreatePublicPrivateSelectBox("Update_ProjectTaskActivityTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskActivityTypes', $SelectedPersonID, "Update")) ?>
		مشاهده: <? echo CreatePublicPrivateSelectBox("View_ProjectTaskActivityTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskActivityTypes', $SelectedPersonID, "View")) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		انواع کارها
		</td>
		<td>
		ایجاد: <? echo CreateYesNoSelectBox("Add_ProjectTaskTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskTypes', $SelectedPersonID, "Add")) ?>
		حذف: <? echo CreatePublicPrivateSelectBox("Remove_ProjectTaskTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskTypes', $SelectedPersonID, "Remove")) ?>
		ویرایش: <? echo CreatePublicPrivateSelectBox("Update_ProjectTaskTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskTypes', $SelectedPersonID, "Update")) ?>
		مشاهده: <? echo CreatePublicPrivateSelectBox("View_ProjectTaskTypes", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskTypes', $SelectedPersonID, "View")) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		گروه کارها
		</td>
		<td>
		ایجاد: <? echo CreateYesNoSelectBox("Add_ProjectTaskGroups", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskGroups', $SelectedPersonID, "Add")) ?>
		حذف: <? echo CreatePublicPrivateSelectBox("Remove_ProjectTaskGroups", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskGroups', $SelectedPersonID, "Remove")) ?>
		ویرایش: <? echo CreatePublicPrivateSelectBox("Update_ProjectTaskGroups", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskGroups', $SelectedPersonID, "Update")) ?>
		مشاهده: <? echo CreatePublicPrivateSelectBox("View_ProjectTaskGroups", security_projects::ReadDetailTablePermission($RecID, 'ProjectTaskGroups', $SelectedPersonID, "View")) ?>
		</td>
	</tr>
	</table>
</td>
</tr>
<tr class="HeaderOfTable">
<td align="center">
<input type="hidden" name="Save" value="1">
<input type="submit" value="ذخیره">&nbsp;
<input type=button value='بستن' onclick='javascript: window.close()'>

</td>
</tr>
</table>
</form>
