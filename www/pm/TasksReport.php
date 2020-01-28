<?php 
/*
 گزارش کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-07-04
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
	$mysql = pdodb::getInstance();
	$query = " select  person_type,PersonID 
				   from ease.persons where personid = ". $_SESSION['PersonID'] ; 

	$mysql->Prepare($query);
	$pres = $mysql->ExecuteStatement(array());
	$prec=$pres->fetch();
	
	if( ($prec['person_type'] == 200 ) and $prec['PersonID']!='705178' )
	{
		echo "<br><br><div style='color:black;font-family:tahoma;font-size:20px;font-weight:bold' align=center>" . 
				" همکار محترم،".
				"دسترسی به این صفحه امکان پذیر نمی باشد."
						."</div>";

				 die();
	}


function ShowTimeInHourAndMinuteOrEmpty($TotalSec)
{
	$TotalMinutes = (int)($TotalSec/60);
	$h = (int)($TotalMinutes/60);
	$m = $TotalMinutes%60;
	if($h<10)
		$h = "0".$h;
	if($m<10)
		$m = "0".$m;
	if($h.":".$m!="00:00")
		return $h.":".$m;
	else
		return "-";
}


HTMLBegin();
$mysql = pdodb::getInstance();
$Childs = array();
// لیست افراد زیر مجموعه در چارت مدیریت وظیفه
$CurPersonID = $_SESSION["PersonID"];
$Childs = ChartServices::GetAllChildsOfPerson(14, $CurPersonID);
/*if($CurPersonID == 401371457)
	$Childs = ChartServices::GetAllChildsOfPerson(14, 100614);*/

$now = date("Ymd");
$yy = substr($now,0,4);
$mm = substr($now,4,2);
$dd = substr($now,6,2);
$CurrentDay = $yy."/".$mm."/".$dd;
list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
$yy = substr($yy, 2, 2);
$CurYear = 1300+$yy;
$mm--;
if($mm==0)
	$mm="1";
if(isset($_REQUEST["SelectedYear"]))
{
	$SelectedYear = $_REQUEST["SelectedYear"];
	$SelectedMonth = $_REQUEST["SelectedMonth"];
}
else
{
	$SelectedYear = $CurYear;
	$SelectedMonth = $mm;
}
$PersonListOptions = "";
for($i=0; $i<count($Childs); $i++)
{
	$PersonListOptions .= "<option value='".$Childs[$i]->PersonID."'>".$Childs[$i]->PersonName;
}
?>
<form id="SearchForm" name="SearchForm" method=post> 
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr id='SearchTr'>
<td>
<table width="100%" align="center" border="0" cellspacing="0">
<tr>
	<td>
 سال: <input size=4 maxlength=4 type=text name=SelectedYear id=SelectedYear value='<?php echo $SelectedYear ?>'> ماه: <input size=2 maxlength=2 type=text name=SelectedMonth id=SelectedMonth value='<?php echo $SelectedMonth ?>'> 
	</td>
</tr>
<tr>
	<td colspan=2>
	<input type=checkbox name=ShowPAS id=ShowPAS <?php if(isset($_REQUEST["ShowPAS"])) echo "checked"; ?> > نمایش اطلاعات زمان حضور و غیبت افراد
	<br>
	توجه: 
	انتخاب این گزینه باعث کند شدن زمان اجرای گزارش خواهد شد
	</td>
