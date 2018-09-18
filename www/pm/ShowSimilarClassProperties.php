<?
	include "header.inc.php";
	
	function GetClassID($ClassTitle, $OntologyID)
	{
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClasses where ClassTitle=? and OntologyID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ClassTitle, $OntologyID));
		if($rec = $res->fetch())
		{
			return $rec["OntologyClassID"];
		}
		return 0;
	}

	function GetClassTitle($ClassID)
	{
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClasses where OntologyClassID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ClassID));
		if($rec = $res->fetch())
		{
			return $rec["ClassTitle"];
		}
		return "";
	}

	
	function HasTwoClassHirarchyRelation($ClassID1, $ClassID2)
	{
		//echo "Class1: ".$ClassID1." Class2: ".$ClassID2."<br>";
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClassHirarchy where OntologyClassID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ClassID1));
		while($rec = $res->fetch())
		{
			$OntologyClassParentID = $rec["OntologyClassParentID"];
			if($OntologyClassParentID==$ClassID2)
				return true;
			if(HasTwoClassHirarchyRelation($OntologyClassParentID, $ClassID2))
				return true;
		}		
		return false;
	}
	HTMLBegin();
	$mysql = pdodb::getInstance();
	//HasTwoClassHirarchyRelation(705651, 70654);
	//die();
	$OntologyID = $_REQUEST["OntologyID"];
	$query = "select * from 
			projectmanagement.OntologyProperties 
			JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
			where PropertyType='DATATYPE' and OntologyID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID));

	echo "<table border=1 cellspacing=0 cellpadding=5 width=98% align=center>";
	echo "<tr class=HeaderOfTable><td colspan=2>خصوصیاتی که برای دو کلاس دارای رابطه سلسله مراتبی تکرار شده اند";
	echo "</tr>";
	while($rec = $res->fetch())
	{
		$PropID = $rec["OntologyPropertyID"];
		$DomainClasses = explode(", ", $rec["domain"]);
		for($i=0; $i<count($DomainClasses); $i++)
		{
			for($j=$i+1; $j<count($DomainClasses); $j++)
			{
				$ClassID1 = GetClassID($DomainClasses[$i], $OntologyID);
				$ClassID2 = GetClassID($DomainClasses[$j], $OntologyID);
				
				if(HasTwoClassHirarchyRelation($ClassID1, $ClassID2) || HasTwoClassHirarchyRelation($ClassID2, $ClassID1))
				{
				
					echo "<tr>";
					echo "<td>";
					
					echo "<b><a href='ManageOntologyProperties.php?UpdateID=".$rec["OntologyPropertyID"]."&OntologyID=".$OntologyID."&DoNotShowList=1' target=_blank>";
					echo $rec["label"]."</a></b>";
					echo "</td>";
					echo "<td>";
					echo "<a href='ManageOntologyClasses.php?UpdateID=".$ClassID1."&OntologyID=".$OntologyID."&OnlyEditForm=1' target=_blank>";
					echo $DomainClasses[$i];
					echo "</a>";
					echo " - ";
					echo "<a href='ManageOntologyClasses.php?UpdateID=".$ClassID2."&OntologyID=".$OntologyID."&OnlyEditForm=1' target=_blank>";					
					echo $DomainClasses[$j];
					echo "</a>";
					echo "</td>";
					echo "</tr>";
						
				}
			}
		}
	}
	

	$query = "SELECT OntologyPropertyID, group_concat(DomainClassID) as domains, RangeClassID as TargetClassID, label from 
			projectmanagement.OntologyObjectPropertyRestriction
			JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
			JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
			where OntologyID=? and RelationStatus='VALID'
			group by OntologyPropertyID, RangeClassID
			having count(*)>1
		union
		SELECT OntologyPropertyID, group_concat(RangeClassID) as domains, DomainClassID as TargetClassID, label from 
			projectmanagement.OntologyObjectPropertyRestriction
			JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
			JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
			where OntologyID=? and RelationStatus='VALID'
			group by OntologyPropertyID, DomainClassID
			having count(*)>1
		";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID, $OntologyID));
	echo "<br><br>";
	echo "<table border=1 cellspacing=0 cellpadding=5 width=98% align=center>";
	echo "<tr class=HeaderOfTable><td colspan=2>رابطه ای که برای دو کلاس دارای رابطه سلسله مراتبی تکرار شده اند";
	echo "</tr>";

	while($rec = $res->fetch())
	{
		$PropID = $rec["OntologyPropertyID"];
		$DomainClasses = explode(",", $rec["domains"]);

		for($i=0; $i<count($DomainClasses); $i++)
		{
			for($j=$i+1; $j<count($DomainClasses); $j++)
			{
				$ClassID1 = $DomainClasses[$i];
				$ClassID2 = $DomainClasses[$j];
				
				if(HasTwoClassHirarchyRelation($ClassID1, $ClassID2) || HasTwoClassHirarchyRelation($ClassID2, $ClassID1))
				{
				
					echo "<tr>";
					echo "<td>";
					
					echo "<b><a href='ManageOntologyProperties.php?UpdateID=".$rec["OntologyPropertyID"]."&OntologyID=".$OntologyID."&DoNotShowList=1' target=_blank>";
					echo $rec["label"]."</a></b>";
					echo "</td>";
					echo "<td>";
					echo "<a href='ManageOntologyClasses.php?UpdateID=".$ClassID1."&OntologyID=".$OntologyID."&OnlyEditForm=1' target=_blank>";
					echo GetClassTitle($DomainClasses[$i]);
					echo "</a>";
					echo " - ";
					echo "<a href='ManageOntologyClasses.php?UpdateID=".$ClassID2."&OntologyID=".$OntologyID."&OnlyEditForm=1' target=_blank>";					
					echo GetClassTitle($DomainClasses[$j]);
					echo "</a>";
					echo " --> ".GetClassTitle($rec["TargetClassID"]);
					echo "</td>";
					echo "</tr>";
						
				}
			}
		}
		
	}	
	//echo "TAMAM";
?>