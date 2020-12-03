<?
class SecurityManager
{
	// چک می کند آیا کاربر به این مرحله دسترسی دارد یا خیر
	static function HasUserAccessToThisStep($PersonID, $StepID)
	{
		$PersonType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$PersonType = "STUDENT";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		if($PersonType=="PERSONEL")
			$query = "select * from hrms_total.persons 
								JOIN hrms_total.staff using (PersonID, person_type)
								JOIN PersonUsers using (PersonID)
								LEFT JOIN hrms_total.writs on (staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver and staff.staff_id=writs.staff_id) 
								where persons.PersonID='".$PersonID."'";
		else
			$query = "select StNo as UserID, EduGrpCode, StudentSpecs.FacCode, StudentSpecs.FacCode as UnitCode, EduGrpCode as SubUnitCode from educ.persons 
								JOIN educ.StudentSpecs using (PersonID)
								JOIN educ.StudyFields using (FldCode) 
								where persons.PersonID='".$PersonID."'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			$UserID = $rec["UserID"];
			$EduGrpCode = $rec["EduGrpCode"];
			$UnitCode = $rec["UnitCode"];
			$FacCode = $rec["FacCode"];
			$SubUnitCode = $rec["sub_ouid"];
		}
		else return false;
		// کاربر به یک مرحله دسترسی دارد اگر 
		// به عنوان کاربر خاص برای آن مرحله تعریف شده باشد
		// دارای نقشی باشد که آن نقش برای دسترسی به مرحله تعریف شده است
		// یا در گروه آموزشی باشد که برای دسترسی به آن مرحله تعریف شده است
		// یا در واحد سازمانی باشد که برای آن مرحله تعریف شده است
		// یا در زیر واحد سازمانی باشد که برای آن مرحله تعریف شده است 
		
