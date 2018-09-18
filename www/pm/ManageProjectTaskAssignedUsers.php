<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کاربران منتسب به کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTaskAssignedUsers.class.php");
include ("classes/ProjectTasks.class.php");
include("classes/ProjectTasksSecurity.class.php");
include('../sharedClasses/sendLetterModule.php');
if ($_SESSION['UserID'] == 'gholami-a') {
ini_set('display_errors','off');
}
ini_set('display_errors','off');
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_ProjectTaskAssignedUsers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["User"]->PersonID, $obj->ProjectTaskID);
	$ProjectTaskID = $obj->ProjectTaskID;
}
else
{
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["User"]->PersonID, $_REQUEST["ProjectTaskID"]);
	$ProjectTaskID = $_REQUEST["ProjectTaskID"];
}
$ProjectTaskObj = new be_ProjectTasks();
$ProjectTaskObj->LoadDataFromDatabase($ProjectTaskID);
$ProjectID = $ProjectTaskObj->ProjectID;

$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_ProjectTaskAssignedUsers")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_ProjectTaskAssignedUsers")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_ProjectTaskAssignedUsers")=="PRIVATE" && $_SESSION["User"]->PersonID==$obj->CreatorID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_ProjectTaskAssignedUsers")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_ProjectTaskAssignedUsers")=="PRIVATE" && $_SESSION["User"]->PersonID==$obj->CreatorID)
		$HasViewAccess = true;
} 
else 
{ 
	$HasViewAccess = true;
} 
if(!$HasViewAccess)
{ 
	echo "مجوز مشاهده این رکورد را ندارید";
	die();
} 
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["ProjectTaskID"]))
		$Item_ProjectTaskID=$_REQUEST["ProjectTaskID"];
	if(isset($_REQUEST["Item_PersonID"]))
		$Item_PersonID=$_REQUEST["Item_PersonID"];
	if(isset($_REQUEST["Item_AssignDescription"]))
		$Item_AssignDescription=$_REQUEST["Item_AssignDescription"];
	if(isset($_REQUEST["Item_ParticipationPercent"]))
		$Item_ParticipationPercent=$_REQUEST["Item_ParticipationPercent"];
	if(isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID=$_REQUEST["Item_CreatorID"];
	if(isset($_REQUEST["Item_AssignType"]))
		$Item_AssignType=$_REQUEST["Item_AssignType"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		if($HasAddAccess)
		manage_ProjectTaskAssignedUsers::Add($Item_ProjectTaskID
				, $Item_PersonID
				, $Item_AssignDescription
				, $Item_ParticipationPercent
				, $Item_AssignType

				);
	}	
	else 
	{	
		if($HasUpdateAccess)
		manage_ProjectTaskAssignedUsers::Update($_REQUEST["UpdateID"] 
				, $Item_PersonID
				, $Item_AssignDescription
				, $Item_ParticipationPercent
				, $Item_AssignType
				);
	}	
	if(isset($_REQUEST["SendLetter"]))
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("select * from projectmanagement.AccountSpecs where PersonID=?");
		$res = $mysql->ExecuteStatement(array($Item_PersonID));
		$rec = $res->fetch();
		$ReceiverUserID = $rec["WebUserID"];

		$LetterMessage .= "با سلام <br>";
		$LetterMessage .= "کاری با شماره ".$ProjectTaskObj->ProjectTaskID." با عنوان \"".$ProjectTaskObj->title."\" به شما انتساب یافته است.<br>";
		$LetterMessage .= "<p align=center><font color=green>(اين نامه به صورت اتوماتيك و توسط اتوماسيون اداري از طرف كاربر سيستم ارسال گرديده لذا خواهشمند است از پاسخ دادن و يا ارجاع آن به شخص ديگر خودداري فرمائيد.)</font>";
		SendLetterModule("انتساب کار با عنوان: ".$ProjectTaskObj->title, $LetterMessage, $ReceiverUserID, "");
	}
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ProjectTaskAssignedUsers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
		$LoadDataJavascriptCode .= "document.getElementById('Span_PersonID_FullName').innerHTML='".$obj->PersonID_FullName."'; \r\n "; 
		if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
			$LoadDataJavascriptCode .= "document.getElementById('Item_PersonID').value='".$obj->PersonID."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_AssignDescription.value='".htmlentities($obj->AssignDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_AssignDescription').innerHTML='".htmlentities($obj->AssignDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ParticipationPercent.value='".htmlentities($obj->ParticipationPercent, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ParticipationPercent').innerHTML='".htmlentities($obj->ParticipationPercent, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_AssignType.value='".htmlentities($obj->AssignType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_AssignType').innerHTML='".htmlentities($obj->AssignType_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_ProjectTasks::ShowSummary($_REQUEST["ProjectTaskID"]);
echo manage_ProjectTasks::ShowTabs($_REQUEST["ProjectTaskID"], "ManageProjectTaskAssignedUsers");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش کاربران منتسب به کار</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="ProjectTaskID" id="ProjectTaskID" value='<? if(isset($_REQUEST["ProjectTaskID"])) echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
	<font color=red>*</font> نام و نام خانوادگی
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type=hidden name="Item_PersonID" id="Item_PersonID">
	<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> 	
	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName<?php if($ProjectID!="0") echo "&ProjectID=".$ProjectID ?>");'>[انتخاب]</a>
	<? } else { ?>
	<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح انتساب
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_AssignDescription" id="Item_AssignDescription" maxlength="500" size="40">
	<? } else { ?>
	<span id="Item_AssignDescription" name="Item_AssignDescription"></span> 
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 درصد مشارکت
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_ParticipationPercent" id="Item_ParticipationPercent" maxlength="3" size="3" value="100">%
	<? } else { ?>
	<span id="Item_ParticipationPercent" name="Item_ParticipationPercent"></span> 
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نقش
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_AssignType" id="Item_AssignType" >
		<option value='EXECUTOR'>مجری</option>
		<option value='VIEWER'>ناظر</option>
	</select>
	<? } else { ?>
	<span id="Item_AssignType" name="Item_AssignType"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td colspan=2>
	<input type=checkbox name=SendLetter> ارسال نامه آگاهی دهنده برای فرد انتخاب شده
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess))
	{
?>
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
<? } ?>
<? if($HasAddAccess || $HasUpdateAccess) { ?>
 <input type="button" onclick="javascript: document.location='ManageProjectTaskAssignedUsers.php?ProjectTaskID=<?php echo $_REQUEST["ProjectTaskID"]; ?>'" value="جدید">
<?php } ?>
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		if(document.getElementById('Item_PersonID'))
		{
			if(document.getElementById('Item_PersonID').value=='')
			{
				alert('مقداری در شخص مربوطه وارد نشده است');
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
if($ppc->GetPermission("Add_ProjectTaskAssignedUsers")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectTaskAssignedUsers");
$UpdateType = $ppc->GetPermission("Update_ProjectTaskAssignedUsers");
$OrderByFieldName = "ProjectTaskAssignedUserID";
$OrderType = "";
if(isset($_REQUEST["OrderByFieldName"]))
{
	$OrderByFieldName = $_REQUEST["OrderByFieldName"];
	$OrderType = $_REQUEST["OrderType"];
}
$res = manage_ProjectTaskAssignedUsers::GetList($_REQUEST["ProjectTaskID"], $OrderByFieldName, $OrderType); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->ProjectTaskAssignedUserID])) 
	{
		if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorID==$_SESSION["PersonID"]))
			{
			manage_ProjectTaskAssignedUsers::Remove($res[$k]->ProjectTaskAssignedUserID); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_ProjectTaskAssignedUsers::GetList($_REQUEST["ProjectTaskID"], $OrderByFieldName, $OrderType); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_ProjectTaskID" name="Item_ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="7">
	کاربران منتسب به کار
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td width=20% nowrap><a href="javascript: Sort('persons2.plname, persons2.pfname', 'ASC');">نام خانوادگی و نام</a></td>
	<td width=5% nowrap><a href="javascript: Sort('ParticipationPercent', 'ASC');">درصد مشارکت</a></td>
	<td width=1% nowrap><a href="javascript: Sort('AssignType', 'ASC');">نقش</a></td>
	<td><a href="javascript: Sort('AssignDescription', 'ASC');">شرح انتساب</a></td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorID==$_SESSION["PersonID"]))
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectTaskAssignedUserID."\">";
	else
		echo " ";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageProjectTaskAssignedUsers.php?UpdateID=".$res[$k]->ProjectTaskAssignedUserID."&ProjectTaskID=".$_REQUEST["ProjectTaskID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td nowrap>";
	echo "<img width=80 src='ShowPersonPhoto.php?PersonID=".$res[$k]->PersonID."'> ";
	echo $res[$k]->PersonID_FullName."</td>";
	echo "	<td>".htmlentities($res[$k]->ParticipationPercent, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".$res[$k]->AssignType_Desc."</td>";
	echo "	<td>&nbsp;".htmlentities($res[$k]->AssignDescription, ENT_QUOTES, 'UTF-8')."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="7" align="center">
<? if($RemoveType!="NONE") { ?>
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
<? } ?>
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewProjectTaskAssignedUsers.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	f2.OrderByFieldName.value=OrderByFieldName; 
	f2.OrderType.value=OrderType; 
	f2.submit();
}
</script>
</html>
