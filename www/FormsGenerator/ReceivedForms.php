<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormFields.class.php");
include("classes/FormsFlowSteps.class.php");
include("classes/FieldsItemList.class.php");
include("classes/FormUtils.class.php");
include("classes/SecurityManager.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
require_once('../organization/classes/ChartServices.class.php');

HTMLBegin();
//$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
//if($_SESSION["PersonID"]=="200852")
//$mysql->audit("پایان1");
?>
<form id="f2" name="f2" method="post" action="ViewForm.php">
	<input type="hidden" name="FormFlowStepID" id="FormFlowStepID" value="0">
	<input type="hidden" name="RelatedRecordID" id="RelatedRecordID" value="0">
	<input type="hidden" name="SelectedFormStructID" id="SelectedFormStructID" value="0">
</form>
<script>
	function ViewForm(FormsStructID, FormFlowStepID, RelatedRecordID)
	{
		document.f2.FormFlowStepID.value=FormFlowStepID;
		document.f2.RelatedRecordID.value=RelatedRecordID;
		document.f2.SelectedFormStructID.value=FormsStructID;
		f2.submit();
	}
</script>

<br><table width=80% align=center border=1 cellspacing=0 cellpadding=5>
<tr class=HeaderOfTable><td colspan=7><b>فرمهای دریافتی</b></td></tr>
<tr bgcolor=#cccccc><td width=1%>ردیف</td><td width=1%>کد</td><td>فرم</td><td>ایجاد کننده</td><td>مرحله</td><td>ارسال کننده</td><td>تاریخ ارسال</td></tr>
<?php 
	$PersonID = $_SESSION["PersonID"];
	/*
	if($PersonID==201309)
	{
		$PersonID=201325;
		$_SESSION["PersonID"] = 201325; 
	}
	*/
	$PersonType = "PERSONEL";
	if($_SESSION["SystemCode"]=="10")
		$PersonType = "STUDENT";

		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		if($PersonType=="PERSONEL")
			$query = "select * from hrms_total.persons 
					LEFT JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=staff.person_type)
					LEFT JOIN hrms_total.writs on (staff.staff_id=writs.staff_id and staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver)
					where persons.PersonID='".$PersonID."'";
		else
			$query = "select StNo as UserID, EduGrpCode, StudentSpecs.FacCode, StudentSpecs.FacCode as UnitCode, EduGrpCode as SubUnitCode from educ.persons 
						JOIN educ.StudentSpecs using (PersonID)
						JOIN educ.StudyFields using (FldCode) 
						where persons.PersonID='".$PersonID."'";
		$res = $mysql->Execute($query);
		$rec = $res->FetchRow();
		$UnitCode = $rec["UnitCode"];
		$SubUnitCode = $rec["sub_ouid"];
		$EduGrpCode = $rec["EduGrpCode"];
		
		$mysql->Execute("delete from ReceivedForms where PersonID='".$PersonID."'");
		$StepList = SecurityManager::GetUserPermittedSteps($PersonID, "");
		$StepsListCSV = "";
		for($sc=0; $sc<count($StepList); $sc++)
		{
			$StepList[$sc]->GetRelatedRecords($PersonID, $UnitCode, $SubUnitCode, $EduGrpCode);
			if($sc>0)
				$StepsListCSV .= ",";
			$StepsListCSV .= $StepList[$sc]->FormsFlowStepID;
		}
		
		if($StepsListCSV!="")	
			SecurityManager::GetRecievedFormsBecauseOfHistory($PersonID, $StepsListCSV);
		/*
		$query = "select *, concat(g2j(SendDate), ' ', substr(SendDate, 12,10)) as gSendDate,
								concat(p1.pfname,' ',p1.plname) as SenderName,
								concat(p2.pfname,' ',p2.plname) as CreatorName
								from ReceivedForms
								LEFT JOIN FormsFlowSteps on (ReceivedForms.FormFlowStepID=FormsFlowSteps.FormsFlowStepID)
								LEFT JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID)
								LEFT JOIN hrms_total.persons as p1 on (p1.PersonID=SenderID) 
								LEFT JOIN hrms_total.persons as p2 on (p2.PersonID=CreatorID)
								where ReceivedForms.PersonID='".$PersonID."' order by SendDate DESC";
		*/
		$query = "select distinct FormsStruct.FormsStructID, FormFlowStepID, ReceivedForms.RecID, StepTitle, SendDate, CreatorType, SenderType, FormTitle, ptitle, 
								concat(p1.pfname,' ',p1.plname) as SenderName,
								concat(p11.pfname,' ',p11.plname) as SenderName2,
								concat(p2.pfname,' ',p2.plname) as CreatorName,
								concat(p22.pfname,' ',p22.plname) as CreatorName2   
								from ReceivedForms
								LEFT JOIN FormsFlowSteps on (ReceivedForms.FormFlowStepID=FormsFlowSteps.FormsFlowStepID)
								LEFT JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID) 
								LEFT JOIN hrms_total.persons as p1 on (p1.PersonID=SenderID) 
								LEFT JOIN hrms_total.persons as p2 on (p2.PersonID=CreatorID)
								LEFT JOIN hrms_total.staff on (p2.PersonID=staff.PersonID and p2.person_type=staff.person_type) 
								LEFT JOIN hrms_total.writs on (staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writ_ver and staff.staff_id=writs.staff_id) 
								
								LEFT JOIN educ.persons as p11 on (p11.PersonID=SenderID) 
								LEFT JOIN educ.persons as p22 on (p22.PersonID=CreatorID)
								LEFT JOIN educ.StudentSpecs on (p22.PersonID=StudentSpecs.PersonID) 
								LEFT JOIN pas.faculties on (StudentSpecs.FacCode=faculties.FacCode)
								
								LEFT JOIN hrms_total.org_units on (writs.ouid=org_units.ouid) 
								where ReceivedForms.PersonID='".$PersonID."' order by SendDate DESC";
		
		$res = $mysql->Execute($query);
		
		$i = 0;
		while($rec = $res->FetchRow())
		{
			$i++;
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td>".$i."</td>";
			echo "<td width=10%>";
			echo "<a href='#' onclick='javascript: ViewForm(".$rec["FormsStructID"].", ".$rec["FormFlowStepID"].", ".$rec["RecID"].");'>";
			echo $rec["RecID"];
			echo "</a>";
			echo "</td>";
			echo "<td nowrap>".$rec["FormTitle"]."</td>";
			if($rec["CreatorType"]=="PERSONEL")
				echo "<td nowrap>".$rec["CreatorName"]."<br>[".$rec["ptitle"]."]</td>";
			else
				echo "<td nowrap>".$rec["CreatorName2"]."<br>[".$rec["PFacName"]."]</td>";
			echo "<td nowrap>".$rec["StepTitle"]."</td>";
			if($rec["SenderType"]=="PERSONEL")
				echo "<td nowrap>".$rec["SenderName"]."</td>";
			else
				echo "<td nowrap>".$rec["SenderName2"]."</td>";
			echo "<td nowrap>".shdate($rec["SendDate"])."</td>";
			echo "</tr>";
		}
  echo '</table>';
HTMLEnd();
?>
