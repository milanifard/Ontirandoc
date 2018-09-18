<?php 
/*
 صفحه نمایش پیامهای سیستمی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTasks.class.php");
include("classes/projects.class.php");

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
	echo "<table width=98% align=center border=1 cellspacing=0>";
	echo "<tr><td colspan=4><b>آخرین عملیات انجام شده روی کارهای مرتبط با شما توسط دیگر کاربران<b></td></tr>";
	echo "<tr class=HeaderOfTable><td width=1% nowrap>زمان</td><td>عملیات انجام شده</td><td width=7% nowrap>کاربر مربوطه</td><td>عنوان کار مربوطه</td></tr>";
	echo $messages;
	echo "</table>";
}


if (!isset($_GET["Full"]))
	$messages = manage_ProjectTasks::GetLastSystemMessage($_SESSION["PersonID"], FALSE);
else
	$messages = manage_ProjectTasks::GetLastSystemMessage($_SESSION["PersonID"], FALSE, TRUE);
if($messages!="")
{
	echo "<table width=98% align=center border=1 cellspacing=0>";
	echo "<tr><td colspan=4><b>آخرین عملیات انجام شده روی کارهای مرتبط با شما توسط خودتان<b></td></tr>";
	echo "<tr class=HeaderOfTable><td width=1% nowrap>زمان</td><td>عملیات انجام شده</td><td>عنوان کار مربوطه</td></tr>";
	echo $messages;
	echo "</table>";
}

echo '<p style="margin-right: 15px;">';
if (!isset($_GET["Full"]))
	echo '<input onclick="javascript: Full();" value="مشاهده‌ی جزئیات بیشتر" type="button">';
else
	echo '<input onclick="javascript: Partial();" value="مشاهده‌ی خلاصه‌تر" type="button">';
echo "</p>";

?>

<br /><br /><br />

<script>
function Partial() { window.location = window.location.pathname; }
function Full() { window.location = window.location.pathname + "?Full=1"; }
</script>

</html>

<?php

// Set page's last visit time for the user ...
$A = pdodb::getInstance();
$query = "insert into UserPageLastVisits (UserID, PageID, LastVisit) values (?, 1, now()) on duplicate key update LastVisit = now();";
$A->Prepare($query);
$Rslt = $A->ExecuteStatement([$_SESSION["UserID"]]);

/*
if($_SESSION["User"]->PersonID == 401371457)
{
	$Rslt = PdoDataAccess::runquery(";");
	print_r(PdoDataAccess::PopException());
}
*/

?>
