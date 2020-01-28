<?php 
/*
 گزارش کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-07-04
*/
include("header.inc.php");
//include_once("../organization/classes/ChartServices.class.php");
//include("../staff/PAS/PAS_shared_utils.php");

function GetStartMonthDate($mm)
{
    $mysql = pdodb::getInstance();
    $tres = $mysql->Execute("select projectmanagement.j2g('".$_REQUEST["SelectedYear"]."', '".$mm."', '01') as gDate");
    $trec = $tres->fetch();
    $gdate = $trec["gDate"];
    return $gdate;
}

function GetHourlyPrice($gdate, $CurPersonID)
{
  $mysql = pdodb::getInstance();
  $res2 = $mysql->Execute("select HourlyPrice from projectmanagement.PersonAgreements where FromDate<='".$gdate."' and ToDate>='".$gdate."' and PersonID='".$CurPersonID."'");
  $HourlyPrice = 0;
  if($rec2 = $res2->fetch())
    $HourlyPrice = $rec2["HourlyPrice"];
  return $HourlyPrice;
}

function GetPaymentAmount($mm, $SYear, $CurPersonID)
{
  // $SYear = $_REQUEST["SelectedYear"]
    $mysql = pdodb::getInstance();
    $EndDay = 31;
    if($mm>6)
    {
	    if($mm==12)
		    $EndDay = 29;
	    else
		    $EndDay = 30;
    }
    
    $query = "	select sum(amount) pa from projectmanagement.payments
		where 
		PaymentDate between projectmanagement.j2g('".$SYear."','".$mm."','01') 
		and projectmanagement.j2g('".$SYear."','".$mm."','".$EndDay."') 
		and PersonID='".$CurPersonID."'
		group by PersonID
		";
    $res = $mysql->Execute($query);
    $amount = 0;
    if($rec = $res->fetch())	
    {
      $amount = $rec["pa"];
    }
    return $amount;
}

function GetWorkTime($mm, $SYear, $CurPersonID)
{
    $mysql = pdodb::getInstance();
    $EndDay = 31;
    if($mm>6)
    {
	    if($mm==12)
		    $EndDay = 29;
	    else
		    $EndDay = 30;
    }
    $query = "  select persons.PersonID, pfname, plname, sum(ActivityLength) as sl from projectmanagement.ProjectTaskActivities
		JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
		JOIN projectmanagement.persons on (persons.PersonID=ProjectTaskActivities.CreatorID)
		where DeleteFlag='NO' 
		and ActivityDate between projectmanagement.j2g('".$SYear."','".$mm."','01') 
		and projectmanagement.j2g('".$SYear."','".$mm."','".$EndDay."') 
		and PersonID='".$CurPersonID."'
		group by persons.PersonID, plname, pfname
		";

    $res = $mysql->Execute($query);
    if($rec = $res->fetch())
      return $rec["sl"];
    return 0;
}

