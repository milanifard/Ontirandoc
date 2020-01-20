<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-29
*/
/*
 * Changed By Naghme Mohammadifar
 */
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "on");

include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ontologies.class.php");
HTMLBegin();

function GetRepositoryClassesCount($SelectedOnto)
{
  $mysql = pdodb::getInstance();
  $query = "select count(distinct label) as tcount from projectmanagement.OntologyClasses 
  JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) 
  where OntologyID in (".$SelectedOnto.")";
  $res = $mysql->Execute($query);
  if($rec = $res->fetch())
    return $rec["tcount"];
  return 0;
}

function GetRepositoryPropertiesCount($SelectedOnto)
{
  $mysql = pdodb::getInstance();
  $query = "select count(distinct label) as tcount from projectmanagement.OntologyProperties 
  JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID) 
  where OntologyID in (".$SelectedOnto.")";
  $res = $mysql->Execute($query);
  if($rec = $res->fetch())
    return $rec["tcount"];
  return 0;
}

function GetClassCount($OntologyID)
{
  $mysql = pdodb::getInstance();
  $query = "select count(*) as tcount from projectmanagement.OntologyClasses where OntologyID=".$OntologyID;
  $res = $mysql->Execute($query);
  if($rec = $res->fetch())
    return $rec["tcount"];
  return 0;
}

function GetPropertyCount($OntologyID)
{
  $mysql = pdodb::getInstance();
  $query = "select count(*) as tcount from projectmanagement.OntologyProperties where OntologyID=".$OntologyID;
  $res = $mysql->Execute($query);
  if($rec = $res->fetch())
    return $rec["tcount"];
  return 0;
}

function HowMuchCover($PropertyOrClass, $MainOnto, $TestOnto)
{
  if($PropertyOrClass=="Class")
  {
    $TableName = "OntologyClasses";
  }
  else 
  {
    $TableName = "OntologyProperties";  
  }
  
  $mysql = pdodb::getInstance();
  $query = "select 
  round ((
  (select count(*) from 
  (select distinct label from projectmanagement.Ontology".$PropertyOrClass."Labels JOIN projectmanagement.".$TableName." using (Ontology".$PropertyOrClass."ID) where OntologyID=".$MainOnto.") as MainOnto
  JOIN 
  (select distinct label from projectmanagement.Ontology".$PropertyOrClass."Labels JOIN projectmanagement.".$TableName." using (Ontology".$PropertyOrClass."ID) where OntologyID=".$TestOnto.") as TestOnto
  using (label)
  )
  /
  (select count(distinct label) from projectmanagement.Ontology".$PropertyOrClass."Labels JOIN projectmanagement.".$TableName." using (Ontology".$PropertyOrClass."ID) where OntologyID=".$TestOnto.")
  ) * 100, 2) as tcount";
  $res = $mysql->Execute($query);
  $rec = $res->fetch();
  return $rec["tcount"];
}

function HowMuchCoverDocument($MainOnto)
{
  $mysql = pdodb::getInstance();
  $query = "
  select 
  ((
  select count(distinct TermTitle) from projectmanagement.TermReferenceMapping JOIN terms using (TermID) 
where 
 TermTitle in
(
select distinct label from projectmanagement.OntologyClassLabels JOIN projectmanagement.OntologyClasses using (OntologyClassID) where OntologyID=".$MainOnto." 
union
select distinct label from projectmanagement.OntologyPropertyLabels JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) where OntologyID=".$MainOnto." 
))
/
  (select count(distinct TermTitle) from projectmanagement.TermReferenceMapping JOIN projectmanagement.terms using (TermID) 
))*100 as tcount
";
  $res = $mysql->Execute($query);
  $rec = $res->fetch();
  return $rec["tcount"];
}

function HowMuchAllCoverDocument($SelectedOnto)
{
  $mysql = pdodb::getInstance();
  $query = "
  select 
  ((
  select count(distinct TermTitle) from projectmanagement.TermReferenceMapping JOIN terms using (TermID) 
where 
 TermTitle in
(
select distinct label from projectmanagement.OntologyClassLabels JOIN projectmanagement.OntologyClasses using (OntologyClassID)  
where OntologyID in (".$SelectedOnto.")
union
select distinct label from projectmanagement.OntologyPropertyLabels JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)  
where OntologyID in (".$SelectedOnto.")
))
/
  (select count(distinct TermTitle) from projectmanagement.TermReferenceMapping JOIN projectmanagement.terms using (TermID) 
))*100 as tcount
";
  $res = $mysql->Execute($query);
  $rec = $res->fetch();
  return $rec["tcount"];
}

$ShowLink = true;
$thereshold = 80; // درصد مشابهت حد آستانه
$mysql = pdodb::getInstance();
$OntoCount = 0;

