<?php
include("header.inc.php");
include_once("classes/files.class.php");
include_once("classes/FileTypeUserPermissions.class.php");
include_once("classes/FileTypeUserPermittedEduGroups.class.php");
include_once("classes/FileTypeUserPermittedUnits.class.php");
include_once("classes/FileTypeUserPermittedSubUnits.class.php");
include_once("classes/SecurityManager.class.php");
include_once("classes/FileTypes.class.php");
include_once("classes/FileTypeForms.class.php");
include_once("classes/FormUtils.class.php");
include_once("classes/FileContents.class.php");


HTMLBegin();
$FileTypeName = "";
$SelectedUnit = 0;
$FileTypeID = -1;

$ContentUpdatePermission = "NO";
$ViewPermission = "NO";

if(!isset($_REQUEST["UpdateID"]))
	die();
$UpdateID = $_REQUEST["UpdateID"];

$CurFile = new be_files();
$CurFile->LoadDataFromDatabase($UpdateID);
$FileTypeID = $CurFile->FileTypeID;
$AccessList = manage_FileTypeUserPermissions::GetList(" FileTypeID='".$CurFile->FileTypeID."' and PersonID='".$_SESSION["PersonID"]."' ");
if(count($AccessList)==0)
{
	echo "Hey! You don't have any permission for this file :D";
	die(); 
}
// تمام رکوردهای دسترسی را بررسی می کند در هر یک از آنها چنانچه این فایل انتخابی موجود بود بررسی می کند آیا دسترسیهای مختلف وجود دارد یا نه و اگر وجود داشت آن را تنظیم می کند
// در کنترل دسترسیها چنانچه به هر طریقی دسترسی برای کاربر در نظر گرفته شده باشد آن را مبنای عمل قرارمی دهد
for($k=0; $k<count($AccessList); $k++)
{
	if($AccessList[$k]->AccessRange=="ALL" || ($AccessList[$k]->AccessRange=="ONLY_USER" && $CurFile->CreatorID==$_SESSION["PersonID"]))
	{
		if($AccessList[$k]->ContentUpdatePermission=="YES")
			$ContentUpdatePermission = "YES";
		if($AccessList[$k]->ViewPermission=="YES")
			$ViewPermission = "YES";
	}
	if($AccessList[$k]->AccessRange=="UNIT")
	{
		$UnitList = manage_FileTypeUserPermittedUnits::GetList(" FileTypeUserPermissionID='".$AccessList[$k]->FileTypeUserPermissionID."' ");
		for($j=0; $j<count($UnitList); $j++)
		{
			if($UnitList[$j]->ouid==$CurFile->ouid)
			{
				if($AccessList[$k]->ContentUpdatePermission=="YES")
					$ContentUpdatePermission = "YES";
				if($AccessList[$k]->ViewPermission=="YES")
					$ViewPermission = "YES";
			}
		}
	}
	if($AccessList[$k]->AccessRange=="SUB_UNIT")
	{
		$UnitList = manage_FileTypeUserPermittedSubUnits::GetList(" FileTypeUserPermissionID='".$AccessList[$k]->FileTypeUserPermissionID."' ");
		for($j=0; $j<count($UnitList); $j++)
		{
			if($UnitList[$j]->SubUnitID==$CurFile->sub_ouid)
			{
				if($AccessList[$k]->ContentUpdatePermission=="YES")
					$ContentUpdatePermission = "YES";
				if($AccessList[$k]->ViewPermission=="YES")
					$ViewPermission = "YES";
			}
		}
	}
	if($AccessList[$k]->AccessRange=="EDU_GROUP")
	{
		$UnitList = manage_FileTypeUserPermittedEduGroups::GetList(" FileTypeUserPermissionID='".$AccessList[$k]->FileTypeUserPermissionID."' ");
		for($j=0; $j<count($UnitList); $j++)
		{
			if($UnitList[$j]->EduGrpCode==$CurFile->EduGrpCode)
			{
				if($AccessList[$k]->ContentUpdatePermission=="YES")
					$ContentUpdatePermission = "YES";
				if($AccessList[$k]->ViewPermission=="YES")
					$ViewPermission = "YES";
				
			}
		}
	}
}	

if(isset($_REQUEST["UpID"]))
	manage_FileContents::ChangeOrder($_REQUEST["UpID"], "UP");
if(isset($_REQUEST["DownID"]))
	manage_FileContents::ChangeOrder($_REQUEST["DownID"], "DOWN");
	
$FileTypeObj = new be_FileTypes();
$FileTypeObj->LoadDataFromDatabase($FileTypeID);
$FileTypeName = $FileTypeObj->FileTypeName;

$res = manage_FileContents::GetList(" fc1.FileID='".$CurFile->FileID."' and fc1.ContentType='".$_REQUEST["ContentType"]."' "); 

