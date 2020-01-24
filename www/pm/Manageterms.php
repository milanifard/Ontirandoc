<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اصطلاحات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-6
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/terms.class.php");
HTMLBegin();

function GetRelatedOntologyElement($TermID)
{
  $ElementName = "";
  $mysql = pdodb::getInstance();
  $mysql->Prepare("select EntityType, OntologyClassLabels.label as ClassLabel, OntologyPropertyLabels.label as PropertyLabel,
		    ClassTitle, PropertyTitle, PermittedValue
		    from projectmanagement.TermOntologyElementMapping 
		    LEFT JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyEntityID)
		    LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
		    LEFT JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyEntityID)
		    LEFT JOIN projectmanagement.OntologyPropertyLabels on (OntologyProperties.OntologyPropertyID=OntologyPropertyLabels.OntologyPropertyID)
		    LEFT JOIN projectmanagement.OntologyPropertyPermittedValues using (OntologyPropertyPermittedValueID)
		    where TermID=?");
  $res = $mysql->ExecuteStatement(array($TermID));
  if($rec = $res->fetch())
  {
    if($rec["EntityType"]=="CLASS")
      $ElementName = $rec["ClassLabel"]." (".$rec["ClassTitle"].")";
    else if($rec["EntityType"]=="DATA_RANGE")
      $ElementName = $rec["PropertyLabel"]." (".$rec["PropertyTitle"].") - ".$rec["PermittedValue"];
    else
      $ElementName = $rec["PropertyLabel"]." (".$rec["PropertyTitle"].")";
  }
  return $ElementName;
}

function GetTermSynSetIDs($TermTitle)
{
  //return "Disabled";
  $ret = "0";
  $mysql = pdodb::getInstance();
  $query = "select synSetID from ferdowsnet.sense 
			    JOIN ferdowsnet.persianwords on (sense.pewordID=persianwords.ID)
			    where word='".$TermTitle."'";
  $res = $mysql->Execute($query);
  $i = 0;
  while($rec = $res->fetch())
  {
    $ret .= ",";
    $ret .= $rec["synSetID"];
    $i++;
  }
  return $ret;
}

function GetSimilarTerm($TermID, $TermTitle, $SimilarList, $thereshold)
{
  $mysql = pdodb::getInstance();
  $mysql->Prepare("select TermID, TermTitle, CreatorUserID from projectmanagement.terms where TermID<>?");
  $res = $mysql->ExecuteStatement(array($_REQUEST["TermID"]));
  $i=0;
  while($rec = $res->fetch())
  {
    $distance = levenshtein($TermTitle, $rec["TermTitle"]);
    if(max(strlen($TermTitle), strlen($rec["TermTitle"])) == 0) $p =0;
    else $p = (1-($distance/max(strlen($TermTitle), strlen($rec["TermTitle"]))) )*100;
    if($p>$thereshold)
    {
      $SimilarList[$i]["TermID"] = $rec["TermID"];
      $SimilarList[$i]["TermTitle"] = $rec["TermTitle"];
      $SimilarList[$i]["CreatorUserID"]=$rec["CreatorUserID"];
      $SimilarList[$i]["reason"] = C_STRUCTURAL_SIMILARITY;
      $i++;
    }
  }
  return $SimilarList;
}

function GetHyponymSynSets($SynSetIDs)
{
  //return "Disabled";
  $ret = "0";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select synSetID2 as ID from ferdowsnet.relationsynset where 
			    synSetID1 in (".$SynSetIDs.") 
			    and relationType='hyponym'
			   union select synSetID1 as ID  from ferdowsnet.relationsynset where 
			    synSetID2 in (".$SynSetIDs.")
			    and relationType='hypernym'
			    ");
  $i = 0;
  while($rec = $res->fetch())
  {
    $ret .= ",";
    $ret .= $rec["ID"];
    $i++;
  }
  return $ret;
}

