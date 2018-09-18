<?
//error_reporting(E_ALL);
include "header.inc.php";
include '../sharedClasses/PHPExcel.php';

function CreateParentClassesFK($foo, $CurRow, $ClassID)
{
	$mysql = pdodb::getInstance();
	$query = "select OntologyClassID, ClassTitle, label from projectmanagement.OntologyClassHirarchy 
	JOIN projectmanagement.OntologyClasses using (OntologyClassID)
	JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
	where OntologyClassParentID=".$ClassID;
	$res = $mysql->Execute($query);
        if($res->rowCount()>0)
        {
        	while($rec = $res->fetch())
        	{
       	        	$CurRow++;
			$foo->setCellValueByColumnAndRow(0, $CurRow, $rec["ClassTitle"]."FK"); 
			$foo->setCellValueByColumnAndRow(1, $CurRow, " کلید خارجی به ".$rec["label"]); 
        	}
        }
        return $CurRow;
}

function CreateAttributes($foo, $CurRow, $ClassTitle, $OntologyID)
{
	$CurRow++;
	$foo->setCellValueByColumnAndRow(0, $CurRow, $ClassTitle."_PK"); 
	$foo->setCellValueByColumnAndRow(1, $CurRow, "کلید اصلی جدول"); 
	$mysql = pdodb::getInstance();
	$query = "select PropertyTitle, label, 
(select group_concat(PermittedValue,' ') from projectmanagement.OntologyPropertyPermittedValues 
where OntologyProperties.OntologyPropertyID=OntologyPropertyPermittedValues.OntologyPropertyID) as PermittedValues
 from projectmanagement.OntologyProperties 
JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID) 
where OntologyID=? and PropertyType='DATATYPE' and (domain='".$ClassTitle."' or domain like '".$ClassTitle.",%' or domain like '%, ".$ClassTitle."' or domain like '%, ".$ClassTitle.",%')";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID));
	while($rec = $res->fetch())
	{
        	$CurRow++;
		$foo->setCellValueByColumnAndRow(0, $CurRow, $rec["PropertyTitle"]); 
		$foo->setCellValueByColumnAndRow(1, $CurRow, $rec["label"]); 
		//$foo->setCellValueByColumnAndRow(2, $CurRow, $rec["PermittedValues"]); 
		//echo " مقادیر مجاز: ".$rec["PermittedValues"];
		//echo "<br>";
	}
        return $CurRow;
}

function CreateRelationTables($foo, $CurRow, $ClassID)
{
	$TableHeader = array(
	        'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => '84aced')
	        )
	    );

	// فقط مواردی که از این کلاس رابطه شروع شده ایجاد می کند
	$mysql = pdodb::getInstance();
	$query = "select  pl.label as pl, l1.label as c1l, l2.label as c2l, c1.ClassTitle as DClassTitle, c2.ClassTitle as RClassTitle from projectmanagement.OntologyObjectPropertyRestriction 
JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
JOIN projectmanagement.OntologyPropertyLabels pl using (OntologyPropertyID)
JOIN projectmanagement.OntologyClasses c1 on (DomainClassID=c1.OntologyClassID)
JOIN projectmanagement.OntologyClasses c2 on (RangeClassID=c2.OntologyClassID)
JOIN projectmanagement.OntologyClassLabels l1 on (c1.OntologyClassID=l1.OntologyClassID)
JOIN projectmanagement.OntologyClassLabels l2 on (c2.OntologyClassID=l2.OntologyClassID)
where RelationStatus='VALID' and (c1.OntologyClassID=".$ClassID.")";


	$res = $mysql->Execute($query);
	while($rec = $res->fetch())
	{
        	$CurRow++;
       		$foo->getStyle("A".$CurRow)->applyFromArray($TableHeader);
		$foo->getStyle("B".$CurRow)->applyFromArray($TableHeader);

		$foo->setCellValueByColumnAndRow(0, $CurRow, $rec["DClassTitle"]."_".$rec["RClassTitle"]); 
		$foo->setCellValueByColumnAndRow(1, $CurRow, $rec["c1l"]." ".$rec["pl"]." ".$rec["c2l"]); 
		
		$CurRow++;
		$foo->setCellValueByColumnAndRow(0, $CurRow, $rec["DClassTitle"]."_FK"); 
		$foo->setCellValueByColumnAndRow(1, $CurRow, "کلید خارجی به جدول ".$rec["c1l"]); 

		$CurRow++;
		$foo->setCellValueByColumnAndRow(0, $CurRow, $rec["RClassTitle"]."_FK"); 
		$foo->setCellValueByColumnAndRow(1, $CurRow, "کلید خارجی به جدول ".$rec["c2l"]); 
		$CurRow++;
		
	}
	return $CurRow;
        
}