//ارزیابی آماری
if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="Stat")
{
  $SelectedOnto = "0";
  $OntoList = "";
  $OntoCount = 0;
  $res = manage_ontologies::GetList(); 
  $OntoArray = array();
  $i = 0;
  for($k=0; $k<count($res); $k++)
  {
	  if(isset($_REQUEST["ch_".$res[$k]->OntologyID])) 
	  {
	      $OntoCount++;
	      $OntoArray[$i++] = $res[$k];
	      $OntoList.= $OntoCount."- ".$res[$k]->OntologyTitle." ";
	      $SelectedOnto .= ", ".$res[$k]->OntologyID;
	  }
  }
  /*
  $query = "select OntologyTitle
,(select count(*) from projectmanagement.OntologyClasses where OntologyClasses.OntologyID=ontologies.OntologyID) as ClassCount
,(select count(*) from projectmanagement.OntologyProperties where OntologyProperties.OntologyID=ontologies.OntologyID) as PropertyCount
 from projectmanagement.ontologies where OntologyID in (".$SelectedOnto.")";
  $res = $mysql->Execute($query);
 */  
 
  echo "<div class='container'><br><table class='table table-bordered table-striped'>";
  echo "    <thead>
                <tr class='table-info text-center'>
                <th colspan=".(count($OntoArray)+2).">Percentage of each ontology coverage other ontology classes</th>
                </tr>
             </thead> 
             <tbody>";
  echo "<tr>";
  echo "<td >Ontology Title</td>";
  for($i=0; $i<count($OntoArray); $i++)
  {
    echo "<td>".$OntoArray[$i]->OntologyTitle."</td>";
  }
  echo "<td ><b>Average</b></td>";
  echo "</tr>";
  $Averages = array();
  for($i=0; $i<count($OntoArray); $i++)
  {
    echo "<tr>";
    echo "<td>".$OntoArray[$i]->OntologyTitle."</td>";
    $TotalP = 0;
    for($j=0; $j<count($OntoArray); $j++)
    {
      if($i==$j)
	echo "<td>&nbsp;</td>";
      else
      {
	$p = HowMuchCover("Class", $OntoArray[$i]->OntologyID, $OntoArray[$j]->OntologyID);
	  echo "<td>".$p."</td>";
	$TotalP += $p;
      }
    }
    echo "<td >".round($TotalP/(count($OntoArray)-1), 2)."</td>";
    $Averages[$i]["OntologyTitle"] = $OntoArray[$i]->OntologyTitle;
    $Averages[$i]["OntologyID"] = $OntoArray[$i]->OntologyID;
    $Averages[$i]["average"] = round($TotalP/(count($OntoArray)-1), 2);
    echo "</tr>";
  }
  echo "</tbody></table>";
  echo "<br>";
  for($i=0; $i<count($Averages); $i++)
    for($j=$i+1; $j<count($Averages); $j++)
      if($Averages[$i]["average"]<$Averages[$j]["average"])
      {
	$a1 = $Averages[$i]["average"];
	$a2 = $Averages[$i]["OntologyID"];
	$a3 = $Averages[$i]["OntologyTitle"];
	$Averages[$i]["average"] = $Averages[$j]["average"];
	$Averages[$i]["OntologyID"] = $Averages[$j]["OntologyID"];
	$Averages[$i]["OntologyTitle"] = $Averages[$j]["OntologyTitle"];
	$Averages[$j]["average"] = $a1;
	$Averages[$j]["OntologyID"] = $a2;
	$Averages[$j]["OntologyTitle"] = $a3;
      }

  echo "<table class='table table-bordered table-striped'>";
  echo "    <thead>
                <tr class='table-info'>
                    <th>Ontology Title</th>
                    <th>Class Count</th>
                    <th>Property Count</th>
                    <th>(Average of coverage other ontologies (classes</th>
                </tr>
              </thead>
              <tbody>";
  $TotalClasses = $TotalProperties = 0;
  for($i=0; $i<count($Averages); $i++)
  {
    $ClassesCount = GetClassCount($Averages[$i]["OntologyID"]);
    $PropertiesCount = GetPropertyCount($Averages[$i]["OntologyID"]);
    $TotalClasses += $ClassesCount;
    $TotalProperties += $PropertiesCount;
    echo "<tr>";
      echo "<td>".$Averages[$i]["OntologyTitle"]."</td>";
      echo "<td>".$ClassesCount."</td>";
      echo "<td>".$PropertiesCount."</td>";
      echo "<td>".$Averages[$i]["average"]."</td>";
      echo "</tr>";
  }
  
  echo "<tr>";
  echo "<td><b>Total</b></td>";
  echo "<td>".$TotalClasses."</td>";
  echo "<td>".$TotalProperties."</td>";
  echo "<td>100</td>";
  echo "</tr>";
  
  echo "<tr>";
  echo "<td><b>Repository</b></td>";
  echo "<td>".GetRepositoryClassesCount($SelectedOnto)."</td>";
  echo "<td>".GetRepositoryPropertiesCount($SelectedOnto)."</td>";
  echo "<td>100</td>";
  echo "</tr>";
  
  echo "</tbody></table>";
  
  echo "<br>";
  
  $Averages = array();
  echo "<table class='table  table-striped table-bordered '>";
  echo "    <thead>
                <tr class='table-info text-center'>
                    <th colspan=".(count($OntoArray)+2).">Percentage of each ontology coverage other ontology properties</th>
                </tr>
             </thead> <tbody>";
  echo "<tr>";
  echo "<td>Ontology Title</td>";
  for($i=0; $i<count($OntoArray); $i++)
  {
       echo "<td>".$OntoArray[$i]->OntologyTitle."</td>";

  }
  echo "<td><b>Average</b></td>";
  echo "</tr>";
  for($i=0; $i<count($OntoArray); $i++)
  {
    echo "<tr>";
    echo "<td>".$OntoArray[$i]->OntologyTitle."</td>";
    $TotalP = 0;
    for($j=0; $j<count($OntoArray); $j++)
    {
      if($i==$j)
	    echo "<td>&nbsp;</td>";
      else {
	    $p = HowMuchCover("Property", $OntoArray[$i]->OntologyID, $OntoArray[$j]->OntologyID);
	    if($p>50)
	        echo "<td>".$p."</td>";
	    else
	         echo "<td>".$p."</td>";
	    $TotalP += $p;
      }
    }
    echo "<td>".round($TotalP/(count($OntoArray)-1), 2)."</td>";
    $Averages[$i]["OntologyTitle"] = $OntoArray[$i]->OntologyTitle;
    $Averages[$i]["OntologyID"] = $OntoArray[$i]->OntologyID;
    $Averages[$i]["average"] = round($TotalP/(count($OntoArray)-1), 2);
    
    echo "</tr>";
  }
  echo "</tbody>
        </table> <br>";
  
  for($i=0; $i<count($Averages); $i++)
    for($j=$i+1; $j<count($Averages); $j++)
      if($Averages[$i]["average"]<$Averages[$j]["average"])
      {
        $a1 = $Averages[$i]["average"];
        $a2 = $Averages[$i]["OntologyID"];
        $a3 = $Averages[$i]["OntologyTitle"];
        $Averages[$i]["average"] = $Averages[$j]["average"];
        $Averages[$i]["OntologyID"] = $Averages[$j]["OntologyID"];
        $Averages[$i]["OntologyTitle"] = $Averages[$j]["OntologyTitle"];
        $Averages[$j]["average"] = $a1;
        $Averages[$j]["OntologyID"] = $a2;
        $Averages[$j]["OntologyTitle"] = $a3;
      }

  echo "<table class='table  table-striped table-bordered '>";
  echo "    <thead>
                <tr class='table-info'>
                    <th>Ontology Title</th>
                    <th>Class Count</th>
                    <th>Property Count</th>
                    <th>Average of coverage other ontologies (properties)</th>
                    </tr>
             </thead>
             <tbody>";
  for($i=0; $i<count($Averages); $i++)
  {
    echo "<tr>";
    echo "<td>".$Averages[$i]["OntologyTitle"]."</td>";
    echo "<td>".GetClassCount($Averages[$i]["OntologyID"])."</td>";
    echo "<td>".GetPropertyCount($Averages[$i]["OntologyID"])."</td>";
    echo "<td>".$Averages[$i]["average"]."</td>";
    echo "</tr>";
  }
  echo "</tbody></table>";

  $res = $mysql->Execute("select count(distinct TermTitle) as tcount from TermReferenceMapping JOIN terms using (TermID)");
  $rec = $res->fetch();
  $TotalTerms = $rec["tcount"];

  $Averages = array();
  for($i=0; $i<count($OntoArray); $i++)
  {
    $p = HowMuchCoverDocument($OntoArray[$i]->OntologyID);
    $Averages[$i]["average"] = round($p, 2);
    $Averages[$i]["OntologyTitle"] = $OntoArray[$i]->OntologyTitle;
    $Averages[$i]["OntologyID"] = $OntoArray[$i]->OntologyID;
  }
  
  for($i=0; $i<count($Averages); $i++)
    for($j=$i+1; $j<count($Averages); $j++)
      if($Averages[$i]["average"]<$Averages[$j]["average"])
      {
	$a1 = $Averages[$i]["average"];
	$a2 = $Averages[$i]["OntologyID"];
	$a3 = $Averages[$i]["OntologyTitle"];
	$Averages[$i]["average"] = $Averages[$j]["average"];
	$Averages[$i]["OntologyID"] = $Averages[$j]["OntologyID"];
	$Averages[$i]["OntologyTitle"] = $Averages[$j]["OntologyTitle"];
	$Averages[$j]["average"] = $a1;
	$Averages[$j]["OntologyID"] = $a2;
	$Averages[$j]["OntologyTitle"] = $a3;
      }
  
  
  echo "<br><table class='table  table-striped table-bordered '>";
  echo "    <thead>
                <tr class='table-info text-center'>
                    <th colspan=3 >(Percentage of each ontology coverage document terms (".$TotalTerms."</th>
                </tr>
             </thead>";
  echo "<tr>";
  echo "<td>Ontology Title</td>";
  echo "<td>Total Elements</td>";
  echo "<td><b>Coverage Percentage</b></td>";
  echo "</tr>";
  for($i=0; $i<count($Averages); $i++)
  {
    echo "<tr>";
    echo "<td>".$Averages[$i]["OntologyTitle"]."</td>";
    echo "<td>".(GetClassCount($Averages[$i]["OntologyID"])+GetPropertyCount($Averages[$i]["OntologyID"]))."</td>";
    $p = $Averages[$i]["average"];
    echo "<td>".round($p, 2)."</td>";
    echo "</tr>";
  }

  echo "<tr>";
  echo "<td><b>Repository</b></td>";
  echo "<td>".(GetRepositoryClassesCount($SelectedOnto)+GetRepositoryPropertiesCount($SelectedOnto))."</td>";
  $p = HowMuchAllCoverDocument($SelectedOnto);
  echo "<td >".round($p, 2)."</td>";
  echo "</tr>";
  
  echo "</table></div>";
  /*
  echo "<br>";
  echo "<table align=center border=1 cellspacing=1 cellpadding=5 width=70%>";
  echo "<tr><td colspan=".(count($OntoArray)+1).">Ontologies that have at least one property in common</td></tr>";
  echo "<tr bgcolor=#cccccc dir=ltr>";
  echo "<td>Ontology Title</td>";
  for($i=0; $i<count($OntoArray); $i++)
  {
    echo "<td>".$OntoArray[$i]->OntologyTitle."</td>";
  }
  echo "</tr>";
  for($i=0; $i<count($OntoArray); $i++)
  {
    $query = "
      select distinct OntologyID, OntologyTitle from projectmanagement.OntologyProperties
      JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID) 
      JOIN projectmanagement.ontologies using (OntologyID)
      where label in
      (
      select label from projectmanagement.OntologyPropertyLabels
      JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
      where OntologyID=".$OntoArray[$i]->OntologyID.")      
      ";
    $res = $mysql->Execute($query);
    $MatchOntos = array();
    $k = 0;
    while($rec = $res->fetch())
    {
      $MatchOntos[$k] = $rec["OntologyTitle"];
      $k++;
    }
    echo "<tr>";
    echo "<td>".$OntoArray[$i]->OntologyTitle."</td>";
    for($j=0; $j<count($OntoArray); $j++)
    {
      $NotFound = true;
      for($k=0; $k<count($MatchOntos); $k++)
      {
	if($MatchOntos[$k]==$OntoArray[$j]->OntologyTitle)
	{
	  echo "<td>X</td>";
	  $NotFound = false;
	  break;
	}
      }
      if($NotFound)
	echo "<td>&nbsp;</td>";
    }
    echo "</tr>";
  }
  echo "</table>";
  */

  die();
}

//analyzed with wordnet
if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="Analyze3")
{
  $SelectedOnto = "0";
  $OntoList = "";
  $OntoCount = 0;
  $res = manage_ontologies::GetList(); 
  for($k=0; $k<count($res); $k++)
  {
	  if(isset($_REQUEST["ch_".$res[$k]->OntologyID])) 
	  {
	      $OntoCount++;
//	      $OntoList.= $OntoCount."- ".$res[$k]->OntologyTitle." ";
          $OntoList.= $res[$k]->OntologyTitle." ";
	      $SelectedOnto .= ", ".$res[$k]->OntologyID;
	  }
  }
  echo "<br><div class='container'>
            <table class='table table-bordered table-striped'>
                <thead>
                    <tr class='table-info'>
                        <th class='text-center' colspan='2'>فهرست هستان نگارها  </th> 
                    </tr>
                </thead>";
  echo "        <tbody>
                    <tr>";
                        $splited = preg_split('/\s+/', $OntoList);
                        $counter = 0;
                        for($i=0; $i<count($splited)-1; $i++) {
                            $counter++;
                            echo "<tr >
                                       <td width='1%'>".$counter."</td>
                                       <td > ".$splited[$i]."</td >
                                       </tr >";
                              }
          echo " </tbody>
           </table>";
  $items = manage_ontologies::LoadClassesAndWordnetSimilars($SelectedOnto);
  echo "<br><table class='table table-bordered table-striped'>";
  echo "    <thead>
                <tr class='table-info'>
                    <th width='15%'>عنوان کلاس</th>
                    <th>کلاسها با نام مشابه (بر اساس WordNet)</th>
                </tr>
              </thead> 
              <tbody>";
  for($i=0; $i<count($items); $i++)
  {
    if($items[$i]["WordnetSimilars"]!="")
    {
      echo "<tr><td>";
      echo "<a target=_blank href='ManageOntologyClassLabels.php?UpdateID=".$items[$i]["LabelID"]."&OntologyClassID=".$items[$i]["ClassID"]."'>";
      echo $items[$i]["ClassTitle"]."</a> (".$items[$i]["label"].") </td><td>".$items[$i]["WordnetSimilars"]."</td></tr>";
    }
  }
  echo "</tbody></table>";

  $items = manage_ontologies::LoadPropertiesAndWordnetSimilars($SelectedOnto);
  echo "<br><table class='table table-striped table-bordered'>";
  echo "    <thead>
                <tr class='table-info'>
                    <th width='15%'>عنوان خصوصیت</th>
                    <th>خصوصیتها با نام مشابه (بر اساس WordNet)</th>
                </tr>
               </thead>
               <tbody>";
  for($i=0; $i<count($items); $i++)
  {
    if($items[$i]["WordnetSimilars"]!="")
    {
      echo "<tr><td>";
      echo "<a target=_blank href='ManageOntologyPropertyLabels.php?UpdateID=".$items[$i]["LabelID"]."&OntologyPropertyID=".$items[$i]["PropertyID"]."'>";
      echo $items[$i]["PropertyTitle"]."</a> (".$items[$i]["label"].") </td><td>".$items[$i]["WordnetSimilars"]."</td></tr>";
    }
  }
  echo "</table> </tbody></div>";
  
  die();
}

//analyzed with distance
if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="Analyze2")
{
  $SelectedOnto = "0";
  $OntoList = "";
  $OntoCount = 0;
  $res = manage_ontologies::GetList(); 
  for($k=0; $k<count($res); $k++)
  {
	  if(isset($_REQUEST["ch_".$res[$k]->OntologyID])) 
	  {
	      $OntoCount++;
	      //if($OntoCount>1)
		//$OntoList.= ", ";
//	      $OntoList.= $OntoCount."- ".$res[$k]->OntologyTitle." ";
          $OntoList.= $res[$k]->OntologyTitle." ";
	      $SelectedOnto .= ", ".$res[$k]->OntologyID;
	  }
  }
  echo "<div class='container'> <br><table class='table table-bordered table-striped'>
                <thead>
                    <tr class='text-center table-info'>
                        <th colspan='2'>فهرست هستان نگارها</th>
                    </tr></thead>";
  echo "        <tbody>";
  $splited = preg_split('/\s+/', $OntoList);
  $counter = 0;
  for($i=0; $i<count($splited)-1; $i++) {
      $counter++;
      echo "<tr >
                   <td width='1%'>".$counter."</td>
                   <td > ".$splited[$i]."</td >
                   </tr > ";
      }
          echo "      </tbody>
           </table>";

  $items = manage_ontologies::LoadClassesAndSimilarities($SelectedOnto, $thereshold);
  
  echo "<br><table class='table table-bordered table-striped'";
  echo "<thead>
            <tr class='table-info text-center'>
              <th colspan=2>بررسی عناوین با بیش از $thereshold درصد مشابهت در بین عناوین کلاسها که برچسب فارسی یکسان ندارند و مربوط به هستان نگارهای متفاوت هستند
              </th>
            </tr>
        </thead>
        <tbody>";

  for($i=0; $i<count($items); $i++)
  {
    if($items[$i]["similars"]!="")
    {
      echo "<tr><td width='20%'>";
      echo "<a target=_blank href='ManageOntologyClassLabels.php?UpdateID=".$items[$i]["LabelID"]."&OntologyClassID=".$items[$i]["ClassID"]."'>";
      echo $items[$i]["ClassTitle"]."</a> (".$items[$i]["label"].")";
      echo "</td><td>".$items[$i]["similars"]."</td></tr>";
    }
    
  }
  
  echo "<tr class='table-info text-center'>
            <td><strong>بررسی برچسبها با بیش از $thereshold درصد مشابهت در بین برچسب فارسی کلاسهایی که عنوان یکسان ندارند و مربوط به هستان نگارهای متفاوت هستند</td>
        </tr>";
  for($i=0; $i<count($items); $i++)
  {
    if($items[$i]["similar_labels"]!="")
    {
      echo "<tr><td>";
      echo "<a target=_blank href='ManageOntologyClassLabels.php?UpdateID=".$items[$i]["LabelID"]."&OntologyClassID=".$items[$i]["ClassID"]."'>";
      echo $items[$i]["label"]."</a> (".$items[$i]["ClassTitle"].")</td><td>".$items[$i]["similar_labels"]."</td></tr>";
    }
  }
  echo "</tbody></table>";
  
  $items = manage_ontologies::LoadPropertiesAndSimilarities($SelectedOnto, $thereshold);
  echo "<br><br>";
  echo "<table  class='table table-bordered table-striped'>";
  echo "    <thead>
                  <tr class='table-info text-center'>
                    <th colspan=2>بررسی عناوین با بیش از $thereshold درصد مشابهت در بین عناوین خصوصیات که برچسب فارسی یکسان ندارند و مربوط به هستان نگارهای متفاوت هستند</th>
                  </tr>
            </thead>
            <tbody>";
  for($i=0; $i<count($items); $i++)
  {
    
    if($items[$i]["similars"]!="")
    {
      echo "<tr><td width='25%'>";
      echo "<a target=_blank href='ManageOntologyPropertyLabels.php?UpdateID=".$items[$i]["LabelID"]."&OntologyPropertyID=".$items[$i]["PropertyID"]."'>";
      echo $items[$i]["PropertyTitle"]."</a> (".$items[$i]["label"].")";
      echo "</td><td>".$items[$i]["similars"]."</td></tr>";
    }
    
  }
  
  echo "<tr class='text-center table-info'>
        <td colspan=2 ><strong>بررسی برچسبها با بیش از $thereshold درصد مشابهت در بین برچسب فارسی خصوصیت هایی که عنوان یکسان ندارند و مربوط به هستان نگارهای متفاوت هستند</td></tr>";
  for($i=0; $i<count($items); $i++)
  {
    if($items[$i]["similar_labels"]!="")
    {
      echo "<tr><td>";
      echo "<a target=_blank href='ManageOntologyPropertyLabels.php?UpdateID=".$items[$i]["LabelID"]."&OntologyPropertyID=".$items[$i]["PropertyID"]."'>";
      echo $items[$i]["label"]."</a> (".$items[$i]["PropertyTitle"].")</td><td>".$items[$i]["similar_labels"]."</td></tr>";
    }
  }
  echo "</tbody></table> </div>";
  
  die();
}

// Dictionary
if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="Dic")
{
  $SelectedOnto = "0";
  $OntoList = "";
  $OntoCount = 0;
  if(!isset($_REQUEST["Update"]))
  {
    $res = manage_ontologies::GetList(); 
    for($k=0; $k<count($res); $k++)
    {
	    if(isset($_REQUEST["ch_".$res[$k]->OntologyID])) 
	    {
		$OntoCount++;
		//if($OntoCount>1)
		  //$OntoList.= ", ";
//		$OntoList.= $OntoCount."- ".$res[$k]->OntologyTitle." ";
        $OntoList.= $res[$k]->OntologyTitle." ";
		$SelectedOnto .= ", ".$res[$k]->OntologyID;
	    }
    }
  }
  else {
      $OntoList = $_REQUEST["OntoList"];
      $SelectedOnto = $_REQUEST["SelectedOnto"];
  }
  
  echo "<div class='container'>
            <br><table class='table table-bordered table-striped'>
                    <thead>
                        <tr>
                            <th class='table-info text-center' colspan='2'>فهرست هستان نگارها</th>
                        </tr>
                    </thead> <tbody>";
                    echo "<tr>";
                        $splited = preg_split('/\s+/', $OntoList);
                        $counter = 0;
                        for($i=0; $i<count($splited)-1; $i++) {
                            $counter++;
                            echo "<tr >
                                       <td width='1%'>".$counter."</td>
                                       <td > ".$splited[$i]."</td >
                                       </tr >" ;
                              }
  //echo "<tr><td dir=ltr align=center>اطلاعات در چند بخش شکسته شده است برای ذخیره دکمه ی همان بخش را استفاده کنید</td></tr>";
  echo "            </tbody>
                 </table>";

  echo "<form method=\"post\" id=\"f1\" name=\"f1\" >";
  echo "<div class='form-group'>";
  echo "    <input type=hidden name='ActionType' id='ActionType' value='Dic'>";
  echo "    <input type=hidden name='OntoList' id='OntoList' value='".$OntoList."'>";
  echo "    <input type=hidden name='SelectedOnto' id='SelectedOnto' value='".$SelectedOnto."'>";
  echo "    <input type=hidden name='Update' id='Update' value='1'> </div>";
  echo "    <br><table class='table table-bordered table-striped' >";
  echo "        <thead>
                    <tr class='table-info'>
                        <th> </th>
                        <th> موضوع هستان نگار</th>
                        <th>نوع هستان نگار</th>
                        <th> </th>
                    </tr>
                </thead> <tbody>";
  $ClassLabelQuery = "
  select * from
  (
  select OntologyID, OntologyTitle, 
replace(  
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(ClassTitle, 'http://xmlns.com/foaf/0.1/' ,'')
,'http://aims.fao.org/aos/geopolitical.owl#', '')
,'http://purl.obolibrary.org/obo/', '')
,'http://purl.org/dc/elements/1.1/', '')
,'http://purl.org/dc/terms/', '')
,'http://purl.org/NET/c4dm/event.owl#', '')
,'http://purl.org/net/OCRe/research.owl#', '')
,'http://purl.org/net/OCRe/statistics.owl#', '')
,'http://purl.org/net/OCRe/study_design.owl#', '')
,'http://purl.org/ontology/bibo/', '')
,'http://purl.org/spar/c4o/', '')
,'http://purl.org/vocab/aiiso/', '')
,'http://purl.org/vocab/vann/', '')
,'http://vitro.mannlib.cornell.edu/ns/vitro/0.7#', '')
,'http://vivoweb.org/ontology/scientific-research#', '')
,'http://www.essepuntato.it/2012/04/', '')
,'http://www.geneontology.org/formats/oboInOwl#', '')
,'http://www.obofoundry.org/ro/ro.owl#', '')
,'http://www.ontologydesignpatterns.org/cp/owl/timeindexedsituation.owl#', '')
,'http://www.w3.org/1999/02/22-rdf-syntax-ns#', '')
,'http://www.w3.org/2000/01/rdf-schema#', '')
,'http://www.w3.org/2002/07/owl#', '')
,'http://purl.org/net/OCRe/study_protocol.owl#', '')
,'http://purl.org/spar/cito/', '')
,'http://purl.org/spar/fabio/', '')
,'http://www.w3.org/2003/06/sw-vocab-status/ns#', '')
,'http://www.w3.org/2004/02/skos/core#', '')
,'http://www.w3.org/2006/timezone#', '')
,'http://www.w3.org/2006/vcard/ns#', '')
,'http://xmlns.com/wot/0.1/', '')
,'http://purl.org/spar/pro/', '')
,'http://www.w3.org/2003/01/geo/wgs84_pos#', '')
,'http://www.w3.org/2008/05/skos#', '')
,'Bibliographic references#', '')
,'#', '')
  as ItemTitle, label, 'class' as ItemType, OntologyClassLabelID as ItemID, OntologyClassID as ObjectID from projectmanagement.OntologyClassLabels 
		    JOIN projectmanagement.OntologyClasses using (OntologyClassID)
		    JOIN projectmanagement.ontologies using (OntologyID) 
		    where OntologyID in (".$SelectedOnto.")
  union all 
  select OntologyID, OntologyTitle, 
  
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(PropertyTitle, 'http://xmlns.com/foaf/0.1/' ,'')
,'http://aims.fao.org/aos/geopolitical.owl#', '')
,'http://purl.obolibrary.org/obo/', '')
,'http://purl.org/dc/elements/1.1/', '')
,'http://purl.org/dc/terms/', '')
,'http://purl.org/NET/c4dm/event.owl#', '')
,'http://purl.org/net/OCRe/research.owl#', '')
,'http://purl.org/net/OCRe/statistics.owl#', '')
,'http://purl.org/net/OCRe/study_design.owl#', '')
,'http://purl.org/ontology/bibo/', '')
,'http://purl.org/spar/c4o/', '')
,'http://purl.org/vocab/aiiso/', '')
,'http://purl.org/vocab/vann/', '')
,'http://vitro.mannlib.cornell.edu/ns/vitro/0.7#', '')
,'http://vivoweb.org/ontology/scientific-research#', '')
,'http://www.essepuntato.it/2012/04/', '')
,'http://www.geneontology.org/formats/oboInOwl#', '')
,'http://www.obofoundry.org/ro/ro.owl#', '')
,'http://www.ontologydesignpatterns.org/cp/owl/timeindexedsituation.owl#', '')
,'http://www.w3.org/1999/02/22-rdf-syntax-ns#', '')
,'http://www.w3.org/2000/01/rdf-schema#', '')
,'http://www.w3.org/2002/07/owl#', '')
,'http://purl.org/net/OCRe/study_protocol.owl#', '')
,'http://purl.org/spar/cito/', '')
,'http://purl.org/spar/fabio/', '')
,'http://www.w3.org/2003/06/sw-vocab-status/ns#', '')
,'http://www.w3.org/2004/02/skos/core#', '')
,'http://www.w3.org/2006/timezone#', '')
,'http://www.w3.org/2006/vcard/ns#', '')
,'http://xmlns.com/wot/0.1/', '')
,'http://purl.org/spar/pro/', '')
,'http://www.w3.org/2003/01/geo/wgs84_pos#', '')
,'http://www.w3.org/2008/05/skos#', '')
,'Bibliographic references#', '')
as ItemTitle, label, 'property' as ItemType, OntologyPropertyLabelID as ItemID, OntologyPropertyID as ObjectID from projectmanagement.OntologyPropertyLabels 
		    JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
		    JOIN projectmanagement.ontologies using (OntologyID) 
		    where OntologyID in (".$SelectedOnto.")
   ) as allitems
   order by ItemTitle ";
  //echo $ClassLabelQuery;
  //die();
  $res = $mysql->Execute($ClassLabelQuery);
  if(isset($_REQUEST["Update"]))
  {
    while($rec = $res->fetch())
    {
      if($rec["ItemType"]=="class" && isset($_REQUEST["cl_".$rec["ItemID"]]))
      {
	//echo "Class: ".$_REQUEST["cl_".$rec["ItemID"]].":".$rec["label"]."<br>";
	if($_REQUEST["cl_".$rec["ItemID"]]!=$rec["label"])
	{
	  //  echo $UpdateQuery = "update projectmanagement.OntologyClassLabels set label='".$_REQUEST["cl_".$rec["ItemID"]]."' where OntologyClassLabelID=".$rec["ItemID"];
	    $UpdateQuery = "update projectmanagement.OntologyClassLabels set label='".$_REQUEST["cl_".$rec["ItemID"]]."' where OntologyClassLabelID=".$rec["ItemID"];
//	    echo "<br>";
	    $mysql->Execute($UpdateQuery);
	}
      }
      else if($rec["ItemType"]=="property" && isset($_REQUEST["pr_".$rec["ItemID"]]))
      {
	//echo "Prop: ".$rec["label"].":".$_REQUEST["pr_".$rec["ItemID"]]."<br>";
	if($_REQUEST["pr_".$rec["ItemID"]]!=$rec["label"])
	{
//	  echo $UpdateQuery = "update projectmanagement.OntologyPropertyLabels set label='".$_REQUEST["pr_".$rec["ItemID"]]."' where OntologyPropertyLabelID=".$rec["ItemID"];
        $UpdateQuery = "update projectmanagement.OntologyPropertyLabels set label='".$_REQUEST["pr_".$rec["ItemID"]]."' where OntologyPropertyLabelID=".$rec["ItemID"];
//        echo "<br>";
	  $mysql->Execute($UpdateQuery);
	}
	
      }
    }
    $res = $mysql->Execute($ClassLabelQuery);
  }
  $i = 0;
  //while(($rec = $res->fetch()) && ($i<900))
  while(($rec = $res->fetch()))
  {
    $item = $rec["ItemTitle"];
    if($rec["ItemType"]=="class")
      $id = "cl_".$rec["ItemID"];
    else
      $id = "pr_".$rec["ItemID"];
    $value = $rec["label"];
    $i++;
    //if($i==997)
    //  continue;
    echo "<tr> <div class='form-group'>";
    echo "<td width=1%>".($i)."</td>";
    //echo "<td width=1%><font color=green>".$rec["ItemID"]."</font></td>";
    echo "<td >";
    echo $rec["OntologyTitle"]."</td>";
    echo "<td>".$rec["ItemType"]."</td>";
    echo "<td ><input type=text name='".$id."' id='".$id."' value='".$value."' >";
    //if(isset($_REQUEST["Update"]))
    //  echo "<font color=red>".$_REQUEST[$id]."</font> ";
    if($rec["ItemType"]=="property")
      echo "<a href='ManageOntologyProperties.php?UpdateID=".$rec["ObjectID"]."&OntologyID=".$rec["OntologyID"]."' target=_blank>";
    else
      echo "<a href='ManageOntologyClasses.php?UpdateID=".$rec["ObjectID"]."&OntologyID=".$rec["OntologyID"]."' target=_blank>";
    echo $item;
    echo "</a>";
    echo "</td>";
    echo "</tr> </div>";
  }
  echo "<tr >
              <td class='text-center' colspan='4'>
                <input type='submit' class='btn   btn-success' value='ذخیره'> ";
  echo "        <input type='button'  class='btn   btn-danger' value='بازگشت' onclick='document.location=\"Manageontologies.php\"'>";
  echo "</td></tr>";
  echo "</form> 
        </tbody></div>";
  
  die();
  /*
  
  echo "<form method=\"post\" id=\"f2\" name=\"f2\" >";
  echo "<input type=hidden name='ActionType' id='ActionType' value='Dic'>";
  echo "<input type=hidden name='OntoList' id='OntoList' value='".$OntoList."'>";
  echo "<input type=hidden name='SelectedOnto' id='SelectedOnto' value='".$SelectedOnto."'>";
  echo "<input type=hidden name='Update' id='Update' value='1'>";
  while(($rec = $res->fetch()) && ($i<1800))
  {
    $item = $rec["ItemTitle"];
    if($rec["ItemType"]=="class")
      $id = "cl_".$rec["ItemID"];
    else 
      $id = "pr_".$rec["ItemID"];    
    $value = $rec["label"];
    $i++;
    //if($i==997)
    //  continue;
    if($i%2==0)
      echo "<tr bgcolor=#cccccc>";
    else
      echo "<tr>";
    
    echo "<td width=1%>".($i)."</td>";
    echo "<td align=left>".$rec["OntologyTitle"]."</td><td align=left>".$rec["ItemType"]."</td>";
    echo "<td align=right><input dir=rtl type=text name='".$id."' id='".$id."' value='".$value."'>";
    //if(isset($_REQUEST["Update"]))
    //  echo "<font color=red>".$_REQUEST[$id]."</font> ";
    echo $item."</td>";
    echo "</tr>";
  }
  echo "<tr class=HeaderOfTable><td align=center colspan=4><input type=submit value='ذخیره'> ";
  echo "<input type=button value='بازگشت' onclick='document.location=\"Manageontologies.php\"'>";
  echo "</td></tr>";
  echo "</form>";
  
  echo "<form method=\"post\" id=\"f3\" name=\"f3\" enctype=\"multipart/form-data\">";
  echo "<input type=hidden name='ActionType' id='ActionType' value='Dic'>";
  echo "<input type=hidden name='OntoList' id='OntoList' value='".$OntoList."'>";
  echo "<input type=hidden name='SelectedOnto' id='SelectedOnto' value='".$SelectedOnto."'>";
  echo "<input type=hidden name='Update' id='Update' value='1'>";
  while(($rec = $res->fetch()) && ($i<2700))
  {
    $item = $rec["ItemTitle"];
    if($rec["ItemType"]=="class")
      $id = "cl_".$rec["ItemID"];
    else 
      $id = "pr_".$rec["ItemID"];    
    $value = $rec["label"];
    $i++;
    //if($i==997)
    //  continue;
    if($i%2==0)
      echo "<tr bgcolor=#cccccc>";
    else
      echo "<tr>";
    
    echo "<td width=1%>".($i)."</td>";
    echo "<td align=left>".$rec["OntologyTitle"]."</td><td align=left>".$rec["ItemType"]."</td>";
    echo "<td align=right><input dir=rtl type=text name='".$id."' id='".$id."' value='".$value."'>";
    //if(isset($_REQUEST["Update"]))
    //  echo "<font color=red>".$_REQUEST[$id]."</font> ";
    echo $item."</td>";
    echo "</tr>";
  }
  echo "<tr class=HeaderOfTable><td align=center colspan=4><input type=submit value='ذخیره'> ";
  echo "<input type=button value='بازگشت' onclick='document.location=\"Manageontologies.php\"'>";
  echo "</td></tr>";
  echo "</table>";
  echo "</form>";
  
  die();
  */
}

