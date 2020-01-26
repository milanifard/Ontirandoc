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

HTMLBegin();
$FileTypeName = "";
$SelectedUnit = 0;
$FileTypeID = -1;

$DefineAccessPermission = "NO";
$AddPermission = "NO";
$RemovePermission = "NO";
$UpdatePermission = "NO";
$ContentUpdatePermission = "NO";
$ViewPermission = "NO";
$TemporaraySendPermission = "NO";

if(isset($_REQUEST["Item_ouid"]))
	$SelectedUnit = $_REQUEST["Item_ouid"];

if(isset($_REQUEST["UpdateID"]))
{
	$CurFile = new be_files();
	$CurFile->LoadDataFromDatabase($_REQUEST["UpdateID"]);
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
			if($AccessList[$k]->DefineAccessPermission=="YES")
				$DefineAccessPermission = "YES";
			if($AccessList[$k]->AddPermission=="YES")
				$AddPermission = "YES";
			if($AccessList[$k]->RemovePermission=="YES")
				$RemovePermission = "YES";
			if($AccessList[$k]->UpdatePermission=="YES")
				$UpdatePermission = "YES";
			if($AccessList[$k]->ContentUpdatePermission=="YES")
				$ContentUpdatePermission = "YES";
			if($AccessList[$k]->ViewPermission=="YES")
				$ViewPermission = "YES";
			if($AccessList[$k]->TemporarySendPermission=="YES")
				$TemporarySendPermission = "YES";
		}
		if($AccessList[$k]->AccessRange=="UNIT")
		{
			$UnitList = manage_FileTypeUserPermittedUnits::GetList(" FileTypeUserPermissionID='".$AccessList[$k]->FileTypeUserPermissionID."' ");
			for($j=0; $j<count($UnitList); $j++)
			{
				if($UnitList[$j]->ouid==$CurFile->ouid)
				{
					if($AccessList[$k]->DefineAccessPermission=="YES")
						$DefineAccessPermission = "YES";
					if($AccessList[$k]->AddPermission=="YES")
						$AddPermission = "YES";
					if($AccessList[$k]->RemovePermission=="YES")
						$RemovePermission = "YES";
					if($AccessList[$k]->UpdatePermission=="YES")
						$UpdatePermission = "YES";
					if($AccessList[$k]->ContentUpdatePermission=="YES")
						$ContentUpdatePermission = "YES";
					if($AccessList[$k]->ViewPermission=="YES")
						$ViewPermission = "YES";
					if($AccessList[$k]->TemporarySendPermission=="YES")
						$TemporarySendPermission = "YES";
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
					if($AccessList[$k]->DefineAccessPermission=="YES")
						$DefineAccessPermission = "YES";
					if($AccessList[$k]->AddPermission=="YES")
						$AddPermission = "YES";
					if($AccessList[$k]->RemovePermission=="YES")
						$RemovePermission = "YES";
					if($AccessList[$k]->UpdatePermission=="YES")
						$UpdatePermission = "YES";
					if($AccessList[$k]->ContentUpdatePermission=="YES")
						$ContentUpdatePermission = "YES";
					if($AccessList[$k]->ViewPermission=="YES")
						$ViewPermission = "YES";
					if($AccessList[$k]->TemporarySendPermission=="YES")
						$TemporarySendPermission = "YES";
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
					if($AccessList[$k]->DefineAccessPermission=="YES")
						$DefineAccessPermission = "YES";
					if($AccessList[$k]->AddPermission=="YES")
						$AddPermission = "YES";
					if($AccessList[$k]->RemovePermission=="YES")
						$RemovePermission = "YES";
					if($AccessList[$k]->UpdatePermission=="YES")
						$UpdatePermission = "YES";
					if($AccessList[$k]->ContentUpdatePermission=="YES")
						$ContentUpdatePermission = "YES";
					if($AccessList[$k]->ViewPermission=="YES")
						$ViewPermission = "YES";
					if($AccessList[$k]->TemporarySendPermission=="YES")
						$TemporarySendPermission = "YES";
				}
			}
		}
		
	}
}
else
{
	// برای مود ایجاد کد پرونده پاس نمی شود و فقط دسترسی ایجاد چک می شود
	$FileTypeID = $_REQUEST["FileTypeID"];
	if(!SecurityManager::HasUserAddAccessToThisFileType($_SESSION["PersonID"], $FileTypeID))
	{
		echo "Hey! You don't have create permission for this type :D";
		die(); 
	}
}


