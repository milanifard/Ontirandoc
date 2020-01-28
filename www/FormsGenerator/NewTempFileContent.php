<?php
include("header.inc.php");
include_once("classes/FileContents.class.php");
include_once("classes/files.class.php");
include_once("classes/FileTypeUserPermissions.class.php");
include_once("classes/FileTypeUserPermittedEduGroups.class.php");
include_once("classes/FileTypeUserPermittedUnits.class.php");
include_once("classes/FileTypeUserPermittedSubUnits.class.php");
include_once("classes/SecurityManager.class.php");

HTMLBegin();
// دسترسی بر اساس دسترسی موقت تعریف شود
$CurFile = new be_files();
$CurFile->LoadDataFromDatabase($_REQUEST["FileID"]);
$FileTypeID = $CurFile->FileTypeID;
$FileID = $_REQUEST["FileID"];
$ContentUpdatePermission = "NO";
$mysql = dbclass::getInstance();
$query = "select ContentUpdatePermission, f.*, ft.*, p.plname as pplname, p.pfname as ppfname, s.pfname as spfname, s.plname as splname,
								u.ptitle as UnitName, su.ptitle as SubUnitName, eg.PEduName   
			 from FilesTemporarayAccessList 
			JOIN files as f using (FileID) 
			LEFT JOIN FileTypes as ft using (FileTypeID) 
			LEFT JOIN hrms_total.persons as p using (PersonID)
			LEFT JOIN StudentSpecs as s using (StNo)
			LEFT JOIN hrms_total.org_units as u using (ouid)
			LEFT JOIN hrms_total.org_sub_units su using (sub_ouid)
			LEFT JOIN EducationalGroups as eg on (f.EduGrpCode=eg.EduGrpCode) where FileID='".$FileID."'";
$ContentUpdatePermission = "NO";
$res = $mysql->Execute($query);
if($rec = $res->FetchRow())
{
	$ContentUpdatePermission = $rec["ContentUpdatePermission"];
	$FileTypeName = $rec["FileTypeName"];
	$FileTypeID = $rec["FileTypeID"];
}
else
{
	die();
}

if(isset($_REQUEST["Save"]))
{
	$FileContent = "";
	$ActualFileName = "";

	if($_REQUEST["ContentType"]=="FILE" || $_REQUEST["ContentType"]=="PHOTO")
	{		
		if (trim($_FILES["Item_FileContent"]['name']) != '' )
		{
			 if ($_FILES["Item_FileContent"]['error'] == 0 )
			 {
				$_size = $_FILES["Item_FileContent"]['size'];
				$_name = $_FILES["Item_FileContent"]['tmp_name'];
				$ActualFileName = $_FILES["Item_FileContent"]['name'];
				$FileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
				
			 }
		}
	}
	
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		
		manage_FileContents::Add($_REQUEST["FileID"]
				, $_REQUEST["ContentType"]
				, $ActualFileName
				, $_REQUEST["Item_description"]
				, $FileContent
				, $_REQUEST["Item_LetterType"]
				, $_REQUEST["Item_ContentNumber"]
				, $_REQUEST["Item_ContentDate"]
				, 0
				, 0
				);
	}	
	else 
	{	
		manage_FileContents::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_description"]
				, $_REQUEST["Item_LetterType"]
				, $_REQUEST["Item_ContentNumber"]
				, $_REQUEST["Item_ContentDate"]
				);
		if($FileContent!="")
			manage_FileContents::UpdateAttachFile($_REQUEST["UpdateID"]
												, $ActualFileName
												, $FileContent
												 );
	}	
	echo "<script>window.opener.document.location='ManageTempFileContent.php?ContentType=".$_REQUEST["ContentType"]."&UpdateID=".$_REQUEST["FileID"]."'; window.close(); </script>";
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
$Item_description = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FileContents();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$NewValue = shdate($obj->ContentDate);
	$NewValue = substr($NewValue,6,2)."/".substr($NewValue,3,2)."/".substr($NewValue,0,2);
	
	$Item_description = $obj->description; 
	$LoadDataJavascriptCode .= "document.f1.Item_LetterType.value='".$obj->LetterType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ContentNumber.value='".$obj->ContentNumber."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ContentDate.value='".$NewValue."'; \r\n "; 
}	
?>
<form method=post id=f1 name=f1 action='NewTempFileContent.php?' ENCTYPE='multipart/form-data'>
<input type=hidden name=FileID id=FileID value='<?php echo $_REQUEST["FileID"] ?>'>
<input type=hidden name=ContentType id=ContentType value='<?php echo $_REQUEST["ContentType"] ?>'>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش محتویات پرونده</td></tr>
<tr><td>
<table width=100% border=0>
<tr id=tr_description name=tr_description style='display: <?php if($_REQUEST["ContentType"]=="FORM") echo "none"; ?>'>
<td width=1% nowrap>
	<?php 
		if($_REQUEST["ContentType"]=="TEXT" || $_REQUEST["ContentType"]=="PHOTO" || $_REQUEST["ContentType"]=="FILE") 
			echo "شرح"; 
		if($_REQUEST["ContentType"]=="LETTER") 
			echo "خلاصه نامه"; 
		if($_REQUEST["ContentType"]=="SESSION") 
			echo "خلاصه جلسه"; 
	?>
</td>
<td nowrap>
	<textarea name=Item_description id=Item_description rows=5 cols=80><?php echo $Item_description; ?></textarea>
</td>
</tr>
<tr id=tr_FileContent name=tr_FileContent style='display: <?php if($_REQUEST["ContentType"]=="FORM" || $_REQUEST["ContentType"]=="TEXT") echo "none"; ?>'>
<td width=1% nowrap>
	فایل
</td>
<td nowrap>
	<input type=file name=Item_FileContent id=Item_FileContent>
</td>
</tr>
<tr id=tr_LetterType name=tr_LetterType style='display: <?php if($_REQUEST["ContentType"]!="LETTER") echo "none"; ?>'>
<td width=1% nowrap>
	نوع نامه
</td>
<td nowrap>
	<select name=Item_LetterType id=Item_LetterType>
		<option value='SENT'>ارسالی
		<option value='RECEIVED'>دریافتی
	</select>
</td>
</tr>
<tr id=tr_ContentNumber name=tr_ContentNumber style='display: <?php if($_REQUEST["ContentType"]!="LETTER" && $_REQUEST["ContentType"]!="SESSION") echo "none"; ?>'>
<td width=1% nowrap>
	شماره 
</td>
<td nowrap>
	<input type=text name=Item_ContentNumber id=Item_ContentNumber>
</td>
</tr>
<tr id=tr_ContentDate name=tr_ContentDate style='display: <?php if($_REQUEST["ContentType"]!="LETTER" && $_REQUEST["ContentType"]!="SESSION") echo "none"; ?>'>
<td width=1% nowrap>
	تاریخ 
</td>
<td nowrap>
	<input type=text name=Item_ContentDate id=Item_ContentDate size=7 maxlength=8> &nbsp; (روز/ماه/سال دو رقم - مثال: 87/03/12)
</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'></td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