//تحلیل فراوانی
if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="Analyze")
{
  $SelectedOnto = "0";
  $OntoList = "";
  $OntoCount = 0;
  $res = manage_ontologies::GetList(); 
  for($k=0; $k<count($res); $k++)
  {
	  if(isset($_REQUEST["ch_".$res[$k]->OntologyID])) 
	  {
	      $OntoCount++;
	      //if($OntoCount>1)
		//$OntoList.= ", ";
//	      $OntoList.= $OntoCount."- ".$res[$k]->OntologyTitle." ";
          $OntoList.=$res[$k]->OntologyTitle." ";
	      $SelectedOnto .= ", ".$res[$k]->OntologyID;
	  }
  }
  echo "<div class='container'><br>";
  echo "    <table class='table table-bordered table-striped '> 
                <thead>
                    <tr class='table-info text-center'>
                        <th  colspan='2'>فهرست هستان نگارها</th>
                    </tr>
</thead>
                <tbody>";
                    $splited = preg_split('/\s+/', $OntoList);
                    $counter = 0;
                    for($i=0; $i<count($splited)-1; $i++) {
                        $counter++;
                        echo "<tr >
                                   <td width='1%'>".$counter."</td>
                                   <td > ".$splited[$i]."</td >
                                   </tr >" ;
                        }
  echo "        </tbody> 

             </table>";
    
    $total = 0;
    $query = "select ClassTitle, label, count(*) as tcount from projectmanagement.OntologyClassLabels
    JOIN projectmanagement.OntologyClasses on (OntologyClassLabels.OntologyClassID=projectmanagement.OntologyClasses.OntologyClassID)
    where OntologyID in (".$SelectedOnto.")
    group by ClassTitle,label
    having count(*)>1
    order by count(*) desc";
    $res = $mysql->Execute($query);
    
    echo "<br><table class='table table-bordered table-striped '>";
    echo "        <thead>
                        <tr class='table-info text-center'>";
    echo "                    <th colspan=4>کلاسها از نظر میزان ارجاع - بیش از دو ارجاع</th>";
    echo "              </tr>
                  </thead>
                  <tbody>";
    echo "            <tr>";
                            $i=0;
                            while($rec = $res->fetch())
                            {
                              if(($i%4==0) && $i>0)
                              {
                                echo "</tr>";
                                echo "<tr>";
                              }
                              $i++;
                              $total++;
                              echo "<td>";
                              if($ShowLink)
                                echo " <a target=_blank href='AnalyzeOntologies.php?label=".$rec["label"]."&EntityType=Class'>";
                              echo $i."- ".$rec["label"];
                              if($ShowLink)
                                 echo "</a>";
                              echo " (".$rec["tcount"].") ";
                              echo "</td>";
    }
    echo "        </tbody>
            </table>";

    echo "<br><table class='table table-bordered table-striped '>";
    echo "        <thead>
                        <tr class='table-info text-center'>";
    echo "                    <th colspan=4>خصوصیات به ترتیب میزان ارجاع - بیش از دو ارجاع</th>";
    echo "              </tr>
                  </thead>
                  <tbody>";
    echo "            <tr>";
    $query = "select PropertyTitle, label, count(*) as tcount from projectmanagement.OntologyPropertyLabels
    JOIN projectmanagement.OntologyProperties on (OntologyPropertyLabels.OntologyPropertyID=projectmanagement.OntologyProperties.OntologyPropertyID)
    where OntologyID in (".$SelectedOnto.")
    group by PropertyTitle,label
    having count(*)>2
    order by count(*) desc";
    $res = $mysql->Execute($query);
    $i=0;
    while($rec = $res->fetch())
    {
          if(($i%4==0) && $i>0)
          {
            echo "</tr>";
            echo "<tr>";
          }
          $i++;
          $total++;
          echo "<td align=right>";
          if($ShowLink)
              echo " <a target=_blank href='AnalyzeOntologies.php?label=".$rec["label"]."&EntityType=Property'>";
          echo $i."- ".$rec["label"];
          if($ShowLink)
              echo "</a>";
          echo " (".$rec["tcount"].") ";
    }
    echo "</tr>";
    echo "</tbody>
    </table>";

    echo "<br><table class='table table-bordered table-striped '>";
    echo "        <thead>
                        <tr class='table-info text-center'>";
    echo "                    <th colspan=4>موجودیتهایی که یکبار به عنوان کلاس و یکبار به عنوان خصوصیت مورد ارجاع بوده اند</th>";
    echo "              </tr>
                  </thead>
                  <tbody>";
    $query = "
select label from 
(select label from projectmanagement.OntologyClassLabels 
JOIN projectmanagement.OntologyClasses using (OntologyClassID)
where OntologyID in (".$SelectedOnto.") 
group by label
having count(*)=1) as c1
JOIN
(
select label from projectmanagement.OntologyPropertyLabels 
JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
where OntologyID in (".$SelectedOnto.") 
group by label
having count(*)=1
) as p
using (label)
union 
select label from 
(select label from projectmanagement.OntologyPropertyLabels
JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
where OntologyID in (".$SelectedOnto.") 
group by label
having count(*)=1) as c1
JOIN
(
select label from projectmanagement.OntologyClassLabels 
JOIN projectmanagement.OntologyClasses using (OntologyClassID)
where OntologyID in (".$SelectedOnto.") 
group by label
having count(*)=1
) as p
using (label)
   ";
    //echo $query;
    //die();
    $res = $mysql->Execute($query);
    
    echo "<tr>";
    $i=0;
    while($rec = $res->fetch())
    {
          if(($i%4==0) && $i>0)
          {
              echo "</tr>";
              echo "<tr>";
          }
          $i++;
          $total++;
          echo "<td >";
         if($ShowLink)
             echo " <a target=_blank href='AnalyzeOntologies.php?label=".$rec["label"]."'>";
         echo $i."- ".$rec["label"];
         if($ShowLink)
             echo "</a>";
         echo "</td>";
    }
    echo "</tr>";
    echo "</tbody></table>";

    echo "<br><table class='table table-bordered table-striped '>";
    echo "        <thead>
                        <tr class='table-info text-center'>";
    echo "                    <th colspan=1>کلاسهایی که تنها یکبار مورد اشاره قرار گرفته اند (به عنوان خصوصیت نیز ارجاع نشده اند)</th>";
    echo "              </tr>
                  </thead>
                  <tbody>";


    $query = "select ClassTitle, label, count(*) as tcount from projectmanagement.OntologyClassLabels
    JOIN projectmanagement.OntologyClasses on (OntologyClassLabels.OntologyClassID=projectmanagement.OntologyClasses.OntologyClassID)
    where OntologyID in (".$SelectedOnto.")
    and label not in
    (
      select label from projectmanagement.OntologyPropertyLabels
      JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
      where OntologyID in (".$SelectedOnto.") 
    )
    group by ClassTitle,label
    having count(*)=1
    order by label";
    $res = $mysql->Execute($query);
    
    echo "<tr>";
    $i=0;
    while($rec = $res->fetch())
    {
          if(($i%4==0) && $i>0)
          {
            echo "</tr>";
            echo "<tr>";
          }
          $i++;
          $total++;
          echo "<td >";
          if($ShowLink)
                echo " <a target=_blank href='AnalyzeOntologies.php?label=".$rec["label"]."&EntityType=Class'>";
          echo $i."- ".$rec["label"];
          if($ShowLink)
                echo "</a>";
          echo "</td>";
    }
    echo "</tr>";
    echo "</tbody> </table>";

    echo "<br><table class='table table-bordered table-striped '>";
    echo "        <thead>
                        <tr class='table-info text-center'>";
    echo "                    <th colspan=1>خصوصیاتی که تنها یکبار مورد اشاره قرار گرفته اند (به عنوان کلاس هم ارجاع نشده اند)</th>";
    echo "              </tr>
                  </thead>
                  <tbody>";
    $i=0;
    $query = "select PropertyTitle, label, count(*) as tcount from projectmanagement.OntologyPropertyLabels
    JOIN projectmanagement.OntologyProperties on (OntologyPropertyLabels.OntologyPropertyID=projectmanagement.OntologyProperties.OntologyPropertyID)
    where OntologyID in (".$SelectedOnto.")
    and label not in
    (
      select label from projectmanagement.OntologyClassLabels
      JOIN projectmanagement.OntologyClasses using (OntologyClassID)
      where OntologyID in (".$SelectedOnto.") 
    )
    group by PropertyTitle,label
    having count(*)=1
    order by label";
    $res = $mysql->Execute($query);
    while($rec = $res->fetch())
    {
          if(($i%4==0) && $i>0)
          {
            echo "</tr>";
            echo "<tr>";
          }
          $i++;
          $total++;
          echo "<td>";
          if($ShowLink)
                echo " <a target=_blank href='AnalyzeOntologies.php?label=".$rec["label"]."&EntityType=Prop'>";
          echo $i."- ".$rec["label"];
          if($ShowLink)
                echo "</a>";
          echo "</td>";
    }
    echo "</tr>";
    echo "</tbody></table> </div>";
  die();
}

