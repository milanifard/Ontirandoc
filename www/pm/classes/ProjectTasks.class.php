<?php
/*
 تعریف کلاسها و متدهای مربوط به : کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/

/*
کلاس پایه: کار
*/
class be_ProjectTasks
{
	public $ProjectTaskID;		//
	public $ProjectID;		//پروژه مربوطه
	public $ProjectID_Desc;		/* شرح مربوط به پروژه مربوطه */
	public $ProjectTaskTypeID;		//نوع کار
	public $ProjectTaskTypeID_Desc;		/* شرح مربوط به نوع کار */
	public $title;		//عنوان
	public $description;		//شرح
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	
	public $ControllerID;		//کنترل کننده
	public $ControllerID_FullName;		/* نام و نام خانوادگی مربوط به کنترل کننده */
	
	public $PeriodType;		//پریود انجام
	public $PeriodType_Desc;		/* شرح مربوط به پریود انجام */
	public $CountOfDone;		//تعداد دفعات انجام
	public $EstimatedStartTime;		//زمان تخمینی شروع
	public $EstimatedStartTime_Shamsi;		/* مقدار شمسی معادل با زمان تخمینی شروع */
	public $RealStartTime;		//زمان واقعی شروع	
	public $RealStartTime_Shamsi;		/* مقدار شمسی معادل با زمان واقعی شروع */
	public $EstimatedRequiredTimeDay;		//زمان مورد نیاز - روز
	public $EstimatedRequiredTimeHour;		//زمان مورد نیاز - ساعت
	public $EstimatedRequitedTimeMin;		//زمان مورد نیاز - دقیقه
	public $HasExpireTime;		//مهلت اقدام دارد؟
	public $HasExpireTime_Desc;		/* شرح مربوط به مهلت اقدام دارد؟ */
	public $ExpireTime;		//مهلت اقدام
	public $ExpireTime_Shamsi;		/* مقدار شمسی معادل با مهلت اقدام */
	public $TaskPeriority;		//اولویت
	public $TaskPeriority_Desc;		/* شرح مربوط به اولویت */
	public $TaskStatus;		//وضعیت
	public $TaskStatus_Desc;		/* شرح مربوط به وضعیت */
	public $ParentID;		//کار پدر
	public $ParentID_Desc;		/* شرح مربوط به کار بالاتر */
	public $DoneDate; // تاریخ پایان کار
	public $DoneDate_Shamsi; // تاریخ پایان کار به شمسی - محاسبه شده از روی تاریخ پایان کار
	public $CreateDate; // تاریخ ایجاد کار
	public $CreateDate_Shamsi; // تاریخ ایجاد کار به شمسی - محاسبه شده از روی تاریخ پایان کار
	public $CanRemoveByCaller; // مشخص می کند آیا این کار می تواند توسط فراخواننده تابع حذف شود
	public $UpdateReason;
	public $BugID;
	public $ProgramLevelID; // کد مرحله برنامه مربوطه
	public $ProgramLevelID_Desc; // عنوان برنامه و مرحله مربوطه
	public $TaskGroupID;
	public $TaskGroupName; 
	public $StartTime;
	public $EndTime;
	public $study;
	
