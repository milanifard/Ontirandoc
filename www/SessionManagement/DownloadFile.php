<?php
include("header.inc.php");
include("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
include("classes/SessionDecisions.class.php");
include("classes/SessionPreCommands.class.php");
include("classes/SessionDocuments.class.php");
include("classes/SessionMembers.class.php");
include("../sharedClasses/SharedClass.class.php");
$RecID = $_REQUEST["RecID"];
$FileType = $_REQUEST["FileType"];
if($FileType=="SessionDescision")
{
	$obj = new be_UniversitySessions();
	$obj->LoadDataFromDatabase($RecID);
	$pc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $obj->UniversitySessionID);
	
	header('Content-disposition: filename="' . $obj->SessionDescisionsFileName.'"');
	header('Content-type: application/octetstream');
	header('Pragma: no-cache');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Transfer-Encoding: binary");
	if($pc->GetPermission("SessionDescisionsFile")!="NONE")
	{
		// در صورتیکه وضعیت صورتجلسه تایید شده باشد قابل دانلود اس
		if($obj->DescisionsFileStatus=="CONFIRMED")
			echo $obj->SessionDescisionsFile;
		else
		{
			// در صورتیکه وضعیت صورتجلسه تایید نشده باشد فقط برای افراد صاحب امضا یا دارای مجوز ویرایش قابل دریافت است
			if($pc->GetPermission("SessionDescisionsFile")!="WRITE" || manage_SessionMembers::HasSignRight($obj->UniversitySessionID, $_SESSION["PersonID"])=="YES")
				echo $obj->SessionDescisionsFile;
			else
				echo "مجوز خواندن این فایل برای شما وجود ندارد (2(";
		}
	}
	else
		echo "مجوز خواندن این فایل برای شما وجود ندارد";
}
else if($FileType=="Decesion")
{
	$obj = new be_SessionDecisions();
	$obj->LoadDataFromDatabase($RecID);
	$pc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $obj->UniversitySessionID);	
	header('Content-disposition: filename="' . $obj->RelatedFileName.'"');
	header('Content-type: application/octetstream');
	header('Pragma: no-cache');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Transfer-Encoding: binary");
	if($pc->GetPermission("View_SessionDecisions")=="PUBLIC" || ($pc->GetPermission("View_SessionDecisions")=="PRIVATE" && $obj->CreatorPersonID==$_SESSION["PersonID"]))
		echo $obj->RelatedFile;
	else
		echo "مجوز خواندن این فایل برای شما وجود ندارد";
}
else if($FileType=="PreCommand")
{
	$obj = new be_SessionPreCommands();
	$obj->LoadDataFromDatabase($RecID);
	$pc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $obj->UniversitySessionID);
	header('Content-disposition: filename="' . $obj->RelatedFileName.'"');
	header('Content-type: application/octetstream');
	header('Pragma: no-cache');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Transfer-Encoding: binary");
	if($pc->GetPermission("View_SessionPreCommands")=="PUBLIC" || ($pc->GetPermission("View_SessionPreCommands")=="PRIVATE" && $obj->CreatorPersonID==$_SESSION["PersonID"]))
		echo $obj->RelatedFile;
	else
		echo "مجوز خواندن این فایل برای شما وجود ندارد";
}
else if($FileType=="Document")
{
	$obj = new be_SessionDocuments();
	$obj->LoadDataFromDatabase($RecID);
	$pc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $obj->UniversitySessionID);
	header('Content-disposition: filename="' . $obj->DocumentFileName.'"');
	header('Content-type: application/octetstream');
	header('Pragma: no-cache');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Transfer-Encoding: binary");
	if($pc->GetPermission("View_SessionDocuments")=="PUBLIC" || ($pc->GetPermission("View_SessionDocuments")=="PRIVATE" && $obj->CreatorPersonID==$_SESSION["PersonID"]))
		echo $obj->DocumentFile;
	else
		echo "مجوز خواندن این فایل برای شما وجود ندارد";
}

?>