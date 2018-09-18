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
$mysql = dbclass::getInstance();
$FileTypeName = "";
$SelectedUnit = 0;
$FileTypeID = -1;

$TemporarySendPermission = "NO";
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
		if($AccessList[$k]->TemporarySendPermission=="YES")
			$TemporarySendPermission = "YES";
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
				if($AccessList[$k]->TemporarySendPermission=="YES")
					$TemporarySendPermission = "YES";
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
				if($AccessList[$k]->TemporarySendPermission=="YES")
					$TemporarySendPermission = "YES";
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
				if($AccessList[$k]->TemporarySendPermission=="YES")
					$TemporarySendPermission = "YES";
				if($AccessList[$k]->ViewPermission=="YES")
					$ViewPermission = "YES";
				
			}
		}
	}
}	

?>
<script>
<? echo PersiateKeyboard() ?>
</script>
<form method=post id=f1 name=f1>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
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
			<td width=11% align=center bgcolor=#efefef>
				<a href='ManageFileTempUsers.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img width=50  border=0 title='امانتها' src='images/file_share.gif'><br>امانتها</a>			
			</td>
			<td width=11% align=center >
				<a href='ShowFileHistory.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='تاریخچه' src='images/file_history.gif'><br>تاریخچه</a>			
			</td>
			
		</tr>
	</table>
	</td>
</tr>
<tr><td>
<table width=100% border=1 cellspacing=0>
<tr class=HeaderOfTable>
	<td width=1%>&nbsp;</td>
	<td width=1%>&nbsp;</td>
	<td>
	امانت گیرنده
	</td>
	<td>
	مجاز به تغییر محتوا
	</td>
	<td>
	تاریخ امانت
	</td>
	<td>
	امانت دهنده
	</td>
	<!--  
	<td width=1% nowrap>
	دسترسی فرمها
	</td>
	-->
</tr>
<?php 
	$query = "select *, concat(p1.pfname, ' ', p1.plname) as ReceiverName, concat(p2.pfname, ' ', p2.plname) as SenderName, g2j(SendDate) as gSendDate from FilesTemporarayAccessList 
							JOIN hrms_total.persons as p1 on (ReceiverID=p1.PersonID)
							JOIN hrms_total.persons as p2 on (SenderID=p2.PersonID)
							where FileID='".$_REQUEST["UpdateID"]."'";
	$res = $mysql->Execute($query);
	$i = 0;
	while($rec = $res->FetchRow())
	{
		if(isset($_REQUEST["ch_".$rec["FilesTemporarayAccessListID"]]))
		{
			$mysql->Execute("delete from FilesTemporarayAccessList where FilesTemporarayAccessListID='".$rec["FilesTemporarayAccessListID"]."'");
		}
		else
		{
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			
			echo "<td><input type=checkbox name=ch_".$rec["FilesTemporarayAccessListID"]." id=ch_".$rec["FilesTemporarayAccessListID"]."></td>";
			echo "<td><a target=_blank href='NewFileTempUser.php?FileID=".$rec["FileID"]."&UpdateID=".$rec["FilesTemporarayAccessListID"]."'>".$rec["FilesTemporarayAccessListID"]."</a></td>";
			echo "<td>".$rec["ReceiverName"]."</td>";
			echo "<td>".$rec["ContentUpdatePermission"]."</td>";
			echo "<td>".$rec["gSendDate"]."</td>";
			echo "<td>".$rec["SenderName"]."</td>";
		}
		//echo "<td><a target=_blank href='ManageFileTempUserForms.php?id=".$rec["FilesTemporarayAccessListID"]."'><img src='images/FieldsAccess.jpg' border=0 title='تعریف دسترسی به فرمها'></a></td>";
		echo "</tr>";
	}
?>
<?php if($TemporarySendPermission=="YES") { ?>
<tr class=FooterOfTable><td align=center colspan=10>
<input type=submit value='حذف'>&nbsp;
<input type=button onclick='javascript: window.open("NewFileTempUser.php?FileID=<?php echo $CurFile->FileID; ?>");' value='ایجاد'>
</td>
</tr>
<?php } ?>
</table>
<input type=hidden name=Save id=Save value=0>
</form>
