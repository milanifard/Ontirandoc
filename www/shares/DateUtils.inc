<?php
	$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
	$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29); 
	function ConvertX2SDate($g_d, $g_m, $g_y) { 
   	global $g_days_in_month, $j_days_in_month; 
	   	//$div = create_function('$a, $b', 'return (int) ($a / $b);');
		$div = function($a, $b) { return (int) ($a / $b); };
		$gy = $g_y-1600; 
	   	$gm = $g_m-1; 
		$gd = $g_d-1; 
	   	$g_day_no = 365*$gy+$div($gy+3, 4)-$div($gy+99, 100)+$div($gy+399, 400); 
	   	for ($i=0; $i < $gm; ++$i) 
			$g_day_no += $g_days_in_month[$i]; 
	   	if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0))) 
			$g_day_no++; /* leap and after Feb */ 
		$g_day_no += $gd; 
	   	$j_day_no = $g_day_no-79; 
	   	$j_np = $div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */ 
		$j_day_no = $j_day_no % 12053; 
	   	$jy = 979+33*$j_np+4*$div($j_day_no, 1461); /* 1461 = 365*4 + 4/4 */ 
	   	$j_day_no %= 1461; 
		if ($j_day_no >= 366) { 
			$jy += $div($j_day_no-1, 365); 
			$j_day_no = ($j_day_no-1)%365; 
		} 
	   	for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) 
			$j_day_no -= $j_days_in_month[$i]; 
	   	$jm = $i+1; 
		$jd = $j_day_no+1; 
	   	return array($jd, $jm, $jy); 
	} 
	function ConvertS2XDate($j_d, $j_m, $j_y) { 
   	global $g_days_in_month, $j_days_in_month; 
	   	$div = create_function('$a, $b', 'return (int) ($a / $b);'); 
	   	$jy = $j_y-979; 
	   	$jm = $j_m-1; 
		$jd = $j_d-1; 
	   	$j_day_no = 365*$jy + $div($jy, 33)*8 + $div($jy%33+3, 4); 
		for ($i=0; $i < $jm; ++$i) 
			$j_day_no += $j_days_in_month[$i]; 
	   	$j_day_no += $jd; 
	   	$g_day_no = $j_day_no+79; 
	   	$gy = 1600 + 400*$div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */ 
		$g_day_no = $g_day_no % 146097; 
	   	$leap = true; 
		if ($g_day_no >= 36525) { /* 36525 = 365*100 + 100/4 */ 
			$g_day_no--; 
			$gy += 100*$div($g_day_no, 36524); /* 36524 = 365*100 + 100/4 - 100/100 */ 
			$g_day_no = $g_day_no % 36524; 
			if($g_day_no >= 365) 
				$g_day_no++; 
			else 
				$leap = false; 
		} 
	   	$gy += 4*$div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */ 
		$g_day_no %= 1461; 
	   	if ($g_day_no >= 366) { 
			$leap = false; 
			$g_day_no--; 
			$gy += $div($g_day_no, 365); 
			$g_day_no = $g_day_no % 365; 
	   	} 
		for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++) 
			$g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap); 
		$gm = $i+1; 
		$gd = $g_day_no+1; 
		return sprintf("%04d%02d%02d", $gy, $gm, $gd);
  		// return array($gy, $gm, $gd); 
	} 
	function shdate($date)
	  {
	   if($date==1000-01-01)
	     echo '';
	   else{

	   $yy=substr($date,0,4);
	   $mm=substr($date,5,2);
	   $dd=substr($date,8,2);
	   $sh = ConvertX2SDate($dd,$mm,$yy);
	   $sh[2]=substr($sh[2],2,2);
	   if ($sh[1]<10)
	      $sh[1]='0'.$sh[1];
	   if ($sh[0]<10)
	      $sh[0]='0'.$sh[0];
	   return $sh[0].'/'.$sh[1].'/'.$sh[2];
	       }
	  };
	function shdate2($date)
	  {
	   if($date==1000-01-01)
	     echo '';
	   else{

	   $yy=substr($date,0,4);
	   $mm=substr($date,5,2);
	   $dd=substr($date,8,2);
	   $sh = ConvertX2SDate($dd,$mm,$yy);
	   $sh[2]=substr($sh[2],2,2);
	   if ($sh[1]<10)
	      $sh[1]='0'.$sh[1];
	   if ($sh[0]<10)
	      $sh[0]='0'.$sh[0];
	   return $sh[0].'/'.$sh[1].'/13'.$sh[2];
	       }
	  };
	function xdate($date)
	  {
	   if($date==NULL)
	     return '1000-01-01';
	   else{
	   $yy=substr($date,0,2);
   	   $mm=substr($date,3,2);
	   $dd=substr($date,6,2);
	   $yy='13'.$yy;
	   $xdate = ConvertS2XDate($dd,$mm,$yy);
	   return $xdate;}
	  };
	/* functions  added because mysql 4.1.XX maintain time stamp field other than mysql 4.0.XX and below */
	function  Extract_And_Convert_Date_TimeStamp($ts)	  {		
		return ConvertX2SDate(substr($ts, 8, 2), substr($ts, 5, 2), substr($ts, 0, 4));		
	}
	function  Extract_Time_TimeStamp($TS) {
		$_h = substr($TS, 11, 2);
		$_m = substr($TS, 14, 2);
		$_s = substr($TS, 17, 2);
		return array($_h,$_m,$_s);		
	}
	function  Show_Time_TimeStamp($TS) {
		$_h = substr($TS, 11, 2);
		$_m = substr($TS, 14, 2);
		$_s = substr($TS, 17, 2);
		return $_h.":".$_m.":".$_s;		
	}
	function  Show_Date_TimeStamp($ts)        {
                list($d,$m,$y) = ConvertX2SDate(substr($ts, 8, 2), substr($ts, 5, 2), substr($ts, 0, 4));
                return  $d."/".$m."/".$y;
    }    	
	