function GetHypernymSynSets($SynSetIDs)
{
  //return "Disabled";
  $ret = "0";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select synSetID2 as ID from ferdowsnet.relationsynset where 
			    synSetID1 in (".$SynSetIDs.") 
			    and relationType='hypernym'
			   union select synSetID1 as ID  from ferdowsnet.relationsynset where 
			    synSetID2 in (".$SynSetIDs.")
			    and relationType='hyponym'
			    ");
  $i = 0;
  while($rec = $res->fetch())
  {
    $ret .= ",";
    $ret .= $rec["ID"];
    $i++;
  }
  return $ret;
}

function GetSameSynSetTerms($SynSetIDs, $TermTitle, $SimilarList)
{
  $i = count($SimilarList);
  $mysql = pdodb::getInstance();
  $query = "select distinct TermID, TermTitle, CreatorUserID from ferdowsnet.sense 
			    JOIN ferdowsnet.persianwords on (sense.pewordID=persianwords.ID)
			    JOIN projectmanagement.terms on (TermTitle=word)
			    where synSetID in (".$SynSetIDs.") and TermTitle<>'".$TermTitle."'";
  $res = $mysql->Execute($query);
  $i = 0;
  while($rec = $res->fetch())
  {
      $SimilarList[$i]["TermID"] = $rec["TermID"];
      $SimilarList[$i]["TermTitle"] = $rec["TermTitle"];
      $SimilarList[$i]["CreatorUserID"]=$rec["CreatorUserID"];
      $SimilarList[$i]["reason"] = C_PEERS_IN_THE_SEMANTIC_NETWORK;
      $i++;
  }
  return $SimilarList;
}

function GetHyponymTerms($HypoSynSetIDs, $TermTitle, $SimilarList)
{
  $i = count($SimilarList);
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select distinct TermID, TermTitle, CreatorUserID from ferdowsnet.sense 
			    JOIN ferdowsnet.persianwords on (sense.pewordID=persianwords.ID)
			    JOIN projectmanagement.terms on (TermTitle=word)
			    where synSetID in (".$HypoSynSetIDs.") and TermTitle<>'".$TermTitle."'");
  $i = 0;
  while($rec = $res->fetch())
  {
      $SimilarList[$i]["TermID"] = $rec["TermID"];
      $SimilarList[$i]["TermTitle"] = $rec["TermTitle"];
      $SimilarList[$i]["CreatorUserID"]=$rec["CreatorUserID"];
      $SimilarList[$i]["reason"] = C_MORE_SPECIFIC_MEANING_IN_THE_SEMANTIC_GRID;
      $i++;
  }
  return $SimilarList;
}

function GetHyperTermsByStructSimilarTerm($TermID, $TermTitle, $SimilarList)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $i = count($SimilarList);
  $res = $mysql->Execute("select TermID, TermTitle,CreatorUserID  from projectmanagement.terms where TermID<>'".$TermID."'  and TermID in (select TermID from projectmanagement.TermReferenceMapping )");

  while($rec = $res->fetch())
  {
    if($TermTitle!="" && $rec["TermTitle"]!="" && strpos($TermTitle, $rec["TermTitle"])>-1)
    {
      $SimilarList[$i]["TermID"] = $rec["TermID"];
      $SimilarList[$i]["TermTitle"] = $rec["TermTitle"];
      $SimilarList[$i]["CreatorUserID"]=$rec["CreatorUserID"];
      $SimilarList[$i]["reason"] = C_STRUCTURAL_SIMILARITY;
      $i++;
    }
  }
  return $SimilarList;
}

function GetHypernymTerms($HyperSynSetIDs, $TermTitle, $SimilarList)
{
  return $SimilarList;
  $i = count($SimilarList);
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select distinct TermID, TermTitle, CreatorUserID  from ferdowsnet.sense 
			    JOIN ferdowsnet.persianwords on (sense.pewordID=persianwords.ID)
			    JOIN projectmanagement.terms on (TermTitle=word)
			    where synSetID in (".$HyperSynSetIDs.") and TermTitle<>'".$TermTitle."'");
  $i = 0;
  while($rec = $res->fetch())
  {
      $SimilarList[$i]["TermID"] = $rec["TermID"];
      $SimilarList[$i]["TermTitle"] = $rec["TermTitle"];
      $SimilarList[$i]["CreatorUserID"]=$rec["CreatorUserID"];
      $SimilarList[$i]["reason"] = C_MORE_GENERAL_MEANING_IN_THE_SEMANTIC_GRID;
      $i++;
  }
  return $SimilarList;
}

