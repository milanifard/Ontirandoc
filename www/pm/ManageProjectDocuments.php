<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : مستندات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectDocuments.class.php");
include("classes/projects.class.php");
include("classes/projectsSecurity.class.php");
include("classes/ProjectDocumentTypes.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_ProjectDocuments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectID);
}
else
	$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_ProjectDocuments")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_ProjectDocuments")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_ProjectDocuments")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_ProjectDocuments")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_ProjectDocuments")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
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
	if(isset($_REQUEST["Item_ProjectDocumentTypeID"]))
		$Item_ProjectDocumentTypeID=$_REQUEST["Item_ProjectDocumentTypeID"];
	
	$Item_FileContent = "";
	$Item_FileName = "";
	if (trim($_FILES['Item_FileContent']['name']) != '')
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
			$st = preg_split ( '\.', $_FILES ['Item_FileContent'] ['name'] );
			$extension = $st [count ( $st ) - 1];	
			
			$Item_FileName = $extension;

			
		}
	}

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
	
	if(isset($_REQUEST["Item_FileName"]))
		$Item_FileName=$_REQUEST["Item_FileName"];
	if(isset($_REQUEST["Item_description"]))
		$Item_description=$_REQUEST["Item_description"];
	if(isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID=$_REQUEST["Item_CreatorID"];
	if(isset($_REQUEST["Item_CreateDate"]))
		$Item_CreateDate=$_REQUEST["Item_CreateDate"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		if($HasAddAccess)
		$ProjectDocID=manage_ProjectDocuments::Add($Item_ProjectID
				, $Item_ProjectDocumentTypeID
                                , $Item_FileContent
				, $Item_FileName
				, $Item_description
				);
	}	
	else 
	{	
		if($HasUpdateAccess)
		manage_ProjectDocuments::Update($_REQUEST["UpdateID"] 
				, $Item_ProjectDocumentTypeID
                                , $Item_FileContent
				, $Item_FileName
				, $Item_description
				);
				$ProjectDocID = $_REQUEST["UpdateID"];
	}
	$Item_FileContent = "";

	if (isset ( $_FILES ['Item_FileContent'] ) && trim ( $_FILES ['Item_FileContent'] ['tmp_name'] ) != '') 
	{
		$st = split ( '\.', $_FILES ['Item_FileContent'] ['name'] );
		$extension = $st [count ( $st ) - 1];	
		$fp = fopen("/mystorage/PlanAndProjectDocuments/projects/Documents/" .$ProjectDocID . "." . $extension, "w");
		fwrite ($fp, fread ( fopen ( $_FILES ['Item_FileContent'] ['tmp_name'], 'r' ), $_FILES ['Item_FileContent']['size']));
		fclose ($fp);			
		$Item_FileContent = $extension;
	}	
	
/*	if (isset ( $_FILES ['Item_FileContent'] ) && trim ( $_FILES ['Item_FileContent'] ['tmp_name'] ) != '') 
	{
		$st = split ( '\.', $_FILES ['Item_FileContent'] ['name'] );
		//$extension = $st [count ( $st ) - 1];
			$Item_FileContent=$_FILES ['Item_FileContent'];
	  $fp = fopen($_FILES ['Item_FileContent'] ['tmp_name'], 'r');
      if($fp){    
        $content = fread($fp, filesize($_FILES['Item_FileContent']['tmp_name']));
        fclose($fp);
        if($content){
          $Item_FileContent=$content;
        }
      }
	}
		
	*/
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ProjectDocuments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ProjectDocumentTypeID.value='".htmlentities($obj->ProjectDocumentTypeID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ProjectDocumentTypeID').innerHTML='".htmlentities($obj->ProjectDocumentTypeID_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_description.value='".htmlentities($obj->description, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_description').innerHTML='".htmlentities($obj->description, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_projects::ShowSummary($_REQUEST["ProjectID"]);
echo manage_projects::ShowTabs($_REQUEST["ProjectID"], "ManageProjectDocuments");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش مستندات</td>
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
 نوع سند
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_ProjectDocumentTypeID" id="Item_ProjectDocumentTypeID">
	<? echo manage_ProjectDocumentTypes::CreateSelectOptions($_REQUEST["ProjectID"]); ?>	</select>
	<? } else { ?>
	<span id="Item_ProjectDocumentTypeID" name="Item_ProjectDocumentTypeID"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 فایل
	</td>
	<td  nowrap>
	<input type="file" name="Item_FileContent" id="Item_FileContent">
   </td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<textarea name="Item_description" id="Item_description" cols="80" rows="5"></textarea>
	<? } else { ?>
	<span id="Item_description" name="Item_description"></span> 
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
 <input type="button" onclick="javascript: document.location='ManageProjectDocuments.php?ProjectID=<?php echo $_REQUEST["ProjectID"]; ?>'" value="جدید">
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
if($ppc->GetPermission("Add_ProjectDocuments")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectDocuments");
$UpdateType = $ppc->GetPermission("Update_ProjectDocuments");
$res = manage_ProjectDocuments::GetList($_REQUEST["ProjectID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->ProjectDocumentID])) 
	{
		if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorID==$_SESSION["PersonID"]))
			{
			manage_ProjectDocuments::Remove($res[$k]->ProjectDocumentID); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_ProjectDocuments::GetList($_REQUEST["ProjectID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_ProjectID" name="Item_ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	مستندات
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td width=5%>نوع سند</td>
	<td>شرح</td>
	<td width=1%>فایل</td>
	<td width=10%>ایجاد کننده</td>
	<td width=10%>تاریخ ایجاد</td>
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
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectDocumentID."\">";
	else
		echo " ";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageProjectDocuments.php?UpdateID=".$res[$k]->ProjectDocumentID."&ProjectID=".$_REQUEST["ProjectID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
		echo "	<td>".$res[$k]->ProjectDocumentTypeID_Desc."</td>";
	echo "	<td>".htmlentities(str_replace('\n', '<br>', $res[$k]->description), ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>
	           <a target='_blank' href=\"ReciptFile.php?PrDID=".$res[$k]->ProjectDocumentID."&FName_PrDID=".$res[$k]->FileName."\">
                   <img border=0 src='images/Download.gif' id='fileimg' title='دریافت فایل'></a></td>";
		
                echo "<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
	echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
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
<form target="_blank" method="post" action="NewProjectDocuments.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectID" name="ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