$FileTypeObj = new be_FileTypes();
$FileTypeObj->LoadDataFromDatabase($FileTypeID);
$FileTypeName = $FileTypeObj->FileTypeName;

$mysql = dbclass::getInstance();
if(isset($_REQUEST["Save"]) && $_REQUEST["Save"]=="1")
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		$LastID = manage_files::GetLastID();
		manage_files::Add($FileTypeID
				, $_REQUEST["Item_ouid"]
				, $_REQUEST["Item_sub_ouid"]
				, $_REQUEST["Item_EduGrpCode"]
				, $_REQUEST["Item_FileNo"]
				, $_REQUEST["Item_PersonType"]
				, $_REQUEST["Item_PersonID"]
				, $_REQUEST["Item_StNo"]
				, $_REQUEST["Item_PFName"]
				, $_REQUEST["Item_PLName"]
				, $_REQUEST["Item_FileTitle"]
				, $_SESSION["PersonID"]
				);
		$CurLastID = manage_files::GetLastID();
		if($LastID!=$CurLastID)
		{
			echo "<script>document.location='NewFile.php?UpdateID=".$CurLastID."';</script>";
			die();
		}
	}	
	else 
	{
		if($UpdatePermission=="YES")
		{	
			manage_files::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_ouid"]
				, $_REQUEST["Item_sub_ouid"]
				, $_REQUEST["Item_EduGrpCode"]
				, $_REQUEST["Item_FileNo"]
				, $_REQUEST["Item_PersonType"]
				, $_REQUEST["Item_PersonID"]
				, $_REQUEST["Item_StNo"]
				, $_REQUEST["Item_PFName"]
				, $_REQUEST["Item_PLName"]
				, $_REQUEST["Item_FileTitle"]
				);
		}
		else
		{
			echo "<p align=center><font color=red>مجوز بروزرسانی برای شما وجود ندارد</font></p>";
		}
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
$PersonName = "";
$UnitName = "";
$SubUnitName = "";
$EduGroupName = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_files();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.FileTypeID.value='".$obj->FileTypeID."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ouid.value='".$obj->ouid."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_sub_ouid.value='".$obj->sub_ouid."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_EduGrpCode.value='".$obj->EduGrpCode."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_FileNo.value='".$obj->FileNo."'; \r\n ";
	$LoadDataJavascriptCode .= "if(document.f1.Item_PersonType) \r\n ";
	$LoadDataJavascriptCode .= "	document.f1.Item_PersonType.value='".$obj->PersonType."'; \r\n ";
	$LoadDataJavascriptCode .= "if(document.f1.Item_PersonID) \r\n "; 
	$LoadDataJavascriptCode .= "	document.f1.Item_PersonID.value='".$obj->PersonID."'; \r\n ";
	$LoadDataJavascriptCode .= "if(document.f1.Item_StNo) \r\n "; 
	$LoadDataJavascriptCode .= "	document.f1.Item_StNo.value='".$obj->StNo."'; \r\n ";
	$LoadDataJavascriptCode .= "if(document.f1.Item_PFName) \r\n "; 
	$LoadDataJavascriptCode .= "	document.f1.Item_PFName.value='".$obj->PFName."'; \r\n ";
	$LoadDataJavascriptCode .= "if(document.f1.Item_PLName) \r\n "; 
	$LoadDataJavascriptCode .= "	document.f1.Item_PLName.value='".$obj->PLName."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_FileTitle.value='".$obj->FileTitle."'; \r\n ";
	$LoadDataJavascriptCode .= "if(document.f1.Item_FileStatus) \r\n ";
	$LoadDataJavascriptCode .= "	document.f1.Item_FileStatus.value='".$obj->FileStatus."'; \r\n ";
	$PersonName = $obj->PFName." ".$obj->PLName; 
	$UnitName = $obj->UnitName;
	$SubUnitName = $obj->SubUnitName;
	$EduGroupName = $obj->EduGrpName;
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
<br><table width=90% border=1 cellspacing=0 align=center  class=".container">
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش پرونده ها</td></tr>
<!-- در مود ویرایش امکان مدیریت سایر بخشهای پرونده را در صورت داشتن دسترسی می دهد -->
<?php if(isset($_REQUEST["UpdateID"])) { ?>
<tr bgcolor=#cccccc>
	<td align=center>
	<table width=100% border=1 cellspacing=0 class=".container">
		<tr class="row">
			<td width=11% align=center bgcolor=#efefef>
				<a href='NewFile.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img width=50 border=0 title="<?php echo C_FILE_DEFINITION; ?>" ><i class="far fa-folder"></i><br><?php echo C_DEFINITION; ?></a>
			</td>
			<td width=11%  class="col-sm-2" align=center >
				<a href='ManageFileContent.php?ContentType=TEXT&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><i width=50 border=0 class="far fa-folder-open" title="<?php echo C_TEXTS; ?>"></i><br><?php echo C_TEXTS; ?></a>
			</td>
			<td width=11% class="col-sm-2"  align=center >
				<a href='ManageFileContent.php?ContentType=PHOTO&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><i width=50 border=0 title="<?php echo C_IMAGES; ?>" class="far fa-photo-video"></i><br><?php echo C_IMAGES; ?></a>
			</td>
			<td width=11% class="col-sm-2"  align=center >
				<a href='ManageFileContent.php?ContentType=FILE&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><i  width=50 border=0 title="<?php echo C_FILES; ?>" class="far fa-file-music"></i><br><?php echo C_FILES; ?></a>
			</td>
			<td width=11% class="col-sm-1"  align=center >
				<a href='ManageFileContent.php?ContentType=FORM&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><i  width=50 border=0 title="<?php echo C_FORMS; ?>" class="far fa-folders"></i><br><?php echo C_FORMS; ?></a>
			</td>
			<td width=11% class="col-sm-1"  align=center >
				<a href='ManageFileContent.php?ContentType=LETTER&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><i  width=50 border=0 title="<?php echo C_LETTERS; ?>" class="far fa-mail-bulk"></i><br><?php echo C_LETTERS; ?></a>
			</td>
			<td width=11% class="col-sm-1"  align=center >
				<a href='ManageFileContent.php?ContentType=SESSION&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><i  width=50 border=0 title="<?php echo C_SESSIONS; ?>" class="far fa-handshake"></i><br><?php echo C_SESSIONS; ?></a>
			</td>
			<td width=11% class="col-sm-2"  align=center >
				<a href='ManageFileTempUsers.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><i  width=50 border=0 title="<?php echo C_DEBTS; ?>" class="far fa-share-alt"></i><br><?php echo C_DEBTS; ?></a>
			</td>
			<td width=11% class="col-sm-1"  align=center >
				<a href='ShowFileHistory.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><i width=50 border=0 title="<?php echo C_HISTORY; ?>"  class="far fa-history"></i><br><?php echo C_HISTORY; ?></a>
			</td>
		</tr>
	</table>
	</td>
</tr>
<?php } ?>
<tr><td>
<table width=100% border=0 class=".container">