function GetHypoTermsByStructSimilarTerm($TermID, $TermTitle, $SimilarList)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select TermID, TermTitle, CreatorUserID from projectmanagement.terms where TermID<>'".$TermID."' and TermTitle like '".$TermTitle."%' and TermID in (select TermID from projectmanagement.TermReferenceMapping)");
  $i = count($SimilarList);
  while($rec = $res->fetch())
  {
    $SimilarList[$i]["TermID"]=$rec["TermID"];
    $SimilarList[$i]["TermTitle"]=$rec["TermTitle"];
    $SimilarList[$i]["CreatorUserID"]=$rec["CreatorUserID"];
    $SimilarList[$i]["reason"] = C_STRUCTURAL_SIMILARITY;
    $i++;
  }
  $res = $mysql->Execute("select TermID, TermTitle, CreatorUserID from projectmanagement.terms where TermID<>'".$TermID."' and TermTitle like '%".$TermTitle."' and TermTitle not like '".$TermTitle."%'  and TermID in (select TermID from projectmanagement.TermReferenceMapping )");
  while($rec = $res->fetch())
  {
    $SimilarList[$i]["TermID"]=$rec["TermID"];
    $SimilarList[$i]["TermTitle"]=$rec["TermTitle"];
    $SimilarList[$i]["CreatorUserID"]=$rec["CreatorUserID"];
    $SimilarList[$i]["reason"] = C_STRUCTURAL_SIMILARITY;
    $i++;
  }
  $res = $mysql->Execute("select TermID, TermTitle, CreatorUserID from projectmanagement.terms where TermID<>'".$TermID."' and TermTitle like '%".$TermTitle."%' and (TermTitle not like '".$TermTitle."%' and TermTitle not like '%".$TermTitle."')  and TermID in (select TermID from projectmanagement.TermReferenceMapping )");
  while($rec = $res->fetch())
  {
    $SimilarList[$i]["TermID"]=$rec["TermID"];
    $SimilarList[$i]["TermTitle"]=$rec["TermTitle"];
    $SimilarList[$i]["CreatorUserID"]=$rec["CreatorUserID"];
    $SimilarList[$i]["reason"] = C_STRUCTURAL_SIMILARITY;
    $i++;
  }

  $SynSetIDs = GetTermSynSetIDs($TermTitle);
  $SimilarList = GetSameSynSetTerms($SynSetIDs, $TermTitle, $SimilarList);

  $HypoSynSetIDs = GetHyponymSynSets($SynSetIDs);
  $SimilarList = GetHyponymTerms($HypoSynSetIDs, $TermTitle, $SimilarList);

  $HyperSynSetIDs = GetHypernymSynSets($SynSetIDs);
  $SimilarList = GetHypernymTerms($HyperSynSetIDs, $TermTitle, $SimilarList);

  return $SimilarList;
}