function ShowTimeInHourAndMinuteOrEmpty($TotalMinutes)
{
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

function GetRemainOfPreviousYears($SelectedYear, $CurPersonID)
{
  $result = array();
  $mysql = pdodb::getInstance();
  $totalpayment = 0;
  $totalamount = 0;
  $totaltime = 0;
  for($mm=1; $mm<13; $mm++)
  {
      $tres = $mysql->Execute("select projectmanagement.j2g('".$SelectedYear."', '".$mm."', '01') as gDate");
      $trec = $tres->fetch();
      $gdate = $trec["gDate"];
  
      $EndDay = 31;
      if($mm>6)
      {
	      if($mm==12)
		      $EndDay = 29;
	      else
		      $EndDay = 30;
      }
      
      $query = " select sum(amount) pa from projectmanagement.payments
		  where 
		  PaymentDate between projectmanagement.j2g('".$SelectedYear."','".$mm."','01') 
		  and projectmanagement.j2g('".$SelectedYear."','".$mm."','".$EndDay."') 
		  and PersonID='".$CurPersonID."'
		  group by PersonID
		  ";
      $res = $mysql->Execute($query);
      $amount = 0;
      if($rec = $res->fetch())	
      {
	$amount = $rec["pa"];
      }
  
      $query = "  select persons.PersonID, pfname, plname, sum(ActivityLength) as sl from projectmanagement.ProjectTaskActivities
		  JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
		  JOIN projectmanagement.persons on (persons.PersonID=ProjectTaskActivities.CreatorID)
		  where DeleteFlag='NO' 
		  and ActivityDate between projectmanagement.j2g('".$SelectedYear."','".$mm."','01') 
		  and projectmanagement.j2g('".$SelectedYear."','".$mm."','".$EndDay."') 
		  and PersonID='".$CurPersonID."'
		  group by persons.PersonID, plname, pfname
		  ";

      $res = $mysql->Execute($query);
      if($rec = $res->fetch())
      {
	$res2 = $mysql->Execute("select HourlyPrice from projectmanagement.PersonAgreements where FromDate<='".$gdate."' and ToDate>='".$gdate."' and PersonID='".$CurPersonID."'");
	$HourlyPrice = 0;
	if($rec2 = $res2->fetch())
	  $HourlyPrice = $rec2["HourlyPrice"];
	$payment = round(($rec["sl"]*$HourlyPrice)/60, 0);
	$totalpayment += $payment;
	$totalamount += $amount;
	$totaltime += $rec["sl"];
      }
      else
	$totalamount += $amount;
    }
    $result["TotalTime"] = $totaltime;
    $result["TotalPayment"] = $totalpayment;
    $result["TotalAmount"] = $totalamount;
    return $result;
}

HTMLBegin();
$mysql = pdodb::getInstance();
$now = date("Ymd"); 
$yy = substr($now,0,4); 
$mm = substr($now,4,2); 
$dd = substr($now,6,2);
$CurrentDay = $yy."/".$mm."/".$dd;
list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
$yy = substr($yy, 2, 2);
$CurYear = 1300+$yy;
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

?>

<?php 
if(isset($_REQUEST["SYear"])) 
{ 
	$SelectedYear = $_REQUEST["SYear"];
	$SelectedMonth = $_REQUEST["SMonth"];
    $res = $mysql->Execute("select projectmanagement.j2g('".$_REQUEST["SYear"]."', '".$_REQUEST["SMonth"]."', '01') as gDate");
    $rec = $res->fetch();
    $gdate = $rec["gDate"];
    
    
?>
	<br>
	<table width=80% align=center border=1 cellspacing=0 cellpadding=3>
	<tr class=HeaderOfTable>
		<td colspan=19 align=center>
		 گزارش زمان مصرفی 
		</td>
	</tr>
	<tr bgcolor=#cccccc>
		<td width=5%>
		تاریخ
		</td>
	<td width=40%>
		پروژه
		</td>
		<td width=30%>
	کار
		</td>
		<td width=20%>
	اقدام
		</td>
		<td width=20%>
	زمان
		</td>
		
	</tr>
	<?php
	if(strlen($SelectedMonth)<2)
		$SelectedMonth = "0".$SelectedMonth;
	$EndDay = 31;
	if($SelectedMonth>6)
	{
		if($SelectedMonth==12)
			$EndDay = 29;
		else
			$EndDay = 30;
	}
	
	$query = "	select projects.title as ProjectTitle, ProjectTasks.title, ActivityDescription, g2j(ActivityDate) as gDate, ActivityLength
			from projectmanagement.ProjectTaskActivities
			JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
			LEFT JOIN projectmanagement.projects using (ProjectID)
			JOIN projectmanagement.persons on (persons.PersonID=ProjectTaskActivities.CreatorID)
			where ProjectTasks.DeleteFlag='NO' 
			and ActivityDate between projectmanagement.j2g('".$SelectedYear."','".$SelectedMonth."','01') 
			and projectmanagement.j2g('".$SelectedYear."','".$SelectedMonth."','".$EndDay."') 
			and ProjectTaskActivities.CreatorID='".$_REQUEST["PID"]."'
			";

	$res = $mysql->Execute($query);
	$total = 0;
	while($rec = $res->fetch())
	{
	    $total += $rec["ActivityLength"];
	    echo "</tr>";				
	    echo "<td nowrap>".$rec["gDate"]."</td>";
	    echo "<td>&nbsp;".$rec["ProjectTitle"]."</td>";
	    echo "<td>".$rec["title"]."</td>";
	    
	    echo "<td>&nbsp;".$rec["ActivityDescription"]."</td>";
	    echo "<td>".ShowTimeInHourAndMinuteOrEmpty($rec["ActivityLength"])."</td>";
	    echo "</tr>";			
	}
	echo "<tr bgcolor=#cccccc><td colspan=4><b>مجموع</td><td>".ShowTimeInHourAndMinuteOrEmpty($total);
	$res = $mysql->Execute("select HourlyPrice from projectmanagement.PersonAgreements where FromDate<='".$gdate."' and ToDate>='".$gdate."' and PersonID='".$_REQUEST["PID"]."'");
	$HourlyPrice = 0;
	if($rec = $res->fetch())
	{
	  $HourlyPrice = $rec["HourlyPrice"];
	}
	$payment = round(($total*$HourlyPrice)/60, 0);
	echo "<br>(".number_format($payment)." ریال)";
	echo "</td></tr>";
	?>
	</table> 
<?php die(); } ?>


<form id="SearchForm" name="SearchForm" method=post> 
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr id='SearchTr'>
<td>
<table width="100%" align="center" border="0" cellspacing="0">
<tr>
	<td>
	سال: 
 <input size=4 maxlength=4 type=text name=SelectedYear id=SelectedYear value='<?php echo $SelectedYear ?>'> 
 <input type=hidden name=SelectedMonth id=SelectedMonth value=1>
	</td>
</tr>
<tr class="HeaderOfTable">
<td align="center"><input type="submit" value="نمایش گزارش زمانی"></td>
</tr>
</table>
</td>
</tr>
</table>
<?php
if(isset($_REQUEST["SelectedYear"])) 
{ 

?>
	<br>
	<table width=100% align=center border=1 cellspacing=0 cellpadding=3>
	<tr class=HeaderOfTable>
		<td colspan=20 align=center>
		خلاصه گزارش زمان مصرفی 
		</td>
	</tr>
	<tr bgcolor=#cccccc>
		<td width=10% nowrap>
		نام و نام خانوادگی
		</td>
		<td>&nbsp;</td>
		<td>سال پیش</td>
		<td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td><td>12</td>
		<td>مبلغ کل</td>
	</tr>
	<?php
	$i = 0;
	$pres = $mysql->Execute("select * from projectmanagement.persons order by plname, pfname");
	while($prec = $pres->fetch())
	{
	    $i++;
	    if($i%2==0)
	    {
	      $bgcolor="#999999";
	    }
	    else 
	    {
	      $bgcolor="#ffffff";
	    }
	    
	    $Row2 = "<tr bgcolor='".$bgcolor."'><td>حق الزحمه </td>";
	    $Row3 = "<tr bgcolor='".$bgcolor."'><td>پرداختی </td>";
	    $Row4 = "<tr bgcolor='".$bgcolor."'><td>مانده </td>";
	    
	    echo "<tr bgcolor='".$bgcolor."'>";
	    echo "<td rowspan=4>".$prec["pfname"]." ".$prec["plname"]."</td>";
	    $CurPersonID = $prec["PersonID"];
	    
	    $PrevResult = GetRemainOfPreviousYears(($_REQUEST["SelectedYear"]-1), $CurPersonID);
	    echo "<td>ساعت کارکرد</td>";
	    echo "<td nowrap dir=ltr>";
	    echo ShowTimeInHourAndMinuteOrEmpty($PrevResult["TotalTime"])."</a>";
	    echo "</td>";
	    
	    $Row2 .= "<td dir=ltr>".number_format($PrevResult["TotalPayment"])." "."</td>";
	    if($PrevResult["TotalAmount"]>0)
	      $Row3 .= "<td  dir=ltr><font color=green> ".number_format($PrevResult["TotalAmount"])." </font></td>";
	    else 
	      $Row3 .= "<td>&nbsp;</td>";

	     $Row4 .= "<td  dir=ltr><font color=red>".number_format(abs($PrevResult["TotalPayment"]-$PrevResult["TotalAmount"]))." </font> ";
	     if(($PrevResult["TotalPayment"]-$PrevResult["TotalAmount"])<0)
	      $Row4 .= "بد";
	     else 
	      $Row4 .= "بس";
	    $Row4 .= "</td>";

	    
	    $totalpayment = 0; //$PrevResult["TotalPayment"];
	    $totalamount = 0; //$PrevResult["TotalAmount"];
	    $totaltime = 0; //$PrevResult["TotalTime"];
	    
	    for($mm=1; $mm<13; $mm++)
	    {
		$gdate = GetStartMonthDate($mm);
		
		$amount = GetPaymentAmount($mm, $_REQUEST["SelectedYear"], $CurPersonID);
		
		$sl = GetWorkTime($mm, $_REQUEST["SelectedYear"], $CurPersonID);
		if($sl>0)
		{
		  $HourlyPrice = GetHourlyPrice($gdate, $CurPersonID);
		  $payment = round(($sl*$HourlyPrice)/60, 0);
		  $totalpayment += $payment;
		  $totalamount += $amount;
		  $totaltime += $sl;
		  echo "<td nowrap  dir=ltr>";
		  echo "<a href='TimeReport.php?PID=".$rec["PersonID"]."&SYear=".$_REQUEST["SelectedYear"]."&SMonth=".$mm."'>";
		  echo ShowTimeInHourAndMinuteOrEmpty($sl)."</a>";
		  echo "</td>";
		  $Row2 .= "<td  dir=ltr>".number_format($payment)." "."</td>";
		  if($amount>0)
		    $Row3 .= "<td  dir=ltr><font color=green> ".number_format($amount)." </font></td>";
		  else 
		    $Row3 .= "<td>&nbsp;</td>";
		  $Row4 .= "<td>&nbsp;</td>";
		}
		else
		{
		  $totalamount += $amount;
		  echo "<td>&nbsp;</td>";
		  $Row2 .= "<td>&nbsp;</td>";
		  if($amount>0)
		    $Row3 .= "<td  dir=ltr><font color=green>".number_format($amount)." </font></td>";
		  else
		    $Row3 .= "<td>&nbsp;</td>";
		  $Row4 .= "<td>&nbsp;</td>";		
		}
	     }
	     echo "<td nowrap  dir=ltr> ".ShowTimeInHourAndMinuteOrEmpty($totaltime)."</td>";
	     echo "</tr>";
	     $Row2 .= "<td  dir=ltr>".number_format($totalpayment)." "."</td>";
	     $Row3 .= "<td  dir=ltr><font color=green> ".number_format($totalamount)." </font></td>";
	     $Row4 .= "<td  dir=ltr><font color=red>".number_format(abs($totalpayment-$totalamount))." </font>&nbsp;";
	     if(($totalpayment-$totalamount)<0)
	      $Row4 .= "بد";
	     else 
	      $Row4 .= "بس";
             $Row4 .= "<br>";
	     $Row4 .= "<br>در مجموع: <font color=red>".number_format(abs(($totalpayment+$PrevResult["TotalPayment"])-($totalamount+$PrevResult["TotalAmount"])))." </font>&nbsp;";
	     if((($totalpayment+$PrevResult["TotalPayment"])-($totalamount+$PrevResult["TotalAmount"]))<0)
	      $Row4 .= "بد";
	     else {
	      $Row4 .= "بس";
	     }
             
	     $Row4 .= "</td>";
	     echo $Row2."</tr>";
	     echo $Row3."</tr>";
	     echo $Row4."</tr>";
	     /*
	     echo "<br>------------------------";
	     */
	 }
	?>
	</table> 
	<?php } ?>
</html>
