<?php 
/*
 باز گرداندن لیست گروه های پروژه در یک واحد سازمانی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-5-16
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectGroups.class.php");
echo "<select name='Item_ProjectGroupID' id='Item_ProjectGroupID'>";
echo "<option value=0>-";
echo manage_ProjectGroups::CreateSelectOptions($_REQUEST["ouid"]);
echo "</select>";
?>