<tr id=tr_FileTypeID name=tr_FileTypeID style='display:'>
<td width=1% nowrap>
    <?php echo C_FILE_TYPE; ?>
</td>
<td nowrap>
	<input type=hidden name=FileTypeID id=FileTypeID value='<?php echo $FileTypeObj->FileTypeID ?>'>
	<span id=FileTypeName name=FileTypeName><?php echo $FileTypeName ?></span>
</td>
</tr>
<tr id=tr_FileNo name=tr_FileNo style='display:'>
<td width=1% nowrap>
    <?php echo C_FILE_NUMBER; ?>
</td>
<td nowrap>
	<input type=text name=Item_FileNo id=Item_FileNo>
</td>
</tr>
<?php if($FileTypeObj->RelatedToPerson=="YES") { ?>
<tr id=tr_PersonType name=tr_PersonType style='display:'>
<td width=1% nowrap>
    <?php echo C_USER_TYPE; ?>
</td>
<td nowrap>
	<select name=Item_PersonType id=Item_PersonType onchange='javascript: ChangePersonType();'>
	<?php 
	if($FileTypeObj->RelatedPersonCanBeProffessor=="YES") echo "<option value='PROF'>".C_PROFESSOR;
	if($FileTypeObj->RelatedPersonCanBeStaff=="YES") echo "<option value='STAFF'>".C_EMPLOYER;
	if($FileTypeObj->RelatedPersonCanBeStudent=="YES") echo "<option value='STUDENT'>".C_STUDENT;
	if($FileTypeObj->RelatedPersonCanBeOther=="YES") echo "<option value='OTHER'>".C_OTHERS;
	?>
	</select>
</td>
</tr>

<tr id=tr_PersonID name=tr_PersonID style='display:none'>
<td colspan=2 nowrap>
	<span name=PersonSpan id=PersonSpan><?php echo $PersonName ?></span>&nbsp;
	<input type=hidden name=Item_PersonID id=Item_PersonID>
	<a href='javascript: document.f2.submit()'>[<?php echo C_SELECT; ?>]</a>
</td>
</tr>
<tr id=tr_StNo name=tr_StNo style='display:'>
<td colspan=2 nowrap>
	<span name=StudentSpan id=StudentSpan><?php echo $PersonName ?></span>&nbsp;
	<input type=hidden name=Item_StNo id=Item_StNo>
	<a href='javascript: document.f3.submit()'>[<?php echo C_SELECT; ?>]</a>
</td>
</tr>
<tr id=tr_PFName name=tr_PFName style='display:'>
<td width=1% nowrap>
    <?php echo C_NAME; ?>
</td>
<td nowrap>
	<input type=text name=Item_PFName id=Item_PFName>
</td>
</tr>
<tr id=tr_PLName name=tr_PLName style='display:'>
<td width=1% nowrap>
    <?php echo C_LAST_NAME; ?>
</td>
<td nowrap>
	<input type=text name=Item_PLName id=Item_PLName>
</td>
</tr>
<?php } ?>
<tr id=tr_ouid name=tr_ouid style='display:'>
<td width=1% nowrap>
    <?php echo C_STRUCTURE_UNIT; ?>