		$query = "	select distinct FormsFlowStepID  
					from FormsFlowSteps 
					LEFT JOIN StepPermittedUnits on (FormsFlowSteps.FilterType='UNITS' and FormsFlowSteps.FormsFlowStepID=StepPermittedUnits.FormFlowStepID)
					LEFT JOIN StepPermittedSubUnits on (FormsFlowSteps.FilterType='SUB_UNITS' and FormsFlowSteps.FormsFlowStepID=StepPermittedSubUnits.FormFlowStepID)
					LEFT JOIN StepPermittedEduGroups on (FormsFlowSteps.FilterType='EDU_GROUPS' and FormsFlowSteps.FormsFlowStepID=StepPermittedEduGroups.FormFlowStepID) ";
		if($PersonType=="PERSONEL") // تنها برای پرسنل نقش و امکان انتخاب به عنوان افراد خاص دارای دسترسی به یک مرحله وجود دارد
		{
		$query .= " LEFT JOIN StepPermittedPersons on (FormsFlowSteps.FilterOnSpecifiedUsers='YES' and FormsFlowSteps.FormsFlowStepID=StepPermittedPersons.FormFlowStepID) 
					LEFT JOIN StepPermittedRoles on (FormsFlowSteps.FilterOnUserRoles='YES' and FormsFlowSteps.FormsFlowStepID=StepPermittedRoles.FormFlowStepID)
					LEFT JOIN UsersRoles on (StepPermittedRoles.RoleID=UsersRoles.UserRole and StepPermittedRoles.SysCode=UsersRoles.SysCode)";
		}
		$query .= "	where FormsFlowSteps.FormsFlowStepID='".$StepID."' and (";
		// کاربر خاص فقط برای پرسنل امکانپذیر است
		if($PersonType=="PERSONEL")
		{
			$query .= " StepPermittedPersons.PersonID='".$PersonID."' or "; 
			$query .= " UserID='".$UserID."' or ";
		}
		$query .= " (FilterOnUserRoles='NO' and FilterOnSpecifiedUsers='NO' and FilterType='NO_FILTER') ";
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
		
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			return true;
		}
		return false;
	}
	
	// لیست مراحلی که یک کاربر به آنها دسترسی دارد را بر می گرداند
	// نحوه دسترسی به آن مرحله - حوزه مورد دسترسی - هم برگشت داده می شود
	static function GetUserPermittedSteps($PersonID, $ExtraCondition = "")
	{
		$PersonType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$PersonType = "STUDENT";
		
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		if($PersonType=="PERSONEL")
			$query = "select * from hrms_total.persons 
								JOIN hrms_total.staff using (PersonID, person_type)
								JOIN formsgenerator.PersonUsers using (PersonID)
								LEFT JOIN hrms_total.writs on (staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver and staff.staff_id=writs.staff_id) 
								where persons.PersonID='".$PersonID."' ";
		else
			$query = "select StNo as UserID, EduGrpCode, StudentSpecs.FacCode, StudentSpecs.FacCode as UnitCode, EduGrpCode as SubUnitCode from educ.persons 
								JOIN educ.StudentSpecs using (PersonID)
								JOIN educ.StudyFields using (FldCode) 
								where persons.PersonID='".$PersonID."'";
		
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			$UserID = $rec["UserID"];
			$EduGrpCode = $rec["EduGrpCode"];
			$UnitCode = $rec["UnitCode"];
			$FacCode = $rec["FacCode"];
			$SubUnitCode = $rec["sub_ouid"];
		}
		else return $ret;
		
		// کاربر به یک مرحله دسترسی دارد اگر 
		// به عنوان کاربر خاص برای آن مرحله تعریف شده باشد
		// دارای نقشی باشد که آن نقش برای دسترسی به مرحله تعریف شده است
		// یا در گروه آموزشی باشد که برای دسترسی به آن مرحله تعریف شده است
		// یا در واحد سازمانی باشد که برای آن مرحله تعریف شده است
		// یا در زیر واحد سازمانی باشد که برای آن مرحله تعریف شده است 
		$query = "	select distinct FormsFlowStepID  
					from formsgenerator.FormsFlowSteps 
					LEFT JOIN formsgenerator.StepPermittedUnits on (FormsFlowSteps.FilterType='UNITS' and FormsFlowSteps.FormsFlowStepID=StepPermittedUnits.FormFlowStepID)
					LEFT JOIN formsgenerator.StepPermittedSubUnits on (FormsFlowSteps.FilterType='SUB_UNITS' and FormsFlowSteps.FormsFlowStepID=StepPermittedSubUnits.FormFlowStepID)
					LEFT JOIN formsgenerator.StepPermittedEduGroups on (FormsFlowSteps.FilterType='EDU_GROUPS' and FormsFlowSteps.FormsFlowStepID=StepPermittedEduGroups.FormFlowStepID)";
		if($PersonType=="PERSONEL") // تنها برای پرسنل نقش و امکان انتخاب به عنوان افراد خاص دارای دسترسی به یک مرحله وجود دارد
		{
		$query .= " LEFT JOIN formsgenerator.StepPermittedPersons on (FormsFlowSteps.FilterOnSpecifiedUsers='YES' and FormsFlowSteps.FormsFlowStepID=StepPermittedPersons.FormFlowStepID) 
					LEFT JOIN formsgenerator.StepPermittedRoles on (FormsFlowSteps.FilterOnUserRoles='YES' and FormsFlowSteps.FormsFlowStepID=StepPermittedRoles.FormFlowStepID)
					LEFT JOIN formsgenerator.UsersRoles on (StepPermittedRoles.RoleID=UsersRoles.UserRole and StepPermittedRoles.SysCode=UsersRoles.SysCode)";
		}
		$query .= "	where (";
		if($PersonType=="PERSONEL") // تنها برای پرسنل نقش و امکان انتخاب به عنوان افراد خاص دارای دسترسی به یک مرحله وجود دارد
		{
		$query .= "	StepPermittedPersons.PersonID='".$PersonID."' 
					or UserID='".$UserID."' 
					or ";
		}
		$query .= " (FilterOnUserRoles='NO' and FilterOnSpecifiedUsers='NO' and FilterType='NO_FILTER') ";
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
		if($ExtraCondition!="")
			$query .= " and ".$ExtraCondition;

		if($_SESSION["SystemCode"]=="10")
			$query .= " and StudentPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="101" || $_SESSION["SystemCode"]=="103")
			$query .= " and ProfPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="26" || $_SESSION["SystemCode"]=="150")
			$query .= " and StaffPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="41")
			$query .= " and OtherPortalAccess='ALLOW' ";
		$query .= " order by FormsStructID, StepTitle";
		/*
		if($_SESSION["PersonID"]=="201309")
		{
				echo $query."<br><br>"; 
		}
		*/
		$res = $mysql->Execute($query);
		$i = 0;
		while($rec = $res->FetchRow())
		{
			$obj = new be_FormsFlowSteps();
			//$obj->SendDatePermittedStartDate
			$ret[$i] = $obj;
			// بهتر است بعدا به جای اجرای این متد کل محتویات خصوصیات کلاس در همینجا پر شود
			$ret[$i]->LoadDataFromDatabase($rec["FormsFlowStepID"]);
			$i++;
		}
		return $ret;
	}
	
	// لیست مراحل مجاز در یک فرم خاص برای کاربر را بر می گرداند
	static function GetUserPermittedStepsInAForm($PersonID, $FormsStructID)
	{
		return SecurityManager::GetUserPermittedSteps($PersonID, "FormsFlowSteps.FormsStructID='".$FormsStructID."'");
	}

	// لیست مراحل مجاز برای شروع در یک فرم خاص برای کاربر را بر می گرداند
	static function GetUserPermittedStepsInAFormForStart($PersonID, $FormsStructID)
	{
		return SecurityManager::GetUserPermittedSteps($PersonID, "FormsFlowSteps.FormsStructID='".$FormsStructID."' and StepType='START'");
	}
	
		// لیست فرمهایی که کاربر به یکی از مراحل آنها دسترسی دارد بر می گرداند
	static function GetUserPermittedForms($PersonID, $ExtraCondition = "")
	{
		$PersonType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$PersonType = "STUDENT";
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		if($PersonType=="PERSONEL")
			$query = "select * from hrms_total.persons 
								JOIN hrms_total.staff using (PersonID, person_type)
								JOIN PersonUsers using (PersonID)
								LEFT JOIN hrms_total.writs on (staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver and staff.staff_id=writs.staff_id) 
								where persons.PersonID='".$PersonID."'";
		else
			$query = "select StNo as UserID, EduGrpCode, StudentSpecs.FacCode, StudentSpecs.FacCode as UnitCode, EduGrpCode as SubUnitCode from educ.persons 
								JOIN educ.StudentSpecs using (PersonID)
								JOIN educ.StudyFields using (FldCode) 
								where persons.PersonID='".$PersonID."'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			$UserID = $rec["UserID"];
			$EduGrpCode = $rec["EduGrpCode"];
			$UnitCode = $rec["UnitCode"];
			$FacCode = $rec["FacCode"];
			$SubUnitCode = $rec["sub_ouid"];
		}
		else return $ret;
		
		// کاربر به یک مرحله دسترسی دارد اگر 
		// به عنوان کاربر خاص برای آن مرحله تعریف شده باشد
		// دارای نقشی باشد که آن نقش برای دسترسی به مرحله تعریف شده است
		// یا در گروه آموزشی باشد که برای دسترسی به آن مرحله تعریف شده است
		// یا در واحد سازمانی باشد که برای آن مرحله تعریف شده است
		// یا در زیر واحد سازمانی باشد که برای آن مرحله تعریف شده است 
		$query = "	select distinct FormsFlowSteps.FormsStructID   
					from FormsFlowSteps 
					LEFT JOIN StepPermittedUnits on (FormsFlowSteps.FilterType='UNITS' and FormsFlowSteps.FormsFlowStepID=StepPermittedUnits.FormFlowStepID)
					LEFT JOIN StepPermittedSubUnits on (FormsFlowSteps.FilterType='SUB_UNITS' and FormsFlowSteps.FormsFlowStepID=StepPermittedSubUnits.FormFlowStepID)
					LEFT JOIN StepPermittedEduGroups on (FormsFlowSteps.FilterType='EDU_GROUPS' and FormsFlowSteps.FormsFlowStepID=StepPermittedEduGroups.FormFlowStepID)";
		if($PersonType=="PERSONEL") // تنها برای پرسنل نقش و امکان انتخاب به عنوان افراد خاص دارای دسترسی به یک مرحله وجود دارد
		{
		$query .= " LEFT JOIN StepPermittedPersons on (FormsFlowSteps.FilterOnSpecifiedUsers='YES' and FormsFlowSteps.FormsFlowStepID=StepPermittedPersons.FormFlowStepID) 
					LEFT JOIN StepPermittedRoles on (FormsFlowSteps.FilterOnUserRoles='YES' and FormsFlowSteps.FormsFlowStepID=StepPermittedRoles.FormFlowStepID)
					LEFT JOIN UsersRoles on (StepPermittedRoles.RoleID=UsersRoles.UserRole and StepPermittedRoles.SysCode=UsersRoles.SysCode)";
		}
		$query .= "	where (";
		if($PersonType=="PERSONEL") // تنها برای پرسنل نقش و امکان انتخاب به عنوان افراد خاص دارای دسترسی به یک مرحله وجود دارد
		{
		$query .= "	StepPermittedPersons.PersonID='".$PersonID."' 
					or UserID='".$UserID."' 
					or ";
		}
		$query .= " (FilterOnUserRoles='NO' and FilterOnSpecifiedUsers='NO' and FilterType='NO_FILTER') ";
		if($EduGrpCode!="0" and $EduGrpCode!="")
					$query .= " or EduGrpCode='".$EduGrpCode."' ";
		if($SubUnitCode!="0" and $SubUnitCode!="")
					$query .= " or (StepPermittedSubUnits.UnitID='".$UnitCode."' and StepPermittedSubUnits.SubUnitID='".$SubUnitCode."') ";
		if($UnitCode!="0" and $UnitCode!="")
					$query .= "	or StepPermittedUnits.UnitID='".$UnitCode."' ";
		if($FacCode!=$UnitCode and $FacCode!="0" and $FacCode!="")				
					$query .= "	or StepPermittedUnits.UnitID='".$FacCode."' ";	
		$query .= " ) ";
		
		$query .= " and (";
		$query .= " SendDatePermittedStartDate='1000-01-01 00:00:00' or SendDatePermittedStartDate<=concat(substr(now(), 1, 10), ' ', '00:00:00')";
		$query .= " ) ";
		$query .= " and (";
		$query .= " SendDatePermittedEndDate='1000-01-01 00:00:00' or SendDatePermittedEndDate>concat(substr(now(), 1, 10), ' ', '00:00:00')";
		$query .= " ) ";
		
		if($_SESSION["SystemCode"]=="10")
			$query .= " and StudentPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="101" || $_SESSION["SystemCode"]=="103")
			$query .= " and ProfPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="26" || $_SESSION["SystemCode"]=="150")
			$query .= " and StaffPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="41")
			$query .= " and OtherPortalAccess='ALLOW' "; 			
		
		if($ExtraCondition!="")
			$query .= " and ".$ExtraCondition;
		//for example: StepType='START';
		//echo $query;
		//die();
			
		$res = $mysql->Execute($query);
		$i = 0;
		while($rec = $res->FetchRow())
		{
			$obj = new be_FormsStruct();
			$ret[$i] = $obj;
			// بهتر است بعدا به جای اجرای این متد کل محتویات خصوصیات کلاس در همینجا پر شود
			$ret[$i]->LoadDataFromDatabase($rec["FormsStructID"]);
			$i++;
		}
		return $ret;
	}

	// لیست فرمهای مجازی که کاربر به مرحله شروعی از آنها دسترسی دارد بر می گرداند
	static function GetUserPermittedFormsForStart($PersonID)
	{
		$ExtraCondition = "StepType='START'
							and (
							NumberOfPermittedSend=0 
							or (select count(*) from formsgenerator.FormsRecords where FormsStructID=FormsFlowSteps.FormsStructID and CreatorID='".$PersonID."')<NumberOfPermittedSend
							) 
							";
		return SecurityManager::GetUserPermittedForms($PersonID, $ExtraCondition);
	}
	
	// بررسی می کند آیا کاربر مورد نظر به رکوردی در مرحله ذکر شده دسترسی دارد یا خیر
	static function HasUserAccessToThisRecord($PersonID, $FormsFlowStepID, $RecordID)
	{
		$PersonType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$PersonType = "STUDENT";
		$CurStep = new be_FormsFlowSteps();
		$CurStep->LoadDataFromDatabase($FormsFlowStepID);
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		if($CurStep->FilterOnSpecifiedUsers=="YES")
		{
			$res = $mysql->Execute("select count(*) from StepPermittedPersons where FormFlowStepID='".$FormsFlowStepID."' and PersonID='".$PersonID."'");
			if($rec = $res->FetchRow())
			{
				if($rec[0]==0)
					return false;
				return true;
			}
			return false;
		}
		$ret = array();
		
		// ابتدا واحد سازمانی و گروه آموزشی و زیر واحد سازمانی مربوط به شخص از جداول مربوطه استخراج می شوند
		if($PersonType=="PERSONEL")
			$query = "select * from hrms_total.persons 
								JOIN hrms_total.staff using (PersonID, person_type)
								JOIN PersonUsers using (PersonID)
								LEFT JOIN hrms_total.writs on (staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver and staff.staff_id=writs.staff_id) 
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
		
		$query = "select FormsRecords.FormsRecordsID as TotalCount 
					 from FormsRecords
					JOIN FormsFlowSteps on (FormsFlowSteps.FormsFlowStepID=FormsRecords.FormFlowStepID)
					JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID)";
		if($PersonType=="PERSONEL")
		{				
		$query .= "	LEFT JOIN hrms_total.persons on (FormsRecords.SenderID=persons.PersonID) 
					LEFT JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=staff.person_type)
					LEFT JOIN hrms_total.writs on (staff.staff_id=writs.staff_id and staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver)";
		}
		else
		{
		$query .= "	LEFT JOIN educ.persons on (FormsRecords.SenderID=persons.PersonID)
					LEFT JOIN educ.StudentSpecs using (PersonID)
					LEFT JOIN educ.StudyFields using (FldCode)"; 
		}
		$query .= "	where (FormFlowStepID='".$FormsFlowStepID."' and FormsRecords.RelatedRecordID='".$RecordID."' ";
		if($CurStep->UserAccessRange=="HIM") //  - فقط فرمهای خودش را ببیند - در این حالت چک می کند ارسال کننده آخر یا ایجاد کننده اولیه فرم اگر خود کاربر باشد اجازه دسترسی می دهد
		{
			if($CurStep->StepType=="START")
				$query .= " and FormsRecords.CreatorID='".$PersonID."' ";
			else
				$query .= " and (FormsRecords.SenderID='".$PersonID."' or FormsRecords.CreatorID='".$PersonID."') ";
		}	
		if($CurStep->UserAccessRange=="UNIT") // فقط فرمهای واحد را ببیند
		{
			if($PersonType=="PERSONEL")
				$query .= " and staff.UnitCode='".$UnitCode."'";
			else
				$query .= " and StudentSpecs.FacCode='".$UnitCode."'";
		}
		else if($CurStep->UserAccessRange=="SUB_UNIT") // فقط فرمهای زیر واحد را ببیند
		{
			if($PersonType=="PERSONEL")
				$query .= " and writs.ouid='".$rec["ouid"]."' and writs.sub_ouid='".$SubUnitCode."'";
			else
				$query .= " and StudyFields.EduGrpCode='".$SubUnitCode."'";
		}
		else if($CurStep->UserAccessRange=="EDU_GROUP") // فقط فرمهای گروه آموزشی را ببیند
		{
			if($PersonType=="PERSONEL")
				$query .= " and staff.UnitCode='".$EduGrpCode."'";
			else
				$query .= " and StudyFields.EduGrpCode='".$EduGrpCode."'";
		}
		else if($CurStep->UserAccessRange=="BELOW_IN_CHART_ALL_LEVEL") // فقط فرمهای افراد زیر مجموعه اش را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetAllChildsOfPerson($CurStep->RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($CurStep->AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2";
		}
		else if($CurStep->UserAccessRange=="BELOW_IN_CHART_LEVEL1") // فقط فرمهای افراد زیر مجموعه سطح اولش را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetLevel1ChildsOfPerson($CurStep->RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($CurStep->AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2 ";
		}
		else if($CurStep->UserAccessRange=="BELOW_IN_CHART_LEVEL2") // فقط فرمهای افراد زیر مجموعه سطح دوم را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetLevel2ChildsOfPerson($CurStep->RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($CurStep->AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2 ";
		}
		else if($CurStep->UserAccessRange=="BELOW_IN_CHART_LEVEL3") // فقط فرمهای افراد زیر مجموعه سطح سوم را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetLevel3ChildsOfPerson($CurStep->RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($CurStep->AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2 ";
		}
		else if($CurStep->UserAccessRange=="UNDER_MANAGEMENT") // فقط فرمهای افراد تحت مدیریت
		{
			if(ChartServices::IsHeManager($PersonID,$CurStep->RelatedOrganzationChartID))
			{
				// اگر شخص مدیر باشد کلیه افراد زیر مجموعه جزو افراد تحت مدیریت او محسوب می شوند
				$PersonCondition = "";
				$PersonList = ChartServices::GetAllChildsOfPerson($CurStep->RelatedOrganzationChartID, $PersonID);
				for($i=0; $i<count($PersonList); $i++)
				{
					if($PersonCondition!="")
						$PersonCondition .= ", ";
					$PersonCondition .= $PersonList[$i]->PersonID;
				}
				// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
				if($PersonCondition!="")
				{
					if($CurStep->AccessRangeRelatedPersonType=="CREATOR")
						$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
					else
						$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
				}
				else  // اگر شخص مدیر نبود اصلا دسترسی به رکوردهای این مرحله برای او بدون معنی می باشد
					$query .= " and 1=2 ";
			}
			else
				$query .= " and 1=2 ";
		}
		if($_SESSION["SystemCode"]=="10")
			$query .= " and StudentPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="101" || $_SESSION["SystemCode"]=="103")
			$query .= " and ProfPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="26" || $_SESSION["SystemCode"]=="150")
			$query .= " and StaffPortalAccess='ALLOW' "; 
		else if($_SESSION["SystemCode"]=="41")
			$query .= " and OtherPortalAccess='ALLOW' "; 
		$query .= ") ";
		// در مورد بالا باید توجه داشت ممکن است فردی یک فرم را ایجاد کند و این فرم پس از گردش بسیار به او بازگردد در صورتیکه فقط آخرین فرستنده چک شود شخص امکان مشاهده آن فرم را نخواهد داشت که نادرست است.
		// بنابراین در کیوری بالا علاوه بر ارسال کننده آخرین ایجاد کننده نیز بررسی می شود
		
		$query .= " union select FormsRecords.FormsRecordsID 
					from FormsRecords 
					JOIN FormsFlowSteps on (FormsFlowSteps.FormsFlowStepID=FormsRecords.FormFlowStepID) 
					JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID) 
					JOIN FormsFlowHistory on (FormsFlowHistory.FormsStructID=FormsFlowSteps.FormsStructID and FormsFlowHistory.RecID=FormsRecords.RelatedRecordID)
					LEFT JOIN hrms_total.persons on (FormsRecords.CreatorID=persons.PersonID) 
					LEFT JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=staff.person_type) 
					LEFT JOIN hrms_total.writs on (staff.staff_id=writs.staff_id and staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver) 
					where (FormFlowStepID='".$FormsFlowStepID."' and FormsFlowHistory.FromPersonID=".$PersonID." and FormsRecords.SenderID<>'".$PersonID."')";
		
		//$query .= " order by SendDate DESC";
		
		//if($_SESSION["PersonID"]=="401366289")
		//	echo $query;
		
		$res = $mysql->Execute($query);
		$i = 0;
		if($res->RecordCount()>0)
			return true;
		else
			return false;
	}
	
	// بررسی می کند آیا کاربران در این مرحله امکان اضافه کردن به این جدول جزییات را دارند یا خیر
	static function HasUserAddAccessToThisDetailForm($MasterFormsStructID, $DetailFormsStructID, $FormsFlowStepID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select AddAccessType from FormsDetailTables 
					JOIN DetailTablesAccessType using (FormsDetailTableID)
				where FormStructID='".$MasterFormsStructID."' AND DetailFormStructID='".$DetailFormsStructID."' AND FormFlowStepID='".$FormsFlowStepID."'";
		$res = $mysql->Execute($query);
		$rec = $res->FetchRow();
		if($rec["AddAccessType"]=="ACCESS")
			return true;
		else
			return false;
	}

	// نوع دسترسی ویرایش کاربران به این جدول جزییات را برمی گرداند
	//READ_ONLY, ONLY_USER, ALL
	static function GetUserEditAccessTypeToThisDetailForm($MasterFormsStructID, $DetailFormsStructID, $FormsFlowStepID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select EditAccessType from FormsDetailTables 
					JOIN DetailTablesAccessType using (FormsDetailTableID)
				where FormStructID='".$MasterFormsStructID."' AND DetailFormStructID='".$DetailFormsStructID."' AND FormFlowStepID='".$FormsFlowStepID."'";
		$res = $mysql->Execute($query);
		$rec = $res->FetchRow();
		return $rec["EditAccessType"];
	}

	// نوع دسترسی حذف برای کاربران به این جدول جزییات را برمی گرداند
	//READ_ONLY, ONLY_USER, ALL
	static function GetUserRemoveAccessTypeToThisDetailForm($MasterFormsStructID, $DetailFormsStructID, $FormsFlowStepID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select RemoveAccessType from FormsDetailTables 
					JOIN DetailTablesAccessType using (FormsDetailTableID)
				where FormStructID='".$MasterFormsStructID."' AND DetailFormStructID='".$DetailFormsStructID."' AND FormFlowStepID='".$FormsFlowStepID."'";
		$res = $mysql->Execute($query);
		$rec = $res->FetchRow();
		return $rec["RemoveAccessType"];
	}
	

	// لیست انواع مجاز پرونده برای ایجاد کردن برای یک فرد را برمی گرداند
	static function GetUserPermittedFileTypesForAdding($PersonID)
	{
		$i = 0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		// ممکن است برای هر نوع پرونده چندین دسترسی مختلف برای یک کاربر داشته باشیم برای همین دیستینکت می گیریم
		$query = "select distinct FileTypeID from FileTypeUserPermissions JOIN FileTypes using (FileTypeID) where PersonID='".$PersonID."' and AddPermission='YES'"; 
		$res = $mysql->Execute($query);
		while($rec = $res->FetchRow())
		{
			$ret[$i] = new be_FileTypes();
			$ret[$i]->LoadDataFromDatabase($rec["FileTypeID"]);
			$i++;
		}
		return $ret;
	}

	// لیست انواع مجاز پرونده برای دسترسی یک فرد را برمی گرداند
	static function GetUserPermittedFileTypesForAccess($PersonID)
	{
		require_once("classes/FileTypeUserPermittedUnits.class.php");
		require_once("classes/FileTypeUserPermittedSubUnits.class.php");
		require_once("classes/FileTypeUserPermittedEduGroups.class.php");
		$i = 0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		// ممکن است برای هر نوع پرونده چندین دسترسی مختلف برای یک کاربر داشته باشیم برای همین دیستینکت می گیریم
		$query = "select FileTypeUserPermissionID, FileTypeID, AccessRange, FileTypeName from FileTypeUserPermissions JOIN FileTypes using (FileTypeID) where PersonID='".$PersonID."' "; 
		$res = $mysql->Execute($query);
		while($rec = $res->FetchRow())
		{
			$ret[$i]["FileTypeID"] = $rec["FileTypeID"];
			$ret[$i]["FileTypeName"] = $rec["FileTypeName"];
			$ret[$i]["AccessRange"] = $rec["AccessRange"];
			// برای اینکه اگر موردی را انتخاب نکرده بود - واحد - زیر واحد یا گروه آموزشی و محدودیت بر اساس آنها تعریف کرده بود هیچ گزینه ای را نیاورد
			$ret[$i]["PermittedRangeList"] = "-1";
			$FileTypeUserPermissionID = $rec["FileTypeUserPermissionID"];
			if($rec["AccessRange"]=="UNIT")
			{
				$list = manage_FileTypeUserPermittedUnits::GetList(" FileTypeUserPermissionID='".$FileTypeUserPermissionID."' ");
				for($k=0; $k<count($list); $k++)
				{
					$ret[$i]["PermittedRangeList"] .= ",";
					$ret[$i]["PermittedRangeList"] .= $list[$k]->ouid;
				}
			}
			else if($rec["AccessRange"]=="SUB_UNIT")
			{
				$list = manage_FileTypeUserPermittedSubUnits::GetList(" FileTypeUserPermissionID='".$FileTypeUserPermissionID."' ");
				for($k=0; $k<count($list); $k++)
				{
					$ret[$i]["PermittedRangeList"] .= ",";
					$ret[$i]["PermittedRangeList"] .= $list[$k]->SubUnitID;
				}
			}
			else if($rec["AccessRange"]=="EDU_GROUP")
			{
				
				$list = manage_FileTypeUserPermittedEduGroups::GetList(" FileTypeUserPermissionID='".$FileTypeUserPermissionID."' ");
				for($k=0; $k<count($list); $k++)
				{
					$ret[$i]["PermittedRangeList"] .= ",";
					$ret[$i]["PermittedRangeList"] .= $list[$k]->EduGrpCode;
				}
			}
			$i++;
		}
		return $ret;
	}
	
	// بررسی می کند آیا کاربر به ایجاد این نوع پرونده دسترسی دارد
	static function HasUserAddAccessToThisFileType($PersonID, $FileTypeID)
	{
		$i = 0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		// ممکن است برای هر نوع پرونده چندین دسترسی مختلف برای یک کاربر داشته باشیم برای همین دیستینکت می گیریم
		$query = "select * from FileTypeUserPermissions where PersonID='".$PersonID."' and FileTypeID='".$FileTypeID."' and AddPermission='YES'"; 
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
			return true;
		return false;
	}

	// آیا کاربر مجوز حذف این نوع فرم را از این نوع پرونده الکترونیکی دارد؟
	static function HasUserRemoveAccessThisFormFromThisFileType($PersonID, $FormStructID, $FileTypeID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from FileTypeUserPermissions JOIN FileTypeUserPermittedForms using (FileTypeUserPermissionID) where PersonID='".$PersonID."' and FileTypeID='".$FileTypeID."' and FormsStructID='".$FormStructID."' and RemoveFormPermission='YES'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
			return true;
		return false;		
	}

	// آیا کاربر مجوز اضافه کردن این نوع فرم را در این نوع پرونده الکترونیکی دارد؟
	static function HasUserAddAccessThisFormFromThisFileType($PersonID, $FormStructID, $FileTypeID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from FileTypeUserPermissions JOIN FileTypeUserPermittedForms using (FileTypeUserPermissionID) where PersonID='".$PersonID."' and FileTypeID='".$FileTypeID."' and FormsStructID='".$FormStructID."' and AddFormPermission='YES'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
			return true;
		return false;		
	}
	
	// لیست رکوردهایی که کاربر به خاطر حضور در تاریخچه به آنها دسترسی دارد بر می گرداند - به لیست فرمهای رسیده اضافه می کند
	function GetRecievedFormsBecauseOfHistory($PersonID, $StepsList)
	{
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		
		$query = " select CreatorType, FormsRecords.SenderType, CreatorID, FormsRecords.SendDate, StepTitle, FormsFlowSteps.FormsStructID, RelatedRecordID, FormsRecords.FormFlowStepID, SenderID, concat(plname, ' ', pfname) as SenderName, FormTitle 
					from formsgenerator.FormsRecords 
					JOIN formsgenerator.FormsFlowSteps on (FormsFlowSteps.FormsFlowStepID=FormsRecords.FormFlowStepID) 
					JOIN formsgenerator.FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID) 
					JOIN formsgenerator.FormsFlowHistory on (FormsFlowHistory.FormsStructID=FormsFlowSteps.FormsStructID and FormsFlowHistory.RecID=FormsRecords.RelatedRecordID)
					LEFT JOIN hrms_total.persons on (FormsRecords.CreatorID=persons.PersonID) 
					LEFT JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=staff.person_type) 
					LEFT JOIN hrms_total.writs on (staff.staff_id=writs.staff_id and staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver) 
					where (FormFlowStepID in (".$StepsList.") and FormsFlowHistory.FromPersonID=".$PersonID." and FormsRecords.SenderID<>'".$PersonID."')";
		$res = $mysql->Execute($query);
		$i = 0;
		while($rec = $res->FetchRow())
		{
				$query = "insert into formsgenerator.ReceivedForms (PersonID, RecID, FormFlowStepID, SendDate, CreatorID, SenderID, CreatorType, SenderType) values (";
				$query .= "'".$PersonID."', ";
				$query .= "'".$rec["RelatedRecordID"]."', ";
				$query .= "'".$rec["FormFlowStepID"]."', ";
				$query .= "'".$rec["SendDate"]."', ";
				$query .= "'".$rec["CreatorID"]."', ";
				$query .= "'".$rec["SenderID"]."', ";
				$query .= "'".$rec["CreatorType"]."', ";
				$query .= "'".$rec["SenderType"]."')";
				$mysql->Execute($query);
		}
	}

	// prevent injection attacks
	static function validateInput(array $input):array{
		foreach($input as $key => $value){
			$input[$key] = self::validateText($value);
		}
		return $input;
	}

	static function validateText($str):string{
    if (is_object($str) || is_array($str)) {
        return '';
    }
    $filtered = iconv('UTF-8//IGNORE', "ISO-8859-1//IGNORE", (string) $str); // check if chrarachters are in UTF-8
		$filtered = htmlentities($filtered);
		$filtered = htmlspecialchars($filtered);
		$filtered = preg_replace('/[\r\n\t ]+/', ' ', $filtered); // ignore white spaces
    $filtered = trim($filtered);
    return $filtered;
	}
	
}
?>
