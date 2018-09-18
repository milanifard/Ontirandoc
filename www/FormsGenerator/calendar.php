<?php
	include("header.inc.php");

	function IsHoliday($ThisDate)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from projectmanagement.holidays where HolidayDate='".$ThisDate."'");
		if($arr_res=$res->FetchRow())
			return true;		
		return false;
	}
	
	function IsEndWeekVacation($CurDate)
	{
		$CurDate2  = mktime(0, 0, 0, substr($CurDate, 4, 2), substr($CurDate, 6, 2), substr($CurDate, 0, 4));
		if(date("l", $CurDate2)=="Friday" || date("l", $CurDate2)=="Thursday")
			return true;
		else
			return false;
	}
	
	function FarsiDayNumberInWeek($EnglishDayName)
	{
		if($EnglishDayName=="Friday")
			return 7;
		if($EnglishDayName=="Thursday")
			return 6;
		if($EnglishDayName=="Wednesday")
			return 5;
		if($EnglishDayName=="Tuesday")
			return 4;
		if($EnglishDayName=="Monday")
			return 3;
		if($EnglishDayName=="Sunday")
			return 2;
		if($EnglishDayName=="Saturday")
			return 1;
	}
	
	function GetMiladiDate($Year, $Month, $Day)
	{
		$Year = $Year-1300;
		if($Month<10 && strlen($Month)==1)
			$Month = "0".$Month;
		if($Day<10 && strlen($Day)==1)
			$Day = "0".$Day;
		return xdate($Year."/".$Month."/".$Day);
	}
	
	function GetMonthName($month)
	{
		if($month==1)
			return "فروردین";
		if($month==2)
			return "اردیبهشت";
		if($month==3)
			return "خرداد";
		if($month==4)
			return "تیر";
		if($month==5)
			return "مرداد";
		if($month==6)
			return "شهریور";
		if($month==7)
			return "مهر";
		if($month==8)
			return "آبان";
		if($month==9)
			return "آذر";
		if($month==10)
			return "دی";
		if($month==11)
			return "بهمن";
		if($month==12)
			return "اسفند";
		return "";
	}
	
	HTMLBegin();

	$now = date("Ymd"); 
	$yy = substr($now,0,4); 
	$mm = substr($now,4,2); 
	$dd = substr($now,6,2);
	list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
	if(strlen($mm)==1)
		$mm = "0".$mm;
	if(strlen($dd)==1)
		$dd = "0".$dd;
	$yy = substr($yy, 2, 2);
	$CurYear = 1300+$yy;

	$NowMiladi = GetMiladiDate($CurYear, $mm, $dd);
	
	if(isset($_REQUEST["ActiveYear"]))
	{
		$CurYear = $_REQUEST["ActiveYear"];
	}
	
	$list = "";
	$list .= "<tr>";
	for($month=1; $month<13; $month++)
	{
		$list .= "<td>";
		
		$list .= "<table width=100% border=0>";
		$list .= "<tr>";
		$list .= "<td colspan=7 align=center class=HeaderOfTable>";
		$list .= GetMonthName($month);
		$list .= "</td>";
		$list .= "</tr>";
		$RowCount = 0;
		$list .= "<tr>";
		
		$CurDateMiladi = GetMiladiDate($CurYear, $month, 1);
		$CurDate2  = mktime(0, 0, 0, substr($CurDateMiladi, 4, 2), substr($CurDateMiladi, 6, 2), substr($CurDateMiladi, 0, 4));
		$FirstDayLoc = FarsiDayNumberInWeek(date("l", $CurDate2));
		for($i=1; $i<$FirstDayLoc; $i++)
		{
			$list .= "<td width=3% align=center>&nbsp;</td>";
		}
		
		for($day=1; $day<31; $day++)
		{
			$CurDateMiladi = GetMiladiDate($CurYear, $month, $day);
			if(IsEndWeekVacation($CurDateMiladi))
			{
				if($CurDateMiladi==$NowMiladi)
					$list .= "<td width=3% align=center bgcolor=#cccc>";
				else
					$list .= "<td width=3% align=center bgcolor=#cccccc>";
				$list .= "<b><a href='javascript: SelectDay(".($CurYear-1300).", ".$month.", ".$day.");'>".$day."</b>";
			}
			else
			{
				if($CurDateMiladi==$NowMiladi)
					$list .= "<td width=3% align=center bgcolor=#cccc>";
				else
					$list .= "<td width=3% align=center>";
				$list .= "<a href='javascript: SelectDay(".($CurYear-1300).", ".$month.", ".$day.");'>";
				if(IsHoliday($CurDateMiladi))
					$list .= "<font color=red><b>".$day."</b></font>";
				else
					$list .= $day;
				$list .= "</a>";
				$list .= "</td>";
			}				
			if(($day+$FirstDayLoc-1)%7==0)
			{
				$list .= "</tr><tr>";
				$RowCount++;
			}
		}
		if($month<7)
		{
			$day = 31;
			$CurDateMiladi = GetMiladiDate($CurYear, $month, $day);
			if(IsEndWeekVacation($CurDateMiladi))
			{
				if($CurDateMiladi==$NowMiladi)
					$list .= "<td width=3% align=center bgcolor=#cccc>";
				else
					$list .= "<td width=3% align=center bgcolor=#cccccc>";
				$list .= "<b><a href='javascript: SelectDay(".($CurYear-1300).", ".$month.", ".$day.");'>".$day."</b>";
			}
			else
			{
				if($CurDateMiladi==$NowMiladi)
					$list .= "<td width=3% align=center bgcolor=#cccc>";
				else
					$list .= "<td width=3% align=center>";
				$list .= "<a href='javascript: SelectDay(".($CurYear-1300).", ".$month.", ".$day.");'>";
				if(IsHoliday($CurDateMiladi))
					$list .= "<font color=red><b>".$day."</b></font>";
				else
					$list .= $day;
				$list .= "</a>";
			}
			$list .= "</td></tr>";
		}
		else
		{
			$list .= "<td width=3% align=center>";
			$list .= "&nbsp;";
			$list .= "</td></tr>";
		}
		$list .= "</tr>";
		if($RowCount<5)
			$list .= "<tr><td colspan=7>&nbsp;</td></tr>";
		$list .= "</table>";
		$list .= "</td>";
		if($month%3==0)
		{
			$list .= "</tr>";
			$list .= "<tr>";
		}
		
	}

	$YearOptions = "";
	for($year=1386; $year<1400; $year++)
	{
		$YearOptions .= "<option value='".$year."' ";
		if($year==$CurYear)
			$YearOptions .= " selected ";
		$YearOptions .= ">".$year;
	}
?>
<form method=post name=f1 id=f1>
<p align=center>
<input type=hidden name='FormName' value='<?= $_REQUEST["FormName"] ?>'>
<input type=hidden name='InputName' value='<?= $_REQUEST["InputName"] ?>'>
<select name=ActiveYear onchange='javascript: f1.submit();'>
<?= $YearOptions ?>
</select>
</p>
<table width=98% align=center border=1 cellspacing=0>
	<?php echo $list; ?>
</table>
</form>
<script>
	function SelectDay(year, month, day)
	{
		if(day<10)
			day = '0'+day;
		if(month<10)
			month = '0'+month;
		window.opener.document.<?php echo $_REQUEST["FormName"] ?>.<?php echo $_REQUEST["InputName"] ?>.value=year+'/'+month+'/'+day;
		window.close();
	}
</script>
<?
	HTMLEnd();
?>