function ShowTermMappings($TermID)
{
  $mysql = pdodb::getInstance();
  $mysql->Prepare("select title, count(*) as tcount from projectmanagement.TermReferenceMapping
		    JOIN projectmanagement.TermReferences using (TermReferenceID)
		    where TermID=?
		    group by title");
  $res = $mysql->ExecuteStatement(array($TermID));
  while($rec = $res->fetch())
  {
    echo $rec["title"].C_PERIODICITY.$rec["tcount"]."<br>";
  }
}

$CheckAllScript = "";
$UnCheckAllScript = "";
$mysql = pdodb::getInstance();

if(isset($_REQUEST["ReplaceTermID"]))
{
  $query = "select TermTitle from projectmanagement.terms where TermID=?";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($_REQUEST["TermID"]));
  $rec = $res->fetch();
  $PreTermTitle = $rec["TermTitle"];

  $query = "select TermTitle from projectmanagement.terms where TermID=?";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($_REQUEST["ReplaceTermID"]));
  $rec = $res->fetch();
  $NewTermTitle = $rec["TermTitle"];

  $query = "update projectmanagement.TermReferenceMapping set TermID=? where TermID=?";
  $mysql->Prepare($query);
  $mysql->ExecuteStatement(array($_REQUEST["ReplaceTermID"], $_REQUEST["TermID"]));

  $query = "delete from projectmanagement.terms where TermID=?";
  $mysql->Prepare($query);
  $mysql->ExecuteStatement(array($_REQUEST["TermID"]));

  $query = "insert into projectmanagement.TermsManipulationHistory (PreTermID, PreTermTitle, NewTermID, NewTermTitle, ActionType, PersonID, ATS)
	    values (?, ?, ?, ?, 'REPLACE', '".$_SESSION["PersonID"]."', now()) ";
  $mysql->Prepare($query);
  $mysql->ExecuteStatement(array($_REQUEST["TermID"], $PreTermTitle, $_REQUEST["ReplaceTermID"], $NewTermTitle));
  
  echo "<div class='container '>
<br>
            <div class='row align-items-center'>
                 <div class='test-center'>.col-md-3 .offset-md-3</div>
                 
                 </div>
  </div>";


//    <div class= 'row  align-items-center justify-content-md-center'>
//            <div class='text-center'>عملیات جایگزینی انجام شد</div>
//          </div>
//          <div class= 'row align-items-center justify-content-md-center'>
//            <div class='text-center'><a class='btn btn-danger' href='Manageterms.php'>بازگشت به صفحه مدیریت واژگان</a></div>
//          </div>
  die();
}

if(isset($_REQUEST["Merge"])) 
{
  $thereshold = 90;
  $mysql->Prepare("select TermID, TermTitle, CreatorUserID from projectmanagement.terms where TermID=?");
  $res = $mysql->ExecuteStatement(array($_REQUEST["TermID"]));
  $rec = $res->fetch();
  //echo "اصطلاحات مشابه (تحلیل فاصله لونشتین): ";
  $SimilarList = array();
  $SimilarList = GetSimilarTerm($rec["TermID"], $rec["TermTitle"], $SimilarList, $thereshold);
  $SimilarList = GetHypoTermsByStructSimilarTerm($rec["TermID"], $rec["TermTitle"], $SimilarList);
  $SimilarList = GetHyperTermsByStructSimilarTerm($rec["TermID"], $rec["TermTitle"], $SimilarList);

  $TermElements = explode(" ",$rec["TermTitle"]);
  if(count($TermElements)>1)
  {
    for($i=0; $i<count($TermElements); $i++)
    {
      if(strlen($TermElements[$i])>2) // برای اجزای کلمات ترکیبی که بیشتر از ۲ کاراکتر باشند مجددا مشابه یابی را انجام می دهد
      {
	$SimilarList = GetSimilarTerm($rec["TermID"], $TermElements[$i], $SimilarList, $thereshold);
	$SimilarList = GetHypoTermsByStructSimilarTerm($rec["TermID"], $TermElements[$i], $SimilarList);
	$SimilarList = GetHyperTermsByStructSimilarTerm($rec["TermID"], $TermElements[$i], $SimilarList);
      }
    }
  }

  $DistinctArray = array();
  for($i=0; $i<count($SimilarList); $i++)
  {
    $Exist = false;
    for($j=0; $j<count($DistinctArray); $j++)
    {
      if($DistinctArray[$j]["TermID"]==$SimilarList[$i]["TermID"])
      {
	$Exist = true;
	break;
      }
    }
    if(!$Exist)
    {
      $LastIndex = count($DistinctArray);
      $DistinctArray[$LastIndex]["TermID"]=$SimilarList[$i]["TermID"];
      $DistinctArray[$LastIndex]["TermTitle"]=$SimilarList[$i]["TermTitle"];
      $DistinctArray[$LastIndex]["reason"]=$SimilarList[$i]["reason"];
      $DistinctArray[$LastIndex]["CreatorUserID"]=$SimilarList[$i]["CreatorUserID"];
    }
  }
  $SimilarList = $DistinctArray;
  echo "<div class='container'><br>";
  echo "    <table class='table table-bordered table-striped'> 
                <thead>";
  echo "            <tr class='table-info text-center'>";
  echo "                <th colspan=3>".C_WORD.': '.$rec["TermTitle"]." </th>
                        <th colspan='2'>".C_RECORDER.': '.$rec["CreatorUserID"]. "</th>
                        <th>".C_REFERENCES_IN_WORDS;ShowTermMappings($rec["TermID"]) ; echo "</th>";
  echo "            </tr>
                </thead>
                <tbody>";
  echo "<tr>";
  echo "    <td >".C_WORD_CODE."</td>
            <td>".C_WORD."</td>
            <td>".C_SIMILARITY_TYPE."</td>
            <td>".C_RECORDER."</td>
            <td>".C_RESOURCES_AND_THE_NUMBER_OF_REPETITIONS."</td>
            <td>".C_REPLACEMENT."</td>";
  echo "</tr>";
  for($i=0; $i<count($SimilarList); $i++)
  {
    echo "<tr>";
    echo "<td>".$SimilarList[$i]["TermID"]."</td>";
    echo "<td>".$SimilarList[$i]["TermTitle"]."</td>";
    echo "<td>".$SimilarList[$i]["reason"]."</td>";
    echo "<td>".$SimilarList[$i]["CreatorUserID"]."</td>";
    echo "<td>";
    ShowTermMappings($SimilarList[$i]["TermID"]);
    echo "</td>";
    echo "<td><a class='btn' href='Manageterms.php?TermID=".$rec["TermID"]."&ReplaceTermID=".$SimilarList[$i]["TermID"]."'>".C_REPLACE_THE_SELECTED_WORD_WITH_THE_WORD_IN_THIS_ROW."</a></td>";
    echo "</tr>";
  }
  echo "<tr>";
  echo "<td colspan=6 class='text-center'>
            <input type=button class='btn btn-danger' value=".'"'.C_RETURN.'"'."onclick='javascript: history.back();'></td>";
  echo "</tr> </tbody> </div>";
  die();
}

