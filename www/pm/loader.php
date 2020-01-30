<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', "on");
ini_set('memory_limit', '100M');

include "header.inc.php";
include "classes/ontologies.class.php";
require_once("OWLLib.php");
require_once("reader/OWLReader.php");
require_once("memory/OWLMemoryOntology.php");

HTMLBegin();

$OntologyID = $_REQUEST["OntologyID"];
$obj = new be_ontologies();
$obj->LoadDataFromDatabase($OntologyID);
$reader = new OWLReader();
$ontology = new OWLMemoryOntology();
$reader->readFromMemory($obj->OntologyTitle, $obj->FileContent, $ontology);

$id = 0;

$NameSpace = $ontology->getNamespace();

echo $NameSpace;
$mysql = pdodb::getInstance();
//die();

$classes = $ontology->getAllClasses();
for ($i = 0; $i < count($classes); $i++) {
	$ClassID = $classes[$i]->getID();
	if (strpos($ClassID, "http://purl.obolibrary.org/obo") === 0) {
		$res = $mysql->Execute("select OntologyClassID from projectmanagement.OntologyClasses where OntologyID=" . $OntologyID . " and ClassTitle='" . $ClassID . "'");
		if ($rec = $res->fetch()) {
			$query = "insert into projectmanagement.OntologyClassLabels (OntologyClassID, label) values ('" . $rec["OntologyClassID"] . "', '" . $classes[$i]->getLabel("en") . "')";
			$mysql->Execute($query);
			echo "ClassID: " . $rec["OntologyClassID"] . " -> " . $classes[$i]->getLabel("en");
		} else
			echo "ClassID: " . $ClassID . " not fount";
		echo "<br>";
	}
}

$props = $ontology->getAllProperties();
for ($i = 0; $i < count($props); $i++) {
	$PropertyID = $props[$i]->getID();
	if (strpos($PropertyID, "http://purl.obolibrary.org/obo") === 0) {
		$res = $mysql->Execute("select OntologyPropertyID from projectmanagement.OntologyProperties where OntologyID=" . $OntologyID . " and PropertyTitle='" . $PropertyID . "'");
		if ($rec = $res->fetch()) {
			$query = "insert into projectmanagement.OntologyPropertyLabels (OntologyPropertyID, label) values ('" . $rec["OntologyPropertyID"] . "', '" . $props[$i]->getLabel("en") . "')";
			$mysql->Execute($query);
			echo "PropertyID: " . $rec["OntologyPropertyID"] . " -> " . $props[$i]->getLabel("en");
		} else
			echo "PropID: " . $PropID . " not fount";
		echo "<br>";
	}
}

$mysql->Execute("delete from projectmanagement.OntologyClassHirarchy where OntologyClassID in (select OntologyClassID from projectmanagement.OntologyClasses where OntologyID=" . $OntologyID . ")");
$mysql->Execute("delete from projectmanagement.OntologyClasses where OntologyID=" . $OntologyID);
$mysql->Execute("delete from projectmanagement.OntologyProperties where OntologyID=" . $OntologyID);



echo "<div class'row'></div>"
echo "<div class'col-1'></div>"
echo "<div class'col-10'>"
echo "<table class='table table-sm table-borderless' dir=ltr>";
echo "<tr bgcolor=#cccccc><td align=center><b>classes: </b></td></tr>";
$classes = $ontology->getAllClasses();
for ($i = 0; $i < count($classes); $i++) {
	echo "<tr>";
	echo "<td>";
	$ClassID = $classes[$i]->getID();
	$ClassName = str_replace($NameSpace, "", $ClassID);

	$mysql->Execute("insert into projectmanagement.OntologyClasses (ClassTitle, OntologyID) values ('" . str_replace("'", "", $ClassName) . "', '" . $OntologyID . "')");
	echo $ClassName;

	echo "</td>";
	echo "</tr>";
}

for ($i = 0; $i < count($classes); $i++) {
	echo "<tr>";
	echo "<td>";
	$ClassID = $classes[$i]->getID();
	$ClassName = str_replace($NameSpace, "", $ClassID);
	$res = $mysql->Execute("select OntologyClassID from projectmanagement.OntologyClasses where ClassTitle='" . str_replace("'", "", $ClassName) . "' and OntologyID='" . $OntologyID . "'");
	$rec = $res->fetch();
	$ParentID = $rec["OntologyClassID"];
	$childs = $classes[$i]->getSubclasses();
	echo $ParentID . " (" . count($childs) . ") :";
	for ($j = 0; $j < count($childs); $j++) {
		$ChildClassID = $childs[$j]->getID();
		$ChildClassName = str_replace($NameSpace, "", $ChildClassID);
		$res = $mysql->Execute("select OntologyClassID from projectmanagement.OntologyClasses where ClassTitle='" . str_replace("'", "", $ChildClassName) . "' and OntologyID='" . $OntologyID . "'");
		$rec = $res->fetch();
		$ChildID = $rec["OntologyClassID"];
		if ($ParentID > 0 && $ChildID > 0)
			$mysql->Execute("insert into projectmanagement.OntologyClassHirarchy (OntologyClassID, OntologyClassParentID) values ('" . $ParentID . "', '" . $ChildID . "')");
		echo $ChildID . ", ";
	}
	echo "</td>";
	echo "</tr>";
}


echo "<tr bgcolor=#cccccc><td align=center><b>properties: </b></td></tr>";
$properties = $ontology->getAllProperties();
for ($i = 0; $i < count($properties); $i++) {
	echo "<tr>";
	echo "<td>";
	$PropertyID = $properties[$i]->getID();
	$PropertyName = str_replace($NameSpace, "", $PropertyID);
	echo $PropertyName;

	$PropertyType = "OBJECT";
	if ($properties[$i]->isDatatype())
		$PropertyType = "DATATYPE";
	else if ($properties[$i]->IsAnnotation())
		$PropertyType = "ANNOTATION";

	echo " -> " . $PropertyType;

	$ranges = $properties[$i]->getRange();
	echo " range: " . count($ranges);

	$domains = $properties[$i]->getDomain();
	echo " domains: " . count($domains);

	$rangelist = "";
	for ($k = 0; $k < count($ranges); $k++) {
		if ($k > 0)
			$rangelist .= ", ";
		$rangelist .= str_replace($NameSpace, "", $ranges[$k]->getID());
	}

	$domainlist = "";
	for ($k = 0; $k < count($domains); $k++) {
		if ($k > 0)
			$domainlist .= ", ";
		$domainlist .= str_replace($NameSpace, "", $domains[$k]->getID());
	}
	$res2 = $mysql->Execute("select * from projectmanagement.OntologyProperties where PropertyTitle='" . $PropertyName . "' and OntologyID='" . $OntologyID . "'");
	if ($rec2 = $res2->fetch()) {
		echo "exist<br>";
	} else {
		$query = "insert into projectmanagement.OntologyProperties (PropertyTitle, PropertyType, domain, `range`, OntologyID) values ('" . $PropertyName . "', '" . $PropertyType . "', '" . $domainlist . "', '" . $rangelist . "', '" . $OntologyID . "')";
		$mysql->Execute($query);
	}
	echo "</td>";
	echo "</tr>";
}
echo "<tr><td align=center><input type=button value='".C_BACK."' onclick='document.location=\"Manageontologies.php?UpdateID=" . $OntologyID . "\"'></td></tr>";
echo "</table>";
echo "</div>";
echo "<div class'col-1'></div>";
echo "<div class'row'></div>";
?>
</body>