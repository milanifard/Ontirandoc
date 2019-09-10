<?php
/*
 تعریف کلاسها و متدهای مربوط به : تاریخچه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-23
*/

/*
کلاس پایه: تاریخچه
*/
class be_ProjectTaskHistory
{
	public $ProjectTaskHistoryID;		//
	public $ProjectTaskID;		//کار مربوطه
	public $PersonID;		//شخص
	public $PersonID_FullName;		/* نام و نام خانوادگی مربوط به شخص */
	public $ActionDesc;		//شرح کار
	public $ChangedPart;		//بخش مربوطه
	public $ChangedPart_Desc;		/* شرح مربوط به بخش مربوطه */
	public $RelatedItemID;		//کد آیتم مربوطه
	public $ActionType;		//نوع عمل
	public $ActionType_Desc;		/* شرح مربوط به نوع عمل */
	public $ProjectTaskTitle; // استخراج از روی کد کار
	public $ActionTime;
	public $ActionTime_Shamsi;
	
	
	function be_ProjectTaskHistory() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTaskHistory.* 
			, concat(g2j(ActionTime), ' ', substr(ActionTime, 12, 10)) as ActionTime_Shamsi
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, CASE ProjectTaskHistory.ChangedPart 
				WHEN 'MAIN_TASK' THEN 'مشخصات کار' 
				WHEN 'COMMENT' THEN 'یادداشت' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'ACTIVITY' THEN 'اقدام' 
				WHEN 'REQUISITE' THEN 'پیشنیاز' 
				WHEN 'USER' THEN 'کاربر' 
				WHEN 'VIEWER' THEN 'ناظر' 
				END as ChangedPart_Desc 
			, CASE ProjectTaskHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'DELETE' THEN 'حذف' 
				WHEN 'UPDATE' THEN 'بروزرسانی' 
				WHEN 'VIEW' THEN 'مشاهده' 
				END as ActionType_Desc from projectmanagement.ProjectTaskHistory 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskHistory.PersonID)  where  ProjectTaskHistory.ProjectTaskHistoryID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectTaskHistoryID=$rec["ProjectTaskHistoryID"];
			$this->ProjectTaskID=$rec["ProjectTaskID"];
			$this->PersonID=$rec["PersonID"];
			$this->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$this->ActionDesc=$rec["ActionDesc"];
			$this->ChangedPart=$rec["ChangedPart"];
			$this->ChangedPart_Desc=$rec["ChangedPart_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->RelatedItemID=$rec["RelatedItemID"];
			$this->ActionType=$rec["ActionType"];
			$this->ActionType_Desc=$rec["ActionType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->ActionTime=$rec["ActionTime"];
			$this->ActionTime_Shamsi=$rec["ActionTime_Shamsi"];  // 
		}
	}
}
/*
کلاس مدیریت تاریخچه
*/
class manage_ProjectTaskHistory
{
	static function GetCount($ProjectTaskID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectTaskHistoryID) as TotalCount from projectmanagement.ProjectTaskHistory";
			$query .= " where ProjectTaskID='".$ProjectTaskID."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	
	static function GetCountOfAllInProject($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectTaskHistoryID) as TotalCount from projectmanagement.ProjectTaskHistory JOIN projectmanagement.ProjectTasks using (ProjectTaskID) ";
			$query .= " where ProjectID='".$ProjectID."'";
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
		$query = "select max(ProjectTaskHistoryID) as MaxID from projectmanagement.ProjectTaskHistory";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectTaskID: کار مربوطه
	* @param $ActionDesc: شرح کار
	* @param $ChangedPart: بخش مربوطه
	* @param $RelatedItemID: کد آیتم مربوطه
	* @param $ActionType: نوع عمل
	* @return کد داده اضافه شده	*/
	static function Add($ProjectTaskID, $ActionDesc, $ChangedPart, $RelatedItemID, $ActionType)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectTaskHistory (";
		$query .= " ProjectTaskID";
		$query .= ", PersonID";
		$query .= ", ActionDesc";
		$query .= ", ChangedPart";
		$query .= ", RelatedItemID";
		$query .= ", ActionType , ActionTime";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , ? , now() ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectTaskID); 
		array_push($ValueListArray, $_SESSION["PersonID"]);	
		array_push($ValueListArray, $ActionDesc); 
		array_push($ValueListArray, $ChangedPart); 
		array_push($ValueListArray, $RelatedItemID); 
		array_push($ValueListArray, $ActionType); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectTaskHistory::GetLastID();
		//$mysql->audit("ثبت داده جدید در تاریخچه با کد ".$LastID);
		return $LastID;
	}
	static function GetList($ProjectTaskID, $FromRec, $NumberOfRec)
	{
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskHistory.ProjectTaskHistoryID
				,ProjectTaskHistory.ProjectTaskID
				,ProjectTaskHistory.PersonID
				,ProjectTaskHistory.ActionDesc
				,ProjectTaskHistory.ChangedPart
				,ProjectTaskHistory.RelatedItemID
				,ProjectTaskHistory.ActionType
				,ProjectTaskHistory.ActionTime
			, concat(g2j(ActionTime), ' ', substr(ActionTime, 12, 10)) as ActionTime_Shamsi
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, CASE ProjectTaskHistory.ChangedPart 
				WHEN 'MAIN_TASK' THEN 'مشخصات کار' 
				WHEN 'COMMENT' THEN 'یادداشت' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'ACTIVITY' THEN 'اقدام' 
				WHEN 'REQUISITE' THEN 'پیشنیاز' 
				WHEN 'USER' THEN 'کاربر' 
				WHEN 'VIEWER' THEN 'ناظر' 
				END as ChangedPart_Desc 
			, CASE ProjectTaskHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'DELETE' THEN 'حذف' 
				WHEN 'UPDATE' THEN 'بروزرسانی' 
				WHEN 'VIEW' THEN 'مشاهده' 
				END as ActionType_Desc  from projectmanagement.ProjectTaskHistory 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskHistory.PersonID)  ";
		$query .= " where ProjectTaskID=? ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectTaskID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskHistory();
			$ret[$k]->ProjectTaskHistoryID=$rec["ProjectTaskHistoryID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActionDesc=$rec["ActionDesc"];
			$ret[$k]->ChangedPart=$rec["ChangedPart"];
			$ret[$k]->ChangedPart_Desc=$rec["ChangedPart_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->RelatedItemID=$rec["RelatedItemID"];
			$ret[$k]->ActionType=$rec["ActionType"];
			$ret[$k]->ActionType_Desc=$rec["ActionType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ActionTime=$rec["ActionTime"];
			$ret[$k]->ActionTime_Shamsi=$rec["ActionTime_Shamsi"];  // 
			$k++;
		}
		return $ret;
	}
	/**
	* @param $ProjectTaskID کد آیتم پدر
	* @param $PersonID: شخص
	* @param $ChangedPart: بخش مربوطه
	* @param $ActionType: نوع عمل
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($ProjectTaskID, $ActionPersonID, $ChangedPart, $ActionType, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskHistory.ProjectTaskHistoryID
				,ProjectTaskHistory.ProjectTaskID
				,ProjectTaskHistory.PersonID
				,ProjectTaskHistory.ActionDesc
				,ProjectTaskHistory.ChangedPart
				,ProjectTaskHistory.RelatedItemID
				,ProjectTaskHistory.ActionType
				,ProjectTaskHistory.ActionTime
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName
			 , concat(g2j(ActionTime), ' ', substr(ActionTime, 12, 10)) as ActionTime_Shamsi
			, CASE ProjectTaskHistory.ChangedPart 
				WHEN 'MAIN_TASK' THEN 'مشخصات کار' 
				WHEN 'COMMENT' THEN 'یادداشت' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'ACTIVITY' THEN 'اقدام' 
				WHEN 'REQUISITE' THEN 'پیشنیاز' 
				WHEN 'USER' THEN 'کاربر' 
				WHEN 'VIEWER' THEN 'ناظر' 
				END as ChangedPart_Desc 
			, CASE ProjectTaskHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'DELETE' THEN 'حذف' 
				WHEN 'UPDATE' THEN 'بروزرسانی' 
				WHEN 'VIEW' THEN 'مشاهده' 
				END as ActionType_Desc  from projectmanagement.ProjectTaskHistory 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskHistory.PersonID)  ";
		$cond = "ProjectTaskID=? ";
		if($ActionPersonID!="0" && $ActionPersonID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectTaskHistory.PersonID=? ";
		}
		if($ChangedPart!="0" && $ChangedPart!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectTaskHistory.ChangedPart=? ";
		}
		if($ActionType!="0" && $ActionType!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectTaskHistory.ActionType=? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$query .= " order by ProjectTaskHistoryID DESC";
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectTaskID); 
		if($ActionPersonID!="0" && $ActionPersonID!="") 
			array_push($ValueListArray, $ActionPersonID); 
		if($ChangedPart!="0" && $ChangedPart!="") 
			array_push($ValueListArray, $ChangedPart); 
		if($ActionType!="0" && $ActionType!="") 
			array_push($ValueListArray, $ActionType); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskHistory();
			$ret[$k]->ProjectTaskHistoryID=$rec["ProjectTaskHistoryID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActionDesc=$rec["ActionDesc"];
			$ret[$k]->ChangedPart=$rec["ChangedPart"];
			$ret[$k]->ChangedPart_Desc=$rec["ChangedPart_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->RelatedItemID=$rec["RelatedItemID"];
			$ret[$k]->ActionType=$rec["ActionType"];
			$ret[$k]->ActionType_Desc=$rec["ActionType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ActionTime=$rec["ActionTime"];
			$ret[$k]->ActionTime_Shamsi=$rec["ActionTime_Shamsi"];  // 
			$k++;
		}
		return $ret;
	}

	static function SearchInAllTasksOfProject($ProjectID, $ActionPersonID, $ChangedPart, $ActionType, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskHistory.ProjectTaskHistoryID
				,ProjectTaskHistory.ProjectTaskID
				,ProjectTaskHistory.PersonID
				,ProjectTaskHistory.ActionDesc
				,ProjectTaskHistory.ChangedPart
				,ProjectTaskHistory.RelatedItemID
				,ProjectTaskHistory.ActionType
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, CASE ProjectTaskHistory.ChangedPart 
				WHEN 'MAIN_TASK' THEN 'مشخصات کار' 
				WHEN 'COMMENT' THEN 'یادداشت' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'ACTIVITY' THEN 'اقدام' 
				WHEN 'REQUISITE' THEN 'پیشنیاز' 
				WHEN 'USER' THEN 'کاربر' 
				WHEN 'VIEWER' THEN 'ناظر' 
				END as ChangedPart_Desc 
			, CASE ProjectTaskHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'DELETE' THEN 'حذف' 
				WHEN 'UPDATE' THEN 'بروزرسانی' 
				WHEN 'VIEW' THEN 'مشاهده' 
				END as ActionType_Desc
				, ProjectTasks.title
				from projectmanagement.ProjectTaskHistory
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskHistory.PersonID)  
			JOIN projectmanagement.ProjectTasks using (ProjectTaskID) ";
		$cond = "ProjectID=? ";
		if($ActionPersonID!="0" && $ActionPersonID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectTaskHistory.PersonID=? ";
		}
		if($ChangedPart!="0" && $ChangedPart!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectTaskHistory.ChangedPart=? ";
		}
		if($ActionType!="0" && $ActionType!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectTaskHistory.ActionType=? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$query .= " order by ProjectTaskHistoryID DESC";		
		$mysql->Prepare($query);
		
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		if($ActionPersonID!="0" && $ActionPersonID!="") 
			array_push($ValueListArray, $ActionPersonID); 
		if($ChangedPart!="0" && $ChangedPart!="") 
			array_push($ValueListArray, $ChangedPart); 
		if($ActionType!="0" && $ActionType!="") 
			array_push($ValueListArray, $ActionType);
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskHistory();
			$ret[$k]->ProjectTaskHistoryID=$rec["ProjectTaskHistoryID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->ProjectTaskTitle=$rec["title"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActionDesc=$rec["ActionDesc"];
			$ret[$k]->ChangedPart=$rec["ChangedPart"];
			$ret[$k]->ChangedPart_Desc=$rec["ChangedPart_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->RelatedItemID=$rec["RelatedItemID"];
			$ret[$k]->ActionType=$rec["ActionType"];
			$ret[$k]->ActionType_Desc=$rec["ActionType_Desc"];  // محاسبه بر اساس لیست ثابت
			$k++;
		}
		return $ret;
	}
	
}
?>
