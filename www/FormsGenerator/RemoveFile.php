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

$RemovePermission = "NO";

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
			if($AccessList[$k]->RemovePermission=="YES")
				$RemovePermission = "YES";
		}
		if($AccessList[$k]->AccessRange=="UNIT")
		{
			$UnitList = manage_FileTypeUserPermittedUnits::GetList(" FileTypeUserPermissionID='".$AccessList[$k]->FileTypeUserPermissionID."' ");
			for($j=0; $j<count($UnitList); $j++)
			{
				if($UnitList[$j]->ouid==$CurFile->ouid)
				{
					if($AccessList[$k]->RemovePermission=="YES")
						$RemovePermission = "YES";
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
					if($AccessList[$k]->RemovePermission=="YES")
						$RemovePermission = "YES";
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
					if($AccessList[$k]->RemovePermission=="YES")
						$RemovePermission = "YES";
				}
			}
		}
		
	}
}
else
{
	echo "UpdateID=? :D";
	die(); 
}

if($RemovePermission=="NO")
{
	echo ":D";
	die();
}

manage_files::Remove($_REQUEST["UpdateID"]);
echo "<p align=center><font color=green>پرونده حذف شد</font></p>";

?>