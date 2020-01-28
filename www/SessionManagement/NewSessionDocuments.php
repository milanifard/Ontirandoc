<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : مستندات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-3
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionDocuments.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_SessionDocuments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $obj->UniversitySessionID);
}
else
	$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_SessionDocuments")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_SessionDocuments")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_SessionDocuments")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorPersonID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_SessionDocuments")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_SessionDocuments")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorPersonID)
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
	if(isset($_REQUEST["UniversitySessionID"]))
		$Item_UniversitySessionID=$_REQUEST["UniversitySessionID"];
	if(isset($_REQUEST["Item_CreatorPersonID"]))
		$Item_CreatorPersonID=$_REQUEST["Item_CreatorPersonID"];
	if(isset($_REQUEST["Item_CreateTime"]))
		$Item_CreateTime=$_REQUEST["Item_CreateTime"];
	$Item_DocumentFile = "";
	$Item_DocumentFileName = "";
	if (trim($_FILES['Item_DocumentFile']['name']) != '')
	{
		if ($_FILES['Item_DocumentFile']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_DocumentFile']['error'];
		}
		else
		{
			$_size = $_FILES['Item_DocumentFile']['size'];
			$_name = $_FILES['Item_DocumentFile']['tmp_name'];
			$Item_DocumentFile = addslashes((fread(fopen($_name, 'r' ),$_size)));
			$Item_DocumentFileName = trim($_FILES['Item_DocumentFile']['name']);
		}
	}
	if(isset($_REQUEST["Item_DocumentFileName"]))
		$Item_DocumentFileName=$_REQUEST["Item_DocumentFileName"];
	if(isset($_REQUEST["Item_DocumentDescription"]))
		$Item_DocumentDescription=$_REQUEST["Item_DocumentDescription"];
	if(isset($_REQUEST["Item_InputOrOutput"]))
		$Item_InputOrOutput=$_REQUEST["Item_InputOrOutput"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		if($HasAddAccess)
		manage_SessionDocuments::Add($Item_UniversitySessionID
				, $Item_DocumentFile
				, $Item_DocumentFileName
				, $Item_DocumentDescription
				, $Item_InputOrOutput
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
	}	
	else 
	{	
		if($HasUpdateAccess)
		manage_SessionDocuments::Update($_REQUEST["UpdateID"] 
				, $Item_DocumentFile
				, $Item_DocumentFileName
				, $Item_DocumentDescription
				, $Item_InputOrOutput
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
		die();
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_SessionDocuments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_DocumentDescription.value='".htmlentities($obj->DocumentDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_DocumentDescription').innerHTML='".htmlentities($obj->DocumentDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_InputOrOutput.value='".htmlentities($obj->InputOrOutput, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_InputOrOutput').innerHTML='".htmlentities($obj->InputOrOutput_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
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
<input type="hidden" name="UniversitySessionID" id="UniversitySessionID" value='<? if(isset($_REQUEST["UniversitySessionID"])) echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 فایل
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="file" name="Item_DocumentFile" id="Item_DocumentFile">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->DocumentFileName!="") { ?>
	<a href='DownloadFile.php?TableName=SessionDocuments&FieldName=DocumentFile&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->DocumentFileName; ?>]</a>
	<? } ?>
	<? } else { ?>
	<? if(isset($_REQUEST["UpdateID"]) && $obj->DocumentFileName!="") { ?>
	<a href='DownloadFile.php?TableName=SessionDocuments&FieldName=DocumentFile&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->DocumentFileName; ?>]</a>
	<? } ?>
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_DocumentDescription" id="Item_DocumentDescription" maxlength="500" size="40">
	<? } else { ?>
	<span id="Item_DocumentDescription" name="Item_DocumentDescription"></span> 
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نوع
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_InputOrOutput" id="Item_InputOrOutput" >
		<option value='INPUT'>ورودی</option>
		<option value='OUTPUT'>خروجی</option>
	</select>
	<? } else { ?>
	<span id="Item_InputOrOutput" name="Item_InputOrOutput"></span> 	<? } ?>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<? 
if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess))
	{
?>
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
<? } ?>
 <input type="button" onclick="javascript: window.close();" value="بستن">
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
</html>
