<?php
/*
 تعریف کلاسها و متدهای مربوط به : سابقه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-3
*/

/*
کلاس پایه: سابقه
*/
class be_SessionHistory
{
	public $SessionHistoryID;		//
	public $UniversitySessionID;		//کد جلسه
	public $ItemID;		//کد آیتم
	public $ItemType;		//نوع آیتم
	public $ItemType_Desc;		/* شرح مربوط به نوع آیتم */
	public $description;		//مقدار قبلی
	public $PersonID;		//کد شخص
	public $PersonID_FullName;		/* نام و نام خانوادگی مربوط به کد شخص عمل کننده */
	public $ActionType;		//نوع عمل
	public $ActionType_Desc;		/* شرح مربوط به نوع عمل */
	public $ActionTime;		//زمان عمل
	public $ActionTime_Shamsi;		/* مقدار شمسی معادل با زمان انجام */
	public $IPAddress;		//آدرس آی پی

	function be_SessionHistory() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SessionHistory.* 
			, CASE SessionHistory.ItemType 
				WHEN 'MAIN' THEN 'مشخصه جلسه' 
				WHEN 'PRECOMMAND' THEN 'دستور کار' 
				WHEN 'DECISION' THEN 'مصوبه' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'USER' THEN 'کاربر' 
				WHEN 'OTHER' THEN 'سایر' 
				END as ItemType_Desc 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE SessionHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'EDIT' THEN 'ویرایش' 
				WHEN 'REMOVE' THEN 'حذف' 
				WHEN 'VIEW' THEN 'مشاهده' 
				END as ActionType_Desc 
			, concat(g2j(ActionTime), ' ', substr(ActionTime, 12,10)) as ActionTime_Shamsi from sessionmanagement.SessionHistory 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=SessionHistory.PersonID)  where  SessionHistory.SessionHistoryID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->SessionHistoryID=$rec["SessionHistoryID"];
			$this->UniversitySessionID=$rec["UniversitySessionID"];
			$this->ItemID=$rec["ItemID"];
			$this->ItemType=$rec["ItemType"];
			$this->ItemType_Desc=$rec["ItemType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->description=$rec["description"];
			$this->PersonID=$rec["PersonID"];
			$this->PersonID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$this->ActionType=$rec["ActionType"];
			$this->ActionType_Desc=$rec["ActionType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->ActionTime=$rec["ActionTime"];
			$this->ActionTime_Shamsi=$rec["ActionTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->IPAddress=$rec["IPAddress"];
		}
	}
}
/*
کلاس مدیریت سابقه
*/
class manage_SessionHistory
{
	static function GetCount($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(SessionHistoryID) as TotalCount from sessionmanagement.SessionHistory";
			$query .= " where UniversitySessionID='".$UniversitySessionID."'";
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
		$query = "select max(SessionHistoryID) as MaxID from sessionmanagement.SessionHistory";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $UniversitySessionID: کد جلسه
	* @param $ItemID: کد آیتم
	* @param $ItemType: نوع آیتم
	* @param $description: شرح
	* @param $ActionType: نوع عمل
	* @param $IPAddress: IPAddress
	* @return کد داده اضافه شده	*/
	static function Add($UniversitySessionID, $ItemID, $ItemType, $description, $ActionType)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.SessionHistory (";
		$query .= " UniversitySessionID";
		$query .= ", ItemID";
		$query .= ", ItemType";
		$query .= ", description";
		$query .= ", PersonID";
		$query .= ", ActionType";
		$query .= ", ActionTime";
		$query .= ", IPAddress";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , ? , now() , '".$_SESSION['LIPAddress']."' ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $UniversitySessionID); 
		array_push($ValueListArray, $ItemID); 
		array_push($ValueListArray, $ItemType); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		array_push($ValueListArray, $ActionType); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SessionHistory::GetLastID();
		$mysql->audit("ثبت داده جدید در سابقه با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $ItemID: کد آیتم
	* @param $ItemType: نوع آیتم
	* @param $description: شرح
	* @param $ActionType: نوع عمل
	* @param $IPAddress: IPAddress
	* @return 	*/
	static function Update($UpdateRecordID, $ItemID, $ItemType, $description, $ActionType, $IPAddress)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionHistory set ";
			$query .= " ItemID=? ";
			$query .= ", ItemType=? ";
			$query .= ", description=? ";
			$query .= ", ActionType=? ";
			$query .= ", IPAddress=? ";
		$query .= " where SessionHistoryID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $ItemID); 
		array_push($ValueListArray, $ItemType); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $ActionType); 
		array_push($ValueListArray, $IPAddress); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در سابقه");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.SessionHistory where SessionHistoryID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از سابقه");
	}
	static function GetList($UniversitySessionID, $FromRec, $NumberOfRec)
	{
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SessionHistory.SessionHistoryID
				,SessionHistory.UniversitySessionID
				,SessionHistory.ItemID
				,SessionHistory.ItemType
				,SessionHistory.description
				,SessionHistory.PersonID
				,SessionHistory.ActionType
				,SessionHistory.ActionTime
				,SessionHistory.IPAddress
			, CASE SessionHistory.ItemType 
				WHEN 'MAIN' THEN 'مشخصه جلسه' 
				WHEN 'PRECOMMAND' THEN 'دستور کار' 
				WHEN 'DECISION' THEN 'مصوبه' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'USER' THEN 'کاربر' 
				WHEN 'PAList' THEN 'لیست حضور/غیاب'
				WHEN 'OTHER' THEN '-' 
				END as ItemType_Desc 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE SessionHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'EDIT' THEN 'ویرایش' 
				WHEN 'REMOVE' THEN 'حذف' 
				WHEN 'VIEW' THEN 'مشاهده'
				WHEN 'CONFIRM' THEN 'تایید درخواست'
				WHEN 'REJECT' THEN 'رد درخواست'
				WHEN 'SIGN' THEN 'امضا'
				END as ActionType_Desc 
			, concat(g2j(ActionTime), ' ', substr(ActionTime, 12, 10)) as ActionTime_Shamsi from sessionmanagement.SessionHistory 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=SessionHistory.PersonID)  ";
		$query .= " where UniversitySessionID=? ";
		$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $UniversitySessionID);
		if($ppc->GetPermission("View_SessionHistory")=="PRIVATE")
				$query .= " and SessionHistory.PersonID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_SessionHistory")=="NONE")
				$query .= " and 0=1 ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionHistory();
			$ret[$k]->SessionHistoryID=$rec["SessionHistoryID"];
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->ItemID=$rec["ItemID"];
			$ret[$k]->ItemType=$rec["ItemType"];
			$ret[$k]->ItemType_Desc=$rec["ItemType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->description=$rec["description"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActionType=$rec["ActionType"];
			$ret[$k]->ActionType_Desc=$rec["ActionType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ActionTime=$rec["ActionTime"];
			$ret[$k]->ActionTime_Shamsi=$rec["ActionTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->IPAddress=$rec["IPAddress"];
			$k++;
		}
		return $ret;
	}
	/**
	* @param $UniversitySessionID کد آیتم پدر
	* @param $ItemType: نوع آیتم
	* @param $description: شرح
	* @param $PersonID: کد شخص عمل کننده
	* @param $ActionType: نوع عمل
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($UniversitySessionID, $ItemType, $description, $PersonID, $ActionType, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SessionHistory.SessionHistoryID
				,SessionHistory.UniversitySessionID
				,SessionHistory.ItemID
				,SessionHistory.ItemType
				,SessionHistory.description
				,SessionHistory.PersonID
				,SessionHistory.ActionType
				,SessionHistory.ActionTime
				,SessionHistory.IPAddress
			, CASE SessionHistory.ItemType 
				WHEN 'MAIN' THEN 'مشخصه جلسه' 
				WHEN 'PRECOMMAND' THEN 'دستور کار' 
				WHEN 'DECISION' THEN 'مصوبه' 
				WHEN 'DOCUMENT' THEN 'سند' 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'USER' THEN 'کاربر' 
				WHEN 'PAList' THEN 'لیست حضور/غیاب'
				WHEN 'OTHER' THEN '-' 
				END as ItemType_Desc 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE SessionHistory.ActionType 
				WHEN 'ADD' THEN 'اضافه' 
				WHEN 'EDIT' THEN 'ویرایش' 
				WHEN 'REMOVE' THEN 'حذف' 
				WHEN 'VIEW' THEN 'مشاهده'
				WHEN 'CONFIRM' THEN 'تایید درخواست'
				WHEN 'REJECT' THEN 'رد درخواست'
				WHEN 'SIGN' THEN 'امضا'
				END as ActionType_Desc 
			, concat(g2j(ActionTime), ' ', substr(ActionTime, 12, 10)) as ActionTime_Shamsi from sessionmanagement.SessionHistory 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=SessionHistory.PersonID)  ";
		$cond = "UniversitySessionID=? ";
		if($ItemType!="0" && $ItemType!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "SessionHistory.ItemType=? ";
		}
		if($description!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "SessionHistory.description like ? ";
		}
		if($PersonID!="0" && $PersonID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "SessionHistory.PersonID=? ";
		}
		if($ActionType!="0" && $ActionType!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "SessionHistory.ActionType=? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $UniversitySessionID); 
		if($ItemType!="0" && $ItemType!="") 
			array_push($ValueListArray, $ItemType); 
		if($description!="") 
			array_push($ValueListArray, "%".$description."%"); 
		if($PersonID!="0" && $PersonID!="") 
			array_push($ValueListArray, $PersonID); 
		if($ActionType!="0" && $ActionType!="") 
			array_push($ValueListArray, $ActionType); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionHistory();
			$ret[$k]->SessionHistoryID=$rec["SessionHistoryID"];
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->ItemID=$rec["ItemID"];
			$ret[$k]->ItemType=$rec["ItemType"];
			$ret[$k]->ItemType_Desc=$rec["ItemType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->description=$rec["description"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActionType=$rec["ActionType"];
			$ret[$k]->ActionType_Desc=$rec["ActionType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ActionTime=$rec["ActionTime"];
			$ret[$k]->ActionTime_Shamsi=$rec["ActionTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->IPAddress=$rec["IPAddress"];
			$k++;
		}
		return $ret;
	}
}
?>
