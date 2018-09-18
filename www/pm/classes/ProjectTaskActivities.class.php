<?php
/*
 تعریف کلاسها و متدهای مربوط به : اقدامات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/

/*
کلاس پایه: اقدامات
*/
class be_ProjectTaskActivities
{
	public $ProjectTaskActivityID;		//
	public $ProjectTaskID;		//کار مربوطه
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $ActivityDate;		//تاریخ اقدام
	public $ActivityDate_Shamsi;		/* مقدار شمسی معادل با تاریخ اقدام */
	public $ProjectTaskActivityTypeID;		//نوع اقدام
	public $ProjectTaskActivityTypeID_Desc;		/* شرح مربوط به نوع اقدام */
	public $ActivityLength;		//زمان مصرفی
	public $ProgressPercent;		//درصد پیشرفت
	public $ActivityDescription;		//شرح
	public $FileContent;		//فایل ضمیمه
	public $FileName;		//نام فایل
	public $ChangedTables;		//جداول تغییر داده شده
	public $ChangedPages;		//صفحات تغییر داده شده
	
	public $ProjectID_Desc;

	function be_ProjectTaskActivities() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTaskActivities.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, g2j(ProjectTaskActivities.ActivityDate) as ActivityDate_Shamsi 
			, p4.title  as p4_title from projectmanagement.ProjectTaskActivities 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskActivities.CreatorID) 
			LEFT JOIN projectmanagement.ProjectTaskActivityTypes  p4 on (p4.ProjectTaskActivityTypeID=ProjectTaskActivities.ProjectTaskActivityTypeID)  where  ProjectTaskActivities.ProjectTaskActivityID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectTaskActivityID=$rec["ProjectTaskActivityID"];
			$this->ProjectTaskID=$rec["ProjectTaskID"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$this->ActivityDate=$rec["ActivityDate"];
			$this->ActivityDate_Shamsi=$rec["ActivityDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->ProjectTaskActivityTypeID=$rec["ProjectTaskActivityTypeID"];
			$this->ProjectTaskActivityTypeID_Desc=$rec["p4_title"]; // محاسبه از روی جدول وابسته
			$this->ActivityLength=$rec["ActivityLength"];
			$this->ProgressPercent=$rec["ProgressPercent"];
			$this->ActivityDescription=$rec["ActivityDescription"];
			$this->FileContent=$rec["FileContent"];
			$this->FileName=$rec["FileName"];
			$this->ChangedTables=$rec["ChangedTables"];
			$this->ChangedPages=$rec["ChangedPages"];
		}
	}
}
/*
کلاس مدیریت اقدامات
*/
class manage_ProjectTaskActivities
{
	static function GetCount($ProjectTaskID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(ProjectTaskActivityID) as TotalCount from projectmanagement.ProjectTaskActivities";
			$query .= " where ProjectTaskID='".$ProjectTaskID."'";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	static function GetLastID()
	{
		$mysql = dbclass::getInstance();
		$query = "select max(ProjectTaskActivityID) as MaxID from projectmanagement.ProjectTaskActivities";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectTaskID: کار مربوطه
	* @param $ActivityDate: تاریخ اقدام
	* @param $ProjectTaskActivityTypeID: نوع اقدام
	* @param $ActivityLength: زمان مصرفی
	* @param $ProgressPercent: درصد پیشرفت
	* @param $ActivityDescription: شرح
	* @param $FileContent: فایل ضمیمه
	* @param $FileName: نام فایل
	* @param $ChangedTables: جداول تغییر داده شده
	* @param $ChangedPages: صفحات تغییر داده شده
	* @return کد داده اضافه شده	*/
	static function Add($ProjectTaskID, $ActivityDate, $ProjectTaskActivityTypeID, $ActivityLength, $ProgressPercent, $ActivityDescription, $FileContent, $FileName, $ChangedTables, $ChangedPages)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectTaskActivities (";
		$query .= " ProjectTaskID";
		$query .= ", CreatorID";
		$query .= ", ActivityDate";
		$query .= ", ProjectTaskActivityTypeID";
		$query .= ", ActivityLength";
		$query .= ", ProgressPercent";
		$query .= ", ActivityDescription";
		$query .= ", FileName";
		$query .= ", ChangedTables";
		$query .= ", ChangedPages";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , ? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectTaskID); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		array_push($ValueListArray, $ActivityDate); 
		array_push($ValueListArray, $ProjectTaskActivityTypeID); 
		array_push($ValueListArray, $ActivityLength); 
		array_push($ValueListArray, $ProgressPercent); 
		array_push($ValueListArray, $ActivityDescription); 
		array_push($ValueListArray, $FileName); 
		array_push($ValueListArray, $ChangedTables); 
		array_push($ValueListArray, $ChangedPages); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectTaskActivities::GetLastID();
		
		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$mysql = dbclass::getInstance();
			$query = "update projectmanagement.ProjectTaskActivities set ";
			$query .= " FileContent='".$FileContent."' ";
			$query .= " where ProjectTaskActivityID='".$LastID."'";
			$mysql->Execute($query);
 		}
		
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($ProjectTaskID, "", "ACTIVITY", $LastID, "ADD");
		//$mysql->audit("ثبت داده جدید در اقدامات با کد ".$LastID);

		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $ActivityDate: تاریخ اقدام
	* @param $ProjectTaskActivityTypeID: نوع اقدام
	* @param $ActivityLength: زمان مصرفی
	* @param $ProgressPercent: درصد پیشرفت
	* @param $ActivityDescription: شرح
	* @param $FileContent: فایل ضمیمه
	* @param $FileName: نام فایل
	* @param $ChangedTables: جداول تغییر داده شده
	* @param $ChangedPages: صفحات تغییر داده شده
	* @return 	*/
	static function Update($UpdateRecordID, $ActivityDate, $ProjectTaskActivityTypeID, $ActivityLength, $ProgressPercent, $ActivityDescription, $FileContent, $FileName, $ChangedTables, $ChangedPages)
	{
		$k=0;
		$obj = new be_ProjectTaskActivities();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectTaskActivities set ";
			$query .= " ActivityDate=? ";
			$query .= ", ProjectTaskActivityTypeID=? ";
			$query .= ", ActivityLength=? ";
			$query .= ", ProgressPercent=? ";
			$query .= ", ActivityDescription=? ";
		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", FileName=? ";
		}
			$query .= ", ChangedTables=? ";
			$query .= ", ChangedPages=? ";
		$query .= " where ProjectTaskActivityID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $ActivityDate); 
		array_push($ValueListArray, $ProjectTaskActivityTypeID); 
		array_push($ValueListArray, $ActivityLength); 
		array_push($ValueListArray, $ProgressPercent); 
		array_push($ValueListArray, $ActivityDescription); 
		if($FileName!="")
		{ 
			array_push($ValueListArray, $FileName); 
		} 
		array_push($ValueListArray, $ChangedTables); 
		array_push($ValueListArray, $ChangedPages); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		
		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$mysql = dbclass::getInstance();
			$query = "update projectmanagement.ProjectTaskActivities set ";
			$query .= " FileContent='".$FileContent."' ";
			$query .= " where ProjectTaskActivityID='".$UpdateRecordID."'";
			$mysql->Execute($query);
 		}
		
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "ACTIVITY", $UpdateRecordID, "UPDATE");
		
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در اقدامات");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectTaskActivities();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectTaskActivities where ProjectTaskActivityID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));

		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "ACTIVITY", $RemoveRecordID, "DELETE");
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از اقدامات");
	}
	
	static function GetList($ProjectTaskID, $OrderByFieldName, $OrderType)
	{
		if(strtoupper($OrderType)!="ASC" && strtoupper($OrderType)!="DESC")
			$OrderType = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskActivities.ProjectTaskActivityID
				,ProjectTaskActivities.ProjectTaskID
				,ProjectTaskActivities.CreatorID
				,ProjectTaskActivities.ActivityDate
				,ProjectTaskActivities.ProjectTaskActivityTypeID
				,ProjectTaskActivities.ActivityLength
				,ProjectTaskActivities.ProgressPercent
				,ProjectTaskActivities.ActivityDescription
				,ProjectTaskActivities.FileName
				,ProjectTaskActivities.ChangedTables
				,ProjectTaskActivities.ChangedPages
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, g2j(ActivityDate) as ActivityDate_Shamsi 
			, p4.title  as p4_title  from projectmanagement.ProjectTaskActivities 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskActivities.CreatorID) 
			LEFT JOIN projectmanagement.ProjectTaskActivityTypes  p4 on (p4.ProjectTaskActivityTypeID=ProjectTaskActivities.ProjectTaskActivityTypeID)  ";
		$query .= " where ProjectTaskID=? ";
		$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $ProjectTaskID);
		if($ppc->GetPermission("View_ProjectTaskActivities")=="PRIVATE")
				$query .= " and ProjectTaskActivities.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectTaskActivities")=="NONE")
				$query .= " and 0=1 ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$mysql->Prepare($query);
		// echo $query;
		$res = $mysql->ExecuteStatement(array($ProjectTaskID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskActivities();
			$ret[$k]->ProjectTaskActivityID=$rec["ProjectTaskActivityID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActivityDate=$rec["ActivityDate"];
			$ret[$k]->ActivityDate_Shamsi=$rec["ActivityDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->ProjectTaskActivityTypeID=$rec["ProjectTaskActivityTypeID"];
			$ret[$k]->ProjectTaskActivityTypeID_Desc=$rec["p4_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActivityLength=$rec["ActivityLength"];
			$ret[$k]->ProgressPercent=$rec["ProgressPercent"];
			$ret[$k]->ActivityDescription=$rec["ActivityDescription"];
			$ret[$k]->FileName=$rec["FileName"];
			$ret[$k]->ChangedTables=$rec["ChangedTables"];
			$ret[$k]->ChangedPages=$rec["ChangedPages"];
			$k++;
		}
		return $ret;
	}

	// لیست کلیه تغییرات جدول یا فایلهای مربوط به پروژه های یک شخص را بر می گرداند
	static function GetAllChangedFilesAndTablesOfRelatedProjects($ProjectID, $PersonID,$ChangedTables,$ChangedPages, $FromRec, $NumberOfRec)
	{
		$ret = array();
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		
		$mysql = pdodb::getInstance();
		$query = "select 
				ProjectTaskActivities.ProjectTaskActivityID
				,ProjectTaskActivities.ProjectTaskID
				,ProjectTaskActivities.CreatorID
				,ProjectTaskActivities.ActivityDate
				,ProjectTaskActivities.ProjectTaskActivityTypeID
				,ProjectTaskActivities.ActivityLength
				,ProjectTaskActivities.ProgressPercent
				,ProjectTaskActivities.ActivityDescription
				,ProjectTaskActivities.FileName
				,ProjectTaskActivities.ChangedTables
				,ProjectTaskActivities.ChangedPages
				,ProjectTasks.title
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, g2j(ActivityDate) as ActivityDate_Shamsi 
			, p4.title  as p4_title
			, projects.title as ProjectTitle
				from projectmanagement.ProjectTaskActivities
				JOIN projectmanagement.ProjectTasks using (ProjectTaskID) 
				JOIN projectmanagement.ProjectMembers using (ProjectID)
				JOIN projectmanagement.projects using (ProjectID) 
				LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskActivities.CreatorID) 
				LEFT JOIN projectmanagement.ProjectTaskActivityTypes  p4 on (p4.ProjectTaskActivityTypeID=ProjectTaskActivities.ProjectTaskActivityTypeID)  
				where ProjectMembers.PersonID=? and (ChangedTables<>'' or ChangedPages<>'') ";
		if($ProjectID<>'' and $ProjectID<>'0')
			$query .= " and ProjectTasks.ProjectID=? ";
			
		if($ChangedTables!="") 
			$query .= " and ProjectTaskActivities.ChangedTables like ? ";			
		if($ChangedPages!="") 
			$query .= " and ProjectTaskActivities.ChangedPages like ? ";	
					
		$query .= " order by ActivityDate DESC ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";					 
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID);
		if($ProjectID<>'' and $ProjectID<>'0') 
			array_push($ValueListArray, $ProjectID);
			
		if($ChangedTables!="") 
			array_push($ValueListArray, "%".$ChangedTables."%"); 
		if($ChangedPages!="") 
			array_push($ValueListArray, "%".$ChangedPages."%"); 
			
			
		$res = $mysql->ExecuteStatement($ValueListArray);
		$k = 0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskActivities();
			$ret[$k]->ProjectTaskActivityID=$rec["ProjectTaskActivityID"];
			$ret[$k]->ProjectID_Desc=$rec["ProjectTitle"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->ProjectTaskTitle=$rec["title"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActivityDate=$rec["ActivityDate"];
			$ret[$k]->ActivityDate_Shamsi=$rec["ActivityDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->ProjectTaskActivityTypeID=$rec["ProjectTaskActivityTypeID"];
			$ret[$k]->ProjectTaskActivityTypeID_Desc=$rec["p4_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActivityLength=$rec["ActivityLength"];
			$ret[$k]->ProgressPercent=$rec["ProgressPercent"];
			$ret[$k]->ActivityDescription=$rec["ActivityDescription"];
			$ret[$k]->FileName=$rec["FileName"];
			$ret[$k]->ChangedTables=$rec["ChangedTables"];
			$ret[$k]->ChangedPages=$rec["ChangedPages"];
			$k++;
		}
		return $ret;
	}

	// تعداد کلیه تغییرات جدول یا فایلهای مربوط به پروژه های یک شخص را بر می گرداند
	static function GetCountOfAllChangedFilesAndTablesOfRelatedProjects($ProjectID,$ChangedTables,$ChangedPages, $PersonID)
	{
		$ret = array();
		$mysql = pdodb::getInstance();
		$query = "select count(*) as TotalCount from projectmanagement.ProjectTaskActivities
							JOIN projectmanagement.ProjectTasks using (ProjectTaskID) 
							JOIN projectmanagement.ProjectMembers using (ProjectID)
				where ProjectMembers.PersonID=? and (ChangedTables<>'' or ChangedPages<>'') ";
		if($ProjectID<>'' and $ProjectID<>'0')
			$query .= " and ProjectTasks.ProjectID=? ";
			
		if($ChangedTables!="") 
			$query .= " and ProjectTaskActivities.ChangedTables like ? ";			
		if($ChangedPages!="") 
			$query .= " and ProjectTaskActivities.ChangedPages like ? ";			
			
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID);
		if($ProjectID<>'' and $ProjectID<>'0') 
			array_push($ValueListArray, $ProjectID); 
			
		if($ChangedTables!="") 
			array_push($ValueListArray, "%".$ChangedTables."%"); 
		if($ChangedPages!="") 
			array_push($ValueListArray, "%".$ChangedPages."%"); 
			
			
		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
}
?>
