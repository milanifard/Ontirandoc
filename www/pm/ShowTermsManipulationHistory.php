<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اصطلاحات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-6
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
HTMLBegin();
$mysql = pdodb::getInstance();
$res = $mysql->Execute("select *, g2j(ATS) as ActionDate from projectmanagement.TermsManipulationHistory
		    JOIN projectmanagement.persons using (PersonID) order by ATS DESC");
$i=0;
echo "<br><br><div class=\"row\">
        <div class=\"col-1\"></div>
        <div class=\"col-10\">";
echo "<table class=\"table table-bordered table-sm table-striped\">";
echo "<thead class=\"table-info\">";
echo "<td width='1%' nowrap=''>".C_ROW."</td>
        <td class='text-center'>".C_COMPLETED_TASK."</td>
        <td class='text-center'>".C_DESCRIPTION."</td>
        <td class='text-center'>".C_SUBJECT."</td>
        <td class='text-center'>".C_TIME."</td>";
echo "</thead>";
while($rec = $res->fetch())
{
    $i++;
    echo "<tr>";
    echo "<td class='text-center'>";
    echo $i;
    echo "</td>";
    echo "<td class='text-center'>";
    if($rec["ActionType"]=="INSERT")
        echo C_EXTRACT_NEW_WORD;
    else if($rec["ActionType"]=="REMOVE")
        echo C_REMOVE_WORD;
    else if($rec["ActionType"]=="REPLACE")
        echo C_MERGE_TWO_WORDS;
    else if($rec["ActionType"]=="UPDATE")
        echo C_CHANGE_WORD;
    echo "</td>";
    echo "<td class='text-center'>";
    if($rec["ActionType"]=="INSERT" || $rec["ActionType"]=="REMOVE")
        echo $rec["PreTermTitle"];
    else if($rec["ActionType"]=="UPDATE")
        echo C_CHANGE_WORD." <b>".$rec["PreTermTitle"]."</b> ".C_TO_USER." <b>".$rec["NewTermTitle"]."</b>";
    else if($rec["ActionType"]=="REPLACE")
        echo C_REPLACE_WORD." <b>".$rec["PreTermTitle"]."</b> ".C_BY." ".$rec["NewTermTitle"]."</b>";

    echo "</td>";
    echo "<td class='text-center'>".$rec["pfname"]." ".$rec["plname"]."</td>";
    echo "<td class='text-center'>".$rec["ActionDate"]."</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div><div class='col-1'></div>";
?>
</html>
