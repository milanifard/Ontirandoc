<?php 
/*
 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-6
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
HTMLBegin();
$mysql = pdodb::getInstance();
$res = $mysql->Execute("select distinct TermReferenceID, PageNum, ParagraphNo from projectmanagement.TermReferenceMapping where TermReferenceID=1 ");
$frequency = $docs = $terms = array();
$i = 0;
while($rec = $res->fetch())
{
  $docs[$i]["PageNum"] = $rec["PageNum"];
  $docs[$i]["TermReferenceID"] = $rec["TermReferenceID"];
  $docs[$i]["ParagraphNo"] = $rec["ParagraphNo"];
  $i++;
}
$res = $mysql->Execute("select distinct TermID, TermTitle from projectmanagement.terms 
JOIN projectmanagement.TermReferenceMapping using (TermID) 
where TermReferenceID=1");
$i = 0;
while($rec = $res->fetch())
{
  $terms[$i]["TermID"] = $rec["TermID"];
  $terms[$i]["TermTitle"] = $rec["TermTitle"];
  $i++;
}
$res = $mysql->Execute("select TermID, TermReferenceID, PageNum, ParagraphNo from projectmanagement.TermReferenceMapping where TermReferenceID=1");
$i = 0;
while($rec = $res->fetch())
{
  $DocID = -1;
  for($k=0; $k<count($docs); $k++)
  {
    if($docs[$k]["TermReferenceID"] == $rec["TermReferenceID"] && $docs[$k]["PageNum"] == $rec["PageNum"] && $docs[$k]["ParagraphNo"] == $rec["ParagraphNo"])
    {
      $DocID = $k;
      break;
    }
  }
  if($DocID>0)
  {
    $frequency[$DocID][$rec["TermID"]] = 1; // binary matrix (if there is a term in a discourse we have 1 otherwise 0/null)
  }
}

// TermsCoOccure
$mysql->Execute("delete from projectmanagement.TermsCoOccure");
$TermsCount = count($terms);
//$TermsCount = 10;
for($i=0; $i<$TermsCount; $i++)
{
  for($j=$i+1; $j<$TermsCount; $j++)
  {
    $s = 0;
    for($DocID=0; $DocID<count($docs); $DocID++)
    {
      $term1 = $terms[$i]["TermID"];
      $term2 = $terms[$j]["TermID"];
      if(isset($frequency[$DocID][$term1]) && isset($frequency[$DocID][$term2]))
	$s++;
    }
    if($s>0)
    {
      $mysql->Execute("insert into projectmanagement.TermsCoOccure (TermID1, TermID2, frequency) values ('".$term1."','".$term2."','".$s."')");
      echo "'".$terms[$i]["TermTitle"]."','".$terms[$j]["TermTitle"]."','".$s."'<br>";
    }
  }
}

/*
for($i=0; $i<count($docs); $i++)
{
  echo "<br>";$docs[$i]["TermReferenceID"].":".$docs[$i]["PageNum"].":".$docs[$i]["ParagraphNo"]." : ";
  for($j=0; $j<count($terms); $j++)
  {
    if(isset($frequency[$i][$terms[$j]["TermID"]]))
      echo $frequency[$i][$terms[$j]["TermID"]].",";
    else {
        echo "0,";
    }
  }
}
*/
?>
</html>
