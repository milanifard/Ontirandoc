<?php 
/*
 صفحه  تنظیم دسترسی به فیلدها و جداول جزییات مربوط به : الگوی جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-30
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionTypesSecurity.class.php");
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

function CreateNonePublicSelectBox($SelectBoxName, $CurValue)
{
	$ret = "<select name='".$SelectBoxName."' id='".$SelectBoxName."'>";
	$ret .= "<option value='PUBLIC'>تمام آیتمها";
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
if(isset($_REQUEST["Save"])) 
{
	security_SessionTypes::ResetRecordFieldsPermission($RecID, $SelectedPersonID);
	security_SessionTypes::ResetRecordDetailTablesPermission($RecID, $SelectedPersonID);
	security_SessionTypes::SaveFieldPermission($RecID, 'CreateNewSession', $SelectedPersonID, $_REQUEST["CreateNewSession"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'RemoveSession', $SelectedPersonID, $_REQUEST["RemoveSession"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'SessionTypeID', $SelectedPersonID, $_REQUEST["SessionTypeID"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'SessionNumber', $SelectedPersonID, $_REQUEST["SessionNumber"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'SessionTitle', $SelectedPersonID, $_REQUEST["SessionTitle"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'SessionDate', $SelectedPersonID, $_REQUEST["SessionDate"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'SessionLocation', $SelectedPersonID, $_REQUEST["SessionLocation"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'SessionStartTime', $SelectedPersonID, $_REQUEST["SessionStartTime"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'SessionDurationTime', $SelectedPersonID, $_REQUEST["SessionDurationTime"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'SessionStatus', $SelectedPersonID, $_REQUEST["SessionStatus"]);
	security_SessionTypes::SaveFieldPermission($RecID, 'SessionDescisionsFile', $SelectedPersonID, $_REQUEST["SessionDescisionsFile"]);
	security_SessionTypes::SaveDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, $_REQUEST["Add_SessionDecisions"], $_REQUEST["Remove_SessionDecisions"], $_REQUEST["Update_SessionDecisions"], $_REQUEST["View_SessionDecisions"]);
	security_SessionTypes::SaveDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, $_REQUEST["Add_SessionDocuments"], $_REQUEST["Remove_SessionDocuments"], $_REQUEST["Update_SessionDocuments"], $_REQUEST["View_SessionDocuments"]);
	security_SessionTypes::SaveDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, $_REQUEST["Add_SessionMembers"], $_REQUEST["Remove_SessionMembers"], $_REQUEST["Update_SessionMembers"], $_REQUEST["View_SessionMembers"]);
	security_SessionTypes::SaveDetailTablePermission($RecID, 'MembersPAList', $SelectedPersonID, "NONE", "NONE", $_REQUEST["Update_MembersPAList"], $_REQUEST["View_MembersPAList"]);
	security_SessionTypes::SaveDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, $_REQUEST["Add_SessionOtherUsers"], $_REQUEST["Remove_SessionOtherUsers"], $_REQUEST["Update_SessionOtherUsers"], $_REQUEST["View_SessionOtherUsers"]);
	security_SessionTypes::SaveDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, $_REQUEST["Add_SessionPreCommands"], $_REQUEST["Remove_SessionPreCommands"], $_REQUEST["Update_SessionPreCommands"], $_REQUEST["View_SessionPreCommands"]);
	security_SessionTypes::SaveDetailTablePermission($RecID, 'SessionHistory', $SelectedPersonID, "YES", "NONE", "NONE", $_REQUEST["View_SessionHistory"]);
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
?><form method=post>
<input type=hidden name='SelectedPersonID' value='<? echo $SelectedPersonID ?>'>
<input type=hidden name='RecID' value='<? echo $RecID ?>'>
<table width="80%" align="center" border="1" cellspacing="0">
<tr class="HeaderOfTable">
<td align=center>
تعریف دسترسی برای <? echo $PersonName ?> 
</td>
</tr>
<tr>
	<td><input type=checkbox name=CheckAll id=CheckAll onclick='javascript: SelectAll()'> دسترسی کامل
</tr>
<tr>
<td>
	<table width="100%" border="0">
	<tr>
		<td width="1%" nowrap colspan=2>
		این شخص امکان ایجاد جلسه ای از این نوع را  
		<?php 
			echo "<select name='CreateNewSession' id='CreateNewSession'>";
			echo "<option value='WRITE'>دارد";
			echo "<option value='NONE' ";
			if(security_SessionTypes::ReadFieldPermission($RecID, 'CreateNewSession', $SelectedPersonID)=="NONE") 
				echo "selected"; 
			echo ">ندارد";
			echo "</select>";
		?>		
		</td>
	</tr>
	<tr> 
		<td width="1%" nowrap colspan=2>
		کاربر امکان حذف جلسه ای از این نوع را که خودش ایجاد کرده باشد   
		<?php 
			echo "<select name='RemoveSession' id='RemoveSession'>";
			echo "<option value='WRITE'>دارد";
			echo "<option value='NONE' ";
			if(security_SessionTypes::ReadFieldPermission($RecID, 'RemoveSession', $SelectedPersonID)=="NONE") 
				echo "selected"; 
			echo ">ندارد";
			echo "</select>";
		?>		
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		نوع جلسه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionTypeID", security_SessionTypes::ReadFieldPermission($RecID, 'SessionTypeID', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		شماره جلسه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionNumber", security_SessionTypes::ReadFieldPermission($RecID, 'SessionNumber', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		عنوان
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionTitle", security_SessionTypes::ReadFieldPermission($RecID, 'SessionTitle', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		تاریخ
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionDate", security_SessionTypes::ReadFieldPermission($RecID, 'SessionDate', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		مکان
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionLocation", security_SessionTypes::ReadFieldPermission($RecID, 'SessionLocation', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		زمان شروع
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionStartTime", security_SessionTypes::ReadFieldPermission($RecID, 'SessionStartTime', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		مدت جلسه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionDurationTime", security_SessionTypes::ReadFieldPermission($RecID, 'SessionDurationTime', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		وضعیت
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionStatus", security_SessionTypes::ReadFieldPermission($RecID, 'SessionStatus', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		فایل صورتجلسه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionDescisionsFile", security_SessionTypes::ReadFieldPermission($RecID, 'SessionDescisionsFile', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<table cellspacing=0 cellpadding=3 border=1>
			<tr class=HeaderOfTable>
				<td>بخش</td>
				<td>ایجاد</td>
				<td>حذف</td>
				<td>ویرایش</td>
				<td>مشاهده</td>
			</tr>
			<tr>
				<td nowrap>
				مصوبات جلسه
				</td>
				<td>
				<? echo CreateYesNoSelectBox("Add_SessionDecisions", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Remove_SessionDecisions", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Update_SessionDecisions", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("View_SessionDecisions", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				مستندات
				</td>
				<td>
				<? echo CreateYesNoSelectBox("Add_SessionDocuments", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Remove_SessionDocuments", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Update_SessionDocuments", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("View_SessionDocuments", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				اعضا
				</td>
				<td>
				<? echo CreateYesNoSelectBox("Add_SessionMembers", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("Remove_SessionMembers", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("Update_SessionMembers", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("View_SessionMembers", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				حضو و غیاب اعضا
				</td>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>
				<? echo CreateNonePublicSelectBox("Update_MembersPAList", security_SessionTypes::ReadDetailTablePermission($RecID, 'MembersPAList', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("View_MembersPAList", security_SessionTypes::ReadDetailTablePermission($RecID, 'MembersPAList', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				سایر کاربران
				</td>
				<td>
				<? echo CreateYesNoSelectBox("Add_SessionOtherUsers", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("Remove_SessionOtherUsers", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("Update_SessionOtherUsers", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("View_SessionOtherUsers", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				دستور کار
				</td>
				<td>
				<? echo CreateYesNoSelectBox("Add_SessionPreCommands", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Remove_SessionPreCommands", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Update_SessionPreCommands", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("View_SessionPreCommands", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				سابقه
				</td>
				<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("View_SessionHistory", security_SessionTypes::ReadDetailTablePermission($RecID, 'SessionHistory', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
		</td>
	</table>
</td>
</tr>
<tr class="HeaderOfTable">
<td align="center" colspan=2>
<input type="hidden" name="Save" value="1">
<input type="submit" value="ذخیره">
</td>
</tr>
</table>
</form>
<script>
	function SelectAll()
	{
		if(document.getElementById('CheckAll').checked)
		{
			document.getElementById('CreateNewSession').value='WRITE';
			document.getElementById('RemoveSession').value='WRITE';
			document.getElementById('SessionTypeID').value='WRITE';
			document.getElementById('SessionNumber').value='WRITE';
			document.getElementById('SessionTitle').value='WRITE';
			document.getElementById('SessionDate').value='WRITE';
			document.getElementById('SessionLocation').value='WRITE';
			document.getElementById('SessionStartTime').value='WRITE';
			document.getElementById('SessionDurationTime').value='WRITE';
			document.getElementById('SessionStatus').value='WRITE';
			document.getElementById('SessionDescisionsFile').value='WRITE';
				      
	 		document.getElementById('Add_SessionDecisions').value='ADD';
	 		document.getElementById('Remove_SessionDecisions').value='PUBLIC';
	 		document.getElementById('Update_SessionDecisions').value='PUBLIC';
	 		document.getElementById('View_SessionDecisions').value='PUBLIC';

	 		document.getElementById('Add_SessionDocuments').value='ADD';
	 		document.getElementById('Remove_SessionDocuments').value='PUBLIC';
	 		document.getElementById('Update_SessionDocuments').value='PUBLIC';
	 		document.getElementById('View_SessionDocuments').value='PUBLIC';

	 		document.getElementById('Add_SessionMembers').value='ADD';
	 		document.getElementById('Remove_SessionMembers').value='PUBLIC';
	 		document.getElementById('Update_SessionMembers').value='PUBLIC';
	 		document.getElementById('View_SessionMembers').value='PUBLIC';

	 		document.getElementById('Add_SessionOtherUsers').value='ADD';
	 		document.getElementById('Remove_SessionOtherUsers').value='PUBLIC';
	 		document.getElementById('Update_SessionOtherUsers').value='PUBLIC';
	 		document.getElementById('View_SessionOtherUsers').value='PUBLIC';

	 		document.getElementById('Add_SessionPreCommands').value='ADD';
	 		document.getElementById('Remove_SessionPreCommands').value='PUBLIC';
	 		document.getElementById('Update_SessionPreCommands').value='PUBLIC';
	 		document.getElementById('View_SessionPreCommands').value='PUBLIC';

	 		document.getElementById('Update_MembersPAList').value='PUBLIC';
	 		document.getElementById('View_MembersPAList').value='PUBLIC';
			
		}
	}
</script>