$mysql = dbclass::getInstance();
if(isset($_REQUEST["Save"]) && $_REQUEST["Save"]=="1")
{
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
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
			<td width=10% align=center>
				<a href='NewFile.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='مشخصات پرونده' src='images/file_Properties.gif'><br>مشخصات</a>			
			</td>
			<td width=10% align=center <?php if($_REQUEST["ContentType"]=="TEXT") echo "bgcolor=#efefef"; ?>>
				<a href='ManageFileContent.php?ContentType=TEXT&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='متون' src='images/file_text.gif'><br>متون</a>			
			</td>
			<td width=10% align=center <?php if($_REQUEST["ContentType"]=="PHOTO") echo "bgcolor=#efefef"; ?>>
				<a href='ManageFileContent.php?ContentType=PHOTO&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='تصاویر' src='images/file_photo.gif'><br>تصاویر</a>			
			</td>
			<td width=10% align=center <?php if($_REQUEST["ContentType"]=="FILE") echo "bgcolor=#efefef"; ?>>
				<a href='ManageFileContent.php?ContentType=FILE&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='فایلها' src='images/file_file.gif'><br>فایلها</a>			
			</td>
			<td width=10% align=center <?php if($_REQUEST["ContentType"]=="FORM") echo "bgcolor=#efefef"; ?>>
				<a href='ManageFileContent.php?ContentType=FORM&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='فرمها' src='images/file_form.gif'><br>فرمها</a>			
			</td>
			<td width=10% align=center <?php if($_REQUEST["ContentType"]=="LETTER") echo "bgcolor=#efefef"; ?>>
				<a href='ManageFileContent.php?ContentType=LETTER&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='نامه ها' src='images/file_letter.gif'><br>نامه ها</a>			
			</td>
			<td width=10% align=center <?php if($_REQUEST["ContentType"]=="SESSION") echo "bgcolor=#efefef"; ?>>
				<a href='ManageFileContent.php?ContentType=SESSION&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='جلسات' src='images/file_session.gif'><br>جلسات</a>			
			</td>
			<td width=10% align=center >
				<a href='ManageFileTempUsers.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='امانتها' src='images/file_share.gif'><br>امانتها</a>			
			</td>
			<td width=10% align=center >
				<a href='ShowFileHistory.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='تاریخچه' src='images/file_history.gif'><br>تاریخچه</a>			
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
				if(SecurityManager::HasUserRemoveAccessThisFormFromThisFileType($_SESSION["PersonID"], $res[$k]->FormsStructID, $FileTypeID))
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
					echo "<a target=_blank href='ViewFileForm.php?FormStructID=".$res[$k]->FormsStructID."&FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&RelatedRecordID=".$res[$k]->FormRecordID."'>";
				else
					echo "<a target=_blank href='ViewFileContent.php?FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&UpdateID=".$res[$k]->RelatedContentID."'>";
			}
			else
			{
				if($_REQUEST["ContentType"]=="FORM")
					echo "<a target=_blank href='NewFileForm.php?FormStructID=".$res[$k]->FormsStructID."&FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&RelatedRecordID=".$res[$k]->FormRecordID."'>";
				else
					echo "<a target=_blank href='NewFileContent.php?FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&UpdateID=".$res[$k]->FileContentID."'>";
			}
		}
		else if($_REQUEST["ContentType"]=="FORM")
		{
			echo "<a target=_blank href='NewFileForm.php?FormStructID=".$res[$k]->FormsStructID."&FileID=".$res[$k]->FileID."&ContentType=".$res[$k]->ContentType."&RelatedRecordID=".$res[$k]->FormRecordID."'>";
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
	$list = manage_FileTypeForms::GetList($CurFile->FileTypeID);
	for($i=0; $i<count($list); $i++)
	{
		if(SecurityManager::HasUserAddAccessThisFormFromThisFileType($_SESSION["PersonID"], $list[$i]->FormsStructID, $FileTypeID))
		{
			$FormOptions .= "<option value='".$list[$i]->FormsStructID."'>".$list[$i]->FormTitle;
		} 
	}
	if($FormOptions!="")
	{
?>
<input type=button onclick='javascript: window.open("NewFileForm.php?FileID=<?php echo $CurFile->FileID; ?>&FormStructID="+document.f1.SelectedFormID.value);' value='ایجاد'>
<select name=SelectedFormID id=SelectedFormID>
<?php echo $FormOptions; ?>
</select>
<br><br>
<input type=button value='ایجاد اتصال به یک فرم در گردش' onclick='javascript: window.open("NewFormLink.php?FileID=<?php echo $CurFile->FileID; ?>&ContentType=<?php echo $_REQUEST["ContentType"] ?>");'>
<?php 
} 
?>
<?php } else { ?>
<input type=button onclick='javascript: window.open("NewFileContent.php?FileID=<?php echo $CurFile->FileID; ?>&ContentType=<?php echo $_REQUEST["ContentType"] ?>");' value='ایجاد'>
&nbsp;
<input type=button onclick='javascript: window.open("NewFileContentLink.php?FileID=<?php echo $CurFile->FileID; ?>&ContentType=<?php echo $_REQUEST["ContentType"] ?>");' value='ایجاد اتصال'>

<?php } ?>
</td>
</tr>
<?php } ?>
</table>
<input type=hidden name=Save id=Save value=0>
</form>
