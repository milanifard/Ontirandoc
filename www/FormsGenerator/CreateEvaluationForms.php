<?php
include("header.inc.php");
HTMLBegin();
$mysql = dbclass::getInstance();
echo "<form>";
echo "<input type=hidden name=EvalForms id=EvalForms>";
echo "<select name=SelectedUnitID>";
$res = $mysql->Execute("select ouid, ptitle from hrms_total.org_units");
while($rec = $res->FetchRow())
{
	echo "<option value='".$rec["ouid"]."' ";
	if(isset($_REQUEST["SelectedUnitID"]) && $_REQUEST["SelectedUnitID"]==$rec["ouid"])
	{
		echo " selected ";
	}
	echo ">".$rec["ptitle"];
}
echo "</select><input type=submit value='تولید فرم ارزشیابی'>";
echo "</form>";
echo "<br>";
/*
echo "<form>";
echo "<input type=hidden name=Taxi value=1>";
echo "<input type=submit value='تولید فرم نظرسنجی تاکسی سرویس'>";
echo "</form>";
*/

echo "<form>";
echo "<input type=hidden name=ICDLForm value=1>";
echo "<input type=submit value='تولید فرم نظرسنجی ICDL'>";
echo "</form>";

echo "<br>";

echo "<form>";
echo "<input type=hidden name=VirtualForm value=1>";
echo "<input type=submit value='تولید فرم نظرسنجی دوره های مجازی'>";
echo "</form>";


/*
if(isset($_REQUEST["Taxi"]))
{
	$query = "select * from hrms_total.persons JOIN hrms_total.staff using (PersonID, person_type)";
	$res = $mysql->Execute($query);
	while($rec=$res->FetchRow())
	{
		$query = "insert into TaxiEvaluationForms (PersonID) values ('".$rec["PersonID"]."')";
		$mysql->Execute($query);
	
		$query = "select max(TaxiEvaluationFormID) as MaxID from TaxiEvaluationForms where PersonID='".$rec["PersonID"]."' ";
		$res2 = $mysql->Execute($query);
		$rec2 = $res2->FetchRow();
		$CurID = $rec2["MaxID"];
		
		$mysql->Execute("insert into FormsRecords (FormFlowStepID, RelatedRecordID, SendDate, SenderID, CreatorID, FormsStructID) values ('61', '".$CurID."', now(), '".$rec["PersonID"]."', '".$rec["PersonID"]."', '48')");
	}
	die();
}
*/
if(isset($_REQUEST["EvalForms"]))
{
	$query = "select persons.PersonID, pfname, plname, staff.person_type, emp_state, UnitCode from hrms_total.persons 
							JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=persons.person_type) 
							LEFT JOIN hrms_total.writs on (staff.staff_id=writs.staff_id and staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver) 
							where plname<>'??' and plname<>'???' and UnitCode='".$_REQUEST["SelectedUnitID"]."' and (staff.person_type=5 or staff.person_type=6 or
							(staff.person_type=2 and (emp_state=1 or emp_state=10 or emp_state=2)))
							order by plname, pfname";
	$res = $mysql->Execute($query);
	while($rec=$res->FetchRow())
	{
		$query = "select * from TempStaffEvaluationForms where PersonID='".$rec["PersonID"]."' and ouid='".$rec["UnitCode"]."'";
		$res2 = $mysql->Execute($query);
		if($res2->FetchRow())
		{
			echo "<font color=green>".$rec["PersonID"].": ".$rec["plname"]." ".$rec["pfname"]."</font><br>";
		}
		else
		{
			echo $rec["PersonID"].": ".$rec["plname"]." ".$rec["pfname"]."<br>";
			$query = "insert into TempStaffEvaluationForms (PersonID, ouid) values ('".$rec["PersonID"]."', '".$rec["UnitCode"]."')";
			$mysql->Execute($query);
		
			$query = "select max(StaffEvaluationFormID) as MaxID from TempStaffEvaluationForms where PersonID='".$rec["PersonID"]."' ";
			$res2 = $mysql->Execute($query);
			$rec2 = $res2->FetchRow();
			$CurID = $rec2["MaxID"];
			
			$mysql->Execute("insert into FormsRecords (FormFlowStepID, RelatedRecordID, SendDate, SenderID, CreatorID, FormsStructID) values ('43', '".$CurID."', now(), '".$rec["PersonID"]."', '".$rec["PersonID"]."', '40')");
		}
	}
}
if(isset($_REQUEST["ICDLForm"]))
{
	$query = "select persons.PersonID, pfname, plname, staff.person_type, UnitCode from hrms_total.persons 
							JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=persons.person_type) 
							where plname<>'??' and plname<>'???' and staff.person_type in (2, 3, 5, 6)
							order by plname, pfname";
	$res = $mysql->Execute($query);
	while($rec=$res->FetchRow())
	{
		$query = "select * from formsgenerator.ICDLVotes where PersonID='".$rec["PersonID"]."'";
		$res2 = $mysql->Execute($query);
		if($res2->FetchRow())
		{
			echo "<font color=green>".$rec["PersonID"].": ".$rec["plname"]." ".$rec["pfname"]."</font><br>";
		}
		else
		{
			echo $rec["PersonID"].": ".$rec["plname"]." ".$rec["pfname"]."<br>";
			$query = "insert into ICDLVotes (PersonID) values ('".$rec["PersonID"]."')";
			$mysql->Execute($query);
		
			$query = "select max(ICDLVotesID) as MaxID from formsgenerator.ICDLVotes where PersonID='".$rec["PersonID"]."' ";
			$res2 = $mysql->Execute($query);
			$rec2 = $res2->FetchRow();
			$CurID = $rec2["MaxID"];
			
			$mysql->Execute("insert into FormsRecords (FormFlowStepID, RelatedRecordID, SendDate, SenderID, CreatorID, FormsStructID) values ('67', '".$CurID."', now(), '".$rec["PersonID"]."', '".$rec["PersonID"]."', '50')");
		}
	}
}
if(isset($_REQUEST["VirtualForm"]))
{
	$query = "select persons.PersonID, pfname, plname, staff.person_type, UnitCode from hrms_total.persons 
							JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=persons.person_type) 
							where plname<>'??' and plname<>'???' and staff.person_type in (1, 200)
							order by plname, pfname";
	$res = $mysql->Execute($query);
	while($rec=$res->FetchRow())
	{
		$query = "select * from formsgenerator.VirtualLessonsEval where PersonID='".$rec["PersonID"]."'";
		$res2 = $mysql->Execute($query);
		if($res2->FetchRow())
		{
			echo "<font color=green>".$rec["PersonID"].": ".$rec["plname"]." ".$rec["pfname"]."</font><br>";
		}
		else
		{
			echo $rec["PersonID"].": ".$rec["plname"]." ".$rec["pfname"]."<br>";
			$query = "insert into VirtualLessonsEval (PersonID) values ('".$rec["PersonID"]."')";
			$mysql->Execute($query);
		
			$query = "select max(VirtualLessonsEvalID) as MaxID from formsgenerator.VirtualLessonsEval where PersonID='".$rec["PersonID"]."' ";
			$res2 = $mysql->Execute($query);
			$rec2 = $res2->FetchRow();
			$CurID = $rec2["MaxID"];
			
			$mysql->Execute("insert into FormsRecords (FormFlowStepID, RelatedRecordID, SendDate, SenderID, CreatorID, FormsStructID) values ('70', '".$CurID."', now(), '".$rec["PersonID"]."', '".$rec["PersonID"]."', '51')");
		}
	}
}
?>
</html>
	
