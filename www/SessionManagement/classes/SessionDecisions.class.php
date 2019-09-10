<?php
/*
 تعریف کلاسها و متدهای مربوط به : مصوبات جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-2
*/

/*
کلاس پایه: مصوبات جلسه
*/
require_once("SessionHistory.class.php");
class be_SessionDecisions
{
	public $SessionDecisionID;		//
	public $UniversitySessionID;		//کد جلسه
	public $OrderNo;		//ردیف
	public $description;		//شرح
	public $ResponsiblePersonID;		//کد شخص مسوول
	public $ResponsiblePersonID_FullName;		/* نام و نام خانوادگی مربوط به مسوول پیگیری */
	public $RepeatInNextSession;		//در جلسه بعدی تکرار شود
	public $RepeatInNextSession_Desc;		/* شرح مربوط به تکرار در دستور کار جلسه بعد */
	public $RelatedFile;		//فایل ضمیمه
	public $RelatedFileName;		//نام فایل ضمیمه
	public $DeadLine;
	public $priority;	
	public $HasDeadline;		//مهلت اقدام دارد؟
	public $HasDeadline_Desc;		/* شرح مربوط به مهلت اقدام دارد */
	public $DeadlineDate;		//مهلت اقدام
	public $DeadlineDate_Shamsi;		/* مقدار شمسی معادل با مهلت اقدام */
	public $CreatorPersonID;		//ایجاد کننده
	public $CreatorPersonID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $SessionPreCommandID;  // کد دستور کار جلسه مربوطه
	public $SessionPreCommandDescription; // شرح دستور کار مربوطه - استخراج از جدول دستورکارها
	public $SessionPreCommandRow; // شماره ردیف دستور کار جلسه
	public $SessionControl; // کنترل مصوبه
	public $SessionControl_Desc;
	