if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_OntologyTitle"]))
		$Item_OntologyTitle=$_REQUEST["Item_OntologyTitle"];
	if(isset($_REQUEST["Item_OntologyURI"]))
		$Item_OntologyURI=$_REQUEST["Item_OntologyURI"];
	if(isset($_REQUEST["Item_FileName"]))
		$Item_FileName=$_REQUEST["Item_FileName"];
	$Item_FileContent = "";
	$Item_FileName = "";
	if (trim($_FILES['Item_FileContent']['name']) != '')
	{
		if ($_FILES['Item_FileContent']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_FileContent']['error'];
		}
		else
		{
			$_size = $_FILES['Item_FileContent']['size'];
			$_name = $_FILES['Item_FileContent']['tmp_name'];
			$Item_FileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
			$Item_FileName = trim($_FILES['Item_FileContent']['name']);
		}
	}
	if(isset($_REQUEST["Item_comment"]))
		$Item_comment=$_REQUEST["Item_comment"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_ontologies::Add($Item_OntologyTitle
				, $Item_OntologyURI
				, $Item_FileContent
				, $Item_FileName
				, $Item_comment
				);
	}	
	else 
	{	
		manage_ontologies::Update($_REQUEST["UpdateID"] 
				, $Item_OntologyTitle
				, $Item_OntologyURI
				, $Item_FileContent
				, $Item_FileName
				, $Item_comment
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$comment = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ontologies();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_OntologyTitle.value='".htmlentities($obj->OntologyTitle, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_OntologyURI.value='".htmlentities($obj->OntologyURI, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$comment = htmlentities($obj->comment, ENT_QUOTES, 'UTF-8'); 
}	
?>

<!------------------------------------------below this line all the things are bilingual--------------------------------------------------->
<div class="container">
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
    <div class="form-group">
<?php
	if(isset($_REQUEST["UpdateID"]))
	{
		echo "<input class=\"form-control\" type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		echo manage_ontologies::ShowSummary($_REQUEST["UpdateID"]);
		echo manage_ontologies::ShowTabs($_REQUEST["UpdateID"], "Newontologies");
	}
?>
    </div>
<br>

    <table class="table table-bordered table-sm" ">
        <thead >
        <tr>
            <th class="text-center table-info " colspan="1" ><?php echo C_CREATE_EDIT_ONTOLOGY?></th>
        </tr>
        </thead>
        <tboady>
            <tr>
                <td>
                <div class="form-group row">
                    <label for="Item_OntologyTitle" class="col-sm-2 col-form-label"><?php echo C_TITLE?></label>
                    <div class="col-sm-10">
                    <input class="form-control" type="text" name="Item_OntologyTitle" id="Item_OntologyTitle" >
                    <?php if(isset($_REQUEST["UpdateID"])) { ?>
                            <div class="row text-center">

                                <a class="text-black-50 btn" target=_blank href='GetOwl.php?OntologyID=<?php echo $_REQUEST["UpdateID"]; ?>'><?php echo C_GET_OWL_CODE_FROM_STRUCTURE?></a>
                                <a class="text-black-50 btn" target=_blank href='ShowClassesAnalysis.php?OntologyID=<?php echo $_REQUEST["UpdateID"]; ?>'><?php echo C_CLASS_STATISTICAL_ANALYSIS?></a>
                                <a class="text-black-50 btn" target=_blank href='ShowOntologyClassTree.php?OntologyID=<?php echo $_REQUEST["UpdateID"]; ?>&OnlyView=1'><?php echo C_TREE_STRUCTURE ?></a>
                            </div>
                             <div class="row text-center">
                                 <a class="text-black-50 btn" target=_blank href='EditOntologyLabels.php?EType=Class&OntologyID=<?php echo $_REQUEST["UpdateID"]; ?>'><?php echo C_CLASSES?></a>
                                 <a class="text-black-50 btn" target=_blank href='EditOntologyLabels.php?EType=OProp&OntologyID=<?php echo $_REQUEST["UpdateID"]; ?>'><?php echo C_THING_FEATURES?></a>
                                 <a class="text-black-50 btn" target=_blank href='GetER.php?OntologyID=<?php echo $_REQUEST["UpdateID"]; ?>'><?php echo C_GET_ER_CODE ?></a>
                                 <a class="text-black-50 btn" target=_blank href='EditOntologyLabels.php?EType=DProp&OntologyID=<?php echo $_REQUEST["UpdateID"]; ?>'><?php echo C_DATA_FEATURES?></a>
                                </div>
                    <?php } ?>
                    </div>
                </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group row">
                        <label for="Item_OntologyURI" class="col-sm-2 col-form-label"><?php echo C_INTERNET_PATH ?></label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="Item_OntologyURI" id="Item_OntologyURI" >
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group row">
                        <label for="Item_FileContent" class="col-sm-2 col-form-label"> <?php echo C_FILE?></label>
                        <div class="col-sm-10">
                            <input  type="file" class="form-control" name="Item_FileContent" id="Item_FileContent" data-filesize="3000" data-filesize-error="Max 3000B"
                                    accept="image/* , application/pdf"  />
                            <?php if(isset($_REQUEST["UpdateID"]) && $obj->FileName!="") { ?>
                                <a href='DownloadFile.php?FileType=ontologies&FieldName=FileContent&RecID=<?php echo $_REQUEST["UpdateID"]; ?>'><?php echo C_GETTING_FILE ?>[<?php echo $obj->FileName; ?>]</a>
                                &nbsp;
                                <a href='#' onclick='javascript: ExtractData();'><?php echo C_TRANSMIT_FILE_TO_DB?> </a>
                            <?php } ?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
            <td>
                <div class="form-group row">
                    <label for="Item_comment" class="col-sm-2 col-form-label"> <?php echo C_DESCRIPTION?></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="Item_comment" id="Item_comment" rows="2" ><?php echo $comment; ?></textarea>
                    </div>
                </div>
            </td>
            </tr>
                <?php if(isset($_REQUEST["UpdateID"]) && $obj->FileName!="") { ?>
                        <tr>
                            <td colspan=2>
                                <textarea ><?php echo $obj->FileContent ?></textarea>
                            </td>
                        </tr>
                    <? } ?>
            <tr>
                <td class="text-center">
                    <input type="submit" class="btn btn-success  " " value=<?php echo C_SAVE ?>>
                    <input type="button" class="btn btn-primary " onclick="javascript: document.location='Manageontologies.php';" value=<?php echo C_NEW?>>
                </td>
            </tr>
        </tboady>
    </table>

<input type="hidden" name="Save" id="Save" value="1">
</form>
</div>

<?php
$res = manage_ontologies::GetList();
$SomeItemsRemoved = false;
if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="Remove")
{
  for($k=0; $k<count($res); $k++)
  {
	  if(isset($_REQUEST["ch_".$res[$k]->OntologyID]))
	  {
		  manage_ontologies::Remove($res[$k]->OntologyID);
		  $SomeItemsRemoved = true;
	  }
  }
}
if($SomeItemsRemoved)
	$res = manage_ontologies::GetList();
