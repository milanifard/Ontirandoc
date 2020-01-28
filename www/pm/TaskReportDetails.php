<?php 
/*
 ریز گزارش عملکرد
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-07-05
*/
include("header.inc.php");
include_once("../organization/classes/ChartServices.class.php");
include("../staff/PAS/PAS_shared_utils.php");
if(config::$critical_status!=10){
echo "<br><br>
		<div style='color:red;font-family:tahoma;font-size:14px;font-weight:bold' align=center>" . 
						                                 ".به علت بار زیاد سرور این قسمت غیرفعال شده است" . 
		"</div>";
die();
}

HTMLBegin();
$mysql = pdodb::getInstance();
$CurPersonID = $_SESSION["PersonID"];
if($CurPersonID=="201309")
	$CurPersonID = 200037;
$Childs = ChartServices::GetAllChildsOfPerson(1, $CurPersonID);
if(!isset($_REQUEST["SelectedPersonID"]))
{
	echo "ERROR";
	die();
}
if(!is_numeric($_REQUEST["SelectedPersonID"]))
{
	echo "PARAMETER ERROR";
	die();
}
$SelectedPersonID = $_REQUEST["SelectedPersonID"];
$sw = false;
if($SelectedPersonID==$_SESSION["PersonID"])
{
	$res = $mysql->Execute("select * from hrmstotal.persons where PersonID=".$SelectedPersonID);
	$rec = $res->fetch();
	$CurPersonName = $rec["pfname"]." ".$rec["plname"];
	$sw = true;
}
for($i=0; $i<count($Childs); $i++)
{
	if($Childs[$i]->PersonID==$SelectedPersonID)
	{
		$CurPersonName = $Childs[$i]->PersonName;
		$sw = true;
	}
}
if(!$sw)
{
	echo "ACCESS DENIED";
	die();
}
$SelectedYear = $_REQUEST["SelectedYear"];
$SelectedMonth = $_REQUEST["SelectedMonth"];
if(strlen($SelectedMonth)<2)
	$SelectedMonth = "0".$SelectedMonth;
$StartOfMonth = PASUtils::GetMiladiDate($SelectedYear, $SelectedMonth, "01");
$EndDay = 31;
if($SelectedMonth>6)
{
	if($SelectedMonth==12)
		$EndDay = 29;
	else
		$EndDay = 30;
}
$EndOfMonth = PASUtils::GetMiladiDate($SelectedYear, $SelectedMonth, $EndDay);
$StartOfMonth = substr($StartOfMonth, 0, 4)."-".substr($StartOfMonth, 4, 2)."-".substr($StartOfMonth, 6, 2)." 00:00:00";
$EndOfMonth = substr($EndOfMonth, 0, 4)."-".substr($EndOfMonth, 4, 2)."-".substr($EndOfMonth, 6, 2)." 23:59:00";

if(!is_numeric($SelectedYear) || !is_numeric($SelectedMonth))
{
	echo "PARAMETER ERROR";
	die();
}
	/*
					union
					select 'DONE' as StatusName, count(*) as TotalCount from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) 
					where DeleteFlag='NO' and PersonID=".$Childs[$i]->PersonID." and DoneDate between '".$StartOfMonth."' and '".$EndOfMonth."' and (TaskStatus='DONE' or TaskStatus='REPLYED')
					union
					select 'REMAIN' as StatusName, count(*) as TotalCount from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) 
					where DeleteFlag='NO' and PersonID=".$Childs[$i]->PersonID." and (TaskStatus='NOT_START' or TaskStatus='PROGRESSING')
					union
					select 'TIME' as StatusName, sum(ActivityLength) as TotalCount  from projectmanagement.ProjectTaskActivities
					JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) 
					where DeleteFlag='NO' 
					and PersonID=".$Childs[$i]->PersonID." 
					and DoneDate between '".$StartOfMonth."' and '".$EndOfMonth."' 
					and (TaskStatus='DONE' or TaskStatus='REPLYED')
					union
					select 'SYSTEM' as StatusName, count(distinct ProjectID) as TotalCount from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) 
					where DeleteFlag='NO' and PersonID=".$Childs[$i]->PersonID." and DoneDate between '".$StartOfMonth."' and '".$EndOfMonth."' and (TaskStatus='DONE' or TaskStatus='REPLYED')
	 */

