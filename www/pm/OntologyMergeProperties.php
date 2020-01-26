<?php
	include("header.inc.php");
	
	function ShowClassesLabel($ClassList)
	{
	  $ret = "";
	  $mysql = pdodb::getInstance();
	  $classes = explode(",", $ClassList);
	  for($i=0; $i<count($classes); $i++)
	  {
	    $res = $mysql->Execute("select * from projectmanagement.OntologyClasses 
					    JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) 
					    where ClassTitle='".$classes[$i]."'");
	    if($rec = $res->fetch())
	    {
	      if($ret!="")
		$ret .= " - ";
	      $ret .= $rec["ClassTitle"]." ( ".$rec["label"]." ) ";
	    }
	  }
	  return $ret;
	}
	
	function SetValidRelation($OntologyPropertyID, $DomainClassID, $RangeClassID)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select OntologyObjectPropertyRestrictionID from projectmanagement.OntologyObjectPropertyRestriction 
			where OntologyPropertyID=? and DomainClassID=? and RelationStatus=?";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($OntologyPropertyID, $DomainClassID, $RangeClassID));
	  if($rec=$res->fetch())
	  {
	    $query = "update projectmanagement.OntologyObjectPropertyRestriction set RelationStatus='VALID'  
			  where OntologyObjectPropertyRestrictionID=?";
	    $mysql->Prepare($query);
	    $mysql->ExecuteStatement(array($rec["OntologyObjectPropertyRestrictionID"]));
	  }
	  else
	  {
	    $query = "insert into projectmanagement.OntologyObjectPropertyRestriction 
			(OntologyPropertyID, DomainClassID, RangeClassID, RelationStatus) values (?,?,?,'VALID')";
			
	    $mysql->Prepare($query);
	    $mysql->ExecuteStatement(array($OntologyPropertyID, $DomainClassID, $RangeClassID));
	  }
	}
	
	function ShowPropertyMergeSuggestions($TargetOnto)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select OntologyMergeReviewedPotentialID
			  ,p1.OntologyPropertyID as PropertyID1
			  ,l1.label as PropertyLabel1
			  ,p2.OntologyPropertyID as PropertyID2
			  ,l2.label as PropertyLabel2
			  ,p1.domain as PropertyDomain1
			  ,p1.range as PropertyRange1
			  ,p2.domain as PropertyDomain2
			  ,p2.range as PropertyRange2
			  ,(select group_concat(PermittedValue) from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyPermittedValues.OntologyPropertyID=EntityID1) as PermittedValues1
			  ,(select group_concat(PermittedValue) from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyPermittedValues.OntologyPropertyID=EntityID2) as PermittedValues2
			  from projectmanagement.OntologyMergeReviewedPotentials 
			  JOIN projectmanagement.OntologyProperties p1 on (p1.OntologyPropertyID=EntityID1)
			  JOIN projectmanagement.OntologyPropertyLabels l1 on (l1.OntologyPropertyID=p1.OntologyPropertyID)
			  JOIN projectmanagement.OntologyProperties p2 on (p2.OntologyPropertyID=EntityID2)
			  JOIN projectmanagement.OntologyPropertyLabels l2 on (l2.OntologyPropertyID=p2.OntologyPropertyID)
			  where TargetOntologyID=? and (EntityType1='OBJPROP' or EntityType1='DATAPROP') and ActionType='NOT_DECIDE' limit 0,30";
