<?
class be_FormsFlowSteps
{
	public $FormsFlowStepID;		//
	public $FormsStructID;		//کد فرم مربوطه
	public $StepTitle;		//عنوان
	public $StepType;		//نوع مرحله (شروع/غیره)
	public $StudentPortalAccess;		//دسترسی از طریق پورتال دانشجویی
	public $StaffPortalAccess;		//دسترسی از طریق پورتال کارمندی
	public $ProfPortalAccess;		//دسترسی از طریق پورتال اساتید
	public $OtherPortalAccess;		//دسترسی از طریق پورتال سایر
	public $FilterType;		//نوع فیلتر اعمال شده برای دسترسی کاربران
	public $FilterOnUserRoles;		//فیلتر بر اساس نقش کاربران
	public $FilterOnSpecifiedUsers;		//فیلتر بر اساس کاربران خاص
	public $UserAccessRange;		//فضای داده های در دسترس کاربر (خودش - واحدش - زیر واحدش - گروه آموزشی خودش)
	public $RelatedOrganzationChartID;		//کد چارت سازمانی مربوطه
	public $AccessRangeRelatedPersonType; // مشخص می کند محدوده دسترسی در زمانیکه وابسته به چارت سازمانی است بر اساس سازنده فرم کنترل شود یا فرستنده فرم
	
	public $PreviousStep; // این مشخصه برای این گذاشته شد که مشخص کند این مرحله جزو مراحل قبلی است یا بعدی. در متدی که لیست مراحل مرتبط با یک مرحله را استخراج می کند کاربرد دارد

	public $FormTitle; // عنوان فرم مربوطه مستخرج از کد ساختار فرم
	
	public $ShowBarcodeInPrintPage; // کد فرم به شکل بارکد در بالای صفحه نمایش داده شود؟
	public $UserCanBackward; // کاربر می تواند از این مرحله فرم را به مراحل قبل برگشت بزند؟
	public $PrintPageHeader, $PrintPageFooter, $PrintPageTitle; // متن بالا و پایین صفحه و عنوان صفحه در حالت چاپی
	public $PrintPageSigniture; // نوع نمایش امضا در پایین خروجی چاپی فرم
	
	public $ShowHistoryInPrintPage; // سابقه ارسالها در انتهای صفحه چاپ نمایش داده شود
	
	public $NumberOfPermittedSend; // تعداد مجاز ارسال
	public $LimitationOfNumberPeriod; // نوع بازه محدود کننده برای ارسال
	public $SendDatePermittedStartDate, $SendDatePermittedEndDate; // مهلت شروع و پایان ارسال
	public $Shamsi_SendDatePermittedStartDate, $Shamsi_SendDatePermittedEndDate; // مهلت شروع و پایان ارسال - محاسبه شده به تاریخ شمسی
	
