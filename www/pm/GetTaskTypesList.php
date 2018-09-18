<?php 
/*
 باز گرداندن لیست انواع کارهای تعریف شده در یک پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTaskTypes.class.php");
$CurValue = 0;
if(isset($_REQUEST["CurValue"]))
	$CurValue = $_REQUEST["CurValue"];
echo "<select name='Item_ProjectTaskTypeID' id='Item_ProjectTaskTypeID'>";
echo "<option value=0>-";
echo manage_ProjectTaskTypes::CreateSelectOptions($_REQUEST["ProjectID"], $CurValue);
echo "</select>";
?>