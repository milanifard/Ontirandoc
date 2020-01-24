<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-29
*/
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "on");

include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ontologies.class.php");
HTMLBegin();
echo "<div class=\"container\">";
$mysql = pdodb::getInstance();

if(isset($_REQUEST["label"]))
{

  $query = "select OntologyClassLabelID, ontologies.OntologyID, OntologyClasses.OntologyClassID, label, OntologyTitle, ClassTitle 
  from projectmanagement.OntologyClassLabels
  JOIN projectmanagement.OntologyClasses on (OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID)
  JOIN projectmanagement.ontologies on (ontologies.OntologyID=OntologyClasses.OntologyID)
  where label=?";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($_REQUEST["label"]));
  echo "<div class='table-responsive text-center'> <table class=\"table table-striped\">";
  while($rec = $res->fetch())
  {
    echo "<tr>";
    echo "<td><br>".$rec["OntologyTitle"]."</td>";
    $EntityName = $rec["ClassTitle"];
    echo "<td>".C_Class."</td>";
    echo "<td><a target=_blank href='ManageOntologyClassLabels.php?UpdateID=".$rec["OntologyClassLabelID"]."&OntologyClassID=".$rec["OntologyClassID"]."'>".$rec["ClassTitle"]."</a></td>";
    echo "<td>".$rec["label"]."</td>";
    echo "</tr>";
  }

  $query = "select OntologyPropertyLabelID, ontologies.OntologyID, OntologyProperties.OntologyPropertyID, label, OntologyTitle, PropertyTitle 
  from projectmanagement.OntologyPropertyLabels 
  JOIN projectmanagement.OntologyProperties on (OntologyPropertyLabels.OntologyPropertyID=OntologyProperties.OntologyPropertyID)
  JOIN projectmanagement.ontologies on (ontologies.OntologyID=OntologyProperties.OntologyID)
  where label=?";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($_REQUEST["label"]));
  while($rec = $res->fetch())
  {
    echo "<tr>";
    echo "<td>".$rec["OntologyTitle"]."</td>";
    $EntityName = $rec["PropertyTitle"];
    echo "<td>".C_Property."</td>";
    echo "<td><A target=_blank href='ManageOntologyPropertyLabels.php?UpdateID=".$rec["OntologyPropertyLabelID"]."&OntologyPropertyID=".$rec["OntologyPropertyID"]."'>".$rec["PropertyTitle"]."</a></td>";
    echo "<td>".$rec["label"]."</td>";
    echo "</tr>";
  }
  if(isset($_REQUEST["EntityName"]))
  {
    $EntityName = $_REQUEST["EntityName"];
  }
  $LabelName = "";
  if(isset($_REQUEST["LabelName"]))
  {
    $LabelName = $_REQUEST["LabelName"];
  }
  echo "<tr><td colspan =4 class='text-center'><input type=button class =' btn btn-danger' value='".CLOSE_N."' onclick='window.close()'></td></tr>";
  echo "</table></div>";
} 
?>
<form class="form-horizontal" method=post name=f1 id=f1>
    <div class="container">
        <div class="row border border-light shadow-sm" style="margin-top: 3% !important;">
            <div class="col-12">
                <div class="row">
<input type=hidden class="form-control " name="label" id="label" value="<? echo $_REQUEST["label"] ?>"></div>
                <div class="row">
<input type=text class="form-control text-center" name='EntityName' id='EntityName' value='<? echo $EntityName ?>' size=100>
                </div> <div class="row">
<? echo"<input type=submit class='form-control' value='".C_SinCnnP."'>"; ?>
                </div> </div>
        </div>
    </div>
</form>
<?
  if(isset($_REQUEST["EntityName"]))
  {
    echo "<div class=\"table-responsive\"> <table class=\"table table-striped\">";
    $res = $mysql->Execute("select * from projectmanagement.OntologyClassLabels 
    JOIN projectmanagement.OntologyClasses using (OntologyClassID) 
    JOIN projectmanagement.ontologies using (OntologyID) 
    where ClassTitle like '%".$_REQUEST["EntityName"]."%'");
    while($rec = $res->fetch())
    {
      echo "<tr><td>".$rec["OntologyTitle"]."</td><td>".C_Class."</td><td>".$rec["ClassTitle"]."</td><td>";
      echo "<A target=_blank href='ManageOntologyClassLabels.php?UpdateID=".$rec["OntologyClassLabelID"]."&OntologyClassID=".$rec["OntologyClassID"]."'>";
      echo $rec["label"]."</a></td></tr>";
    }
    $res = $mysql->Execute("select * from projectmanagement.OntologyPropertyLabels 
    JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
    JOIN projectmanagement.ontologies using (OntologyID) 
    where PropertyTitle like '%".$_REQUEST["EntityName"]."%'");
    while($rec = $res->fetch())
    {
      echo "<tr><td>".$rec["OntologyTitle"]."</td><td>".C_Property."</td><td>".$rec["PropertyTitle"]."</td><td>";
      echo "<A target=_blank href='ManageOntologyPropertyLabels.php?UpdateID=".$rec["OntologyPropertyLabelID"]."&OntologyPropertyID=".$rec["OntologyPropertyID"]."'>";
      echo $rec["label"]."</a></td></tr>";
    }
    echo "</table></div>";
  }
?>

<form method=post name=f1 id=f1>
    <div class="form-group">
<input type=hidden name="label" id="label" value="<? echo $_REQUEST["label"] ?>">
<input type=text class="form-control text-center" name='LabelName' id='LabelName' value='<? echo $LabelName ?>' size=100>
<? echo"<input type=submit class='form-control text-center' value='".C_SinLabels."'>" ?>
    </div>
</form>
<?
  if(isset($_REQUEST["LabelName"]))
  {
    echo "<div class=\"table-responsive\"> <table class=\"table table-striped\">";
    $res = $mysql->Execute("select * from projectmanagement.OntologyClassLabels 
    JOIN projectmanagement.OntologyClasses using (OntologyClassID) 
    JOIN projectmanagement.ontologies using (OntologyID) 
    where label like '%".$_REQUEST["LabelName"]."%'");
    while($rec = $res->fetch())
    {
      echo "<tr><td>".$rec["OntologyTitle"]."</td><td>".C_Class."</td><td>".$rec["ClassTitle"]."</td><td>".$rec["label"]."</td></tr>";
    }
    $res = $mysql->Execute("select * from projectmanagement.OntologyPropertyLabels 
    JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
    JOIN projectmanagement.ontologies using (OntologyID) 
    where label like '%".$_REQUEST["LabelName"]."%'");
    while($rec = $res->fetch())
    {
      echo "<tr><td>".$rec["OntologyTitle"]."</td><td>".C_Property."</td><td>".$rec["PropertyTitle"]."</td><td>".$rec["label"]."</td></tr>";
    }
    echo "</table></div>";
  }
  echo "</div>";
?>

</html>
