<?
  include("header.inc.php");
  HTMLBegin();
  $mysql = pdodb::getInstance();
  $query = "select MIS_Tables.DBName, TableName, MIS_Tables.description, count(*) from mis.MIS_TableFields 
	      JOIN mis.MIS_Tables on (MIS_Tables.DBName=MIS_TableFields.DBName and MIS_Tables.name=MIS_TableFields.TableName)
	      where (EducationalDomain='YES' or ResearchDomain='YES') and SystemRelatedDomain='NO' 
	      and EnableField='YES' 
	      and concat(MIS_Tables.DBName,'.',name) not in 
	      (select concat(DBName,'.',TableName) from MIS_TableFields where RelatedTable is not null and RelatedTable<>'')
	      group by MIS_Tables.DBName, TableName having count(*)<6";
  $res = $mysql->Execute($query);
  echo "<table dir=ltr>";
  while($rec = $res->fetch())
  {
    $res2 = $mysql->Execute("select count(*) as tcount from ".$rec["DBName"].".".$rec["TableName"]);
    $rec2 = $res2->fetch();
    if($rec2["tcount"]<20)
    {
      echo "<tr>";
      echo "<td>";
      echo $rec["DBName"]."</td><td>".$rec["TableName"];
      echo "</td>";
      echo "<td dir=rtl>".$rec["description"]."</td>";
      echo "<td>";
      
      $res3 = $mysql->Execute("select * from mis.MIS_TableFields where DBName='".$rec["DBName"]."' and TableName='".$rec["TableName"]."'");
      while($rec3 = $res3->fetch())
      {
	echo $rec3["FieldName"]." (".$rec3["description"].") - ";
      }
      
      echo "</td>";
      echo "</tr>";
    }
  }
  echo "</table>";
?>