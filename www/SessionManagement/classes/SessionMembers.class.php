<?php
/*
 تعریف کلاسها و متدهای مربوط به : اعضا
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-30
*/

/*
کلاس پایه: اعضا
*/
require_once("SessionHistory.class.php");
class be_SessionMembers
{
	public $SessionMemberID;		//
	public $MemberRow;
	public $UniversitySessionID;		//کد جلسه
	public $MemberPersonType;		//نوع عضو
	public $MemberPersonType_Desc;		/* شرح مربوط به نوع عضو */
	public $MemberPersonID;		//کد شخصی عضو
	public $MemberPersonID_FullName;		/* نام و نام خانوادگی مربوط به عضو */
	public $FirstName;		//نام
	public $LastName;		//نام خانوادگی
	public $MemberRole;		//نقش 
	public $MemberRole_Desc;		/* شرح مربوط به نقش  */
	public $NeedToConfirm;		//برگزاری جلسه منوط به تایید این کاربر است
	public $NeedToConfirm_Desc;		/* شرح مربوط به برگزاری منوط به تایید کاربر است */
	public $AccessSign;		//اجازه امضای صورتجلسه
	public $AccessSign_Desc;		/* شرح مربوط به اجازه امضای صورتجلسه */
	public $ConfirmStatus;		//وضعیت تایید درخواست جلسه
	public $ConfirmStatus_Desc;		/* شرح مربوط به وضعیت تایید درخواست */
	public $SignStatus;		//وضعیت امضای فرد
	public $SignStatus_Desc;		/* شرح مربوط به وضعیت امضا */
	public $SignDescription;		//شرح امضا
	public $SignTime;		//زمان امضای صورتجلسه
	public $SignTime_Shamsi;		/* مقدار شمسی معادل با زمان امضا */
	public $PresenceType;		//نوع حضور
	public $PresenceType_Desc;		/* شرح مربوط به نوع حضور */
	public $PresenceTime;		//مدت حضور
	public $TardinessTime;		//غیبت
	public $canvasimg;	
	
