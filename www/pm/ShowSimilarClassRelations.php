<?
	include "header.inc.php";
	
	function ShowSimilarRelation($OntologyID, $PropID, $DomainClassID, $RangeClassID)
	{
		$mysql = pdodb::getInstance();
		$query = "select OntologyProperties.OntologyPropertyID, DomainClassID, RangeClassID, 
		l1.label as DomainLabel, l2.label as RangeLabel, OntologyPropertyLabels.label as PropLabel 
		from projectmanagement.OntologyObjectPropertyRestriction 
			JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
			JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
			JOIN projectmanagement.OntologyClasses c1 on (c1.OntologyClassID=DomainClassID)
			JOIN projectmanagement.OntologyClasses c2 on (c1.OntologyClassID=RangeClassID)
			JOIN projectmanagement.OntologyClassLabels l1 on (c1.OntologyClassID=l1.OntologyClassID)
			JOIN projectmanagement.OntologyClassLabels l2 on (c2.OntologyClassID=l2.OntologyClassID)
				where OntologyProperties.OntologyID=? and ((DomainClassID=? and RangeClassID=?) or (DomainClassID=? and RangeClassID=?)) 
				and OntologyProperties.OntologyPropertyID<>?  and RelationStatus='VALID'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($DomainClassID, $RangeClassID, $RangeClassID, $DomainClassID, $PropID));
		while($rec = $res->fetch())
		{
			echo $rec["DomainLabel"]." <b>".$rec["PropLabel"]."</b> ".$rec["RangeLabel"]." - ";
		}		
	}
	HTMLBegin();
	$mysql = pdodb::getInstance();
	$OntologyID = $_REQUEST["OntologyID"];
	$query = "select OntologyProperties.OntologyPropertyID, DomainClassID, RangeClassID, l1.label as DomainLabel, l2.label as RangeLabel, OntologyPropertyLabels.label as PropLabel from projectmanagement.OntologyObjectPropertyRestriction 
			JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
			JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
			JOIN projectmanagement.OntologyClasses c1 on (c1.OntologyClassID=DomainClassID)
			JOIN projectmanagement.OntologyClasses c2 on (c2.OntologyClassID=RangeClassID)
			JOIN projectmanagement.OntologyClassLabels l1 on (c1.OntologyClassID=l1.OntologyClassID)
			JOIN projectmanagement.OntologyClassLabels l2 on (c2.OntologyClassID=l2.OntologyClassID)
			where OntologyProperties.OntologyID=? and RelationStatus='VALID'";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID));
	echo "<table border=1 cellspacing=0 cellpadding=5 width=98% align=center>";
	while($rec = $res->fetch())
	{
		$PropID = $rec["OntologyPropertyID"];
		$DomainClassID = $rec["DomainClassID"];
		$RangeClassID = $rec["RangeClassID"];
		//echo $PropID." - ".$DomainClassID." - ".$RangeClassID."<br>";
		
		$query = "select OntologyProperties.OntologyPropertyID, DomainClassID, RangeClassID, l1.label as DomainLabel, l2.label as RangeLabel, OntologyPropertyLabels.label as PropLabel from projectmanagement.OntologyObjectPropertyRestriction 
			JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
			JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
			JOIN projectmanagement.OntologyClasses c1 on (c1.OntologyClassID=DomainClassID)
			JOIN projectmanagement.OntologyClasses c2 on (c2.OntologyClassID=RangeClassID)
			JOIN projectmanagement.OntologyClassLabels l1 on (c1.OntologyClassID=l1.OntologyClassID)
			JOIN projectmanagement.OntologyClassLabels l2 on (c2.OntologyClassID=l2.OntologyClassID)
				where OntologyProperties.OntologyID=? and ((DomainClassID=? and RangeClassID=?) or (DomainClassID=? and RangeClassID=?)) and OntologyProperties.OntologyPropertyID<>?  and RelationStatus='VALID'";
		$mysql->Prepare($query);
		$res2 = $mysql->ExecuteStatement(array($OntologyID, $DomainClassID, $RangeClassID, $RangeClassID, $DomainClassID, $PropID));
		
		if($res2->rowCount()>0)
		{
			echo "<tr>";
			echo "<td>";
			echo $rec["DomainLabel"]." <b><a href='ManageOntologyProperties.php?UpdateID=".$rec["OntologyPropertyID"]."&OntologyID=".$OntologyID."&DoNotShowList=1' target=_blank>".$rec["PropLabel"]."</a></b> ".$rec["RangeLabel"];
			echo "</td>";
			echo "<td>";
			while($rec2 = $res2->fetch())
			{
				echo $rec2["DomainLabel"]." <b><a href='ManageOntologyProperties.php?UpdateID=".$rec2["OntologyPropertyID"]."&OntologyID=".$OntologyID."&DoNotShowList=1' target=_blank>".$rec2["PropLabel"]."</a></b> ".$rec2["RangeLabel"]." - ";
			}
			echo "</td>";
			echo "</tr>";
		}
		
	}
?>