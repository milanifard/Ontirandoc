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
$mysql = dbclass::getInstance();
$CurFile = new be_files();
$CurFile->LoadDataFromDatabase($_REQUEST["FileID"]);
$FileTypeID = $CurFile->FileTypeID;

$AccessList = manage_FileTypeUserPermissions::GetList(" FileTypeID='".$CurFile->FileTypeID."' and PersonID='".$_SESSION["PersonID"]."' ");
if(count($AccessList)==0)
{
	echo "Hey! You don't have any permission for this file :D";
	die(); 
}
$TemporarySendPermission = "NO";
$ParentContentUpdatePermission = "NO";
// تمام رکوردهای دسترسی را بررسی می کند در هر یک از آنها چنانچه این فایل انتخابی موجود بود بررسی می کند آیا دسترسیهای مختلف وجود دارد یا نه و اگر وجود داشت آن را تنظیم می کند
// در کنترل دسترسیها چنانچه به هر طریقی دسترسی برای کاربر در نظر گرفته شده باشد آن را مبنای عمل قرارمی دهد
for($k=0; $k<count($AccessList); $k++)
{
	if($AccessList[$k]->AccessRange=="ALL" || ($AccessList[$k]->AccessRange=="ONLY_USER" && $CurFile->CreatorID==$_SESSION["PersonID"]))
	{
		if($AccessList[$k]->TemporarySendPermission=="YES")
			$TemporarySendPermission = "YES";
		if($AccessList[$k]->ContentUpdatePermission=="YES")
			$ParentContentUpdatePermission = "YES";
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
				if($AccessList[$k]->ContentUpdatePermission=="YES")
					$ParentContentUpdatePermission = "YES";
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
				if($AccessList[$k]->ContentUpdatePermission=="YES")
					$ParentContentUpdatePermission = "YES";
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
				if($AccessList[$k]->ContentUpdatePermission=="YES")
					$ParentContentUpdatePermission = "YES";
				
			}
		}
	}
}	
if($TemporarySendPermission=="NO")
{
	echo "You don't have permission for adding temporary access :)";
	die();	
}


