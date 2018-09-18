<?php 
/*
 صفحه  تنظیم دسترسی به فیلدها و جداول جزییات مربوط به : جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-30
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
function CreateYesNoSelectBox($SelectBoxName, $CurValue)
{
	$ret = "<select name='".$SelectBoxName."' id='".$SelectBoxName."'>";
	$ret .= "<option value='YES'>دارد";
	$ret .= "<option value='NO' ";
	if($CurValue=="NO") $ret .= "selected"; 
	$ret .= ">ندارد";
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
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["RecID"]);
$UpdateType1 = $ppc->GetPermission("Update_SessionMembers");
$UpdateType2 = $ppc->GetPermission("Update_SessionOtherUsers");
if($UpdateType1!="PUBLIC" && $UpdateType2!="PUBLIC")
{
	echo "مجوز وجود ندارد";
	die();
}


$mysql = pdodb::getInstance();
$query = "select concat(pfname, ' ', plname) as FullName from hrmstotal.persons where PersonID=?";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($SelectedPersonID));
$PersonName = "";
if($rec=$res->fetch())
	$PersonName = $rec["FullName"];
if(isset($_REQUEST["Save"])) 
{
	security_UniversitySessions::ResetRecordFieldsPermission($RecID, $SelectedPersonID);
	security_UniversitySessions::ResetRecordDetailTablesPermission($RecID, $SelectedPersonID);
	security_UniversitySessions::SaveFieldPermission($RecID, 'RemoveSession', $SelectedPersonID, $_REQUEST["RemoveSession"]);
	security_UniversitySessions::SaveFieldPermission($RecID, 'SessionTypeID', $SelectedPersonID, $_REQUEST["SessionTypeID"]);
	security_UniversitySessions::SaveFieldPermission($RecID, 'SessionNumber', $SelectedPersonID, $_REQUEST["SessionNumber"]);
	security_UniversitySessions::SaveFieldPermission($RecID, 'SessionTitle', $SelectedPersonID, $_REQUEST["SessionTitle"]);
	security_UniversitySessions::SaveFieldPermission($RecID, 'SessionDate', $SelectedPersonID, $_REQUEST["SessionDate"]);
	security_UniversitySessions::SaveFieldPermission($RecID, 'SessionLocation', $SelectedPersonID, $_REQUEST["SessionLocation"]);
	security_UniversitySessions::SaveFieldPermission($RecID, 'SessionStartTime', $SelectedPersonID, $_REQUEST["SessionStartTime"]);
	security_UniversitySessions::SaveFieldPermission($RecID, 'SessionDurationTime', $SelectedPersonID, $_REQUEST["SessionDurationTime"]);
	security_UniversitySessions::SaveFieldPermission($RecID, 'SessionStatus', $SelectedPersonID, $_REQUEST["SessionStatus"]);
	security_UniversitySessions::SaveFieldPermission($RecID, 'SessionDescisionsFile', $SelectedPersonID, $_REQUEST["SessionDescisionsFile"]);
	security_UniversitySessions::SaveDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, $_REQUEST["Add_SessionDecisions"], $_REQUEST["Remove_SessionDecisions"], $_REQUEST["Update_SessionDecisions"], $_REQUEST["View_SessionDecisions"]);
	security_UniversitySessions::SaveDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, $_REQUEST["Add_SessionDocuments"], $_REQUEST["Remove_SessionDocuments"], $_REQUEST["Update_SessionDocuments"], $_REQUEST["View_SessionDocuments"]);
	security_UniversitySessions::SaveDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, $_REQUEST["Add_SessionMembers"], $_REQUEST["Remove_SessionMembers"], $_REQUEST["Update_SessionMembers"], $_REQUEST["View_SessionMembers"]);
	security_UniversitySessions::SaveDetailTablePermission($RecID, 'MembersPAList', $SelectedPersonID, "NONE", "NONE", $_REQUEST["Update_MembersPAList"], $_REQUEST["View_MembersPAList"]);
	security_UniversitySessions::SaveDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, $_REQUEST["Add_SessionOtherUsers"], $_REQUEST["Remove_SessionOtherUsers"], $_REQUEST["Update_SessionOtherUsers"], $_REQUEST["View_SessionOtherUsers"]);
	security_UniversitySessions::SaveDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, $_REQUEST["Add_SessionPreCommands"], $_REQUEST["Remove_SessionPreCommands"], $_REQUEST["Update_SessionPreCommands"], $_REQUEST["View_SessionPreCommands"]);
	security_UniversitySessions::SaveDetailTablePermission($RecID, 'SessionHistory', $SelectedPersonID, "YES", "NONE", "NONE", $_REQUEST["View_SessionHistory"]);
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
<td>
	<table width="100%" border="0">
	<tr>
		<td width="1%" nowrap colspan=2>
		کاربر امکان حذف این جلسه را    
		<?php 
			echo "<select name='RemoveSession' id='RemoveSession'>";
			echo "<option value='WRITE'>دارد";
			echo "<option value='NONE' ";
			if(security_UniversitySessions::ReadFieldPermission($RecID, 'RemoveSession', $SelectedPersonID)=="NONE") 
				echo "selected"; 
			echo ">ندارد";
			echo "</select>";
		?>		
		</td>
	</tr>
	<tr>
		<td bgcolor=#cccccc colspan=2>
		<b> دسترسی به گزینه های جلسه:</b> 
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		نوع جلسه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionTypeID", security_UniversitySessions::ReadFieldPermission($RecID, 'SessionTypeID', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		شماره جلسه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionNumber", security_UniversitySessions::ReadFieldPermission($RecID, 'SessionNumber', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		عنوان
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionTitle", security_UniversitySessions::ReadFieldPermission($RecID, 'SessionTitle', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		تاریخ
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionDate", security_UniversitySessions::ReadFieldPermission($RecID, 'SessionDate', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		مکان
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionLocation", security_UniversitySessions::ReadFieldPermission($RecID, 'SessionLocation', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		زمان شروع
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionStartTime", security_UniversitySessions::ReadFieldPermission($RecID, 'SessionStartTime', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		مدت جلسه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionDurationTime", security_UniversitySessions::ReadFieldPermission($RecID, 'SessionDurationTime', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		وضعیت
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionStatus", security_UniversitySessions::ReadFieldPermission($RecID, 'SessionStatus', $SelectedPersonID)) ?>
		</td>
	</tr>
	<tr>
		<td width="1%" nowrap>
		فایل صورتجلسه
		</td>
		<td>
		<? echo CreateReadWriteSelectBox("SessionDescisionsFile", security_UniversitySessions::ReadFieldPermission($RecID, 'SessionDescisionsFile', $SelectedPersonID)) ?>
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
				<? echo CreateYesNoSelectBox("Add_SessionDecisions", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Remove_SessionDecisions", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Update_SessionDecisions", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("View_SessionDecisions", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionDecisions', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				مستندات
				</td>
				<td>
				<? echo CreateYesNoSelectBox("Add_SessionDocuments", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Remove_SessionDocuments", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Update_SessionDocuments", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("View_SessionDocuments", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionDocuments', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				اعضا
				</td>
				<td>
				<? echo CreateYesNoSelectBox("Add_SessionMembers", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("Remove_SessionMembers", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("Update_SessionMembers", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("View_SessionMembers", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionMembers', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				حضو و غیاب اعضا
				</td>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>
				<? echo CreateNonePublicSelectBox("Update_MembersPAList", security_UniversitySessions::ReadDetailTablePermission($RecID, 'MembersPAList', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("View_MembersPAList", security_UniversitySessions::ReadDetailTablePermission($RecID, 'MembersPAList', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				سایر کاربران
				</td>
				<td>
				<? echo CreateYesNoSelectBox("Add_SessionOtherUsers", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("Remove_SessionOtherUsers", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("Update_SessionOtherUsers", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreateNonePublicSelectBox("View_SessionOtherUsers", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionOtherUsers', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				دستور کار
				</td>
				<td>
				<? echo CreateYesNoSelectBox("Add_SessionPreCommands", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, "Add")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Remove_SessionPreCommands", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, "Remove")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("Update_SessionPreCommands", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, "Update")) ?>
				</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("View_SessionPreCommands", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionPreCommands', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
			<tr>
				<td nowrap>
				سابقه
				</td>
				<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
				<td>
				<? echo CreatePublicPrivateSelectBox("View_SessionHistory", security_UniversitySessions::ReadDetailTablePermission($RecID, 'SessionHistory', $SelectedPersonID, "View")) ?>
				</td>
			</tr>
		</td>
	</table>
</td>
</tr>
<tr class="HeaderOfTable">
<td align="center" colspan=2>
<input type="hidden" name="Save" value="1">
<input type="submit" value="ذخیره">&nbsp;
<input type=button value='بستن' onclick='javascript: window.close()'>
</td>
</tr>
</table>
</form>
<script>

 setInterval(function(){
        
        var xmlhttp;
            if (window.XMLHttpRequest)
            {
                // code for IE7 , Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            }
            else
            {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            
            xmlhttp.open("POST","header.inc.php",true);            
            xmlhttp.send();
        
    }, 60000);
</script>