/*added by elaheh bararsani  84/01/28*/
	function Month_NtoL($month)
	{
		global $l;
		switch ($month) {
    		           case 1 :
        			return($l['month_1']);
			break;
    		           case 2 :
        			return($l['month_2']);
        			break;
    		           case 3 :
        			return($l['month_3']);
        			break;
        		           case 4 :
        		                      return($l['month_4']);
			break;
		           case 5 :
        		                      return($l['month_5']);
			break;
		           case 6 :
        		                      return($l['month_6']);
			break;				
		           case 7 :
        		                      return($l['month_7']);
			break;				
		           case 8 :
        		                      return($l['month_8']);
			break;				
		           case 9 :
        		                      return($l['month_9']);
			break;
		           case 10 :
        		                      return($l['month_10']);
			break;
		           case 11 :
        		                      return($l['month_11']);
			break;
		           case 12 :
        		                      return($l['month_12']);
			break;
			        }				
	}	

/**
 * return shamsi today date. For example: 1386/03/25 
 * written by jafarkhani at 87.02
 * @return string
 */
function shNow()
{
	$now = getdate();
	$now = ConvertX2SDate($now["mday"],$now["mon"],$now["year"]);
	$yy=$now[2];
	$mm=$now[1];
	$dd=$now[0];
	if ($dd < 10)
		$dd = '0' . $dd;
	if ($mm < 10)
		$mm = '0' . $mm;
	return $yy . '/' . $mm . '/' . $dd;	
};
function shdate3($date)
	  {
	   if($date==1000-01-01)
	     echo '';
	   else{

	   $yy=substr($date,0,4);
	   $mm=substr($date,5,2);
	   $dd=substr($date,8,2);
	   $sh = ConvertX2SDate($dd,$mm,$yy);
	   $sh[2]=substr($sh[2],2,2);
	   if ($sh[1]<10)
	      $sh[1]='0'.$sh[1];
	   if ($sh[0]<10)
	      $sh[0]='0'.$sh[0];
	   return $sh[2].'/'.$sh[1].'/'.$sh[0];
	       }
	  };
?>