	function be_ProjectTasks() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTasks.* 
			, p1.title  as p1_title 
			, p2.title  as p2_title 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName
			, concat(persons6.pfname, ' ', persons6.plname) as persons6_FullName  
			, CASE ProjectTasks.PeriodType 
				WHEN 'ONCE' THEN 'یکبار' 
				WHEN 'EVERYDAY' THEN 'روزانه' 
				WHEN 'EVERYWEEK' THEN 'هفتگی' 
				WHEN 'EVERYMONTH' THEN 'ماهانه' 
				END as PeriodType_Desc 
			, g2j(ProjectTasks.EstimatedStartTime) as EstimatedStartTime_Shamsi 
			, g2j(ProjectTasks.RealStartTime) as RealStartTime_Shamsi
			, CASE ProjectTasks.HasExpireTime 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasExpireTime_Desc 
			, g2j(ProjectTasks.ExpireTime) as ExpireTime_Shamsi 
			, concat(g2j(ProjectTasks.DoneDate), ' ', substr(ProjectTasks.DoneDate, 12,10)) as DoneDate_Shamsi
			, concat(g2j(ProjectTasks.CreateDate), ' ', substr(ProjectTasks.CreateDate, 12,10)) as CreateDate_Shamsi
			, CASE ProjectTasks.TaskStatus 
				WHEN 'NOT_START' THEN 'اقدام نشده' 
				WHEN 'PROGRESSING' THEN 'در دست اقدام' 
				WHEN 'DONE' THEN 'اقدام شده' 
				WHEN 'SUSPENDED' THEN 'معلق' 
				WHEN 'REPLYED' THEN 'پاسخ داده شده' 
				WHEN 'READY_FOR_TEST' THEN 'آماده برای کنترل'
				WHEN 'CONFWAIT' THEN 'منتظرتایید'
				WHEN 'EXECUTECONF' THEN 'تاییدجهت اجرا'
				WHEN 'NOCONF' THEN 'عدم تایید'				
				END as TaskStatus_Desc
			, concat(programs.title, ' - ', ProgramLevels.title) as LevelFullName
			, TaskGroupName 
			from projectmanagement.ProjectTasks
			LEFT JOIN projectmanagement.projects  p1 on (p1.ProjectID=ProjectTasks.ProjectID) 
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) 
			LEFT JOIN projectmanagement.ProjectTaskGroups  on (ProjectTaskGroups.ProjectTaskGroupID=ProjectTasks.TaskGroupID)
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID)
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=ProjectTasks.ControllerID)
			LEFT JOIN projectmanagement.ProgramLevels on (ProgramLevels.ProgramLevelID=ProjectTasks.ProgramLevelID) 
			LEFT JOIN projectmanagement.programs on (programs.ProgramID=ProgramLevels.ProgramID)  
			where  ProjectTasks.ProjectTaskID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
               // echo $query;
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->TaskGroupID=$rec["TaskGroupID"];
			$this->TaskGroupName=$rec["TaskGroupName"];
			$this->ProgramLevelID=$rec["ProgramLevelID"];
			$this->ProgramLevelID_Desc=$rec["LevelFullName"];
			$this->ProjectTaskID=$rec["ProjectTaskID"];
			$this->ProjectID=$rec["ProjectID"];
			$this->ProjectID_Desc=$rec["p1_title"]; // محاسبه از روی جدول وابسته
			$this->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$this->ProjectTaskTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$this->title=$rec["title"];
			$this->description=$rec["description"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$this->ControllerID=$rec["ControllerID"];
			$this->ControllerID_FullName=$rec["persons6_FullName"]; // محاسبه از روی جدول وابسته
			$this->PeriodType=$rec["PeriodType"];
			$this->PeriodType_Desc=$rec["PeriodType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->CountOfDone=$rec["CountOfDone"];
			$this->EstimatedStartTime=$rec["EstimatedStartTime"];
			$this->RealStartTime=$rec["RealStartTime"];
			$this->EstimatedStartTime_Shamsi=$rec["EstimatedStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->RealStartTime_Shamsi=$rec["RealStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->EstimatedRequiredTimeDay=$rec["EstimatedRequiredTimeDay"];
			$this->EstimatedRequiredTimeHour=$rec["EstimatedRequiredTimeHour"];
			$this->EstimatedRequitedTimeMin=$rec["EstimatedRequitedTimeMin"];
			$this->HasExpireTime=$rec["HasExpireTime"];
			$this->HasExpireTime_Desc=$rec["HasExpireTime_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->ExpireTime=$rec["ExpireTime"];
			$this->ExpireTime_Shamsi=$rec["ExpireTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->TaskPeriority=str_pad($rec["TaskPeriority"], 2, '0', STR_PAD_LEFT);
			$this->TaskStatus=$rec["TaskStatus"];
			$this->TaskStatus_Desc=$rec["TaskStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->ParentID=$rec["ParentID"];
			$this->DoneDate=$rec["DoneDate"];
			$this->DoneDate_Shamsi=($rec["DoneDate_Shamsi"] == 'date-error 00:00:00')? 'تمام نشده': $rec["DoneDate_Shamsi"];
			$this->CreateDate=$rec["CreateDate"];
			$this->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];
			$this->UpdateReason = $rec["UpdateReason"];
			//$this->BugID = $rec["SentBugsID"];
			$this->study = $rec["study"];
			//$this->ParentID_Desc=$rec["p16_title"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت کار
*/
class manage_ProjectTasks
{
	static function DoRefer($TaskID, $PersonID, $Executor, $Description, $Remove)
	{
		$mysql = pdodb::getInstance();

		/*
		// Handy Manipulations ...

		// insert into ProjectTaskAssignedUsers (ProjectTaskID, PersonID, AssignType) values (66005, 401371457, 'EXECUTOR');
		// delete from ProjectTaskAssignedUsers where ProjectTaskID = 66005 and PersonID = 201309;

		$mysql->Prepare("");
		$res = $mysql->ExecuteStatement([]);

		*/
		
		// Adding the new EXECUTOR ...
		$mysql->Prepare("INSERT INTO ProjectTaskAssignedUsers (ProjectTaskID, PersonID, AssignDescription, CreatorID, AssignType) SELECT ?, ?, 'ارجاع کار', ?, 'EXECUTOR' FROM dual WHERE NOT EXISTS (select ProjectTaskAssignedUserID from ProjectTaskAssignedUsers where ProjectTaskID = ? and PersonID = ?);");
		$mysql->ExecuteStatement([$TaskID, $Executor, $PersonID, $TaskID, $Executor]);

		// Writing the TaskRefer ...
		$mysql->Prepare("insert into ProjectTaskRefers (TaskID, FromPerson, ToPerson, Description, DateTime) values (?, ?, ?, ?, now());");
		$mysql->ExecuteStatement([$TaskID, $PersonID, $Executor, $Description]);

		// Deleting the old EXECUTOR if necessary ...
		if ($Remove == 'true')
		{
			$mysql->Prepare("delete from ProjectTaskAssignedUsers where ProjectTaskID = ? and PersonID = ?;");
			$mysql->ExecuteStatement([$TaskID, $PersonID]);
		}
	}

	static function GetTaskRefers($TaskID)
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("SELECT FromPerson, ToPerson, Description, DateTime, g2j(DateTime) as Date, substr(DateTime, 12,10) as Time FROM projectmanagement.ProjectTaskRefers where TaskID = ? order by DateTime;");
		$res = $mysql->ExecuteStatement([$TaskID]);
		return $res->fetchAll();
	}

	static function GetFullName($PersonID)
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("SELECT concat(PFName, ' ', PLName) as FullName FROM projectmanagement.persons where PersonID = ?;");
		$res = $mysql->ExecuteStatement([$PersonID]);
		return $res->fetch()["FullName"];
	}

	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectTaskID) as TotalCount from projectmanagement.ProjectTasks";
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
        static function GetCountTotal($WhereCondition="",$where="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectTaskID) as TotalCount from projectmanagement.ProjectTasks";
		if($WhereCondition!="")
		{
			$query .= " where ProjectID=".$WhereCondition." and CreatorID=".$where." and DeleteFlag='NO' ";                            }
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
		$query = "select max(ProjectTaskID) as MaxID from projectmanagement.ProjectTasks";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}

	// این تابع مخصوص بخش جستجوی کار است - Search
	static function SchResultCount($ProjectID, $ProjectTaskTypeID, $ProjectTaskID, $title, $description, $CreatorID, $TaskPeriority, $TaskStatus, $ExecutorID, $TaskComment, 
          $DocumentDescription, $ActivityDescription, $OtherConditions)
	{
                $CallerPersonID = $_SESSION["PersonID"]; 
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(distinct ProjectTasks.ProjectTaskID) as TotalCount 
			from 
			projectmanagement.ProjectTasks 
			LEFT JOIN projectmanagement.ProgramLevels on (ProgramLevels.ProgramLevelID=ProjectTasks.ProgramLevelID) 
			LEFT JOIN projectmanagement.programs on (programs.ProgramID=ProgramLevels.ProgramID)
			LEFT JOIN projectmanagement.ProjectTaskGroups  on (ProjectTaskGroupID=TaskGroupID)  
			";
		$query .= " JOIN
					 (select distinct ProjectTaskID
						from projectmanagement.ProjectTasks 
						LEFT JOIN projectmanagement.ProjectMembers on (ProjectMembers.ProjectID=ProjectTasks.ProjectID)
						LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)"; 
		if($TaskComment!="")
			$query .= " LEFT JOIN projectmanagement.ProjectTaskComments using (ProjectTaskID)";
		if($ActivityDescription!="")
			$query .= " LEFT JOIN projectmanagement.ProjectTaskActivities using (ProjectTaskID)";
		if($DocumentDescription!="")
			$query .= " LEFT JOIN projectmanagement.ProjectTaskDocuments using (ProjectTaskID)";
		$query .= "	where 
						ProjectTasks.DeleteFlag='NO' and 
						(" . ((in_array($CallerPersonID, security_ProjectTasks::$Exceptions))? "true" : "false") . " or (ProjectMembers.PersonID=? and ProjectMembers.AccessType<>'MEMBER') 
							or ProjectTasks.CreatorID=? or ProjectTaskAssignedUsers.PersonID=? or ProjectTaskID in (select distinct TaskID from projectmanagement.ProjectTaskRefers where FromPerson = ? or ToPerson = ?)) ";
		if($ProjectID!="0" && $ProjectID!="") 
			$query .= " and ProjectTasks.ProjectID=? ";
		if($ProjectTaskTypeID!="0" && $ProjectTaskTypeID!="") 
			$query .= " and ProjectTasks.ProjectTaskTypeID=? ";
                if($ProjectTaskID!="0" && $ProjectTaskID!="") 
			$query .= " and ProjectTasks.ProjectTaskID=? ";
		if($title!="") 
			$query .= " and ProjectTasks.title like ? ";
		if($description!="" /*&& $description!='0'*/) {
//print_r($description);die();
			$query .= " and ProjectTasks.description like ? ";}
		if($CreatorID!="0" && $CreatorID!="") 
			$query .= " and ProjectTasks.CreatorID=? ";
		if($TaskPeriority!="0" && $TaskPeriority!="") 
			$query .= " and ProjectTasks.TaskPeriority=? ";
		if($TaskStatus!="0" && $TaskStatus!="") 
			$query .= " and ProjectTasks.TaskStatus=? ";
		if($ExecutorID!="0" && $ExecutorID!="") 
			$query .= " and ProjectTaskAssignedUsers.PersonID=? ";
		if($TaskComment!="")
			$query .= " and CommentBody like ? "; 
		if($DocumentDescription!="")
			$query .= " and DocumentDescription like ? "; 
		if($ActivityDescription!="")
			$query .= " and ActivityDescription like ? ";
		$query .= $OtherConditions;

		$query .= " ) as SelectedTasks using (ProjectTaskID)";
		$query .= " LEFT JOIN projectmanagement.projects on (projects.ProjectID=ProjectTasks.ProjectID)";
		$query .= " LEFT JOIN projectmanagement.ProjectMembers on (ProjectMembers.ProjectID=ProjectTasks.ProjectID)";
		$query .= " LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)";
		$query .= "  
			LEFT JOIN projectmanagement.projects  p1 on (p1.ProjectID=ProjectTasks.ProjectID) 
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=ProjectTasks.ControllerID)
			  ";	

		
		/*if($_SESSION["PersonID"]=="401366873")
		{
                        echo $query;echo "<br>";
			print_r($ProjectID);echo "<br>";
			
		}*/

		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID); 

		if($ProjectTaskTypeID!="0" && $ProjectTaskTypeID!="") 
			array_push($ValueListArray, $ProjectTaskTypeID);  
                if($ProjectTaskID!="0" && $ProjectTaskID!="") 
			array_push($ValueListArray, $ProjectTaskID); 
		if($title!="") 
			array_push($ValueListArray, "%".$title."%"); 
		if($description!="" /*&& $description!='0'*/) 
			array_push($ValueListArray, "%".$description."%"); 
		if($CreatorID!="0" && $CreatorID!="") 
			array_push($ValueListArray, $CreatorID); 
		if($TaskPeriority!="0" && $TaskPeriority!="") 
			array_push($ValueListArray, $TaskPeriority); 
		if($TaskStatus!="0" && $TaskStatus!="") 
			array_push($ValueListArray, $TaskStatus);
		if($ExecutorID!="0" && $ExecutorID!="")
		 	array_push($ValueListArray, $ExecutorID);
		if($TaskComment!="")
			array_push($ValueListArray, "%".$TaskComment."%");
		if($DocumentDescription!="")
			array_push($ValueListArray, "%".$DocumentDescription."%");
		if($ActivityDescription!="")
			array_push($ValueListArray, "%".$ActivityDescription."%");
		

		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec = $res->fetch()) return $rec["TotalCount"];
                else return 0;
	}

	/**
	* @param $ProjectID: پروژه مربوطه
	* @param $ProjectTaskTypeID: نوع کار
	* @param $title: عنوان
	* @param $description: شرح
	* @param $PeriodType: پریود انجام
	* @param $CountOfDone: تعداد دفعات انجام
	* @param $EstimatedStartTime: زمان تخمینی شروع
	* @param $RealStartTime: زمان واقعی شروع
	* @param $EstimatedRequiredTimeDay: زمان مورد نیاز - روز
	* @param $EstimatedRequiredTimeHour: زمان مورد نیاز - ساعت
	* @param $EstimatedRequitedTimeMin: زمان مورد نیاز - دقیقه
	* @param $HasExpireTime: مهلت اقدام دارد؟
	* @param $ExpireTime: مهلت اقدام
	* @param $TaskPeriority: اولویت
	* @param $TaskStatus: وضعیت
	* @param $ParentID: کار بالاتر
	* @return کد داده اضافه شده	*/
	static function Add($ProjectID, $ProgramLevelID, $ProjectTaskTypeID, $title, $description, $PeriodType, $CountOfDone, $EstimatedStartTime, $RealStartTime, $EstimatedRequiredTimeDay, $EstimatedRequiredTimeHour, $EstimatedRequitedTimeMin, $HasExpireTime, $ExpireTime, $TaskPeriority, $TaskStatus, $ParentID, $TaskGroupID , $ControllerID, $StartTime='00:00' ,$EndTime='00:00',$study, $PC, $LetterNumber, $LetterType, $LetterDate)
	{
		if($EstimatedRequiredTimeDay=="")
			$EstimatedRequiredTimeDay = "0";
		if($EstimatedRequitedTimeMin=="")
			$EstimatedRequitedTimeMin = "0";
		if($EstimatedRequiredTimeHour=="")
			$EstimatedRequiredTimeHour = "0";

		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectTasks (CreatorID, CreateDate ";
		if($PC->GetPermission("ControllerID")=="WRITE")
		{
			$query .= ", ControllerID";
		}
		if($PC->GetPermission("ProgramLevelID")=="WRITE")
		{
			$query .= ", ProgramLevelID";
		}
		if($PC->GetPermission("ProjectID")=="WRITE")
		{
			$query .= ", ProjectID";
		}
		if($PC->GetPermission("ProjectTaskTypeID")=="WRITE")
		{
			$query .= ", ProjectTaskTypeID";
		}
		if($PC->GetPermission("title")=="WRITE")
		{
			$query .= ", title";
		}
		if($PC->GetPermission("description")=="WRITE")
		{
			$query .= ", description";
		}
		if($PC->GetPermission("PeriodType")=="WRITE")
		{
			$query .= ", PeriodType";
		}
		if($PC->GetPermission("CountOfDone")=="WRITE")
		{
			$query .= ", CountOfDone";
		}
		if($PC->GetPermission("EstimatedStartTime")=="WRITE")
		{
			if(SharedClass::IsDateFormat($EstimatedStartTime))
				$query .= ", EstimatedStartTime";
		}
		if($PC->GetPermission("RealStartTime")=="WRITE")
		{
			if(SharedClass::IsDateFormat($RealStartTime))
				$query .= ", RealStartTime";
		}
		if($PC->GetPermission("EstimatedRequiredTimeDay")=="WRITE")
		{
			$query .= ", EstimatedRequiredTimeDay";
		}
		if($PC->GetPermission("EstimatedRequiredTimeHour")=="WRITE")
		{
			$query .= ", EstimatedRequiredTimeHour";
 
		}
		if($PC->GetPermission("EstimatedRequitedTimeMin")=="WRITE")
		{
			$query .= ", EstimatedRequitedTimeMin";
		}
		if($PC->GetPermission("HasExpireTime")=="WRITE")
		{
			$query .= ", HasExpireTime";
			$k++; 
		}
		if($PC->GetPermission("ExpireTime")=="WRITE")
		{
			if(SharedClass::IsDateFormat($ExpireTime))
				$query .= ", ExpireTime";
		}
		if($PC->GetPermission("TaskPeriority")=="WRITE")
		{
			$query .= ", TaskPeriority";
		}
		if($PC->GetPermission("TaskStatus")=="WRITE")
		{
			$query .= ", TaskStatus";
			if($TaskStatus=="DONE" || $TaskStatus=="REPLYED")
			{
				$query .= ", DoneDate ";
			}
		}
		if($PC->GetPermission("ParentID")=="WRITE")
		{
			$query .= ", ParentID";
		}
		if($PC->GetPermission("TaskGroupID")=="WRITE")
		{
			$query .= ", TaskGroupID";
		}
		if($PC->GetPermission("StartTime")=="WRITE")
		{
			$query .= ", StartTime";
		}
		if($PC->GetPermission("EndTime")=="WRITE")
		{
			$query .= ", EndTime";
		}
                if($PC->GetPermission("study")=="WRITE")
		{
			$query .= ", study ";
		}

		$query .= ") values ('".$_SESSION["PersonID"]."', now() ";
		if($PC->GetPermission("ControllerID")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("ProgramLevelID")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("ProjectID")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("ProjectTaskTypeID")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("title")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("description")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("PeriodType")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("CountOfDone")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("EstimatedStartTime")=="WRITE")
		{
			if(SharedClass::IsDateFormat($EstimatedStartTime))
				$query .= ", ?";
		}
		if($PC->GetPermission("RealStartTime")=="WRITE")
		{
			if(SharedClass::IsDateFormat($RealStartTime))
				$query .= ", ?";
		}
		if($PC->GetPermission("EstimatedRequiredTimeDay")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("EstimatedRequiredTimeHour")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("EstimatedRequitedTimeMin")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("HasExpireTime")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("ExpireTime")=="WRITE")
		{
			if(SharedClass::IsDateFormat($ExpireTime))
				$query .= ", ?";
		}
		if($PC->GetPermission("TaskPeriority")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("TaskStatus")=="WRITE")
		{
			$query .= ", ?";
			if($TaskStatus=="DONE" || $TaskStatus=="REPLYED")
			{
				$query .= ", now() ";
			}
			/*if($study=="YES")
			{
				$query .= ", CONFWAIT ";
			}*/
		}
		if($PC->GetPermission("ParentID")=="WRITE")
		{
			$query .= ", ?";
		}
		if($PC->GetPermission("TaskGroupID")=="WRITE")
		{
			$query .= ", ?";
		}
        if($PC->GetPermission("StartTime")=="WRITE")
		{
					$query .= ", ?";
		}
        if($PC->GetPermission("EndTime")=="WRITE")
		{
					$query .= ", ?";
		}
		if($PC->GetPermission("study")=="WRITE")
		{
			$query .= ", ?";
		}

		$query .= ")";
		$ValueListArray = array();
		if($PC->GetPermission("ControllerID")=="WRITE")
			array_push($ValueListArray, $ControllerID);
		if($PC->GetPermission("ProgramLevelID")=="WRITE")
			array_push($ValueListArray, $ProgramLevelID);
		if($PC->GetPermission("ProjectID")=="WRITE")
			array_push($ValueListArray, $ProjectID);
		if($PC->GetPermission("ProjectTaskTypeID")=="WRITE")
			array_push($ValueListArray, $ProjectTaskTypeID); 
		if($PC->GetPermission("title")=="WRITE")
			array_push($ValueListArray, $title); 
		if($PC->GetPermission("description")=="WRITE")
			array_push($ValueListArray, $description); 
		if($PC->GetPermission("PeriodType")=="WRITE")
			array_push($ValueListArray, $PeriodType); 
		if($PC->GetPermission("CountOfDone")=="WRITE")
			array_push($ValueListArray, $CountOfDone); 
		if($PC->GetPermission("EstimatedStartTime")=="WRITE" && SharedClass::IsDateFormat($EstimatedStartTime))
			array_push($ValueListArray, $EstimatedStartTime); 
		if($PC->GetPermission("RealStartTime")=="WRITE"  && SharedClass::IsDateFormat($RealStartTime))
			array_push($ValueListArray, $RealStartTime); 
		if($PC->GetPermission("EstimatedRequiredTimeDay")=="WRITE")
			array_push($ValueListArray, $EstimatedRequiredTimeDay); 
		if($PC->GetPermission("EstimatedRequiredTimeHour")=="WRITE")
			array_push($ValueListArray, $EstimatedRequiredTimeHour); 
		if($PC->GetPermission("EstimatedRequitedTimeMin")=="WRITE")
			array_push($ValueListArray, $EstimatedRequitedTimeMin); 
		if($PC->GetPermission("HasExpireTime")=="WRITE")
			array_push($ValueListArray, $HasExpireTime); 
		if($PC->GetPermission("ExpireTime")=="WRITE"  && SharedClass::IsDateFormat($ExpireTime))
			array_push($ValueListArray, $ExpireTime); 
		if($PC->GetPermission("TaskPeriority")=="WRITE")
			array_push($ValueListArray, $TaskPeriority); 
		if($PC->GetPermission("TaskStatus")=="WRITE")
			array_push($ValueListArray, $TaskStatus); 
		if($PC->GetPermission("ParentID")=="WRITE")
			array_push($ValueListArray, $ParentID); 
		if($PC->GetPermission("TaskGroupID")=="WRITE")
			array_push($ValueListArray, $TaskGroupID); 
		if($PC->GetPermission("StartTime")=="WRITE")
			array_push($ValueListArray, $StartTime); 
		if($PC->GetPermission("EndTime")=="WRITE")
			array_push($ValueListArray, $EndTime);
		if($PC->GetPermission("study")=="WRITE")
			array_push($ValueListArray, $study);
		$mysql->Prepare($query);

               //echo $query;die();
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectTasks::GetLastID();
		//$mysql->audit("ثبت داده جدید در کار با کد ".$LastID);
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($LastID, "", "MAIN_TASK", $LastID, "ADD");
		
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $ProjectID: پروژه مربوطه
	* @param $ProjectTaskTypeID: نوع کار
	* @param $title: عنوان
	* @param $description: شرح
	* @param $PeriodType: پریود انجام
	* @param $CountOfDone: تعداد دفعات انجام
	* @param $EstimatedStartTime: زمان تخمینی شروع
	* @param $RealStartTime: زمان واقعی شروع
	* @param $EstimatedRequiredTimeDay: زمان مورد نیاز - روز
	* @param $EstimatedRequiredTimeHour: زمان مورد نیاز - ساعت
	* @param $EstimatedRequitedTimeMin: زمان مورد نیاز - دقیقه
	* @param $HasExpireTime: مهلت اقدام دارد؟
	* @param $ExpireTime: مهلت اقدام
	* @param $TaskPeriority: اولویت
	* @param $TaskStatus: وضعیت
	* @param $ParentID: کار بالاتر
	* @return 	*/
	static function Update($UpdateRecordID, $ProjectID, $ProgramLevelID, $ProjectTaskTypeID, $title, $description, $PeriodType, $CountOfDone, $EstimatedStartTime, $RealStartTime, $EstimatedRequiredTimeDay, $EstimatedRequiredTimeHour, $EstimatedRequitedTimeMin, $HasExpireTime, $ExpireTime, $TaskPeriority, $TaskStatus, $ParentID, $UpdateReason, $TaskGroupID, $ControllerID, $study ,$PC, $LetterNumber, $LetterType, $LetterDate)
	{
		$obj = new be_ProjectTasks();
		$obj->LoadDataFromDatabase($UpdateRecordID);

		$LogDesc = manage_ProjectTasks::ComparePassedDataWithDB($UpdateRecordID, $ProjectID, $ProjectTaskTypeID, $title, $description, $PeriodType, $CountOfDone, $EstimatedStartTime, $RealStartTime, $EstimatedRequiredTimeDay, $EstimatedRequiredTimeHour, $EstimatedRequitedTimeMin, $HasExpireTime, $ExpireTime, $TaskPeriority, $TaskStatus, $ParentID, $UpdateReason);
		$mysql = pdodb::getInstance();
		$k = 0;
		$query = "update projectmanagement.ProjectTasks set ";
		if($PC->GetPermission("ControllerID")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ControllerID=? ";
			$k++;
		}
		if($PC->GetPermission("TaskGroupID")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "TaskGroupID=? ";
			$k++;
		}
		if($PC->GetPermission("ProgramLevelID")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ProgramLevelID=? ";
			$k++;
		}
		if($PC->GetPermission("ProjectID")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ProjectID=? ";
			$k++; 
		}

		if($PC->GetPermission("ProjectID")=="READ" && $obj->ProjectID=="0"){

			if($k>0) 
				$query .= ", ";
			$query .= "ProjectID=? ";
			$k++; 
		}
		if($PC->GetPermission("ProjectTaskTypeID")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ProjectTaskTypeID=? ";
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
		if($PC->GetPermission("PeriodType")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "PeriodType=? ";
			$k++; 
		}
		if($PC->GetPermission("CountOfDone")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "CountOfDone=? ";
			$k++; 
		}
		if($PC->GetPermission("EstimatedStartTime")=="WRITE" &&  SharedClass::IsDateFormat($EstimatedStartTime))
		{
			if($k>0) 
				$query .= ", ";
			$query .= "EstimatedStartTime=? ";
			$k++; 
		}
		if($PC->GetPermission("RealStartTime")=="WRITE" &&  SharedClass::IsDateFormat($RealStartTime))
		{
			if($k>0) 
				$query .= ", ";
			$query .= "RealStartTime=? ";
			$k++; 
		}
		if($PC->GetPermission("EstimatedRequiredTimeDay")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "EstimatedRequiredTimeDay=? ";
			$k++; 
		}
		if($PC->GetPermission("EstimatedRequiredTimeHour")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "EstimatedRequiredTimeHour=? ";
			$k++; 
		}
		if($PC->GetPermission("EstimatedRequitedTimeMin")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "EstimatedRequitedTimeMin=? ";
			$k++; 
		}
		if($PC->GetPermission("HasExpireTime")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "HasExpireTime=? ";
			$k++; 
		}
		if($PC->GetPermission("ExpireTime")=="WRITE" &&  SharedClass::IsDateFormat($ExpireTime))
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ExpireTime=? ";
			$k++; 
		}
		if($PC->GetPermission("TaskPeriority")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "TaskPeriority=? ";
			$k++; 
		}
		if($PC->GetPermission("TaskStatus")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "TaskStatus=? ";
			if(($TaskStatus=="DONE" || $TaskStatus=="REPLYED") && $obj->TaskStatus!=$TaskStatus)
			{
				$query .= ", DoneDate=now() ";
			}
			$k++; 
		}
		if($PC->GetPermission("TaskStatus")=="READ" && $obj->study=="YES")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "TaskStatus=? ";
			if(($TaskStatus=="DONE" || $TaskStatus=="REPLYED") && $obj->TaskStatus!=$TaskStatus)
			{
				$query .= ", DoneDate=now() ";
			}

			$k++; 
		}

		if($PC->GetPermission("ParentID")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "ParentID=? ";
			$k++; 
		}
		if($PC->GetPermission("study")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "study=? ";
			$k++; 
		}


		$query .= " where ProjectTaskID=?";
		$ValueListArray = array();
		if($PC->GetPermission("ControllerID")=="WRITE")
		{
			array_push($ValueListArray, $ControllerID);
		}
		if($PC->GetPermission("TaskGroupID")=="WRITE")
		{
			array_push($ValueListArray, $TaskGroupID);
		}
		
		if($PC->GetPermission("ProgramLevelID")=="WRITE")
		{
			array_push($ValueListArray, $ProgramLevelID);
		}
		if($PC->GetPermission("ProjectID")=="WRITE")
		{
			array_push($ValueListArray, $ProjectID); 
		}
		if($PC->GetPermission("ProjectID")=="READ" && $obj->ProjectID=="0"){

			array_push($ValueListArray, $ProjectID); 
                }
		if($PC->GetPermission("ProjectTaskTypeID")=="WRITE")
		{
			array_push($ValueListArray, $ProjectTaskTypeID); 
		}
		if($PC->GetPermission("title")=="WRITE")
		{
			array_push($ValueListArray, $title); 
		}
		if($PC->GetPermission("description")=="WRITE")
		{
			array_push($ValueListArray, $description); 
		}
		if($PC->GetPermission("PeriodType")=="WRITE")
		{
			array_push($ValueListArray, $PeriodType); 
		}
		if($PC->GetPermission("CountOfDone")=="WRITE")
		{
			array_push($ValueListArray, $CountOfDone); 
		}
		if($PC->GetPermission("EstimatedStartTime")=="WRITE" &&  SharedClass::IsDateFormat($EstimatedStartTime))
		{
			array_push($ValueListArray, $EstimatedStartTime); 
		}
		if($PC->GetPermission("RealStartTime")=="WRITE" &&  SharedClass::IsDateFormat($RealStartTime))
		{
			array_push($ValueListArray, $RealStartTime); 
		}
		if($PC->GetPermission("EstimatedRequiredTimeDay")=="WRITE")
		{
			array_push($ValueListArray, $EstimatedRequiredTimeDay); 
		}
		if($PC->GetPermission("EstimatedRequiredTimeHour")=="WRITE")
		{
			array_push($ValueListArray, $EstimatedRequiredTimeHour); 
		}
		if($PC->GetPermission("EstimatedRequitedTimeMin")=="WRITE")
		{
			array_push($ValueListArray, $EstimatedRequitedTimeMin); 
		}
		if($PC->GetPermission("HasExpireTime")=="WRITE" )
		{
			array_push($ValueListArray, $HasExpireTime); 
		}
		if($PC->GetPermission("ExpireTime")=="WRITE" &&  SharedClass::IsDateFormat($ExpireTime))
		{
			array_push($ValueListArray, $ExpireTime); 
		}
		if($PC->GetPermission("TaskPeriority")=="WRITE")
		{
			array_push($ValueListArray, $TaskPeriority); 
		}
		if($PC->GetPermission("TaskStatus")=="WRITE")
		{
			if($study=="YES")
			{
			array_push($ValueListArray, 'CONFWAIT');
			}
			else{
			array_push($ValueListArray, $TaskStatus);} 

		}
		if($PC->GetPermission("TaskStatus")=="READ" && $obj->study=="YES")
		{
			array_push($ValueListArray, $TaskStatus); 
		}
		if($PC->GetPermission("ParentID")=="WRITE")
		{
			array_push($ValueListArray, $ParentID); 
		}
		if($PC->GetPermission("study")=="WRITE")
		{
			array_push($ValueListArray, $study); 
		}
		array_push($ValueListArray, $UpdateRecordID);
		$mysql->Prepare($query);
		/*if($_SESSION["UserID"]=="gholami-a") {
			       echo $query;die();
		}*/

		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در کار");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($UpdateRecordID, "گزینه های تغییر داده شده: ".$LogDesc."\r\n دلیل بروزرسانی: ".$UpdateReason, "MAIN_TASK", $UpdateRecordID, "UPDATE");

		// Logging The State Changes ...
		$mysql = pdodb::getInstance();
		$mysql->Prepare("SELECT TaskStatus FROM projectmanagement.ProjectTasks where ProjectTaskID = ?;");
		$res = $mysql->ExecuteStatement([$obj->ProjectTaskID]);
		$NewState = $res->fetch()["TaskStatus"];

		if ($obj->TaskStatus != $NewState)
		{
			$mysql->Prepare("insert into projectmanagement.ProjectTaskStatusChanges (TaskID, BeforeState, NewState, DateTime, PersonID) values (?, ?, ?, now(), ?);");
			$res = $mysql->ExecuteStatement([$obj->ProjectTaskID, $obj->TaskStatus, $NewState, $_SESSION["PersonID"]]);
		}
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectTasks set DeleteFlag='YES' where ProjectTaskID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		/*
		$query = "delete from projectmanagement.ProjectTasks where ProjectTaskID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ProjectTaskViewers where ProjectTaskID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ProjectTaskActivities where ProjectTaskID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ProjectTaskAssignedUsers where ProjectTaskID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ProjectTaskComments where ProjectTaskID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ProjectTaskDocuments where ProjectTaskID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ProjectTaskHistory where ProjectTaskID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ProjectTaskRequisites where ProjectTaskID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		*/
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از کار");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($RemoveRecordID, "", "MAIN_TASK", $RemoveRecordID, "DELETE");
		
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
		$query = "select ProjectTasks.ProjectTaskID
				,ProjectTasks.ProjectID
				,ProjectTasks.ProjectTaskTypeID
				,ProjectTasks.title
				,ProjectTasks.description
				,ProjectTasks.CreatorID
				,ProjectTasks.ControllerID
				,ProjectTasks.PeriodType
				,ProjectTasks.CountOfDone
				,ProjectTasks.EstimatedStartTime
				,ProjectTasks.RealStartTime
				,ProjectTasks.EstimatedRequiredTimeDay
				,ProjectTasks.EstimatedRequiredTimeHour
				,ProjectTasks.EstimatedRequitedTimeMin
				,ProjectTasks.HasExpireTime
				,ProjectTasks.ExpireTime
				,ProjectTasks.TaskPeriority
				,ProjectTasks.TaskStatus
				,ProjectTasks.ParentID
				,ProjectTasks.DoneDate
				,ProjectTasks.CreateDate
				,ProjectTasks.ProgramLevelID
				,ProjectTasks.TaskGroupID
				,ProjectTaskGroups.TaskGroupName
				,ProjectTasks.study
				, p1.title  as p1_title 
			, p2.title  as p2_title 
			, concat(g2j(ProjectTasks.CreateDate), ' ', substr(ProjectTasks.CreateDate, 12,10)) as CreateDate_Shamsi
			, concat(g2j(ProjectTasks.DoneDate), ' ', substr(ProjectTasks.DoneDate, 12,10)) as DoneDate_Shamsi
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName
			, concat(persons6.pfname, ' ', persons6.plname) as persons6_FullName
			, CASE ProjectTasks.PeriodType 
				WHEN 'ONCE' THEN 'یکبار' 
				WHEN 'EVERYDAY' THEN 'روزانه' 
				WHEN 'EVERYWEEK' THEN 'هفتگی' 
				WHEN 'EVERYMONTH' THEN 'ماهانه' 
				END as PeriodType_Desc 
			, g2j(ProjectTasks.EstimatedStartTime) as EstimatedStartTime_Shamsi 
			, g2j(ProjectTasks.RealStartTime) as RealStartTime_Shamsi
			, CASE ProjectTasks.HasExpireTime 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasExpireTime_Desc 
			, g2j(ProjectTasks.ExpireTime) as ExpireTime_Shamsi  
			, CASE ProjectTasks.TaskStatus 
				WHEN 'NOT_START' THEN 'اقدام نشده' 
				WHEN 'PROGRESSING' THEN 'در دست اقدام' 
				WHEN 'DONE' THEN 'اقدام شده' 
				WHEN 'SUSPENDED' THEN 'معلق' 
				WHEN 'REPLYED' THEN 'پاسخ داده شده'
				WHEN 'READY_FOR_TEST' THEN 'آماده برای کنترل' 
				WHEN 'CONFWAIT' THEN 'منتظرتایید'
				WHEN 'EXECUTECONF' THEN 'تاییدجهت اجرا'
				WHEN 'NOCONF' THEN 'عدم تایید' 
				END as TaskStatus_Desc 
			from projectmanagement.ProjectTasks 
			LEFT JOIN projectmanagement.projects  p1 on (p1.ProjectID=ProjectTasks.ProjectID) 
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) 
			LEFT JOIN projectmanagement.ProjectTaskGroups  on (ProjectTaskGroupID=TaskGroupID)
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID)
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=ProjectTasks.ControllerID)
			where ProjectTasks.DeleteFlag='NO' "; 
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTasks();
			$ret[$k]->TaskGroupID=$rec["TaskGroupID"];
			$ret[$k]->TaskGroupName=$rec["TaskGroupName"];
			$ret[$k]->ProgramLevelID=$rec["ProgramLevelID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->ProjectID_Desc=$rec["p1_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$ret[$k]->ProjectTaskTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ControllerID=$rec["ControllerID"];
			$ret[$k]->ControllerID_FullName=$rec["persons6_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->PeriodType=$rec["PeriodType"];
			$ret[$k]->PeriodType_Desc=$rec["PeriodType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->CountOfDone=$rec["CountOfDone"];
			$ret[$k]->EstimatedStartTime=$rec["EstimatedStartTime"];
			$ret[$k]->RealStartTime=$rec["RealStartTime"];
			$ret[$k]->EstimatedStartTime_Shamsi=$rec["EstimatedStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->RealStartTime_Shamsi=$rec["EstimatedStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EstimatedRequiredTimeDay=$rec["EstimatedRequiredTimeDay"];
			$ret[$k]->EstimatedRequiredTimeHour=$rec["EstimatedRequiredTimeHour"];
			$ret[$k]->EstimatedRequitedTimeMin=$rec["EstimatedRequitedTimeMin"];
			$ret[$k]->HasExpireTime=$rec["HasExpireTime"];
			$ret[$k]->HasExpireTime_Desc=$rec["HasExpireTime_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ExpireTime=$rec["ExpireTime"];
			$ret[$k]->ExpireTime_Shamsi=$rec["ExpireTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->TaskPeriority=str_pad($rec["TaskPeriority"], 2, '0', STR_PAD_LEFT);

			$ret[$k]->TaskStatus=$rec["TaskStatus"];
			$ret[$k]->TaskStatus_Desc=$rec["TaskStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ParentID=$rec["ParentID"];
			$ret[$k]->DoneDate=$rec["DoneDate"];
			$ret[$k]->DoneDate_Shamsi=$rec["DoneDate_Shamsi"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];
			$ret[$k]->study=$rec["study"];
			//$ret[$k]->ParentID_Desc=$rec["p16_title"]; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}
	function ShowSummary($RecID)
	{
		$ret = "<br>";
		$ret .= "<table class=\"table table-sm table-bordered\">";
		$ret .= "<tr>";
		$ret .= "<td>";
		$ret .= "<table class=\"table table-sm table-borderless\">";
		$obj = new be_ProjectTasks();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>".C_PROJECT.": </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->ProjectID_Desc, ENT_QUOTES, 'UTF-8');
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>".C_WORK_CODE.": </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->ProjectTaskID, ENT_QUOTES, 'UTF-8');
		$ret .= "</td>";
		$ret .= "</tr>";
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
		return $ret;
	}
	function ShowTabs($RecID, $CurrentPageName)
	{
		$ret = "<table class=\"table table-sm table-bordered\">";
 		$ret .= "<tr>";
		$ret .= "<td width=\"15%\" ";
		if($CurrentPageName=="NewProjectTasks")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='NewProjectTasks.php?UpdateID=".$RecID."'>".C_SESSION_INFO."</a></td>";
		$ret .= "<td width=\"15%\" ";
		if($CurrentPageName=="ManageProjectTaskAssignedUsers")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageProjectTaskAssignedUsers.php?ProjectTaskID=".$RecID."'>".C_WORK_CODE."/".C_VIEWERS."</a></td>";
		$ret .= "<td width=\"15%\" ";
		if($CurrentPageName=="ManageProjectTaskActivities")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageProjectTaskActivities.php?ProjectTaskID=".$RecID."'>".C_ACTIONS."</a></td>";
		$ret .= "<td width=\"15%\" ";
		if($CurrentPageName=="ManageProjectTaskComments")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageProjectTaskComments.php?ProjectTaskID=".$RecID."'>".C_NOTES."</a></td>";
		$ret .= "<td width=\"15%\" ";
		if($CurrentPageName=="ManageProjectTaskDocuments")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageProjectTaskDocuments.php?ProjectTaskID=".$RecID."'>".C_DOCUMENTS."</a></td>";
		$ret .= "<td width=\"15%\" ";
		if($CurrentPageName=="ManageProjectTaskRequisites")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageProjectTaskRequisites.php?ProjectTaskID=".$RecID."'>".C_PREREQUISITES."</a></td>";
		$ret .= "<td width=\"15%\" ";
		if($CurrentPageName=="ManageProjectTaskHistory")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageProjectTaskHistory.php?ProjectTaskID=".$RecID."'>".C_HISTORY."</a></td>";
		$ret .= "</table>";
		return $ret;
	}
	static function CreateARelatedTableSelectOptions($RelatedTable, $RelatedValueField, $RelatedDescriptionField, $OrderBy = "", $FilterStr = "")
	{
		if($OrderBy=="")
			$OrderBy = $RelatedValueField;

		$ret = "";
		$mysql = pdodb::getInstance();
		$mysql->Prepare("select * from $RelatedTable where $RelatedDescriptionField != '' $FilterStr order by $OrderBy");
		$res = $mysql->ExecuteStatement(array());
		while($rec = $res->fetch())
		{
			$ret .= "<option value='".$rec[$RelatedValueField]."'>";
			$ret .= $rec[$RelatedDescriptionField];
			$ret .= "</option>";
		}
		return $ret;
	}

	
	// جستجو در بین کارهای مجاز به دسترسی برای کاربر
	/**
	* @param $ProjectID: پروژه مربوطه
	* @param $ProjectTaskTypeID: نوع کار
	* @param $title: عنوان
	* @param $description: شرح
	* @param $CreatorID: ایجاد کننده
	* @param $TaskPeriority: اولویت
	* @param $TaskStatus: وضعیت
	* @param $ExecutorID: فرد منتسب
	* @param $TaskComment: کلمه کلیدی در یادداشت
	* @param $DocumentDescription: کلمه کلیدی در اسناد
	* @param $ActivityDescription: کلمه کلیدی در اقدامات
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند*  
	* @return لیست داده های حاصل جستجو */
	 
	static function Search($ProjectID, $ProjectTaskTypeID, $ProjectTaskID, $title, $description, $CreatorID, $TaskPeriority, $TaskStatus, $ExecutorID, $TaskComment, $DocumentDescription, $ActivityDescription, $OtherConditions, $FromRec, $NumberOfRec, $OrderByFieldName="", $OrderType="")
	{
//print_r($ProjectTaskID);
		require_once("ProjectMembers.class.php");
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
		$ManagedProjects = manage_ProjectMembers::GetProjectIDsOfManager($CallerPersonID);
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select distinct
				(select sum(ActivityLength) from projectmanagement.ProjectTaskActivities where ProjectTaskID=ProjectTasks.ProjectTaskID) as ActivityLength  
				,ProjectTasks.ProjectTaskID
				,ProjectTasks.ProjectID
				,ProjectTasks.ProjectTaskTypeID
				,ProjectTasks.title
				,ProjectTasks.description
				,ProjectTasks.CreatorID
				,ProjectTasks.ControllerID
				,ProjectTasks.PeriodType
				,ProjectTasks.CountOfDone
				,ProjectTasks.EstimatedStartTime
				,ProjectTasks.RealStartTime
				,ProjectTasks.EstimatedRequiredTimeDay
				,ProjectTasks.EstimatedRequiredTimeHour
				,ProjectTasks.EstimatedRequitedTimeMin
				,ProjectTasks.HasExpireTime
				,ProjectTasks.ExpireTime
				,ProjectTasks.TaskPeriority
				,ProjectTasks.TaskStatus
				,ProjectTasks.ParentID
				,ProjectTasks.DoneDate
				,ProjectTasks.CreateDate
				,ProjectTasks.ProgramLevelID
				,ProjectTasks.TaskGroupID
				,ProjectTasks.study
				,ProjectTaskGroups.TaskGroupName
			, p1.title  as p1_title 
			, p2.title  as p2_title 
			, g2j(ProjectTasks.CreateDate) as CreateDate_Shamsi
			, concat(g2j(ProjectTasks.DoneDate), ' ', substr(ProjectTasks.DoneDate, 12,5)) as DoneDate_Shamsi
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, concat(persons6.pfname, ' ', persons6.plname) as persons6_FullName
			, CASE ProjectTasks.PeriodType 
				WHEN 'ONCE' THEN 'یکبار' 
				WHEN 'EVERYDAY' THEN 'روزانه' 
				WHEN 'EVERYWEEK' THEN 'هفتگی' 
				WHEN 'EVERYMONTH' THEN 'ماهانه' 
				END as PeriodType_Desc 
			, g2j(ProjectTasks.EstimatedStartTime) as EstimatedStartTime_Shamsi 
			, g2j(ProjectTasks.RealStartTime) as RealStartTime_Shamsi
			, CASE ProjectTasks.HasExpireTime 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasExpireTime_Desc 
			, g2j(ProjectTasks.ExpireTime) as ExpireTime_Shamsi
			, CASE ProjectTasks.TaskStatus 
				WHEN 'NOT_START' THEN 'اقدام نشده' 
				WHEN 'PROGRESSING' THEN 'در دست اقدام' 
				WHEN 'DONE' THEN 'اقدام شده' 
				WHEN 'SUSPENDED' THEN 'معلق' 
				WHEN 'REPLYED' THEN 'پاسخ داده شده'
				WHEN 'READY_FOR_TEST' THEN 'آماده برای کنترل' 
				WHEN 'CONFWAIT' THEN 'منتظرتایید'
				WHEN 'EXECUTECONF' THEN 'تاییدجهت اجرا'
				WHEN 'NOCONF' THEN 'عدم تایید'				
				END as TaskStatus_Desc
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName
			, concat(programs.title, ' - ', ProgramLevels.title) as LevelFullName
                        ,CASE WHEN Acs.LastActivityDate IS NULL THEN 'بدون اقدام' ELSE g2j(Acs.LastActivityDate) END as LastActivityDate
			from 
			projectmanagement.ProjectTasks
			LEFT JOIN (SELECT ProjectTaskID, substr(max(ActivityDate), 1, 11) as LastActivityDate FROM projectmanagement.ProjectTaskActivities group by ProjectTaskID) Acs using (ProjectTaskID)
			LEFT JOIN projectmanagement.ProgramLevels on (ProgramLevels.ProgramLevelID=ProjectTasks.ProgramLevelID) 
			LEFT JOIN projectmanagement.programs on (programs.ProgramID=ProgramLevels.ProgramID)
			LEFT JOIN projectmanagement.ProjectTaskGroups  on (ProjectTaskGroupID=TaskGroupID)
			";
		$query .= " JOIN
					 (select distinct ProjectTaskID
						from projectmanagement.ProjectTasks 
						LEFT JOIN projectmanagement.ProjectMembers on (ProjectMembers.ProjectID=ProjectTasks.ProjectID)
						LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) ";
		$query .= " ) as SelectedTasks using (ProjectTaskID)";
		$query .= " LEFT JOIN projectmanagement.projects on (projects.ProjectID=ProjectTasks.ProjectID)";
		$query .= " LEFT JOIN projectmanagement.ProjectMembers on (ProjectMembers.ProjectID=ProjectTasks.ProjectID)";
		$query .= " LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)";
		if($TaskComment!="")
			$query .= " LEFT JOIN projectmanagement.ProjectTaskComments using (ProjectTaskID)";
		if($ActivityDescription!="")
			$query .= " LEFT JOIN projectmanagement.ProjectTaskActivities using (ProjectTaskID)";
		if($DocumentDescription!="")
			$query .= " LEFT JOIN projectmanagement.ProjectTaskDocuments using (ProjectTaskID)";
		$query .= "  
			LEFT JOIN projectmanagement.projects  p1 on (p1.ProjectID=ProjectTasks.ProjectID) 
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=ProjectTasks.ControllerID)
			  ";

		$query .= "	where 
						ProjectTasks.DeleteFlag='NO' and 
						( (ProjectMembers.PersonID=? and ProjectMembers.AccessType<>'MEMBER') 
							or ProjectTasks.CreatorID=? or ProjectTaskAssignedUsers.PersonID=? or ProjectTaskID in (select distinct TaskID from projectmanagement.ProjectTaskRefers where FromPerson = ? or ToPerson = ?)) ";
		if($ProjectID!="0" && $ProjectID!="") 
			$query .= " and ProjectTasks.ProjectID=? ";
		if($ProjectTaskTypeID!="0" && $ProjectTaskTypeID!="") 
			$query .= " and ProjectTasks.ProjectTaskTypeID=? ";
                if($ProjectTaskID!="0" && $ProjectTaskID!="") 
			$query .= " and ProjectTasks.ProjectTaskID=? ";
		if($title!="") 
			$query .= " and ProjectTasks.title like ? ";
		if($description!="") 
			$query .= " and ProjectTasks.description like ? ";
		if($CreatorID!="0" && $CreatorID!="") 
			$query .= " and ProjectTasks.CreatorID=? ";
		if($TaskPeriority!="0" && $TaskPeriority!="") 
			$query .= " and ProjectTasks.TaskPeriority=? ";
		if($TaskStatus!="0" && $TaskStatus!="") 
			$query .= " and ProjectTasks.TaskStatus=? ";
		if($ExecutorID!="0" && $ExecutorID!="") 
			$query .= " and ProjectTaskAssignedUsers.PersonID=? ";
		if($TaskComment!="")
			$query .= " and CommentBody like ? "; 
		if($DocumentDescription!="")
			$query .= " and DocumentDescription like ? "; 
		if($ActivityDescription!="")
			$query .= " and ActivityDescription like ? ";
		//$query .= $OtherConditions;

		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		
		$query .=  " limit $FromRec, $NumberOfRec;";
		$mysql->Prepare($query);

		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID); 

		if($ProjectTaskTypeID!="0" && $ProjectTaskTypeID!="") 
			array_push($ValueListArray, $ProjectTaskTypeID); 
		if($title!="") 
			array_push($ValueListArray, "%".$title."%"); 
		if($description!="") 
			array_push($ValueListArray, "%".$description."%"); 
		if($CreatorID!="0" && $CreatorID!="") 
			array_push($ValueListArray, $CreatorID); 
		if($TaskPeriority!="0" && $TaskPeriority!="") 
			array_push($ValueListArray, $TaskPeriority); 
		if($TaskStatus!="0" && $TaskStatus!="") 
			array_push($ValueListArray, $TaskStatus);
		if($ExecutorID!="0" && $ExecutorID!="")
		 	array_push($ValueListArray, $ExecutorID);
		if($TaskComment!="")
			array_push($ValueListArray, "%".$TaskComment."%");
		if($DocumentDescription!="")
			array_push($ValueListArray, "%".$DocumentDescription."%");
		if($ActivityDescription!="")
			array_push($ValueListArray, "%".$ActivityDescription."%");
		if($ProjectTaskID!="0" && $ProjectTaskID!="") 
			array_push($ValueListArray, $ProjectTaskID); 

		/*if($_SESSION["PersonID"]=="401371457")
		{
			echo $query;
			print_r($ValueListArray);
		}*/

		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTasks();
			$ret[$k]->TaskGroupID=$rec["TaskGroupID"];
			$ret[$k]->TaskGroupName=$rec["TaskGroupName"];
			$ret[$k]->ProgramLevelID_Desc=$rec["LevelFullName"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->ProjectID_Desc=$rec["p1_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$ret[$k]->ProjectTaskTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ControllerID=$rec["ControllerID"];
			$ret[$k]->ControllerID_FullName=$rec["persons6_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->PeriodType=$rec["PeriodType"];
			$ret[$k]->PeriodType_Desc=$rec["PeriodType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->CountOfDone=$rec["CountOfDone"];
			$ret[$k]->EstimatedStartTime=$rec["EstimatedStartTime"];
			$ret[$k]->RealStartTime=$rec["RealStartTime"];
			$ret[$k]->EstimatedStartTime_Shamsi=$rec["EstimatedStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->RealStartTime_Shamsi=$rec["RealStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EstimatedRequiredTimeDay=$rec["EstimatedRequiredTimeDay"];
			$ret[$k]->EstimatedRequiredTimeHour=$rec["EstimatedRequiredTimeHour"];
			$ret[$k]->EstimatedRequitedTimeMin=$rec["EstimatedRequitedTimeMin"];
			$ret[$k]->HasExpireTime=$rec["HasExpireTime"];
			$ret[$k]->HasExpireTime_Desc=$rec["HasExpireTime_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ExpireTime=$rec["ExpireTime"];
			$ret[$k]->ExpireTime_Shamsi=$rec["ExpireTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->TaskPeriority=str_pad($rec["TaskPeriority"], 2, '0', STR_PAD_LEFT);
			$ret[$k]->TaskStatus=$rec["TaskStatus"];
			$ret[$k]->TaskStatus_Desc=$rec["TaskStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ParentID=$rec["ParentID"];
			$ret[$k]->DoneDate=$rec["DoneDate"];
			$ret[$k]->DoneDate_Shamsi=$rec["DoneDate_Shamsi"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];
			//$ret[$k]->ParentID_Desc=$rec["p16_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->CanRemoveByCaller = false;
			$ret[$k]->ActivityLength = $rec["ActivityLength"];
			$ret[$k]->LastActivityDate = $rec["LastActivityDate"];
			// در صورتیکه کاربر مدیر پروژه باشد در هر وضعیتی می تواند کار را حذف کند
			for($i=0; $i<count($ManagedProjects); $i++)
			{
				if($rec["ProjectID"]==$ManagedProjects[$i])
				{
					$ret[$k]->CanRemoveByCaller = true;
					break;
				}
			}
			if($ret[$k]->CanRemoveByCaller==false)
			{
				if($rec["CreatorID"]==$CallerPersonID && $rec["TaskStatus"]=="NOT_START")
				{
					// اگر مدیر پروژه نبود و ایجاد کننده کار بود تنها در صورتی دسترسی حذف دارد که کار اقدام نشده باشد
					if($rec["TaskStatus"]=="NOT_START")
						$ret[$k]->CanRemoveByCaller = true;
				}
			}		
			$k++;
		}
		return $ret;
	}



	// تعداد نتایج جستجو در بین کارهای مجاز به دسترسی برای کاربر
	/**
	* @param $ProjectID: پروژه مربوطه
	* @param $ProjectTaskTypeID: نوع کار
	* @param $title: عنوان
	* @param $description: شرح
	* @param $CreatorID: ایجاد کننده
	* @param $TaskPeriority: اولویت
	* @param $TaskStatus: وضعیت
	* @param $ExecutorID: فرد منتسب
	* @param $TaskComment: کلمه کلیدی در یادداشت
	* @param $DocumentDescription: کلمه کلیدی در اسناد
	* @param $ActivityDescription: کلمه کلیدی در اقدامات
	* @return لیست داده های حاصل جستجو */
	 
	static function GetTasksCountInSearchResult($ProjectID, $ProjectTaskTypeID, $title, $description, $CreatorID, $TaskPeriority, $TaskStatus, $ExecutorID, $TaskComment, $DocumentDescription, $ActivityDescription)
	{
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(distinct ProjectTasks.ProjectTaskID) as TotalCount 
			from 
			projectmanagement.ProjectTasks ";
		$query .= " LEFT JOIN projectmanagement.projects using (ProjectID)";
		$query .= " LEFT JOIN projectmanagement.ProjectMembers using (ProjectID)";
		$query .= " LEFT JOIN projectmanagement.ProjectTaskComments using (ProjectTaskID)";
		$query .= " LEFT JOIN projectmanagement.ProjectTaskActivities using (ProjectTaskID)";
		$query .= " LEFT JOIN projectmanagement.ProjectTaskDocuments using (ProjectTaskID)";
		$query .= " LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)";
		$query .= "  
			LEFT JOIN projectmanagement.projects  p1 on (p1.ProjectID=ProjectTasks.ProjectID) 
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			  ";
		$cond = " ProjectTasks.DeleteFlag='NO' and (";
		// یا کاربر فراخواننده در پروژه ای که کار در آن تعریف شده به طور کلی سمت مدیر یا ناظر دارد 
		$cond .= " (ProjectMembers.PersonID=? and ProjectMembers.AccessType<>'MEMBER') or ";
		// یا کاربر فراخواننده ایجاد کننده کار است
		$cond .= " ProjectTasks.CreatorID=? or ";
		// کاربر مجری یا ناظر کار است
		$cond .= " ProjectTaskAssignedUsers.PersonID=? ";
		$cond .= "or ProjectTaskID in (select distinct TaskID from projectmanagement.ProjectTaskRefers where FromPerson = ? or ToPerson = ?)";
		if($ProjectID!="0" && $ProjectID!="") 
			$cond .= " and ProjectTasks.ProjectID=? ";
		if($ProjectTaskTypeID!="0" && $ProjectTaskTypeID!="") 
			$cond .= " and ProjectTasks.ProjectTaskTypeID=? ";
		if($title!="") 
			$cond .= " and ProjectTasks.title like ? ";
		if($description!="") 
			$cond .= " and ProjectTasks.description like ? ";
		if($CreatorID!="0" && $CreatorID!="") 
			$cond .= " and ProjectTasks.CreatorID=? ";
		if($TaskPeriority!="0" && $TaskPeriority!="") 
			$cond .= " and ProjectTasks.TaskPeriority=? ";
		if($TaskStatus!="0" && $TaskStatus!="") 
			$cond .= " and ProjectTasks.TaskStatus=? ";
		if($ExecutorID!="0" && $ExecutorID!="") 
			$cond .= " and ProjectTaskAssignedUsers.PersonID=? ";
		if($TaskComment!="")
			$cond .= " and CommentBody like ? "; 
		if($DocumentDescription!="")
			$cond .= " and DocumentDescription like ? "; 
		if($ActivityDescription!="")
			$cond .= " and ActivityDescription like ? "; 
		
		$query .= " where ";
		$query .= $cond;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID); 
		if($ProjectTaskTypeID!="0" && $ProjectTaskTypeID!="") 
			array_push($ValueListArray, $ProjectTaskTypeID); 
		if($title!="") 
			array_push($ValueListArray, "%".$title."%"); 
		if($description!="") 
			array_push($ValueListArray, "%".$description."%"); 
		if($CreatorID!="0" && $CreatorID!="") 
			array_push($ValueListArray, $CreatorID); 
		if($TaskPeriority!="0" && $TaskPeriority!="") 
			array_push($ValueListArray, $TaskPeriority); 
		if($TaskStatus!="0" && $TaskStatus!="") 
			array_push($ValueListArray, $TaskStatus);
		if($ExecutorID!="0" && $ExecutorID!="")
		 	array_push($ValueListArray, $ExecutorID);
		if($TaskComment!="")
			array_push($ValueListArray, "%".$TaskComment."%");
		if($DocumentDescription!="")
			array_push($ValueListArray, "%".$DocumentDescription."%");
		if($ActivityDescription!="")
			array_push($ValueListArray, "%".$ActivityDescription."%");
			
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}

	static function GetTasksCountForControl($ProjectID)
	{
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(distinct ProjectTasks.ProjectTaskID) as TotalCount
			from 
			projectmanagement.ProjectTasks ";
		$query .= " 
			LEFT JOIN projectmanagement.ProjectMembers using (ProjectID) 
			LEFT JOIN projectmanagement.projects as p1 using (ProjectID)
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			  ";
		$cond = "  ProjectTasks.DeleteFlag='NO' and ";
		// کاربر مجری یا ناظر کار است
		$cond .= " ProjectTasks.ControllerID=? ";
		if($ProjectID!="0" && $ProjectID!="") 
			$cond .= " and ProjectTasks.ProjectID=? ";
		
		$query .= " where TaskStatus='READY_FOR_TEST' and ";
		$query .= $cond;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		if($rec=$res->fetch())
			return $rec["TotalCount"];
		return 0;
	}

	static function GetPersonCartableTasksCount($PersonID)
	{
		$mysql = pdodb::getInstance();
		$ret = array();
		$query = "select count(distinct ProjectTasks.ProjectTaskID) as TotalCount
			from 
			projectmanagement.ProjectTasks ";
		$query .= " 
			LEFT JOIN projectmanagement.ProjectMembers using (ProjectID) 
			LEFT JOIN projectmanagement.projects as p1 using (ProjectID)
			LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			  ";
		$cond = "  ProjectTasks.DeleteFlag='NO' and ";
		// کاربر مجری یا ناظر کار است
		$cond .= " ProjectTaskAssignedUsers.PersonID=? ";

	        if($PersonID == '542'){// || $PersonID == '401367373'
		$query3="select DATE_SUB(now(),INTERVAL 6 month) as date";
		$resm=$mysql->Execute($query3);
		$member = $resm->fetch();
		$cond .= " and ProjectTasks.CreateDate >='".$member["date"]."' ";
		}

		
		$query .= " where (TaskStatus='NOT_START' or TaskStatus='PROGRESSING') and ";
		$query .= $cond;
		$mysql->Prepare($query);

		$ValueListArray = array();
		array_push($ValueListArray, $PersonID);
		$res = $mysql->ExecuteStatement($ValueListArray);

		if($rec=$res->fetch())
			return $rec["TotalCount"];
		return 0;
	}
	
	static function GetTasksCountInKartable($ProjectID)
	{
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(distinct ProjectTasks.ProjectTaskID) as TotalCount
			from 
			projectmanagement.ProjectTasks ";
		$query .= " 
			LEFT JOIN projectmanagement.ProjectMembers using (ProjectID) 
			LEFT JOIN projectmanagement.projects as p1 using (ProjectID)
			LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			  ";
		$cond = "  ProjectTasks.DeleteFlag='NO' and ";
		// کاربر مجری یا ناظر کار است
		$cond .= " ProjectTaskAssignedUsers.PersonID=? ";
		if($ProjectID!="0" && $ProjectID!="") 
			$cond .= " and ProjectTasks.ProjectID=? ";

	        if($CallerPersonID == '542'){// || $CallerPersonID == '401367373'
		$query3="select DATE_SUB(now(),INTERVAL 6 month) as date";
		$resm=$mysql->Execute($query3);
		$member = $resm->fetch();
		$cond .= " and ProjectTasks.CreateDate >='".$member["date"]."' ";
		}

		
		$query .= " where (TaskStatus='NOT_START' or TaskStatus='PROGRESSING') and ";
		$query .= $cond;
		$mysql->Prepare($query);

		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID); 
		$res = $mysql->ExecuteStatement($ValueListArray);

                if($_SESSION["UserID"]=='bimakr' /*|| $_SESSION["UserID"]=='hosseini.s' */){
                echo '<br>';
		/*echo $query;
                die();*/
                }

		$i=0;
		if($rec=$res->fetch())
			return $rec["TotalCount"];
		return 0;
	}

	static function GetUserRequestedTasksCount($ProjectID)
	{
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(distinct ProjectTasks.ProjectTaskID) as TotalCount
			from 
			projectmanagement.ProjectTasks ";
		$query .= " 
			LEFT JOIN projectmanagement.ProjectMembers using (ProjectID) 
			LEFT JOIN projectmanagement.projects as p1 using (ProjectID)
			LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			  ";
		$cond = "  ProjectTasks.DeleteFlag='NO' and ";
		// کاربر ایجاد کننده کار است
		$cond .= " ProjectTasks.CreatorID=? ";
		if($ProjectID!="0" && $ProjectID!="") 
			$cond .= " and ProjectTasks.ProjectID=? ";
		
		$query .= " where ";
		$query .= $cond;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		if($rec=$res->fetch())
			return $rec["TotalCount"];
		return 0;
	}
	
	static function GetTasksForControl($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName="", $OrderType="")
	{
		require_once("ProjectMembers.class.php");
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
		$ManagedProjects = manage_ProjectMembers::GetProjectIDsOfManager($CallerPersonID);
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select distinct ProjectTasks.ProjectTaskID
				,ProjectTasks.ProjectID
				,ProjectTasks.ProjectTaskTypeID
				,ProjectTasks.title
				,ProjectTasks.description
				,ProjectTasks.CreatorID
				,ProjectTasks.PeriodType
				,ProjectTasks.CountOfDone
				,ProjectTasks.EstimatedStartTime
				,ProjectTasks.RealStartTime
				,ProjectTasks.EstimatedRequiredTimeDay
				,ProjectTasks.EstimatedRequiredTimeHour
				,ProjectTasks.EstimatedRequitedTimeMin
				,ProjectTasks.HasExpireTime
				,ProjectTasks.ExpireTime
				,ProjectTasks.TaskPeriority
				,ProjectTasks.TaskStatus
				,ProjectTasks.ParentID
				,ProjectTasks.DoneDate
				,ProjectTasks.CreateDate
				,ProjectTasks.ProgramLevelID
				,ProjectTasks.TaskGroupID
				,ProjectTaskGroups.TaskGroupName
			, p1.title  as p1_title 
			, p2.title  as p2_title 
			, concat(g2j(ProjectTasks.CreateDate), ' ', substr(ProjectTasks.CreateDate, 12,10)) as CreateDate_Shamsi
			, concat(g2j(ProjectTasks.DoneDate), ' ', substr(ProjectTasks.DoneDate, 12,10)) as DoneDate_Shamsi
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE ProjectTasks.PeriodType 
				WHEN 'ONCE' THEN 'یکبار' 
				WHEN 'EVERYDAY' THEN 'روزانه' 
				WHEN 'EVERYWEEK' THEN 'هفتگی' 
				WHEN 'EVERYMONTH' THEN 'ماهانه' 
				END as PeriodType_Desc 
			, g2j(ProjectTasks.EstimatedStartTime) as EstimatedStartTime_Shamsi 
			, g2j(ProjectTasks.RealStartTime) as RealStartTime_Shamsi
			, CASE ProjectTasks.HasExpireTime 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasExpireTime_Desc 
			, g2j(ProjectTasks.ExpireTime) as ExpireTime_Shamsi
			, CASE ProjectTasks.TaskStatus 
				WHEN 'NOT_START' THEN 'اقدام نشده' 
				WHEN 'PROGRESSING' THEN 'در دست اقدام' 
				WHEN 'DONE' THEN 'اقدام شده' 
				WHEN 'SUSPENDED' THEN 'معلق' 
				WHEN 'REPLYED' THEN 'پاسخ داده شده'
				WHEN 'READY_FOR_TEST' THEN 'آماده برای کنترل' 
				WHEN 'CONFWAIT' THEN 'منتظرتایید'
				WHEN 'EXECUTECONF' THEN 'تاییدجهت اجرا'
				WHEN 'NOCONF' THEN 'عدم تایید'
				END as TaskStatus_Desc 
			from 
			projectmanagement.ProjectTasks ";
		$query .= " 
			LEFT JOIN projectmanagement.ProjectMembers using (ProjectID) 
			LEFT JOIN projectmanagement.projects as p1 using (ProjectID)
			LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID)
			LEFT JOIN projectmanagement.ProjectTaskGroups  on (ProjectTaskGroupID=TaskGroupID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			  ";
		$cond = "  ProjectTasks.DeleteFlag='NO' and ";
		// کاربر کنترل کننده است
		$cond .= " ProjectTasks.ControllerID=? ";
		if($ProjectID!="0" && $ProjectID!="") 
			$cond .= " and ProjectTasks.ProjectID=? ";
		
		$query .= " where TaskStatus='READY_FOR_TEST' and ";
		$query .= $cond;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		else
			$query .= " order by TaskPeriority, EstimatedStartTime ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		//echo $query;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTasks();
			$ret[$k]->TaskGroupID=$rec["TaskGroupID"];
			$ret[$k]->TaskGroupName=$rec["TaskGroupName"];
			$ret[$k]->ProgramLevelID=$rec["ProgramLevelID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->ProjectID_Desc=$rec["p1_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$ret[$k]->ProjectTaskTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->PeriodType=$rec["PeriodType"];
			$ret[$k]->PeriodType_Desc=$rec["PeriodType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->CountOfDone=$rec["CountOfDone"];
			$ret[$k]->EstimatedStartTime=$rec["EstimatedStartTime"];
			$ret[$k]->RealStartTime=$rec["RealStartTime"];
			$ret[$k]->EstimatedStartTime_Shamsi=$rec["EstimatedStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->RealStartTime_Shamsi=$rec["RealStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EstimatedRequiredTimeDay=$rec["EstimatedRequiredTimeDay"];
			$ret[$k]->EstimatedRequiredTimeHour=$rec["EstimatedRequiredTimeHour"];
			$ret[$k]->EstimatedRequitedTimeMin=$rec["EstimatedRequitedTimeMin"];
			$ret[$k]->HasExpireTime=$rec["HasExpireTime"];
			$ret[$k]->HasExpireTime_Desc=$rec["HasExpireTime_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ExpireTime=$rec["ExpireTime"];
			$ret[$k]->ExpireTime_Shamsi=$rec["ExpireTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->TaskPeriority=str_pad($rec["TaskPeriority"], 2, '0', STR_PAD_LEFT);
			$ret[$k]->TaskStatus=$rec["TaskStatus"];
			$ret[$k]->TaskStatus_Desc=$rec["TaskStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ParentID=$rec["ParentID"];
			$ret[$k]->DoneDate=$rec["DoneDate"];
			$ret[$k]->DoneDate_Shamsi=$rec["DoneDate_Shamsi"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];
			//$ret[$k]->ParentID_Desc=$rec["p16_title"]; // محاسبه از روی جدول وابسته

			$ret[$k]->CanRemoveByCaller = false;
			$k++;
		}
		return $ret;
	}
	
	// لیست کارهای موجود در کارتابل برای کاربر
	/**
	* @param $ProjectID: پروژه مربوطه
	* @return لیست داده های حاصل جستجو */
	 
	static function GetTasksInKartable($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName="", $OrderType="")
	{
		require_once("ProjectMembers.class.php");
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
//if ($_SESSION["PersonID"] == 401371457) $CallerPersonID = 201406;
		$ManagedProjects = manage_ProjectMembers::GetProjectIDsOfManager($CallerPersonID);
		$mysql = pdodb::getInstance();
		$k=0;
		$cond1='';
		$ret = array();
		if($ProjectID!="0" && $ProjectID!="") {
		$query2 = "select ProjectMembers.ProjectmemberID
				,ProjectMembers.ProjectID
				,ProjectMembers.PersonID
				,ProjectMembers.AccessType
				,ProjectMembers.ParticipationPercent
				,ProjectMembers.CreatorID
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, CASE ProjectMembers.AccessType 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'VIEWER' THEN 'ناظر' 
				WHEN 'MANAGER' THEN 'مدیر' 
				WHEN 'PMMANAGER' THEN 'کارشناس مدیریت فرآیندها' 
				END as AccessType_Desc 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName  from projectmanagement.ProjectMembers 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectMembers.PersonID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectMembers.CreatorID) 

			 ";
		$query2 .= " where ProjectMembers.ProjectID=? and ProjectMembers.PersonID=? ";
		$mysql->Prepare($query2);
		$ValueList = array();
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueList, $ProjectID); 
		array_push($ValueList, $CallerPersonID);
		$res2 = $mysql->ExecuteStatement($ValueList);
/*if($_SESSION["UserID"]=='gholami-a'){
		echo $query2;
}*/

		$rec2=$res2->fetch();
			if($rec2['AccessType']=="PMMANAGER" ){
				$cond1 .= " or ProjectTasks.TaskStatus='CONFWAIT' ";
				}
			else if($rec2['AccessType']=="MANAGER"){
				$cond1 .= " or ProjectTasks.TaskStatus='EXECUTECONF' or ProjectTasks.TaskStatus='NOCONF'";
				}

		}


		$query = "select distinct ProjectTasks.ProjectTaskID
				,ProjectTasks.ProjectID
				,ProjectTasks.ProjectTaskTypeID
				,ProjectTasks.title
				,ProjectTasks.description
				,ProjectTasks.CreatorID
				,ProjectTasks.PeriodType
				,ProjectTasks.CountOfDone
				,ProjectTasks.EstimatedStartTime
				,ProjectTasks.RealStartTime
				,ProjectTasks.EstimatedRequiredTimeDay
				,ProjectTasks.EstimatedRequiredTimeHour
				,ProjectTasks.EstimatedRequitedTimeMin
				,ProjectTasks.HasExpireTime
				,ProjectTasks.ExpireTime
				,ProjectTasks.TaskPeriority
				,ProjectTasks.TaskStatus
				,ProjectTasks.ParentID
				,ProjectTasks.DoneDate
				,ProjectTasks.CreateDate
				,ProjectTasks.ProgramLevelID
				,ProjectTasks.TaskGroupID
				,ProjectTaskGroups.TaskGroupName,ProjectTasks.study
			, p1.title  as p1_title 
			, p2.title  as p2_title 
			, concat(g2j(ProjectTasks.CreateDate), ' ', substr(ProjectTasks.CreateDate, 12,10)) as CreateDate_Shamsi
			, concat(g2j(ProjectTasks.DoneDate), ' ', substr(ProjectTasks.DoneDate, 12,10)) as DoneDate_Shamsi
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE ProjectTasks.PeriodType 
				WHEN 'ONCE' THEN 'یکبار' 
				WHEN 'EVERYDAY' THEN 'روزانه' 
				WHEN 'EVERYWEEK' THEN 'هفتگی' 
				WHEN 'EVERYMONTH' THEN 'ماهانه' 
				END as PeriodType_Desc 
			, g2j(ProjectTasks.EstimatedStartTime) as EstimatedStartTime_Shamsi 
			, g2j(ProjectTasks.RealStartTime) as RealStartTime_Shamsi
			, CASE ProjectTasks.HasExpireTime 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasExpireTime_Desc 
			, g2j(ProjectTasks.ExpireTime) as ExpireTime_Shamsi 
			, ProjectTasks.TaskPeriority as TaskPeriority_Desc 
			, CASE ProjectTasks.TaskStatus 
				WHEN 'NOT_START' THEN 'اقدام نشده' 
				WHEN 'PROGRESSING' THEN 'در دست اقدام' 
				WHEN 'DONE' THEN 'اقدام شده' 
				WHEN 'SUSPENDED' THEN 'معلق' 
				WHEN 'REPLYED' THEN 'پاسخ داده شده'
				WHEN 'READY_FOR_TEST' THEN 'آماده برای کنترل' 
				WHEN 'CONFWAIT' THEN 'منتظرتایید'
				WHEN 'EXECUTECONF' THEN 'تاییدجهت اجرا'
				WHEN 'NOCONF' THEN 'عدم تایید'				
				END as TaskStatus_Desc 
			from 
			projectmanagement.ProjectTasks ";
		$query .= " 
			LEFT JOIN projectmanagement.ProjectMembers using (ProjectID) 
			LEFT JOIN projectmanagement.projects as p1 using (ProjectID)
			LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID)
			LEFT JOIN projectmanagement.ProjectTaskGroups  on (ProjectTaskGroupID=TaskGroupID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			  ";
		$cond = "  ProjectTasks.DeleteFlag='NO' and ";
		// کاربر مجری یا ناظر کار است
		$cond .= " ProjectTaskAssignedUsers.PersonID=? ";
		if($ProjectID!="0" && $ProjectID!="") 
			$cond .= " and ProjectTasks.ProjectID=? ";

	        if($CallerPersonID == '542'){// || $CallerPersonID == '401367373'
		$query3="select DATE_SUB(now(),INTERVAL 6 month) as date";
		$resm=$mysql->Execute($query3);
		$member = $resm->fetch();
		$cond .= " and ProjectTasks.CreateDate >='".$member["date"]."' ";
		}

		
		$query .= " where (TaskStatus='NOT_START' or TaskStatus='PROGRESSING' ".$cond1.") and ";


		$query .= $cond;
			
		if($OrderByFieldName!="")
			$query .= " order by $OrderByFieldName $OrderType";
		else
			$query .= " order by TaskPeriority asc, EstimatedStartTime desc";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		if($ProjectID!="0" && $ProjectID!="")
			array_push($ValueListArray, $ProjectID); 



		$res = $mysql->ExecuteStatement($ValueListArray);
/*if($_SESSION["UserID"]=='gholami-a'){
		echo $query;
echo "<br>";
echo $CallerPersonID;

}*/

		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTasks();
			$ret[$k]->TaskGroupID=$rec["TaskGroupID"];
			$ret[$k]->TaskGroupName=$rec["TaskGroupName"];
			$ret[$k]->ProgramLevelID=$rec["ProgramLevelID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->ProjectID_Desc=$rec["p1_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$ret[$k]->ProjectTaskTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->PeriodType=$rec["PeriodType"];
			$ret[$k]->PeriodType_Desc=$rec["PeriodType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->CountOfDone=$rec["CountOfDone"];
			$ret[$k]->EstimatedStartTime=$rec["EstimatedStartTime"];
			$ret[$k]->RealStartTime=$rec["RealStartTime"];
			$ret[$k]->EstimatedStartTime_Shamsi=$rec["EstimatedStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->RealStartTime_Shamsi=$rec["RealStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EstimatedRequiredTimeDay=$rec["EstimatedRequiredTimeDay"];
			$ret[$k]->EstimatedRequiredTimeHour=$rec["EstimatedRequiredTimeHour"];
			$ret[$k]->EstimatedRequitedTimeMin=$rec["EstimatedRequitedTimeMin"];
			$ret[$k]->HasExpireTime=$rec["HasExpireTime"];
			$ret[$k]->HasExpireTime_Desc=$rec["HasExpireTime_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ExpireTime=$rec["ExpireTime"];
			$ret[$k]->ExpireTime_Shamsi=$rec["ExpireTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->TaskPeriority=str_pad($rec["TaskPeriority"], 2, '0', STR_PAD_LEFT);
			$ret[$k]->TaskStatus=$rec["TaskStatus"];
			$ret[$k]->TaskStatus_Desc=$rec["TaskStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ParentID=$rec["ParentID"];
			$ret[$k]->DoneDate=$rec["DoneDate"];
			$ret[$k]->DoneDate_Shamsi=$rec["DoneDate_Shamsi"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];
			//$ret[$k]->ParentID_Desc=$rec["p16_title"]; // محاسبه از روی جدول وابسته

			$ret[$k]->CanRemoveByCaller = false;
			// در صورتیکه کاربر مدیر پروژه باشد در هر وضعیتی می تواند کار را حذف کند
			for($i=0; $i<count($ManagedProjects); $i++)
			{
				if($rec["ProjectID"]==$ManagedProjects[$i])
				{
					$ret[$k]->CanRemoveByCaller = true;
					break;
				}
			}
			if($ret[$k]->CanRemoveByCaller==false)
			{
				if($rec["CreatorID"]==$CallerPersonID && $rec["TaskStatus"]=="NOT_START")
				{
					// اگر مدیر پروژه نبود و ایجاد کننده کار بود تنها در صورتی دسترسی حذف دارد که کار اقدام نشده باشد
					if($rec["TaskStatus"]=="NOT_START")
						$ret[$k]->CanRemoveByCaller = true;
				}
			}		
			
			$k++;
		}
		return $ret;
	}
	

	static function GetUserRequestedTasks($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName="", $OrderType="")
	{
		require_once("ProjectMembers.class.php");
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
		$ManagedProjects = manage_ProjectMembers::GetProjectIDsOfManager($CallerPersonID);
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select distinct ProjectTasks.ProjectTaskID
				,ProjectTasks.ProgramLevelID
				,ProjectTasks.ProjectID
				,ProjectTasks.ProjectTaskTypeID
				,ProjectTasks.title
				,ProjectTasks.description
				,ProjectTasks.CreatorID
				,ProjectTasks.PeriodType
				,ProjectTasks.CountOfDone
				,ProjectTasks.EstimatedStartTime
				,ProjectTasks.RealStartTime
				,ProjectTasks.EstimatedRequiredTimeDay
				,ProjectTasks.EstimatedRequiredTimeHour
				,ProjectTasks.EstimatedRequitedTimeMin
				,ProjectTasks.HasExpireTime
				,ProjectTasks.ExpireTime
				,ProjectTasks.TaskPeriority
				,ProjectTasks.TaskStatus
				,ProjectTasks.ParentID
				,ProjectTasks.DoneDate
				,ProjectTasks.CreateDate
				, ProjectTasks.TaskGroupID
				, ProjectTaskGroups.TaskGroupName
			, p1.title  as p1_title 
			, p2.title  as p2_title 
			, concat(g2j(ProjectTasks.CreateDate), ' ', substr(ProjectTasks.CreateDate, 12,10)) as CreateDate_Shamsi
			, concat(g2j(ProjectTasks.DoneDate), ' ', substr(ProjectTasks.DoneDate, 12,10)) as DoneDate_Shamsi
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE ProjectTasks.PeriodType 
				WHEN 'ONCE' THEN 'یکبار' 
				WHEN 'EVERYDAY' THEN 'روزانه' 
				WHEN 'EVERYWEEK' THEN 'هفتگی' 
				WHEN 'EVERYMONTH' THEN 'ماهانه' 
				END as PeriodType_Desc 
			, g2j(ProjectTasks.EstimatedStartTime) as EstimatedStartTime_Shamsi 
			, g2j(ProjectTasks.RealStartTime) as RealStartTime_Shamsi
			, CASE ProjectTasks.HasExpireTime 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasExpireTime_Desc 
			, g2j(ProjectTasks.ExpireTime) as ExpireTime_Shamsi
			, ProjectTasks.TaskPeriority
			, CASE ProjectTasks.TaskStatus 
				WHEN 'NOT_START' THEN 'اقدام نشده' 
				WHEN 'PROGRESSING' THEN 'در دست اقدام' 
				WHEN 'DONE' THEN 'اقدام شده' 
				WHEN 'SUSPENDED' THEN 'معلق' 
				WHEN 'REPLYED' THEN 'پاسخ داده شده'
				WHEN 'READY_FOR_TEST' THEN 'آماده برای کنترل' 
				WHEN 'CONFWAIT' THEN 'منتظرتایید'
				WHEN 'EXECUTECONF' THEN 'تاییدجهت اجرا'
				WHEN 'NOCONF' THEN 'عدم تایید'				
				END as TaskStatus_Desc 
			from 
			(select * from projectmanagement.ProjectTasks "; 
		$cond = "  ProjectTasks.DeleteFlag='NO' and ";
		// کاربر ایجاد کننده کار است
		$cond .= " ProjectTasks.CreatorID=? ";
		if($ProjectID!="0" && $ProjectID!="") 
			$cond .= " and ProjectTasks.ProjectID=? ";
		
		$query .= " where ";
		$query .= $cond;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		else
			$query .= " order by CreateDate DESC ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$query .= "	) as ProjectTasks";
		$query .= " 
			LEFT JOIN projectmanagement.ProjectMembers using (ProjectID) 
			LEFT JOIN projectmanagement.projects as p1 using (ProjectID)
			LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID)
			LEFT JOIN projectmanagement.ProjectTaskGroups  on (ProjectTaskGroupID=TaskGroupID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			  ";

		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID);

		/*if($_SESSION["UserID"]=="mshariati")
		{
			echo $query . "<br />" . print_r($ValueListArray) . "<br />";
			$Rslt = PdoDataAccess::runquery($query, $ValueListArray);
			print_r(PdoDataAccess::PopException());
		}*/

		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTasks();
			$ret[$k]->ProgramLevelID=$rec["ProgramLevelID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->ProjectID_Desc=$rec["p1_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$ret[$k]->ProjectTaskTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->PeriodType=$rec["PeriodType"];
			$ret[$k]->PeriodType_Desc=$rec["PeriodType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->CountOfDone=$rec["CountOfDone"];
			$ret[$k]->EstimatedStartTime=$rec["EstimatedStartTime"];
			$ret[$k]->RealStartTime=$rec["RealStartTime"];
			$ret[$k]->EstimatedStartTime_Shamsi=$rec["EstimatedStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->RealStartTime_Shamsi=$rec["RealStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EstimatedRequiredTimeDay=$rec["EstimatedRequiredTimeDay"];
			$ret[$k]->EstimatedRequiredTimeHour=$rec["EstimatedRequiredTimeHour"];
			$ret[$k]->EstimatedRequitedTimeMin=$rec["EstimatedRequitedTimeMin"];
			$ret[$k]->HasExpireTime=$rec["HasExpireTime"];
			$ret[$k]->HasExpireTime_Desc=$rec["HasExpireTime_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ExpireTime=$rec["ExpireTime"];
			$ret[$k]->ExpireTime_Shamsi=$rec["ExpireTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->TaskPeriority=$rec["TaskPeriority"];
			$ret[$k]->TaskStatus=str_pad($rec["TaskPeriority"], 2, '0', STR_PAD_LEFT);
			$ret[$k]->TaskStatus_Desc=$rec["TaskStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ParentID=$rec["ParentID"];
			$ret[$k]->DoneDate=$rec["DoneDate"];
			$ret[$k]->DoneDate_Shamsi=$rec["DoneDate_Shamsi"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];
			//$ret[$k]->ParentID_Desc=$rec["p16_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->CanRemoveByCaller = false;
			// در صورتیکه کاربر مدیر پروژه باشد در هر وضعیتی می تواند کار را حذف کند
			for($i=0; $i<count($ManagedProjects); $i++)
			{
				if($rec["ProjectID"]==$ManagedProjects[$i])
				{
					$ret[$k]->CanRemoveByCaller = true;
					break;
				}
			}
			if($ret[$k]->CanRemoveByCaller==false)
			{
				if($rec["CreatorID"]==$CallerPersonID && $rec["TaskStatus"]=="NOT_START")
				{
					// اگر مدیر پروژه نبود و ایجاد کننده کار بود تنها در صورتی دسترسی حذف دارد که کار اقدام نشده باشد
					if($rec["TaskStatus"]=="NOT_START")
						$ret[$k]->CanRemoveByCaller = true;
				}
			}		
			
			$k++;
		}
		return $ret;
	}

	static function GetReferredTasks($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName = "", $OrderType = "")
	{
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
		$mysql = pdodb::getInstance();

		$k=0;
		$ret = array();
		$query = "select distinct ProjectTasks.ProjectTaskID
				, ProjectTasks.ProgramLevelID
				, ProjectTasks.ProjectID
				, ProjectTasks.ProjectTaskTypeID
				, ProjectTasks.title
				, ProjectTasks.description
				, ProjectTasks.CreatorID
				, ProjectTasks.PeriodType
				, ProjectTasks.CountOfDone
				, ProjectTasks.EstimatedStartTime
				, ProjectTasks.RealStartTime
				, ProjectTasks.EstimatedRequiredTimeDay
				, ProjectTasks.EstimatedRequiredTimeHour
				, ProjectTasks.EstimatedRequitedTimeMin
				, ProjectTasks.HasExpireTime
				, ProjectTasks.ExpireTime
				, ProjectTasks.TaskPeriority
				, ProjectTasks.TaskStatus
				, ProjectTasks.ParentID
				, ProjectTasks.DoneDate
				, ProjectTasks.CreateDate
			, p1.title as p1_title 
			, p2.title as p2_title 
			, TR.ReferTime as ReferTime
			, TR.ReferTimeC as ReferTimeC
			, concat(g2j(ProjectTasks.CreateDate), ' ', substr(ProjectTasks.CreateDate, 12,10)) as CreateDate_Shamsi
			, concat(g2j(ProjectTasks.DoneDate), ' ', substr(ProjectTasks.DoneDate, 12,10)) as DoneDate_Shamsi
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE ProjectTasks.PeriodType 
				WHEN 'ONCE' THEN 'یکبار' 
				WHEN 'EVERYDAY' THEN 'روزانه' 
				WHEN 'EVERYWEEK' THEN 'هفتگی' 
				WHEN 'EVERYMONTH' THEN 'ماهانه' 
				END as PeriodType_Desc 
			, g2j(ProjectTasks.EstimatedStartTime) as EstimatedStartTime_Shamsi 
			, g2j(ProjectTasks.RealStartTime) as RealStartTime_Shamsi
			, CASE ProjectTasks.HasExpireTime 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasExpireTime_Desc 
			, g2j(ProjectTasks.ExpireTime) as ExpireTime_Shamsi 
			, ProjectTasks.TaskPeriority
			, CASE ProjectTasks.TaskStatus 
				WHEN 'NOT_START' THEN 'اقدام نشده' 
				WHEN 'PROGRESSING' THEN 'در دست اقدام' 
				WHEN 'DONE' THEN 'اقدام شده' 
				WHEN 'SUSPENDED' THEN 'معلق' 
				WHEN 'REPLYED' THEN 'پاسخ داده شده'
				WHEN 'READY_FOR_TEST' THEN 'آماده برای کنترل' 
				WHEN 'CONFWAIT' THEN 'منتظر تایید'
				WHEN 'EXECUTECONF' THEN 'تایید جهت اجرا'
				WHEN 'NOCONF' THEN 'عدم تایید'				
				END as TaskStatus_Desc 
			, ToPerson
			, concat(persons6.pfname, ' ', persons6.plname) as ToPersonName
			, ToPersonWUID
			from 
			(select * from projectmanagement.ProjectTasks ";
		$cond = " ProjectTasks.DeleteFlag='NO' and ProjectTaskID in (select distinct TaskID from projectmanagement.ProjectTaskRefers where FromPerson = ? or ToPerson = ?)";
		if ((int) $ProjectID > 0) $cond .= " and ProjectID = ? ";
		
		$query .= " where ";
		$query .= $cond;
		$query .= ") as ProjectTasks";
		$query .= " 
			LEFT JOIN projectmanagement.projects as p1 using (ProjectID)
			LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID)
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
 LEFT JOIN (SELECT ReferID, TaskID, ToPerson, DateTime as ReferTimeC,concat(g2j(DateTime), ' ', substr(DateTime, 12,10)) as ReferTime, WebUserID as ToPersonWUID FROM projectmanagement.ProjectTaskRefers LEFT JOIN projectmanagement.AccountSpecs on (ToPerson = PersonID)) TR on (ProjectTasks.ProjectTaskID = TR.TaskID)
LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=TR.ToPerson) 
			  where ReferID in (SELECT ptr1.ReferID FROM projectmanagement.ProjectTaskRefers ptr1 LEFT JOIN projectmanagement.ProjectTaskRefers ptr2
 ON (ptr1.TaskID = ptr2.TaskID AND ptr1.DateTime < ptr2.DateTime)
WHERE ptr2.DateTime IS NULL)";
		if($OrderByFieldName != "")
			$query .= " order by " . $OrderByFieldName . " " . $OrderType;
		else
			$query .= " order by CreateDate DESC ";
		$query .= " limit " . $FromRec . ", " . $NumberOfRec;

		//if($_SESSION["UserID"]=="mshariati") echo $query . "<br />";

		$ValueListArray = array();
		array_push($ValueListArray, $CallerPersonID);
		array_push($ValueListArray, $CallerPersonID);
		if ((int) $ProjectID > 0) array_push($ValueListArray, $ProjectID); 
		//array_push($ValueListArray, $_SESSION["UserID"]);

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement($ValueListArray);

		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTasks();
			$ret[$k]->ProgramLevelID=$rec["ProgramLevelID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->ProjectID_Desc=$rec["p1_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$ret[$k]->ProjectTaskTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->PeriodType=$rec["PeriodType"];
			$ret[$k]->PeriodType_Desc=$rec["PeriodType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->CountOfDone=$rec["CountOfDone"];
			$ret[$k]->EstimatedStartTime=$rec["EstimatedStartTime"];
			$ret[$k]->RealStartTime=$rec["RealStartTime"];
			$ret[$k]->EstimatedStartTime_Shamsi=$rec["EstimatedStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->RealStartTime_Shamsi=$rec["RealStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EstimatedRequiredTimeDay=$rec["EstimatedRequiredTimeDay"];
			$ret[$k]->EstimatedRequiredTimeHour=$rec["EstimatedRequiredTimeHour"];
			$ret[$k]->EstimatedRequitedTimeMin=$rec["EstimatedRequitedTimeMin"];
			$ret[$k]->HasExpireTime=$rec["HasExpireTime"];
			$ret[$k]->HasExpireTime_Desc=$rec["HasExpireTime_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ExpireTime=$rec["ExpireTime"];
			$ret[$k]->ExpireTime_Shamsi=$rec["ExpireTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->TaskPeriority=str_pad($rec["TaskPeriority"], 2, '0', STR_PAD_LEFT);
			$ret[$k]->TaskStatus=$rec["TaskStatus"];
			$ret[$k]->TaskStatus_Desc=$rec["TaskStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ParentID=$rec["ParentID"];
			$ret[$k]->DoneDate=$rec["DoneDate"];
			$ret[$k]->DoneDate_Shamsi=$rec["DoneDate_Shamsi"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];
			//$ret[$k]->ParentID_Desc=$rec["p16_title"]; // محاسبه از روی جدول وابسته
	
			$ret[$k]->ReferTime=$rec["ReferTime"];
			$ret[$k]->ReferTimeC=$rec["ReferTimeC"];
			$ret[$k]->ToPerson=$rec["ToPerson"];
			$ret[$k]->ToPersonName=$rec["ToPersonName"];
			$ret[$k]->ToPersonWUID=$rec["ToPersonWUID"];

			$k++;
		}
		return $ret;
	}

	static function IsReferredTaskVisited($TaskID, $UserID, $ReferDate)
	{
		return true;
		$MySQL = pdodb::getInstance();
		$Stmt = $MySQL->Prepare("SELECT if(count(*) = 0 or LastVisit < :ReferDate, 'NO', 'YES') as Visited FROM projectmanagement.UserPageLastVisits where UserID = :UserID and PageID = (select PageID from projectmanagement.PagesInfo where PageRelativePath = concat('NewProjectTasks.php?UpdateID=', :TaskID));");
		$Stmt->Execute(['UserID' => $UserID, 'ReferDate' => $ReferDate, 'TaskID' => $TaskID]);
		return $Stmt->fetch(PDO::FETCH_ASSOC)["Visited"];
	}

	static function GetReferredTasksCount($ProjectID)
	{
		$CallerPersonID = $_SESSION["PersonID"];

		$mysql = pdodb::getInstance();

		$mysql->Prepare("select count(distinct ProjectTaskID) as C from (select * from projectmanagement.ProjectTasks where ProjectTasks.DeleteFlag='NO' " . (((int) $ProjectID > 0)? "and ProjectID = :ProjectID " : "") . "and ProjectTaskID in (select distinct TaskID from projectmanagement.ProjectTaskRefers where FromPerson = :CallerPersonID or ToPerson = :CallerPersonID)) as ProjectTasks LEFT JOIN projectmanagement.projects as p1 using (ProjectID) LEFT JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID) LEFT JOIN projectmanagement.ProjectTaskTypes p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID) LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID);");

		$Params["CallerPersonID"] = $CallerPersonID;
		if((int) $ProjectID > 0) $Params["ProjectID"] = $ProjectID;
		$Res = $mysql->ExecuteStatement($Params);

		return ($Res->fetch()["C"]);
	}
	
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $ProjectID: پروژه مربوطه
	* @param $ProjectTaskTypeID: نوع کار
	* @param $title: عنوان
	* @param $description: شرح
	* @param $PeriodType: پریود انجام
	* @param $CountOfDone: تعداد دفعات انجام
	* @param $EstimatedStartTime: زمان تخمینی شروع
	* @param $RealStartTime: زمان واقعی شروع
	* @param $EstimatedRequiredTimeDay: زمان مورد نیاز - روز
	* @param $EstimatedRequiredTimeHour: زمان مورد نیاز - ساعت
	* @param $EstimatedRequitedTimeMin: زمان مورد نیاز - دقیقه
	* @param $HasExpireTime: مهلت اقدام دارد؟
	* @param $ExpireTime: مهلت اقدام
	* @param $TaskPeriority: اولویت
	* @param $TaskStatus: وضعیت
	* @param $ParentID: کار بالاتر
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $ProjectID, $ProjectTaskTypeID, $title, $description, $PeriodType, $CountOfDone, $EstimatedStartTime, $RealStartTime, $EstimatedRequiredTimeDay, $EstimatedRequiredTimeHour, $EstimatedRequitedTimeMin, $HasExpireTime, $ExpireTime, $TaskPeriority, $TaskStatus, $ParentID, $UpdateReason)
	{
		$ret = "";
		$obj = new be_ProjectTasks();
		$obj->LoadDataFromDatabase($CurRecID);
		if($ProjectID!=$obj->ProjectID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "پروژه مربوطه";
		}
		if($ProjectTaskTypeID!=$obj->ProjectTaskTypeID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نوع کار";
		}
		if($title!=$obj->title)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($description!=$obj->description)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "شرح";
		}
		if($PeriodType!=$obj->PeriodType)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "پریود انجام";
		}
		if($CountOfDone!=$obj->CountOfDone)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "تعداد دفعات انجام";
		}
		if($EstimatedStartTime." 00:00:00"!=$obj->EstimatedStartTime)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "زمان تخمینی شروع";
			//$ret .= "(".$EstimatedStartTime.") (".$obj->EstimatedStartTime.")";
		}
		if($RealStartTime." 00:00:00"!=$obj->RealStartTime)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "زمان واقعی شروع";
			//$ret .= "(".$EstimatedStartTime.") (".$obj->EstimatedStartTime.")";
		}
		if($EstimatedRequiredTimeDay!=$obj->EstimatedRequiredTimeDay)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "زمان مورد نیاز - روز";
		}
		if($EstimatedRequiredTimeHour!=$obj->EstimatedRequiredTimeHour)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "زمان مورد نیاز - ساعت";
		}
		if($EstimatedRequitedTimeMin!=$obj->EstimatedRequitedTimeMin)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "زمان مورد نیاز - دقیقه";
		}
		if($HasExpireTime!=$obj->HasExpireTime)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "مهلت اقدام دارد؟";
		}
		if($ExpireTime." 00:00:00"!=$obj->ExpireTime)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "مهلت اقدام";
		}
		if($TaskPeriority!=$obj->TaskPeriority)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "اولویت";
		}
		if($TaskStatus!=$obj->TaskStatus)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "وضعیت";
		}
		if($ParentID!=$obj->ParentID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "کار بالاتر";
		}
		if($UpdateReason!=$obj->UpdateReason)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "";
		}
		return $ret;
	}

	static function GetWebUserID($PersonID)
	{
		$MySQL = pdodb::getInstance();

		$Stmt = $MySQL->Prepare("select WebUserID from framework.AccountSpecs where PersonID = ?;");
		$Stmt->Execute([$PersonID]);

		return ($Stmt->rowCount() > 0)? $Stmt->fetch()[0] : 0;
	}

	static function GetVisited($PersonID, $PageID, $ReferDateTime)
	{
		return 0;
		$WUID = manage_ProjectTasks::GetWebUserID($PersonID);
		$MySQL = pdodb::getInstance();

		$Stmt = $MySQL->Prepare("SELECT if(count(*) = 0 or LastVisit < ?, 'خیر', 'بله') as Visited FROM projectmanagement.UserPageLastVisits where UserID = ? and PageID = ?;");
		$Stmt->Execute([$ReferDateTime, $WUID, $PageID]);

		return ($Stmt->rowCount() > 0)? $Stmt->fetch()[0] : "نامشخص";
	}
	
	static function CreateKartableHeader($CurrentPageName)
	{
		$ret = "<table class=\"text-center  table table-info table-bordered\">";
	        $ret .= "<tr>";
	        /*
        	$ret .= "<td width=\"13%\" ";
        	if($CurrentPageName=="TasksMessages")
			$ret .= "bgcolor=\"#cccccc\" ";
			$ret .= "><a href='TasksMessages.php'>پیام‌های سیستمی</a></td>";
	        */
        	$ret .= "<td ";

			if($CurrentPageName=="TasksKartable"){
				$ret .= "class=\"bg-info\" ";
				$ret .= "><a href='TasksKartable.php' class=\"text-light\">".C_CURRENT_TASKS."</a></td>";
				
			}else{
				$ret .= "><a href='TasksKartable.php' class=\"text-primary\">".C_CURRENT_TASKS."</a></td>";
			}
		/*
        $ret .= "<td width=\"13%\" ";
        if($CurrentPageName=="ReferredTasks")
             $ret .= " bgcolor=\"#cccccc\" ";

	$NewRefers = '';
	$NotVisited = 0;

        $ret .= "><a href='ReferredTasks.php'>کارهای ارجاع شده " . $NewRefers . "</a></td>";
		*/
        $ret .= "<td ";
        if($CurrentPageName=="LastCreatedTasks"){
			$ret .= "class=\"bg-info\" ";
			$ret .= "><a href='LastCreatedTasks.php' class=\"text-light\">".C_CREATED_TASKS."</a></td>";
		}
        $ret .= "><a href='LastCreatedTasks.php' class=\"text-primary\">".C_CREATED_TASKS."</a></td>";

        $ret .= "<td ";
        if($CurrentPageName=="LastDoneTasks"){
        	$ret .= "class=\"bg-info\" ";
			$ret .= "><a href='LastDoneTasks.php' class=\"text-light\">".C_DONE_TASKS."</a></td>";
		}
        $ret .= "><a href='LastDoneTasks.php' class=\"text-primary\">".C_DONE_TASKS."</a></td>";

        $ret .= "<td ";
        if($CurrentPageName=="TasksForControl"){
			$ret .= "class=\"bg-info\" ";
			$ret .= "><a href='TasksForControl.php' class=\"text-light\"> ".C_TASKS_IN_NEED_OF_CONTROL."</a></td>";
		}
        $ret .= "><a href='TasksForControl.php' class=\"text-primary\"> ".C_TASKS_IN_NEED_OF_CONTROL."</a></td>";
        // $ret .= C_TASKS_IN_NEED_OF_CONTROL;
        $TasksForControlCount = manage_ProjectTasks::GetTasksCountForControl(0);
        if($TasksForControlCount>0)
            $ret .= " (".$TasksForControlCount.")";
        // $ret .= "</a></td>";

        /*
        $ret .= "<td width=\"17%\" ";
        if($CurrentPageName=="ProjectsKartable")
             $ret .= " bgcolor=\"#cccccc\" ";
        $ret .= "><a href='ProjectsKartable.php'>پروژه ها</a></td>";
        */
        $ret .= "<td ";
        if($CurrentPageName=="ShowAllPersonStatus"){
			$ret .= " class=\"bg-info\" ";
			$ret .= "><a href='ShowAllPersonStatus.php' class=\"text-light\">".C_PROJECTS_MEMBERS."</a></td>";

		}
        $ret .= "><a href='ShowAllPersonStatus.php' class=\"text-primary\">".C_PROJECTS_MEMBERS."</a></td>";
        /*
        $ret .= "<td width=\"13%\" ";
        if($CurrentPageName=="ShowLastChanges")
             $ret .= " bgcolor=\"#cccccc\" ";
        $ret .= "><a href='ShowLastChanges.php'>تغییرات کد/جداول</a></td>";
        */
        $ret .= "</tr>";
        $ret .= "</table>";
        return $ret;
		
	}
	
	
	// گرفتن لیست کارهایی که فرد ایجاد کننده یا مجری آنها است
	/**
	* @param $ProjectID: پروژه مربوطه
	* @param $PersonID: ایجاد کننده یا مجری
	* @param $PersonRole: نوع فرد که مجری باشد یا ایجاد کننده
	* @param $TaskStatus: وضعیت
	* @return لیست داده های حاصل جستجو */
	 
	static function GetTasksOfPerson($ProjectID, $PersonID, $PersonRole, $TaskStatus, $FromRec, $NumberOfRec, $OrderByFieldName="", $OrderType="", $ShowAll = true)
	{
		require_once("ProjectMembers.class.php");
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		$CallerPersonID = $_SESSION["PersonID"]; // شخصی که این متد را اجرا کرده است. کل داده ها باید بر اساس سطح دسترسی این فرد فیلتر شود
		$ManagedProjects = manage_ProjectMembers::GetProjectIDsOfManager($CallerPersonID);
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTasks.ProjectTaskID
				,ProjectTasks.ProgramLevelID
				,ProjectTasks.ProjectID
				,ProjectTasks.ProjectTaskTypeID
				,ProjectTasks.title
				,ProjectTasks.description
				,ProjectTasks.CreatorID
				,ProjectTasks.PeriodType
				,ProjectTasks.CountOfDone
				,ProjectTasks.EstimatedStartTime
				,ProjectTasks.RealStartTime
				,ProjectTasks.EstimatedRequiredTimeDay
				,ProjectTasks.EstimatedRequiredTimeHour
				,ProjectTasks.EstimatedRequitedTimeMin
				,ProjectTasks.HasExpireTime
				,ProjectTasks.ExpireTime
				,ProjectTasks.TaskPeriority
				,ProjectTasks.TaskStatus
				,ProjectTasks.ParentID
				,ProjectTasks.DoneDate
				,ProjectTasks.CreateDate
				,ProjectTasks.TaskGroupID
				,ProjectTaskGroups.TaskGroupName
				,ProjectTasks.StartTime
                                ,ProjectTasks.EndTime
                                ,CASE WHEN Acs.LastActivityDate IS NULL THEN 'بدون اقدام' ELSE g2j(Acs.LastActivityDate) END as LastActivityDate
			, p1.title  as p1_title 
			, p2.title  as p2_title 
			, concat(g2j(ProjectTasks.CreateDate), ' ', substr(ProjectTasks.CreateDate, 12,10)) as CreateDate_Shamsi
			, concat(g2j(ProjectTasks.DoneDate), ' ', substr(ProjectTasks.DoneDate, 12,10)) as DoneDate_Shamsi
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE ProjectTasks.PeriodType 
				WHEN 'ONCE' THEN 'یکبار' 
				WHEN 'EVERYDAY' THEN 'روزانه' 
				WHEN 'EVERYWEEK' THEN 'هفتگی' 
				WHEN 'EVERYMONTH' THEN 'ماهانه' 
				END as PeriodType_Desc 
			, g2j(ProjectTasks.EstimatedStartTime) as EstimatedStartTime_Shamsi 
                        ,g2j (substr(ProjectTasks.RealStartTime, 1,10)) as RealStartTime_Shamsi
			, CASE ProjectTasks.HasExpireTime 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasExpireTime_Desc 
			, g2j(ProjectTasks.ExpireTime) as ExpireTime_Shamsi 
			, ProjectTasks.TaskPeriority
			, CASE ProjectTasks.TaskStatus 
				WHEN 'NOT_START' THEN 'اقدام نشده' 
				WHEN 'PROGRESSING' THEN 'در دست اقدام' 
				WHEN 'DONE' THEN 'اقدام شده' 
				WHEN 'SUSPENDED' THEN 'معلق' 
				WHEN 'REPLYED' THEN 'پاسخ داده شده'
				WHEN 'READY_FOR_TEST' THEN 'آماده برای کنترل' 
				WHEN 'CONFWAIT' THEN 'منتظرتایید'
				WHEN 'EXECUTECONF' THEN 'تاییدجهت اجرا'
				WHEN 'NOCONF' THEN 'عدم تایید'				
				END as TaskStatus_Desc 
			from 
			projectmanagement.ProjectTasks ";
		if($PersonRole=="EXECUTOR")
		$query .= " JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)";
		
		$query .= "  
			LEFT JOIN projectmanagement.projects  p1 on (p1.ProjectID=ProjectTasks.ProjectID) 
			LEFT JOIN projectmanagement.ProjectTaskTypes  p2 on (p2.ProjectTaskTypeID=ProjectTasks.ProjectTaskTypeID)
			LEFT JOIN projectmanagement.ProjectTaskGroups  on (ProjectTaskGroupID=TaskGroupID)
			LEFT JOIN (SELECT ProjectTaskID, substr(max(ActivityDate), 1, 11) as LastActivityDate FROM projectmanagement.ProjectTaskActivities group by ProjectTaskID) Acs on (projectmanagement.ProjectTasks.ProjectTaskID = Acs.ProjectTaskID)
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTasks.CreatorID) 
			  ";
		$cond = "  ProjectTasks.DeleteFlag='NO' and  TaskStatus!='CONFWAIT' and TaskStatus!='EXECUTECONF' and TaskStatus!='NOCONF' and ";
		if($PersonRole=="EXECUTOR")
			$cond .= " ProjectTaskAssignedUsers.PersonID=? ";
		else
			$cond .= " ProjectTasks.CreatorID=? ";
		
		if($ProjectID!="0" && $ProjectID!="") 
			$cond .= " and ProjectTasks.ProjectID=? ";
		if($TaskStatus!="0" && $TaskStatus!="") 
			$cond .= " and ProjectTasks.TaskStatus=? ";

		if ($ShowAll == false) $cond .= " and ProjectTasks.CreateDate between date_sub(now(), interval 2 month) and now() ";
		
		$query .= " where ";
		$query .= $cond;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		/*if($_SESSION["PersonID"]=="401371457")
			echo $query;*/
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID); 
		if($TaskStatus!="0" && $TaskStatus!="") 
			array_push($ValueListArray, $TaskStatus);
			
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTasks();
			$ret[$k]->TaskGroupID=$rec["TaskGroupID"];
			$ret[$k]->TaskGroupName=$rec["TaskGroupName"];
			$ret[$k]->ProgramLevelID=$rec["ProgramLevelID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->ProjectID_Desc=$rec["p1_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$ret[$k]->ProjectTaskTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->title=$rec["title"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->PeriodType=$rec["PeriodType"];
			$ret[$k]->PeriodType_Desc=$rec["PeriodType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->CountOfDone=$rec["CountOfDone"];
			$ret[$k]->EstimatedStartTime=$rec["EstimatedStartTime"];
			$ret[$k]->RealStartTime=$rec["RealStartTime"];
			$ret[$k]->EstimatedStartTime_Shamsi=$rec["EstimatedStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->RealStartTime_Shamsi=$rec["RealStartTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EstimatedRequiredTimeDay=$rec["EstimatedRequiredTimeDay"];
			$ret[$k]->EstimatedRequiredTimeHour=$rec["EstimatedRequiredTimeHour"];
			$ret[$k]->EstimatedRequitedTimeMin=$rec["EstimatedRequitedTimeMin"];
			$ret[$k]->HasExpireTime=$rec["HasExpireTime"];
			$ret[$k]->HasExpireTime_Desc=$rec["HasExpireTime_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ExpireTime=$rec["ExpireTime"];
			$ret[$k]->ExpireTime_Shamsi=$rec["ExpireTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->TaskPeriority=str_pad($rec["TaskPeriority"], 2, '0', STR_PAD_LEFT);
			$ret[$k]->TaskStatus=$rec["TaskStatus"];
			$ret[$k]->TaskStatus_Desc=$rec["TaskStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ParentID=$rec["ParentID"];
			$ret[$k]->DoneDate=$rec["DoneDate"];
			$ret[$k]->DoneDate_Shamsi=$rec["DoneDate_Shamsi"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];
			$ret[$k]->StartTime=$rec["StartTime"];
                        $ret[$k]->EndTime=$rec["EndTime"];
			//$ret[$k]->ParentID_Desc=$rec["p16_title"]; // محاسبه از روی جدول وابسته

			$ret[$k]->LastActivityTime = $rec["LastActivityDate"];

			$ret[$k]->CanRemoveByCaller = false;
			// در صورتیکه کاربر مدیر پروژه باشد در هر وضعیتی می تواند کار را حذف کند
			for($i=0; $i<count($ManagedProjects); $i++)
			{
				if($rec["ProjectID"]==$ManagedProjects[$i])
				{
					$ret[$k]->CanRemoveByCaller = true;
					break;
				}
			}
			if($ret[$k]->CanRemoveByCaller==false)
			{
				if($rec["CreatorID"]==$CallerPersonID && $rec["TaskStatus"]=="NOT_START")
				{
					// اگر مدیر پروژه نبود و ایجاد کننده کار بود تنها در صورتی دسترسی حذف دارد که کار اقدام نشده باشد
					if($rec["TaskStatus"]=="NOT_START")
						$ret[$k]->CanRemoveByCaller = true;
				}
			}		
			$k++;
		}
		//if($_SESSION["PersonID"]=="201309")
		//	echo $query;
		return $ret;
	}

	static function GetTasksOfPersonCount($ProjectID, $PersonID, $PersonRole, $TaskStatus, $ShowAll = true)
	{
		require_once("ProjectMembers.class.php");
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(*) as TotalCount 
			from 
			projectmanagement.ProjectTasks ";
		if($PersonRole=="EXECUTOR")
		$query .= " JOIN projectmanagement.ProjectTaskAssignedUsers using (ProjectTaskID)";
		$cond = "  ProjectTasks.DeleteFlag='NO' and ";
		if($PersonRole=="EXECUTOR")
			$cond .= " ProjectTaskAssignedUsers.PersonID=? ";
		else
			$cond .= " ProjectTasks.CreatorID=? ";
		
		if($ProjectID!="0" && $ProjectID!="") 
			$cond .= " and ProjectTasks.ProjectID=? ";
		if($TaskStatus!="0" && $TaskStatus!="") 
			$cond .= " and ProjectTasks.TaskStatus=? ";

		if ($ShowAll == false) $cond .= " and ProjectTasks.CreateDate between date_sub(now(), interval 2 month) and now() ";
		
		$query .= " where ";
		$query .= $cond;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID);
		if($ProjectID!="0" && $ProjectID!="") 
			array_push($ValueListArray, $ProjectID); 
		if($TaskStatus!="0" && $TaskStatus!="") 
			array_push($ValueListArray, $TaskStatus);
			
		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	
	static function GetTaskProgressPercentAndUsedTime($ProjectTaskID)
	{
		$ret = array();
		$ret["TotalProgress"] = $ret["TotalTime"] = 0;  
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select sum(ProgressPercent) as TotalProgress, sum(ActivityLength) as TotalTime  
			from 
			projectmanagement.ProjectTaskActivities 
			 where ProjectTaskID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectTaskID));
		
		if($rec = $res->fetch())
		{
			$ret["TotalProgress"] = $rec["TotalProgress"];
			$ret["TotalTime"] = $rec["TotalTime"];
		}	

		return $ret;
	}
	
	static function GetRelatedProgramLevels($ProjectID, $CurValue=0)
	{
		$mysql = pdodb::getInstance();
		$ret = "";
		$query = "select distinct ProgramLevels.ProgramLevelID, concat(programs.title, ' - ', ProgramLevels.title) as LevelFullName  
			from 
			projectmanagement.programs 
			JOIN projectmanagement.ProgramLevels on (programs.ProgramID=ProgramLevels.ProgramID)
			JOIN projectmanagement.ProgramLevelProjects on (ProgramLevels.ProgramLevelID=ProgramLevelProjects.ProgramLevelID) 
			 where ProjectID=? order by programs.OrderNo, ProgramLevels.LevelNo";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		while($rec = $res->fetch())
		{
			$ret .= "<option value='".$rec["ProgramLevelID"]."' ";
			if($CurValue==$rec["ProgramLevelID"])
				$ret .= " selected ";
			$ret .= ">".$rec["LevelFullName"];
		}
		return $ret;
	}

	// returns the number of new state changes on bug report tasks.
	static function GetNumberOfResponses()
	{
		return 0;
		$A = pdodb::getInstance();

		$query = "select ProjectTaskID from projectmanagement.SentBugs LEFT JOIN projectmanagement.ProjectTasks using (ProjectTaskID) where UserID = :UserID;";
		$A->Prepare($query);
		$Rslt = $A->ExecuteStatement(["UserID" => $_SESSION["UserID"]]);
		$Reports = $Rslt->fetchAll(PDO::FETCH_ASSOC);
	
		$query = "SELECT LastVisit FROM projectmanagement.UserPageLastVisits where UserID = :UserID and PageID = 2;";
		$A->Prepare($query);
		$Rslt = $A->ExecuteStatement(["UserID" => $_SESSION["UserID"]]);
		$LastVisit = $Rslt->fetch()["LastVisit"];
	
		$EventCount = 0;
		$query = "select count(*) as Status from (
			(SELECT BeforeState FROM projectmanagement.ProjectTaskStatusChanges where DateTime > :LastVisit and TaskID = :TaskID order by DateTime asc limit 1)
			union
			(SELECT NewState FROM projectmanagement.ProjectTaskStatusChanges where DateTime > :LastVisit and TaskID = :TaskID order by DateTime desc limit 1)
			) as t";
		$A->Prepare($query);
		for ($i = 0; $i < count($Reports); $i++)
		{
			$Rslt = $A->ExecuteStatement(["LastVisit" => $LastVisit, "TaskID" => $Reports[$i]["ProjectTaskID"]]);
			if ($Rslt->fetch()["Status"] == 2) $EventCount++;
		}

		return $EventCount;
	}


	//آخرین تغییرات داده شده روی کارهایی که شخص ایجاد کرده یا مجری آن است را در چند روز اخیر نشان می دهد
	// پارامتر دوم اگر درست باشد یعنی کارهایی که دیگران انجام داده اند نشان دهد و اگر نادرست باشد یعنی کارهای خود فرد نشان داده شود
	static function GetLastSystemMessage($PersonID, $OnlyOthers = TRUE, $Full = FALSE)
	{
		$mysql = pdodb::getInstance();
		$ret = "";
		$query = "SELECT ProjectTaskHistory.PersonID, pfname, plname, ChangedPart, ActionType, concat(g2j(ActionTime), ' ', substr(ActionTime, 11, 8)) as ActionTime, ProjectTasks.ProjectTaskID, title FROM projectmanagement.ProjectTasks 
					JOIN projectmanagement.ProjectTaskHistory on (ProjectTaskHistory.ProjectTaskID=ProjectTasks.ProjectTaskID)  
					JOIN projectmanagement.ProjectTaskAssignedUsers on (ProjectTaskAssignedUsers.ProjectTaskID=ProjectTasks.ProjectTaskID)
					JOIN projectmanagement.persons on (ProjectTaskHistory.PersonID=persons.PersonID)
					where ProjectTasks.DeleteFlag = 'NO' and (ProjectTaskAssignedUsers.PersonID=? or ProjectTasks.CreatorID=?) and ActionTime between date_sub(now(), interval 12 day) and now() ";
		if($OnlyOthers==TRUE)
			$query .="	and ProjectTaskHistory.PersonID<>? ";
		else
			$query .="	and ProjectTaskHistory.PersonID=? ";
		if($Full == False)
			$query .="	and ProjectTaskHistory.ActionType in ('ADD', 'UPDATE') ";
		$query .= " order by ActionTime DESC";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID,$PersonID,$PersonID));
		$recs = $res->fetchAll();

		$RowNo = 1;
		for ($i = 0; $i < count($recs); $i++)
		{
			$rec = $recs[$i];
			$Parts = null;
			$Operations = null;

			$Parts[0] = $rec["ChangedPart"];
			$Operations[0] = $rec["ActionType"];

			// Merging
//if($_SESSION["PersonID"] == 401371457)
//{
			if($Full == False)
				for ($j = $i + 1; $j < count($recs); $j++)
					if ($recs[$j]["ProjectTaskID"] == $rec["ProjectTaskID"] && ($OnlyOthers == FALSE || $recs[$j]["PersonID"] == $rec["PersonID"]))
					{
						if (!($Parts[count($Parts) - 1] == 'MAIN_TASK' && $Operations[count($Parts) - 1] == 'UPDATE' && $recs[$j]["ChangedPart"] == 'MAIN_TASK' && $recs[$j]["ActionType"] == 'UPDATE'))
						{
							$Parts[count($Parts)] = $recs[$j]["ChangedPart"];
							$Operations[count($Parts) - 1] = $recs[$j]["ActionType"];
							$rec["ActionTime"] = $recs[$j]["ActionTime"];
						}
						$i = $j;
					}
					else
						break;
//}
			// Merging end

			if($RowNo++ % 2 == 1)
				$ret .= "<tr class=OddRow>";
			else
				$ret .= "<tr class=EvenRow>";

			$ret .= "<td nowrap>".$rec["ActionTime"]."</td>";
			$ret .= "<td>";

			for ($j = count($Parts) - 1; $j >= 0; $j--)
			{
				$ObjName = "";
				switch ($Parts[$j])
				{
					case "MAIN_TASK":
						$ret .= "<img src='images/task.jpg'>";
						$ObjName = "کار";
					break;
					case "USER":
						$ret .= " <img width=20 src='images/members.gif'> ";
						$ObjName = "مجری";
					break;
					case "COMMENT":
						$ret .= " <img src='images/comment.jpeg'> ";
						$ObjName = "یادداشت";
					break;
					case "DOCUMENT":
						$ret .= " <img src='images/document.jpg'> ";
						$ObjName = "سند";
					break;
					case "ACTIVITY":
						$ret .= " <img src='images/activity.jpg'> ";
						$ObjName = "اقدام";
					break;
				}

				switch ($Operations[$j])
				{
					case "ADD": $ret .= " ایجاد "; break;
					case "UPDATE": $ret .= " به‌روزرسانی "; break;
					case "DELETE": $ret .= " حذف "; break;
				}

				$ret .= $ObjName;
			}

			$ret .= "</td>";
			if($OnlyOthers==TRUE)
				$ret .= "<td> ".$rec["pfname"]." ".$rec["plname"]."</td>";
			$ret .= " <td><a style='text-decoration: none !important;' href='NewProjectTasks.php?UpdateID=".$rec["ProjectTaskID"]."' target='_blank'>" . "&nbsp;" . $rec["title"] . "</a></td>";
			$ret .= "</tr>";
		}

		return $ret;
	}
	
}
?>