	function be_SessionMembers() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SessionMembers.* 
			, CASE SessionMembers.MemberPersonType 
				WHEN 'PERSONEL' THEN 'پرسنل' 
				WHEN 'OTHER' THEN 'سایر' 
				END as MemberPersonType_Desc 
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName 
			, s6.title  as s6_title 
			, CASE SessionMembers.NeedToConfirm 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as NeedToConfirm_Desc 
			, CASE SessionMembers.AccessSign 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as AccessSign_Desc 
			, CASE SessionMembers.ConfirmStatus 
				WHEN 'RAW' THEN 'در انتظار تایید' 
				WHEN 'ACCEPT' THEN 'پذیرفته' 
				WHEN 'REJECT' THEN 'رد شده' 
				END as ConfirmStatus_Desc 
			, CASE SessionMembers.SignStatus 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as SignStatus_Desc 
			, g2j(SignTime) as SignTime_Shamsi 
			, CASE SessionMembers.PresenceType 
				WHEN 'PRESENT' THEN 'حاضر' 
				WHEN 'ABSENT' THEN 'غایب' 
				END as PresenceType_Desc from sessionmanagement.SessionMembers 
			LEFT JOIN hrmstotal.persons persons3 on (persons3.PersonID=SessionMembers.MemberPersonID) 
			LEFT JOIN sessionmanagement.MemberRoles  s6 on (s6.MemberRoleID=SessionMembers.MemberRole)  where  SessionMembers.SessionMemberID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->MemberRow=$rec["MemberRow"];
			$this->SessionMemberID=$rec["SessionMemberID"];
			$this->UniversitySessionID=$rec["UniversitySessionID"];
			$this->MemberPersonType=$rec["MemberPersonType"];
			$this->MemberPersonType_Desc=$rec["MemberPersonType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->MemberPersonID=$rec["MemberPersonID"];
			$this->MemberPersonID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$this->FirstName=$rec["FirstName"];
			$this->LastName=$rec["LastName"];
			$this->MemberRole=$rec["MemberRole"];
			$this->MemberRole_Desc=$rec["s6_title"]; // محاسبه از روی جدول وابسته
			$this->NeedToConfirm=$rec["NeedToConfirm"];
			$this->NeedToConfirm_Desc=$rec["NeedToConfirm_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->AccessSign=$rec["AccessSign"];
			$this->AccessSign_Desc=$rec["AccessSign_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->ConfirmStatus=$rec["ConfirmStatus"];
			$this->ConfirmStatus_Desc=$rec["ConfirmStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->SignStatus=$rec["SignStatus"];
			$this->SignStatus_Desc=$rec["SignStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->SignDescription=$rec["SignDescription"];
			$this->SignTime=$rec["SignTime"];
			$this->SignTime_Shamsi=$rec["SignTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->PresenceType=$rec["PresenceType"];
			$this->PresenceType_Desc=$rec["PresenceType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->PresenceTime=$rec["PresenceTime"];
			$this->TardinessTime=$rec["TardinessTime"];
		}
	}
	
}
/*
کلاس مدیریت اعضا
*/
class manage_SessionMembers
{
	static function GetCount($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(SessionMemberID) as TotalCount from sessionmanagement.SessionMembers";
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
		$query = "select max(SessionMemberID) as MaxID from sessionmanagement.SessionMembers";
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
	* @param $MemberPersonType: نوع عضو
	* @param $MemberPersonID: عضو
	* @param $FirstName: نام
	* @param $LastName: نام خانوادگی
	* @param $MemberRole: نقش 
	* @param $NeedToConfirm: برگزاری منوط به تایید کاربر است
	* @param $AccessSign: اجازه امضای صورتجلسه
	* @param $ConfirmStatus: وضعیت تایید درخواست
	* @param $SignStatus: وضعیت امضا
	* @param $SignDescription: شرح امضا
	* @param $SignTime: زمان امضا
	* @param $PresenceType: نوع حضور
	* @param $PresenceTime: مدت حضور
	* @param $TardinessTime: غیبت
	* @return کد داده اضافه شده	*/
	static function Add($MemberRow, $UniversitySessionID, $MemberPersonType, $MemberPersonID, $FirstName, $LastName, $MemberRole, $NeedToConfirm, $AccessSign, $ConfirmStatus, $SignStatus, $SignDescription, $SignTime, $PresenceType, $PresenceTime, $TardinessTime)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.SessionMembers (";
		$query .= "  MemberRow";
		$query .= ", UniversitySessionID";
		$query .= ", MemberPersonType";
		$query .= ", MemberPersonID";
		$query .= ", FirstName";
		$query .= ", LastName";
		$query .= ", MemberRole";
		$query .= ", NeedToConfirm";
		$query .= ", AccessSign";
		$query .= ", ConfirmStatus";
		$query .= ", SignStatus";
		$query .= ", SignDescription";
		$query .= ", SignTime";
		$query .= ", PresenceType";
		$query .= ", PresenceTime";
		$query .= ", TardinessTime";
		$query .= ") values (";
		$query .= "?, ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $MemberRow);
		array_push($ValueListArray, $UniversitySessionID); 
		array_push($ValueListArray, $MemberPersonType); 
		array_push($ValueListArray, $MemberPersonID); 
		array_push($ValueListArray, $FirstName); 
		array_push($ValueListArray, $LastName); 
		array_push($ValueListArray, $MemberRole); 
		array_push($ValueListArray, $NeedToConfirm); 
		array_push($ValueListArray, $AccessSign); 
		array_push($ValueListArray, $ConfirmStatus); 
		array_push($ValueListArray, $SignStatus); 
		array_push($ValueListArray, $SignDescription); 
		array_push($ValueListArray, $SignTime); 
		array_push($ValueListArray, $PresenceType); 
		array_push($ValueListArray, $PresenceTime); 
		array_push($ValueListArray, $TardinessTime); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SessionMembers::GetLastID();
		$mysql->audit("ثبت داده جدید در اعضا با کد ".$LastID);
		if($AccessSign=="YES" && $SignStatus=="NO")
			manage_UniversitySessions::UnConfirmDescisionsFileStatus($UniversitySessionID);
		else
		{
			if(manage_UniversitySessions::IsAllPermittedMembersSignTheDescisionsFile($UniversitySessionID))
				manage_UniversitySessions::ConfirmDescisionsFileStatus($UniversitySessionID);
		}
		manage_SessionHistory::Add($UniversitySessionID, $LastID, "MEMBER", "", "ADD");
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $MemberPersonType: نوع عضو
	* @param $MemberPersonID: عضو
	* @param $FirstName: نام
	* @param $LastName: نام خانوادگی
	* @param $MemberRole: نقش 
	* @param $NeedToConfirm: برگزاری منوط به تایید کاربر است
	* @param $AccessSign: اجازه امضای صورتجلسه
	* @param $ConfirmStatus: وضعیت تایید درخواست
	* @param $SignStatus: وضعیت امضا
	* @param $SignDescription: شرح امضا
	* @param $SignTime: زمان امضا
	* @param $PresenceType: نوع حضور
	* @param $PresenceTime: مدت حضور
	* @param $TardinessTime: غیبت
	* @return 	*/
	static function Update($UpdateRecordID, $MemberRow, $MemberPersonType, $MemberPersonID, $FirstName, $LastName, $MemberRole, $NeedToConfirm, $AccessSign, $ConfirmStatus, $SignStatus, $SignDescription, $SignTime, $PresenceType, $PresenceTime, $TardinessTime)
	{
		$obj = new be_SessionMembers();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionMembers set ";
		$query .= " MemberRow=? ";
		$query .= ", MemberPersonType=? ";
		$query .= ", MemberPersonID=? ";
		$query .= ", FirstName=? ";
		$query .= ", LastName=? ";
		$query .= ", MemberRole=? ";
		$query .= ", NeedToConfirm=? ";
		$query .= ", AccessSign=? ";
		$query .= ", ConfirmStatus=? ";
		$query .= ", SignStatus=? ";
		$query .= ", SignDescription=? ";
		$query .= ", SignTime=? ";
		$query .= ", PresenceType=? ";
		$query .= ", PresenceTime=? ";
		$query .= ", TardinessTime=? ";
			$k++;
		$query .= " where SessionMemberID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $MemberRow);
		array_push($ValueListArray, $MemberPersonType); 
		array_push($ValueListArray, $MemberPersonID); 
		array_push($ValueListArray, $FirstName); 
		array_push($ValueListArray, $LastName); 
		array_push($ValueListArray, $MemberRole); 
		array_push($ValueListArray, $NeedToConfirm); 
		array_push($ValueListArray, $AccessSign); 
		array_push($ValueListArray, $ConfirmStatus); 
		array_push($ValueListArray, $SignStatus); 
		array_push($ValueListArray, $SignDescription); 
		array_push($ValueListArray, $SignTime); 
		array_push($ValueListArray, $PresenceType); 
		array_push($ValueListArray, $PresenceTime); 
		array_push($ValueListArray, $TardinessTime); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در اعضا");
		if($AccessSign=="YES" && $SignStatus=="NO")
			manage_UniversitySessions::UnConfirmDescisionsFileStatus($UniversitySessionID);
		else
		{
			if(manage_UniversitySessions::IsAllPermittedMembersSignTheDescisionsFile($UniversitySessionID))
				manage_UniversitySessions::ConfirmDescisionsFileStatus($UniversitySessionID);
		}
		manage_SessionHistory::Add($UniversitySessionID, $UpdateRecordID, "MEMBER", "", "EDIT");
	}
	
	// وضعیت حضور و غیاب فرد را بروز می کند
	static function UpdatePAStatus($UpdateRecordID, $PresenceType, $PresenceTime, $TardinessTime)
	{
		$obj = new be_SessionMembers();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionMembers set ";
		$query .= " PresenceType=? ";
		$query .= ", PresenceTime=? ";
		$query .= ", TardinessTime=? ";
		$query .= " where SessionMemberID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PresenceType); 
		array_push($ValueListArray, $PresenceTime); 
		array_push($ValueListArray, $TardinessTime); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در حضور و غیاب اعضا");
	}
	
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_SessionMembers();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.SessionMembers where SessionMemberID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از اعضا");
		manage_SessionHistory::Add($UniversitySessionID, $RemoveRecordID, "MEMBER", "", "REMOVE");
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
		$query = "select SessionMembers.* 
			, CASE SessionMembers.MemberPersonType 
				WHEN 'PERSONEL' THEN 'پرسنل' 
				WHEN 'OTHER' THEN 'سایر' 
				END as MemberPersonType_Desc 
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName 
			, s6.title  as s6_title 
			, CASE SessionMembers.NeedToConfirm 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as NeedToConfirm_Desc 
			, CASE SessionMembers.AccessSign 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as AccessSign_Desc 
			, CASE SessionMembers.ConfirmStatus 
				WHEN 'RAW' THEN 'در انتظار تایید' 
				WHEN 'ACCEPT' THEN 'پذیرفته' 
				WHEN 'REJECT' THEN 'رد شده' 
				END as ConfirmStatus_Desc 
			, CASE SessionMembers.SignStatus 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as SignStatus_Desc 
			, g2j(SignTime) as SignTime_Shamsi 
			, CASE SessionMembers.PresenceType 
				WHEN 'PRESENT' THEN 'حاضر' 
				WHEN 'ABSENT' THEN 'غایب' 
				END as PresenceType_Desc from sessionmanagement.SessionMembers 
			LEFT JOIN hrmstotal.persons persons3 on (persons3.PersonID=SessionMembers.MemberPersonID) 
			LEFT JOIN sessionmanagement.MemberRoles  s6 on (s6.MemberRoleID=SessionMembers.MemberRole)  ";
		$query .= " where UniversitySessionID=? ";
		$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $UniversitySessionID);
		if($ppc->GetPermission("View_SessionMembers")=="PRIVATE")
				$query .= " and SessionMembers.='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_SessionMembers")=="NONE")
				$query .= " and 0=1 ";
		$query .= " order by MemberRow limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
/*if($_SESSION["UserID"]=='gholami-a'){
		echo $query;
}*/
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionMembers();
			$ret[$k]->MemberRow=$rec["MemberRow"];
			$ret[$k]->SessionMemberID=$rec["SessionMemberID"];
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->MemberPersonType=$rec["MemberPersonType"];
			$ret[$k]->MemberPersonType_Desc=$rec["MemberPersonType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->MemberPersonID=$rec["MemberPersonID"];
			$ret[$k]->MemberPersonID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->FirstName=$rec["FirstName"];
			$ret[$k]->LastName=$rec["LastName"];
			$ret[$k]->MemberRole=$rec["MemberRole"];
			$ret[$k]->MemberRole_Desc=$rec["s6_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->NeedToConfirm=$rec["NeedToConfirm"];
			$ret[$k]->NeedToConfirm_Desc=$rec["NeedToConfirm_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->AccessSign=$rec["AccessSign"];
			$ret[$k]->AccessSign_Desc=$rec["AccessSign_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ConfirmStatus=$rec["ConfirmStatus"];
			$ret[$k]->ConfirmStatus_Desc=$rec["ConfirmStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->SignStatus=$rec["SignStatus"];
			$ret[$k]->SignStatus_Desc=$rec["SignStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->SignDescription=$rec["SignDescription"];
			$ret[$k]->SignTime=$rec["SignTime"];
			$ret[$k]->SignTime_Shamsi=$rec["SignTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->PresenceType=$rec["PresenceType"];
			$ret[$k]->PresenceType_Desc=$rec["PresenceType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->PresenceTime=$rec["PresenceTime"];
			$ret[$k]->TardinessTime=$rec["TardinessTime"];
			$ret[$k]->canvasimg=$rec["canvasimg"];			
			$k++;
		}
		return $ret;
	}
	function ShowSummary($RecID)
	{
		$ret = "<br>";
		$ret .= "<table width=\"90%\" align=\"center\" border=\"1\" cellspacing=\"0\">";
		$ret .= "<tr>";
		$ret .= "<td>";
		$ret .= "<table width=\"100%\" border=\"0\">";
		$obj = new be_SessionMembers();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "</table>";
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</table>";
		return $ret;
	}
	function ShowTabs($RecID, $CurrentPageName)
	{
		$ret = "<table align=\"center\" width=\"90%\" border=\"1\" cellspacing=\"0\">";
 		$ret .= "<tr>";
		$ret .= "<td width=\"100%\" ";
		if($CurrentPageName=="NewSessionMembers")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='NewSessionMembers.php?UpdateID=".$RecID."'>مشخصات اصلی</a></td>";
		$ret .= "</table>";
		return $ret;
	}
	
	// تعیین می کند آیا کاربر اجازه امضا دارد
	function HasSignRight($UniversitySessionID, $PersonID)
	{
		$mysql = pdodb::getInstance();
		$query = "select AccessSign from sessionmanagement.SessionMembers where MemberPersonID=? and UniversitySessionID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID, $UniversitySessionID));
		if($rec = $res->fetch())
			return $rec["AccessSign"];
		return "NO";
	}

	function GetLastMemberRow($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select max(MemberRow)+1 as MaxMemberRow from sessionmanagement.SessionMembers	";
		$query .= " where UniversitySessionID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		if($rec = $res->fetch())
			return $rec["MaxMemberRow"];
		return 1;
	}
	
}
?>