?>
<div class="container">
<form id="ListForm" name="ListForm" method="post">
    <div class="form-group">
        <input class="form-control" type="hidden" id="ActionType" name="ActionType" value="Analyze">
    </div>
    <br>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
	          <th class="table-info text-center"  colspan="14"><?php echo C_ONTOLOGY?></th>
            </tr>
            <tr>
<!--                <th ><input type=CheckBox name=CheckAll id=CheckAll onchange='javascript: DoCheckAll(this.checked);'></th>-->
                <th> </th>
                <th ><?php echo C_ROW?></th>
                <th ><?php echo C_EDIT?></th>
                <th><?php echo C_TITLE?></th>
                <th><?php echo C_INTERNET_PATH?></th>
                <th><?php echo C_FILE?></th>
                <th><?php echo C_DESCRIPTION?></th>
                <th ><?php echo C_CLASSES?></th>
                <th  ><?php echo C_FEATURES?></th>
                <th ><?php echo C_EXPERT_JUDGES?></th>
                <th ><?php echo C_PRINT?></th>
                <th  ><?php echo C_PRINT_WITH_MERGE_SOURCES?></th>
                <th  ><?php echo C_PRINT_WITH_VOCAB_EXTRACTION_SOURCES?></th>
                <th ><?php echo C_PRINT_WITH_DATABASE_SOURCES?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $CheckAllCode = "";
            for($k=0; $k<count($res); $k++)
            {
              $ccount = manage_ontologies::GetClassCount($res[$k]->OntologyID);
              $pcount = manage_ontologies::GetPropertyCount($res[$k]->OntologyID);
              $CheckAllCode .= "document.getElementById('ch_".$res[$k]->OntologyID."').checked = CheckValue; \r\n";
              echo "<tr >";
              echo "<td>";
              echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyID."\" id=\"ch_".$res[$k]->OntologyID."\">";
              echo "</td>";
              echo "<td>".($k+1)."</td>";
              echo "	<td><a href=\"Manageontologies.php?UpdateID=".$res[$k]->OntologyID."\"> <i class=\"fa fa-edit\"></i></a></td>";
              echo "	<td>".htmlentities($res[$k]->OntologyTitle, ENT_QUOTES, 'UTF-8')."</td>";
              echo "	<td>".htmlentities($res[$k]->OntologyURI, ENT_QUOTES, 'UTF-8')."</td>";
              echo "	<td><a class='btn btn-sm btn-outline-dark' href='DownloadFile.php?FileType=ontologies&FieldName=FileContent&RecID=".$res[$k]->OntologyID."'><i class=\"fa fa-file-download\"></i></a></td>";
              echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->comment, ENT_QUOTES, 'UTF-8'))."</td>";
              echo "<td ><a  class='btn btn-sm btn-outline-dark' target=\"_blank\" href='ManageOntologyClasses.php?OntologyID=".$res[$k]->OntologyID ."'><i class=\"fa fa-clipboard\"> </i>";
              if($ccount>0)
                  echo "(".$ccount.")";
                echo "</a></td>";
                echo "<td ><a class='btn btn-sm btn-outline-dark' target=\"_blank\" href='ManageOntologyProperties.php?OntologyID=".$res[$k]->OntologyID ."'><i class=\"fa fa-clipboard-list\">";
                if($pcount>0)
                  echo "(".$pcount.")";
                echo "</a></td>";
                echo "<td><a class='btn btn-sm btn-outline-dark' target=_blank href='ManageOntologyValidationExperts.php?OntologyID=".$res[$k]->OntologyID."'> <i class=\"fa fa-balance-scale\"></a></td>";
                echo "<td><a class='btn btn-sm btn-outline-dark' target=_blank href='PrintOntologyDetails.php?OntologyID=".$res[$k]->OntologyID."'><i class=\"fa fa-print\"></i></a></td>";
                echo "<td><a class='btn btn-sm btn-outline-dark' target=_blank href='PrintOntologyDetails2.php?OntologyID=".$res[$k]->OntologyID."'><i class=\"fa fa-paste\"></i></a></td>";
                echo "<td><a class='btn btn-sm btn-outline-dark' target=_blank href='PrintOntologyDetails3.php?OntologyID=".$res[$k]->OntologyID."'><i class=\"fa fa-paste\"></i></a></td>";
                echo "<td><a class='btn btn-sm btn-outline-dark' target=_blank href='PrintOntologyDetails4.php?OntologyID=".$res[$k]->OntologyID."'><i class=\"fa fa-paste\"></i></a></td>";
                echo "</tr>";
            }
            ?>
            <tr>
                <td colspan="14" class="text-center table-light">
                    <input class="btn btn-dark " type="button" onclick="javascript: ConfirmDelete();" value=<?php echo C_DELETE?>>
                    <input class="btn btn-dark " type="button" onclick="javascript: ConfirmDic(); " value=<?php echo C_DICTIONARY ?>>
                    <input class="btn btn-dark " type="button" onclick="javascript: ConfirmAnalyze(); " value=<?php echo C_FREQUENCY_ANALYSIS?>>
                    <input class="btn btn-dark " type="button" onclick="javascript: ConfirmAnalyze2(); " value=<?php echo C_DISTANCE_ANALYSIS?>>
                    <input class="btn btn-dark " type="button" onclick="javascript: ConfirmStatistical(); " value=<?php echo C_STATISTICAL_ANALYSIS?>>
                </td>
            </tr>
            <tr>
                <td colspan="14" class="text-center table-light">
                    <input class="btn  btn-dark" type="button" onclick="javascript: document.location='MetaData2Onto.php' " value=<?php echo C_REVERSE_ENGINEERING?>>
                    <input class="btn btn-dark " type="button" onclick="javascript: ConfirmAnalyze3(); " value= <?php echo C_ANALYSIS_WITH_WORDNET?>>
                    <input class="btn btn-dark " type="button" onclick="javascript: document.location='CompareOntologies.php' " value=<?php echo C_CONTENT_COMPARISON ?>>
                    <input class="btn btn-dark " type="button" onclick="javascript: document.location='ManageOntologyMergeProject.php' " value=<?php echo C_MERGED_PROJECTS?>>
                </td>
            <tr>

