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
if($_SESSION["UserID"]!="omid" && $_SESSION["UserID"]!="shokri" && $_SESSION["UserID"]!="afkhami")
{
	echo "موقتا غیر فعال می باشد";
	die();
}
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
<tr class=HeaderOfTable><td colspan=6><b>فرمهای دریافتی</b></td></tr>
<tr bgcolor=#cccccc><td width=1%>کد</td><td>فرم</td><td>ایجاد کننده</td><td>مرحله</td><td>ارسال کننده</td><td>تاریخ ارسال</td></tr>
<?php 
	function CreateQueryForPermittedSteps($PersonID, $UserID, $UnitID, $SubUnitCode, $EduGrpCode, $FacCode)
	{
		//کاربر به یک مرحله دسترسی دارد اگر 
		// به عنوان کاربر خاص برای آن مرحله تعریف شده باشد
		// دارای نقشی باشد که آن نقش برای دسترسی به مرحله تعریف شده است
		// یا در گروه آموزشی باشد که برای دسترسی به آن مرحله تعریف شده است
		// یا در واحد سازمانی باشد که برای آن مرحله تعریف شده است
		// یا در زیر واحد سازمانی باشد که برای آن مرحله تعریف شده است 
		$query = "	select distinct FormsFlowStepID, UserAccessRange, RelatedOrganzationChartID, AccessRangeRelatedPersonType 
					from FormsFlowSteps 
					LEFT JOIN StepPermittedUnits on (FormsFlowSteps.FilterType='UNITS' and FormsFlowSteps.FormsFlowStepID=StepPermittedUnits.FormFlowStepID)
					LEFT JOIN StepPermittedSubUnits on (FormsFlowSteps.FilterType='SUB_UNITS' and FormsFlowSteps.FormsFlowStepID=StepPermittedSubUnits.FormFlowStepID)
					LEFT JOIN StepPermittedEduGroups on (FormsFlowSteps.FilterType='EDU_GROUPS' and FormsFlowSteps.FormsFlowStepID=StepPermittedEduGroups.FormFlowStepID)
					LEFT JOIN StepPermittedPersons on (FormsFlowSteps.FilterOnSpecifiedUsers='YES' and FormsFlowSteps.FormsFlowStepID=StepPermittedPersons.FormFlowStepID)
					LEFT JOIN StepPermittedRoles on (FormsFlowSteps.FilterOnUserRoles='YES' and FormsFlowSteps.FormsFlowStepID=StepPermittedRoles.FormFlowStepID)
					LEFT JOIN UsersRoles on (StepPermittedRoles.RoleID=UsersRoles.UserRole and StepPermittedRoles.SysCode=UsersRoles.SysCode)
					where ( 
					StepPermittedPersons.PersonID='".$PersonID."' 
					or UserID='".$UserID."' 
					or (FilterOnUserRoles='NO' and FilterOnSpecifiedUsers='NO' and FilterType='NO_FILTER') ";
		if($EduGrpCode!="0" and $EduGrpCode!="")
					$query .= " or EduGrpCode='".$EduGrpCode."' ";
		if($SubUnitCode!="0" and $SubUnitCode!="")
					$query .= " or (StepPermittedSubUnits.UnitID='".$UnitCode."' and StepPermittedSubUnits.SubUnitID='".$SubUnitCode."') ";

		// دلیل آنکه دو مقدار برای واحد سازمانی ارسال می شود به این دلیل است که امکان دارد واحد سازمانی و دانشکده فرد متفاوت باشد
		// در این حالت هم واحد سازمانی و هم دانشکده مورد بررسی قرار می گیرند
		if($UnitCode!="0" and $UnitCode!="")
					$query .= "	or StepPermittedUnits.UnitID='".$UnitCode."' ";
		if($FacCode!=$UnitCode and $FacCode!="0" and $FacCode!="")				
					$query .= "	or StepPermittedUnits.UnitID='".$FacCode."' ";
		$query .= " ) ";	

		if($_SESSION["SystemCode"]=="10")
			$query .= " and StudentPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="101" || $_SESSION["SystemCode"]=="103")
			$query .= " and ProfPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="26" || $_SESSION["SystemCode"]=="150")
			$query .= " and StaffPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="41")
			$query .= " and OtherPortalAccess='ALLOW' "; 
		return $query;
	}

	function CreateQueryForPermittedRecords($PersonID, $UserID, $UnitID, $SubUnitCode, $EduGrpCode, $FacCode, $FormsFlowStepID, $UserAccessRange, $RelatedOrganzationChartID, $AccessRangeRelatedPersonType)
	{
		$query = "select CreatorID, SendDate, StepTitle, FormsFlowSteps.FormsStructID, RelatedRecordID, FormsRecords.FormFlowStepID, SenderID, g2j(SendDate) as gSendDate, concat(plname, ' ', pfname) as SenderName, FormTitle
			 from FormsRecords
			JOIN FormsFlowSteps on (FormsFlowSteps.FormsFlowStepID=FormsRecords.FormFlowStepID)
			JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID)		
			LEFT JOIN hrms_total.persons on (FormsRecords.CreatorID=persons.PersonID) 
			LEFT JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=staff.person_type)
			LEFT JOIN hrms_total.writs on (staff.staff_id=writs.staff_id and staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver)
			where FormFlowStepID='".$FormsFlowStepID."' ";
		if($UserAccessRange=="HIM") // فقط فرمهای خودش را ببیند
			$query .= " and FormsRecords.CreatorID='".$PersonID."'";
		if($UserAccessRange=="UNIT") // فقط فرمهای واحد را ببیند
			$query .= " and staff.UnitCode='".$UnitCode."'";
		else if($UserAccessRange=="SUB_UNIT") // فقط فرمهای زیر واحد را ببیند
			$query .= " and writs.ouid='".$UnitID."' and writs.sub_ouid='".$SubUnitCode."'";
		else if($UserAccessRange=="EDU_GROUP") // فقط فرمهای گروه آموزشی را ببیند
			$query .= " and staff.UnitCode='".$EduGrpCode."'";
		else if($UserAccessRange=="BELOW_IN_CHART_ALL_LEVEL") // فقط فرمهای افراد زیر مجموعه اش را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetAllChildsOfPerson($RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2";
		}
		else if($UserAccessRange=="BELOW_IN_CHART_LEVEL1") // فقط فرمهای افراد زیر مجموعه سطح اولش را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetLevel1ChildsOfPerson($RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2";
		}
		else if($UserAccessRange=="BELOW_IN_CHART_LEVEL2") // فقط فرمهای افراد زیر مجموعه سطح دوم را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetLevel2ChildsOfPerson($RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2";
		}
		else if($UserAccessRange=="BELOW_IN_CHART_LEVEL3") // فقط فرمهای افراد زیر مجموعه سطح سوم را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetLevel3ChildsOfPerson($RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2";
		}
		else if($UserAccessRange=="UNDER_MANAGEMENT") // فقط فرمهای افراد تحت مدیریت
		{
			if(ChartServices::IsHeManager($PersonID,$RelatedOrganzationChartID))
			{
				// اگر شخص مدیر باشد کلیه افراد زیر مجموعه جزو افراد تحت مدیریت او محسوب می شوند
				$PersonCondition = "";
				$PersonList = ChartServices::GetAllChildsOfPerson($RelatedOrganzationChartID, $PersonID);
				for($i=0; $i<count($PersonList); $i++)
				{
					if($PersonCondition!="")
						$PersonCondition .= ", ";
					$PersonCondition .= $PersonList[$i]->PersonID;
				}
				// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
				if($PersonCondition!="")
				{
					if($AccessRangeRelatedPersonType=="CREATOR")
						$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
					else
						$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
				}
				else
					$query .= " and 1=2";
			}
			else
				$query .= " and 1=2";
		}
		$query .= " order by SendDate DESC";
		return $query;
	}
	
	
	$PersonID = $_SESSION["PersonID"];
	$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
	
	$query = "select * from hrms_total.persons 
							JOIN hrms_total.staff using (PersonID, person_type)
							JOIN PersonUsers using (PersonID)
							LEFT JOIN hrms_total.writs on (staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver and staff.staff_id=writs.staff_id) 
							where persons.PersonID='".$PersonID."' ";
	$res = $mysql->Execute($query);
	if($rec = $res->FetchRow())
	{
		$UserID = $rec["UserID"];
		$EduGrpCode = $rec["EduGrpCode"];
		$UnitCode = $rec["UnitCode"];
		$FacCode = $rec["FacCode"];
		$SubUnitCode = $rec["sub_ouid"];
	}
	else
	{ 
		die();
	}
		
	$mysql->Execute("delete from ReceivedForms where PersonID='".$PersonID."'");
	$query = CreateQueryForPermittedSteps($PersonID, $UserID, $UnitID, $SubUnitCode, $EduGrpCode, $FacCode);
	$res = $mysql->Execute($query);
		
		while($sc = $res->FetchRow())
		{
			$query = CreateQueryForPermittedRecords($PersonID, $UserID, $UnitID, $SubUnitCode, $EduGrpCode, $FacCode, $sc["FormsFlowStepID"], $sc["UserAccessRange"], $sc["RelatedOrganzationChartID"], $sc["AccessRangeRelatedPersonType"]);

			echo $query."<br><br>"; 
			/*
			$res2 = $mysql->Execute($query);
			$i = 0;
			while($rec = $res2->FetchRow())
			{
					$query = "insert into ReceivedForms (PersonID, RecID, FormFlowStepID, SendDate, CreatorID, SenderID) values (";
					$query .= "'".$PersonID."', ";
					$query .= "'".$rec["RelatedRecordID"]."', ";
					$query .= "'".$rec["FormFlowStepID"]."', ";
					$query .= "'".$rec["SendDate"]."', ";
					$query .= "'".$rec["CreatorID"]."', ";
					$query .= "'".$rec["SenderID"]."')";
					$mysql->Execute($query);
			}
			*/
			
			
		}	

		$query = "select *, concat(g2j(SendDate), ' ', substr(SendDate, 12,10)) as gSendDate,
								concat(p1.pfname,' ',p1.plname) as SenderName,
								concat(p2.pfname,' ',p2.plname) as CreatorName
								from ReceivedForms
								LEFT JOIN FormsFlowSteps on (ReceivedForms.FormFlowStepID=FormsFlowSteps.FormsFlowStepID)
								LEFT JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID)
								LEFT JOIN hrms_total.persons as p1 on (p1.PersonID=SenderID) 
								LEFT JOIN hrms_total.persons as p2 on (p2.PersonID=CreatorID)
								where ReceivedForms.PersonID='".$PersonID."' order by SendDate DESC";
		//if($_SESSION["PersonID"]=="200852")
		//		$mysql->audit("مرحله 3 ");		
		$res = $mysql->Execute($query);
		//if($_SESSION["PersonID"]=="200852")
		//		$mysql->audit("مرحله 4 ");		
		
		$i = 0;
		while($rec = $res->FetchRow())
		{
			$i++;
			//if($_SESSION["PersonID"]=="200852")
			//	$mysql->audit("مرحله 5: ".$i);		
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td width=10%>";
			echo "<a href='#' onclick='javascript: ViewForm(".$rec["FormsStructID"].", ".$rec["FormFlowStepID"].", ".$rec["RecID"].");'>";
			echo $rec["RecID"];
			echo "</a>";
			echo "</td>";
			echo "<td nowrap>".$rec["FormTitle"]."</td>";
			echo "<td nowrap>".$rec["CreatorName"]."</td>";
			echo "<td nowrap>".$rec["StepTitle"]."</td>";
			echo "<td nowrap>".$rec["SenderName"]."</td>";
			echo "<td nowrap>".$rec["gSendDate"]."</td>";
			echo "</tr>";
		}
  echo '</table>';
HTMLEnd();
?>
