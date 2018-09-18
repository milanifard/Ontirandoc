<?php
include("header.inc.php");
include("classes/files.class.php");
include("classes/FileContents.class.php");
include("classes/FileTypeForms.class.php");

HTMLBegin();
$FileTypeName = "";
$SelectedUnit = 0;
$FileTypeID = -1;

$FileID = $_REQUEST["UpdateID"];
$UpdateID = $_REQUEST["UpdateID"];
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

if(isset($_REQUEST["UpID"]))
	manage_FileContents::ChangeOrder($_REQUEST["UpID"], "UP");
if(isset($_REQUEST["DownID"]))
	manage_FileContents::ChangeOrder($_REQUEST["DownID"], "DOWN");
	
$res = manage_FileContents::GetList(" fc1.FileID='".$FileID."' and fc1.ContentType='".$_REQUEST["ContentType"]."' "); 

$mysql = dbclass::getInstance();
?>
<script>
<? echo PersiateKeyboard() ?>
</script>
<form method=post id=f1 name=f1>
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش پرونده ها</td></tr>
<tr bgcolor=#cccccc>
	<td align=center>
	<table width=100% border=1 cellspacing=0>
		<tr>
			<td width=14% align=center>
				<a href='NewTempFile.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='مشخصات پرونده' src='images/file_Properties.gif'><br>مشخصات</a>			
			</td>
			<td width=14% align=center <?php if($_REQUEST["ContentType"]=="TEXT") echo "bgcolor=#efefef"; ?>>
				<a href='ManageTempFileContent.php?ContentType=TEXT&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='متون' src='images/file_text.gif'><br>متون</a>			
			</td>
			<td width=14% align=center <?php if($_REQUEST["ContentType"]=="PHOTO") echo "bgcolor=#efefef"; ?>>
				<a href='ManageTempFileContent.php?ContentType=PHOTO&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='تصاویر' src='images/file_photo.gif'><br>تصاویر</a>			
			</td>
			<td width=14% align=center <?php if($_REQUEST["ContentType"]=="FILE") echo "bgcolor=#efefef"; ?>>
				<a href='ManageTempFileContent.php?ContentType=FILE&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='فایلها' src='images/file_file.gif'><br>فایلها</a>			
			</td>
			<td width=14% align=center <?php if($_REQUEST["ContentType"]=="FORM") echo "bgcolor=#efefef"; ?>>
				<a href='ManageTempFileContent.php?ContentType=FORM&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='فرمها' src='images/file_form.gif'><br>فرمها</a>			
			</td>
			<td width=14% align=center <?php if($_REQUEST["ContentType"]=="LETTER") echo "bgcolor=#efefef"; ?>>
				<a href='ManageTempFileContent.php?ContentType=LETTER&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='نامه ها' src='images/file_letter.gif'><br>نامه ها</a>			
			</td>
			<td width=14% align=center <?php if($_REQUEST["ContentType"]=="SESSION") echo "bgcolor=#efefef"; ?>>
				<a href='ManageTempFileContent.php?ContentType=SESSION&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='جلسات' src='images/file_session.gif'><br>جلسات</a>			
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr><td>
<table width=100% border=1 cellspacing=0>
<?php 
echo "<tr class=HeaderOfTable>";
if($ContentUpdatePermission=="YES")
	echo "<td width=1%> </td>";
echo "	<td width=2%>کد</td>";
if($_REQUEST["ContentType"]=="PHOTO" || $_REQUEST["ContentType"]=="FILE" || $_REQUEST["ContentType"]=="TEXT")
	echo "	<td>شرح</td>";
else if($_REQUEST["ContentType"]=="LETTER")
	echo "	<td>خلاصه نامه</td>";
else if($_REQUEST["ContentType"]=="SESSION")
	echo "	<td>خلاصه جلسه</td>";
if($_REQUEST["ContentType"]!="TEXT" && $_REQUEST["ContentType"]!="FORM")
	echo "	<td>فایل ضمیمه</td>";
