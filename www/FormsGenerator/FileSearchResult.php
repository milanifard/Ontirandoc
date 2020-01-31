<?php
include("../shares/header.inc.php");
include_once("classes/FormUtils.class.php");
include_once("classes/FileTypes.class.php");
require_once("classes/SecurityManager.class.php");
include_once("classes/files.class.php");
HTMLBegin();
$list = SecurityManager::GetUserPermittedFileTypesForAccess($_SESSION["PersonID"]);
$_REQUEST = SecurityManager::validateInput($_REQUEST);
$FileTypeOptions = "";
$personTypes = ["STAFF" => "کارمند", "PROF" => "هیات علمی", "STUDENT" => "دانشجو", "OTHER" => "سایر"];
foreach($list as $key => $item)
{
	if($key>0)
		$FileTypeOptions .= " or ";
	$FileTypeOptions .= "(f.FileTypeID=".$item["FileTypeID"];
	// اگر دسترسی کاربر به این نوع دارای محدودیتی بود آن محدودیت هم به شرط اضافه می شود
	if($item["AccessRange"]=="UNIT")
		$FileTypeOptions .= " and f.ouid in (".$item["PermittedRangeList"].") ";
	else if($item["AccessRange"]=="SUB_UNIT")
		$FileTypeOptions .= " and f.sub_ouid in (".$item["PermittedRangeList"].")  ";
	else if($item["AccessRange"]=="EDU_GROUP")
		$FileTypeOptions .= " and f.EduGrpCode in (".$item["PermittedRangeList"].")  ";
	else if($item["AccessRange"]=="ONLY_USER")
		$FileTypeOptions .= " and f.CreatorID='".$_SESSION["PersonID"]."' ";
	$FileTypeOptions .= ")";
}
if(empty($FileTypeOptions))
{
	echo "هیچ نوع پرونده ای در دسترس شما نمی باشد";
	die();
}
$ListCondition = " (".$FileTypeOptions.") ";
if($_REQUEST["FileType"] && $_REQUEST["FileTypeID"]!="0" && !empty($_REQUEST["FileTypeID"]))
	$ListCondition .= " and FileTypeID='".$_REQUEST["FileTypeID"]."' ";
if($_REQUEST["FileID"] && !empty($_REQUEST["FileID"]))
	$ListCondition .= " and FileID='".$_REQUEST["FileID"]."' ";
if($_REQUEST["ouid"] && $_REQUEST["ouid"]!="0")
	$ListCondition .= " and f.ouid='".$_REQUEST["ouid"]."' ";
if($_REQUEST["sub_ouid"] && $_REQUEST["sub_ouid"]!="0")
	$ListCondition .= " and f.sub_ouid='".$_REQUEST["sub_ouid"]."' ";
if($_REQUEST["EduGrpCode"] && $_REQUEST["EduGrpCode"]!="0")
	$ListCondition .= " and f.EduGrpCode='".$_REQUEST["EduGrpCode"]."' ";
if($_REQUEST["FileNo"] && !empty($_REQUEST["FileNo"]))
	$ListCondition .= " and f.FileNo like '%".$_REQUEST["EduGrpCode"]."%' ";
	
if($_REQUEST["PLName"] && !empty($_REQUEST["PLName"]))
	$ListCondition .= " and (p.plname like '%".$_REQUEST["PLName"]."%' or s.PLName like '%".$_REQUEST["PLName"]."%' or f.PLName like '%".$_REQUEST["PLName"]."%')";
if($_REQUEST["PFName"] && !empty($_REQUEST["PFName"]))
	$ListCondition .= " and (p.pfname like '%".$_REQUEST["PFName"]."%' or s.PFName like '%".$_REQUEST["PFName"]."%' or f.PFName like '%".$_REQUEST["PFName"]."%')";
	
//echo $ListCondition;

$FileList = manage_files::GetList($ListCondition);
echo "<br><table width='90%' align='center' cellspacing=0 cellpadding='3px' border='1px'>";
echo "<tr class='HeaderOfTable'>";
echo "<td width='1%'>محتویات</td>";
echo "<td width='1%'>نوع</td>";
echo "<td>عنوان</td>";
echo "<td width='1%'>شماره</td>";
echo "<td width='1%'>نوع فرد</td>";
echo "<td width='1%'>شخص مربوطه</td>";
echo "<td>مکان</td>";
echo "<td>امانت گیرندگان</td>";
echo "</tr>";
foreach($FileList as $item)
{
	echo "<tr>";
	echo "<td nowrap><a href='NewFile.php?UpdateID=".$item->FileID."'><img border=0 width=30 src='images/edit.jpeg'></a></td>";
	echo "<td nowrap>".$item->FileTypeName."</td>";
	echo "<td nowrap>&nbsp;".$item->FileTitle."</td>";
	echo "<td nowrap>&nbsp;".$item->FileNo."</td>";		
	if($item->RelatedToPerson==="YES")
	{
		echo "<td nowrap>&nbsp;";
		echo $personTypes[$item->PersonType];
		echo "</td>";
		echo "<td nowrap>&nbsp;".$item->PFName." ".$item->PLName."</td>";
	}
	else
	{
		echo "<td colspan='2'>&nbsp;</td>";
	}
	$Place = $item->UnitName;
	if(!empty($item->SubUnitName))
	{
		if(!empty($Place))
			$Place .= " - ".$item->SubUnitName;
	}
	if(!empty($item->EduGrpName))
	{
		if(!empty($Place))
			$Place .= " - ".$item->EduGrpName;
	}
	echo "<td nowrap>&nbsp;".$Place."</td>";
	echo "<td>&nbsp;".$item->GetDeptedUsersName()."</td>";
	echo "</tr>";
}
echo "</table>";
?>
</html>