if(isset($_REQUEST["Save"]))
{
	if(isset($_REQUEST["Item_TermTitle"]))
		$Item_TermTitle=$_REQUEST["Item_TermTitle"];
	if(isset($_REQUEST["Item_comment"]))
		$Item_comment=$_REQUEST["Item_comment"];
	if(isset($_REQUEST["Item_CreatorUserID"]))
		$Item_CreatorUserID=$_REQUEST["Item_CreatorUserID"];
	if(isset($_REQUEST["Item_CreateDate"]))
		$Item_CreateDate=$_REQUEST["Item_CreateDate"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_terms::Add($Item_TermTitle
				, $Item_comment
				);
	}	
	else
	{
		manage_terms::Update($_REQUEST["UpdateID"] 
				, $Item_TermTitle
				, $Item_comment
				);
	}
	echo SharedClass::CreateMessageBox(C_INFORMATION_SAVED);
}
$LoadDataJavascriptCode = '';
$Item_comment = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_terms();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$LoadDataJavascriptCode .= "document.f1.Item_TermTitle.value='".htmlentities($obj->TermTitle, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	$Item_comment = htmlentities($obj->comment, ENT_QUOTES, 'UTF-8');
}
?>

<form method="post" id="f1" name="f1" >
<?php
	if(isset($_REQUEST["UpdateID"]))
	{
	    echo "<div class='container'>";
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		echo manage_terms::ShowSummary($_REQUEST["UpdateID"]);
		echo manage_terms::ShowTabs($_REQUEST["UpdateID"], "Newterms");
		echo "</div>";
	}
?>