</td>
<td nowrap>
	<span name=UnitSpan id=UnitSpan><?php echo $UnitName ?></span>&nbsp;
	<?php if($FileTypeObj->SetLocationType=="NONE") { ?>
	<select name=Item_ouid id=Item_ouid onchange='javascript: document.f1.submit()'>
		<option value='0'>-
		<?php echo FormUtils::CreateUnitsOptions($SelectedUnit); ?>
	</select>
	<?php } else { ?>
	<input type=hidden name=Item_ouid id=Item_ouid>
	<?php } ?>
</td>
</tr>
<tr id=tr_sub_ouid name=tr_sub_ouid style='display:'>
<td width=1% nowrap>
    <?php echo C_STRUCTURE_SUBUNIT; ?>
</td>
<td nowrap>
	<span name=SubUnitSpan id=SubUnitSpan><?php echo $SubUnitName ?></span>&nbsp;
	<?php if($FileTypeObj->SetLocationType=="NONE") { ?>
	<select name=Item_sub_ouid id=Item_sub_ouid>
		<?php echo FormUtils::CreateSubUnitsOptions($SelectedUnit, ""); ?>
	</select>
	<?php } else { ?>
	<input type=hidden name=Item_sub_ouid id=Item_sub_ouid>
	<?php } ?>
</td>
</tr>
<tr id=tr_EduGrpCode name=tr_EduGrpCode style='display:'>
<td width=1% nowrap>
    <?php echo C_INSTRUCTION_GROUP; ?>
</td>
<td nowrap>
	<span name=EduGrpSpan id=EduGrpSpan><?php echo $EduGroupName ?></span>&nbsp;
	<?php if($FileTypeObj->SetLocationType=="NONE") { ?>
	<select name=Item_EduGrpCode id=Item_EduGrpCode>
		<?php echo FormUtils::CreateEduGrpsOptionsForFacility($SelectedUnit, ""); ?>
	</select>
	<?php } else { ?>
	<input type=hidden name=Item_EduGrpCode id=Item_EduGrpCode>
	<?php } ?>
</td>
</tr>
<tr id=tr_FileTitle name=tr_FileTitle style='display:'>
<td width=1% nowrap>
    <?php echo C_TITLE; ?>
</td>
<td nowrap>
	<input type=text name=Item_FileTitle id=Item_FileTitle>
