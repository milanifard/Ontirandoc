<?
	include "header.inc.php";
	$OntologyClassID = $_REQUEST["OntologyClassID"];
	$TargetOntologyID = 61;
	$mysql = pdodb::getInstance();
	$mysql->Execute("insert into projectmanagement.OntologyClasses (OntologyID, ClassTitle) select ".$TargetOntologyID.", ClassTitle from projectmanagement.OntologyClasses where OntologyClassID=".$OntologyClassID);
	$res = $mysql->Execute("select max(OntologyClassID) as NewOntologyClassID from projectmanagement.OntologyClasses");
	$rec = $res->fetch();
	$NewOntologyClassID = $rec["NewOntologyClassID"];
	$res = $mysql->Execute("select ClassTitle from projectmanagement.OntologyClasses where OntologyClassID=".$NewOntologyClassID);
	$rec = $res->fetch();
	$ClassTitle = $rec["ClassTitle"];
	$mysql->Execute("insert into projectmanagement.OntologyClassLabels (OntologyClassID, label) select ".$NewOntologyClassID.", label from projectmanagement.OntologyClassLabels where OntologyClassID=".$OntologyClassID);
	
	$res = $mysql->Execute("select * from projectmanagement.OntologyProperties where 
				(domain='".$ClassTitle."' or domain like '".$ClassTitle.",%' or domain like '%, ".$ClassTitle."' or domain like '%, ".$ClassTitle.",%') or
				(`range`='".$ClassTitle."' or `range` like '".$ClassTitle.",%' or `range` like '%, ".$ClassTitle."' or `range` like '%, ".$ClassTitle.",%') ");
	while($rec = $res->fetch())
	{
		$query = "select * from projectmanagement.OntologyProperties where OntologyID=".$TargetOntologyID." and PropertyTitle='".$rec["PropertyTitle"]."'";
		$res2 = $mysql->Execute($query);
		if($rec2 = $res2->fetch())
		{
		}
		else
		{
			$query = "insert into projectmanagement.OntologyProperties (OntologyID, PropertyTitle, domain, `range`, PropertyType) values ('".$TargetOntologyID."', '".$rec["PropertyTitle"]."', '".$rec["domain"]."', '".$rec["range"]."', '".$rec["PropertyType"]."')";
			$mysql->Execute($query);
			$res3 = $mysql->Execute("select max(OntologyPropertyID) as NewOntologyPropertyID from projectmanagement.OntologyProperties");
			$rec3 = $res3->fetch();
			$NewOntologyPropertyID = $rec3["NewOntologyPropertyID"];
			$mysql->Execute("insert into projectmanagement.OntologyPropertyLabels (OntologyPropertyID, label) select ".$NewOntologyPropertyID.", label from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=".$rec["OntologyPropertyID"]);			
		}
	}
?>