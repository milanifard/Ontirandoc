<?php
	//session_cache_limiter('public');
	include("header.inc.php");
	header('Content-Type: text/plain;charset=utf-8');
	header('Content-disposition: attachment; filename=Report.xls');
	
	$mysql = dbclass::getInstance();
	
	
	$MyStr = "";
	$FormFlowStepID = $_REQUEST["FormFlowStepID"];
	$query = "select * from FormsFlowSteps JOIN FormsStruct using (FormsStructID) where FormsFlowStepID='".$FormFlowStepID."'";
	$res = $mysql->Execute($query);
	if($rec = $res->FetchRow())
	{
		$RelatedDB = $rec["RelatedDB"];
		$RelatedTable = $rec["RelatedTable"];
		$KeyField = $rec["KeyFieldName"];
	}
	else
	{
		die();
	}
	
	$query = "select * from formsgenerator.FormManagers where PersonID='".$_SESSION["PersonID"]."' and FormsStructID='".$rec["FormsStructID"]."' and (AccessType='FULL' or AccessType='DATA')";
	$res = $mysql->Execute($query);
	if(!($rec = $res->FetchRow()))
	{
		echo "مجوز ندارید";
		//echo $query;
		die();
	}
	
	$query = "select COLUMN_NAME from COLUMNS where TABLE_SCHEMA='".$RelatedDB."' and TABLE_NAME='".$RelatedTable."'"; 
	$res = $mysql->Execute($query);
	$cols = array();
	$ColumnsCount = 0;
	$MyStr = "CreatorID, CreatorName ";
	while($rec = $res->FetchRow())
	{
		$cols[$ColumnsCount] = $rec[0];
		$MyStr .= ",";
		$MyStr .= '"' . $rec[0] . '"';
		$ColumnsCount++;
	}
	$MyStr .= "\n";
	
	$query = "select FormsRecords.CreatorID, concat(persons.plname, ' ', persons.pfname) ";
	for($i=0; $i<$ColumnsCount; $i++)
	{
		$query .= ",";
		$query .= $RelatedTable.".".$cols[$i];
	}
	$query .= " from FormsRecords 
					JOIN ".$RelatedDB.".".$RelatedTable." on (FormsRecords.RelatedRecordID=".$RelatedTable.".".$KeyField.") 
					LEFT JOIN hrms_total.persons on (FormsRecords.CreatorID=persons.PersonID) 
					where FormFlowStepID='".$FormFlowStepID."'";
	$res = $mysql->Execute($query);
	while($rec = $res->FetchRow())
	{
		for($i=0; $i<$ColumnsCount+2; $i++)
		{
			if($i>0)
				$MyStr .= ",";
			$MyStr .= '"' . $rec[$i] . '"';
		}
		$MyStr .= "\n";
	}
	echo $MyStr;
?>