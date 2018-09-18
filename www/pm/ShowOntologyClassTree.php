<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-29
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyClasses.class.php");
include("classes/OntologyClassLabels.class.php");
include("classes/OntologyClassHirarchy.class.php");
include ("classes/ontologies.class.php");
HTMLBegin();

function ShowChilds($LevelNo, $ParentID)
{
  $mysql = pdodb::getInstance();
  $LevelNo++;
  if($LevelNo>6)
    return;
  $indent = "";
  for($i=0; $i<$LevelNo*5; $i++)  
    $indent .= "&nbsp;";
  $query = "select OntologyClasses.OntologyClassID, ClassTitle, 
    (select group_concat(label, ' ') from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID group by OntologyClassID) as ClassLabel
    from projectmanagement.OntologyClasses 
    JOIN projectmanagement.OntologyClassHirarchy on (OntologyClassHirarchy.OntologyClassParentID=OntologyClasses.OntologyClassID)
    where OntologyClassHirarchy.OntologyClassID=?";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($ParentID));
  while($rec = $res->fetch())
  {
    echo $indent;
    if(!isset($_REQUEST["OnlyView"]))
    {
	    echo "<a href='#' onclick=\"javascript: SelectItem('";
	    if(isset($_REQUEST["ReturnID"]))
	      echo $rec["OntologyClassID"];
	    else 
	      echo $rec["ClassTitle"];
	    echo "')\">";
	    echo "<img src='images/chain.gif' border=0></a>".$rec["ClassTitle"]." ";
    }
    echo $rec["ClassLabel"]."<br>";
    ShowChilds($LevelNo, $rec["OntologyClassID"]);
  }
}
if(!isset($_REQUEST["OnlyView"]))
	echo "<table dir=ltr border=1 cellpadding=5 align=center><tr><td>";
else
	echo "<table dir=rtl border=1 cellpadding=5 align=center><tr><td>";
$OntologyID = $_REQUEST["OntologyID"];
$mysql = pdodb::getInstance();
$query = "select OntologyClasses.OntologyClassID, ClassTitle, 
  (select group_concat(label, ' ') from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID group by OntologyClassID) as ClassLabel
  from projectmanagement.OntologyClasses 
  where OntologyID=? and 
  OntologyClassID not in (select OntologyClassParentID from projectmanagement.OntologyClassHirarchy) ";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($OntologyID));
while($rec = $res->fetch())
{
  if(!isset($_REQUEST["OnlyView"]))
  {
	  echo "<a href='#' onclick=\"javascript: SelectItem('";
	  if(isset($_REQUEST["ReturnID"]))
	    echo $rec["OntologyClassID"];
	  else 
	    echo $rec["ClassTitle"];
	  echo "')\">";
	  echo "<img src='images/chain.gif' border=0></a>".$rec["ClassTitle"]." ";
  }
  echo $rec["ClassLabel"]."<br>";
  ShowChilds(1, $rec["OntologyClassID"]);
}
echo "</td></tr></table>";
?>
<script>
  <? if(isset($_REQUEST["ReturnID"])) { ?>
  function SelectItem(ClassID)
  {
    var obj = window.opener.document.getElementById('<? echo $_REQUEST["InputName"] ?>');
    if(obj==null)
      return;
    obj.value = ClassID;
    window.close();
  }
  <? } else { ?>
  function SelectItem(ClassTitle)
  {
    var obj = window.opener.document.getElementById('<? echo $_REQUEST["InputName"] ?>');
    if(obj==null)
      return;
    if(obj.value!='')
      obj.value += ', ';
    obj.value += ClassTitle;
    window.close();
  }
  <? } ?>
</script>
</html>
