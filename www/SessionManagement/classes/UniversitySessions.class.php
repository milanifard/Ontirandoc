<?php
/*
 تعریف کلاسها و متدهای مربوط به : جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-29
*/

/*
کلاس پایه: جلسات
*/
require_once("SessionHistory.class.php");
class be_UniversitySessions
{
	public $UniversitySessionID;		//
	public $SessionTypeID;		//کد الگو
	public $SessionTypeID_Desc;		/* شرح مربوط به نوع جلسه */
	public $SessionNumber;		//شماره جلسه
	public $SessionTitle;		//عنوان
	public $SessionDate;		//تاریخ
	public $SessionDate_Shamsi;		/* مقدار شمسی معادل با تاریخ تشکیل */
	public $SessionLocation;		//مکان
	public $SessionStartTime;		//زمان شروع
	public $SessionDurationTime;		//مدت جلسه
	public $SessionStatus;		//وضعیت
	public $SessionStatus_Desc;		/* شرح مربوط به وضعیت جلسه */
	public $SessionDescisionsFile;		//فایل صورتجلسه
	public $SessionDescisionsFileName;		//نام فایل صورتجلسه
	public $CreatorPersonID;	// کد شخصی ایجاد کننده جلسه
	public $Creator_FullName; // نام شخص ایجاد کننده جلسه
	
	public $CurrentUserHasRemoveAccess = "NO"; // مشخص می کند آیا کاربر جاری مجوز حذف این رکورد را دارد یا خیر
	public $DescisionsFileStatus; // وضعیت فایل صورتجلسه
	
	public $DescisionsListStatus; // وضعیت لیست مصوبات جلسه
	