if(isset($_REQUEST["Save"]))
{
	if($ParentContentUpdatePermission=="NO")
		$ContentUpdatePermission = "NO";
	else
		$ContentUpdatePermission = $_REQUEST["ContentUpdatePermission"];
	
	if(!isset($_REQUEST["UpdateID"]))
	{
		$query = "insert into FilesTemporarayAccessList (SenderID, ReceiverID, SendDate, FileID, ContentUpdatePermission) values ";
		$query .= "('".$_SESSION["PersonID"]."', '".$_REQUEST["ReceiverID"]."', now(), '".$_REQUEST["FileID"]."', '".$ContentUpdatePermission."')";
		$mysql->Execute($query);
	
		$res = $mysql->Execute("select max(FilesTemporarayAccessListID) from FilesTemporarayAccessList");
		$rec = $res->FetchRow();
		$RecID = $rec[0];
		
		$res = $mysql->Execute("select * from FileTypeForms where FileTypeID='".$FileTypeID."' ");
		while($rec = $res->FetchRow())
		{
			$AddPermission = "NO";
			$RemovePermission = "NO";
			if(isset($_REQUEST["AddForm_".$rec["FormsStructID"]]))
			{
				$AddPermission = $_REQUEST["AddForm_".$rec["FormsStructID"]];
				$RemovePermission = $_REQUEST["RemoveForm_".$rec["FormsStructID"]];
			}
			$query = "insert into FileFormsTemporaryAccessForAddRemove (FilesTemporarayAccessListID, FormsStructID, AddPermission, RemovePermission) ";
			$query .= " values ('".$RecID."', '".$rec["FormsStructID"]."', '".$AddPermission."', '".$RemovePermission."')"; 
			$mysql->Execute($query);
		}
		
		$res = $mysql->Execute("select * from FileTypeForms JOIN FormFields using (FormsStructID) where FileTypeForms.FileTypeID='".$FileTypeID."' ");
		while($rec = $res->FetchRow())
		{
			if(isset($_REQUEST["f_".$rec["FormFieldID"]]))
			{
				$NewValue = $_REQUEST["f_".$rec["FormFieldID"]];
				$query = "insert into FileFormsTemporarayAccessList (FilesTemporarayAccessListID, FormFieldID, AccessType) values ";
				$query .= "('".$RecID."', '".$rec["FormFieldID"]."', '".$NewValue."')";
				$mysql->Execute($query);
			}
		}
		
		$query = "insert into FilesTemporaryAccessListHistory (SenderID, ReceiverID, ActionTime, FileID, ActionType) values ";
		$query .= "('".$_SESSION["PersonID"]."', '".$_REQUEST["ReceiverID"]."', now(), '".$_REQUEST["FileID"]."', 'SEND')";
		$mysql->Execute($query);
		
	}
	else
	{
		$query = "update FilesTemporarayAccessList set ";
		$query .= " ReceiverID='".$_REQUEST["ReceiverID"]."', ContentUpdatePermission='".$ContentUpdatePermission."' where FilesTemporarayAccessListID='".$_REQUEST["UpdateID"]."'";
		$mysql->Execute($query);

		
		$res = $mysql->Execute("select * from FileTypeForms where FileTypeID='".$FileTypeID."' ");
		while($rec = $res->FetchRow())
		{
			$AddPermission = "NO";
			$RemovePermission = "NO";
			if(isset($_REQUEST["AddForm_".$rec["FormsStructID"]]))
			{
				$AddPermission = $_REQUEST["AddForm_".$rec["FormsStructID"]];
				$RemovePermission = $_REQUEST["RemoveForm_".$rec["FormsStructID"]];
				$mysql->Execute("delete from FileFormsTemporaryAccessForAddRemove where FilesTemporarayAccessListID='".$_REQUEST["UpdateID"]."' and FormsStructID='".$rec["FormsStructID"]."'");
				$query = "insert into FileFormsTemporaryAccessForAddRemove (FilesTemporarayAccessListID, FormsStructID, AddPermission, RemovePermission) ";
				$query .= " values ('".$_REQUEST["UpdateID"]."', '".$rec["FormsStructID"]."', '".$AddPermission."', '".$RemovePermission."')"; 
				$mysql->Execute($query);
			}
		}
		
		
		$mysql->Execute("delete from FileFormsTemporarayAccessList where FilesTemporarayAccessListID='".$_REQUEST["UpdateID"]."'");

		$res = $mysql->Execute("select * from FileTypeForms JOIN FormFields using (FormsStructID) where FileTypeForms.FileTypeID='".$FileTypeID."' ");
		
		while($rec = $res->FetchRow())
		{
			if(isset($_REQUEST["f_".$rec["FormFieldID"]]))
			{
				$NewValue = $_REQUEST["f_".$rec["FormFieldID"]];
				$query = "insert into FileFormsTemporarayAccessList (FilesTemporarayAccessListID, FormFieldID, AccessType) values ";
				$query .= "('".$_REQUEST["UpdateID"]."', '".$rec["FormFieldID"]."', '".$NewValue."')";
				$mysql->Execute($query);
			}
		}
				
		$query = "insert into FilesTemporaryAccessListHistory (SenderID, ReceiverID, ActionTime, FileID, ActionType) values ";
		$query .= "('".$_SESSION["PersonID"]."', '".$_REQUEST["ReceiverID"]."', now(), '".$_REQUEST["FileID"]."', 'UPDATE')";
		$mysql->Execute($query);
	}
	if(!isset($_REQUEST["UpdateID"]))
		echo "<script>window.opener.document.location='ManageFileTempUsers.php?UpdateID=".$_REQUEST["FileID"]."'; window.close(); </script>";
	//echo "<script>window.opener.document.location='ManageFileTempUsers.php?UpdateID=".$_REQUEST["FileID"]."'; </script>";
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$PersonName = "";
$ReceiverID = 0;
$ContentUpdatePermission = "NO";
$JavascriptCode = "";
if(isset($_REQUEST["UpdateID"]))
{
	$query = "select * from FilesTemporarayAccessList JOIN hrms_total.persons on (ReceiverID=PersonID) where FilesTemporarayAccessListID='".$_REQUEST["UpdateID"]."'";
	$res = $mysql->Execute($query);
	if($rec = $res->FetchRow())
	{
		$PersonName = $rec["pfname"]." ".$rec["plname"];
		$ContentUpdatePermission = $rec["ContentUpdatePermission"];
		$ReceiverID = $rec["ReceiverID"];
		$query = "select * from FileFormsTemporarayAccessList where FilesTemporarayAccessListID='".$_REQUEST["UpdateID"]."'";
		$res = $mysql->Execute($query);
		while($rec = $res->FetchRow())
		{
			$JavascriptCode .= "document.f1.f_".$rec["FormFieldID"].".value='".$rec["AccessType"]."';\n";
		} 
	}
}
?>
<script>
<? echo PersiateKeyboard() ?>
</script>
<form method=post id=f1 name=f1 action='NewFileTempUser.php?' ENCTYPE='multipart/form-data'>
<input type=hidden name=FileID id=FileID value='<?php echo $_REQUEST["FileID"] ?>'>
<input type=hidden name=ContentType id=ContentType value='<?php echo $_REQUEST["ContentType"] ?>'>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد دسترسی موقت</td></tr>
<tr><td>
<table width=100% border=0>
<tr id=tr_ContentNumber name=tr_ContentNumber style='display:'>
<td width=1% nowrap>
	امانت گیرنده 
</td>
<td nowrap>
	<input type=hidden name=ReceiverID id=ReceiverID value='<?php echo $ReceiverID; ?>'>
	<span id=MySpan name=MySpan><?php echo $PersonName; ?></span>
	<a target=_blank href='SelectStaff.php?InputName=ReceiverID&SpanName=MySpan'>[انتخاب]</a>
	<select name=test onchange='javascript: document.f1.ReceiverID.value=this.value;'>
		<option value=0>-
		<?php 
			$res = $mysql->Execute("select distinct persons.PersonID, concat(persons.plname, ' ', persons.pfname) as FullName from formsgenerator.FilesTemporarayAccessList
											JOIN formsgenerator.files using (FileID)
											JOIN hrms_total.persons on (persons.PersonID=FilesTemporarayAccessList.ReceiverID) 
											where FileTypeID=".$FileTypeID." order by persons.plname, persons.pfname");
			while($rec = $res->FetchRow())
			{
				echo "<option value='".$rec["PersonID"]."'>".$rec["FullName"];
			}
		?>
	</select>
</td>
</tr>
<?php if($ParentContentUpdatePermission=="YES") { // در صورتیکه امانت دهنده دسترسی برای ویرایش محتوا داشته باشد می تواند دسترسی برای ویرایش محتوا هم بدهد?>
<tr id=tr_ContentNumber name=tr_ContentNumber style='display:'>
<td width=1% nowrap>
	مجوز بروزرسانی محتویات
</td>
<td nowrap>
	<select name=ContentUpdatePermission id=ContentUpdatePermission>
		<option value='NO'>ندارد
		<option value='YES' <?php if($ContentUpdatePermission=="YES") echo "selected"; ?> >دارد		
	</select>
</td>
</tr>
<?php } ?>
<?php 

$res = $mysql->Execute("select * from FileTypeForms JOIN FormsStruct using (FormsStructID) where FileTypeForms.FileTypeID='".$FileTypeID."' ");
while($rec = $res->FetchRow())
{
	$AddPermission = "NO";
	$RemovePermission = "NO";
	$ParentAddPermission = "NO";
	$ParentRemovePermission = "NO";
	
	if(isset($_REQUEST["UpdateID"]))
	{
		// بررسی می کند آیا قبلا دسترسی حذف و اضافه تعریف شده است و اگر تعریف شده مقادیر آنها را استخراج می کند
		$res2 = $mysql->Execute("select * from FileFormsTemporaryAccessForAddRemove where FilesTemporarayAccessListID='".$_REQUEST["UpdateID"]."' and FormsStructID='".$rec["FormsStructID"]."'");
		if($rec2=$res2->FetchRow())
		{
			$AddPermission = $rec2["AddPermission"];
			$RemovePermission = $rec2["RemovePermission"];
		}
	}
	// دسترسی شخصی که دارد امانت تعریف می کند را برای حذف و اضافه آن نوع فرم استخراج می کند
	// اگر شخص امانت دهنده دسترسی نداشته باشد نباید بتواند دسترسی هم بدهد
	$res2 = $mysql->Execute("select AddFormPermission, RemoveFormPermission from FileTypeUserPermissions
								JOIN FileTypeUserPermittedForms using (FileTypeUserPermissionID)
								 where FileTypeID='".$CurFile->FileTypeID."' and PersonID='".$_SESSION["PersonID"]."' and FormsStructID='".$rec["FormsStructID"]."'");
	if($rec2=$res2->FetchRow())
	{
		$ParentAddPermission = $rec2["AddFormPermission"];
		$ParentRemovePermission = $rec2["RemoveFormPermission"];
		
	}
	echo "<tr>";
	echo "<td colspan=2>";
	echo "<table width=90% align=center border=1 cellspacing=0>";
	echo "<tr class=HeaderOfTable><td align=center colspan=2>".$rec["FormTitle"]."</td></tr>";
	echo "<tr><td colspan=2>دسترسی ایجاد: ";
	echo "<select name=AddForm_".$rec["FormsStructID"]." id=form_".$rec["FormsStructID"].">";
	echo "<option value='NO'>ندارد";
	if($ParentAddPermission=="YES")
	{
		echo "<option value='YES' ";
		if($AddPermission=="YES")
			echo " selected ";
		echo ">دارد";
	}
	echo "</select>";	
	echo "&nbsp;&nbsp;";
	echo "دسترسی حذف: ";
	echo "<select name=RemoveForm_".$rec["FormsStructID"]." id=form_".$rec["FormsStructID"].">";
	echo "<option value='NO'>ندارد";
	if($ParentRemovePermission=="YES")
	{
		echo "<option value='YES' ";
		if($RemovePermission=="YES")
			echo " selected ";
		echo ">دارد";
	}
	echo "</select>";	
	echo "</td></tr>";
	$fres =  $mysql->Execute("select * from FormFields where FormsStructID='".$rec["FormsStructID"]."' order by OrderInInputForm ");
	while($frec = $fres->FetchRow())
	{
		$query = "select AccessType from FileTypeUserPermissions
						JOIN FileTypeUserPermittedForms using (FileTypeUserPermissionID) 
						JOIN FileTypeUserPermittedFormDetails using (FileTypeUserPermittedFormID)
						where PersonID='".$_SESSION["PersonID"]."' 
							and FileTypeID='".$FileTypeID."' 
							and FormsStructID='".$rec["FormsStructID"]."' 
							and FormFieldID='".$frec["FormFieldID"]."' ";
		$res2 = $mysql->Execute($query);
		if($rec2 = $res2->FetchRow())
		{
			echo "<tr>";
			echo "<td>".$frec["FieldTitle"]."</td>";
			echo "<td width=50%>";
			echo "<select name=f_".$frec["FormFieldID"]." id=f_".$frec["FormFieldID"].">";
			echo "<option value='HIDE'>غیر قابل مشاهده";
			if($rec2[0]!="HIDE")
				echo "<option value='READ_ONLY'>فقط خواندنی";
			if($rec2[0]!="HIDE" && $rec2[0]!="READ_ONLY")
				echo "<option value='EDITABLE'>قابل ویرایش";
			echo "</select>";
			echo "</td>";
			echo "</tr>";
		}
	}	
	
	echo "</table>";
	echo "</td>";
	echo "<tr>";
}
?>

</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'></td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	<?php echo $JavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