</td>
</tr>
</table></td></tr>
<!-- در صورتیکه در مد ایجاد باشد یا مجوز بروزرسانی وجود داشته باشد دکمه ذخیره را نمایش می دهد -->
<tr class=FooterOfTable>
<td align=center>&nbsp;
<?php if(!isset($_REQUEST["UpdateID"]) || $UpdatePermission=="YES") { ?>
<input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
<?php } if(isset($_REQUEST["UpdateID"]) && $RemovePermission=="YES") { ?>
&nbsp;<input type=button onclick='javascript: if(confirm("آیا اطمینان دارید")) document.location="RemoveFile.php?UpdateID=<?php echo $_REQUEST["UpdateID"] ?>";' value='حذف پرونده'>
<?php } ?>
</td></tr>
</table>
<input type=hidden name=Save id=Save value=0>
</form>
<form method=post id=f2 name=f2 target=_blank action=SelectStaff.php>
	<input type=hidden name=InputName id=InputName value='Item_PersonID'>
	<input type=hidden name=SpanName id=SpanName value='PersonSpan'>
	<?php if($FileTypeObj->SetLocationType=="RELATED_PERSON") { ?>
	<input type=hidden name=UnitInputName id=UnitInputName value='Item_ouid'>
	<input type=hidden name=SubUnitInputName id=SubUnitInputName value='Item_sub_ouid'>
	<input type=hidden name=EduGrpInputName id=EduGrpInputName value='Item_EduGrpCode'>
	
	<input type=hidden name=UnitSpanName id=UnitSpanName value='UnitSpan'>
	<input type=hidden name=SubUnitSpanName id=SubUnitSpanName value='SubUnitSpan'>
	<input type=hidden name=EduGrpSpanName id=EduGrpSpanName value='EduGrpSpan'>
	<?php } ?>
</form>
<form method=post id=f3 name=f3 target=_blank action=SelectStudent.php>
	<input type=hidden name=InputName id=InputName value='Item_StNo'>
	<input type=hidden name=SpanName id=SpanName value='StudentSpan'>
	<?php if($FileTypeObj->SetLocationType=="RELATED_PERSON") { ?>
	<input type=hidden name=UnitInputName id=UnitInputName value='Item_ouid'>
	<input type=hidden name=SubUnitInputName id=SubUnitInputName value='Item_sub_ouid'>
	<input type=hidden name=EduGrpInputName id=EduGrpInputName value='Item_EduGrpCode'>
	
	<input type=hidden name=UnitSpanName id=UnitSpanName value='UnitSpan'>
	<input type=hidden name=SubUnitSpanName id=SubUnitSpanName value='SubUnitSpan'>
	<input type=hidden name=EduGrpSpanName id=EduGrpSpanName value='EduGrpSpan'>
	<?php } ?>
</form>
<script>
	
	function ValidateForm()
	{
		document.f1.Save.value='1';
		document.f1.submit();
	}
	function ChangePersonType()
	{
		if(document.getElementById('tr_PersonID')==null)
			return;
		document.getElementById('tr_PersonID').style.display = 'none';
		document.getElementById('tr_StNo').style.display = 'none';
		document.getElementById('tr_PFName').style.display = 'none';
		document.getElementById('tr_PLName').style.display = 'none';
		
		if(document.f1.Item_PersonType.value=="STAFF" || document.f1.Item_PersonType.value=="PROF")
		{
			document.getElementById('tr_PersonID').style.display = '';
		} 
		if(document.f1.Item_PersonType.value=="STUDENT")
		{
			document.getElementById('tr_StNo').style.display = '';
		}
		if(document.f1.Item_PersonType.value=="OTHER")
		{
			document.getElementById('tr_PFName').style.display = '';
			document.getElementById('tr_PLName').style.display = '';
		}
	}
	<?php 
			if(isset($_REQUEST["Item_PersonType"]))
			{ 	
				echo "if(document.f1.Item_PersonType) { \r\n";
				echo "document.f1.Item_PersonType.value='".$_REQUEST["Item_PersonType"]."';\r\n";
				echo "document.f1.Item_PFName.value='".$_REQUEST["Item_PFName"]."'\r\n;";
				echo "document.f1.Item_PLName.value='".$_REQUEST["Item_PLName"]."'\r\n;";
				echo "}\r\n";
			}
			if($FileTypeObj->SetLocationType=="CREATOR") 
			{
				echo "document.getElementById('UnitSpan').innerHTML='".$_SESSION["UnitName"]."';\r\n";
				echo "document.getElementById('SubUnitSpan').innerHTML='".$_SESSION["SubUnitName"]."';\r\n";
				echo "document.getElementById('EduGrpSpan').innerHTML='".$_SESSION["EduGrpName"]."';\r\n";
				echo "document.f1.Item_ouid.value='".$_SESSION["UnitID"]."';\r\n";
				echo "document.f1.Item_sub_ouid.value='".$_SESSION["sub_ouid"]."';\r\n";
				echo "document.f1.Item_EduGrpCode.value='".$_SESSION["EduGrpCode"]."';\r\n";
			}
	?>
	ChangePersonType();
	<? echo $LoadDataJavascriptCode; ?>
</script>