	function be_SessionDecisions() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SessionDecisions.* 
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName 
			, CASE SessionDecisions.RepeatInNextSession 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as RepeatInNextSession_Desc 
			, CASE SessionDecisions.SessionControl 
				WHEN 'NOT_START' THEN 'اجرا نشده' 
				WHEN 'DONE' THEN 'اجرا شده' 
				END as SessionControl_Desc 
			, CASE SessionDecisions.HasDeadline 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasDeadline_Desc 
			, g2j(DeadlineDate) as DeadlineDate_Shamsi 
			, concat(persons10.pfname, ' ', persons10.plname) as persons10_FullName
			, SessionPreCommands.SessionPreCommandID
			, SessionPreCommands.description as PreCommandDesc 
			, SessionPreCommands.OrderNo as SessionPreCommandRow
			from sessionmanagement.SessionDecisions
			LEFT JOIN sessionmanagement.SessionPreCommands using (SessionPreCommandID)  
			LEFT JOIN hrmstotal.persons persons4 on (persons4.PersonID=SessionDecisions.ResponsiblePersonID) 
			LEFT JOIN hrmstotal.persons persons10 on (persons10.PersonID=SessionDecisions.CreatorPersonID)  where  SessionDecisions.SessionDecisionID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->SessionDecisionID=$rec["SessionDecisionID"];
			$this->SessionControl=$rec["SessionControl"];
			$this->SessionControl_Desc=$rec["SessionControl_Desc"];
			$this->UniversitySessionID=$rec["UniversitySessionID"];
			$this->OrderNo=$rec["OrderNo"];
			$this->description=$rec["description"];
			$this->ResponsiblePersonID=$rec["ResponsiblePersonID"];
			$this->ResponsiblePersonID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
			$this->RepeatInNextSession=$rec["RepeatInNextSession"];
			$this->RepeatInNextSession_Desc=$rec["RepeatInNextSession_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->RelatedFile=$rec["RelatedFile"];
			$this->RelatedFileName=$rec["RelatedFileName"];
			$this->HasDeadline=$rec["HasDeadline"];
			$this->HasDeadline_Desc=$rec["HasDeadline_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->DeadlineDate=$rec["DeadlineDate"];
			$this->DeadlineDate_Shamsi=$rec["DeadlineDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->CreatorPersonID=$rec["CreatorPersonID"];
			$this->CreatorPersonID_FullName=$rec["persons10_FullName"]; // محاسبه از روی جدول وابسته
			$this->SessionPreCommandID=$rec["SessionPreCommandID"];
			$this->SessionPreCommandDescription=$rec["PreCommandDesc"];
			$this->SessionPreCommandRow=$rec["SessionPreCommandRow"];
		}
	}
}
/*
کلاس مدیریت مصوبات جلسه
*/
class manage_SessionDecisions
{
	static function GetCount($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(SessionDecisionID) as TotalCount from sessionmanagement.SessionDecisions";
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
		$query = "select max(SessionDecisionID) as MaxID from sessionmanagement.SessionDecisions";
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
	* @param $OrderNo: ردیف
	* @param $description: شرح
	* @param $ResponsiblePersonID: مسوول پیگیری
	* @param $RepeatInNextSession: تکرار در دستور کار جلسه بعد
	* @param $RelatedFile: فایل ضمیمه
	* @param $RelatedFileName: نام فایل
	* @param $HasDeadline: مهلت اقدام دارد
	* @param $DeadlineDate: مهلت اقدام
	* @return کد داده اضافه شده	*/
	static function Add($UniversitySessionID, $OrderNo, $description, $ResponsiblePersonID, $RepeatInNextSession, $RelatedFile, $RelatedFileName, $HasDeadline, $DeadlineDate, $SessionPreCommandID, $SessionControl)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.SessionDecisions (";
		$query .= " UniversitySessionID";
		$query .= ", OrderNo";
		$query .= ", description";
		$query .= ", ResponsiblePersonID";
		$query .= ", RepeatInNextSession";
		$query .= ", RelatedFile";
		$query .= ", RelatedFileName";
		$query .= ", HasDeadline";
		$query .= ", DeadlineDate";
		$query .= ", CreatorPersonID";
		$query .= ", SessionPreCommandID";
		$query .= ", SessionControl";
		
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , '".$RelatedFile."', ? , ? , ? , ? , ?, ?";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $UniversitySessionID); 
		array_push($ValueListArray, $OrderNo); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $ResponsiblePersonID); 
		array_push($ValueListArray, $RepeatInNextSession); 
		array_push($ValueListArray, $RelatedFileName); 
		array_push($ValueListArray, $HasDeadline); 
		array_push($ValueListArray, $DeadlineDate);
		array_push($ValueListArray, $_SESSION["PersonID"]);
		array_push($ValueListArray, $SessionPreCommandID);
		array_push($ValueListArray, $SessionControl);
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SessionDecisions::GetLastID();
		$mysql->audit("ثبت داده جدید در مصوبات جلسه با کد ".$LastID);
		manage_SessionHistory::Add($UniversitySessionID, $LastID, "DECISION", "", "ADD");
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $OrderNo: ردیف
	* @param $description: شرح
	* @param $ResponsiblePersonID: مسوول پیگیری
	* @param $RepeatInNextSession: تکرار در دستور کار جلسه بعد
	* @param $RelatedFile: فایل ضمیمه
	* @param $RelatedFileName: نام فایل
	* @param $HasDeadline: مهلت اقدام دارد
	* @param $DeadlineDate: مهلت اقدام
	* @return 	*/
	static function Update($UpdateRecordID, $OrderNo, $description, $ResponsiblePersonID, $RepeatInNextSession, $RelatedFile, $RelatedFileName, $HasDeadline, $DeadlineDate, $SessionPreCommandID, $SessionControl)
	{
		$obj = new be_SessionDecisions();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionDecisions set ";
		$query .= " OrderNo=? ";
		$query .= ", description=? ";
		$query .= ", ResponsiblePersonID=? ";
		$query .= ", RepeatInNextSession=? ";
		if($RelatedFileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", RelatedFileName=?, RelatedFile='".$RelatedFile."' ";
		}
		$query .= ", HasDeadline=? ";
		$query .= ", DeadlineDate=? ";
		$query .= ", SessionPreCommandID=? ";
		$query .= ", SessionControl=? ";
		$query .= " where SessionDecisionID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $OrderNo); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $ResponsiblePersonID); 
		array_push($ValueListArray, $RepeatInNextSession); 
		if($RelatedFileName!="")
		{ 
			array_push($ValueListArray, $RelatedFileName); 
		} 
		array_push($ValueListArray, $HasDeadline); 
		array_push($ValueListArray, $DeadlineDate); 
		array_push($ValueListArray, $SessionPreCommandID);
		array_push($ValueListArray, $SessionControl);
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در مصوبات جلسه");
		manage_SessionHistory::Add($UniversitySessionID, $UpdateRecordID, "DECISION", "", "EDIT");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_SessionDecisions();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.SessionDecisions where SessionDecisionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از مصوبات جلسه");
		manage_SessionHistory::Add($UniversitySessionID, $RemoveRecordID, "DECISION", "", "REMOVE");
	}
	static function GetList($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SessionDecisions.SessionDecisionID
				,SessionDecisions.UniversitySessionID
				,SessionDecisions.OrderNo
				,SessionDecisions.description
				,SessionDecisions.ResponsiblePersonID
				,SessionDecisions.RepeatInNextSession
				,SessionDecisions.RelatedFile
				,SessionDecisions.RelatedFileName
				,SessionDecisions.HasDeadline
				,SessionDecisions.DeadlineDate
				,SessionDecisions.CreatorPersonID
				,SessionDecisions.SessionControl
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName 
			, CASE SessionDecisions.SessionControl 
				WHEN 'NOT_START' THEN 'اجرا نشده' 
				WHEN 'DONE' THEN 'اجرا شده' 
				END as SessionControl_Desc 
			, CASE SessionDecisions.RepeatInNextSession 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as RepeatInNextSession_Desc 
			, CASE SessionDecisions.HasDeadline 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as HasDeadline_Desc 
			, g2j(DeadlineDate) as DeadlineDate_Shamsi 
			, concat(persons10.pfname, ' ', persons10.plname) as persons10_FullName
			, SessionPreCommands.SessionPreCommandID
			, SessionPreCommands.description as PreCommandDesc 
			, SessionPreCommands.OrderNo as SessionPreCommandRow
			from sessionmanagement.SessionDecisions
			LEFT JOIN sessionmanagement.SessionPreCommands using (SessionPreCommandID) 
			LEFT JOIN hrmstotal.persons persons4 on (persons4.PersonID=SessionDecisions.ResponsiblePersonID) 
			LEFT JOIN hrmstotal.persons persons10 on (persons10.PersonID=SessionDecisions.CreatorPersonID)  ";
		$query .= " where SessionDecisions.UniversitySessionID=? ";
		$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $UniversitySessionID);
		if($ppc->GetPermission("View_SessionDecisions")=="PRIVATE")
				$query .= " and SessionDecisions.CreatorPersonID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_SessionDecisions")=="NONE")
				$query .= " and 0=1 ";
		$query .= " order by SessionDecisions.OrderNo";
		$mysql->Prepare($query);
                /*if($_SESSION["UserID"]=='gholami-a'){
		     echo $query;
                }*/
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionDecisions();
			$ret[$k]->SessionDecisionID=$rec["SessionDecisionID"];
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->OrderNo=$rec["OrderNo"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->ResponsiblePersonID=$rec["ResponsiblePersonID"];
			$ret[$k]->ResponsiblePersonID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->RepeatInNextSession=$rec["RepeatInNextSession"];
			$ret[$k]->RepeatInNextSession_Desc=$rec["RepeatInNextSession_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->RelatedFile=$rec["RelatedFile"];
			$ret[$k]->RelatedFileName=$rec["RelatedFileName"];
			$ret[$k]->HasDeadline=$rec["HasDeadline"];
			$ret[$k]->HasDeadline_Desc=$rec["HasDeadline_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->DeadlineDate=$rec["DeadlineDate"];
			$ret[$k]->DeadlineDate_Shamsi=$rec["DeadlineDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->CreatorPersonID=$rec["CreatorPersonID"];
			$ret[$k]->CreatorPersonID_FullName=$rec["persons10_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->SessionPreCommandID=$rec["SessionPreCommandID"];
			$ret[$k]->SessionPreCommandDescription=$rec["PreCommandDesc"];
			$ret[$k]->SessionPreCommandRow=$rec["SessionPreCommandRow"];
			$ret[$k]->SessionControl=$rec["SessionControl"];
			$ret[$k]->SessionControl_Desc=$rec["SessionControl_Desc"];
			$k++;
		}
		return $ret;
	}

	// آخرین شماره ردیف مصوبات جلسه را بر می گرداند
	static function GetMaxOrderNo($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select max(OrderNo) as MaxNo from sessionmanagement.SessionDecisions where  UniversitySessionID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		$rec = $res->fetch();
		if($rec["MaxNo"]=="")
			return 0;
		return $rec["MaxNo"];
	}	
}
?>
