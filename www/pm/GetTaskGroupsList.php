<?php 
/*
 باز گرداندن لیست گروه های کارهای تعریف شده در یک پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-10-06
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTaskGroups.class.php");
$CurValue = 0;
if(isset($_REQUEST["CurValue"]))
	$CurValue = $_REQUEST["CurValue"];
echo "<select name='Item_TaskGroupID' id='Item_TaskGroupID'>";
echo "<option value=0>-";
echo manage_ProjectTaskGroups::CreateSelectOptions($_REQUEST["ProjectID"], $CurValue);
echo "</select>";
?>