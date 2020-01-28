<?php 
/*
 صفحه نمایش پیامهای سیستمی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");


HTMLBegin();
$now = date("Y-m-d");
?>
<br>
<?php
echo manage_ProjectTasks::CreateKartableHeader("TasksMessages"); 

if (!isset($_GET["Full"]))
	$messages = manage_ProjectTasks::GetLastSystemMessage($_SESSION["PersonID"]);
else
	$messages = manage_ProjectTasks::GetLastSystemMessage($_SESSION["PersonID"], TRUE, TRUE);

if($messages!="")
{
	echo "<table width=98% align=center class=\"table table-bordered\">";
	echo "<tr class=\"warning\"><td align=center class=\"text-center\"><b> <? echo  C_LATEST_STATUS; ?> <b></td></tr class=\"warning\">";
	echo "<tr class=warning><td class=\"text-center\"><? echo  C_TIME; ?></td><td class=\"text-center\"><? echo  C_JOB_DONE; ?></td><td width=7% class=\"text-center\"><? echo  C_RELATED_USER; ?></td><td class=\"text-center\"><? echo  C_JOB_TITLE; ?></td></tr>";
	echo $messages;
	echo "</table>";
}


if (!isset($_GET["Full"]))
	$messages = manage_ProjectTasks::GetLastSystemMessage($_SESSION["PersonID"], FALSE);
else
	$messages = manage_ProjectTasks::GetLastSystemMessage($_SESSION["PersonID"], FALSE, TRUE);
if($messages!="")
{
	echo "<table width=98% align=center class=\"table table-bordered\">";
	echo "<tr class=\"warning\"><td align=center class=\"text-center\"><b><? echo  C_LATEST_STATUS; ?><b></td></tr>";
	echo "<tr class=warning><td wiclass=\"text-center\"><? echo  C_TIME; ?></td><td class=\"text-center\"><? echo  C_JOB_DONE; ?></td><td class=\"text-center\"><? echo  C_RELATED_USER; ?></td></tr>";
	echo $messages;
	echo "</table>";
}

echo '<p class="text">';
if (!isset($_GET["Full"]))
	echo "<input onclick=\"javascript: Full();\" value=\"" . C_MORE_DET ."\" type=\"button\">";
else
	echo "<input onclick=\"javascript: Partial();\" value=\"" . C_LESS_DET ."\" type=\"button\">>";
echo "</p>";

?>

<br /><br /><br />

<script>
function Partial() { window.location = window.location.pathname; }
function Full() { window.location = window.location.pathname + "?Full=1"; }
</script>

</html>

<?php

/*
if($_SESSION["User"]->PersonID == 401371457)
{
	$Rslt = PdoDataAccess::runquery(";");
	print_r(PdoDataAccess::PopException());
}
*/

?>
