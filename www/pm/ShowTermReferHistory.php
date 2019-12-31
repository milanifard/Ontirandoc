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
  echo "<td width=1% nowrap>".C_ROW."</td><td>".C_COMPLETED_TASK."</td><td>".C_DESCRIPTION."</td><td>".C_SOURCE_NAME."</td><td>".C_PAGE." / ".C_PARAGRAPH."</td><td>".C_SUBJECT."</td><td>".C_TIME."</td>";
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
      echo C_SUBMIT_NEW_REFERENCE;
    else if($rec["ActionType"]=="REMOVE")
      echo C_REMOVE_REFERENCE;
    else if($rec["ActionType"]=="REPLACE")
      echo C_CHANGE_REFERENCE;
    echo "</td>";
    echo "<td>";
    if($rec["ActionType"]=="INSERT" || $rec["ActionType"]=="REMOVE")
      echo $rec["TermTitle"];
    else if($rec["ActionType"]=="REPLACE")
      echo C_REPLACE_REFERENCE_WITH." <b>".$rec["TermTitle"]."</b> ".C_WITH_REFERENCE_TO." <b>".$rec["ReplacedTermTitle"]."</b>";
    echo "</td>";
    echo "<td>";
    echo $rec["TermReferenceTitle"];
    echo "</td>";
    echo "<td>";
    echo C_S.": ".$rec["PageNum"]." - ".C_P.": ".$rec["ParagraphNo"];
    echo "</td>";
    echo "<td>".$rec["pfname"]." ".$rec["plname"]."</td>";
    echo "<td>".$rec["ActionDate"]."</td>";
    echo "</tr>";
  }
  echo "</table>";
?>
</html>
