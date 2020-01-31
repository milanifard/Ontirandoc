<?

class FormUtils
{
	static function HTMLEncode($str)
	{
		$ret = "";
		foreach($str as $item)
		{
			if(!ctype_alnum($item))
				$ret .= "&#".ord($item).";";
			else
				$ret .= $item;
		}
		return $ret;
	}
	
	static function CreateFormTypesOptions($DefaultValue)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select * from FieldTypes order by FieldTypeID';
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$list = "";
		
		while($rec=$res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select * from roles order by RoleName';
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		$list = "";
		
		while($rec=$res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select * from systems order by description';
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		$list = "";
		
		while($rec=$res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select * from hrms_total.org_units order by ptitle';
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		$list = "";
		
		while($rec=$res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from hrms_total.org_sub_units where ouid='".$UnitID."' order by ptitle";

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		$list = "";
		
		while($rec=$res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from EducationalGroups order by PEduName";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		$list = "";
		
		while($rec=$res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from EducationalGroups where FacCode='".$FacCode."' order by PEduName";

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		$list = "";
		
		while($rec=$res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select ".$ValueFieldName.", ".$DescriptionFieldName." from ".$TableName;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		while($rec = $res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		while($rec = $res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from domains where DomainName='".$DomainName."' order by description";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		while($rec = $res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select ".$ValueFieldName.", ".$DescriptionFieldName." from ".$TableName." where ".$ValueFieldName."='".$FieldValue."'";

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		while($rec = $res->fetch())
		{
			$ret = $rec[$DescriptionFieldName];
		}
		return $ret;
	}
	
	// با توجه به یک پرس و جو شرح یک آیتم را بدست می آورد
	static function CreateItemDescriptionAccordingToQuery($query, $FieldValue)
	{
		$ret = "-";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		while($rec = $res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from baseinfo.domains where DomainName='".$DomainName."' and DomainValue='".$FieldValue."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		while($rec = $res->fetch())
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
		$mm = strlen($mm)==1 ? "0".$mm : $mm;
		$dd = strlen($dd)==1 ? "0".$dd : $dd;
		$yy = substr($yy, 2, 2);
		$CurDate = $yy."/".$mm."/".$dd;		
		$valuesMap = ["#CURRENT_DATE#" => $_SESSION["UserID"],
									"#CURRENT_PERSON_ID#" => $_SESSION["PersonID",
									"#IP_ADDRESS#" => $_SESSION['LIPAddress'],
									"#CUR_EDU_YEAR#" => $_SESSION["EduYear"],
									"#CUR_SEMESTER#" => $_SESSION["semester"],
									"#CUR_YEAR#" => $yy,
									"#CUR_MONTH#" => $mm,
									"#CUR_DAY#" => $dd,
									"#PRE_YEAR#" => $yy-1,
									"#PRE_MONTH#" => $mm-1,];
		if(in_array($KeyName, $valuesMap)){
			return $valuesMap[$KeyName];
		}
		return 	$KeyName;
	}

	// با گرفتن کد یک آیتم و کد ساختار مربوطه بررسی می کند آن رکورد در کدام مرحله قرار دارد
	static function GetCurrentStepID($RelatedRecordID, $FormsStructID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select FormFlowStepID from FormsRecords where FormsStructID='".$FormsStructID."' and RelatedRecordID='".$RelatedRecordID."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		if($rec = $res->fetch())
		{
			return $rec["FormFlowStepID"];
		}
		return 0;
	}

	// بررسی می کند آیا این مرحله در مسیر حرکت رکورد وجود داشته است یا خیر
	static function IsStepInRecordPath($StepID, $RecID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$Step = new be_FormsFlowSteps();
		$Step->LoadDataFromDatabase($StepID);
		
		$query = "select * from FormsFlowHistory where FormsStructID='".$Step->FormsStructID."' and RecID='".$RecID."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		if($rec = $res->fetch())
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select StepTitle, SenderType, 
						concat(p1.pfname, ' ', p1.plname) as PersonelName, 
						concat(p2.pfname, ' ', p2.plname) as StudentName, 
						concat(g2j(SendDate), ' ', substr(SendDate, 12,10)) as gSendDate 
						from FormsFlowHistory
						LEFT JOIN hrms_total.persons p1 on (FromPersonID=p1.PersonID)
						LEFT JOIN educ.persons p2 on (FromPersonID=p2.PersonID)  
						LEFT JOIN FormsFlowSteps on (FormsFlowStepID=ToStepID)  
						where FormsFlowHistory.FormsStructID='".$FormsStructID."' and RecID='".$RecID."' order by SendDate DESC";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		while($rec = $res->fetch())
		{
			$ret .= "<tr>";
			$ret .= "<td>".($rec["SenderType"]=="PERSONEL")?$rec["PersonelName"]:$rec["StudentName"]."</td>";
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
		$ret = "<table width='50%' align='center' border='1px' cellspacing='0' cellpadding='3px'>";
		$ret .= "<tr bgcolor='#cccccc'><td colspan='3'>".$CurForm->FormTitle."</td></tr>";
		$ret .= "<tr class='HeaderOfTable'>";
		$ret .= "<td width='30%' nowrap>".C_UPDATER."</td><td>".C_DETAILS."</td><td width='1%' nowrap>".C_UPDATE_DATE."</td>";
		$ret .= "</tr>";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select description, PersonType, 
						concat(p1.pfname, ' ', p1.plname) as PersonelName, 
						concat(p2.pfname, ' ', p2.plname) as StudentName, 
						concat(g2j(UpdateTime), ' ', substr(UpdateTime, 12,10)) as gUpdateTime 
						from FormsDataUpdateHistory
						LEFT JOIN hrms_total.persons p1 on (FormsDataUpdateHistory.PersonID=p1.PersonID)
						LEFT JOIN educ.persons p2 on (FormsDataUpdateHistory.PersonID=p2.PersonID)  
						where FormsDataUpdateHistory.FormsStructID='".$FormsStructID."' and RecID='".$RecID."' order by UpdateTime DESC";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		while($rec = $res->fetch())
		{
			$ret .= "<tr>";
			$ret .= "<td>".($rec["PersonType"]=="PERSONEL")?$rec["PersonelName"]:$rec["StudentName"]."</td>";
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		
			$query = "select * from projectmanagement.persons 
								where persons.PersonID='".$PersonID."'";

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		$rec = $res->fetch();

		$mysql->Prepare("delete from ReceivedForms where PersonID='".$PersonID."'");
		$mysql->ExecuteStatement([]);
		//if($_SESSION["PersonID"]=="200852")
		//	$mysql->audit("مرحله ۱");
		$StepList = SecurityManager::GetUserPermittedSteps($PersonID, "");
		foreach($StepList as $key => $value)
		{
			$StepList[$key]->GetRelatedRecords($PersonID, $UnitCode, $SubUnitCode, $EduGrpCode);
			//if($_SESSION["PersonID"]=="200852")
			//	$mysql->audit("مرحله 2: کد مرحله: ".$StepList[$sc]->FormsFlowStepID);
			
		}	

		$query = "select FormsStruct.FormsStructID, FormFlowStepID, ReceivedForms.RecID, StepTitle, SendDate, CreatorType, SenderType, FormTitle, 
								concat(p1.pfname,' ',p1.plname) as SenderName,
								concat(p2.pfname,' ',p2.plname) as CreatorName,
								from ReceivedForms
								LEFT JOIN FormsFlowSteps on (ReceivedForms.FormFlowStepID=FormsFlowSteps.FormsFlowStepID)
								LEFT JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID) 
								LEFT JOIN prjectmanagement.persons as p1 on (p1.PersonID=SenderID) 
								LEFT JOIN prjectmanagement.persons as p2 on (p2.PersonID=CreatorID)
								where ReceivedForms.PersonID='".$PersonID."' order by SendDate DESC";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		//if($_SESSION["PersonID"]=="200852")
		//		$mysql->audit("مرحله 4 ");		
		
		while($rec = $res->fetch())
		{
			echo "<tr>";
			echo "<td width='10%'>";
			echo "<a href='#' onclick='javascript: ViewForm(".$rec["FormsStructID"].", ".$rec["FormFlowStepID"].", ".$rec["RecID"].");'>";
			echo $rec["RecID"];
			echo "</a>";
			echo "</td>";
			echo "<td nowrap>".$rec["FormTitle"]."</td>";
			echo "<td nowrap>".$rec["CreatorName"]."<br>[".$rec["ptitle"]."]</td>";
			echo "<td nowrap>".$rec["StepTitle"]."</td>";
			echo "<td nowrap>".$rec["SenderName"]."</td>";
			echo "<td nowrap>".shdate($rec["SendDate"])."</td>";
		}
		return $ret;
	}

	// با گرفتن کد ساختار فرم اصلی و ساختار فرم جزییات نام فیلد کلید خارجی در جدول جزییات را بر می گرداند
	static function GetRelationField($MasterFormsStructID, $DetailFormsStructID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("select RelatedField from FormsDetailTables where FormStructID='".$MasterFormsStructID."' and DetailFormStructID='".$DetailFormsStructID."'");
		$res = $mysql->ExecuteStatement([]);
		if($rec = $res->fetch())
			return $rec[0];
		return "";
	}

	// تعداد فرمهای ارسال شده توسط شخص را بر می گرداند
	static function GetSentFormsCount($PersonID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select count(*)
					 from FormsFlowHistory
					where FromPersonID='".$PersonID."' ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		$rec = $res->fetch();
		return $rec[0];
	}
	
	// لیست فرمهای ارسالی شخص را بر می گرداند 
	// از آنجا که این لیست به تدریج بزرگ شده و باید صفحه بندی شود شماره رکورد شروع و تعداد رکوردهای مورد نظر هم به تابع پاس می شود
	static function GetSentForms($PersonID, $FromRec, $count)
	{
		$ret = [];
		$PersonType = ($_SESSION["SystemCode"]=="10")?"STUDENT":"PERSONEL";
		
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select distinct FormsRecords.SenderType, FormsRecords.FormsStructID, RecID, FormTitle, s3.StepTitle as CurrentStep,
					concat(p1.pfname, ' ', p1.plname) as PersonelName, 
					 from FormsFlowHistory
					JOIN FormsRecords on (FormsRecords.RelatedRecordID=FormsFlowHistory.RecID and FormsRecords.FormsStructID=FormsFlowHistory.FormsStructID)
					LEFT JOIN projectmanagement.persons p1 on (FormsRecords.CreatorID=p1.PersonID)
					JOIN FormsStruct on (FormsFlowHistory.FormsStructID=FormsStruct.FormsStructID)
					LEFT JOIN FormsFlowSteps s3 on (s3.FormsFlowStepID=FormsRecords.FormFlowStepID)
					where FromPersonID='".$PersonID."' order by FormsFlowHistory.SendDate DESC limit ".$FromRec.",".$count;
		
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		$i = 0;
		while($rec = $res->FetchRow())
		{
			$ret[$i]->RecID = $rec["RecID"];
			$ret[$i]->SendDate = $rec["gSendDate"];
			$ret[$i]->FromStep = $rec["FromStep"];
			$ret[$i]->ToStep = $rec["ToStep"];
			$ret[$i]->FormTitle = $rec["FormTitle"];
			if($rec["SenderType"]=="PERSONEL")
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select CreatorID from DetailFormRecords where DetailFormsStructID='".$DetailFormsStructID."' and DetailRecordID='".$RecID."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec = $res->fetch())
			return $rec[0];
		return 0;
	}
}
?>