</tr>
<tr class="HeaderOfTable">
<td align="center"><input type="submit" value="نمایش گزارش کار"></td>
</tr>
</table>
</td>
</tr>
</table>
<? 
if(isset($_REQUEST["SelectedYear"])) 
{ 
	?>
	<br>
	<table width=98% align=center border=1 cellspacing=0 cellpadding=3>
	<tr class=HeaderOfTable>
		<td colspan=19 align=center>
		خلاصه گزارش کار شما و افراد زیر مجموعه در چارت سازمانی مدیریت کار
		</td>
	</tr>
	<tr bgcolor=#cccccc>
		<td rowspan=3 width=40%>
		نام و نام خانوادگی
		</td>
		<td align=center colspan=9 width=20%>
		کارها
		</td>
		<?php  if(isset($_REQUEST["ShowPAS"])) { ?>
		<td align=center colspan=6 width=20%>
		حضور و غیاب
		</td>
		<?php } ?>
		<td align=center colspan=4 width=20%>
		تماسهای تلفنی
		</td>
	</tr>
	<tr bgcolor=#cccccc>
		<td rowspan=2 width=10%>
		ایجاد شده <br>
		(در این ماه)
		</td>
		<td rowspan=2 width=10%>
		منتسب شده <br>
		(در این ماه)
		</td>
		<td rowspan=2 width=10%>
		انجام شده<br>
		(در این ماه)
		</td>
		<td rowspan=2 width=10%>
		باقیمانده<br>
		(در کل) 
		</td>
		<td rowspan=2 width=10%>
		زمان مصرفی
		</td>
		<td rowspan=2 width=10%>
		تعداد پروژه
		</td>
		<td rowspan=2 width=10%>
		ارباب رجوع
		</td>
		<td colspan=2 width=10%>
		تعداد تغییرات
		</td>
		<?php  if(isset($_REQUEST["ShowPAS"])) { ?>
		<td rowspan=2 width=10%>
		حضور
		</td>
		<td rowspan=2 width=10%>
		اضافه کار
		</td>
		<td rowspan=2 width=10%>
		غیبت
		</td>
		<td colspan=3 width=10%>
		مرخصی
		</td>
		<?php } ?>
		<td colspan=2 width=10%>
		 ورودی
		</td>
		<td colspan=2 width=10%>
		 خروجی
		</td>
	</tr>
	<tr bgcolor=#cccccc>
		<td width=10%>صفحات</td>
		<td width=10%>جداول</td>
		<?php  if(isset($_REQUEST["ShowPAS"])) { ?>
		<td width=10%>ساعتی</td>
		<td width=10%>روزانه</td>
		<td width=10%>استحقاقی</td>
		<?php  } ?>
		<td width=10%>تعداد</td>
		<td width=10%>مدت</td>
		<td width=10%>تعداد</td>
		<td width=10%>مدت</td>
	</tr>
	<?php
	if(strlen($SelectedMonth)<2)
		$SelectedMonth = "0".$SelectedMonth;
	$StartOfMonth = PASUtils::GetMiladiDate($SelectedYear, $SelectedMonth, "01");
	//$StartOfMonth = PASUtils::GetMiladiDate($SelectedYear, "01", "01");
	$EndDay = 31;
	if($SelectedMonth>6)
	{
		if($SelectedMonth==12)
			$EndDay = 29;
		else
			$EndDay = 30;
	}
	$EndOfMonth = PASUtils::GetMiladiDate($SelectedYear, $SelectedMonth, $EndDay);
	//$EndOfMonth = PASUtils::GetMiladiDate($SelectedYear, "10", "15");
	
	$StartOfMonth = substr($StartOfMonth, 0, 4)."-".substr($StartOfMonth, 4, 2)."-".substr($StartOfMonth, 6, 2)." 00:00:00";
	$EndOfMonth = substr($EndOfMonth, 0, 4)."-".substr($EndOfMonth, 4, 2)."-".substr($EndOfMonth, 6, 2)." 23:59:00";

	$LastID = count($Childs);
	$Childs[$LastID] = new PersonStruct();
	$Childs[$LastID]->PersonID = $_SESSION["PersonID"];
	$res = $mysql->Execute("select concat(pfname, ' ' , plname) as FullName from hrmstotal.persons where PersonID=".$_SESSION["PersonID"]);
	$rec = $res->fetch();
	$Childs[$LastID]->PersonName = $rec["FullName"];
	for($i=0; $i<count($Childs); $i++)
	{
		echo "<tr>";
		echo "<td>".$Childs[$i]->PersonName."</td>";
		$query = "	select * from projectmanagement.ProjectTaskActivities
					JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) 
					where DeleteFlag='NO' 
					and PersonID=".$Childs[$i]->PersonID." 
					and DoneDate between '".$StartOfMonth."' and '".$EndOfMonth."' 
					and (TaskStatus='DONE' or TaskStatus='REPLYED')
					and (ChangedTables<>'' or ChangedPages<>'') 
					";
		$res = $mysql->Execute($query);
//echo $query;
		$NumberOfPages = 0;
		$NumberOfTables = 0;
		while($rec = $res->fetch())
		{
			$ret = explode(" ", $rec["ChangedPages"]);
			$NumberOfPages += count($ret);
			$ret = explode(" ", $rec["ChangedTables"]);
			$NumberOfTables += count($ret);
		}
		
		$query = "	select 'CREATED' as StatusName, count(*) as TotalCount  from projectmanagement.ProjectTasks 
					where DeleteFlag='NO' and CreatorID=".$Childs[$i]->PersonID." and CreateDate between '".$StartOfMonth."' and '".$EndOfMonth."' 
					union
					select 'CREATE' as StatusName, count(*) as TotalCount  from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) 
					where DeleteFlag='NO' and PersonID=".$Childs[$i]->PersonID." and CreateDate between '".$StartOfMonth."' and '".$EndOfMonth."' and (TaskStatus='NOT_START')
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
					union
					select 'CLIENTS' as StatusName, count(distinct ProjectTasks.CreatorID) as TotalCount  from projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) 
					where DeleteFlag='NO' and PersonID=".$Childs[$i]->PersonID." and CreateDate between '".$StartOfMonth."' and '".$EndOfMonth."' 
					";
		
		$res = $mysql->Execute($query);
		$rec = $res->fetch();
		$Created = $rec["TotalCount"];
		echo "<td><a href='TaskReportDetails.php?SelectedYear=".$SelectedYear."&SelectedMonth=".$SelectedMonth."&SelectedPersonID=".$Childs[$i]->PersonID."&DetailType=CreatedTasks' target=_blank>".$Created."</a></td>"; 
		$rec = $res->fetch();
		$Assigned = $rec["TotalCount"];
		echo "<td><a href='TaskReportDetails.php?SelectedYear=".$SelectedYear."&SelectedMonth=".$SelectedMonth."&SelectedPersonID=".$Childs[$i]->PersonID."&DetailType=AssignedTasks' target=_blank>".$Assigned."</a></td>"; 
		$rec = $res->fetch();
		$Done = $rec["TotalCount"];
		echo "<td><a href='TaskReportDetails.php?SelectedYear=".$SelectedYear."&SelectedMonth=".$SelectedMonth."&SelectedPersonID=".$Childs[$i]->PersonID."&DetailType=DoneTasks' target=_blank>".$Done."</td>";
		$rec = $res->fetch();
		$Remain = $rec["TotalCount"];
		echo "<td><a href='TaskReportDetails.php?SelectedYear=".$SelectedYear."&SelectedMonth=".$SelectedMonth."&SelectedPersonID=".$Childs[$i]->PersonID."&DetailType=RemainTasks' target=_blank>".$Remain."</td>";
		$rec = $res->fetch();
		echo "<td>&nbsp;".PASUtils::ShowTimeInHourAndMinuteOrEmpty($rec["TotalCount"])."</td>";
		$rec = $res->fetch();
		echo "<td><a href='TaskReportDetails.php?SelectedYear=".$SelectedYear."&SelectedMonth=".$SelectedMonth."&SelectedPersonID=".$Childs[$i]->PersonID."&DetailType=ProjectsCount' target=_blank>".$rec["TotalCount"]."</td>";
		$rec = $res->fetch();
		echo "<td><a href='TaskReportDetails.php?SelectedYear=".$SelectedYear."&SelectedMonth=".$SelectedMonth."&SelectedPersonID=".$Childs[$i]->PersonID."&DetailType=ClientsCount' target=_blank>".$rec["TotalCount"]."</td>";
		
		
		echo "<td><a href='TaskReportDetails.php?SelectedYear=".$SelectedYear."&SelectedMonth=".$SelectedMonth."&SelectedPersonID=".$Childs[$i]->PersonID."&DetailType=UpdatePages' target=_blank>".$NumberOfPages."</td>";
		echo "<td><a href='TaskReportDetails.php?SelectedYear=".$SelectedYear."&SelectedMonth=".$SelectedMonth."&SelectedPersonID=".$Childs[$i]->PersonID."&DetailType=UpdatePages' target=_blank>".$NumberOfTables."</td>";
		
		
		if(isset($_REQUEST["ShowPAS"])) 
		{
			$query = "select * from pas.MonthlyCalculationSummary where CalculatedYear=? and CalculatedMonth=? and PersonID=".$Childs[$i]->PersonID;
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array($SelectedYear, $SelectedMonth)); 
			if($rec = $res->fetch())
			{
				$ret["Haste"] = $rec["HasteTime"];
				$ret["Tardiness"] = $rec["TardinessTime"];
				$ret["Absent"] = $rec["AbsentTime"];
				$ret["ExtraWorkTime"] = $rec["ExtraWorkTime"];
				$ret["LeaveTime"] = $rec["LeaveTime"];
				$ret["Mission"] = $rec["MissionTime"];
				$ret["PresentTime"] = $rec["PresentTime"];
				$ret["MissionDays"] = $rec["MissionDays"];
				$ret["DailyOfficialLeaves"] = $rec["DailyOfficialLeaves"];
				$ret["DailyCureLeaves"] = $rec["DailyCureLeaves"];
			}
			else
			{
				$res2 = $mysql->Execute("select * from pas.PersonSettings JOIN pas.ValidTardiness on (UnitID=WorkUnitCode) where PersonID=".$Childs[$i]->PersonID);
				$rec2 = $res2->fetch();
				$ret = PASUtils::CalculateAPersonSummaryStatusInAMonth($Childs[$i]->PersonID, $SelectedYear, $SelectedMonth, $rec2["ValidFloatMinutes"], $rec2["ValidTardinessMinutes"], $rec2["ValidHasteMinutes"]);
				// ذخیره نتایج در خلاصه محاسبات ذخیره شده
				// قبل از ذخیره کردن یکبار دیگر چک می کند که داده در دیتابیس درج نشده باشد
				$mysql->Prepare("select * from pas.MonthlyCalculationSummary where CalculatedYear=? and CalculatedMonth=? and PersonID='".$Childs[$i]->PersonID."'");
				$tmp = $mysql->ExecuteStatement(array($SelectedYear, $SelectedMonth));
				if(!($t_rec = $tmp->fetch()))
				{
					$query = "insert into pas.MonthlyCalculationSummary (CalculatedYear, CalculatedMonth, PersonID, PresentTime, WorkTime, TardinessTime, HasteTime, AbsentTime, ExtraWorkTime, LeaveTime, MissionTime, MissionDays, DailyOfficialLeaves, DailyCureLeaves) values (";
					$query .= "?, ?, '".$Childs[$i]->PersonID."', ";
					$query .= "'".$ret["PresentTime"]."', ";
					$query .= "'".$ret["PresentTime"]."', ";
					$query .= "'".$ret["Tardiness"]."', ";
					$query .= "'".$ret["Haste"]."', ";
					$query .= "'".$ret["Absent"]."', ";
					$query .= "'".$ret["ExtraWorkTime"]."', ";
					$query .= "'".$ret["LeaveTime"]."', ";
					$query .= "'".$ret["Mission"]."', ";
					$query .= "'".$ret["MissionDays"]."', ";
					$query .= "'".$ret["DailyOfficialLeaves"]."', ";
					$query .= "'".$ret["DailyCureLeaves"]."') ";
					$mysql->Prepare($query);
					$mysql->ExecuteStatement(array($SelectedYear, $SelectedMonth));
				}
			}
			
			echo "<td>".PASUtils::ShowTimeInHourAndMinuteOrEmpty($ret["PresentTime"])."</td>";
			echo "<td>".PASUtils::ShowTimeInHourAndMinuteOrEmpty($ret["ExtraWorkTime"])."</td>";
			echo "<td>".PASUtils::ShowTimeInHourAndMinuteOrEmpty($ret["Absent"]+$ret["Haste"]+$ret["Tardiness"])."</td>";
			echo "<td>".PASUtils::ShowTimeInHourAndMinuteOrEmpty($ret["LeaveTime"])."</td>";
			echo "<td>&nbsp;".$ret["DailyOfficialLeaves"]."</td>";
			echo "<td>&nbsp;".$ret["DailyCureLeaves"]."</td>";
		}
		
		$cres = $mysql->Execute("select * from voipdb.InternalPhones where PersonID=".$Childs[$i]->PersonID);
		if($crec = $cres->fetch())
		{
			$PhoneNo = $crec["internalphone"];
			//echo "select count(*) as TotalCount, sum(CallDuration) as TotalDuration from voipdb.CallsInfo where CallFrom='".$PhoneNo."' and CallDate between '".$StartOfMonth."' and '".$EndOfMonth."'";
			//echo "<br>";
			$tres = $mysql->Execute("select count(*) as TotalCount, sum(CallDuration) as TotalDuration from voipdb.CallsInfo where CallFrom='".$PhoneNo."' and CallDate between '".$StartOfMonth."' and '".$EndOfMonth."'");
			$trec = $tres->fetch();
			echo "<td>".$trec["TotalCount"]."</td>";
			echo "<td>".ShowTimeInHourAndMinuteOrEmpty($trec["TotalDuration"])."</td>";

			$tres = $mysql->Execute("select count(*) as TotalCount, sum(CallDuration) as TotalDuration from voipdb.CallsInfo where CallTo='".$PhoneNo."' and CallDate between '".$StartOfMonth."' and '".$EndOfMonth."'");
			$trec = $tres->fetch();
			echo "<td>".$trec["TotalCount"]."</td>";
			echo "<td>".ShowTimeInHourAndMinuteOrEmpty($trec["TotalDuration"])."</td>";
		}
		else
		{
			echo "<td>-</td>";
			echo "<td>-</td>";
			echo "<td>-</td>";
			echo "<td>-</td>";
		}
		echo "</tr>";
	}
	?>
	</table> 
	<?php 
}

?>
</html>
