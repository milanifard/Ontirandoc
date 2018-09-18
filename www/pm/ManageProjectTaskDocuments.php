<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اسناد کارها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTaskDocuments.class.php");
include ("classes/ProjectTasks.class.php");
include("classes/ProjectTasksSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_ProjectTaskDocuments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectTaskID);
}
else
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_ProjectTaskDocuments")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_ProjectTaskDocuments")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_ProjectTaskDocuments")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_ProjectTaskDocuments")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_ProjectTaskDocuments")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
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
	if(isset($_REQUEST["Item_DocumentDescription"]))
		$Item_DocumentDescription=$_REQUEST["Item_DocumentDescription"];
	$Item_FileContent = "";
	$Item_FileName = "";
	/*if (trim($_FILES['Item_FileContent']['name']) != '')
	{
		if ($_FILES['Item_FileContent']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_FileContent']['error'];
		}
		else
		{
			$_size = $_FILES['Item_FileContent']['size'];
			$_name = $_FILES['Item_FileContent']['tmp_name'];
			$Item_FileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
			$Item_FileName = trim($_FILES['Item_FileContent']['name']);
		}
	}*/
/*-------------added by gholami 94/09/08------------------*/

	if (trim($_FILES['Item_FileContent']['name']) != '')
	{
		if ($_FILES['Item_FileContent']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_FileContent']['error'];
		}
		else
		{
			//$_size = $_FILES['Item_FileContent']['size'];
			//$_name = $_FILES['Item_FileContent']['tmp_name'];
			//$Item_FileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
			
			//$st = split ( '\.', $_FILES ['Item_FileContent'] ['name'] );
                        $st =  preg_split( "/\./", $_FILES ['Item_FileContent'] ['name'] );

			$extension = $st [count ( $st ) - 1];	
			
			$Item_FileName = $extension;
		}
	}
/*-------------------------------------*/

	if(isset($_REQUEST["Item_FileName"]))
		$Item_FileName=$_REQUEST["Item_FileName"];
	if(!isset($_REQUEST["UpdateID"])) 
	{
		if($HasAddAccess)
		$TaskDocID=manage_ProjectTaskDocuments::Add($Item_ProjectTaskID
				, $Item_DocumentDescription
				, $Item_FileContent
				, $Item_FileName
				);
	}	
	else 
	{	
		if($HasUpdateAccess)
		manage_ProjectTaskDocuments::Update($_REQUEST["UpdateID"] 
				, $Item_DocumentDescription
				, $Item_FileContent
				, $Item_FileName
				);
 $TaskDocID = $_REQUEST["UpdateID"];
	}	
/*-------------added by gholami 94/09/08------------------*/
	$Item_FileContent = "";
	if (isset ( $_FILES ['Item_FileContent'] ) && trim ( $_FILES ['Item_FileContent'] ['tmp_name'] ) != '') 
	{
		$st = split ( '\.', $_FILES ['Item_FileContent'] ['name'] );
		$extension = $st [count ( $st ) - 1];	
		$fp = fopen("/mystorage/PlanAndProjectDocuments/TaskDocuments/" .$TaskDocID . "." . $extension, "w");
		fwrite ($fp, fread ( fopen ( $_FILES ['Item_FileContent'] ['tmp_name'], 'r' ), $_FILES ['Item_FileContent']['size']));
		fclose ($fp);			
		$Item_FileContent = $extension;
	}	
/*---------------------------------------------*/

	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ProjectTaskDocuments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_DocumentDescription.value='".htmlentities($obj->DocumentDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_DocumentDescription').innerHTML='".htmlentities($obj->DocumentDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_ProjectTasks::ShowSummary($_REQUEST["ProjectTaskID"]);
echo manage_ProjectTasks::ShowTabs($_REQUEST["ProjectTaskID"], "ManageProjectTaskDocuments");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش اسناد کارها</td>
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
 شرح
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_DocumentDescription" id="Item_DocumentDescription" maxlength="1000" size="40">
	<? } else { ?>
	<span id="Item_DocumentDescription" name="Item_DocumentDescription"></span> 
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 فایل ضمیمه
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="file" name="Item_FileContent" id="Item_FileContent">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->FileName!="") { ?>
	        <a  href='ReciptFile.php?PtdID=<? echo $obj->ProjectTaskDocumentID?>&FName_ptdID=<?echo $obj->FileName;?>'>دریافت فایل [<?php echo $obj->FileName; ?>]</a>

	<? } ?>
	<? } else { ?>
	<? if(isset($_REQUEST["UpdateID"]) && $obj->FileName!="") { ?>
        <a  href='ReciptFile.php?PtdID=<? echo $obj->ProjectTaskDocumentID?>&FName_ptdID=<?echo $obj->FileName;?>'>دریافت فایل [<?php echo $obj->FileName; ?>]</a>

	<? } ?>
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
 <input type="button" onclick="javascript: document.location='ManageProjectTaskDocuments.php?ProjectTaskID=<?php echo $_REQUEST["ProjectTaskID"]; ?>'" value="جدید">
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
$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_ProjectTaskDocuments")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectTaskDocuments");
$UpdateType = $ppc->GetPermission("Update_ProjectTaskDocuments");
$res = manage_ProjectTaskDocuments::GetList($_REQUEST["ProjectTaskID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->ProjectTaskDocumentID])) 
	{
		if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorID==$_SESSION["PersonID"]))
			{
			manage_ProjectTaskDocuments::Remove($res[$k]->ProjectTaskDocumentID); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_ProjectTaskDocuments::GetList($_REQUEST["ProjectTaskID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_ProjectTaskID" name="Item_ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="7">
	اسناد کارها
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>شرح</td>
	<td width=5%>ضمیمه</td>
	<td width=5% nowrap>ایجاد کننده</td>
	<td width=5% nowrap>تاریخ ایجاد</td>
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
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectTaskDocumentID."\">";
	else
		echo " ";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageProjectTaskDocuments.php?UpdateID=".$res[$k]->ProjectTaskDocumentID."&ProjectTaskID=".$_REQUEST["ProjectTaskID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".htmlentities($res[$k]->DocumentDescription, ENT_QUOTES, 'UTF-8')."</td>";

/*echo "	<td><a href='DownloadFile.php?FileType=TaskDocument&RecID=".$res[$k]->ProjectTaskDocumentID."'><img src='images/Download.gif'></a></td>";*/
echo "<td>";
if ($res[$k]->FileName != "")
	echo "<a target='_blank' href=\"ReciptFile.php?PtdID=" . $res[$k]->ProjectTaskDocumentID . "&FName_ptdID=" . $res[$k]->FileName . "\"><img border=0 src='images/Download.gif' id='fileimg' title='دریافت فایل'></a>";
else
	echo "ندارد";
echo "</td>";
	
	echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
	echo "	<td nowrap>".$res[$k]->CreateTime_Shamsi."</td>";
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
<form target="_blank" method="post" action="NewProjectTaskDocuments.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