if($_REQUEST["ContentType"]=="LETTER")
	echo "	<td>نوع نامه</td>";
if($_REQUEST["ContentType"]=="LETTER")
	echo "	<td>شماره نامه</td>";
else if($_REQUEST["ContentType"]=="SESSION")
	echo "	<td>شماره جلسه</td>";
if($_REQUEST["ContentType"]=="LETTER")
	echo "	<td>تاریخ نامه</td>";
else if($_REQUEST["ContentType"]=="SESSION")
	echo "	<td>تاریخ جلسه</td>";
	
if($_REQUEST["ContentType"]=="FORM")
{
	echo "	<td>فرم</td>";
	echo "	<td>کد فرم</td>";
}
if($ContentUpdatePermission=="YES")
	echo "	<td width=5% nowrap>تغییر ترتیب</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FileContentID]) && $ContentUpdatePermission=="YES") 
	{
		manage_FileContents::Remove($res[$k]->FileContentID); 
	}
	else
	{
		
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		if($_REQUEST["ContentType"]=="FORM")
		{
			if($ContentUpdatePermission=="YES")
			{
				// چک می کند آیا امکان حذف این فرم برای این کاربر در دسترسی موقت وجود دارد یا خیر
				$query = "select FormsStructID, FormTitle from FilesTemporarayAccessList 
								JOIN FileFormsTemporaryAccessForAddRemove using (FilesTemporarayAccessListID)
								JOIN FormsStruct using (FormsStructID) 
								where FileID='".$FileID."' and ReceiverID='".$_SESSION["PersonID"]."' and RemovePermission='YES' and FormsStructID='".$res[$k]->FormsStructID."'";
				$tres = $mysql->Execute($query);
				if($tres->FetchRow())
				{
					echo "<td><input type=checkbox name=ch_".$res[$k]->FileContentID."></td>";
				}
				else
				{
					echo "<td>&nbsp;</td>";
				}
			}
		}
		else
		{
			if($ContentUpdatePermission=="YES")
				echo "<td><input type=checkbox name=ch_".$res[$k]->FileContentID."></td>";
		}
		echo "	<td>";
		if($ContentUpdatePermission=="YES")
		{
			// داده به صورت لینک است نه داده اصلی
			if($res[$k]->RelatedContentID>0)
			{
				if($_REQUEST["ContentType"]=="FORM")
					echo "<a target=_blank href='ViewTempFileForm.php?FormStructID=".$res[$k]->FormsStructID."&FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&RelatedRecordID=".$res[$k]->FormRecordID."'>";
				else
					echo "<a target=_blank href='ViewTempFileContent.php?FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&UpdateID=".$res[$k]->RelatedContentID."'>";
			}
			else
			{
				if($_REQUEST["ContentType"]=="FORM")
					echo "<a target=_blank href='NewTempFileForm.php?FormStructID=".$res[$k]->FormsStructID."&FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&RelatedRecordID=".$res[$k]->FormRecordID."'>";
				else
					echo "<a target=_blank href='NewTempFileContent.php?FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&UpdateID=".$res[$k]->FileContentID."'>";
			}
		}
		else if($_REQUEST["ContentType"]=="FORM")
		{
			echo "<a target=_blank href='NewTempFileForm.php?FormStructID=".$res[$k]->FormsStructID."&FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&RelatedRecordID=".$res[$k]->FormRecordID."'>";
		}
		echo $res[$k]->FileContentID;
		echo "</a>";
		if($res[$k]->RelatedContentID>0)
		{
			echo "&nbsp;<img title='اتصال به محتوای دیگر' src='images/link.jpg' width=20 valign=center>";
		}
		
		echo "</td>";
		if($_REQUEST["ContentType"]!="FORM")
			echo "	<td>&nbsp;".$res[$k]->description."</td>";
		if($_REQUEST["ContentType"]=="PHOTO")
		{
			// یعنی لینک است نه محتوای اصلی
			if($res[$k]->RelatedContentID>0)
				echo "	<td><img width=200 src='ShowPhotoFileContent.php?FileContentID=".$res[$k]->RelatedContentID."'></td>";
			else
				echo "	<td><img width=200 src='ShowPhotoFileContent.php?FileContentID=".$res[$k]->FileContentID."'></td>";
		}
		else if($_REQUEST["ContentType"]!="TEXT" && $_REQUEST["ContentType"]!="FORM")
		{
			// یعنی لینک است نه محتوای اصلی
			if($res[$k]->RelatedContentID>0)
				echo "	<td><a href='DownloadFileContent.php?FileContentID=".$res[$k]->RelatedContentID."'>دریافت فایل</a></td>";
			else
				echo "	<td><a href='DownloadFileContent.php?FileContentID=".$res[$k]->FileContentID."'>دریافت فایل</a></td>";
		}
		if($_REQUEST["ContentType"]=="LETTER")
		{
			if($res[$k]->LetterType=="RECEIVED")
				echo "	<td>دریافتی</td>";
			else
				echo "	<td>ارسالی</td>";
		}
		if($_REQUEST["ContentType"]=="LETTER" || $_REQUEST["ContentType"]=="SESSION")
		{
			echo "	<td>".$res[$k]->ContentNumber."</td>";
			echo "	<td>".shdate($res[$k]->ContentDate)."</td>";
		}
		if($_REQUEST["ContentType"]=="FORM")
		{
			echo "	<td>".$res[$k]->FormTitle."</td>";
			echo "	<td>".$res[$k]->FormRecordID."</td>";
		}
		if($ContentUpdatePermission=="YES")
		{
			echo "	<td nowrap>";
			echo "	<a href='ManageFileContent.php?ContentType=".$_REQUEST["ContentType"]."&UpdateID=".$_REQUEST["UpdateID"]."&UpID=".$res[$k]->FileContentID."'><img width=20 src='images/Down.gif' border=0></a>";
			echo "	&nbsp;<a href='ManageFileContent.php?ContentType=".$_REQUEST["ContentType"]."&UpdateID=".$_REQUEST["UpdateID"]."&DownID=".$res[$k]->FileContentID."'><img width=20 src='images/UP.gif' border=0></a></td>";
		}
		echo "</tr>";
	}
}
?>
<?php if($ContentUpdatePermission=="YES") { ?>
<tr class=FooterOfTable><td align=center colspan=10>
<input type=submit value='حذف'>&nbsp;
<?php 
if($_REQUEST["ContentType"]=="FORM") 
{ 
	$FormOptions = "";
	// فقط مواردی که مجاز به دسترسی برای اضافه کردن است در لیست نمایش می دهد
	$query = "select FormsStructID, FormTitle from FilesTemporarayAccessList 
								JOIN FileFormsTemporaryAccessForAddRemove using (FilesTemporarayAccessListID)
								JOIN FormsStruct using (FormsStructID) 
								where FileID='".$FileID."' and ReceiverID='".$_SESSION["PersonID"]."' and AddPermission='YES'";
	$res = $mysql->Execute($query);
	while($rec = $res->FetchRow())
	{
		$FormOptions .= "<option value='".$rec["FormsStructID"]."'>".$rec["FormTitle"];
	}
	if($FormOptions!="")
	{
?>
<input type=button onclick='javascript: window.open("NewTempFileForm.php?FileID=<?php echo $FileID; ?>&FormStructID="+document.f1.SelectedFormID.value);' value='ایجاد'>
<select name=SelectedFormID id=SelectedFormID>
<?php echo $FormOptions; ?>
</select>
<?php 
} 
?>
<?php } else { ?>
<input type=button onclick='javascript: window.open("NewTempFileContent.php?FileID=<?php echo $FileID; ?>&ContentType=<?php echo $_REQUEST["ContentType"] ?>");' value='ایجاد'>

<?php } ?>
</td>
</tr>
<?php } ?>
</table>
<input type=hidden name=Save id=Save value=0>
</form>
