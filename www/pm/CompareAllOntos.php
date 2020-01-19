<?php
/*
مقایسه دو هستان نگار
*/
include("header.inc.php");

function GetOntologyTitle($OntologyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select OntologyTitle, comment from projectmanagement.ontologies where OntologyID=?");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	$rec = $res->fetch();
	return $rec["OntologyTitle"]." (".substr($rec["comment"], 0, 100).")";
}

function ShowCompareTable($BenchmarkOntoID, $BenchmarkLabel, $OntologyList)
{

	$ret = "";
	$mysql = pdodb::getInstance();
	$res = $mysql->Execute("select OntologyID, OntologyTitle, comment from projectmanagement.ontologies where OntologyID in (".$OntologyList.")");
//	echo "<table dir=ltr width=70% align=center border=1 cellspacing=0>";
//	echo "<tr dir=rtl bgcolor=#cccccc><td colspan=3>";
//	echo "مقایسه همپوشانی هستان نگار ".$BenchmarkLabel." با سایر هستان نگارها";
//	echo "</td></tr>";
//	echo "<tr class=HeaderOfTable>";
//	echo "<td>نام هستان نگار</td>";
//	echo "<td>درصد نگاشت کلاسها</td>";
//	echo "<td>درصد نگاشت خصوصیات</td>";
//	echo "</tr>";

	echo "<div class='container'>
			<div class='row'>
				<div class='col-9 text-dark text-center' dir='ltr'>
					<div class='row'>
						<div class='col-12 bg-info border border-dark' dir='ltr' >"
							.
							C_COMPARE_COVER_HASTAN_NEGAR.$BenchmarkLabel.C_WITH_OTHER_HASTAN_NEGAR
							.
						"</div>
					</div>
					<div class='row HeaderOfTable'>
						<div class='col-4 border border-dark'>"
						.
						C_NAME_HASTAN_NEGAR
						.
						"</div>
						<div class='col-4 border border-dark'>"
						.
						C_PERCENTAGE_MAPPING_CLASS
						.
						"</div>
						<div class='col-4 border border-dark'>"
						.
						C_PROPERTIES_MAPPING_PERCENTAGE
						.
						"</div>
					</div>";

	while($rec = $res->fetch())
	{
//		echo "<tr>";
//		echo "<td>";
//		echo "<a href='CompareOntologies.php?OntologyID1=".$BenchmarkOntoID."&OntologyID2=".$rec["OntologyID"]."&ActionType=Show'>";
//		echo $rec["OntologyTitle"];
//		echo "</a>";
//		echo "</td>";
//		echo "<td>".CalculateClassCoveragePercentage($BenchmarkOntoID, $rec["OntologyID"])."</td>";
//		echo "<td>".CalculatePropertyCoveragePercentage($BenchmarkOntoID, $rec["OntologyID"])."</td>";
//		echo "</tr>";
		echo "<div class='row'>
				<div class='col-4'>
					<a href='CompareOntologies.php?OntologyID1=".$BenchmarkOntoID."&OntologyID2=".$rec["OntologyID"]."&ActionType=Show'>"
					.
					$rec["OntologyTitle"]
					.
					"</a>
				</div>
				<div class='col-4'>"
				.
				CalculateClassCoveragePercentage($BenchmarkOntoID, $rec["OntologyID"])
				.
				"</div>
				<div class='col-4'>"
				.
				CalculatePropertyCoveragePercentage($BenchmarkOntoID, $rec["OntologyID"])
				.
				"</div>
				</div>";
	}
//	echo "</table>";
		echo "</div>
			</div>
		</div>";

	return $ret;
}


function CalculateClassCoveragePercentage($OntologyID1, $OntologyID2)
{
	$mysql = pdodb::getInstance();
	$query = "select count(*) as tcount from projectmanagement.OntologyClasses where OntologyID=? and
		 OntologyClassID in (
		 select OntologyClassID from projectmanagement.OntologyClassMapping where
		OntologyClassMapping.OntologyID=? and MappedOntologyID=? and MappedOntologyEntityID<>'0')";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID1, $OntologyID2), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$MappedClassCount = $rec["tcount"];
	$query = "select count(*) as tcount from projectmanagement.OntologyClasses where OntologyID=? ";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$TotalClassCount = $rec["tcount"];
	return round(($MappedClassCount*100)/$TotalClassCount, 2);
}

function CalculatePropertyCoveragePercentage($OntologyID1, $OntologyID2)
{
	$mysql = pdodb::getInstance();
	$query = "select count(*) as tcount from projectmanagement.OntologyProperties where OntologyID=? and
		 OntologyPropertyID in (
		 select OntologyPropertyID from projectmanagement.OntologyPropertyMapping where
		OntologyPropertyMapping.OntologyID=? and MappedOntologyID=? and MappedOntologyEntityID<>'0')";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID1, $OntologyID2), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$MappedClassCount = $rec["tcount"];
	$query = "select count(*) as tcount from projectmanagement.OntologyProperties where OntologyID=? ";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$TotalClassCount = $rec["tcount"];
	return round(($MappedClassCount*100)/$TotalClassCount, 2);
}


HTMLBegin();
// LUMB: 23 , from docs: 52, re-eng: 57
// diagnostika: 20
// DBLP: 27, MscProgram: 26
// FRAPO: 33, Scoro: 35
$OntologyList1 = "1, 25, 46, 29, 38, 41, 42, 43, 57, 52";
$OntologyList2 = "1, 25, 46, 29, 38, 41, 42, 43, 57, 23";
$OntologyList3 = "1, 25, 46, 29, 38, 41, 42, 43, 52, 23";
ShowCompareTable(23, "LUMB", $OntologyList1);
echo "<br>";
ShowCompareTable(52, "ساخته شده بر اساس مستندات وزارت عتف", $OntologyList2);
echo "<br>";
ShowCompareTable(57, "ساخته شده بر اساس مهندسی معکوس سدف", $OntologyList3);
?>
