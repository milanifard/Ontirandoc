<?php 
/*
 صفحه نمایش گزارش کلی پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-4-7
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/projects.class.php");
include_once("classes/projectsSecurity.class.php");
include_once("classes/ProjectTaskGroups.class.php");
require_once("classes/ProjectTaskTypes.class.php");

HTMLBegin();

// با گرفتن سال و ماه و روز تاریخ میلادی می سازد به صورت رشته ای
// سال به صورت دو رقمی
function GetMiladiDate($Year, $Month, $Day)
{
	if($Month<10 && strlen($Month)==1)
		$Month = "0".$Month;
	if($Day<10 && strlen($Day)==1)
		$Day = "0".$Day;
	$CurDateMiladi = xdate($Year."/".$Month."/".$Day);
	return substr($CurDateMiladi, 0, 4)."-".substr($CurDateMiladi, 4, 2)."-".substr($CurDateMiladi, 6, 2); 
}

function GetCreatedTasksCount($SelectedYear, $SelectedMonth, $ProjectID, $TaskGroupID)
{
	$mysql = pdodb::getInstance();
	$NextMonth = $SelectedMonth+1;
	$NextYear = $SelectedYear;
	if($NextMonth>12)
	{
		$NextYear++;
		$NextMonth=1;
	}
	$StartTime = GetMiladiDate($SelectedYear, $SelectedMonth, 1);
	$EndTime = GetMiladiDate($NextYear, $NextMonth, 1);
	$query = "select count(*) as TotalCount from projectmanagement.ProjectTasks  
			where ProjectID=? and CreateDate>='".$StartTime." 00:00:00' and CreateDate<'".$EndTime." 00:00:00' and TaskGroupID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($ProjectID, $TaskGroupID));
	$rec = $res->fetch();
	return $rec["TotalCount"];
}

function GetDoneTasksCount($SelectedYear, $SelectedMonth, $ProjectID, $TaskGroupID)
{
	$mysql = pdodb::getInstance();
	$NextMonth = $SelectedMonth+1;
	$NextYear = $SelectedYear;
	if($NextMonth>12)
	{
		$NextYear++;
		$NextMonth=1;
	}
	$StartTime = GetMiladiDate($SelectedYear, $SelectedMonth, 1);
	$EndTime = GetMiladiDate($NextYear, $NextMonth, 1);
	$query = "select count(*) as TotalCount from projectmanagement.ProjectTasks  
			where ProjectID=? and DoneDate>='".$StartTime." 00:00:00' and DoneDate<'".$EndTime." 00:00:00' and TaskGroupID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($ProjectID, $TaskGroupID));
	$rec = $res->fetch();
	return $rec["TotalCount"];
}

function GetUsedTime($SelectedYear, $SelectedMonth, $ProjectID, $TaskGroupID)
{
	$mysql = pdodb::getInstance();
	$NextMonth = $SelectedMonth+1;
	$NextYear = $SelectedYear;
	if($NextMonth>12)
	{
		$NextYear++;
		$NextMonth=1;
	}
	$StartTime = GetMiladiDate($SelectedYear, $SelectedMonth, 1);
	$EndTime = GetMiladiDate($NextYear, $NextMonth, 1);
	$query = "select sum(ActivityLength) as TotalCount from projectmanagement.ProjectTaskActivities 
					JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
			where ProjectID=? and ActivityDate>='".$StartTime." 00:00:00' and ActivityDate<'".$EndTime." 00:00:00' and TaskGroupID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($ProjectID, $TaskGroupID));
	$rec = $res->fetch();
	return $rec["TotalCount"];
}

function GetCreatedTasksCount_ByType($SelectedYear, $SelectedMonth, $ProjectID, $ProjectTaskTypeID)
{
	$mysql = pdodb::getInstance();
	$NextMonth = $SelectedMonth+1;
	$NextYear = $SelectedYear;
	if($NextMonth>12)
	{
		$NextYear++;
		$NextMonth=1;
	}
	$StartTime = GetMiladiDate($SelectedYear, $SelectedMonth, 1);
	$EndTime = GetMiladiDate($NextYear, $NextMonth, 1);
	$query = "select count(*) as TotalCount from projectmanagement.ProjectTasks  
			where ProjectID=? and CreateDate>='".$StartTime." 00:00:00' and CreateDate<'".$EndTime." 00:00:00' and ProjectTaskTypeID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($ProjectID, $ProjectTaskTypeID));
	$rec = $res->fetch();
	return $rec["TotalCount"];
}

function GetUsedTime_ByType($SelectedYear, $SelectedMonth, $ProjectID, $ProjectTaskTypeID)
{
	$mysql = pdodb::getInstance();
	$NextMonth = $SelectedMonth+1;
	$NextYear = $SelectedYear;
	if($NextMonth>12)
	{
		$NextYear++;
		$NextMonth=1;
	}
	$StartTime = GetMiladiDate($SelectedYear, $SelectedMonth, 1);
	$EndTime = GetMiladiDate($NextYear, $NextMonth, 1);
	$query = "select sum(ActivityLength) as TotalCount from projectmanagement.ProjectTaskActivities 
					JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
			where ProjectID=? and ActivityDate>='".$StartTime." 00:00:00' and ActivityDate<'".$EndTime." 00:00:00' and ProjectTaskTypeID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($ProjectID, $ProjectTaskTypeID));
	$rec = $res->fetch();
	return $rec["TotalCount"];
}

function ShowReportTable($ReportType, $SelectedYear, $ProjectID)
{
	echo "<table width=98% align=center border=1 cellspacing=0 cellpadding=3>";
	echo "<tr bgcolor=#cccccc><td colspan=14 align=center>";
	if($ReportType==1)
		echo "تعداد کارهای ایجاد شده بر اساس گروه های کاری";
	else if($ReportType==2)
		echo "تعداد کارهای انجام شده بر اساس گروه های کاری";
	else if($ReportType==3)
		echo "زمان مصرفی بر اساس گروه های کاری";
	echo "</td></tr>";
	echo "<tr class=HeaderOfTable>";
	echo "<td>گروه کار</td>";
	$mt = array();
	for($m=1; $m<13; $m++)
	{
		echo "<td align=center width=6%>ماه ".$m."</td>";
		$mt[$m] = 0;
	}
	$mt[13] = 0;
	echo "<td>مجموع</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>-</td>";
	$total = 0;
	for($SelectedMonth=1; $SelectedMonth<13; $SelectedMonth++)
	{
		if($ReportType==1)
			$n = GetCreatedTasksCount($SelectedYear, $SelectedMonth, $ProjectID, 0);
		else if($ReportType==2)
			$n = GetDoneTasksCount($SelectedYear, $SelectedMonth, $ProjectID, 0);
		else if($ReportType==3)
			$n = GetUsedTime($SelectedYear, $SelectedMonth, $ProjectID, 0);
		
		if($ReportType==3)
			echo "<td>".floor($n/60).":".($n%60)."</td>";
		else	
			echo "<td>".$n."</td>";
		$total += $n;
	}
	if($ReportType==3)
		echo "<td>".floor($total/60).":".($total%60)."</td>";
	else
		echo "<td>".$total."</td>";
	echo "</tr>";
	$mt[13] += $total;
	
	$GroupList = manage_ProjectTaskGroups::GetList($_REQUEST["ProjectID"]);
	for($i=0; $i<count($GroupList); $i++)
	{
		$total = 0;
		echo "<tr>";
		echo "<td>".$GroupList[$i]->TaskGroupName."</td>";
		for($SelectedMonth=1; $SelectedMonth<13; $SelectedMonth++)
		{
			if($ReportType==1)
				$n = GetCreatedTasksCount($SelectedYear, $SelectedMonth, $ProjectID, $GroupList[$i]->ProjectTaskGroupID);
			else if($ReportType==2)
				$n = GetDoneTasksCount($SelectedYear, $SelectedMonth, $ProjectID, $GroupList[$i]->ProjectTaskGroupID);
			else if($ReportType==3)
				$n = GetUsedTime($SelectedYear, $SelectedMonth, $ProjectID, $GroupList[$i]->ProjectTaskGroupID);
		
			if($ReportType==3)
				echo "<td>".floor($n/60).":".($n%60)."</td>";
			else	
				echo "<td>".$n."</td>";
			$total += $n;
			$mt[$SelectedMonth] += $n;
		}
		if($ReportType==3)
			echo "<td>".floor($total/60).":".($total%60)."</td>";
		else
			echo "<td>".$total."</td>";
		echo "</tr>";
		$mt[13] += $total;
	}
	echo "<tr class=FooterOfTable>";
	echo "<td>مجموع</td>";
	for($SelectedMonth=1; $SelectedMonth<=13; $SelectedMonth++)
	{
		if($ReportType==3)
			echo "<td>".floor($mt[$SelectedMonth]/60).":".($mt[$SelectedMonth]%60)."</td>";
		else
			echo "<td>".$mt[$SelectedMonth]."</td>";
	}
	echo "</tr>";
	echo "</table>";
}

$ProjectID = $_REQUEST["ProjectID"];
echo manage_projects::ShowSummary($ProjectID);
if(isset($_REQUEST["TabPage"]))
{
	$TabPage = $_REQUEST["TabPage"];
}
else
{
	$TabPage = "1";
}
?>
<table width=90% border=1 cellspacing=0 cellpadding=5 align=center>
	<tr>
		<td <?php if($TabPage=="1") echo "bgcolor=#cccccc" ?> >
		<a href='ShowProjectOverview.php?ProjectID=<?php echo $ProjectID?>&TabPage=1' >نمودار تعداد کارها</a>
		</td>
		<td <?php if($TabPage=="2") echo "bgcolor=#cccccc" ?> >
		<a href='ShowProjectOverview.php?ProjectID=<?php echo $ProjectID?>&TabPage=2' >گانت چارت</a>
		</td>
	</tr>
</table>
<br>
<?php 
if($TabPage=="1") 
{
	$now = date("Ymd");
	$yy = substr($now,0,4); 
	$mm = substr($now,4,2); 
	$dd = substr($now,6,2);
	list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
	$SelectedYear = substr($yy, 2, 2);
	if(isset($_REQUEST["CurYear"]))
		$SelectedYear = $_REQUEST["CurYear"];
	echo "<p align=center>";
	echo "نمودار تعداد کارهای درخواستی (قرمز) و تعداد کارهای انجام گرفته (سبز) بر اساس ماه های سال";
	echo " <select name=CurYear onchange='javascript: document.location=\"ShowProjectOverview.php?ProjectID=".$_REQUEST["ProjectID"]."&TabPage=1&CurYear=\"+this.value'>";
	for($y=87; $y<=95; $y++)
	{
		echo "<option value='".$y."' ";
		if($y==$SelectedYear)
			echo " selected ";
		echo ">".$y;
	}
	echo "</select>";
	echo "</p>";
	echo "<p align=center><img src='BarChart.php?ProjectID=".$ProjectID."&CurYear=".$SelectedYear."'></p>";
	echo "<br>";
	ShowReportTable(1, $SelectedYear, $ProjectID);
	echo "<br>";
	ShowReportTable(2, $SelectedYear, $ProjectID);
	echo "<br>";
	ShowReportTable(3, $SelectedYear, $ProjectID);
	echo "<br>";
	$TypeList = manage_ProjectTaskTypes::GetList($ProjectID);
	$GeneralTypeList = manage_ProjectTaskTypes::GetList(0);
	echo "<table width=98% align=center border=1 cellspacing=0 cellpadding=3>";
	echo "<tr bgcolor=#cccccc><td colspan=".(count($GeneralTypeList)+count($TypeList)+2)." align=center>تعداد کارها بر اساس نوع کار و زمان مصرفی</td></tr>";
	echo "<tr class=HeaderOfTable>";
	echo "<td>ماه</td>";
	for($i=0; $i<count($GeneralTypeList);$i++)
		echo "<td>".$GeneralTypeList[$i]->title."</td>";
	for($i=0; $i<count($TypeList);$i++)
		echo "<td>".$TypeList[$i]->title."</td>";
	echo "<td><b>مجموع</b></td>";
	echo "</tr>";
	$tt = array();
	for($i=0; $i<count($GeneralTypeList)+count($TypeList)+1;$i++)
		$tt[$i] = 0;
	for($SelectedMonth=1; $SelectedMonth<13; $SelectedMonth++)
	{
		echo "<tr>";
		$total = 0;
		echo "<td>".$SelectedMonth."</td>";
		for($i=0; $i<count($GeneralTypeList);$i++)
		{
			$n = GetCreatedTasksCount_ByType($SelectedYear, $SelectedMonth, $ProjectID, $GeneralTypeList[$i]->ProjectTaskTypeID);
			echo "<td>".$n."</td>";
			$total += $n;
			$tt[$i] += $n;
		}
		for($i=0; $i<count($TypeList);$i++)
		{
			$n = GetCreatedTasksCount_ByType($SelectedYear, $SelectedMonth, $ProjectID, $TypeList[$i]->ProjectTaskTypeID);
			echo "<td>".$n."</td>";
			$total += $n;
			$tt[$i+count($GeneralTypeList)] += $n;
		}
		$tt[count($TypeList)+count($GeneralTypeList)] += $total;
		echo "<td>".$total."</td>";
		echo "</tr>";
	}
	echo "<tr class=FooterOfTable>";
	echo "<td>مجموع</td>";
	for($i=0; $i<count($GeneralTypeList)+count($TypeList)+1;$i++)
	{
		echo "<td>".$tt[$i]."</td>";
	}
	echo "</tr>";	
	echo"</table>";
} else if($TabPage=="2") { ?>
<br>
<p align=center>
<img src='GantChart.php?ProjectID=<?php echo $ProjectID ?>'>
</p>
<?php  
	$today = date("Y-m-d");
	$enddate = strtotime('+40 day', strtotime ($today));
	$enddate = date ( 'Y-m-j' , $enddate );
	$StartYear = substr($today, 0, 4);
	$StartMonth = substr($today, 5, 2);
	$StartDay = substr($today, 8, 2);
	$StartDate = $StartYear."-".$StartMonth."-".$StartDay." 00:00:00";
	
	$EndYear = substr($enddate, 0, 4);
	$EndMonth = substr($enddate, 5, 2);
	$EndDay = substr($enddate, 8, 2);
	$EndDate = $EndYear."-".$EndMonth."-".$EndDay." 23:59:00";
	
	$mysql = pdodb::getInstance();
	$query = "select * from projectmanagement.ProjectTasks where ProjectID=? and 
				(
				EstimatedStartTime between '".$StartDate."' and '".$EndDate."' 
				or RealStartTime between '".$StartDate."' and '".$EndDate."'
				or (EstimatedStartTime<>'0000-00-00 00:00:00' and TaskStatus<>'DONE' and TaskStatus<>'REPLAYED') 
				)";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($ProjectID));
	$TasksList = array();
	echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5><tr class=HeaderOfTable><td width=1% nowrap>کد کار</td><td>عنوان</td></tr>";
	while($rec = $res->fetch())
	{
		echo "<tr>";
		echo "<td><a href='NewProjectTasks.php?UpdateID=".$rec["ProjectTaskID"]."'>".$rec["ProjectTaskID"]."</a></td>";
		echo "<td>".$rec["title"]."</td>";
		echo "</tr>";
	}
	echo "</table>";
} 
else if($TabPage=="3") 
{
?>
<table width=90% align=center border=1 cellspacing=0>
<tr bgcolor=#ccccc>
	<td colspan=4 ><b>اعضای پروژه</b></td>
</tr>
<tr class="HeaderOfTable">
	<td width=30% nowrap>نام و نام خانوادگی</td><td width=10% nowrap>کارهای جاری</td><td width=10% nowrap>کارهای اقدام شده</td><td width=10% nowrap>زمان صرف شده</td>
</tr>
<?php
	$mysql = pdodb::getInstance();
	$query = "select pfname, plname, PersonID,
			(select count(*) from projectmanagement.ProjectTasks 
							JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) 
							where ProjectID=ProjectMembers.ProjectID and ProjectTaskAssignedUsers.PersonID=ProjectMembers.PersonID and TaskStatus='NOT_START'
			) as CurrentTasks
			,
			(select count(*) from projectmanagement.ProjectTasks 
							JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) 
							where ProjectID=ProjectMembers.ProjectID and ProjectTaskAssignedUsers.PersonID=ProjectMembers.PersonID and (TaskStatus='DONE' or TaskStatus='REPLAYED')
			) as DoneTasks,
			(select sum(ActivityLength) from projectmanagement.ProjectTasks 
			JOIN projectmanagement.ProjectTaskActivities using (ProjectTaskID)
			where ProjectID=ProjectMembers.ProjectID and ProjectTaskActivities.CreatorID=ProjectMembers.PersonID 
			) as SpentTime
 			from projectmanagement.ProjectMembers 
			JOIN hrmstotal.persons using (PersonID) 
			where ProjectID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($ProjectID));
	$k = 0;
	while($rec = $res->fetch())
	{
		$k++;
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td>";
		echo $rec["pfname"]." ".$rec["plname"];
		echo "</td>";
		echo "<td>";
		echo $rec["CurrentTasks"];
		echo "</td>";
		echo "<td>";
		echo $rec["DoneTasks"];
		echo "</td>";
		echo "<td>";
		if($rec["SpentTime"]!="")
			echo floor($rec["SpentTime"]/60).":".$rec["SpentTime"]%60;
		echo "</td>";
		echo "</tr>";   
	}
?>
</tr>
</table>
<?php } ?>

</body>
</html>