<div class='container'>
<br><table class="table table-bordered ">
    <thead>
        <tr class="text-center table-info">
            <th align="center"><?php echo C_CREATE_EDIT_TERMS?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <div class="form-group row">
                    <label for="Item_TermTitle" class="col-sm-2 col-form-label"><?php echo C_TITLE?></label>
                    <div class="col-sm-10">
                        <input type="text" name="Item_TermTitle" id="Item_TermTitle" class="form-control" required>
                    </div>
	        </td>
        </tr>
        <tr>
	        <td>
                <div class="form-group row">
                    <label for="Item_comment" class="col-sm-2 col-form-label"><?php echo C_NOTE?></label>
                    <div class="col-sm-10">
                        <textarea name="Item_comment" id="Item_comment" class="form-control"><?php echo $Item_comment ?></textarea>
                    </div>

	        </td>

        </tr>
        <tr>
            <td colspan=2 class="text-center">
                <span id=DetailsSpan name=DetailsSpan></span>
            </td>
</tr>
        <tr class="text-center">
            <td>
                <input type="button" class="btn   btn-success" onclick="javascript: ValidateForm();" value=<?php echo C_SAVE?>>
                <input type="button" class="btn   btn-danger" onclick="javascript: document.location='Manageterms.php';" value=<?php echo C_NEW?>>
                <?php if(isset($_REQUEST["UpdateID"])) { ?>
                <input type="button" class="btn   btn-info" onclick="javascript: MergeSuggestions('<?php echo $_REQUEST["UpdateID"]; ?>');" value=<?php echo '"'.C_MERGE_SUGGESTIONS.'"'?>>
                <?php } ?>
            </td>
        </tr>
    </tbody>

</table>