	function be_UniversitySessions() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select AccessType, UniversitySessions.* 
			, s1.SessionTypeTitle  as s1_SessionTypeTitle 
			, g2j(SessionDate) as SessionDate_Shamsi 
			, CASE UniversitySessions.SessionStatus 
				WHEN '0' THEN 'درخواست تشکیل' 
				WHEN '1' THEN 'در حال برگزاری' 
				WHEN '2' THEN 'برگزار شده' 
				END as SessionStatus_Desc
			, concat(pfname, ' ', plname) as CreatorFullName 
			from sessionmanagement.UniversitySessions 
			LEFT JOIN sessionmanagement.SessionTypes  s1 on (s1.SessionTypeID=UniversitySessions.SessionTypeID)  
			LEFT JOIN projectmanagement.persons on (CreatorPersonID=PersonID)
			LEFT JOIN sessionmanagement.PersonPermissionsOnFields on (PersonPermissionsOnFields.RecID=UniversitySessionID and PersonPermissionsOnFields.TableName='UniversitySessions' and PersonPermissionsOnFields.FieldName='RemoveSession' and PersonPermissionsOnFields.PersonID=?)
			where  UniversitySessions.UniversitySessionID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($_SESSION["PersonID"], $RecID));
		if($rec=$res->fetch())
		{
			$this->UniversitySessionID=$rec["UniversitySessionID"];
			$this->SessionTypeID=$rec["SessionTypeID"];
			$this->SessionTypeID_Desc=$rec["s1_SessionTypeTitle"]; // محاسبه از روی جدول وابسته
			$this->SessionNumber=$rec["SessionNumber"];
			$this->SessionTitle=$rec["SessionTitle"];
			$this->SessionDate=$rec["SessionDate"];
			$this->SessionDate_Shamsi=$rec["SessionDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->SessionLocation=$rec["SessionLocation"];
			$this->SessionStartTime=$rec["SessionStartTime"];
			$this->SessionDurationTime=$rec["SessionDurationTime"];
			$this->SessionStatus=$rec["SessionStatus"];
			$this->SessionStatus_Desc=$rec["SessionStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->SessionDescisionsFile=$rec["SessionDescisionsFile"];
			$this->SessionDescisionsFileName=$rec["SessionDescisionsFileName"];
			$this->CreatorPersonID=$rec["CreatorPersonID"];
			$this->Creator_FullName=$rec["CreatorFullName"];
			$this->DescisionsFileStatus = $rec["DescisionsFileStatus"];
			$this->DescisionsListStatus = $rec["DescisionsListStatus"];
			$this->CurrentUserHasRemoveAccess="NO";
			if($rec["AccessType"]=="WRITE")
				$this->CurrentUserHasRemoveAccess="YES";
		}
	}
}
/*
کلاس مدیریت جلسات
*/
class manage_UniversitySessions
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(UniversitySessionID) as TotalCount from sessionmanagement.UniversitySessions";
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
	
	// کد آخرین جلسه از یک نوع را بر می گرداند
	static function GetLastID($SessionTypeID)
	{
		$mysql = pdodb::getInstance();
		$query = "select max(UniversitySessionID) as MaxID from sessionmanagement.UniversitySessions where SessionTypeID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($SessionTypeID));
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/*static function GetLID($SessionTypeID)
	{
		$mysql = pdodb::getInstance();
		$query = "select max(SessionNumber) as MID from sessionmanagement.UniversitySessions where SessionTypeID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($SessionTypeID));
		if($rec=$res->fetch())
		{
			return $rec["MID"];
		}
		return -1;
	}*/

	/**
	* @param $SessionTypeID: نوع جلسه
	* @param $SessionNumber: شماره جلسه
	* @param $SessionTitle: عنوان جلسه
	* @param $SessionDate: تاریخ تشکیل
	* @param $SessionLocation: محل تشکیل
	* @param $SessionStartTime: زمان شروع
	* @param $SessionDurationTime: مدت جلسه
	* @param $SessionStatus: وضعیت جلسه
	* @param $SessionDescisionsFile: صورتجلسه
	* @param $SessionDescisionsFileName: نام فایل
	* @param $PC: شیء از نوع PermissionsContainer که مشخصات دسترسی کاربر را در بر دارد
	* @return کد داده اضافه شده	*/
	static function Add($SessionTypeID, $SessionNumber, $SessionDate, $SessionLocation, $SessionStartTime, $SessionDurationTime)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.UniversitySessions (";
		$query .= "SessionTypeID";
		$query .= ", SessionNumber";
		$query .= ", SessionDate";
		$query .= ", SessionLocation";
		$query .= ", SessionStartTime";
		$query .= ", SessionDurationTime";
		$query .= ", SessionStatus";		
		$query .= ", CreatorPersonID";
		$query .= ") values (?, ?, ?, ?, ?, ?, 1, '".$_SESSION["PersonID"]."')";
		$ValueListArray = array();
		array_push($ValueListArray, $SessionTypeID); 
		array_push($ValueListArray, $SessionNumber); 
		array_push($ValueListArray, $SessionDate); 
		array_push($ValueListArray, $SessionLocation); 
		array_push($ValueListArray, $SessionStartTime); 
		array_push($ValueListArray, $SessionDurationTime); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_UniversitySessions::GetLastID($SessionTypeID);
		$mysql->audit("ثبت داده جدید در جلسات با کد ".$LastID);
		manage_SessionHistory::Add($LastID, $LastID, "MAIN", "ایجاد جلسه جدید", "ADD");
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PC: شیء از نوع PermissionsContainer که مشخصات دسترسی کاربر را در بر دارد
	* @param $SessionNumber: شماره جلسه
	* @param $SessionTitle: عنوان جلسه
	* @param $SessionDate: تاریخ تشکیل
	* @param $SessionLocation: محل تشکیل
	* @param $SessionStartTime: زمان شروع
	* @param $SessionDurationTime: مدت جلسه
	* @param $SessionStatus: وضعیت جلسه
	* @param $SessionDescisionsFile: صورتجلسه
	* @param $SessionDescisionsFileName: نام فایل
	* @return 	*/
	static function Update($UpdateRecordID, $SessionNumber, $SessionTitle, $SessionDate, $SessionLocation, $SessionStartTime, $SessionDurationTime, $SessionStatus, $SessionDescisionsFile, $SessionDescisionsFileName, $PC)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.UniversitySessions set ";
		if($PC->GetPermission("SessionNumber")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "SessionNumber=? ";
			$k++; 
		}
		if($PC->GetPermission("SessionTitle")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "SessionTitle=? ";
			$k++; 
		}
		if($PC->GetPermission("SessionDate")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "SessionDate=? ";
			$k++; 
		}
		if($PC->GetPermission("SessionLocation")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "SessionLocation=? ";
			$k++; 
		}
		if($PC->GetPermission("SessionStartTime")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "SessionStartTime=? ";
			$k++; 
		}
		if($PC->GetPermission("SessionDurationTime")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "SessionDurationTime=? ";
			$k++; 
		}
		if($PC->GetPermission("SessionStatus")=="WRITE")
		{
			if($k>0) 
				$query .= ", ";
			$query .= "SessionStatus=? ";
			$k++; 
		}
		if($PC->GetPermission("SessionDescisionsFile")=="WRITE")
		{
			if($SessionDescisionsFileName!="") // در صورتیکه فایل ارسال شده باشد
	 		{
				if($k>0) 
					$query .= ", ";
				$query .= "SessionDescisionsFileName=?, SessionDescisionsFile='".$SessionDescisionsFile."' ";
				if(manage_UniversitySessions::IsAnyOfMembersHasSignRight($UpdateRecordID))
				{
					manage_UniversitySessions::UnConfirmDescisionsFileStatus($UpdateRecordID);
					manage_UniversitySessions::UnSignAllMembers($UpdateRecordID);
				}
			}
			$k++; 
		}
		$query .= " where UniversitySessionID=?";
		$ValueListArray = array();
		if($PC->GetPermission("SessionNumber")=="WRITE")
		{
			array_push($ValueListArray, $SessionNumber); 
		}
		if($PC->GetPermission("SessionTitle")=="WRITE")
		{
			array_push($ValueListArray, $SessionTitle); 
		}
		if($PC->GetPermission("SessionDate")=="WRITE")
		{
			array_push($ValueListArray, $SessionDate); 
		}
		if($PC->GetPermission("SessionLocation")=="WRITE")
		{
			array_push($ValueListArray, $SessionLocation); 
		}
		if($PC->GetPermission("SessionStartTime")=="WRITE")
		{
			array_push($ValueListArray, $SessionStartTime); 
		}
		if($PC->GetPermission("SessionDurationTime")=="WRITE")
		{
			array_push($ValueListArray, $SessionDurationTime); 
		}
		if($PC->GetPermission("SessionStatus")=="WRITE")
		{
			array_push($ValueListArray, $SessionStatus); 
		}
		if($PC->GetPermission("SessionDescisionsFile")=="WRITE")
		{
			if($SessionDescisionsFileName!="")
		{ 
			array_push($ValueListArray, $SessionDescisionsFileName); 
		} 
		}
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در جلسات");
		manage_SessionHistory::Add($UpdateRecordID, $UpdateRecordID, "MAIN", "بروزرسانی مشخصات جلسه", "EDIT");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		require_once("UniversitySessionsSecurity.class.php");
		if(security_UniversitySessions::ReadFieldPermission($RemoveRecordID, "RemoveSession", $_SESSION["PersonID"])=="NONE")
			return;
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.UniversitySessions where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from sessionmanagement.SessionDecisions where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from sessionmanagement.SessionDocuments where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from sessionmanagement.SessionMembers where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from sessionmanagement.SessionOtherUsers where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from sessionmanagement.SessionPreCommands where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		/*
		$query = "delete from sessionmanagement.SessionHistory where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		*/
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از جلسات");
		manage_SessionHistory::Add($RemoveRecordID, $RemoveRecordID, "MAIN", "حذف جلسه", "REMOVE");
	}
	
	/**
	* @param $CurrentUser: کاربر جاری - فقط جلساتی که این کاربر به آنها دسترسی دارد برگردانده می شود مگر اینکه این مقدار منهای یک باشد
	* @param $SessionTypeID: نوع جلسه
	* @param $SessionNumber: شماره جلسه
	* @param $SessionTitle: عنوان جلسه
	* @param $SessionDate: تاریخ تشکیل
	* @param $SessionLocation: محل تشکیل
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($CurrentUser, $SessionTypeID,$type="", $SessionNumber, $SessionTitle, $SessionFromDate, $SessionToDate, $SessionLocation, $PreCommandKeyWord, $DecisionKeyWord, $OtherConditions, $FromRec = 0, $NumberOfRec = 30, $OrderByFieldName="", $OrderType="")
	{
		if(!is_numeric($FromRec) || !is_numeric($NumberOfRec))
		{
			$FromRec = 0;
			$NumberOfRec = 30;
		}
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select AccessType, UniversitySessions.*
			, s1.SessionTypeTitle  as s1_SessionTypeTitle 
			, g2j(SessionDate) as SessionDate_Shamsi 
			, CASE UniversitySessions.SessionStatus 
				WHEN '0' THEN 'درخواست تشکیل' 
				WHEN '1' THEN 'در حال برگزاری' 
				WHEN '2' THEN 'برگزار شده' 
				END as SessionStatus_Desc 
			, concat(pfname, ' ', plname) as CreatorPerson_FullName
			from sessionmanagement.UniversitySessions 
			LEFT JOIN projectmanagement.persons on (CreatorPersonID=PersonID)
			LEFT JOIN sessionmanagement.SessionTypes  s1 on (s1.SessionTypeID=UniversitySessions.SessionTypeID)  
			LEFT JOIN sessionmanagement.PersonPermissionsOnFields on (PersonPermissionsOnFields.RecID=UniversitySessionID and PersonPermissionsOnFields.TableName='UniversitySessions' and PersonPermissionsOnFields.FieldName='RemoveSession' and PersonPermissionsOnFields.PersonID=?) 
			";
		$cond = "";
		if($CurrentUser!="-1")
		{
			$cond .= " (CreatorPersonID=".$CurrentUser." or 
			UniversitySessionID in 
			(select UniversitySessionID from sessionmanagement.SessionMembers where MemberPersonID=".$CurrentUser." 
			union select UniversitySessionID from sessionmanagement.SessionOtherUsers where PersonID=".$CurrentUser."
			)) ";
		}
		if($SessionTypeID!="0" && $SessionTypeID!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionTypeID=? ";
		}
		if($type!="0" && $type!="" ) 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionTypeID=? ";
		}

		if($SessionNumber!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionNumber like ? ";
		}
		if($SessionTitle!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionTitle like ? ";
		}
		if($SessionFromDate!="0000-00-00" && $SessionFromDate!="")
		{
			if($cond!="") $cond .= " and ";
			$cond .= "UniversitySessions.SessionDate>=? ";
		}
		if($SessionToDate!="0000-00-00" && $SessionToDate!="")
		{
			if($cond!="") $cond .= " and ";
			$cond .= "UniversitySessions.SessionDate<=? ";
		}
		if($SessionLocation!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionLocation like ? ";
		}
		if($PreCommandKeyWord!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.UniversitySessionID in 
							(select UniversitySessionID from sessionmanagement.SessionPreCommands where description like ?) ";
		}
		if($DecisionKeyWord!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.UniversitySessionID in 
							(select UniversitySessionID from sessionmanagement.SessionDecisions where description like ?) ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$query .= " limit ".$FromRec.",".$NumberOfRec;
		
		/*if($_SESSION['UserID']=='gholami-a'){
			echo $query;
                         echo "<br>";
                          echo $SessionTypeID ;
                              echo "<br>";
                          echo $type ;

	        }*/
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CurrentUser);
		if($SessionTypeID!="0" && $SessionTypeID!="") 
			array_push($ValueListArray, $SessionTypeID); 
                if($type!="0" && $type!="") 
			array_push($ValueListArray, $type); 
		if($SessionNumber!="") 
			array_push($ValueListArray, "%".$SessionNumber."%"); 
		if($SessionTitle!="") 
			array_push($ValueListArray, "%".$SessionTitle."%"); 
		if($SessionFromDate!="0000-00-00"  && $SessionFromDate!="")
			array_push($ValueListArray, $SessionFromDate); 
		if($SessionToDate!="0000-00-00"  && $SessionToDate!="")
			array_push($ValueListArray, $SessionToDate); 
		if($SessionLocation!="") 
			array_push($ValueListArray, "%".$SessionLocation."%"); 
		if($PreCommandKeyWord!="") 
			array_push($ValueListArray, "%".$PreCommandKeyWord."%"); 
		if($DecisionKeyWord!="") 
			array_push($ValueListArray, "%".$DecisionKeyWord."%"); 
			
		$res = $mysql->ExecuteStatement($ValueListArray);
		
		/*if($_SESSION['UserID']=='gholami-a')
			print_r($ValueListArray);*/
		
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_UniversitySessions();
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->SessionTypeID=$rec["SessionTypeID"];
			$ret[$k]->SessionTypeID_Desc=$rec["s1_SessionTypeTitle"]; // محاسبه از روی جدول وابسته
			$ret[$k]->SessionNumber=$rec["SessionNumber"];
			$ret[$k]->SessionTitle=$rec["SessionTitle"];
			$ret[$k]->SessionDate=$rec["SessionDate"];
			$ret[$k]->SessionDate_Shamsi=$rec["SessionDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->SessionLocation=$rec["SessionLocation"];
			$ret[$k]->SessionStartTime=$rec["SessionStartTime"];
			$ret[$k]->SessionDurationTime=$rec["SessionDurationTime"];
			$ret[$k]->SessionStatus=$rec["SessionStatus"];
			$ret[$k]->SessionStatus_Desc=$rec["SessionStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->SessionDescisionsFileName=$rec["SessionDescisionsFileName"];
			$ret[$k]->CreatorPersonID=$rec["CreatorPersonID"];
			$ret[$k]->CreatorPerson_FullName=$rec["CreatorPerson_FullName"];
			$ret[$k]->DescisionsFileStatus=$rec["DescisionsFileStatus"];
			$ret[$k]->DescisionsListStatus=$rec["DescisionsListStatus"];
			$ret[$k]->CurrentUserHasRemoveAccess="NO";
			if($rec["AccessType"]=="WRITE")
				$ret[$k]->CurrentUserHasRemoveAccess="YES";
			$k++;
		}
		return $ret;
	}
	
	function GetSearchResultCount($CurrentUser, $SessionTypeID,$type="", $SessionNumber, $SessionTitle, $SessionFromDate, $SessionToDate, $SessionLocation, $PreCommandKeyWord, $DecisionKeyWord, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(*) as TotalCount from sessionmanagement.UniversitySessions 
			LEFT JOIN projectmanagement.persons on (CreatorPersonID=PersonID)
			LEFT JOIN sessionmanagement.SessionTypes  s1 on (s1.SessionTypeID=UniversitySessions.SessionTypeID)  
			LEFT JOIN sessionmanagement.PersonPermissionsOnFields on (PersonPermissionsOnFields.RecID=UniversitySessionID and PersonPermissionsOnFields.TableName='UniversitySessions' and PersonPermissionsOnFields.FieldName='RemoveSession' and PersonPermissionsOnFields.PersonID=?) 
			";
		$cond = "";
		if($CurrentUser!="-1")
		{
			$cond .= " (CreatorPersonID=".$CurrentUser." or 
			UniversitySessionID in 
			(select UniversitySessionID from sessionmanagement.SessionMembers where MemberPersonID=".$CurrentUser." 
			union select UniversitySessionID from sessionmanagement.SessionOtherUsers where PersonID=".$CurrentUser."
			)) ";
		}
		if($SessionTypeID!="0" && $SessionTypeID!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionTypeID=? ";
		}
                if($type!="0" && $type!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionTypeID=? ";
		}
		if($SessionNumber!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionNumber like ? ";
		}
		if($SessionTitle!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionTitle like ? ";
		}
		if($SessionFromDate!="0000-00-00" && $SessionFromDate!="")
		{
			if($cond!="") $cond .= " and ";
			$cond .= "UniversitySessions.SessionDate>=? ";
		}
		if($SessionToDate!="0000-00-00" && $SessionToDate!="")
		{
			if($cond!="") $cond .= " and ";
			$cond .= "UniversitySessions.SessionDate<=? ";
		}
		if($SessionLocation!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.SessionLocation like ? ";
		}
		if($PreCommandKeyWord!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.UniversitySessionID in 
							(select UniversitySessionID from sessionmanagement.SessionPreCommands where description like ?) ";
		}
		if($DecisionKeyWord!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UniversitySessions.UniversitySessionID in 
							(select UniversitySessionID from sessionmanagement.SessionDecisions where description like ?) ";
		}
		
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CurrentUser);
		if($SessionTypeID!="0" && $SessionTypeID!="") 
			array_push($ValueListArray, $SessionTypeID); 
                if($type!="0" && $type!="") 
			array_push($ValueListArray, $type);
		if($SessionNumber!="") 
			array_push($ValueListArray, "%".$SessionNumber."%"); 
		if($SessionTitle!="") 
			array_push($ValueListArray, "%".$SessionTitle."%"); 
		if($SessionFromDate!="0000-00-00"  && $SessionFromDate!="")
			array_push($ValueListArray, $SessionFromDate); 
		if($SessionToDate!="0000-00-00"  && $SessionToDate!="")
			array_push($ValueListArray, $SessionToDate); 
		if($SessionLocation!="") 
			array_push($ValueListArray, "%".$SessionLocation."%");
		if($PreCommandKeyWord!="") 
			array_push($ValueListArray, "%".$PreCommandKeyWord."%"); 
		if($DecisionKeyWord!="") 
			array_push($ValueListArray, "%".$DecisionKeyWord."%"); 
			
		$res = $mysql->ExecuteStatement($ValueListArray);
               /* if($_SESSION['UserID']=='gholami-a'){
			echo $query;
                         echo "<br>";
                          echo $SessionTypeID ;              

	        }*/
		$rec = $res->fetch();
		return $rec["TotalCount"];		
	}

	function GetOptions($PersonID)
	{
		$mysql = pdodb::getInstance();
		$ret = "";
		$query = "select distinct SessionTypeID, SessionTypes.SessionTypeTitle  
			from sessionmanagement.PersonPermissionsOnFields 
			JOIN sessionmanagement.SessionTypes on (SessionTypeID=RecID) 
			where 
			PersonPermissionsOnFields.TableName='SessionTypes' 
			
			and PersonPermissionsOnFields.FieldName='CreateNewSession' 
			and PersonPermissionsOnFields.PersonID=? ";
		//echo $query;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["SessionTypeID"]."'>".$rec["SessionTypeTitle"];
		}
		return $ret;
	}

	function GetcountOptions($PersonID)
	{
		$mysql = pdodb::getInstance();
		$ret = "";
		$query = "select count(*) as TotalCount 
			from sessionmanagement.PersonPermissionsOnFields 
			JOIN sessionmanagement.SessionTypes on (SessionTypeID=RecID) 
			where 
			PersonPermissionsOnFields.TableName='SessionTypes' 
			
			and PersonPermissionsOnFields.FieldName='CreateNewSession' 
			and PersonPermissionsOnFields.PersonID=? ";
		//echo $query;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID));
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
        static function Typelist($PersonID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select  SessionTypes.SessionTypeID, SessionTypes.SessionTypeTitle  
			from sessionmanagement.PersonPermissionsOnFields 
			JOIN sessionmanagement.SessionTypes on (SessionTypes.SessionTypeID=PersonPermissionsOnFields.RecID) 
			where 
			PersonPermissionsOnFields.TableName='SessionTypes' 
			
			and PersonPermissionsOnFields.FieldName='CreateNewSession' 
			and PersonPermissionsOnFields.PersonID=?  order by SessionTypes.SessionTypeID";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID));
		
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_UniversitySessions();
                        $ret[$k]->SessionTypeID=$rec["SessionTypeID"];
			$ret[$k]->SessionTypeTitle=$rec["SessionTypeTitle"];
			
		}
		return $ret;
	}

	
	function ShowSummary($RecID)
	{
		// this method is used in ManageMembersPAList that taken by MGhayour
		$ret = "<br>MGHAYOUR";
		$ret .= "<table width=\"90%\" align=\"center\" border=\"1\" cellspacing=\"0\">";
		$ret .= "<tr>";
		$ret .= "<td>";
		$ret .= "<table width=\"100%\" border=\"0\">";
		$obj = new be_UniversitySessions();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>شماره جلسه: </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->SessionNumber, ENT_QUOTES, 'UTF-8');
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>نوع و عنوان جلسه: </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->SessionTypeID_Desc, ENT_QUOTES, 'UTF-8')." - ".htmlentities($obj->SessionTitle, ENT_QUOTES, 'UTF-8');
		$ret .= "</td>";
		$ret .= "</tr>";
		
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>تاریخ تشکیل: </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= substr($obj->SessionDate_Shamsi, 0, 4).'/'.substr($obj->SessionDate_Shamsi, 5, 2).'/'.substr($obj->SessionDate_Shamsi, 8, 2); 
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</table>";
		return $ret;
	}
	function ShowTabs($RecID, $CurrentPageName)
	{
		$ret = "<table align=\"center\" width=\"90%\" border=\"1\" cellspacing=\"0\">";
 		$ret .= "<tr>";
		$ret .= "<td width=\"13%\" ";
		if($CurrentPageName=="NewUniversitySessions")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='UpdateUniversitySessions.php?UpdateID=".$RecID."'>مشخصات اصلی</a></td>";
		$ret .= "<td width=\"13%\" ";
		if($CurrentPageName=="ManageSessionPreCommands")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageSessionPreCommands.php?UniversitySessionID=".$RecID."'>دستور کار</a></td>";
		$ret .= "<td width=\"13%\" ";
		if($CurrentPageName=="ManageMembersPAList")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageMembersPAList.php?UniversitySessionID=".$RecID."'>حضور و غیاب اعضا</a></td>";
		$ret .= "<td width=\"13%\" ";
		if($CurrentPageName=="ManageSessionDecisions")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageSessionDecisions.php?UniversitySessionID=".$RecID."'>مصوبات جلسه</a></td>";
		$ret .= "<td width=\"13%\" ";
		if($CurrentPageName=="ManageSessionDocuments")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageSessionDocuments.php?UniversitySessionID=".$RecID."'>مستندات</a></td>";
		$ret .= "<td width=\"13%\" ";
		if($CurrentPageName=="ManageSessionMembers")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageSessionMembers.php?UniversitySessionID=".$RecID."'>اعضا</a></td>";
		$ret .= "<td width=\"13%\" ";
		if($CurrentPageName=="ManageSessionOtherUsers")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageSessionOtherUsers.php?UniversitySessionID=".$RecID."'>سایر کاربران</a></td>";
		$ret .= "<td width=\"13%\" ";
		if($CurrentPageName=="ManageSessionHistory")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageSessionHistory.php?UniversitySessionID=".$RecID."'>سابقه</a></td>";
		$ret .= "</table>";
		return $ret;
	}
	
	// لیست جلساتی که درخواست شرکت در آنها برای کاربر ارسال شده و هنوز تایید یا رد نشده است
	function GetRequestedSessions($CurrentUser)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select AccessType, UniversitySessions.UniversitySessionID
			, UniversitySessions.SessionNumber
			, UniversitySessions.SessionTitle
			, UniversitySessions.SessionDate
			, UniversitySessions.SessionLocation
			, UniversitySessions.SessionStartTime
			, UniversitySessions.SessionDurationTime
			, UniversitySessions.SessionStatus
			, UniversitySessions.SessionDescisionsFileName
			, UniversitySessions.SessionTypeID
			, UniversitySessions.CreatorPersonID
			, s1.SessionTypeTitle  as s1_SessionTypeTitle 
			, g2j(SessionDate) as SessionDate_Shamsi 
			, CASE UniversitySessions.SessionStatus 
				WHEN '0' THEN 'درخواست تشکیل' 
				WHEN '1' THEN 'در حال برگزاری' 
				WHEN '2' THEN 'برگزار شده' 
				END as SessionStatus_Desc 
			, concat(pfname, ' ', plname) as CreatorPerson_FullName
			from sessionmanagement.UniversitySessions 
			LEFT JOIN projectmanagement.persons on (CreatorPersonID=PersonID)
			LEFT JOIN sessionmanagement.SessionTypes  s1 on (s1.SessionTypeID=UniversitySessions.SessionTypeID)  
			LEFT JOIN sessionmanagement.PersonPermissionsOnFields on (PersonPermissionsOnFields.RecID=UniversitySessionID and PersonPermissionsOnFields.TableName='UniversitySessions' and PersonPermissionsOnFields.FieldName='RemoveSession' and PersonPermissionsOnFields.PersonID=?)
			where
			UniversitySessionID in 
			(select UniversitySessionID from sessionmanagement.SessionMembers where MemberPersonID=".$CurrentUser." and ConfirmStatus='RAW')";
		
		$query .= " order by SessionDate DESC";
		$mysql->Prepare($query);

		$ValueListArray = array();
		array_push($ValueListArray, $CurrentUser);
		$res = $mysql->ExecuteStatement($ValueListArray);
