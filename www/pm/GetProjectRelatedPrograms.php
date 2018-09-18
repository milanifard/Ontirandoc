<?php 
/*
 باز گرداندن لیست گروه های پروژه در یک واحد سازمانی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-5-16
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTasks.class.php");
$CurValue = 0;
if(isset($_REQUEST["CurValue"]))
	$CurValue = $_REQUEST["CurValue"];
echo "<select name='Item_ProgramLevelID' id='Item_ProgramLevelID'>";
echo "<option value=0>-";
echo manage_ProjectTasks::GetRelatedProgramLevels($_REQUEST["ProjectID"], $CurValue);
echo "</select>";
?>