<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<?php echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php
$NumberOfRec = 150;
 $k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
	if(!is_numeric($PageNumber))
		$PageNumber = 0;
	else
		$PageNumber = $_REQUEST["PageNumber"];
	$FromRec = $PageNumber*$NumberOfRec;
}
else
{
	$FromRec = 0;
}
if(isset($_REQUEST["SearchAction"]))
{
	$OrderByFieldName = "TermTitle";
	$OrderType = "";
	if(isset($_REQUEST["OrderByFieldName"]))
	{
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		$OrderType = $_REQUEST["OrderType"];
	}
	$TermTitle=htmlentities($_REQUEST["Item_TermTitle"], ENT_QUOTES, 'UTF-8');
	$comment=htmlentities($_REQUEST["Item_comment"], ENT_QUOTES, 'UTF-8');
}
else
{
	$OrderByFieldName = "TermTitle";
	$OrderType = "";
	$TermTitle='';
	$comment='';
}
$res = manage_terms::Search($TermTitle, $comment, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->TermID]))
	{
		manage_terms::Remove($res[$k]->TermID);
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_terms::Search($TermTitle, $comment, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
?>
<form id="SearchForm" name="SearchForm" method=post>
    <div class="form-group row">
        <input type="hidden" name="PageNumber" id="PageNumber" value="0" class="form-control">
        <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" class="form-contro value="<?php echo $OrderByFieldName; ?>">
        <input type="hidden" name="OrderType" id="OrderType" class="form-contro value="<?php echo $OrderType; ?>">
        <input type="hidden" name="SearchAction" id="SearchAction" class="form-contro value="1">
    </div>
    <br>
    <table class="table table-bordered ">
        <thead>
            <tr class="table-info">
                <th >
                    <b><a class='btn btn-sm ' href="#" onclick='javascript: if(document.getElementById("SearchTr").style.display=="none") document.getElementById("SearchTr").style.display="";
                                                                            else document.getElementById("SearchTr").style.display="none";'>
                        <i class="fa fa-search"></i><?php echo C_SEARCH?>
                    </a>
            </th>
            </tr>
        </thead>
        <tbody id='SearchTr' style='display: none'>
            <tr >
                <td >
                    <div class="form-group row">
                        <label for="Item_TermTitle" class="col-sm-2 col-form-label"><?php echo C_TITLE?></label>
                        <div class="col-sm-10">
                            <input type="text" name="Item_TermTitle" id="Item_TermTitle" class="form-control">
                        </div>
                    </div>
	            </td>
            </tr>

            <tr>
	            <td>
                    <div class="form-group row">
                        <label for="Item_comment" class="col-sm-2 col-form-label"><?php echo C_NOTE?></label>
                        <div class="col-sm-10">
                            <input type="text" name="Item_comment" id="Item_comment" rows="2" class="form-control">
                        </div>
                    </div>
	            </td>

            </tr>
            <tr>
                <td colspan="2" class="text-center">
                    <input type="submit" class="btn btn-success" value=<?php echo C_SEARCH?>>
                </td>
            </tr>
    </table>
</form>
<?php
if(isset($_REQUEST["SearchAction"]))
{
?>
<script>
	document.SearchForm.Item_TermTitle.value='<?php echo htmlentities($_REQUEST["Item_TermTitle"], ENT_QUOTES, 'UTF-8'); ?>';
	document.SearchForm.Item_comment.value='<?php echo htmlentities($_REQUEST["Item_comment"], ENT_QUOTES, 'UTF-8'); ?>';
</script>
<?php
}
?>
<form id="ListForm" name="ListForm" method="post">
    <div class="form-group">
<?php if(isset($_REQUEST["PageNumber"]))
	echo "<input class='form-control' type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<?php if(isset($_REQUEST["SearchAction"])) { ?>
    <input class='form-control' type="hidden" name="SearchAction" id="SearchAction" value="1">
    <input class='form-control' type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<?php echo $OrderByFieldName; ?>">
    <input class='form-control' type="hidden" name="OrderType" id="OrderType" value="<?php echo $OrderType; ?>">
    <input class='form-control' type="hidden" name="Item_TermTitle" id="Item_TermTitle" value="<?php echo $_REQUEST["Item_TermTitle"]; ?>" >
    <input class='form-control' type="hidden" name="Item_comment" id="Item_comment" value="<?php echo $_REQUEST["Item_comment"]; ?>" >
    </div>
<?php } ?>

<br><table class="table table-striped table-bordered">
        <thead>
            <tr class="text-center table-info">
                <th colspan="9">
                <?php echo C_TERMS?>
                </th>
            </tr>
            <tr >
                <th>
                    <input type=checkbox name=CAll onclick='javascript: if(this.checked) CheckAll(); else UnCheckAll();'>
                </th>
                <th ><?php echo C_ROW ?></th>
                <th><?php echo C_EDIT ?></th>
                <th><a class='btn btn-sm' href="javascript: Sort('TermTitle', 'ASC');"><b><?php echo C_TITLE?></b></a></th>
                <th><a class='btn btn-sm' href="javascript: Sort('comment', 'ASC');"><b><?php echo C_NOTE?></b></a></th>
                <th><a class='btn btn-sm' href="javascript: Sort('CreatorUserID', 'ASC');"><b><?php echo C_CREATOR ?></b></a></th>
                <th><a class='btn btn-sm' href="javascript: Sort('CreateDate', 'ASC');"><b><?php echo C_CREATION_TIME ?></b></a></th>
                <th ><a class='btn btn-sm' href="javascript: Sort('ReferCount', 'ASC');"><b><?php echo C_REFERENCES_IN_WORDS ?></b></a></th>
                <th><?php echo C_ONTOLOGY_ELEMENT ?></th>
            </tr>
        </thead>
        <tbody>

<?php
for($k=0; $k<count($res); $k++)
{
  $CheckAllScript .= "document.getElementById(\"ch_".$res[$k]->TermID."\").checked = true;\r\n";
  $UnCheckAllScript .= "document.getElementById(\"ch_".$res[$k]->TermID."\").checked = false;\r\n";
//	if($res[$k]->ReferCount==0)
    echo "<tr >";
	echo "    <td>";
	echo "      <input type=\"checkbox\" name=\"ch_".$res[$k]->TermID."\" id=\"ch_".$res[$k]->TermID."\">";
	echo "    </td>";
	echo "    <td>".($k+$FromRec+1)."</td>";
	echo "	  <td><a class = 'btn btn-sm ' href=\"Manageterms.php?UpdateID=".$res[$k]->TermID;
	                if(isset($_REQUEST["SearchAction"]))
	                     echo "&SearchAction=1&Item_TermTitle=".$_REQUEST["Item_TermTitle"]."&Item_comment=".$_REQUEST["Item_comment"];
	                if(isset($_REQUEST["PageNumber"]))
	                    echo "&PageNumber=".$_REQUEST["PageNumber"];
	echo "          \"><i class=\"fa fa-edit\" ></i></a></td>";
	echo "	  <td>".htmlentities($res[$k]->TermTitle, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	  <td>".str_replace("\r", "<br>", htmlentities($res[$k]->comment, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	  <td>".htmlentities($res[$k]->CreatorUserID, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	  <td>".$res[$k]->CreateDate_Shamsi."</td>";
	echo "    <td><a  target=\"_blank\" href='ShowTermReferenceMapping.php?TermID=".$res[$k]->TermID ."'>";
	echo            $res[$k]->ReferCount;
	echo "        </a>
               </td>";
	echo "	  <td><a href='TermOntologyPage.php?TermID=".$res[$k]->TermID."' target=_blank>";
	                $ElementName = GetRelatedOntologyElement($res[$k]->TermID);
                    if($ElementName=="")
                      echo C_RECORD;
                    else
                      echo $ElementName;
	echo "          </a>
              </td>";
	echo "</tr>";
}
?>
        <tr>
            <td colspan="9" class="text-center">
	            <input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value=<?php echo C_DELETE?>>
	            <input type="button" class="btn btn-success" onclick="javascript: window.open('TermFrequency.php');" value=<?php echo '"'.C_STATISTICAL_ANALYSIS.'"'?>>
            </td>
        </tr>
        <tr >
            <td colspan="9" class="text-center" ">
<!--            i couldn't understand why we do need this?-->
                <?php
                    $TotalCount = manage_terms::SearchResultCount($TermTitle, $comment, "");
                    for($k=0; $k<$TotalCount/$NumberOfRec; $k++)
                    {
                        if($PageNumber!=$k)
                            echo "<a href='javascript: ShowPage(".($k).")'>";
                        echo ($k+1);
                        if($PageNumber!=$k)
                            echo "</a>";
                        echo " ";
                    }
                ?>
            </td>
        </tr>
    </tbody>
    </table>
</form>
<form target="_blank" method="post" action="Newterms.php" id="NewRecordForm" name="NewRecordForm">
</form>
</div>
<script>
function ConfirmDelete()
{
	if(confirm(<?php echo "'".C_CONFIRM_TO_DELETE."'"?>)) document.ListForm.submit();
}
function ShowPage(PageNumber)
{
	SearchForm.PageNumber.value=PageNumber;
	SearchForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	SearchForm.OrderByFieldName.value=OrderByFieldName;
	SearchForm.OrderType.value=OrderType;
	SearchForm.submit();
}
function ShowDetails(TermID)
{
  document.getElementById('DetailsSpan').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  // '<img src="images/ajax-loader.gif">';
  var params = "Ajax=1&TermID="+TermID;
  var http = new XMLHttpRequest();
  http.open("POST", "ManageTermReferenceMapping.php", true);
  //Send the proper header information along with the request
    //******XMLHttpRequest isn't allowed to set these headers, they are being set automatically by the browser.
  // http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  // http.setRequestHeader("Content-length", params.length);
  //http.setRequestHeader("Connection", "close");

  http.onreadystatechange = function()
  {//Call a function when the state changes.
    if(http.readyState === 4 && http.status === 200)
    {
         document.getElementById('DetailsSpan').innerHTML = http.responseText;
    }
  }
  http.send(params);
}
function MergeSuggestions(TermID)
{
  document.location='Manageterms.php?Merge=1&TermID='+TermID;
}

function CheckAll()
{
<?php echo $CheckAllScript; ?>
}

function UnCheckAll()
{
<?php echo $UnCheckAllScript; ?>
}

<?php
    if(isset($_REQUEST["UpdateID"])) { echo "ShowDetails(".$_REQUEST["UpdateID"].");"; } ?>
</script>
