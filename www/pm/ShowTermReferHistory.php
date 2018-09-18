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
  $res = $mysql->Execute("select *, g2j(ATS) as ActionDate from projectmanagement.TermsReferHistory
		    JOIN projectmanagement.persons using (PersonID) order by ATS DESC");
  $i=0;
  echo "<table width=98% align=center border=1 cellspacing=0 cellpadding=5>";
  echo "<tr class=HeaderOfTable>";
  echo "<td width=1% nowrap>ردیف</td><td>عمل انجام شده</td><td>شرح</td><td>نام منبع</td><td>صفحه / پاراگراف</td><td>عمل کننده</td><td>زمان</td>";
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
      echo "ثبت ارجاع جدید";
    else if($rec["ActionType"]=="REMOVE")
      echo "حذف ارجاع";
    else if($rec["ActionType"]=="REPLACE")
      echo "تغییر ارجاع";
    echo "</td>";
    echo "<td>";
    if($rec["ActionType"]=="INSERT" || $rec["ActionType"]=="REMOVE")
      echo $rec["TermTitle"];
    else if($rec["ActionType"]=="REPLACE")
      echo "جایگزینی ارجاع به <b>".$rec["TermTitle"]."</b> با ارجاع به <b>".$rec["ReplacedTermTitle"]."</b>";
    echo "</td>";
    echo "<td>";
    echo $rec["TermReferenceTitle"];
    echo "</td>";
    echo "<td>";
    echo "ص: ".$rec["PageNum"]." - پ: ".$rec["ParagraphNo"];
    echo "</td>";
    echo "<td>".$rec["pfname"]." ".$rec["plname"]."</td>";
    echo "<td>".$rec["ActionDate"]."</td>";
    echo "</tr>";
  }
  echo "</table>";
?>
</html>