</tr>
        </tbody>
</table>
</form>
</div>
<form target="_blank" method="post" action="Newontologies.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function DoCheckAll(CheckValue)
{
  <?php echo $CheckAllCode; ?>
}
function ConfirmDelete()
{
	if(confirm(<?php echo C_CONFIRM_TO_DELETE?>))
	{
	  document.getElementById('ActionType').value="Remove";
	  document.ListForm.submit();
	}
}
function ConfirmAnalyze()
{
	  document.getElementById('ActionType').value="Analyze";
	  document.ListForm.submit();
}
function ConfirmAnalyze2()
{
	  document.getElementById('ActionType').value="Analyze2";
	  document.ListForm.submit();
}
function ConfirmAnalyze3()
{
	  document.getElementById('ActionType').value="Analyze3";
	  document.ListForm.submit();
}
function ConfirmDic()
{
	  document.getElementById('ActionType').value="Dic";
	  document.ListForm.submit();
}
function ConfirmStatistical()
{
	  document.getElementById('ActionType').value="Stat";
	  document.ListForm.submit();
}


function ExtractData()
{
	if(confirm(<?php echo C_ALERT_TO_CLOSE ?>)) document.location='loader.php?OntologyID=<?php if(isset($_REQUEST["UpdateID"])) echo $_REQUEST["UpdateID"]; ?>';
}
</script>
</html>
