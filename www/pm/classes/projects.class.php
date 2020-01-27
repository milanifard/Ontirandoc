<?php
/*
 تعریف کلاسها و متدهای مربوط به : پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-15
*/

/*
کلاس پایه: پروژه
*/
class be_projects
{
	public $ProjectID;		//
	public $title;		//عنوان
	public $description;		//شرح
	public $StartTime;		//شروع
	public $StartTime_Shamsi;		/* مقدار شمسی معادل با شروع */
	public $EndTime;		//پایان
	public $EndTime_Shamsi;		/* مقدار شمسی معادل با پایان */
	public $SysCode;		//سیستم مربوطه
	public $SysCode_Desc;		/* شرح مربوط به سیستم مربوطه */
	public $ProjectPriority;		//اولویت
	public $ProjectPriority_Desc;		/* شرح مربوط به اولویت */
	public $ProjectStatus;		//وضعیت
	public $ProjectStatus_Desc;		/* شرح مربوط به وضعیت */
	public $ouid; // کد واحد سازمانی مربوطه
	public $ProjectGroupID; // کد گروه پروژه
	public $ouid_Desc; // نام واحد سازمانی مربوطه -استخراج از روی کد
	public $ProjectGroupID_Desc; // نام گروه پروژه - استخراج از روی کد
	public $ProjectSize, $ProjectSizeDescription, $ProjectActualSize, $ProjectActualSizeDescription, $CivilActivityID, $PhysicalPercentage, $ProjectCode;
	public $CivilActivityID_Desc;
	public $IsCivilProject;
	
	public $CivilProjectTypeID; // نوع پروژه
	public $CivilProjectTypeID_Desc;
	public $supervision; // نظارت و تعهدات
	public $EducUsage, $CoEducUsage, $ResearchUsage, $WelfareUsage, $OfficialUsage, $DormitoryUsage, $YardUsage, $CultureUsage, $SportUsage, $OtherUsage; //کاربریها
	public $FloorsCount; // تعداد طبقات
	public $SkeletType; // نوع اسکلت
	public $RoofType; // جنس سقف
	public $ViewType; // جنس نما
	public $CoolerSystem; // نوع سیستم سرمایشی
	public $HeaterSystem; // نوع سیستم گرمایشی
	public $SewageSystem; // نوع سیستم دفع فاضلاب
	public $StartUsingDate; // تاریخ بهره برداری
	public $StartingDate; // تاریخ افتتاح
	public $StartUsingDate_Shamsi; 
	public $StartingDate_Shamsi; 
	public $Achievable;
	
	function be_projects() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select projects.* 
			, g2j(StartTime) as StartTime_Shamsi 
			, g2j(EndTime) as EndTime_Shamsi 
			, f5.description  as f5_description 
			, CASE projects.ProjectPriority 
				WHEN '2' THEN 'عادی' 
				WHEN '3' THEN 'پایین' 
				WHEN '1' THEN 'بالا' 
				WHEN '0' THEN 'بحرانی' 
				END as ProjectPriority_Desc 
			, CASE projects.ProjectStatus 
				WHEN 'NOT_STARTED' THEN 'شروع نشده' 
				WHEN 'DEVELOPING' THEN 'در دست اقدام' 
				WHEN 'MAINTENANCE' THEN 'در حال پشتیبانی' 
				WHEN 'FINISHED' THEN 'خاتمه یافته'
				WHEN 'FINISHED_BY_Remained' THEN 'خاتمه یافته با مانده'				
				WHEN 'SUSPENDED' THEN 'متوقف'
				WHEN 'FINISHED_BY_DEBT' THEN 'خاتمه یافته با مطالبات'
				WHEN 'CONTINUOUS' THEN 'مستمر'
				END as ProjectStatus_Desc
			, org_units.ptitle
			, ProjectGroups.ProjectGroupName
			
