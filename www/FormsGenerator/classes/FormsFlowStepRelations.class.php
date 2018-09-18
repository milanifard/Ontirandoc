<?
class be_FormsFlowStepRelations
{
	public $FormFlowStepRelationID;		//
	public $FormFlowStepID;		//کد مرحله
	public $NextStepID;		//کد مرحله بعدی

	public $NextStepName; // نام مرحله بعدی مستخرج از کد مرحله بعدی
	public $NextStepUserAccessRange;
	
	function be_FormsFlowStepRelations() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$res = $mysql->Execute("select * from FormsFlowStepRelations 
										LEFT JOIN FormsFlowSteps on (FormsFlowStepID=NextStepID) 
										where FormFlowStepRelationID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FormFlowStepRelationID=$rec["FormFlowStepRelationID"];
			$this->FormFlowStepID=$rec["FormFlowStepID"];
			$this->NextStepID=$rec["NextStepID"];
			$this->NextStepName=$rec["StepTitle"];
			$this->NextStepUserAccessRange = $rec["UserAccessRange"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FormFlowStepRelationID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد مرحله </td><td>".$this->FormFlowStepID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد مرحله بعدی </td><td>".$this->NextStepID."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FormsFlowStepRelations
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select count(FormFlowStepRelationID) as TotalCount from FormsFlowStepRelations';
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
		$query = 'select max(FormFlowStepRelationID) as MaxID from FormsFlowStepRelations';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormFlowStepID, $NextStepID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FormsFlowStepRelations (FormFlowStepID
				, NextStepID
				) values ('".$FormFlowStepID."'
				, '".$NextStepID."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در ارتباط مراحل در گردش فرم با کد ".manage_FormsFlowStepRelations::GetLastID());
	}
	static function Update($UpdateRecordID, $NextStepID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FormsFlowStepRelations set NextStepID='".$NextStepID."'
				where FormFlowStepRelationID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در ارتباط مراحل در گردش فرم");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "delete from FormsFlowStepRelations where FormFlowStepRelationID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از ارتباط مراحل در گردش فرم");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormsFlowStepRelations LEFT JOIN FormsFlowSteps on (FormsFlowStepID=NextStepID) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FormsFlowStepRelations();
			$ret[$k]->FormFlowStepRelationID=$rec["FormFlowStepRelationID"];
			$ret[$k]->FormFlowStepID=$rec["FormFlowStepID"];
			$ret[$k]->NextStepID=$rec["NextStepID"];
			$ret[$k]->NextStepName=$rec["StepTitle"];
			$ret[$k]->NextStepUserAccessRange = $rec["UserAccessRange"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormsFlowStepRelations LEFT JOIN FormsFlowSteps on (FormsFlowStepID=NextStepID) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
	
	// برای یک مرحله شروع مراحل بعدی را بدست می آورد
	static function GetRelatedStepsForAnStartStep($StepID, $CreatorID)
	{
		$SenderID = $CreatorID;
		require_once("FormsFlowSteps.class.php");
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		
		//$CreatorID = $rec["CreatorID"];
		$query = "select * from FormsFlowStepRelations LEFT JOIN FormsFlowSteps on (FormsFlowStepID=NextStepID) 
					where FormsFlowStepRelations.FormFlowStepID='".$StepID."'";
		
		$res = $mysql->Execute($query);
		$i=0;
		// در نظر گرفتن مراحل بعد از مرحله فعلی
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FormsFlowSteps();
			// بعدا بهینه سازی شود یعنی به جای بار شدن دوباره از دیتابیس در تک تک فیلدها قرار گیرد
			$ret[$k]->LoadDataFromDatabase($rec["NextStepID"]);
			$ret[$k]->PreviousStep = 0;
			if($ret[$k]->UserAccessRange=="UNDER_MANAGEMENT")
			{
				if($ret[$k]->AccessRangeRelatedPersonType=="CREATOR")
					$PName = ChartServices::GetLastParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				else
					$PName = ChartServices::GetLastParentPerson($SenderID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				$ret[$k]->StepTitle .= " (".$PName.")";
			}
			else if($ret[$k]->UserAccessRange=="BELOW_IN_CHART_LEVEL1")
			{
				if($ret[$k]->AccessRangeRelatedPersonType=="CREATOR")
					$PName = ChartServices::GetFirstLevelParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				else
					$PName = ChartServices::GetFirstLevelParentPerson($SenderID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				// در صورتیکه پدری در چارت سازمانی مربوطه برای ایجاد کننده وجود نداشته باشد و مرحله بعدی برای پدر باشد اصلا نباید اجازه بدهد این مرحله انتخاب شود بنابراین عنوان مرحله را خالی می کند تا در زمان نمایش عناوین خالی نشان داده نشوند
				if($PName!="-")
					$ret[$k]->StepTitle .= " (".$PName.")";
			}
			else if($ret[$k]->UserAccessRange=="BELOW_IN_CHART_LEVEL2")
			{
				if($ret[$k]->AccessRangeRelatedPersonType=="CREATOR")
					$PName = ChartServices::GetLevel2ParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				else
					$PName = ChartServices::GetLevel2ParentPerson($SenderID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				// در صورتیکه پدری در چارت سازمانی مربوطه برای ایجاد کننده وجود نداشته باشد و مرحله بعدی برای پدر باشد اصلا نباید اجازه بدهد این مرحله انتخاب شود بنابراین عنوان مرحله را خالی می کند تا در زمان نمایش عناوین خالی نشان داده نشوند
				if($PName!="-")
					$ret[$k]->StepTitle .= " (".$PName.")";
			}
			else if($ret[$k]->UserAccessRange=="BELOW_IN_CHART_LEVEL3")
			{
				if($ret[$k]->AccessRangeRelatedPersonType=="CREATOR")
					$PName = ChartServices::GetLevel3ParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				else
					$PName = ChartServices::GetLevel3ParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				// در صورتیکه پدری در چارت سازمانی مربوطه برای ایجاد کننده وجود نداشته باشد و مرحله بعدی برای پدر باشد اصلا نباید اجازه بدهد این مرحله انتخاب شود بنابراین عنوان مرحله را خالی می کند تا در زمان نمایش عناوین خالی نشان داده نشوند
				if($PName!="-")
					$ret[$k]->StepTitle .= " (".$PName.")";
			}
			$k++;
		}
		return $ret;
	}
	
	// تمام مراحل مرتبط با مرحله پاس شده به تابع را بر می گرداند - شامل مراحل قبل و بعد
	// کد رکورد مربوطه برای این به تابع پاس شده است که تشخیص مراحل قبلی امکانپذیر باشد
	// زیرا داده ممکن است از مسیرهای مختلفی به یک مرحله برسد و تنها باید بتوان داده را به مرحله ای که از آنجا آمده برگشت زد
	static function GetRelatedSteps($StepID, $RelatedRecordID)
	{
		require_once("FormsFlowSteps.class.php");
		$CreatorID = 0;
		$SenderID = 0;
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		
		$query = "select * from FormsRecords where RelatedRecordID='".$RelatedRecordID."' and FormFlowStepID='".$StepID."'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			$CreatorID = $rec["CreatorID"];
			$SenderID = $rec["SenderID"];
			//echo $SenderID;
		}
		
		
		$query = "select * from FormsFlowStepRelations LEFT JOIN FormsFlowSteps on (FormsFlowStepID=NextStepID) 
					where FormsFlowStepRelations.FormFlowStepID='".$StepID."'";
		$res = $mysql->Execute($query);
		$i=0;
		// در نظر گرفتن مراحل بعد از مرحله فعلی
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FormsFlowSteps();
			// بعدا بهینه سازی شود یعنی به جای بار شدن دوباره از دیتابیس در تک تک فیلدها قرار گیرد
			$ret[$k]->LoadDataFromDatabase($rec["NextStepID"]);
			$ret[$k]->PreviousStep = 0;
			if($ret[$k]->UserAccessRange=="UNDER_MANAGEMENT")
			{
				if($ret[$k]->AccessRangeRelatedPersonType=="CREATOR")
					$PName = ChartServices::GetLastParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				else
					//$PName = ChartServices::GetLastParentPerson($SenderID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
					$PName = ChartServices::GetLastParentPerson($_SESSION["PersonID"], $ret[$k]->RelatedOrganzationChartID)->PersonName;
				$ret[$k]->StepTitle .= " (".$PName.")";
			}
			else if($ret[$k]->UserAccessRange=="BELOW_IN_CHART_LEVEL1")
			{
				if($ret[$k]->AccessRangeRelatedPersonType=="CREATOR")
					$PName = ChartServices::GetFirstLevelParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				else
					//$PName = ChartServices::GetFirstLevelParentPerson($SenderID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
					$PName = ChartServices::GetFirstLevelParentPerson($_SESSION["PersonID"], $ret[$k]->RelatedOrganzationChartID)->PersonName;
				// در صورتیکه پدری در چارت سازمانی مربوطه برای ایجاد کننده وجود نداشته باشد و مرحله بعدی برای پدر باشد اصلا نباید اجازه بدهد این مرحله انتخاب شود بنابراین عنوان مرحله را خالی می کند تا در زمان نمایش عناوین خالی نشان داده نشوند
				if($PName!="-")
					$ret[$k]->StepTitle .= " (".$PName.")";
				else
					$ret[$k]->StepTitle = "";
			}
			else if($ret[$k]->UserAccessRange=="BELOW_IN_CHART_LEVEL2")
			{
				if($ret[$k]->AccessRangeRelatedPersonType=="CREATOR")
					$PName = ChartServices::GetLevel2ParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				else
					//$PName = ChartServices::GetLevel2ParentPerson($SenderID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
					$PName = ChartServices::GetLevel2ParentPerson($_SESSION["PersonID"], $ret[$k]->RelatedOrganzationChartID)->PersonName;
					
				// در صورتیکه پدری در چارت سازمانی مربوطه برای ایجاد کننده وجود نداشته باشد و مرحله بعدی برای پدر باشد اصلا نباید اجازه بدهد این مرحله انتخاب شود بنابراین عنوان مرحله را خالی می کند تا در زمان نمایش عناوین خالی نشان داده نشوند
				if($PName!="-")
				{
					$ret[$k]->StepTitle .= " (".$PName.")";
				}
				else
				{
					$ret[$k]->StepTitle = "";
				}
			}
			else if($ret[$k]->UserAccessRange=="BELOW_IN_CHART_LEVEL3")
			{
				if($ret[$k]->AccessRangeRelatedPersonType=="CREATOR")
					$PName = ChartServices::GetLevel3ParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
				else
					//$PName = ChartServices::GetLevel3ParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
					$PName = ChartServices::GetLevel3ParentPerson($_SESSION["PersonID"], $ret[$k]->RelatedOrganzationChartID)->PersonName;
				// در صورتیکه پدری در چارت سازمانی مربوطه برای ایجاد کننده وجود نداشته باشد و مرحله بعدی برای پدر باشد اصلا نباید اجازه بدهد این مرحله انتخاب شود بنابراین عنوان مرحله را خالی می کند تا در زمان نمایش عناوین خالی نشان داده نشوند
				if($PName!="-")
					$ret[$k]->StepTitle .= " (".$PName.")";
				else
					$ret[$k]->StepTitle = "";
			}
			$k++;
		}
		$query = "select * from FormsFlowStepRelations LEFT JOIN FormsFlowSteps on (FormsFlowStepID=NextStepID) 
					where FormsFlowStepRelations.NextStepID='".$StepID."'";
		$res = $mysql->Execute($query);
		// در نظر گرفتن مراحل قبل از مرحله فعلی
		while($rec=$res->FetchRow())
		{
			if(FormUtils::IsStepInRecordPath($rec["FormFlowStepID"], $RelatedRecordID))
			{
				$ret[$k] = new be_FormsFlowSteps();
				// بعدا بهینه سازی شود یعنی به جای بار شدن دوباره از دیتابیس در تک تک فیلدها قرار گیرد
				$ret[$k]->LoadDataFromDatabase($rec["FormFlowStepID"]);
				$ret[$k]->PreviousStep = 1;
			
				// در صورتیکه بر اساس ایجاد کننده در چارت سازمانی بررسی انجام می شد نام فرد مربوط به مرحله را هم در جلوی آن اضافه می کند زیرا برای ارسال کننده باید ارسال کننده در همان زمان قبل مورد بررسی قرار گیرد
				if($ret[$k]->AccessRangeRelatedPersonType=="CREATOR")
				{
					if($ret[$k]->UserAccessRange=="BELOW_IN_CHART_LEVEL1")
					{
						$PName = ChartServices::GetFirstLevelParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
						if($PName!="-")
							$ret[$k]->StepTitle .= " (".$PName.")";
						else
							$ret[$k]->StepTitle = "";
					}
					else if($ret[$k]->UserAccessRange=="BELOW_IN_CHART_LEVEL2")
					{
						$PName = ChartServices::GetLevel2ParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
						if($PName!="-")
							$ret[$k]->StepTitle .= " (".$PName.")";
						else
							$ret[$k]->StepTitle = "";
						
					}
					else if($ret[$k]->UserAccessRange=="BELOW_IN_CHART_LEVEL3")
					{
						$PName = ChartServices::GetLevel3ParentPerson($CreatorID, $ret[$k]->RelatedOrganzationChartID)->PersonName;
						if($PName!="-")
							$ret[$k]->StepTitle .= " (".$PName.")";
						else
							$ret[$k]->StepTitle = "";
					}
				}
				$k++;
			}
		}
		return $ret;
	}
}
?>