$DetailType = $_REQUEST["DetailType"];
if($DetailType=="CreatedTasks")
{
		echo "<table width=98% align=center border=1 cellspacing=0>";
		echo "<tr bgcolor=#cccccc>";
		echo "<td colspan=5 align=center>کارهای ایجاد شده در سال $SelectedYear ماه $SelectedMonth توسط $CurPersonName</td>";
		echo "</tr>";
		echo "<tr class=HeaderOfTable>";
		echo "<td width=1%>ردیف</td><td>پروژه</td><td>عنوان</td><td width=20%>مجری</td><td width=10%>زمان ایجاد</td>";
		echo "</tr>";
		$query = "select ProjectTasks.*, 
					projects.title as ProjectTitle,
					persons.pfname, persons.plname,
					g2j(ProjectTasks.CreateDate) as gCreateDate
					from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
					LEFT JOIN projectmanagement.projects using (ProjectID)
					LEFT JOIN hrmstotal.persons on (ProjectTaskAssignedUsers.PersonID=persons.PersonID)
					where ProjectTasks.DeleteFlag='NO' and ProjectTasks.CreatorID=".$SelectedPersonID." and CreateDate between '".$StartOfMonth."' and '".$EndOfMonth."'  
					";
		//echo $query;
		$res = $mysql->Execute($query);
		$row = 0;
		while($rec = $res->fetch())
		{
			$row++;
			echo "<tr>";
			echo "<td>".$row."</td>";
			echo "<td>";
			echo $rec["ProjectTitle"];
			echo "</td>";
			echo "<td>";
			echo $rec["title"];
			echo "</td>";
			echo "<td>";
			echo $rec["pfname"]." ".$rec["plname"];
			echo "</td>";
			echo "<td>";
			echo $rec["gCreateDate"];
			echo "</td>";
			echo "</tr>";
		}
}
else if($DetailType=="AssignedTasks")
{
		echo "<table width=98% align=center border=1 cellspacing=0>";
		echo "<tr bgcolor=#cccccc>";
		echo "<td colspan=5 align=center>کارهای منتسب شده در سال $SelectedYear ماه $SelectedMonth به $CurPersonName</td>";
		echo "</tr>";
		echo "<tr class=HeaderOfTable>";
		echo "<td width=1%>ردیف</td><td>پروژه</td><td>عنوان</td><td width=20%>ایجاد کننده</td><td width=10%>زمان ایجاد</td>";
		echo "</tr>";
		$query = "select ProjectTasks.*, 
					projects.title as ProjectTitle,
					persons.pfname, persons.plname,
					g2j(ProjectTasks.CreateDate) as gCreateDate
					from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
					LEFT JOIN projectmanagement.projects using (ProjectID)
					LEFT JOIN hrmstotal.persons on (ProjectTasks.CreatorID=persons.PersonID)
					where ProjectTasks.DeleteFlag='NO' and ProjectTaskAssignedUsers.PersonID=".$SelectedPersonID." and CreateDate between '".$StartOfMonth."' and '".$EndOfMonth."' and (TaskStatus='NOT_START')
					";
		//echo $query;
		$res = $mysql->Execute($query);
		$row = 0;
		while($rec = $res->fetch())
		{
			$row++;
			echo "<tr>";
			echo "<td>".$row."</td>";
			echo "<td>";
			echo $rec["ProjectTitle"];
			echo "</td>";
			echo "<td>";
			echo $rec["title"];
			echo "</td>";
			echo "<td>";
			echo $rec["pfname"]." ".$rec["plname"];
			echo "</td>";
			echo "<td>";
			echo $rec["gCreateDate"];
			echo "</td>";
			echo "</tr>";
		}
}
else if($DetailType=="DoneTasks")
{
		echo "<table width=98% align=center border=1 cellspacing=0>";
		echo "<tr bgcolor=#cccccc>";
		echo "<td colspan=7 align=center>کارهای انجام شده در سال $SelectedYear ماه $SelectedMonth به $CurPersonName</td>";
		echo "</tr>";
		echo "<tr class=HeaderOfTable>";
		echo "<td width=1%>ردیف</td><td>پروژه</td><td>عنوان</td><td width=20%>ایجاد کننده</td><td width=10%>زمان ایجاد</td><td>زمان انجام</td><td>زمان مصرفی</td>";
		echo "</tr>";
		$query = "select ProjectTasks.*, 
					projects.title as ProjectTitle,
					persons.pfname, persons.plname,
					g2j(ProjectTasks.CreateDate) as gCreateDate,
					g2j(ProjectTasks.DoneDate) as gDoneDate,
					(select sum(ActivityLength) from projectmanagement.ProjectTaskActivities where ProjectTaskID=ProjectTasks.ProjectTaskID) as TotalTime 
					from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
					LEFT JOIN projectmanagement.projects using (ProjectID)
					LEFT JOIN hrmstotal.persons on (ProjectTasks.CreatorID=persons.PersonID)
					where ProjectTasks.DeleteFlag='NO' and ProjectTaskAssignedUsers.PersonID=".$SelectedPersonID." 
					and DoneDate between '".$StartOfMonth."' and '".$EndOfMonth."' 
					and (TaskStatus='DONE' or TaskStatus='REPLYED')
					order by DoneDate DESC
					";
		//echo $query;
		$res = $mysql->Execute($query);
		$row = 0;
		while($rec = $res->fetch())
		{
			$row++;
			echo "<tr>";
			echo "<td>".$row."</td>";
			echo "<td>";
			echo $rec["ProjectTitle"];
			echo "</td>";
			echo "<td>";
			echo $rec["title"];
			echo "</td>";
			echo "<td>";
			echo $rec["pfname"]." ".$rec["plname"];
			echo "</td>";
			echo "<td>";
			echo $rec["gCreateDate"];
			echo "</td>";
			echo "<td>";
			echo $rec["gDoneDate"];
			echo "</td>";
			echo "<td>&nbsp;";
			echo PASUtils::ShowTimeInHourAndMinuteOrEmpty($rec["TotalTime"]);
			echo "</td>";
			echo "</tr>";
		}
}
else if($DetailType=="ProjectsCount")
{
		echo "<table width=60% align=center border=1 cellspacing=0>";
		echo "<tr bgcolor=#cccccc>";
		echo "<td colspan=5 align=center>پروژه هایی که  $CurPersonName در سال $SelectedYear و ماه $SelectedMonth روی آنها کار کرده است</td>";
		echo "</tr>";
		echo "<tr class=HeaderOfTable>";
		echo "<td width=1%>ردیف</td><td>پروژه</td><td>تعداد کار</td>";
		echo "</tr>";
		$query = "select projects.title as ProjectTitle, count(*) as TotalCount
					from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
					LEFT JOIN projectmanagement.projects using (ProjectID)
					LEFT JOIN hrmstotal.persons on (ProjectTasks.CreatorID=persons.PersonID)
 					where ProjectTasks.DeleteFlag='NO' and ProjectTaskAssignedUsers.PersonID=".$SelectedPersonID." 
					and DoneDate between '".$StartOfMonth."' and '".$EndOfMonth."' 
					and (TaskStatus='DONE' or TaskStatus='REPLYED')
					group by projects.title
					";
		//echo $query;
		$res = $mysql->Execute($query);
		$row = 0;
		while($rec = $res->fetch())
		{
			$row++;
			echo "<tr>";
			echo "<td>".$row."</td>";
			echo "<td>";
			echo $rec["ProjectTitle"];
			echo "</td>";
			echo "<td>";
			echo $rec["TotalCount"];
			echo "</td>";
			echo "</tr>";
		}
}
else if($DetailType=="RemainTasks")
{
		echo "<table width=98% align=center border=1 cellspacing=0>";
		echo "<tr bgcolor=#cccccc>";
		echo "<td colspan=5 align=center>کارهای باقیمانده  $CurPersonName</td>";
		echo "</tr>";
		echo "<tr class=HeaderOfTable>";
		echo "<td width=1%>ردیف</td><td>پروژه</td><td>عنوان</td><td width=20%>ایجاد کننده</td><td width=10%>زمان ایجاد</td>";
		echo "</tr>";
		$query = "select ProjectTasks.*, 
					projects.title as ProjectTitle,
					persons.pfname, persons.plname,
					g2j(ProjectTasks.CreateDate) as gCreateDate
					from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
					LEFT JOIN projectmanagement.projects using (ProjectID)
					LEFT JOIN hrmstotal.persons on (ProjectTasks.CreatorID=persons.PersonID)
					where ProjectTasks.DeleteFlag='NO' and ProjectTaskAssignedUsers.PersonID=".$SelectedPersonID." and (TaskStatus='NOT_START' or TaskStatus='PROGRESSING')
					order by CreateDate DESC
					";
		//echo $query;
		$res = $mysql->Execute($query);
		$row = 0;
		while($rec = $res->fetch())
		{
			$row++;
			echo "<tr>";
			echo "<td>".$row."</td>";
			echo "<td>";
			echo $rec["ProjectTitle"];
			echo "</td>";
			echo "<td>";
			echo $rec["title"];
			echo "</td>";
			echo "<td>";
			echo $rec["pfname"]." ".$rec["plname"];
			echo "</td>";
			echo "<td>";
			echo $rec["gCreateDate"];
			echo "</td>";
			echo "</tr>";
		}
}
else if($DetailType=="UpdatePages")
{
		echo "<table width=98% align=center border=1 cellspacing=0>";
		echo "<tr bgcolor=#cccccc>";
		echo "<td colspan=6 align=center>صفحات ایجاد شده و یا تغییر یافته توسط  $CurPersonName  در سال $SelectedYear و ماه $SelectedMonth</td>";
		echo "</tr>";
		echo "<tr class=HeaderOfTable>";
		echo "<td width=1%>ردیف</td><td>پروژه</td><td>عنوان</td><td width=10%>زمان انجام</td><td>صفحات</td><td>جداول</td>";
		echo "</tr>";
		$query = "select ProjectTasks.*, 
					projects.title as ProjectTitle,
					persons.pfname, persons.plname,
					g2j(ProjectTasks.DoneDate) as gDoneDate,
					ChangedPages,
					ChangedTables
					from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
					JOIN projectmanagement.ProjectTaskActivities using (ProjectTaskID) 
					LEFT JOIN projectmanagement.projects using (ProjectID)
					LEFT JOIN hrmstotal.persons on (ProjectTasks.CreatorID=persons.PersonID)
					where ProjectTasks.DeleteFlag='NO' 
					and ProjectTaskAssignedUsers.PersonID=".$SelectedPersonID." 
					and DoneDate between '".$StartOfMonth."' and '".$EndOfMonth."' 
					and (TaskStatus='DONE' or TaskStatus='REPLYED')
					and (ChangedTables<>'' or ChangedPages<>'')
					order by DoneDate DESC
					";
		//echo $query;
		$res = $mysql->Execute($query);
		$row = 0;
		while($rec = $res->fetch())
		{
			$row++;
			echo "<tr>";
			echo "<td>".$row."</td>";
			echo "<td nowrap>";
			echo $rec["ProjectTitle"];
			echo "</td>";
			echo "<td>";
			echo $rec["title"];
			echo "</td>";
			echo "<td>";
			echo $rec["gDoneDate"];
			echo "</td>";
			echo "<td>";
			echo $rec["ChangedPages"];
			echo "</td>";
			echo "<td>";
			echo $rec["ChangedTables"];
			echo "</td>";
			echo "</tr>";
		}
}
else if($DetailType=="ClientsCount")
{
		echo "<table width=50% align=center border=1 cellspacing=0>";
		echo "<tr bgcolor=#cccccc>";
		echo "<td colspan=5 align=center>ارباب رجوعها در $SelectedYear ماه $SelectedMonth مربوطه به $CurPersonName</td>";
		echo "</tr>";
		echo "<tr class=HeaderOfTable>";
		echo "<td width=1%>ردیف</td><td>ارباب رجوع</td><td>تعداد درخواست</td>";
		echo "</tr>";
		$query = "select persons.pfname, persons.plname, count(*) as TotalCount
					from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
					JOIN hrmstotal.persons on (ProjectTasks.CreatorID=persons.PersonID)
					where ProjectTasks.DeleteFlag='NO' and ProjectTaskAssignedUsers.PersonID=".$SelectedPersonID." and CreateDate between '".$StartOfMonth."' and '".$EndOfMonth."' 
					group by persons.pfname, persons.plname
					";
		//echo $query;
		$res = $mysql->Execute($query);
		$row = 0;
		while($rec = $res->fetch())
		{
			$row++;
			echo "<tr>";
			echo "<td>".$row."</td>";
			echo "<td>";
			echo $rec["pfname"]." ".$rec["plname"];
			echo "</td>";
			echo "<td>";
			echo $rec["TotalCount"];
			echo "</td>";
			echo "</tr>";
		}
}

?>
</body>
</html>