			from projectmanagement.projects 
			LEFT JOIN projectmanagement.systems  f5 on (f5.SysCode=projects.SysCode)  
			LEFT JOIN projectmanagement.org_units on (org_units.ouid=projects.ouid)
			LEFT JOIN projectmanagement.ProjectGroups using (ProjectGroupID) 
			where  projects.ProjectID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectID=$rec["ProjectID"];
			$this->ProjectCode=$rec["ProjectCode"];
			$this->title=$rec["title"];
			$this->description=$rec["description"];
			$this->StartTime=$rec["StartTime"];
			$this->StartTime_Shamsi=$rec["StartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->EndTime=$rec["EndTime"];
			$this->EndTime_Shamsi=$rec["EndTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->SysCode=$rec["SysCode"];
			$this->SysCode_Desc=$rec["f5_description"]; // محاسبه از روی جدول وابسته
			$this->ProjectPriority=$rec["ProjectPriority"];
			$this->ProjectPriority_Desc=$rec["ProjectPriority_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->ProjectStatus=$rec["ProjectStatus"];
			$this->ProjectStatus_Desc=$rec["ProjectStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->ouid=$rec["ouid"];
			$this->ProjectGroupID=$rec["ProjectGroupID"];
			$this->ouid_Desc=$rec["ptitle"];  // محاسبه بر اساس لیست ثابت
			$this->ProjectGroupID_Desc=$rec["ProjectGroupName"];  // محاسبه بر اساس لیست ثابت
			$this->ProjectSize=$rec["ProjectSize"];
			$this->ProjectSizeDescription=$rec["ProjectSizeDescription"];
			$this->ProjectActualSize=$rec["ProjectActualSize"];
			$this->ProjectActualSizeDescription=$rec["ProjectActualSizeDescription"];
			$this->PhysicalPercentage=$rec["PhysicalPercentage"];

		}
	}
}
/*
کلاس مدیریت پروژه
*/
class manage_projects
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectID) as TotalCount from projectmanagement.projects";
		if($WhereCondition!="")
		{
			$query .= " where ".$WhereCondition;
		}
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	static function GetLastID()
	{
		$mysql = pdodb::getInstance();
		$query = "select max(ProjectID) as MaxID from projectmanagement.projects";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $title: عنوان
	* @param $description: شرح
	* @param $StartTime: شروع
	* @param $EndTime: پایان
	* @param $SysCode: سیستم مربوطه
	* @param $ProjectPriority: اولویت
	* @param $ProjectStatus: وضعیت
	* @param $PC: شیء از نوع PermissionsContainer که مشخصات دسترسی کاربر را در بر دارد
	* @return کد داده اضافه شده	*/
	static function Add($ouid, $ProjectGroupID, $title, $description, $StartTime, $EndTime, $SysCode, $ProjectPriority, $ProjectStatus, $Achievable, $PC)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.projects (";
		if($PC->GetPermission("ouid")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ouid";
			$k++; 
		}
		if($PC->GetPermission("ProjectGroupID")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ProjectGroupID";
			$k++; 
		}
		if($PC->GetPermission("title")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "title";
			$k++; 
		}
		if($PC->GetPermission("description")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "description";
			$k++; 
		}
		if($PC->GetPermission("StartTime")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "StartTime";
			$k++; 
		}
		if($PC->GetPermission("EndTime")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "EndTime";
			$k++; 
		}
		if($PC->GetPermission("SysCode")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "SysCode";
			$k++; 
		}
		if($PC->GetPermission("ProjectPriority")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ProjectPriority";
			$k++; 
		}
		if($PC->GetPermission("ProjectStatus")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ProjectStatus";
			$k++; 
		}
		if($PC->GetPermission("Achievable")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "Achievable";
			$k++; 
		}		
		$query .= ") values (";
		$k=0;
		if($PC->GetPermission("ouid")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
		if($PC->GetPermission("ProjectGroupID")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
		if($PC->GetPermission("title")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
		
		if($PC->GetPermission("description")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
		if($PC->GetPermission("StartTime")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
		if($PC->GetPermission("EndTime")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
		if($PC->GetPermission("SysCode")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
		if($PC->GetPermission("ProjectPriority")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
		if($PC->GetPermission("ProjectStatus")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
		if($PC->GetPermission("Achievable")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "?";
			$k++; 
		}
	
		$query .= ")";
		//echo $query;
		$ValueListArray = array();
		if($PC->GetPermission("ouid")=="WRITE")
			array_push($ValueListArray, $ouid); 
		if($PC->GetPermission("ProjectGroupID")=="WRITE")
			array_push($ValueListArray, $ProjectGroupID); 
		if($PC->GetPermission("title")=="WRITE")
			array_push($ValueListArray, $title); 
		if($PC->GetPermission("description")=="WRITE")
			array_push($ValueListArray, $description); 
		if($PC->GetPermission("StartTime")=="WRITE")
			array_push($ValueListArray, $StartTime); 
		if($PC->GetPermission("EndTime")=="WRITE")
			array_push($ValueListArray, $EndTime); 
		if($PC->GetPermission("SysCode")=="WRITE")
			array_push($ValueListArray, $SysCode); 
		if($PC->GetPermission("ProjectPriority")=="WRITE")
			array_push($ValueListArray, $ProjectPriority); 
		if($PC->GetPermission("ProjectStatus")=="WRITE")
			array_push($ValueListArray, $ProjectStatus); 		
		if($PC->GetPermission("Achievable")=="WRITE")
			array_push($ValueListArray, $Achievable);  
			
			
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_projects::GetLastID();
		//$mysql->audit("ثبت داده جدید در پروژه با کد ".$LastID);
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($LastID, "", "MAIN_PROJECT", $LastID, "ADD");

		// در زمان ایجاد پروژه ایجاد کننده به صورت پیش فرض به اعضای پروژه اضافه می شود و به او دسترسی کامل هم داده می شود
		require_once("ProjectMembers.class.php");
		$SelectedPersonID = $_SESSION["PersonID"];
		manage_ProjectMembers::Add($LastID, $SelectedPersonID, "MANAGER", "100");
		security_projects::SaveFieldPermission($LastID, 'ouid', $SelectedPersonID, "WRITE");
		security_projects::SaveFieldPermission($LastID, 'ProjectGroupID', $SelectedPersonID, "WRITE");
		security_projects::SaveFieldPermission($LastID, 'title', $SelectedPersonID, "WRITE");
		security_projects::SaveFieldPermission($LastID, 'description', $SelectedPersonID, "WRITE");
		security_projects::SaveFieldPermission($LastID, 'StartTime', $SelectedPersonID, "WRITE");
		security_projects::SaveFieldPermission($LastID, 'EndTime', $SelectedPersonID, "WRITE");
		security_projects::SaveFieldPermission($LastID, 'SysCode', $SelectedPersonID, "WRITE");
		security_projects::SaveFieldPermission($LastID, 'ProjectPriority', $SelectedPersonID, "WRITE");
		security_projects::SaveFieldPermission($LastID, 'ProjectStatus', $SelectedPersonID, "WRITE");
		security_projects::SaveFieldPermission($LastID, 'Achievable', $SelectedPersonID, "WRITE");
		
		
		security_projects::SaveDetailTablePermission($LastID, 'ProjectDocumentTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
		security_projects::SaveDetailTablePermission($LastID, 'ProjectDocuments', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
		security_projects::SaveDetailTablePermission($LastID, 'ProjectMembers', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
		security_projects::SaveDetailTablePermission($LastID, 'ProjectMilestones', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
		security_projects::SaveDetailTablePermission($LastID, 'ProjectTaskActivityTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
		security_projects::SaveDetailTablePermission($LastID, 'ProjectTaskTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
		
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PC: شیء از نوع PermissionsContainer که مشخصات دسترسی کاربر را در بر دارد
	* @param $title: عنوان
	* @param $description: شرح
	* @param $StartTime: شروع
	* @param $EndTime: پایان
	* @param $SysCode: سیستم مربوطه
	* @param $ProjectPriority: اولویت
	* @param $ProjectStatus: وضعیت
	* @return 	*/
	static function Update($UpdateRecordID, $ouid, $ProjectGroupID, $title, $description, $StartTime, $EndTime, $SysCode, $ProjectPriority, $ProjectStatus, $Achievable, $PC)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.projects set ";
		if($PC->GetPermission("ouid")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ouid=? ";
			$k++; 
		}
		if($PC->GetPermission("ProjectGroupID")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ProjectGroupID=? ";
			$k++; 
		}
		if($PC->GetPermission("title")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "title=? ";
			$k++; 
		}
		if($PC->GetPermission("description")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "description=? ";
			$k++; 
		}
		if($PC->GetPermission("StartTime")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "StartTime=? ";
			$k++; 
		}
		if($PC->GetPermission("EndTime")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "EndTime=? ";
			$k++; 
		}
		if($PC->GetPermission("SysCode")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "SysCode=? ";
			$k++; 
		}
		if($PC->GetPermission("ProjectPriority")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ProjectPriority=? ";
			$k++; 
		}
		if($PC->GetPermission("ProjectStatus")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ProjectStatus=? ";
			$k++; 
		}
		
	    if($PC->GetPermission("Achievable")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "Achievable=? ";
			$k++; 
		}
		$query .= " where ProjectID=?";
		$ValueListArray = array();
		if($PC->GetPermission("ouid")=="WRITE")
		{
			array_push($ValueListArray, $ouid); 
		}
		if($PC->GetPermission("ProjectGroupID")=="WRITE")
		{
			array_push($ValueListArray, $ProjectGroupID); 
		}
		if($PC->GetPermission("title")=="WRITE")
		{
			array_push($ValueListArray, $title); 
		}
		if($PC->GetPermission("description")=="WRITE")
		{
			array_push($ValueListArray, $description); 
		}
		if($PC->GetPermission("StartTime")=="WRITE")
		{
			array_push($ValueListArray, $StartTime); 
		}
		if($PC->GetPermission("EndTime")=="WRITE")
		{
			array_push($ValueListArray, $EndTime); 
		}
		if($PC->GetPermission("SysCode")=="WRITE")
		{
			array_push($ValueListArray, $SysCode); 
		}
		if($PC->GetPermission("ProjectPriority")=="WRITE")
		{
			array_push($ValueListArray, $ProjectPriority); 
		}
		if($PC->GetPermission("ProjectStatus")=="WRITE")
		{
			array_push($ValueListArray, $ProjectStatus); 
		}
		if($PC->GetPermission("Achievable")=="WRITE")
		{
			array_push($ValueListArray, $Achievable); 
		}
				
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پروژه");
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($UpdateRecordID, "", "MAIN_PROJECT", $UpdateRecordID, "UPDATE");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.projects set DeleteFlag='YES' where ProjectID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($RemoveRecordID, "", "MAIN_PROJECT", $RemoveRecordID, "REMOVE");
	}
	static function GetList($FromRec, $NumberOfRec, $OrderByFieldName, $OrderType)
	{
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		if(strtoupper($OrderType)!="ASC" && strtoupper($OrderType)!="DESC")
			$OrderType = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select projects.ProjectID
				,projects.title
				,projects.description
				,projects.StartTime
				,projects.EndTime
				,projects.SysCode
				,projects.ProjectPriority
				,projects.ProjectStatus
				,projects.ouid
				,projects.ProjectGroupID
			, g2j(StartTime) as StartTime_Shamsi 
			, g2j(EndTime) as EndTime_Shamsi 
			, f5.description  as f5_description 
			, CASE projects.ProjectPriority 
				WHEN '2' THEN 'عادی' 
				WHEN '3' THEN 'پایین' 
				WHEN '1' THEN 'بالا' 
				WHEN '0' THEN 'بحرانی' 
				END as ProjectPriority_Desc 
			, CASE projects.ProjectStatus 
				WHEN 'NOT_STARTED' THEN 'شروع نشده' 
				WHEN 'DEVELOPING' THEN 'در دست اقدام' 
				WHEN 'MAINTENANCE' THEN 'در حال پشتیبانی' 
				WHEN 'FINISHED' THEN 'خاتمه یافته' 
				WHEN 'FINISHED_BY_Remained' THEN 'خاتمه یافته با مانده'
				WHEN 'SUSPENDED' THEN 'متوقف'
				 WHEN 'FINISHED_BY_DEBT' THEN 'خاتمه یافته با مطالبات'
				 WHEN 'CONTINUOUS' THEN 'مستمر'
				END as ProjectStatus_Desc 
			, org_units.ptitle
			, ProjectGroups.ProjectGroupName
				from projectmanagement.projects 
			LEFT JOIN projectmanagement.systems  f5 on (f5.SysCode=projects.SysCode)  
			LEFT JOIN projectmanagement.org_units on (org_units.ouid=projects.ouid)
			LEFT JOIN projectmanagement.ProjectGroups using (ProjectGroupID) 
			";
		
		$query .= " where DeleteFlag='NO' ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_projects();
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->StartTime=$rec["StartTime"];
			$ret[$k]->StartTime_Shamsi=$rec["StartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EndTime=$rec["EndTime"];
			$ret[$k]->EndTime_Shamsi=$rec["EndTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->SysCode=$rec["SysCode"];
			$ret[$k]->SysCode_Desc=$rec["f5_description"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectPriority=$rec["ProjectPriority"];
			$ret[$k]->ProjectPriority_Desc=$rec["ProjectPriority_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ProjectStatus=$rec["ProjectStatus"];
			$ret[$k]->ProjectStatus_Desc=$rec["ProjectStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ouid=$rec["ouid"];
			$ret[$k]->ProjectGroupID=$rec["ProjectGroupID"];
			$ret[$k]->ouid_Desc=$rec["ptitle"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ProjectGroupID_Desc=$rec["ProjectGroupName"];  // محاسبه بر اساس لیست ثابت
			$k++;
		}
		return $ret;
	}
	/**
	* @param $title: عنوان
	* @param $SysCode: سیستم مربوطه
	* @param $ProjectStatus: وضعیت
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($MemberPersonID, $ouid, $ProjectGroupID, $title, $SysCode, $ProjectStatus, $OtherConditions, $FromRec, $NumberOfRec, $OrderByFieldName="", $OrderType="")
	{
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=30;
		
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select projects.ProjectID
				,projects.title
				,projects.description
				,projects.StartTime
				,projects.EndTime
				,projects.SysCode
				,projects.ProjectPriority
				,projects.ProjectStatus
				,projects.ouid
				,projects.ProjectGroupID
			, g2j(StartTime) as StartTime_Shamsi 
			, g2j(EndTime) as EndTime_Shamsi 
			, f5.description  as f5_description 
			, CASE projects.ProjectPriority 
				WHEN '2' THEN 'عادی' 
				WHEN '3' THEN 'پایین' 
				WHEN '1' THEN 'بالا' 
				WHEN '0' THEN 'بحرانی' 
				END as ProjectPriority_Desc 
			, CASE projects.ProjectStatus 
				WHEN 'NOT_STARTED' THEN 'شروع نشده' 
				WHEN 'DEVELOPING' THEN 'در دست اقدام' 
				WHEN 'MAINTENANCE' THEN 'در حال پشتیبانی' 
				WHEN 'FINISHED' THEN 'خاتمه یافته' 
				WHEN 'FINISHED_BY_Remained' THEN 'خاتمه یافته با مانده'
				WHEN 'SUSPENDED' THEN 'متوقف' 
				 WHEN 'FINISHED_BY_DEBT' THEN 'خاتمه یافته با مطالبات'
				 WHEN 'CONTINUOUS' THEN 'مستمر'
				END as ProjectStatus_Desc 
			, org_units.ptitle
			, ProjectGroups.ProjectGroupName
				from projectmanagement.projects 
			LEFT JOIN projectmanagement.systems  f5 on (f5.SysCode=projects.SysCode)  
			LEFT JOIN projectmanagement.org_units on (org_units.ouid=projects.ouid)
			LEFT JOIN projectmanagement.ProjectGroups using (ProjectGroupID) 
			";
		$cond = " DeleteFlag='NO' 
					and (projects.ouid in (select PermittedUnitID from projectmanagement.UserProjectScopes where UserProjectScopes.UserID='".$_SESSION["UserID"]."') 
					or projects.ProjectID in (select ProjectID from projectmanagement.ProjectMembers where PersonID='".$_SESSION["PersonID"]."')) "; 
		if($ouid!="0" && $ouid!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.ouid=? ";
		}
		if($ProjectGroupID!="0" && $ProjectGroupID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.ProjectGroupID=? ";
		}
		if($title!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "projects.title like ? ";
		}
		if($SysCode!="0" && $SysCode!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.SysCode=? ";
		}
		if($ProjectStatus!="0" && $ProjectStatus!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.ProjectStatus=? ";
		}
		if($MemberPersonID!="0" && $MemberPersonID!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "projects.ProjectID in (select ProjectID from projectmanagement.ProjectMembers where PersonID=?) ";
		}
		
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		/*if($_SESSION["PersonID"]=="401366873")
			echo $query;*/
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($ouid!="0" && $ouid!="") 
			array_push($ValueListArray, $ouid); 
		if($ProjectGroupID!="0" && $ProjectGroupID!="") 
			array_push($ValueListArray, $ProjectGroupID); 
			
		if($title!="") 
			array_push($ValueListArray, "%".$title."%"); 
		if($SysCode!="0" && $SysCode!="") 
			array_push($ValueListArray, $SysCode); 
		if($ProjectStatus!="0" && $ProjectStatus!="") 
			array_push($ValueListArray, $ProjectStatus);
		if($MemberPersonID!="0" && $MemberPersonID!="")
		 	array_push($ValueListArray, $MemberPersonID);
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_projects();
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->StartTime=$rec["StartTime"];
			$ret[$k]->StartTime_Shamsi=$rec["StartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EndTime=$rec["EndTime"];
			$ret[$k]->EndTime_Shamsi=$rec["EndTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->SysCode=$rec["SysCode"];
			$ret[$k]->SysCode_Desc=$rec["f5_description"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectPriority=$rec["ProjectPriority"];
			$ret[$k]->ProjectPriority_Desc=$rec["ProjectPriority_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ProjectStatus=$rec["ProjectStatus"];
			$ret[$k]->ProjectStatus_Desc=$rec["ProjectStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ouid=$rec["ouid"];
			$ret[$k]->ProjectGroupID=$rec["ProjectGroupID"];
			$ret[$k]->ouid_Desc=$rec["ptitle"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ProjectGroupID_Desc=$rec["ProjectGroupName"];  // محاسبه بر اساس لیست ثابت
			
			$k++;
		}
		return $ret;
	}
	
	// جستجو فقط در بین پروژه های عمرانی
	static function SearchCivilProjects($ProjectCode, $title, $ProjectStatus, $OtherConditions, $FromRec, $NumberOfRec, $OrderByFieldName="", $OrderType="")
	{
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=30;
		
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select projects.ProjectID
				,projects.title
				,projects.description
				,projects.StartTime
				,projects.EndTime
				,projects.SysCode
				,projects.ProjectPriority
				,projects.ProjectStatus
				,projects.ouid
				,projects.ProjectGroupID
			, g2j(StartTime) as StartTime_Shamsi 
			, g2j(EndTime) as EndTime_Shamsi 
			, f5.description  as f5_description 
			, CASE projects.ProjectPriority 
				WHEN '2' THEN 'عادی' 
				WHEN '3' THEN 'پایین' 
				WHEN '1' THEN 'بالا' 
				WHEN '0' THEN 'بحرانی' 
				END as ProjectPriority_Desc 
			, CASE projects.ProjectStatus 
				WHEN 'NOT_STARTED' THEN 'شروع نشده' 
				WHEN 'DEVELOPING' THEN 'در دست اقدام' 
				WHEN 'MAINTENANCE' THEN 'در حال پشتیبانی' 
				WHEN 'FINISHED' THEN 'خاتمه یافته'
				WHEN 'FINISHED_BY_Remained' THEN 'خاتمه یافته با مانده'
				WHEN 'SUSPENDED' THEN 'متوقف'
				 WHEN 'FINISHED_BY_DEBT' THEN 'خاتمه یافته با مطالبات'
				 WHEN 'CONTINUOUS' THEN 'مستمر'
				END as ProjectStatus_Desc 
			, org_units.ptitle
			, ProjectGroups.ProjectGroupName
			, CivilProjectsExtraInfo.ProjectSize
			, CivilProjectsExtraInfo.ProjectSizeDescription
			, CivilProjectsExtraInfo.ProjectActualSize
			, CivilProjectsExtraInfo.ProjectActualSizeDescription
			, CivilProjectsExtraInfo.CivilActivityID
			, CivilProjectsExtraInfo.PhysicalPercentage
			, CivilActivities.description as CivilActivityID_Desc
			, CivilProjectsExtraInfo.ProjectCode
			,EducUsage, CoEducUsage, ResearchUsage, WelfareUsage, OfficialUsage, DormitoryUsage, YardUsage, CultureUsage, SportUsage, OtherUsage
				from projectmanagement.projects
			INNER JOIN projectmanagement.CivilProjectsExtraInfo using (ProjectID)
			LEFT JOIN projectmanagement.CivilActivities using (CivilActivityID)  
			LEFT JOIN projectmanagement.systems  f5 on (f5.SysCode=projects.SysCode)  
			LEFT JOIN projectmanagement.org_units on (org_units.ouid=projects.ouid)
			LEFT JOIN projectmanagement.ProjectGroups using (ProjectGroupID) 
			";
		$cond = " DeleteFlag='NO' 
					 ";
		// and projects.ProjectID in (select ProjectID from projectmanagement.ProjectMembers where PersonID='".$_SESSION["PersonID"]."') 
		if($title!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "projects.title like ? ";
		}
		if($ProjectStatus!="0" && $ProjectStatus!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.ProjectStatus=? ";
		}
		if($ProjectCode!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= " ProjectCode like ? ";
		}
		
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		//echo $query;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($title!="") 
			array_push($ValueListArray, "%".$title."%"); 
		if($ProjectStatus!="0" && $ProjectStatus!="") 
			array_push($ValueListArray, $ProjectStatus); 
		if($ProjectCode!="") 
			array_push($ValueListArray, "%".$ProjectCode."%"); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_projects();
			$ret[$k]->ProjectCode=$rec["ProjectCode"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->StartTime=$rec["StartTime"];
			$ret[$k]->StartTime_Shamsi=$rec["StartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EndTime=$rec["EndTime"];
			$ret[$k]->EndTime_Shamsi=$rec["EndTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->SysCode=$rec["SysCode"];
			$ret[$k]->SysCode_Desc=$rec["f5_description"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectPriority=$rec["ProjectPriority"];
			$ret[$k]->ProjectPriority_Desc=$rec["ProjectPriority_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ProjectStatus=$rec["ProjectStatus"];
			$ret[$k]->ProjectStatus_Desc=$rec["ProjectStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ouid=$rec["ouid"];
			$ret[$k]->ProjectGroupID=$rec["ProjectGroupID"];
			$ret[$k]->ouid_Desc=$rec["ptitle"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ProjectGroupID_Desc=$rec["ProjectGroupName"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ProjectSize=$rec["ProjectSize"];
			$ret[$k]->ProjectSizeDescription=$rec["ProjectSizeDescription"];
			$ret[$k]->ProjectActualSize=$rec["ProjectActualSize"];
			$ret[$k]->ProjectActualSizeDescription=$rec["ProjectActualSizeDescription"];
			$ret[$k]->CivilActivityID=$rec["CivilActivityID"];
			$ret[$k]->PhysicalPercentage=$rec["PhysicalPercentage"];
			$ret[$k]->CivilActivityID_Desc=$rec["CivilActivityID_Desc"];

			$ret[$k]->EducUsage = $rec["EducUsage"];
			$ret[$k]->CoEducUsage = $rec["CoEducUsage"];
			$ret[$k]->ResearchUsage = $rec["ResearchUsage"];
			$ret[$k]->WelfareUsage = $rec["WelfareUsage"];
			$ret[$k]->OfficialUsage = $rec["OfficialUsage"];
			$ret[$k]->DormitoryUsage = $rec["DormitoryUsage"];
			$ret[$k]->YardUsage = $rec["YardUsage"];
			$ret[$k]->CultureUsage = $rec["CultureUsage"];
			$ret[$k]->SportUsage = $rec["SportUsage"];
			$ret[$k]->OtherUsage = $rec["OtherUsage"];
			
			$k++;
		}
		return $ret;
	}

	static function GetProjectsInSearchResult($MemberPersonID, $ouid, $ProjectGroupID, $title, $SysCode, $ProjectStatus)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(*) as TotalCount 
				from projectmanagement.projects 
			LEFT JOIN projectmanagement.systems  f5 on (f5.SysCode=projects.SysCode)  
			LEFT JOIN projectmanagement.org_units on (org_units.ouid=projects.ouid)
			LEFT JOIN projectmanagement.ProjectGroups using (ProjectGroupID) 
			";
		$cond = " DeleteFlag='NO' 
					and (projects.ouid in (select PermittedUnitID from projectmanagement.UserProjectScopes where UserProjectScopes.UserID='".$_SESSION["UserID"]."') 
					or projects.ProjectID in (select ProjectID from projectmanagement.ProjectMembers where PersonID='".$_SESSION["PersonID"]."')) "; 
		if($ouid!="0" && $ouid!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.ouid=? ";
		}
		if($ProjectGroupID!="0" && $ProjectGroupID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.ProjectGroupID=? ";
		}
		if($title!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "projects.title like ? ";
		}
		if($SysCode!="0" && $SysCode!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.SysCode=? ";
		}
		if($ProjectStatus!="0" && $ProjectStatus!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.ProjectStatus=? ";
		}
		if($MemberPersonID!="0" && $MemberPersonID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.ProjectID in (select ProjectID from projectmanagement.ProjectMembers where PersonID=?) ";
		}
		if($cond!="")
			$query .= " where ";
		$query .= $cond;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($ouid!="0" && $ouid!="") 
			array_push($ValueListArray, $ouid); 
		if($ProjectGroupID!="0" && $ProjectGroupID!="") 
			array_push($ValueListArray, $ProjectGroupID); 
			
		if($title!="") 
			array_push($ValueListArray, "%".$title."%"); 
		if($SysCode!="0" && $SysCode!="") 
			array_push($ValueListArray, $SysCode); 
		if($ProjectStatus!="0" && $ProjectStatus!="") 
			array_push($ValueListArray, $ProjectStatus); 
		if($MemberPersonID!="0" && $MemberPersonID!="")
		 	array_push($ValueListArray, $MemberPersonID);
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}

	static function GetCivilProjectsInSearchResult($ProjectCode, $title, $ProjectStatus)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(projects.ProjectID) as TotalCount 
					from projectmanagement.projects
					INNER JOIN projectmanagement.CivilProjectsExtraInfo using (ProjectID) 
					 ";
		$cond = " DeleteFlag='NO' "; 
					//and projects.ProjectID in (select ProjectID from projectmanagement.ProjectMembers where PersonID='".$_SESSION["PersonID"]."') "; 
		
		if($title!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "projects.title like ? ";
		}
		if($ProjectStatus!="0" && $ProjectStatus!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "projects.ProjectStatus=? ";
		}
		if($ProjectCode!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= " ProjectCode like ? ";
		}
		
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($title!="") 
			array_push($ValueListArray, "%".$title."%"); 
		if($ProjectStatus!="0" && $ProjectStatus!="") 
			array_push($ValueListArray, $ProjectStatus); 
		if($ProjectCode!="") 
			array_push($ValueListArray, "%".$ProjectCode."%"); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	
	function ShowSummary($RecID)
	{
		$ret = "<br>";
		$ret .= "<div class='row'>";
		$ret .= "<div class='col-1'></div>";
		$ret .= "<div class='col-10'>";
		$ret .= "<table class='table table-sm table-borderless' align=\"center\" border=\"1\" cellspacing=\"0\">";
		$ret .= "<tr>";
		$ret .= "<td>";
		$ret .= "<table width=\"100%\" border=\"0\">";
		$obj = new be_projects();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>".C_TITLE.": </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->title, ENT_QUOTES, 'UTF-8');
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</table>";
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</table>";
		$ret .= "</div>";
		$ret .= "<div class='col-1'></div>";
		$ret .= "</div>";
		return $ret;
	}
	
	function ShowTabs($RecID, $CurrentPageName)
	{
		$obj = new be_projects();
		$obj->LoadDataFromDatabase($RecID);
		if($obj->IsCivilProject)
		{
			$ret = "<table align=\"center\" width=\"90%\" border=\"1\" cellspacing=\"0\">";
	 		$ret .= "<tr>";
			$ret .= "<td width=\"11%\" ";
			if($CurrentPageName=="Newprojects")
				$ret .= "bgcolor=\"#cccccc\" ";
			$ret .= "><a href='NewCivilProjects.php?UpdateID=".$RecID."'>".C_SESSION_INFO."</a></td>";
			$ret .= "<td width=\"11%\" ";
			if($CurrentPageName=="ManageProjectAgreements")
				$ret .= "bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectAgreements.php?ProjectID=".$RecID."'>".C_CONTRACTS ."</a></td>";
			$ret .= "<td width=\"11%\" ";
			if($CurrentPageName=="ManageProjectProgressTable")
				$ret .= "bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectProgressTable.php?ProjectID=".$RecID."'>".C_PROJECT_PROGRESS."</a></td>";

			$ret .= "<td width=\"11%\" ";
			if($CurrentPageName=="ManageProjectApprovedCredits")
				$ret .= "bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectApprovedCredits.php?ProjectID=".$RecID."'>".C_APPROVAED_CREDIT."</a></td>";

			$ret .= "<td width=\"11%\" ";
			if($CurrentPageName=="ManageProjectApprovedCreditsFromOtherResource")
				$ret .= "bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectApprovedCreditsFromOtherResource.php?ProjectID=".$RecID."'>".C_OTHER_RESOURCES."</a></td>";
						
			$ret .= "<td width=\"11%\" ";
			if($CurrentPageName=="ManageProjectDocuments")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectDocuments.php?ProjectID=".$RecID."'>".C_DOCUMENTS."</a></td>";
			$ret .= "<td width=\"11%\" ";
			if($CurrentPageName=="ManageProjectMilestones")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectMilestones.php?ProjectID=".$RecID."'>".C_IMPORTANT_DATES."</a></td>";
			$ret .= "<td width=\"11%\" ";
			if($CurrentPageName=="ManageProjectDocumentTypes")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectDocumentTypes.php?ProjectID=".$RecID."'>".C_DOCUMENT_TYPES."</a></td>";
			$ret .= "<td width=\"11%\" ";
			if($CurrentPageName=="ManageProjectHistory")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectHistory.php?ProjectID=".$RecID."'>".C_HISTORY."</a></td>";
			
			$ret .= "</table>";
		}
		else
		{
			$ret = "<div class='row'>";
			$ret .= "<div class='col-1'></div>";
			$ret .= "<div class='col-10'>";
			$ret .= "<table class='table table-sm' border=\"1\" cellspacing=\"0\">";
	 		$ret .= "<tr class='table-info'>";
			$ret .= "<td class='text-center' width=\"8%\" ";
			if($CurrentPageName=="Newprojects")
				$ret .= "bgcolor=\"#cccccc\" ";
			$ret .= "><a href='Newprojects.php?UpdateID=".$RecID."'>".C_SESSION_INFO."</a></td>";
			$ret .= "<td class='text-center' width=\"8%\" ";
			if($CurrentPageName=="ManageProjectMembers")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectMembers.php?ProjectID=".$RecID."'>". C_SESSION_MEMBERS. "</a></td>";
			/*$ret .= "<td width=\"8%\" ";
			if($CurrentPageName=="ManageProjectExternalMembers")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectExternalMembers.php?ProjectID=".$RecID."'>اعضای خارجی</a></td>";
			$ret .= "<td width=\"8%\" ";
			if($CurrentPageName=="ManageProjectResponsibles")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectResponsibles.php?ProjectID=".$RecID."'>پاسخگویان</a></td>";
			*/
			$ret .= "<td width=\"8%\" ";
			if($CurrentPageName=="ManageProjectDocuments")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectDocuments.php?ProjectID=".$RecID."'>".C_DOCUMENTS."</a></td>";
			$ret .= "<td class='text-center' width=\"8%\" ";
			if($CurrentPageName=="ManageProjectMilestones")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectMilestones.php?ProjectID=".$RecID."'>".C_IMPORTANT_DATES."</a></td>";
			$ret .= "<td class='text-center' width=\"8%\" ";
			if($CurrentPageName=="ManageProjectDocumentTypes")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectDocumentTypes.php?ProjectID=".$RecID."'>".C_DOCUMENT_TYPES."</a></td>";
			$ret .= "<td class='text-center' width=\"8%\" ";
			if($CurrentPageName=="ManageProjectTaskActivityTypes")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectTaskActivityTypes.php?ProjectID=".$RecID."'>".C_ACTION_TYPES."</a></td>";
			$ret .= "<td class='text-center' width=\"8%\" ";
			if($CurrentPageName=="ManageProjectTaskTypes")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectTaskTypes.php?ProjectID=".$RecID."'>".C_TASK_TYPES."</a></td>";
			$ret .= "<td class='text-center' width=\"8%\" ";
			if($CurrentPageName=="ManageProjectTaskGroups")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectTaskGroups.php?ProjectID=".$RecID."'>".C_GROUP_OF_TASKS."</a></td>";
			$ret .= "<td class='text-center' width=\"8%\" ";
			if($CurrentPageName=="ManageProjectHistory")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ManageProjectHistory.php?ProjectID=".$RecID."'>".C_HISTORY."</a></td>";
			$ret .= "<td class='text-center' width=\"8%\" ";
			if($CurrentPageName=="ShowProjectActivities")
	 			$ret .= " bgcolor=\"#cccccc\" ";
			$ret .= "><a href='ShowProjectActivities.php?ProjectID=".$RecID."'>".C_ACTIVITIES."</a></td>";
		
			$ret .= "</table>";
			$ret .= "</div>";
			$ret .= "<div class='col-1'></div>";
			$ret .= "</div>";
		}
		return $ret;
	}

	// لیست پروژه هایی که شخص عضو آنهاست به صورت آپشنهای یک سلکت باکس بر می گرداند
	function GetUserProjectsOptions($PersonID, $UserID='')
	{
		$ret = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$query = "select distinct projects.ProjectID
				, projects.title
				from projectmanagement.projects  
				where DeleteFlag='NO' and 
				(projects.ouid in (select PermittedUnitID from projectmanagement.UserProjectScopes where UserProjectScopes.UserID=?) 
					or projects.ProjectID in (select ProjectID from projectmanagement.ProjectMembers where PersonID=?))				
					or Achievable='YES'
				order by replace(replace(trim(title), 'آ', char(1400)), 'ا', char(1401))"; // replace(replace(trim ... => to fix sorting disturbances.
		$mysql->Prepare($query);
		//if($_SESSION["User"]->PersonID != 401371457)
		$res = $mysql->ExecuteStatement(array($UserID, $PersonID));
		//else
		//$res = $mysql->ExecuteStatement(array('madadian', 401365865));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["ProjectID"]."'>".$rec["title"];
		}
		return $ret;
	}
	
	// لیست پروژه هایی که یک شخص عضو آنهاست بر می گرداند
	function GetUserProjects($PersonID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select projects.ProjectID
				,projects.title
				,projects.description
				,projects.StartTime
				,projects.EndTime
				,projects.SysCode
				,projects.ProjectPriority
				,projects.ProjectStatus
				,projects.ouid
				,projects.ProjectGroupID
			, g2j(StartTime) as StartTime_Shamsi 
			, g2j(EndTime) as EndTime_Shamsi 
			, f5.description  as f5_description 
			, CASE projects.ProjectPriority 
				WHEN '2' THEN 'عادی' 
				WHEN '3' THEN 'پایین' 
				WHEN '1' THEN 'بالا' 
				WHEN '0' THEN 'بحرانی' 
				END as ProjectPriority_Desc 
			, CASE projects.ProjectStatus 
				WHEN 'NOT_STARTED' THEN 'شروع نشده' 
				WHEN 'DEVELOPING' THEN 'در دست اقدام' 
				WHEN 'MAINTENANCE' THEN 'در حال پشتیبانی' 
				WHEN 'FINISHED' THEN 'خاتمه یافته'
				WHEN 'FINISHED_BY_Remained' THEN 'خاتمه یافته با مانده'				
				WHEN 'SUSPENDED' THEN 'متوقف'
				 WHEN 'FINISHED_BY_DEBT' THEN 'خاتمه یافته با مطالبات'
				 WHEN 'CONTINUOUS' THEN 'مستمر'
				END as ProjectStatus_Desc 
			, org_units.ptitle
			, ProjectGroups.ProjectGroupName
			from projectmanagement.projects 
			LEFT JOIN projectmanagement.systems  f5 on (f5.SysCode=projects.SysCode)  
			JOIN projectmanagement.ProjectMembers using (ProjectID) 
			LEFT JOIN projectmanagement.org_units on (org_units.ouid=projects.ouid)
			LEFT JOIN projectmanagement.ProjectGroups using (ProjectGroupID) 
		where DeleteFlag='NO' and ProjectMembers.PersonID=? ";
		$query .= " order by title";
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_projects();
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->StartTime=$rec["StartTime"];
			$ret[$k]->StartTime_Shamsi=$rec["StartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EndTime=$rec["EndTime"];
			$ret[$k]->EndTime_Shamsi=$rec["EndTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->SysCode=$rec["SysCode"];
			$ret[$k]->SysCode_Desc=$rec["f5_description"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectPriority=$rec["ProjectPriority"];
			$ret[$k]->ProjectPriority_Desc=$rec["ProjectPriority_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ProjectStatus=$rec["ProjectStatus"];
			$ret[$k]->ProjectStatus_Desc=$rec["ProjectStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ouid=$rec["ouid"];
			$ret[$k]->ProjectGroupID=$rec["ProjectGroupID"];
			$ret[$k]->ouid_Desc=$rec["ptitle"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ProjectGroupID_Desc=$rec["ProjectGroupName"];  // محاسبه بر اساس لیست ثابت
			$k++;
		}
		return $ret;
	}

	static function AddCivilExtraInfo($ProjectID, $ProjectSize, $ProjectSizeDescription, $ProjectActualSize, 
										$ProjectActualSizeDescription, $CivilActivityID, $PhysicalPercentage, $ProjectCode
										, $CivilProjectTypeID
										, $supervision
										, $EducUsage, $CoEducUsage, $ResearchUsage, $WelfareUsage, $OfficialUsage, $DormitoryUsage, $YardUsage, $CultureUsage, $SportUsage, $OtherUsage
										, $FloorsCount, $SkeletType, $RoofType, $ViewType, $CoolerSystem, $HeaterSystem, $SewageSystem, $StartUsingDate, $StartingDate
										)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.CivilProjectsExtraInfo 
				(ProjectID, ProjectSize, ProjectSizeDescription, ProjectActualSize, ProjectActualSizeDescription, CivilActivityID, PhysicalPercentage, ProjectCode,
				CivilProjectTypeID, supervision, EducUsage, CoEducUsage, ResearchUsage, WelfareUsage, OfficialUsage, DormitoryUsage, YardUsage, CultureUsage, SportUsage, OtherUsage, FloorsCount,
				SkeletType, RoofType, ViewType, CoolerSystem, HeaterSystem, SewageSystem, StartUsingDate, StartingDate
				)
				values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
						?, ?, ?, ?, ?, 
						?, ?, ?, ?, ?, 
						?, ?, ?, ?, ?, ?)";
 
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID);
		array_push($ValueListArray, $ProjectSize);
		array_push($ValueListArray, $ProjectSizeDescription); 
		array_push($ValueListArray, $ProjectActualSize);
		array_push($ValueListArray, $ProjectActualSizeDescription);
		array_push($ValueListArray, $CivilActivityID);
		array_push($ValueListArray, $PhysicalPercentage);
		array_push($ValueListArray, $ProjectCode);

		array_push($ValueListArray, $CivilProjectTypeID);
		array_push($ValueListArray, $supervision);
		array_push($ValueListArray, $EducUsage);
		array_push($ValueListArray, $CoEducUsage);
		array_push($ValueListArray, $ResearchUsage);
		array_push($ValueListArray, $WelfareUsage);
		array_push($ValueListArray, $OfficialUsage);
		array_push($ValueListArray, $DormitoryUsage);
		array_push($ValueListArray, $YardUsage);
		array_push($ValueListArray, $CultureUsage);
		array_push($ValueListArray, $SportUsage);
		array_push($ValueListArray, $OtherUsage);
		array_push($ValueListArray, $FloorsCount);
		array_push($ValueListArray, $SkeletType);
		array_push($ValueListArray, $RoofType);
		array_push($ValueListArray, $ViewType);
		array_push($ValueListArray, $CoolerSystem);
		array_push($ValueListArray, $HeaterSystem);
		array_push($ValueListArray, $SewageSystem);
		array_push($ValueListArray, $StartUsingDate);
		array_push($ValueListArray, $StartingDate);
		
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
	}

	static function UpdateCivilExtraInfo($ProjectID, $ProjectSize, $ProjectSizeDescription, $ProjectActualSize, 
										$ProjectActualSizeDescription, $CivilActivityID, $PhysicalPercentage, $ProjectCode
										, $CivilProjectTypeID
										, $supervision
										, $EducUsage, $CoEducUsage, $ResearchUsage, $WelfareUsage, $OfficialUsage, $DormitoryUsage, $YardUsage, $CultureUsage, $SportUsage, $OtherUsage
										, $FloorsCount, $SkeletType, $RoofType, $ViewType, $CoolerSystem, $HeaterSystem, $SewageSystem, $StartUsingDate, $StartingDate
										)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.CivilProjectsExtraInfo 
				set ProjectSize=?, ProjectSizeDescription=?, ProjectActualSize=?, ProjectActualSizeDescription=?, CivilActivityID=?, PhysicalPercentage=?, ProjectCode=? 
			, CivilProjectTypeID=?
			, supervision=?
			, EducUsage=?, CoEducUsage=?, ResearchUsage=?, WelfareUsage=?, OfficialUsage=?, DormitoryUsage=?, YardUsage=?, CultureUsage=?, SportUsage=?, OtherUsage=?
			, FloorsCount=?, SkeletType=?, RoofType=?, ViewType=?, CoolerSystem=?, HeaterSystem=?, SewageSystem=?, StartUsingDate=?, StartingDate=?
				
				where ProjectID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectSize);
		array_push($ValueListArray, $ProjectSizeDescription); 
		array_push($ValueListArray, $ProjectActualSize);
		array_push($ValueListArray, $ProjectActualSizeDescription);
		array_push($ValueListArray, $CivilActivityID);
		array_push($ValueListArray, $PhysicalPercentage);
		array_push($ValueListArray, $ProjectCode);
		
		array_push($ValueListArray, $CivilProjectTypeID);
		array_push($ValueListArray, $supervision);
		array_push($ValueListArray, $EducUsage);
		array_push($ValueListArray, $CoEducUsage);
		array_push($ValueListArray, $ResearchUsage);
		array_push($ValueListArray, $WelfareUsage);
		array_push($ValueListArray, $OfficialUsage);
		array_push($ValueListArray, $DormitoryUsage);
		array_push($ValueListArray, $YardUsage);
		array_push($ValueListArray, $CultureUsage);
		array_push($ValueListArray, $SportUsage);
		array_push($ValueListArray, $OtherUsage);
		array_push($ValueListArray, $FloorsCount);
		array_push($ValueListArray, $SkeletType);
		array_push($ValueListArray, $RoofType);
		array_push($ValueListArray, $ViewType);
		array_push($ValueListArray, $CoolerSystem);
		array_push($ValueListArray, $HeaterSystem);
		array_push($ValueListArray, $SewageSystem);
		array_push($ValueListArray, $StartUsingDate);
		array_push($ValueListArray, $StartingDate);
		
		
		array_push($ValueListArray, $ProjectID);
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
	}
	
	// با گرفتن کد سیستم پروژه مربوط به آن را بر می گرداند
	function GetProjectID($SysCode)
	{
		$query = "select projects.ProjectID	from projectmanagement.projects where projects.SysCode=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($SysCode));
		if($rec=$res->fetch())
		{
			return $rec["ProjectID"];
		}
		return 0;
	}

	// با گرفتن کد سیستم پروژه مربوط به آن را بر می گرداند
	function GetProjectIDOfThisCode($ProjectCode)
	{
		$query = "select ProjectID	from projectmanagement.CivilProjectsExtraInfo where ProjectCode=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($ProjectCode));
		if($rec=$res->fetch())
		{
			return $rec["ProjectID"];
		}
		return 0;
	}
	
}

?>
