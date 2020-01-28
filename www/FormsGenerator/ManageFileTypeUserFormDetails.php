<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormFields.class.php");
include_once("classes/FileTypeUserPermissions.class.php");
include_once("classes/FileTypeUserPermittedForms.class.php");
HTMLBegin();
$ListCondition = " 1=1 ";
$ParentObj = new be_FileTypeUserPermittedForms();
$ParentObj->LoadDataFromDatabase($_REQUEST["id"]);

$FormObj = new be_FormsStruct();
$FormObj->LoadDataFromDatabase($ParentObj->FormsStructID);

if(isset($_REQUEST["Save"]))
{
	$res = manage_FormFields::GetList($ParentObj->FormsStructID, "OrderInInputForm");
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
	for($i=0; $i<count($res); $i++)
	{
		$FieldID = $res[$i]->FormFieldID;
		$AccessType = $_REQUEST["s_".$FieldID];
		manage_FileTypeUserPermittedForms::SetFieldAccessType($FieldID, $_REQUEST["id"], $AccessType);
	}
}

$FieldList = manage_FormFields::GetList($ParentObj->FormsStructID, "OrderInInputForm");
echo "<br>";
echo "<form method=post><input type=hidden name=id id=id value='".$_REQUEST["id"]."'><input type=hidden name=Save id=Save value=1>";
echo "<table width=50% align=center border=1 cellspacing=0 cellpadding=5>";
echo "<tr class=HeaderOfTable>";
echo "<td align=center>".$FormObj->FormTitle."</td>";
echo "</tr>";
echo "<tr>";
echo "<td>";
echo "<table width=100%>";
for($i=0; $i<count($FieldList); $i++)
{
	$AccessType = manage_FileTypeUserPermittedForms::GetFieldAccessType($FieldList[$i]->FormFieldID, $_REQUEST["id"]);
	echo "<tr>";
	echo "<td>".$FieldList[$i]->FieldTitle."</td>";
	echo "<td>";
	echo "<select name='s_".$FieldList[$i]->FormFieldID."'>";
	echo "<option value='EDITABLE'>قابل ویرایش";
	echo "<option value='READ_ONLY' ";
	if($AccessType=="READ_ONLY")
		echo " selected ";
	echo ">فقط خواندنی";
	echo "<option value='HIDE' ";
	if($AccessType=="HIDE")
		echo " selected ";
	echo ">عدم دسترسی";
	echo "</select>";
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
echo "</td>";
echo "</tr>";
echo "<tr class=HeaderOfTable>";
echo "<td align=center><input type=submit value='ذخیره'></td>";
echo "</tr>";
echo "</table>";
?>
</html>