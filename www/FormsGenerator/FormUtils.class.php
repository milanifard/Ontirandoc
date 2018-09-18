<?

class FormUtils
{
	static function HTMLEncode($str)
	{
		$ret = "";
		for($i=0; $i<strlen($str); $i++)
		{
			if(!ctype_alnum($str[$i]))
				$ret .= "&#".ord($str[$i]).";";
			else
				$ret .= $str[$i];
		}
		return $ret;
	}
	
	static function CreateFormTypesOptions($DefaultValue)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select * from FieldTypes order by FieldTypeID';
		$res = $mysql->Execute($query);
		$list = "";
		
		while($rec=$res->FetchRow())
		{
			$list .= "<option value='".$rec["FieldTypeID"]."' ";
			if($rec["FieldTypeID"]==$DefaultValue)
			{
				$list .= " selected ";
			}
			$list .= ">".$rec["TypeName"];
		}
		return $list;
	}

	static function CreateUserRolesOptions($DefaultValue)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select * from roles order by RoleName';
		$res = $mysql->Execute($query);
		$list = "";
		
		while($rec=$res->FetchRow())
		{
			$list .= "<option value='".$rec["RoleID"]."' ";
			if($rec["RoleID"]==$DefaultValue)
			{
				$list .= " selected ";
			}
			$list .= ">".$rec["RoleName"];
		}
		return $list;
	}

	static function CreateSystemsOptions($DefaultValue)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select * from systems order by description';
		$res = $mysql->Execute($query);
		$list = "";
		
		while($rec=$res->FetchRow())
		{
			$list .= "<option value='".$rec["SysCode"]."' ";
			if($rec["SysCode"]==$DefaultValue)
			{
				$list .= " selected ";
			}
			$list .= ">".$rec["description"];
		}
		return $list;
	}
	
	static function CreateUnitsOptions($DefaultValue)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select * from hrms_total.org_units order by ptitle';
		$res = $mysql->Execute($query);
		$list = "";
		
		while($rec=$res->FetchRow())
		{
			$list .= "<option value='".$rec["ouid"]."' ";
			if($rec["ouid"]==$DefaultValue)
			{
				$list .= " selected ";
			}
			$list .= ">".$rec["ptitle"];
		}
		return $list;
	}

	static function CreateSubUnitsOptions($UnitID, $DefaultValue)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from hrms_total.org_sub_units where ouid='".$UnitID."' order by ptitle";
		$res = $mysql->Execute($query);
		$list = "";
		
		while($rec=$res->FetchRow())
		{
			$list .= "<option value='".$rec["sub_ouid"]."' ";
			if($rec["ouid"]==$DefaultValue)
			{
				$list .= " selected ";
			}
			$list .= ">".$rec["ptitle"];
		}
		return $list;
	}
	
	static function CreateEduGrpsOptions($DefaultValue)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from EducationalGroups order by PEduName";
		$res = $mysql->Execute($query);
		$list = "";
		
		while($rec=$res->FetchRow())
		{
			$list .= "<option value='".$rec["EduGrpCode"]."' ";
			if($rec["EduGrpCode"]==$DefaultValue)
			{
				$list .= " selected ";
			}
			$list .= ">".$rec["PEduName"];
		}
		return $list;
	}

	static function CreateEduGrpsOptionsForFacility($FacCode, $DefaultValue)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from EducationalGroups where FacCode='".$FacCode."' order by PEduName";
		$res = $mysql->Execute($query);
		$list = "";
		
		while($rec=$res->FetchRow())
		{
			$list .= "<option value='".$rec["EduGrpCode"]."' ";
			if($rec["EduGrpCode"]==$DefaultValue)
			{
				$list .= " selected ";
			}
			$list .= ">".$rec["PEduName"];
		}
		return $list;
	}
	
	// لیست آیتمها را برای یک کمبو باکس از روی یک جدول بدست می آورد
	static function CreateItemsListFromATable($TableName, $ValueFieldName, $DescriptionFieldName, $DefaultValue)
	{
		$ret = "";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select ".$ValueFieldName.", ".$DescriptionFieldName." from ".$TableName;
		$res = $mysql->Execute($query);
		while($rec = $res->FetchRow())
		{
			$ret .= "<option value='".$rec[$ValueFieldName]."' ";
			if($rec[$ValueFieldName]==$DefaultValue)
				$ret .= " selected ";
			$ret .= ">".$rec[$DescriptionFieldName];
		}
		return $ret;
	}
	
	// با توجه به یک پرس و جو آیتمهای یک لیست برای کمبو باکس را ایجاد می کند
	static function CreateItemsListAccordingToQuery($query, $DefaultValue)
	{
		$ret = "";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute($query);
		while($rec = $res->FetchRow())
		{
			$ret .= "<option value='".$rec[0]."' ";
			if($rec[0]==$DefaultValue)
				$ret .= " selected ";
			$ret .= ">".$rec[1];
		}
		return $ret;
	}
	
	// با توجه به یک نام در دومین لیست آیتمها را برای یک کمبو باکس می سازد
	static function CreateItemsListAccordingToDomainName($DomainName, $DefaultValue)
	{
		$ret = "";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from domains where DomainName='".$DomainName."' order by description";
		$res = $mysql->Execute($query);
		while($rec = $res->FetchRow())
		{
			$ret .= "<option value='".$rec["DomainValue"]."' ";
			if($rec["DomainValue"]==$DefaultValue)
				$ret .= " selected ";
			$ret .= "'>".$rec["description"];
		}
		return $ret;
	}

	// شرح یک آیتم را بر اساس کد آن از روی یک جدول بدست می آورد
	static function CreateItemDescriptionFromATable($TableName, $ValueFieldName, $DescriptionFieldName, $FieldValue)
	{
		$ret = "-";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select ".$ValueFieldName.", ".$DescriptionFieldName." from ".$TableName." where ".$ValueFieldName."='".$FieldValue."'";
		$res = $mysql->Execute($query);
		while($rec = $res->FetchRow())
		{
			$ret = $rec[$DescriptionFieldName];
		}
		return $ret;
	}
	
	// با توجه به یک پرس و جو شرح یک آیتم را بدست می آورد
	static function CreateItemDescriptionAccordingToQuery($query, $FieldValue)
	{
		$ret = "-";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute($query);
		while($rec = $res->FetchRow())
		{
			if($FieldValue==$rec[0])
				$ret = $rec[1];
		}
		return $ret;
	}
	
	// با توجه به یک نام و مقدار در جدول دومین شرح آیتم را بر می گرداند
	static function CreateItemDescriptionAccordingToDomainName($DomainName, $FieldValue)
	{
		$ret = "-";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from domains where DomainName='".$DomainName."' and DomainValue='".$FieldValue."'";
		$res = $mysql->Execute($query);
		while($rec = $res->FetchRow())
		{
			$ret = $rec["description"];
		}
		return $ret;
	}

	// بر اساس کلید انتخابی مقدار برگشت داده می شود
	// این کلید در تعریف مقدار پیش فرض فیلدها کاربرد دارد
	static function GetValueAccordingToKeys($KeyName)
	{
		$now = date("Ymd"); 
		$yy = substr($now,0,4); 
		$mm = substr($now,4,2); 
		$dd = substr($now,6,2);
		list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
		if(strlen($mm)==1)
			$mm = "0".$mm;
		if(strlen($dd)==1)
			$dd = "0".$dd;
		$yy = substr($yy, 2, 2);
		$CurDate = $yy."/".$mm."/".$dd;		
		if($KeyName=="#CURRENT_DATE#")
			return $CurDate;
		else if($KeyName=="#CURRENT_USERID#")
			return $_SESSION["UserID"];
		else if($KeyName=="#CURRENT_PERSON_ID#")
			return $_SESSION["PersonID"];
		else if($KeyName=="#IP_ADDRESS#")
			return $_SESSION['LIPAddress'];
		else if($KeyName=="#CUR_EDU_YEAR#")
			return $_SESSION["EduYear"];
		else if($KeyName=="#CUR_SEMESTER#")
			return $_SESSION["semester"];
		else if($KeyName=="#CUR_YEAR#")
			return $yy;
		else if($KeyName=="#CUR_MONTH#")
			return $mm;
		else if($KeyName=="#CUR_DAY#")
			return $dd;
		else if($KeyName=="#PRE_YEAR#")
			return $yy-1;
		else if($KeyName=="#PRE_MONTH#")
			return $mm-1;
		return 	$KeyName;
	}

	// با گرفتن کد یک آیتم و کد ساختار مربوطه بررسی می کند آن رکورد در کدام مرحله قرار دارد
	static function GetCurrentStepID($RelatedRecordID, $FormsStructID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select FormFlowStepID from FormsRecords where FormsStructID='".$FormsStructID."' and RelatedRecordID='".$RelatedRecordID."'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			return $rec["FormFlowStepID"];
		}
		return 0;
	}

	// بررسی می کند آیا این مرحله در مسیر حرکت رکورد وجود داشته است یا خیر
	static function IsStepInRecordPath($StepID, $RecID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$Step = new be_FormsFlowSteps();
		$Step->LoadDataFromDatabase($StepID);
		
		$query = "select * from FormsFlowHistory where FormsStructID='".$Step->FormsStructID."' and RecID='".$RecID."'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			return true;
		}
		return false;
	}
	
	static function ShowFormFlowHistory($FormsStructID, $RecID)
	{
		$ret = "<table width=50% align=center border=1 cellspacing=0 cellpadding=3>";
		$ret .= "<tr class=HeaderOfTable>";
		$ret .= "<td>ارسال کننده</td><td>تاریخ ارسال</td><td>مرحله ای که به آن ارسال شده</td>";
		$ret .= "</tr>";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select StepTitle, SenderType, 
						concat(p1.pfname, ' ', p1.plname) as PersonelName, 
						concat(p2.pfname, ' ', p2.plname) as StudentName, 
						concat(g2j(SendDate), ' ', substr(SendDate, 12,10)) as gSendDate 
						from FormsFlowHistory
						LEFT JOIN hrms_total.persons p1 on (FromPersonID=p1.PersonID)
						LEFT JOIN educ.persons p2 on (FromPersonID=p2.PersonID)  
						LEFT JOIN FormsFlowSteps on (FormsFlowStepID=ToStepID)  
						where FormsFlowHistory.FormsStructID='".$FormsStructID."' and RecID='".$RecID."' order by SendDate DESC";
		$res = $mysql->Execute($query);
		$i = 0;
		while($rec = $res->FetchRow())
		{
			$i++;
			if($i%2==0)
				$ret .= "<tr class=OddRow>";
			else
				$ret .= "<tr class=EvenRow>";
			if($rec["SenderType"]=="PERSONEL")
				$ret .= "<td>".$rec["PersonelName"]."</td>";
			else
				$ret .= "<td>".$rec["StudentName"]."</td>";
			$ret .= "<td>".$rec["gSendDate"]."</td>";
			$ret .= "<td>".$rec["StepTitle"]."</td>";
			$ret .= "</tr>";
		}
		$ret .= "</table>";
		return $ret;
	}
	
	static function ShowUpdateHistory($FormsStructID, $RecID)
	{
		//require_once('classes/FormsDetailTables.class.php');
		$CurForm = new be_FormsStruct();
		$CurForm->LoadDataFromDatabase($FormsStructID);
		$ret = "<table width=50% align=center border=1 cellspacing=0 cellpadding=3>";
		$ret .= "<tr bgcolor=#cccccc><td colspan=3>".$CurForm->FormTitle."</td></tr>";
		$ret .= "<tr class=HeaderOfTable>";
		$ret .= "<td width=30% nowrap>بروزرسانی کننده</td><td>شرح</td><td width=1% nowrap>تاریخ بروزرسانی</td>";
		$ret .= "</tr>";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select description, PersonType, 
						concat(p1.pfname, ' ', p1.plname) as PersonelName, 
						concat(p2.pfname, ' ', p2.plname) as StudentName, 
						concat(g2j(UpdateTime), ' ', substr(UpdateTime, 12,10)) as gUpdateTime 
						from FormsDataUpdateHistory
						LEFT JOIN hrms_total.persons p1 on (FormsDataUpdateHistory.PersonID=p1.PersonID)
						LEFT JOIN educ.persons p2 on (FormsDataUpdateHistory.PersonID=p2.PersonID)  
						where FormsDataUpdateHistory.FormsStructID='".$FormsStructID."' and RecID='".$RecID."' order by UpdateTime DESC";
		$res = $mysql->Execute($query);
		$i = 0;
		while($rec = $res->FetchRow())
		{
			$i++;
			if($i%2==0)
				$ret .= "<tr class=OddRow>";
			else
				$ret .= "<tr class=EvenRow>";
			
			if($rec["PersonType"]=="PERSONEL")
				$ret .= "<td>".$rec["PersonelName"]."</td>";
			else
				$ret .= "<td>".$rec["StudentName"]."</td>";
				
			$ret .= "<td>".$rec["description"]."</td>";
			$ret .= "<td nowrap>".$rec["gUpdateTime"]."</td>";
			$ret .= "</tr>";
		}
		$ret .= "</table>";
		/*
		$DetailTables = manage_FormsDetailTables::GetList($FormsStructID);
		for($i=0; $i<count($DetailTables); $i++)
		{
			$ret .= FormUtils::ShowUpdateHistory($DetailTables[$i]->DetailFormStructID, $RecID);
		}
		*/
		return $ret;
	}
	
	// فرمهای دریافتی را بر می گرداند
	// فرمهایی که به یکی از مراحلی که کاربر به آنها دسترسی دارد 
	static function GetReceivedForms($PersonID)
	{
		$PersonType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$PersonType = "STUDENT";
		
		$ret = "";
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
		$rec = $res->FetchRow();
		$UnitCode = $rec["UnitCode"];
		$SubUnitCode = $rec["sub_ouid"];
		$EduGrpCode = $rec["EduGrpCode"];
		
		$mysql->Execute("delete from ReceivedForms where PersonID='".$PersonID."'");
		//if($_SESSION["PersonID"]=="200852")
		//	$mysql->audit("مرحله ۱");
		$StepList = SecurityManager::GetUserPermittedSteps($PersonID, "");
		for($sc=0; $sc<count($StepList); $sc++)
		{
			$StepList[$sc]->GetRelatedRecords($PersonID, $UnitCode, $SubUnitCode, $EduGrpCode);
			//if($_SESSION["PersonID"]=="200852")
			//	$mysql->audit("مرحله 2: کد مرحله: ".$StepList[$sc]->FormsFlowStepID);
			
		}	

		$query = "select FormsStruct.FormsStructID, FormFlowStepID, ReceivedForms.RecID, StepTitle, SendDate, CreatorType, SenderType, FormTitle, ptitle, 
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
								LEFT JOIN educ.faculties on (StudentSpecs.FacCode=faculties.FacCode)
								
								LEFT JOIN hrms_total.org_units on (writs.ouid=org_units.ouid) 
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
			if($rec["SenderType"]=="PERSONEL")
				echo "<td nowrap>".$rec["CreatorName"]."<br>[".$rec["ptitle"]."]</td>";
			else
				echo "<td nowrap>".$rec["CreatorName2"]."<br>[".$rec["PFacName"]."]</td>";
			echo "<td nowrap>".$rec["StepTitle"]."</td>";
			if($rec["SenderType"]=="PERSONEL")
				echo "<td nowrap>".$rec["SenderName"]."</td>";
			else
				echo "<td nowrap>".$rec["SenderName2"]."</td>";
			echo "<td nowrap>".shdate($rec["SendDate"])."</td>";
		}
		return $ret;
	}

	// با گرفتن کد ساختار فرم اصلی و ساختار فرم جزییات نام فیلد کلید خارجی در جدول جزییات را بر می گرداند
	static function GetRelationField($MasterFormsStructID, $DetailFormsStructID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute("select RelatedField from FormsDetailTables where FormStructID='".$MasterFormsStructID."' and DetailFormStructID='".$DetailFormsStructID."'");
		if($rec = $res->FetchRow())
			return $rec[0];
		return "";
	}

	// تعداد فرمهای ارسال شده توسط شخص را بر می گرداند
	static function GetSentFormsCount($PersonID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select count(DISTINCT recID)
					 from FormsFlowHistory
					where FromPersonID='".$PersonID."'";
		$mysql->Execute($query);
		$res = $mysql->Execute($query);
		$rec = $res->FetchRow();
		return $rec[0];
	}
	
	// لیست فرمهای ارسالی شخص را بر می گرداند 
	// از آنجا که این لیست به تدریج بزرگ شده و باید صفحه بندی شود شماره رکورد شروع و تعداد رکوردهای مورد نظر هم به تابع پاس می شود
	static function GetSentForms($PersonID, $FromRec, $count)
	{
		$ret = array();
		$PersonType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$PersonType = "STUDENT";
		
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		/*
		$query = "select FormsRecords.SenderType, RecID, FormTitle, s1.StepTitle as FromStep, s2.StepTitle as ToStep, s3.StepTitle as CurrentStep,
					concat(g2j(FormsFlowHistory.SendDate), ' ', substr(FormsFlowHistory.SendDate, 12,10)) as gSendDate, 
					concat(p1.pfname, ' ', p1.plname) as PersonelName, 
					concat(p2.pfname, ' ', p2.plname) as StudentName 
					 from FormsFlowHistory
					JOIN FormsRecords on (FormsRecords.RelatedRecordID=FormsFlowHistory.RecID and FormsRecords.FormsStructID=FormsFlowHistory.FormsStructID)
					LEFT JOIN hrms_total.persons p1 on (FormsRecords.CreatorID=p1.PersonID)
					LEFT JOIN educ.persons p2 on (FormsRecords.CreatorID=p2.PersonID)
					JOIN FormsStruct on (FormsFlowHistory.FormsStructID=FormsStruct.FormsStructID)
					LEFT JOIN FormsFlowSteps s1 on (s1.FormsFlowStepID=FromStepID)
					LEFT JOIN FormsFlowSteps s2 on (s2.FormsFlowStepID=ToStepID)
					LEFT JOIN FormsFlowSteps s3 on (s3.FormsFlowStepID=FormsRecords.FormFlowStepID)
					where FromPersonID='".$PersonID."' order by FormsFlowHistory.SendDate DESC limit ".$FromRec.",".$count;
		*/
		$query = "select distinct FormsRecords.CreatorType, FormsRecords.FormsStructID, RecID, FormTitle, s3.StepTitle as CurrentStep,
					concat(p1.pfname, ' ', p1.plname) as PersonelName, 
					concat(p2.PFName, ' ', p2.PLName) as StudentName
					 from FormsFlowHistory
					JOIN FormsRecords on (FormsRecords.RelatedRecordID=FormsFlowHistory.RecID and FormsRecords.FormsStructID=FormsFlowHistory.FormsStructID)
					LEFT JOIN hrms_total.persons p1 on (FormsRecords.CreatorID=p1.PersonID)
					LEFT JOIN educ.persons p2 on (FormsRecords.CreatorID=p2.PersonID)
					JOIN FormsStruct on (FormsFlowHistory.FormsStructID=FormsStruct.FormsStructID)
					LEFT JOIN FormsFlowSteps s3 on (s3.FormsFlowStepID=FormsRecords.FormFlowStepID)
					where FromPersonID='".$PersonID."' order by FormsFlowHistory.SendDate DESC limit ".$FromRec.",".$count;
                 /*if ($_SESSION["UserID"] == 'h-akrami') {
                    echo $query;
                    die();
                }*/
		
		$mysql->Execute($query);
		$res = $mysql->Execute($query);
		$i = 0;
		while($rec = $res->FetchRow())
		{
			$ret[$i]->RecID = $rec["RecID"];
			$ret[$i]->SendDate = $rec["gSendDate"];
			$ret[$i]->FromStep = $rec["FromStep"];
			$ret[$i]->ToStep = $rec["ToStep"];
			$ret[$i]->FormTitle = $rec["FormTitle"];
			if($rec["CreatorType"]=="PERSONEL")
				$ret[$i]->CreatorName = $rec["PersonelName"];
			else
				$ret[$i]->CreatorName = $rec["StudentName"];
			$ret[$i]->CurrentStep = $rec["CurrentStep"];
			$ret[$i]->FormsStructID = $rec["FormsStructID"];
			$i++;
		}
		return $ret;
	}

	// با گرفتن کد ساختار جدول جزییات و کد رکورد مربوطه مشخص می کند ایجاد کننده آن رکورد چه کسی بوده است
	static function GetCreatorID($DetailFormsStructID, $RecID)
	{
		$query = "";
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select CreatorID from DetailFormRecords where DetailFormsStructID='".$DetailFormsStructID."' and DetailRecordID='".$RecID."'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
			return $rec[0];
		return 0;
	}
}
?>
