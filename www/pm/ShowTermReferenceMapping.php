<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : ارتباط اصطلاحات و مراجع
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-7
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/terms.class.php");
include("classes/TermReferenceMapping.class.php");
$mysql = pdodb::getInstance();

HTMLBegin();
$obj = new be_terms();
$obj->LoadDataFromDatabase($_REQUEST["TermID"]);
$mysql->Prepare("select TermReferences.TermReferenceID, TermReferenceMappingID, title, TermReferenceContent.content, TermReferenceMapping.PageNum, ParagraphNo, SentenceNo from projectmanagement.TermReferenceMapping 
JOIN projectmanagement.TermReferences using (TermReferenceID)
LEFT JOIN projectmanagement.TermReferenceContent on (TermReferenceContent.TermReferenceID=TermReferences.TermReferenceID and TermReferenceContent.PageNum=TermReferenceMapping.PageNum)
where TermID=? Order by TermReferenceMappingID DESC");
$res = $mysql->ExecuteStatement(array($_REQUEST["TermID"]));
echo "<form method=post>";
echo "<input type=hidden name=TermID id=TermID value='".$_REQUEST["TermID"]."'>";
echo "<table width=90% cellspacing=0 cellpadding=5 border=1>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%>&nbsp;</td><td width=1%>&nbsp;</td><td width=20%>منبع</td><td width=1%>صفحه</td><td width=1%>پاراگراف</td><td>محتوا</td></tr>";
echo "</tr>";
while($rec = $res->fetch())
{
  if(isset($_REQUEST["ch_".$rec["TermReferenceMappingID"]]))
  {
    manage_TermReferenceMapping::Remove($rec["TermReferenceMappingID"]);
  }
  else 
  {
    echo "<tr>";
    echo "<td>";
    echo "<input type=checkbox name=ch_".$rec["TermReferenceMappingID"]." id=ch_".$rec["TermReferenceMappingID"].">";
    echo "</td>";
    echo "<td>";
    echo "<a target=_blank href='ManageTermReferenceMapping.php?UpdateID=".$rec["TermReferenceMappingID"]."&TermReferenceID=".$rec["TermReferenceID"]."'><img src='images/edit.gif' border=0></a>";
    echo "</td>";
    echo "<td>";
    echo $rec["title"];
    echo "</td>";
    echo "<td>";
    echo $rec["PageNum"];
    echo "</td>";
    echo "<td>";
    echo $rec["ParagraphNo"];
    echo "</td>";
    echo "<td>";
    echo str_replace($obj->TermTitle, "<font color=red>".$obj->TermTitle."</font>", str_replace("\n", "<br>", $rec["content"]));
    echo "</td>";
    echo "</tr>";
  }
}
echo "<tr class=HeaderOfTable>";
echo "<td align=center colspan=5><input type=submit value='حذف'>";
echo " &nbsp; ";
echo "<input type=button value='بازگشت' onclick='javascript: history.back()'>";
echo "</td>";
echo "</tr>";
echo "</table>";
?>
</html>