function CreateEntities($foo, $OntologyID)
{
	$TableHeader = array(
	        'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => '84aced')
	        )
	    );

	$mysql = pdodb::getInstance();
	$mysql->Prepare("select * from projectmanagement.OntologyClasses LEFT JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) where OntologyID=?");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	$i=0;
	while($rec = $res->fetch())
	{
		$i++;
		$foo->getStyle("A".$i)->applyFromArray($TableHeader);
		$foo->getStyle("B".$i)->applyFromArray($TableHeader);

		$foo->setCellValueByColumnAndRow(0, $i, $rec["ClassTitle"]); 
		$foo->setCellValueByColumnAndRow(1, $i, $rec["label"]); 
		$i = CreateAttributes($foo, $i, $rec["ClassTitle"], $OntologyID);
		$i = CreateParentClassesFK($foo, $i, $rec["OntologyClassID"]);
		$i++;
		$i = CreateRelationTables($foo, $i, $rec["OntologyClassID"]);
		$i++;
	}
}

function CreatePermittedValues($foo, $OntologyID)
{
	$TableHeader = array(
	        'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => '90aced')
	        )
	    );
	$CurRow=1;
	$mysql = pdodb::getInstance();
	
	$query = "select distinct OntologyPropertyID, PropertyTitle, label from  
			projectmanagement.OntologyPropertyPermittedValues 
			JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
			JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
			where OntologyID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID));
	while($rec = $res->fetch())
	{
        	$CurRow++;
		$foo->getStyle("A".$CurRow)->applyFromArray($TableHeader);
		$foo->getStyle("B".$CurRow)->applyFromArray($TableHeader);
        	
       		$foo->setCellValueByColumnAndRow(0, $CurRow, $rec["PropertyTitle"]); 
		$foo->setCellValueByColumnAndRow(1, $CurRow, $rec["label"]); 
		
		$query = "select PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID=".$rec["OntologyPropertyID"];
		$res2 = $mysql->Execute($query);
		while($rec2 = $res2->fetch())
		{
	        	$CurRow++;
	       		$foo->setCellValueByColumnAndRow(0, $CurRow, $rec2["PermittedValue"]); 
		}
		
	}
	
        return $CurRow+1;
}


$OntologyID = $_REQUEST["OntologyID"];

$phpExcel = new PHPExcel();
 
$styleArray = array(
'font' => array(
'bold' => true,
)
);
 
$foo = $phpExcel->getActiveSheet();
CreateEntities($foo, $OntologyID);

$objWorkSheet= $phpExcel->createSheet(1);
$objWorkSheet->setTitle("مقادیر مجاز");
//$phpExcel->addSheet($objWorkSheet);
$phpExcel->setActiveSheetIndex(1);
$foo = $phpExcel->getActiveSheet();
CreatePermittedValues($foo, $OntologyID);
$phpExcel->setActiveSheetIndex(0);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"ontology.xls\"");
header("Cache-Control: max-age=0");

$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
$objWriter->save("php://output");
?>
