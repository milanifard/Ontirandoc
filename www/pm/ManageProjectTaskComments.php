<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : یادداشتها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTaskComments.class.php");
include ("classes/ProjectTasks.class.php");
include("classes/ProjectTasksSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_ProjectTaskComments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectTaskID);
}
else
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_ProjectTaskComments")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_ProjectTaskComments")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_ProjectTaskComments")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_ProjectTaskComments")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_ProjectTaskComments")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
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
	if(isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID=$_REQUEST["Item_CreatorID"];
	if(isset($_REQUEST["Item_CreateTime"]))
		$Item_CreateTime=$_REQUEST["Item_CreateTime"];
	if(isset($_REQUEST["Item_CommentBody"]))
		$Item_CommentBody=$_REQUEST["Item_CommentBody"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		if($HasAddAccess)
		manage_ProjectTaskComments::Add($Item_ProjectTaskID
				, $Item_CommentBody
				);
	}	
	else 
	{	
		if($HasUpdateAccess)
		manage_ProjectTaskComments::Update($_REQUEST["UpdateID"] 
				, $Item_CommentBody
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$CommentBody = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ProjectTaskComments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$CommentBody =  htmlentities($obj->CommentBody, ENT_QUOTES, 'UTF-8');
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_CommentBody').innerHTML='".htmlentities($obj->CommentBody, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_ProjectTasks::ShowSummary($_REQUEST["ProjectTaskID"]);
echo manage_ProjectTasks::ShowTabs($_REQUEST["ProjectTaskID"], "ManageProjectTaskComments");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش یادداشتها</td>
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
	<font color=red>*</font> متن
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<textarea name="Item_CommentBody" id="Item_CommentBody" cols="80" rows="5"><?php echo $CommentBody ?></textarea>
	<? } else { ?>
	<span id="Item_CommentBody" name="Item_CommentBody"></span> 
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
 <input type="button" onclick="javascript: document.location='ManageProjectTaskComments.php?ProjectTaskID=<?php echo $_REQUEST["ProjectTaskID"]; ?>'" value="جدید">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		if(document.getElementById('Item_CommentBody'))
		{
			if(document.getElementById('Item_CommentBody').value=='')
			{
				alert('مقداری در متن وارد نشده است');
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
if($ppc->GetPermission("Add_ProjectTaskComments")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectTaskComments");
$UpdateType = $ppc->GetPermission("Update_ProjectTaskComments");
$NumberOfRec = 30;
 $k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
	$FromRec = $_REQUEST["PageNumber"]*$NumberOfRec;
	$PageNumber = $_REQUEST["PageNumber"];
}
else
{
	$FromRec = 0; 
}
$res = manage_ProjectTaskComments::GetList($_REQUEST["ProjectTaskID"], $FromRec, $NumberOfRec); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->ProjectTaskCommentID])) 
	{
		if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorID==$_SESSION["PersonID"]))
			{
			manage_ProjectTaskComments::Remove($res[$k]->ProjectTaskCommentID); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_ProjectTaskComments::GetList($_REQUEST["ProjectTaskID"], $FromRec, $NumberOfRec); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_ProjectTaskID" name="Item_ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="6">
	یادداشتها
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>متن</td>	
	<td width=5% nowrap>ایجاد کننده</td>
	<td width=5% nowrap>زمان ایجاد</td>
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
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectTaskCommentID."\">";
	else
		echo " ";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td><a href=\"ManageProjectTaskComments.php?UpdateID=".$res[$k]->ProjectTaskCommentID."&ProjectTaskID=".$_REQUEST["ProjectTaskID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".str_replace("\r\n", "<br>", htmlentities($res[$k]->CommentBody, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
	echo "	<td nowrap>".$res[$k]->CreateTime_Shamsi."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="6" align="center">
<? if($RemoveType!="NONE") { ?>
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
<? } ?>
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="6" align="right">
<?
for($k=0; $k<manage_ProjectTaskComments::GetCount($_REQUEST["ProjectTaskID"])/$NumberOfRec; $k++)
{
	if($PageNumber!=$k)
		echo "<a href='javascript: ShowPage(".($k).")'>";
	echo ($k+1);
	if($PageNumber!=$k)
		echo "</a>";
	echo " ";
}
?>
</td></tr>
</table>
</form>
<form target="_blank" method="post" action="NewProjectTaskComments.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ShowPage(PageNumber)
{
	f2.PageNumber.value=PageNumber; 
	f2.submit();
}
</script>
</html>