	function be_FormsFlowSteps() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute("select *
								, g2j(SendDatePermittedStartDate) as gSendDatePermittedStartDate
								, g2j(SendDatePermittedEndDate) as gSendDatePermittedEndDate 
								from formsgenerator.FormsFlowSteps
								JOIN  formsgenerator.FormsStruct using (FormsStructID)
								where FormsFlowStepID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FormsFlowStepID=$rec["FormsFlowStepID"];
			$this->FormsStructID=$rec["FormsStructID"];
			$this->StepTitle=$rec["StepTitle"];
			$this->StepType=$rec["StepType"];
			$this->StudentPortalAccess=$rec["StudentPortalAccess"];
			$this->StaffPortalAccess=$rec["StaffPortalAccess"];
			$this->ProfPortalAccess=$rec["ProfPortalAccess"];
			$this->OtherPortalAccess=$rec["OtherPortalAccess"];
			$this->FilterType=$rec["FilterType"];
			$this->FilterOnUserRoles=$rec["FilterOnUserRoles"];
			$this->FilterOnSpecifiedUsers=$rec["FilterOnSpecifiedUsers"];
			$this->UserAccessRange=$rec["UserAccessRange"];
			$this->RelatedOrganzationChartID=$rec["RelatedOrganzationChartID"];
			$this->FormTitle=$rec["FormTitle"];
			$this->AccessRangeRelatedPersonType=$rec["AccessRangeRelatedPersonType"];
			
			$this->ShowBarcodeInPrintPage=$rec["ShowBarcodeInPrintPage"];
			$this->UserCanBackward=$rec["UserCanBackward"];
			$this->PrintPageHeader=$rec["PrintPageHeader"];
			$this->PrintPageFooter=$rec["PrintPageFooter"];
			$this->PrintPageTitle=$rec["PrintPageTitle"];
			$this->PrintPageSigniture=$rec["PrintPageSigniture"];
			$this->ShowHistoryInPrintPage=$rec["ShowHistoryInPrintPage"];

			$this->NumberOfPermittedSend=$rec["NumberOfPermittedSend"];
			$this->LimitationOfNumberPeriod=$rec["LimitationOfNumberPeriod"];
			$this->SendDatePermittedStartDate=$rec["SendDatePermittedStartDate"];
			$this->SendDatePermittedEndDate=$rec["SendDatePermittedEndDate"];
			$this->Shamsi_SendDatePermittedStartDate=$rec["gSendDatePermittedStartDate"];
			$this->Shamsi_SendDatePermittedEndDate=$rec["gSendDatePermittedEndDate"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FormsFlowStepID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد فرم مربوطه </td><td>".$this->FormsStructID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>عنوان </td><td>".$this->StepTitle."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>نوع مرحله (شروع/غیره) </td><td>".$this->StepType."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>دسترسی از طریق پورتال دانشجویی </td><td>".$this->StudentPortalAccess."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>دسترسی از طریق پورتال کارمندی </td><td>".$this->StaffPortalAccess."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>دسترسی از طریق پورتال اساتید </td><td>".$this->ProfPortalAccess."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>دسترسی از طریق پورتال سایر </td><td>".$this->OtherPortalAccess."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>نوع فیلتر اعمال شده برای دسترسی کاربران </td><td>".$this->FilterType."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>فیلتر بر اساس نقش کاربران </td><td>".$this->FilterOnUserRoles."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>فیلتر بر اساس کاربران خاص </td><td>".$this->FilterOnSpecifiedUsers."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>فضای داده های در دسترس کاربر (خودش - واحدش - زیر واحدش - گروه آموزشی خودش) </td><td>".$this->UserAccessRange."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد چارت سازمانی مربوطه </td><td>".$this->RelatedOrganzationChartID."</td>";
		echo "</tr>";
		echo "</table>";
	}

	// 
	// لیست فرمهایی - رکوردهایی - که در این مرحله قرار دارد را به صورت ساختاری در یک آرایه بر می گرداند
	 // تنها رکوردهایی را بر می گرداند که در حوزه دسترسی کاربر پاس شده به تابع هستند
	function GetRelatedRecords($PersonID, $UnitCode, $SubUnitCode, $EduGrpCode)
	{
		//$PersonID = $_SESSION["PersonID"];
		$PersonType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$PersonType = "STUDENT";
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		
		$query = "select CreatorType, SenderType, CreatorID, SendDate, StepTitle, FormsFlowSteps.FormsStructID, RelatedRecordID, FormsRecords.FormFlowStepID, SenderID, concat(plname, ' ', pfname) as SenderName, FormTitle
					 from formsgenerator.FormsRecords
					JOIN formsgenerator.FormsFlowSteps on (FormsFlowSteps.FormsFlowStepID=FormsRecords.FormFlowStepID)
					JOIN formsgenerator.FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID)";
		if($PersonType=="PERSONEL")		
			$query .= "	LEFT JOIN hrms_total.persons on (FormsRecords.CreatorID=persons.PersonID) 
						LEFT JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=staff.person_type)
						LEFT JOIN hrms_total.writs on (staff.staff_id=writs.staff_id and staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver) ";
		else
			$query .= " LEFT JOIN educ.persons on (FormsRecords.CreatorID=persons.PersonID)
						LEFT JOIN educ.StudentSpecs using (PersonID)
						LEFT JOIN educ.StudyFields using (FldCode) "; 
		