//print_r($query);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_UniversitySessions();
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->SessionTypeID=$rec["SessionTypeID"];
			$ret[$k]->SessionTypeID_Desc=$rec["s1_SessionTypeTitle"]; // محاسبه از روی جدول وابسته
			$ret[$k]->SessionNumber=$rec["SessionNumber"];
			$ret[$k]->SessionTitle=$rec["SessionTitle"];
			$ret[$k]->SessionDate=$rec["SessionDate"];
			$ret[$k]->SessionDate_Shamsi=$rec["SessionDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->SessionLocation=$rec["SessionLocation"];
			$ret[$k]->SessionStartTime=$rec["SessionStartTime"];
			$ret[$k]->SessionDurationTime=$rec["SessionDurationTime"];
			$ret[$k]->SessionStatus=$rec["SessionStatus"];
			$ret[$k]->SessionStatus_Desc=$rec["SessionStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->SessionDescisionsFileName=$rec["SessionDescisionsFileName"];
			$ret[$k]->CreatorPersonID=$rec["CreatorPersonID"];
			$ret[$k]->CreatorPerson_FullName=$rec["CreatorPerson_FullName"];
			$ret[$k]->CurrentUserHasRemoveAccess="NO";
			if($rec["AccessType"]=="WRITE")
				$ret[$k]->CurrentUserHasRemoveAccess="YES";
			$k++;
		}
		return $ret;
	}
	
	// لیست جلسات آماده امضا برای کاربر را بر می گرداند
	function GetReadyForSignSessions($CurrentUser)
	{//echo $CurrentUser;
		// جلساتی آماده امضا هستند که شرایط زیر را داشته باشند
		// ۱- فایل صورتجلسه برای آنها موجود باشد
		// ۲- کاربر جاری حق امضا داشته باشد و قبلا امضا نکرده باشد
		// و یا
		// ۱- وضعیت لیست مصوبات تکمیل باشد
		// ۲- کاربر جاری حق امضا داشته باشد و قبلا امضا نکرده باشد 
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select AccessType, UniversitySessions.UniversitySessionID
			, UniversitySessions.SessionNumber
			, UniversitySessions.SessionTitle
			, UniversitySessions.SessionDate
			, UniversitySessions.SessionLocation
			, UniversitySessions.SessionStartTime
			, UniversitySessions.SessionDurationTime
			, UniversitySessions.SessionStatus
			, UniversitySessions.SessionDescisionsFileName
			, UniversitySessions.SessionTypeID
			, UniversitySessions.CreatorPersonID
			, s1.SessionTypeTitle  as s1_SessionTypeTitle 
			, g2j(SessionDate) as SessionDate_Shamsi 
			, CASE UniversitySessions.SessionStatus 
				WHEN '0' THEN 'درخواست تشکیل' 
				WHEN '1' THEN 'در حال برگزاری' 
				WHEN '2' THEN 'برگزار شده' 
				END as SessionStatus_Desc 
			, concat(pfname, ' ', plname) as CreatorPerson_FullName
			from sessionmanagement.UniversitySessions 
			LEFT JOIN projectmanagement.persons on (CreatorPersonID=PersonID)
			LEFT JOIN sessionmanagement.SessionTypes  s1 on (s1.SessionTypeID=UniversitySessions.SessionTypeID)  
			LEFT JOIN sessionmanagement.PersonPermissionsOnFields on (PersonPermissionsOnFields.RecID=UniversitySessionID and PersonPermissionsOnFields.TableName='UniversitySessions' and PersonPermissionsOnFields.FieldName='RemoveSession' and PersonPermissionsOnFields.PersonID=?)
			where (SessionDescisionsFileName<>'' or DescisionsListStatus='CONFIRMED') and 
			UniversitySessionID in (select UniversitySessionID from sessionmanagement.SessionMembers where MemberPersonID=".$CurrentUser." and AccessSign='YES' and SignStatus='NO')";
		
		$query .= " order by SessionDate DESC";
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $CurrentUser);

