<?php
include("header.inc.php");
include_once("classes/FormUtils.class.php");
include_once("classes/FileTypes.class.php");
include_once("classes/SecurityManager.class.php");
include_once("classes/files.class.php");
HTMLBegin();
$list = SecurityManager::GetUserPermittedFileTypesForAccess($_SESSION["PersonID"]);
$FileTypeOptions = "";
for($i=0; $i<count($list); $i++)
{
	if($i>0)
		$FileTypeOptions .= " or ";
	$FileTypeOptions .= "(f.FileTypeID=".$list[$i]["FileTypeID"];
	// اگر دسترسی کاربر به این نوع دارای محدودیتی بود آن محدودیت هم به شرط اضافه می شود
	if($list[$i]["AccessRange"]=="UNIT")
		$FileTypeOptions .= " and f.ouid in (".$list[$i]["PermittedRangeList"].") ";
	else if($list[$i]["AccessRange"]=="SUB_UNIT")
		$FileTypeOptions .= " and f.sub_ouid in (".$list[$i]["PermittedRangeList"].")  ";
	else if($list[$i]["AccessRange"]=="EDU_GROUP")
		$FileTypeOptions .= " and f.EduGrpCode in (".$list[$i]["PermittedRangeList"].")  ";
	else if($list[$i]["AccessRange"]=="ONLY_USER")
		$FileTypeOptions .= " and f.CreatorID='".$_SESSION["PersonID"]."' ";
	$FileTypeOptions .= ")";
}
if($FileTypeOptions=="")
{
	echo "هیچ نوع پرونده ای در دسترس شما نمی باشد";
	die();
}
$ListCondition = " (".$FileTypeOptions.") ";
if($_REQUEST["FileType"] && $_REQUEST["FileTypeID"]!="0" && $_REQUEST["FileTypeID"]!="")
	$ListCondition .= " and FileTypeID='".$_REQUEST["FileTypeID"]."' ";
if($_REQUEST["FileID"] && $_REQUEST["FileID"]!="")
	$ListCondition .= " and FileID='".$_REQUEST["FileID"]."' ";
if($_REQUEST["ouid"] && $_REQUEST["ouid"]!="0")
	$ListCondition .= " and f.ouid='".$_REQUEST["ouid"]."' ";
if($_REQUEST["sub_ouid"] && $_REQUEST["sub_ouid"]!="0")
	$ListCondition .= " and f.sub_ouid='".$_REQUEST["sub_ouid"]."' ";
if($_REQUEST["EduGrpCode"] && $_REQUEST["EduGrpCode"]!="0")
	$ListCondition .= " and f.EduGrpCode='".$_REQUEST["EduGrpCode"]."' ";
if($_REQUEST["FileNo"] && $_REQUEST["FileNo"]!="")
	$ListCondition .= " and f.FileNo like '%".$_REQUEST["EduGrpCode"]."%' ";
	
if($_REQUEST["PLName"] && $_REQUEST["PLName"]!="")
	$ListCondition .= " and (p.plname like '%".$_REQUEST["PLName"]."%' or s.PLName like '%".$_REQUEST["PLName"]."%' or f.PLName like '%".$_REQUEST["PLName"]."%')";
if($_REQUEST["PFName"] && $_REQUEST["PFName"]!="")
	$ListCondition .= " and (p.pfname like '%".$_REQUEST["PFName"]."%' or s.PFName like '%".$_REQUEST["PFName"]."%' or f.PFName like '%".$_REQUEST["PFName"]."%')";
	
//echo $ListCondition;

$FileList = manage_files::GetList($ListCondition);
echo "<br><table width=90% align=center cellspacing=0 cellpadding=3 border=1>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%>محتویات</td>";
echo "<td width=1%>نوع</td>";
echo "<td>عنوان</td>";
echo "<td width=1%>شماره</td>";
echo "<td width=1%>نوع فرد</td>";
echo "<td width=1%>شخص مربوطه</td>";
echo "<td>مکان</td>";
echo "<td>امانت گیرندگان</td>";
echo "</tr>";
for($i=0; $i<count($FileList); $i++)
{
	if($i%2==0)
		echo "<tr class=OddRow>";
	else
		echo "<tr class=EvenRow>";
	echo "<td nowrap><a href='NewFile.php?UpdateID=".$FileList[$i]->FileID."'><img border=0 width=30 src='images/edit.jpeg'></a></td>";
	echo "<td nowrap>".$FileList[$i]->FileTypeName."</td>";
	echo "<td nowrap>&nbsp;".$FileList[$i]->FileTitle."</td>";
	echo "<td nowrap>&nbsp;".$FileList[$i]->FileNo."</td>";		
	if($FileList[$i]->RelatedToPerson=="YES")
	{
		echo "<td nowrap>&nbsp;";
		if($FileList[$i]->PersonType=="STAFF")
			echo "کارمند";
		else if($FileList[$i]->PersonType=="PROF")
			echo "هیات علمی";
		else if($FileList[$i]->PersonType=="STUDENT")
			echo "دانشجو";
		else if($FileList[$i]->PersonType=="OTHER")
			echo "سایر";
		echo "</td>";
		echo "<td nowrap>&nbsp;".$FileList[$i]->PFName." ".$FileList[$i]->PLName."</td>";
	}
	else
	{
		echo "<td colspan=2>&nbsp;</td>";
	}
	$Place = $FileList[$i]->UnitName;
	if($FileList[$i]->SubUnitName!="")
	{
		if($Place != "")
			$Place .= " - ".$FileList[$i]->SubUnitName;
	}
	if($FileList[$i]->EduGrpName!="")
	{
		if($Place != "")
			$Place .= " - ".$FileList[$i]->EduGrpName;
	}
	echo "<td nowrap>&nbsp;".$Place."</td>";
	echo "<td>&nbsp;".$FileList[$i]->GetDeptedUsersName()."</td>";
	echo "</tr>";
}
echo "</table>";
?>
</html>