		$query .= "	where (FormFlowStepID='".$this->FormsFlowStepID."' ";
		if($this->UserAccessRange=="HIM") // فقط فرمهای خودش را ببیند
			$query .= " and FormsRecords.CreatorID='".$PersonID."'";
		if($PersonType=="PERSONEL")		
		if($this->UserAccessRange=="UNIT") // فقط فرمهای واحد را ببیند
		{
			if($PersonType=="PERSONEL")
				$query .= " and staff.UnitCode='".$UnitCode."'";
			else
				$query .= " and StudentSpecs.FacCode='".$UnitCode."'";
		}
		else if($this->UserAccessRange=="SUB_UNIT") // فقط فرمهای زیر واحد را ببیند
		{
			if($PersonType=="PERSONEL")
				$query .= " and writs.ouid='".$UnitCode."' and writs.sub_ouid='".$SubUnitCode."'";
			else
				$query .= " and EduGrpCode='".$SubUnitCode."' ";
		}
		else if($this->UserAccessRange=="EDU_GROUP") // فقط فرمهای گروه آموزشی را ببیند
		{
			if($PersonType=="PERSONEL")
				$query .= " and staff.UnitCode='".$EduGrpCode."'";
			else
				$query .= " and EduGrpCode='".$SubUnitCode."' ";
		}
		else if($this->UserAccessRange=="BELOW_IN_CHART_ALL_LEVEL") // فقط فرمهای افراد زیر مجموعه اش را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetAllChildsOfPerson($this->RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($this->AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2";
		}
		else if($this->UserAccessRange=="BELOW_IN_CHART_LEVEL1") // فقط فرمهای افراد زیر مجموعه سطح اولش را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetLevel1ChildsOfPerson($this->RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($this->AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2";
		}
		else if($this->UserAccessRange=="BELOW_IN_CHART_LEVEL2") // فقط فرمهای افراد زیر مجموعه سطح دوم را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetLevel2ChildsOfPerson($this->RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($this->AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2";
		}
		else if($this->UserAccessRange=="BELOW_IN_CHART_LEVEL3") // فقط فرمهای افراد زیر مجموعه سطح سوم را ببیند
		{
			$PersonCondition = "";
			$PersonList = ChartServices::GetLevel3ChildsOfPerson($this->RelatedOrganzationChartID, $PersonID);
			for($i=0; $i<count($PersonList); $i++)
			{
				if($PersonCondition!="")
					$PersonCondition .= ", ";
				$PersonCondition .= $PersonList[$i]->PersonID;
			}
			// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
			if($PersonCondition!="")
			{
				if($this->AccessRangeRelatedPersonType=="CREATOR")
					$query .= " and FormsRecords.CreatorID in (".$PersonCondition.") ";
				else
					$query .= " and FormsRecords.SenderID in (".$PersonCondition.") ";
			}
			else
				$query .= " and 1=2";
		}
		
		else if($this->UserAccessRange=="UNDER_MANAGEMENT") // فقط فرمهای افراد تحت مدیریت
		{
			if(ChartServices::IsHeManager($PersonID,$this->RelatedOrganzationChartID))
			{
				// اگر شخص مدیر باشد کلیه افراد زیر مجموعه جزو افراد تحت مدیریت او محسوب می شوند
				$PersonCondition = "";
				$PersonList = ChartServices::GetAllChildsOfPerson($this->RelatedOrganzationChartID, $PersonID);
				for($i=0; $i<count($PersonList); $i++)
				{
					if($PersonCondition!="")
						$PersonCondition .= ", ";
					$PersonCondition .= $PersonList[$i]->PersonID;
				}
				// در حالتیکه هیچ فردی در زیر مجموعه فرد نباشد بنابراین هیچ نتیجه ای هم نباید نشان داده شود پس یک شرط همیشه نادرست اضافه می شود
				if($PersonCondition!="")
				{
					if($this->AccessRangeRelatedPersonType=="CREATOR")
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
		$query .= ") ";  //or (FormFlowStepID='".$this->FormsFlowStepID."' and )
		//$query .= " order by SendDate DESC";
		
		// کیوری زیر کامنت شد چون می توان آن را به صورت یکجا و برای همه مراحل بدست آورد و اجرای آن به ازای هر مرحله سیستم را کند می کند
		/*
		$query .= " union select CreatorType, FormsRecords.SenderType, CreatorID, FormsRecords.SendDate, StepTitle, FormsFlowSteps.FormsStructID, RelatedRecordID, FormsRecords.FormFlowStepID, SenderID, concat(plname, ' ', pfname) as SenderName, FormTitle 
					from FormsRecords 
					JOIN FormsFlowSteps on (FormsFlowSteps.FormsFlowStepID=FormsRecords.FormFlowStepID) 
					JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID) 
					JOIN FormsFlowHistory on (FormsFlowHistory.FormsStructID=FormsFlowSteps.FormsStructID and FormsFlowHistory.RecID=FormsRecords.RelatedRecordID)
					LEFT JOIN hrms_total.persons on (FormsRecords.CreatorID=persons.PersonID) 
					LEFT JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=staff.person_type) 
					LEFT JOIN hrms_total.writs on (staff.staff_id=writs.staff_id and staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver) 
					where (FormFlowStepID='".$this->FormsFlowStepID."' and FormsFlowHistory.FromPersonID=".$PersonID." and FormsRecords.SenderID<>'".$PersonID."')";
		*/
		
		/*
		if($_SESSION["PersonID"]=="201309")
		{
				echo $query."<br><br>"; 
			return;
		}
		*/
		
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
			/*
			$ret[$i]->RelatedRecordID = $rec["RelatedRecordID"];
			$ret[$i]->FormFlowStepID = $rec["FormFlowStepID"];
			$ret[$i]->SenderID = $rec["SenderID"];
			$ret[$i]->CreatorID = $rec["CreatorID"];
			$ret[$i]->SendDate = $rec["SendDate"];
			$ret[$i]->ShamsiSendDate = $rec["gSendDate"];
			$ret[$i]->SenderName = $rec["SenderName"];
			$ret[$i]->FormTitle = $rec["FormTitle"];
			$ret[$i]->FormsStructID = $rec["FormsStructID"];
			$ret[$i]->StepTitle = $rec["StepTitle"];
			$i++;
			*/
		}
		//return $ret;
	}

}
class manage_FormsFlowSteps
{
	static function PrintTree($StepID, $ShowAccessList, $Level = 0, $LastChild = false, $Padding = "")
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		if($Level==0)
			echo "<table cellspacing=0 border=0 cellpadding=0>";
		echo "<tr><td style='vertical-align:middle'>";
		$obj = new be_FormsFlowSteps();
		$obj->LoadDataFromDatabase($StepID);
		//if($Level>0)
		{
			// Padding: یک رشته کاراکتری که برای هر نودی که نمایش داده می شود از آن برای نمایش علایم قبل از عنوان نود استفاده می شود
			// به ازای هر تصویر (علامت) یک کاراکتر در آن به کار رفته که در زمان نمایش با کد مناسب برای نمایش تصویر جایگزین می شود
			for($i=0; $i<10; $i++)
				$Padding .=" ";
			
			if($LastChild)
				$Padding .= "L";
			else
				$Padding .= "T";
			$tmp = $Padding;
			$tmp = str_replace("L", "<img style='vertical-align:middle' src='images/l.gif'>", $tmp);
			$tmp = str_replace("T", "<img style='vertical-align:middle' src='images/t.gif'>", $tmp);
			$tmp = str_replace("I", "<img style='vertical-align:middle' src='images/i.gif'>", $tmp);
			$tmp = str_replace("N", "<img style='vertical-align:middle' src='images/noexpand.gif'>", $tmp);
			echo $tmp;
		}
		echo "&nbsp;".$obj->StepTitle;
		if($ShowAccessList)
		{
			$res = $mysql->Execute("select * from FieldsAccessType
										JOIN FormFields using (FormFieldID)  
										where FormFlowStepID='".$obj->FormsFlowStepID."'");
			echo "<br>";
			echo "<table border=1 align=left cellspacing=0>";
			while($rec = $res->FetchRow())
			{
				echo "<tr>";
				echo "<td nowrap>".$rec["FieldTitle"]."</td>";
				echo "<td nowrap>".$rec["AccessType"]."</td>";
				echo "</tr>";				
			}
			echo "</table>";			
			/*
			$res = $mysql->Execute("select * from FieldsAccessType
										JOIN FormFields using (FormFieldID)  
										where FormFlowStepID='".$obj->FormsFlowStepID."' and AccessType='EDITABLE'");
			$i = 0;
			$j = 0;
			echo "<br>";
			while($rec = $res->FetchRow())
			{
				echo $tmp;
				if($i==0)
					echo " <b>ویرایش: </b><br>";
				echo $rec["FieldTitle"]."<br>";
				$i++;
			}
			$res = $mysql->Execute("select * from FieldsAccessType
										JOIN FormFields using (FormFieldID)  
										where FormFlowStepID='".$obj->FormsFlowStepID."' and AccessType='READ_ONLY'");
			while($rec = $res->FetchRow())
			{
				if($j==0)
				{
					if($i>0)
						echo " ";
					echo " <b>مشاهده: </b>";
				}
				else
					echo " - ";
				echo $rec["FieldTitle"];
				$j++;
			}
			if($i>0 || $j>0)
			{
				echo " <b>]</b>";
			}
			*/
		}		
				
		if($Level>0 && $LastChild)
			$Padding = substr($Padding, 0, count($Padding)-2)."N";
		else
			$Padding = substr($Padding, 0, count($Padding)-2)."I";
		
		$res = $mysql->Execute("select count(*) from formsgenerator.FormsFlowStepRelations where FormFlowStepID='".$StepID."'");
		$rec = $res->FetchRow();
		$TotalCount = $rec[0];
		
		$res = $mysql->Execute("select * from formsgenerator.FormsFlowStepRelations where FormFlowStepID='".$StepID."'");
		$i = 0;
		while($rec = $res->FetchRow())
		{
			if($i==$TotalCount-1)
				$LastChild = true;
			else
				$LastChild = false;
			manage_FormsFlowSteps::PrintTree($rec["NextStepID"], $ShowAccessList, $Level+1, $LastChild, $Padding);
			$i++;
		}
	}	
	
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select count(FormsFlowStepID) as TotalCount from FormsFlowSteps';
		if($WhereCondition!="")
		{
			$query .= ' where '.$WhereCondition;
		}
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	static function GetLastID()
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select max(FormsFlowStepID) as MaxID from FormsFlowSteps';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	
	static function Add($FormsStructID, $StepTitle, $StepType, $StudentPortalAccess, $StaffPortalAccess, $ProfPortalAccess, $OtherPortalAccess, $FilterType, $FilterOnUserRoles, $FilterOnSpecifiedUsers, $UserAccessRange, $RelatedOrganzationChartID, $AccessRangeRelatedPersonType, $ShowBarcodeInPrintPage, $UserCanBackward, $PrintPageHeader, $PrintPageFooter, $PrintPageTitle, $PrintPageSigniture, $ShowHistoryInPrintPage, $NumberOfPermittedSend, $LimitationOfNumberPeriod, $SendDatePermittedStartDate, $SendDatePermittedEndDate)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FormsFlowSteps (FormsStructID
				, StepTitle
				, StepType
				, StudentPortalAccess
				, StaffPortalAccess
				, ProfPortalAccess
				, OtherPortalAccess
				, FilterType
				, FilterOnUserRoles
				, FilterOnSpecifiedUsers
				, UserAccessRange
				, RelatedOrganzationChartID
				, AccessRangeRelatedPersonType
				,ShowBarcodeInPrintPage
				, UserCanBackward
				, PrintPageHeader
				, PrintPageFooter
				, PrintPageTitle
				, PrintPageSigniture
				, ShowHistoryInPrintPage
				, NumberOfPermittedSend
				, LimitationOfNumberPeriod
				, SendDatePermittedStartDate
				, SendDatePermittedEndDate
				) values ('".$FormsStructID."'
				, '".$StepTitle."'
				, '".$StepType."'
				, '".$StudentPortalAccess."'
				, '".$StaffPortalAccess."'
				, '".$ProfPortalAccess."'
				, '".$OtherPortalAccess."'
				, '".$FilterType."'
				, '".$FilterOnUserRoles."'
				, '".$FilterOnSpecifiedUsers."'
				, '".$UserAccessRange."'
				, '".$RelatedOrganzationChartID."'
				, '".$AccessRangeRelatedPersonType."'
				, '".$ShowBarcodeInPrintPage."'
				, '".$UserCanBackward."'
				, '".$PrintPageHeader."'
				, '".$PrintPageFooter."'
				, '".$PrintPageTitle."'
				, '".$PrintPageSigniture."'
				, '".$ShowHistoryInPrintPage."'
				, '".$NumberOfPermittedSend."'
				, '".$LimitationOfNumberPeriod."'
				, '".$SendDatePermittedStartDate."'
				, '".$SendDatePermittedEndDate."'
				)";
		//echo $query;
		$mysql->Execute($query);
		$mysql->audit("ایجاد یک مرحله از گردش فرم [".manage_FormsFlowSteps::GetLastID()."]");
	}
	
	static function Update($UpdateRecordID, $StepTitle, $StepType, $StudentPortalAccess, $StaffPortalAccess, $ProfPortalAccess, $OtherPortalAccess, $FilterType, $FilterOnUserRoles, $FilterOnSpecifiedUsers, $UserAccessRange, $RelatedOrganzationChartID, $AccessRangeRelatedPersonType, $ShowBarcodeInPrintPage, $UserCanBackward, $PrintPageHeader, $PrintPageFooter, $PrintPageTitle, $PrintPageSigniture, $ShowHistoryInPrintPage, $NumberOfPermittedSend, $LimitationOfNumberPeriod, $SendDatePermittedStartDate, $SendDatePermittedEndDate)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FormsFlowSteps set StepTitle='".$StepTitle."'
				, StepType='".$StepType."'
				, StudentPortalAccess='".$StudentPortalAccess."'
				, StaffPortalAccess='".$StaffPortalAccess."'
				, ProfPortalAccess='".$ProfPortalAccess."'
				, OtherPortalAccess='".$OtherPortalAccess."'
				, FilterType='".$FilterType."'
				, FilterOnUserRoles='".$FilterOnUserRoles."'
				, FilterOnSpecifiedUsers='".$FilterOnSpecifiedUsers."'
				, UserAccessRange='".$UserAccessRange."'
				, RelatedOrganzationChartID='".$RelatedOrganzationChartID."'
				, AccessRangeRelatedPersonType='".$AccessRangeRelatedPersonType."'
				, ShowBarcodeInPrintPage='".$ShowBarcodeInPrintPage."'
				, UserCanBackward='".$UserCanBackward."'
				, PrintPageHeader='".$PrintPageHeader."'
				, PrintPageFooter='".$PrintPageFooter."'
				, PrintPageTitle='".$PrintPageTitle."'
				, PrintPageSigniture='".$PrintPageSigniture."'
				, ShowHistoryInPrintPage='".$ShowHistoryInPrintPage."'
				, NumberOfPermittedSend='".$NumberOfPermittedSend."'
				, LimitationOfNumberPeriod='".$LimitationOfNumberPeriod."'
				, SendDatePermittedStartDate='".$SendDatePermittedStartDate."'
				, SendDatePermittedEndDate='".$SendDatePermittedEndDate."'
				where FormsFlowStepID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی یک مرحله از گردش فرم [".$UpdateRecordID."]");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "delete from FormsFlowSteps where FormsFlowStepID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف یک مرحله از گردش فرم [".$RemoveRecordID."]");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select *
						, g2j(SendDatePermittedStartDate) as gSendDatePermittedStartDate
						, g2j(SendDatePermittedEndDate) as gSendDatePermittedEndDate 
					 from FormsFlowSteps ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FormsFlowSteps();
			$ret[$k]->FormsFlowStepID=$rec["FormsFlowStepID"];
			$ret[$k]->FormsStructID=$rec["FormsStructID"];
			$ret[$k]->StepTitle=$rec["StepTitle"];
			$ret[$k]->StepType=$rec["StepType"];
			$ret[$k]->StudentPortalAccess=$rec["StudentPortalAccess"];
			$ret[$k]->StaffPortalAccess=$rec["StaffPortalAccess"];
			$ret[$k]->ProfPortalAccess=$rec["ProfPortalAccess"];
			$ret[$k]->OtherPortalAccess=$rec["OtherPortalAccess"];
			$ret[$k]->FilterType=$rec["FilterType"];
			$ret[$k]->FilterOnUserRoles=$rec["FilterOnUserRoles"];
			$ret[$k]->FilterOnSpecifiedUsers=$rec["FilterOnSpecifiedUsers"];
			$ret[$k]->UserAccessRange=$rec["UserAccessRange"];
			$ret[$k]->RelatedOrganzationChartID=$rec["RelatedOrganzationChartID"];
			$ret[$k]->AccessRangeRelatedPersonType=$rec["AccessRangeRelatedPersonType"];

			$ret[$k]->ShowBarcodeInPrintPage=$rec["ShowBarcodeInPrintPage"];
			$ret[$k]->UserCanBackward=$rec["UserCanBackward"];
			$ret[$k]->PrintPageHeader=$rec["PrintPageHeader"];
			$ret[$k]->PrintPageFooter=$rec["PrintPageFooter"];
			$ret[$k]->PrintPageTitle=$rec["PrintPageTitle"];
			$ret[$k]->PrintPageSigniture=$rec["PrintPageSigniture"];
			$ret[$k]->ShowHistoryInPrintPage=$rec["ShowHistoryInPrintPage"];
			
			$ret[$k]->NumberOfPermittedSend=$rec["NumberOfPermittedSend"];
			$ret[$k]->LimitationOfNumberPeriod=$rec["LimitationOfNumberPeriod"];
			$ret[$k]->SendDatePermittedStartDate=$rec["SendDatePermittedStartDate"];
			$ret[$k]->SendDatePermittedEndDate=$rec["SendDatePermittedEndDate"];
			$ret[$k]->Shamsi_SendDatePermittedStartDate=$rec["gSendDatePermittedStartDate"];
			$ret[$k]->Shamsi_SendDatePermittedEndDate=$rec["gSendDatePermittedEndDate"];
			$k++;
		}
		return $ret;
	}
	static function CreateListOptions($WhereCondition)
	{
		$ret = "";
		$res = manage_FormsFlowSteps::GetList($WhereCondition);
		for($i=0; $i<count($res); $i++)
		{
			$ret .= "<option value='".$res[$i]->FormsFlowStepID."'>".$res[$i]->StepTitle;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormsFlowSteps ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
	static function GetStartStepID($FormsStructID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select FormsFlowStepID from FormsFlowSteps where FormsStructID='".$FormsStructID."' and StepType='START'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			return $rec[0];
		}
		return 0;
	}
}
?>
