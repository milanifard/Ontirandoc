<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اصطلاحات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-6
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
HTMLBegin();
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select *, g2j(ATS) as ActionDate from projectmanagement.TermsManipulationHistory
		    JOIN projectmanagement.persons using (PersonID) order by ATS DESC");
  $i=0;
  echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
  echo "<tr class=HeaderOfTable>";
  echo "<td width=1% nowrap>ردیف</td><td>عمل انجام شده</td><td>شرح</td><td>عمل کننده</td><td>زمان</td>";
  echo "</tr>";
  while($rec = $res->fetch())
  {
    $i++;
    echo "<tr>";
    echo "<td>";
    echo $i;
    echo "</td>";
    echo "<td>";
    if($rec["ActionType"]=="INSERT")
      echo "استخراج واژه جدید";
    else if($rec["ActionType"]=="REMOVE")
      echo "حذف واژه";
    else if($rec["ActionType"]=="REPLACE")
      echo "ادغام دو واژه";
    else if($rec["ActionType"]=="UPDATE")
      echo "تغییر واژه";
    echo "</td>";
    echo "<td>";
    if($rec["ActionType"]=="INSERT" || $rec["ActionType"]=="REMOVE")
      echo $rec["PreTermTitle"];
    else if($rec["ActionType"]=="UPDATE")
      echo "تغییر واژه <b>".$rec["PreTermTitle"]."</b> به <b>".$rec["NewTermTitle"]."</b>";
    else if($rec["ActionType"]=="REPLACE")
      echo "جایگزینی واژه <b>".$rec["PreTermTitle"]."</b> با ".$rec["NewTermTitle"];
      
    echo "</td>";
    echo "<td>".$rec["pfname"]." ".$rec["plname"]."</td>";
    echo "<td>".$rec["ActionDate"]."</td>";
    echo "</tr>";
  }
  echo "</table>";
?>
</html>