;
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($TargetOnto));
	  $i=0;
	  while($rec = $res->fetch())
	  {
	    $i++;
	    $MergeID = $rec["OntologyMergeReviewedPotentialID"];
	    if($i%2==0)
	    	echo "<tr class=OddRow>";
	    else
	    	echo "<tr class=EvenRow>";
	    echo "<td>".$i."</td>";
	    echo "<td>";
	    echo "<select id=ch_".$MergeID." name=ch_".$MergeID.">";
	    echo "<option value='Merge'>ادغام";
	    echo "<option value='DoNotMerge' ";
		if($rec["PermittedValues1"]!=$rec["PermittedValues2"])
			echo " selected ";
	    echo ">عدم ادغام";	    	    
	    echo "</select>";
	    echo "</td>";
	    echo "<td>";
	    echo "<a target=_blank href='ManageOntologyProperties.php?UpdateID=".$rec["PropertyID1"]."&OntologyID=".$TargetOnto."&DoNotShowList=1'>";
	    echo $rec["PropertyLabel1"];
	    echo "</a>";
	    echo "</td>";
	    echo "<td>".ShowClassesLabel($rec["PropertyDomain1"])."</td>";
	    echo "<td>".ShowClassesLabel($rec["PropertyRange1"])."</td>";
	    echo "<td>".$rec["PermittedValues1"]."</td>";
	    echo "</tr>";
	    
	    if($i%2==0)
	    	echo "<tr class=OddRow>";
	    else
	    	echo "<tr class=EvenRow>";

	    echo "<td colspan=2>&nbsp;</td>";
	    echo "<td>";
	    echo "<a target=_blank href='ManageOntologyProperties.php?UpdateID=".$rec["PropertyID2"]."&OntologyID=".$TargetOnto."&DoNotShowList=1'>";
	    echo $rec["PropertyLabel2"];
	    echo "</a>";
	    echo "</td>";
	    echo "<td>".ShowClassesLabel($rec["PropertyDomain2"])."</td>";
	    echo "<td>".ShowClassesLabel($rec["PropertyRange2"])."</td>";
	    echo "<td>".$rec["PermittedValues2"]."</td>";
	    echo "</tr>";
	  }
	}
	
	function GetClassID($ClassTitle, $TargetOnto)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select OntologyClassID from projectmanagement.OntologyClasses 
			where ClassTitle=? and OntologyID=?";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($ClassTitle, $TargetOnto));
	  if($rec = $res->fetch())
	  {
	    return $rec["OntologyClassID"];
	  }
	  return 0;
	}
	
	// ممکن است از قبل یکی از خصوصیت با خصوصیت دیگری ادغام شده و دیگر وجود نداشته باشد
	function IsValidMerge($PropertyID1, $PropertyID2)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select count(*) as tcount from projectmanagement.OntologyProperties 
			where OntologyPropertyID=? or OntologyPropertyID=?";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($PropertyID1, $PropertyID2));
	  if($rec = $res->fetch())
	  {
	    if($rec["tcount"]==2)
	    	return true;
	  }
	  return false;
	}
	
	function MergeTwoProperty($TargetOnto, $PropertyID1, $PropertyID2, $PropertyDomain2, $PropertyRange2)
	{
		$mysql = pdodb::getInstance();
	      $query = "update projectmanagement.OntologyProperties set domain=concat(domain,', ', '".$PropertyDomain2."')  where OntologyPropertyID=".$PropertyID1." 
	      and domain not like '%".$PropertyDomain2."%'";
	      $mysql->Execute($query);
	      if($PropertyRange2!="")
	      {
		$query = "update projectmanagement.OntologyProperties set `range`=concat(`range`,', ', '".$PropertyRange2."') where OntologyPropertyID=".$PropertyID1."
	    and `range` not like '%".$PropertyRange2."%'";
		$mysql->Execute($query);
	      }
	      
	      $DomainClassID = GetClassID($PropertyDomain2, $TargetOnto);
	      $RangeClassID = GetClassID($PropertyRange2, $TargetOnto);
	      if($DomainClassID!="0" && $RangeClassID!="0")
		SetValidRelation($PropertyID1, $DomainClassID, $RangeClassID);
	      
	      $query = "delete from projectmanagement.OntologyProperties where OntologyPropertyID=".$PropertyID2;
	      $mysql->Execute($query);
	      $query = "delete from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=".$PropertyID2;
	      $mysql->Execute($query);
	      $query = "delete from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID=".$PropertyID2;
	      $mysql->Execute($query);
	
	}
	
	function SetMergeSuggestionRecordStatus($MergeID, $status)
	{
		$mysql = pdodb::getInstance();
		$mysql->Execute("update projectmanagement.OntologyMergeReviewedPotentials set ActionType='".$status."' where OntologyMergeReviewedPotentialID=".$MergeID);	
	}
	
	function DoPropertyMerge($TargetOnto)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select OntologyMergeReviewedPotentialID
			  ,p1.OntologyPropertyID as PropertyID1
			  ,l1.label as PropertyLabel1
			  ,p2.OntologyPropertyID as PropertyID2
			  ,l2.label as PropertyLabel2
			  ,p1.domain as PropertyDomain1
			  ,p1.range as PropertyRange1
			  ,p2.domain as PropertyDomain2
			  ,p2.range as PropertyRange2
			  ,(select group_concat(PermittedValue) from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyPermittedValues.OntologyPropertyID=EntityID1) as PermittedValues1
			  ,(select group_concat(PermittedValue) from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyPermittedValues.OntologyPropertyID=EntityID2) as PermittedValues2
			  from projectmanagement.OntologyMergeReviewedPotentials 
			  JOIN projectmanagement.OntologyProperties p1 on (p1.OntologyPropertyID=EntityID1)
			  JOIN projectmanagement.OntologyPropertyLabels l1 on (l1.OntologyPropertyID=p1.OntologyPropertyID)
			  JOIN projectmanagement.OntologyProperties p2 on (p2.OntologyPropertyID=EntityID2)
			  JOIN projectmanagement.OntologyPropertyLabels l2 on (l2.OntologyPropertyID=p2.OntologyPropertyID)
			  where TargetOntologyID=? and (EntityType1='OBJPROP' or EntityType1='DATAPROP') and ActionType='NOT_DECIDE'";
