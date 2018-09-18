<?php
	include "header.inc.php";
	include "classes/OntologyClasses.class.php";

	function ShowPropValues($OntologyID, $OntologyPropertyID)
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("select * from projectmanagement.OntologyPropertyPermittedValues
		where OntologyPropertyID=?");
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));
		$ValuesCount = $res->rowCount();
		if($ValuesCount>0)
			echo "(";
		$i=0;
		while($rec = $res->fetch())
		{
			if($i>0)
				echo ", ";
			$i++;
			echo $rec["PermittedValue"];
echo "<a href='javascript: Select(\"DATA_PROP\", \"".$rec["OntologyPropertyPermittedValueID"]."\", \"".$rec["PermittedValue"]."\");'>";
			echo "<img src='images/down.gif' border=0>";
			echo "</a> ";
			
		}
		if($ValuesCount>0)
			echo ")";
	}
	
	function ShowClassProp($OntologyID, $ClassTitle)
	{
		$ClassID = manage_OntologyClasses::GetClassID($OntologyID, $ClassTitle);
		$res = manage_OntologyClasses::GetClassRelatedProperties($ClassTitle, $OntologyID);
		$mysql = pdodb::getInstance();
		
		echo "<table>";
		for($i=0; $i<count($res); $i++)
		{
			if($res[$i]["PropertyType"]=="DATATYPE")
			{
				echo "<tr>";
				echo "<td>";
				echo " &nbsp;&nbsp;&nbsp;&nbsp; ";
				echo "<a target=_blank href='ManageOntologyProperties.php?UpdateID=".$res[$i]["PropertyID"]."&OntologyID=".$OntologyID."&DoNotShowList=1'>";
				echo $res[$i]["PropertyLabel"];
				echo "</a> ";
				echo "<a href='javascript: Select(\"PROP\", \"".$res[$i]["PropertyID"]."\", \"".$res[$i]["PropertyLabel"]."\");'>";
				echo "<img src='images/down.gif' border=0>";
				echo "</a> ";
	
				ShowPropValues($OntologyID, $res[$i]["PropertyID"]);
				echo "</td>";
				echo "</tr>";
			}
			else
			{
				echo "<tr>";
				echo "<td>";
				echo " &nbsp;&nbsp;&nbsp;&nbsp; ";
			
				$mysql->Prepare("select * from projectmanagement.OntologyObjectPropertyRestriction 
				JOIN projectmanagement.OntologyClassLabels on (OntologyClassID=DomainClassID) 
where RangeClassID=? and OntologyObjectPropertyRestriction.OntologyPropertyID=? and RelationStatus='VALID'");
				$domains = $mysql->ExecuteStatement(array($ClassID, $res[$i]["PropertyID"]));
				while($drec = $domains->fetch())
				{
					echo $drec["label"]." ";
				}
				echo "<a target=_blank href='ManageOntologyProperties.php?UpdateID=".$res[$i]["PropertyID"]."&OntologyID=".$OntologyID."&DoNotShowList=1'>";
				echo $res[$i]["PropertyLabel"];
				echo "</a> ";

				$mysql->Prepare("select * from projectmanagement.OntologyObjectPropertyRestriction 
				JOIN projectmanagement.OntologyClassLabels on (OntologyClassID=RangeClassID) 
where DomainClassID=? and OntologyObjectPropertyRestriction.OntologyPropertyID=? and RelationStatus='VALID'");
				$domains = $mysql->ExecuteStatement(array($ClassID, $res[$i]["PropertyID"]));
				while($drec = $domains->fetch())
				{
					echo $drec["label"]." ";
				}


echo "<a href='javascript: Select(\"PROP\", \"".$res[$i]["PropertyID"]."\", \"".$res[$i]["PropertyLabel"]."\");'>";
				echo "<img src='images/down.gif' border=0>";
				echo "</a> ";				
				echo "</td>";
				echo "</tr>";
			}
		}
		
		echo "</table>";
	}
	
	function ShowClasses($OntologyID)
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("select * from projectmanagement.OntologyClasses
		JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
		where OntologyID=? order by label");
		$res = $mysql->ExecuteStatement(array($OntologyID));
		echo "<table>";
		echo "<tr>";
		echo "<td>";
		echo "<a href='javascript: Select(\"CLASS\", \"0\", \"<font color=red>معادل ندارد</font>\");'>";
		echo "<font color=red><b>معادل ندارد</b></font></a>";
		echo "</tr>";		
		
		while($rec = $res->fetch())
		{
			echo "<tr>";
			echo "<td>";
			echo "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$rec["OntologyClassID"]."&OntologyID=".$OntologyID."&OnlyEditForm=1'>";
			echo "<b>".$rec["label"]."</b>";
			echo "</a> ";
			echo "<a href='javascript: Select(\"CLASS\", \"".$rec["OntologyClassID"]."\", \"".$rec["label"]."\");'>";
			echo "<img src='images/down.gif' border=0>";
			echo "</a>";
			ShowClassProp($OntologyID, $rec["ClassTitle"]);
			echo "</td>";
			echo "</tr>";
		}
		echo "<tr>";
		echo "<td>";
		echo "<a href='javascript: Select(\"CLASS\", \"0\", \"<font color=red>معادل ندارد</font>\");'>";
		echo "<font color=red><b>معادل ندارد</b></font></a>";
		echo "</tr>";		
		echo "</table>";
	}
	HTMLBegin();
	ShowClasses($_REQUEST["OntologyID"]);
?>
<script>
	function Select(EntityType, EntityID, EntityLabel)
	{
		var params = "Ajax=1&OntologyID1=<? echo $_REQUEST["MapOntologyID"] ?>&";
		params += "SourceEntityType=<? echo $_REQUEST["MapEntity"] ?>&";
		params += "SourceID=<? echo $_REQUEST["MapID"] ?>&";
		params += "OntologyID2=<? echo $_REQUEST["OntologyID"] ?>&";
		params += "EntityID="+EntityID+"&";
		params += "EntityType="+EntityType;
		var http = new XMLHttpRequest();
		http.open("POST", "CompareOntologies.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.setRequestHeader("Content-length", params.length);
		http.setRequestHeader("Connection", "close");
		  
		http.onreadystatechange = function()
		{
			if(http.readyState == 4 && http.status == 200)
			{ 
				window.opener.document.getElementById('span_<? echo $_REQUEST["MapID"] ?>').innerHTML='<b>'+EntityLabel+'</b>';
				window.close(); 				
			}
		}
		http.send(params);	
	}
</script>
