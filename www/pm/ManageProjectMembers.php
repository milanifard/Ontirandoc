<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اعضای پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectMembers.class.php");
include ("classes/projects.class.php");
include("classes/projectsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_ProjectMembers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectID);
}
else
	$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_ProjectMembers")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_ProjectMembers")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_ProjectMembers")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_ProjectMembers")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_ProjectMembers")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
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
	if(isset($_REQUEST["ProjectID"]))
		$Item_ProjectID=$_REQUEST["ProjectID"];
	if(isset($_REQUEST["Item_PersonID"]))
		$Item_PersonID=$_REQUEST["Item_PersonID"];
	if(isset($_REQUEST["Item_AccessType"]))
		$Item_AccessType=$_REQUEST["Item_AccessType"];
	if(isset($_REQUEST["Item_ParticipationPercent"]))
		$Item_ParticipationPercent=$_REQUEST["Item_ParticipationPercent"];
	if(isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID=$_REQUEST["Item_CreatorID"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		if($HasAddAccess)
		manage_ProjectMembers::Add($Item_ProjectID
				, $Item_PersonID
				, $Item_AccessType
				, $Item_ParticipationPercent
				);
	}	
	else 
	{	
		if($HasUpdateAccess)
		manage_ProjectMembers::Update($_REQUEST["UpdateID"] 
				, $Item_PersonID
				, $Item_AccessType
				, $Item_ParticipationPercent
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ProjectMembers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
		$LoadDataJavascriptCode .= "document.getElementById('Span_PersonID_FullName').innerHTML='".$obj->PersonID_FullName."'; \r\n "; 
		if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
			$LoadDataJavascriptCode .= "document.getElementById('Item_PersonID').value='".$obj->PersonID."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_AccessType.value='".htmlentities($obj->AccessType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_AccessType').innerHTML='".htmlentities($obj->AccessType_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ParticipationPercent.value='".htmlentities($obj->ParticipationPercent, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ParticipationPercent').innerHTML='".htmlentities($obj->ParticipationPercent, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_projects::ShowSummary($_REQUEST["ProjectID"]);
echo manage_projects::ShowTabs($_REQUEST["ProjectID"], "ManageProjectMembers");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش اعضای پروژه</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="ProjectID" id="ProjectID" value='<? if(isset($_REQUEST["ProjectID"])) echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 نام و نام خانوادگی
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type=hidden name="Item_PersonID" id="Item_PersonID">
	<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> 	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName");'>[انتخاب]</a>
	<? } else { ?>
	<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نوع دسترسی
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_AccessType" id="Item_AccessType" >
		<option value='MEMBER'>عضو</option>
		<option value='VIEWER'>ناظر</option>
		<option value='MANAGER'>مدیر</option>
	</select>
	<? } else { ?>
	<span id="Item_AccessType" name="Item_AccessType"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 درصد مشارکت
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_ParticipationPercent" id="Item_ParticipationPercent" maxlength="3" size="3"> %
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
<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess))
	{
?>
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
<? } ?>
 <input type="button" onclick="javascript: document.location='ManageProjectMembers.php?ProjectID=<?php echo $_REQUEST["ProjectID"]; ?>'" value="جدید">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_ProjectMembers")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectMembers");
$UpdateType = $ppc->GetPermission("Update_ProjectMembers");
$res = manage_ProjectMembers::GetList($_REQUEST["ProjectID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->ProjectmemberID])) 
	{
		if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorID==$_SESSION["PersonID"]))
			{
			manage_ProjectMembers::Remove($res[$k]->ProjectmemberID); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_ProjectMembers::GetList($_REQUEST["ProjectID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_ProjectID" name="Item_ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	اعضای پروژه
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>نام و نام خانوادگی</td>
	<td width=10% nowrap>نوع دسترسی</td>
	<td width=5% nowrap>درصد زمانی فرد در این پروژه</td>
	<td width=5% nowrap>درصد زمانی استفاده شده کل</td>
	<td width=1% nowrap>مجوزها</td>
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
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectmemberID."\">";
	else
		echo " ";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageProjectMembers.php?UpdateID=".$res[$k]->ProjectmemberID."&ProjectID=".$_REQUEST["ProjectID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td><img width=80 src='ShowPersonPhoto.php?PersonID=".$res[$k]->PersonID."'> ".$res[$k]->PersonID_FullName."</td>";
	echo "	<td>".$res[$k]->AccessType_Desc."</td>";
	echo "	<td>".htmlentities($res[$k]->ParticipationPercent, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>";
	echo "	<a href='ShowPersonStatus.php?PersonID=".$res[$k]->PersonID."'>";
	echo manage_ProjectMembers::GetSumPercentageUse($res[$k]->PersonID);
	echo "	</a>";
	echo "	</td>";
	echo "	<td><a target=_blank href='projectsSetSecurity.php?RecID=".$_REQUEST["ProjectID"]."&SelectedPersonID=".$res[$k]->PersonID."'><img src='images/security.gif' title='مجوزها'></a></td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="8" align="center">
<? if($RemoveType!="NONE") { ?>
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
<? } ?>
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewProjectMembers.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectID" name="ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