;
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($TargetOnto));
	  $i=0;
	  while($rec = $res->fetch())
	  {
	    $i++;
	    $MergeID = $rec["OntologyMergeReviewedPotentialID"];
	    if(isset($_REQUEST["ch_".$MergeID]))
	    {
		if($_REQUEST["ch_".$MergeID]=="Merge")	    
		{
		
		    	if(IsValidMerge($rec["PropertyID1"], $rec["PropertyID2"]))
		    	{
			     	MergeTwoProperty($TargetOnto, $rec["PropertyID1"], $rec["PropertyID2"], $rec["PropertyDomain2"], $rec["PropertyRange2"]); 
				SetMergeSuggestionRecordStatus($MergeID, "MERGE");
			}
			else SetMergeSuggestionRecordStatus($MergeID, "NOT_MERGE");
		}
		else SetMergeSuggestionRecordStatus($MergeID, "NOT_MERGE");
	     }
	  }
	}
	
	$TargetOnto = "";
	if(isset($_REQUEST["DoMerge"]))
	{
		$TargetOnto = $_REQUEST["TargetOnto"];
	  DoPropertyMerge($TargetOnto);
	}
	
	
	echo "<form method=post>";
	echo "<input type=hidden name=DoMerge id=DoMerge value=1>";
	echo "<input type=hidden name=TargetOnto id=TargetOnto value='".$TargetOnto."'>";
	
	echo '<div class="container">';
	echo '<table class="table table-bordered">';
	echo "<tr class=HeaderOfTable><td colspan=10 align=center>پیشنهادات ادغام</td></tr>";
	echo "<tr bgcolor=#cccccc align=center><td width=1%>ردیف</td><td>خصوصیت </td><td>دامنه </td><td>برد</td><td>مقادیر مجاز </td>";
	//echo "<td>خصوصیت ۲</td><td>دامنه خصوصیت ۲</td><td>برد خصوصیت ۲</td><td>مقادیر مجاز ۲</td>";
	echo "</tr>";
	ShowPropertyMergeSuggestions($TargetOnto);	
	echo "<tr class=FooterOfTable><td colspan=10 align=center><input type=submit value='اعمال'></td></tr>";
	echo "</table>";
	echo "</div>";
	
	echo "</form>";
	HTMLBegin();
?>


<<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body style="direction:rtl">
</body>
</html>

