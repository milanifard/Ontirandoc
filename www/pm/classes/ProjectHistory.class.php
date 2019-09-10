<?php
/*
 تعریف کلاسها و متدهای مربوط به : تاریخچه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-24
*/

/*
کلاس پایه: تاریخچه
*/
class be_ProjectHistory
{
	public $ProjectHistoryID;		//
	public $ProjectID;		//کار مربوطه
	public $PersonID;		//شخص
	public $PersonID_FullName;		/* نام و نام خانوادگی مربوط به شخص */
	public $ActionDesc;		//شرح کار
	public $ChangedPart;		//بخش مربوطه
	public $ChangedPart_Desc;		/* شرح مربوط به بخش مربوطه */
	public $RelatedItemID;		//کد آیتم مربوطه
	public $ActionType;		//نوع عمل
	public $ActionType_Desc;		/* شرح مربوط به نوع عمل */
	public $ActionTime;
	public $ActionTime_Shamsi;

	function be_ProjectHistory() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectHistory.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, CASE ProjectHistory.ChangedPart 
				WHEN 'MAIN_PROJECT' THEN 'مشخصات اصلی' 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'MILESTONE' THEN 'تاریخ مهم' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'DOCUMENT_TYPE' THEN 'نوع سند' 
				WHEN 'ACTIVITY_TYPE' THEN 'نوع اقدام' 
				WHEN 'TASK_TYPE' THEN 'نوع کار' 
				END as ChangedPart_Desc 
			, CASE ProjectHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'REMOVE' THEN 'حذف' 
				WHEN 'UPDATE' THEN 'بروزرسانی' 
				WHEN 'VIEW' THEN 'مشاهده' 
				END as ActionType_Desc
			, concat(g2j(ActionTime), ' ', substr(ActionTime, 12, 10)) as ActionTime_Shamsi
			from projectmanagement.ProjectHistory
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectHistory.PersonID)  where  ProjectHistory.ProjectHistoryID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectHistoryID=$rec["ProjectHistoryID"];
			$this->ProjectID=$rec["ProjectID"];
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
class manage_ProjectHistory
{
	static function GetCount($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectHistoryID) as TotalCount from projectmanagement.ProjectHistory";
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
		$query = "select max(ProjectHistoryID) as MaxID from projectmanagement.ProjectHistory";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectID: پروژه مربوطه
	* @param $ActionDesc: شرح کار
	* @param $ChangedPart: بخش مربوطه
	* @param $RelatedItemID: کد آیتم مربوطه
	* @param $ActionType: نوع عمل
	* @return کد داده اضافه شده	*/
	static function Add($ProjectID, $ActionDesc, $ChangedPart, $RelatedItemID, $ActionType)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectHistory (";
		$query .= " ProjectID";
		$query .= ", PersonID";
		$query .= ", ActionDesc";
		$query .= ", ChangedPart";
		$query .= ", RelatedItemID";
		$query .= ", ActionType, ActionTime ";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , ? , now()";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		array_push($ValueListArray, $ActionDesc); 
		array_push($ValueListArray, $ChangedPart); 
		array_push($ValueListArray, $RelatedItemID); 
		array_push($ValueListArray, $ActionType); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectHistory::GetLastID();
		//$mysql->audit("ثبت داده جدید در تاریخچه با کد ".$LastID);
		return $LastID;
	}

	static function GetList($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType)
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
		$query = "select ProjectHistory.ProjectHistoryID
				,ProjectHistory.ProjectID
				,ProjectHistory.PersonID
				,ProjectHistory.ActionDesc
				,ProjectHistory.ChangedPart
				,ProjectHistory.RelatedItemID
				,ProjectHistory.ActionType
				, concat(g2j(ActionTime), ' ', substr(ActionTime, 12, 10)) as ActionTime_Shamsi
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, CASE ProjectHistory.ChangedPart 
				WHEN 'MAIN_PROJECT' THEN 'مشخصات اصلی' 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'MILESTONE' THEN 'تاریخ مهم' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'DOCUMENT_TYPE' THEN 'نوع سند' 
				WHEN 'ACTIVITY_TYPE' THEN 'نوع اقدام' 
				WHEN 'TASK_TYPE' THEN 'نوع کار' 
				END as ChangedPart_Desc 
			, CASE ProjectHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'REMOVE' THEN 'حذف' 
				WHEN 'UPDATE' THEN 'بروزرسانی' 
				WHEN 'VIEW' THEN 'مشاهده' 
				END as ActionType_Desc  from projectmanagement.ProjectHistory 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectHistory.PersonID)  ";
		$query .= " where ProjectID=? ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectHistory();
			$ret[$k]->ProjectHistoryID=$rec["ProjectHistoryID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
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
	* @param $ProjectID کد آیتم پدر
	* @param $PersonID: شخص
	* @param $ChangedPart: بخش مربوطه
	* @param $ActionType: نوع عمل
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($ProjectID, $PersonID, $ChangedPart, $ActionType, $OtherConditions, $OrderByFieldName="", $OrderType="")
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectHistory.ProjectHistoryID
				,ProjectHistory.ProjectID
				,ProjectHistory.PersonID
				,ProjectHistory.ActionDesc
				,ProjectHistory.ChangedPart
				,ProjectHistory.RelatedItemID
				,ProjectHistory.ActionType
				,ProjectHistory.ActionTime
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName
			, concat(g2j(ActionTime), ' ', substr(ActionTime, 12, 10)) as ActionTime_Shamsi 
			, CASE ProjectHistory.ChangedPart 
				WHEN 'MAIN_PROJECT' THEN 'مشخصات اصلی' 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'MILESTONE' THEN 'تاریخ مهم' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'DOCUMENT_TYPE' THEN 'نوع سند' 
				WHEN 'ACTIVITY_TYPE' THEN 'نوع اقدام' 
				WHEN 'TASK_TYPE' THEN 'نوع کار' 
				END as ChangedPart_Desc 
			, CASE ProjectHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'REMOVE' THEN 'حذف' 
				WHEN 'UPDATE' THEN 'بروزرسانی' 
				WHEN 'VIEW' THEN 'مشاهده' 
				END as ActionType_Desc  from projectmanagement.ProjectHistory 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectHistory.PersonID)  ";
		$cond = "ProjectID=? ";
		if($PersonID!="0" && $PersonID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectHistory.PersonID=? ";
		}
		if($ChangedPart!="0" && $ChangedPart!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectHistory.ChangedPart=? ";
		}
		if($ActionType!="0" && $ActionType!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectHistory.ActionType=? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		if($PersonID!="0" && $PersonID!="") 
			array_push($ValueListArray, $PersonID); 
		if($ChangedPart!="0" && $ChangedPart!="") 
			array_push($ValueListArray, $ChangedPart); 
		if($ActionType!="0" && $ActionType!="") 
			array_push($ValueListArray, $ActionType); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectHistory();
			$ret[$k]->ProjectHistoryID=$rec["ProjectHistoryID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
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
}
?>
