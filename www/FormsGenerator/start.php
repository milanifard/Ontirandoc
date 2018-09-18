<?php
	include('header.inc');	
	$LogFile = "NazarLogs";
	//LogIt($LogFile, $_SESSION['UserID'], U_SIGNED_IN, $_SESSION['UserRecID'], ip2long(GetIPAddress()), $ErrMsg);
	$query=" select * from EducationalCalendar  where IsActive=1";
	$mysql=new DBSQL;
	ExecuteSecureQuery($mysql,$query);
	$mysql->NextRecord();	
	$_SESSION['CriticalYear'] = $mysql->f('CriticalYear');
	$_SESSION['CriticalSemester'] = $mysql->f('CriticalSemester');
	$_SESSION['CurYear'] = $mysql->f('EduYear');
	$_SESSION['CurSemester'] = $mysql->f('semester');	
	$_SESSION['LogFile'] = $LogFile;
	$query=" select UserGroup from ".FRAMEWORK_DB.".UsersSystems where UserID =  '{$_SESSION['UserID']}' and SysCode = ".$_SESSION['SystemCode']." and UserSystemStatus = ".USERS_SYSTEMS_ENABLED;
	ExecuteSecureQuery($mysql,$query);
	$mysql->NextRecord();	
	$_SESSION['UserGroup'] = $mysql->f('UserGroup');	

	$query=" select PersonID from ".FRAMEWORK_DB.".AccountSpecs where WebUserID =  '{$_SESSION['UserID']}' ";
	ExecuteSecureQuery($mysql,$query);
	$mysql->NextRecord();	
	$_SESSION['PersonID'] = $mysql->f('PersonID');	

	$query=" select staff.UnitCode, writ_id, writs.ouid, writs.sub_ouid, org_units.ptitle as UnitName, org_sub_units.ptitle as SubUnitName, PEduName, Units.PName, staff.EduGrpCode from hrms_total.persons
						JOIN hrms_total.staff using (PersonID, person_type)
						LEFT JOIN hrms_total.writs on (writs.writ_id=staff.last_writ_id and writs.writ_ver=staff.last_writ_ver and writs.staff_id=staff.staff_id) 
						LEFT JOIN hrms_total.org_units on (writs.ouid=org_units.ouid)
						LEFT JOIN hrms_total.org_sub_units on (writs.sub_ouid=org_sub_units.sub_ouid)
						LEFT JOIN formsgenerator.EducationalGroups on (EducationalGroups.EduGrpCode=staff.EduGrpCode)
						LEFT JOIN formsgenerator.Units on (Units.id=staff.UnitCode)
						where persons.PersonID =  '".$_SESSION['PersonID']."' ";
	ExecuteSecureQuery($mysql,$query);
	$mysql->NextRecord();
	$_SESSION['EduGrpCode'] = $mysql->f('EduGrpCode');
	$_SESSION['EduGrpName'] = $mysql->f('PEduName');
	if($mysql->f('writ_id')!="")
	{	
		$_SESSION['UnitID'] = $mysql->f('ouid');
		$_SESSION['sub_ouid'] = $mysql->f('sub_ouid');
		$_SESSION['UnitName'] = $mysql->f('UnitName');
		$_SESSION['SubUnitName'] = $mysql->f('SubUnitName');
	}	
	else
	{
		$_SESSION['UnitID'] = $mysql->f('UnitCode');
		$_SESSION['sub_ouid'] = '0';
		$_SESSION['UnitName'] = $mysql->f('PName');
		$_SESSION['SubUnitName'] = '';
	}
	
	ServerTransfer('../gateway/MainFrame.php');
?>