//print_r($query);
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_UniversitySessions();
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->SessionTypeID=$rec["SessionTypeID"];
			$ret[$k]->SessionTypeID_Desc=$rec["s1_SessionTypeTitle"]; // محاسبه از روی جدول وابسته
			$ret[$k]->SessionNumber=$rec["SessionNumber"];
			$ret[$k]->SessionTitle=$rec["SessionTitle"];
			$ret[$k]->SessionDate=$rec["SessionDate"];
			$ret[$k]->SessionDate_Shamsi=$rec["SessionDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->SessionLocation=$rec["SessionLocation"];
			$ret[$k]->SessionStartTime=$rec["SessionStartTime"];
			$ret[$k]->SessionDurationTime=$rec["SessionDurationTime"];
			$ret[$k]->SessionStatus=$rec["SessionStatus"];
			$ret[$k]->SessionStatus_Desc=$rec["SessionStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->SessionDescisionsFileName=$rec["SessionDescisionsFileName"];
			$ret[$k]->CreatorPersonID=$rec["CreatorPersonID"];
			$ret[$k]->CreatorPerson_FullName=$rec["CreatorPerson_FullName"];
			$ret[$k]->CurrentUserHasRemoveAccess="NO";
			if($rec["AccessType"]=="WRITE")
				$ret[$k]->CurrentUserHasRemoveAccess="YES";
			$k++;
		}
		return $ret;
	}
	
	/** جلسه جدیدی از یک نوع خاص ایجاد می کند
	* @param $SessionTypeID: نوع جلسه
	* @return کد جلسه اضافه شده	*/
	function CreateNewUniversitySession($SessionTypeID)
	{
		$mysql = pdodb::getInstance();
		require_once("classes/SessionTypes.class.php");
		require_once("classes/SessionTypeMembers.class.php");
		require_once("classes/SessionMembers.class.php");
		require_once("classes/SessionTypesSecurity.class.php");
		require_once("classes/PersonPermittedSessionTypes.class.php");
		require_once("classes/SessionOtherUsers.class.php");
		
		$now = date("Ymd"); 
		$yy = substr($now,0,4); 
		$mm = substr($now,4,2); 
		$dd = substr($now,6,2);
		$SessionDate = $yy."-".$mm."-".$dd;
		$obj = new be_SessionTypes();
		$obj->LoadDataFromDatabase($SessionTypeID);
		$OldID = manage_UniversitySessions::GetLastID($SessionTypeID);
		$NewID = manage_UniversitySessions::Add($SessionTypeID, $obj->GetLastSessionNumber()+1,	$SessionDate, $obj->SessionTypeLocation, $obj->SessionTypeStartTime, $obj->SessionTypeDurationTime);
		if($OldID!=$NewID)
		{
			$members = manage_SessionTypeMembers::GetList($SessionTypeID);
			for($i=0; $i<count($members); $i++)
			{
				$ConfirmStatus = "RAW";
				if($members[$i]->NeedToSignSessionDecisions=="NO")
					$ConfirmStatus = "ACCEPT";
				$MemberID = manage_SessionMembers::Add(
					$members[$i]->MemberRow,
					$NewID, 
					$members[$i]->MemberPersonType, 
					$members[$i]->MemberPersonID, 
					$members[$i]->FirstName, 
					$members[$i]->LastName, 
					$members[$i]->MemberRoleID, 
					$members[$i]->NeedToConfirm, 
					$members[$i]->AccessSign, 
					$ConfirmStatus, 
					"NO", 
					"", 
					"0000-00-00", 
					"PRESENT", 
					0, 
					0);
			}

			$members = manage_PersonPermittedSessionTypes::GetList($SessionTypeID);
			for($i=0; $i<count($members); $i++)
				$MemberID = manage_SessionOtherUsers::Add($NewID, $members[$i]->PersonID);
			
			// اطلاعات دسترسی کاربران تعریف شده بر روی الگوی جلسه را به این جلسه اضافه می کند
			$query = "insert into sessionmanagement.PersonPermissionsOnFields (PersonID, TableName, FieldName, AccessType, RecID)   
						select PersonID, 'UniversitySessions', FieldName, AccessType, '".$NewID."' from sessionmanagement.PersonPermissionsOnFields 
						where TableName='SessionTypes' and RecID=?";
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array($SessionTypeID));

			// اطلاعات دسترسی کاربران روی جداول جزییات تعریف شده بر روی الگوی جلسه را به این جلسه اضافه می کند
			$query = "insert into sessionmanagement.PersonPermissionsOnTable (PersonID, TableName, DetailTableName, AddAccessType, RemoveAccessType, UpdateAccessType, ViewAccessType, RecID)   
						select PersonID, 'UniversitySessions', DetailTableName, AddAccessType, RemoveAccessType, UpdateAccessType, ViewAccessType, '".$NewID."' from sessionmanagement.PersonPermissionsOnTable 
						where TableName='SessionTypes' and RecID=?";
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array($SessionTypeID));
			
			// آیتمهایی از دستور کار یا مصوبات جلسه قبل که باید در دستور کار جلسه جدید تکرار شود را پیدا و به دستور کار این جلسه اضافه می کند.
			if($OldID>0)
			{
				// اگر جلسه قبلی از این نوع وجود داشت
				$query = "insert into sessionmanagement.SessionPreCommands
				(
				 UniversitySessionID
				, OrderNo
				, description
				, ResponsiblePersonID
				, RepeatInNextSession
				, RelatedFile
				, RelatedFileName
				, CreatorPersonID
				, DeadLine
				, priority
				)				
				select 
				'".$NewID."'
				,SessionPreCommands.OrderNo
				,SessionPreCommands.description
				,SessionPreCommands.ResponsiblePersonID
				,SessionPreCommands.RepeatInNextSession
				,SessionPreCommands.RelatedFile
				,SessionPreCommands.RelatedFileName
				,SessionPreCommands.CreatorPersonID
				,SessionPreCommands.DeadLine
				,SessionPreCommands.priority
				from sessionmanagement.SessionPreCommands where UniversitySessionID=? and RepeatInNextSession='YES'";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($OldID));
				
				$query = "insert into sessionmanagement.SessionPreCommands
				(
				 UniversitySessionID
				, OrderNo
				, description
				, ResponsiblePersonID
				, RepeatInNextSession
				, RelatedFile
				, RelatedFileName
				, CreatorPersonID
				)				
				select 
				'".$NewID."'
				,SessionDecisions.OrderNo
				,SessionDecisions.description
				,SessionDecisions.ResponsiblePersonID
				,SessionDecisions.RepeatInNextSession
				,SessionDecisions.RelatedFile
				,SessionDecisions.RelatedFileName
				,SessionDecisions.CreatorPersonID
				from sessionmanagement.SessionDecisions where UniversitySessionID=? and RepeatInNextSession='YES'";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($OldID));
				
			}
			return $NewID;
		}
		else
			return -1;
	}
	
	// درخواست شرکت در جلسه را تایید می کند
	function AcceptRequest($CurrentUser, $UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionMembers set ConfirmStatus='ACCEPT' where ConfirmStatus='RAW' and UniversitySessionID=? and MemberPersonID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($UniversitySessionID, $CurrentUser));
		manage_SessionHistory::Add($UniversitySessionID, $UniversitySessionID, "OTHER", "", "CONFIRM");
	}

	// درخواست شرکت در جلسه را رد می کند
	function RejectRequest($CurrentUser, $UniversitySessionID, $description)
	{
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionMembers set ConfirmStatus='REJECT', RejectDescription=? where ConfirmStatus='RAW' and UniversitySessionID=? and MemberPersonID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($description, $UniversitySessionID, $CurrentUser));
		manage_SessionHistory::Add($UniversitySessionID, $UniversitySessionID, "OTHER", $description, "REJECT");
	}

	// مشخص می کند آیا کسی از اعضای جلسه حق امضا دارد یا خیر
	function IsAnyOfMembersHasSignRight($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select * from sessionmanagement.SessionMembers where UniversitySessionID=? and AccessSign='YES'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		if($res->fetch())
			return true;
		return false;
		
	}
	
	// مشخص می کند آیا تمام اعضای مجاز به امضا صورتجلسه را امضا کرده اند
	function IsAllPermittedMembersSignTheDescisionsFile($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select * from sessionmanagement.SessionMembers where AccessSign='YES' and UniversitySessionID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		if($res->fetch())
			return true;
		return false;
	}
	
	// تایید وضعیت فایل صورتجلسه
	function ConfirmDescisionsFileStatus($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.UniversitySessions set DescisionsFileStatus='CONFIRMED' where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($UniversitySessionID));
	}

	// از تایید خارج کردن وضعیت فایل صورتجلسه
	function UnConfirmDescisionsFileStatus($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.UniversitySessions set DescisionsFileStatus='RAW' where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($UniversitySessionID));
	}

	// از تایید خارج کردن وضعیت لیست صورتجلسه
	function UnConfirmDescisionsListStatus($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.UniversitySessions set DescisionsListStatus='RAW' where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($UniversitySessionID));
	}

	// تایید وضعیت مصوبات صورتجلسه
	function ConfirmDescisionsListStatus($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.UniversitySessions set DescisionsListStatus='CONFIRMED' where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($UniversitySessionID));
	}
	
	// امضای صورتجلسه توسط کاربر
	function SignTheDescesionFile($CurrentUser, $UniversitySessionID , $canvasimg)
	{
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionMembers set SignTime=now() , canvasimg=? , SignStatus='YES' where UniversitySessionID=? and MemberPersonID=?";
		$mysql->Prepare($query);
		  /*print_r(ExceptionHandler::PopAllExceptions());
		  print_r(PdoDataAccess::GetLatestQueryString());*/		
		/*echo $query;
		echo "<br>";		
		print_r($canvasimg);//die();*/
		$mysql->ExecuteStatement(array($canvasimg, $UniversitySessionID, $CurrentUser));
		/*print_r($UniversitySessionID);
		print_r($CurrentUser);
		print_r($canvasimg);*/

		if(manage_UniversitySessions::IsAllPermittedMembersSignTheDescisionsFile($UniversitySessionID))
			manage_UniversitySessions::ConfirmDescisionsFileStatus($UniversitySessionID);
		manage_SessionHistory::Add($UniversitySessionID, $UniversitySessionID, "OTHER", $SignDescription, "SIGN");
	}

	// لغو امضای صورتجلسه توسط یک کاربر
	function UnSignTheDescesionFile($CurrentUser, $UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionMembers set SignTime='0000-00-00 00:00', SignDescription='', SignStatus='NO' where UniversitySessionID=? and MemberPersonID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($UniversitySessionID, $CurrentUser));
		manage_UniversitySessions::UnConfirmDescisionsFileStatus($UniversitySessionID);
	}
	
	// لغو تمامی امضاها
	function UnSignAllMembers($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionMembers set SignTime='0000-00-00 00:00', SignDescription='', SignStatus='NO' where UniversitySessionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($UniversitySessionID));
	}
}
?>
