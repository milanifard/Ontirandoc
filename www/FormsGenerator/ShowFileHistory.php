<?php
include("header.inc.php");
include("classes/files.class.php");
include("classes/FileTypeUserPermissions.class.php");
include("classes/FileTypeUserPermittedEduGroups.class.php");
include("classes/FileTypeUserPermittedUnits.class.php");
include("classes/FileTypeUserPermittedSubUnits.class.php");
include("classes/SecurityManager.class.php");
include("classes/FileTypes.class.php");
include("classes/FormUtils.class.php");
include("classes/FileContents.class.php");

HTMLBegin();
$FileTypeName = "";
$SelectedUnit = 0;
$FileTypeID = -1;

if(!isset($_REQUEST["UpdateID"]))
	die();
$UpdateID = $_REQUEST["UpdateID"];
$list = "";
$CurFile = new be_files();
$CurFile->LoadDataFromDatabase($UpdateID);
$mysql = dbclass::getInstance();
$res = $mysql->Execute("select *, g2j(ActionTime) as gActionTime from FileHistory JOIN hrms_total.persons using (PersonID) where FileID='".$UpdateID."'");
$i = 0;
while($rec = $res->FetchRow())
{
	if($i%2==0)
		$list .= "<tr class=OddRow>";
	else
		$list .= "<tr class=EvenRow>";
	$list .= "<td>".$rec["description"]."</td>";
	$list .= "<td>".$rec["pfname"]." ".$rec["plname"]."</td>";
	$list .= "<td>".$rec["gActionTime"]." ".substr($rec["ActionTime"], 11, 8)."</td>";
	$list .= "</tr>";
}

$res = $mysql->Execute("select *, g2j(ActionTime) as gActionTime, concat(p1.pfname, ' ', p1.plname) as ReceiverName, concat(p2.pfname, ' ', p2.plname) as SenderName from 
							FilesTemporaryAccessListHistory 
							JOIN hrms_total.persons as p1 on (ReceiverID=p1.PersonID)
							JOIN hrms_total.persons as p2 on (SenderID=p2.PersonID) 
							where FileID='".$UpdateID."'");
$i = 0;
while($rec = $res->FetchRow())
{
	if($i%2==0)
		$list .= "<tr class=OddRow>";
	else
		$list .= "<tr class=EvenRow>";
	$list .= "<td>";
	if($rec["ActionType"]=="SEND")
	{
		$list .= "ایجاد مجوز دسترسی موقت ";
	}
	else if($rec["ActionType"]=="UPDATE")
	{
		$list .= "بروزرسانی مجوز دسترسی موقت ";
	}
	else if($rec["ActionType"]=="REMOVE")
	{
		$list .= "حذف مجوز دسترسی موقت ";
	}
	$list .= " ".$rec["ReceiverName"];	
	$list .= "<td>".$rec["SenderName"]."</td>";
	$list .= "<td>".$rec["gActionTime"]." ".substr($rec["ActionTime"], 11, 8)."</td>";
	$list .= "</tr>";
}

$res = $mysql->Execute("select *, g2j(ActionTime) as gActionTime  from FileContentHistory 
										JOIN hrms_total.persons using (PersonID) 
										JOIN FileContents using (FileContentID)
										where FileContentID in (select FileContentID from FileContents where FileID='".$UpdateID."')");
$i = 0;
while($rec = $res->FetchRow())
{
	if($i%2==0)
		$list .= "<tr class=OddRow>";
	else
		$list .= "<tr class=EvenRow>";
	$list .= "<td>";
	if($rec["ActionType"]=="ADD")
	{
		$list .= "ایجاد محتوای ";
	}
	else if($rec["ActionType"]=="UPDATE")
	{
		$list .= "بروزرسانی محتوای ";
	}
	else if($rec["ActionType"]=="REMOVE")
	{
		$list .= "حذف محتوای ";
	}
	if($rec["ContentType"]=="LETTER")
		$list .= "نامه";
	else if($rec["ContentType"]=="SESSION")
		$list .= "جلسه";
	else if($rec["ContentType"]=="TEXT")
		$list .= "متنی";
	else if($rec["ContentType"]=="PHOTO")
		$list .= "تصویری";
	else if($rec["ContentType"]=="FILE")
		$list .= "فایلی";
	else if($rec["ContentType"]=="FORM")
		$list .= "فرمی";
	$list .= " با کد ".$rec["FileContentID"];
	$list .= "</td>";
	$list .= "<td>".$rec["pfname"]." ".$rec["plname"]."</td>";
	$list .= "<td>".$rec["gActionTime"]." ".substr($rec["ActionTime"], 11, 8)."</td>";
	$list .= "</tr>";
}

?>
<script>
<? echo PersiateKeyboard() ?>
</script>
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش پرونده ها</td></tr>
<tr bgcolor=#cccccc>
	<td align=center>
	<table width=100% border=1 cellspacing=0>
		<tr>
						<td width=11% align=center>
				<a href='NewFile.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='مشخصات پرونده' src='images/file_Properties.gif'><br>مشخصات</a>			
			</td>
			<td width=11% align=center >
				<a href='ManageFileContent.php?ContentType=TEXT&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='متون' src='images/file_text.gif'><br>متون</a>			
			</td>
			<td width=11% align=center >
				<a href='ManageFileContent.php?ContentType=PHOTO&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='تصاویر' src='images/file_photo.gif'><br>تصاویر</a>			
			</td>
			<td width=11% align=center >
				<a href='ManageFileContent.php?ContentType=FILE&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img width=50  border=0 title='فایلها' src='images/file_file.gif'><br>فایلها</a>			
			</td>
			<td width=11% align=center >
				<a href='ManageFileContent.php?ContentType=FORM&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img width=50  border=0 title='فرمها' src='images/file_form.gif'><br>فرمها</a>			
			</td>
			<td width=11% align=center >
				<a href='ManageFileContent.php?ContentType=LETTER&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img width=50  border=0 title='نامه ها' src='images/file_letter.gif'><br>نامه ها</a>			
			</td>
			<td width=11% align=center >
				<a href='ManageFileContent.php?ContentType=SESSION&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='جلسات' src='images/file_session.gif'><br>جلسات</a>			
			</td>
			<td width=11% align=center >
				<a href='ManageFileTempUsers.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img width=50  border=0 title='امانتها' src='images/file_share.gif'><br>امانتها</a>			
			</td>
			<td width=11% align=center bgcolor=#efefef>
				<a href='ShowFileHistory.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='تاریخچه' src='images/file_history.gif'><br>تاریخچه</a>			
			</td>

			
		</tr>
	</table>
	</td>
</tr>
<tr><td>
<table width=100% border=1 cellspacing=0>
<tr class=HeaderOfTable>
	<td>
	عمل
	</td>
	<td>
	کاربر
	</td>
	<td>
	تاریخ
	</td>
</tr>
<?php echo $list; ?>
</table>
