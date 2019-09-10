<?php
/*
 تعریف کلاسها و متدهای مربوط به : اعضای الگوهای جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-26
*/

/*
کلاس پایه: اعضای الگوهای جلسه
*/
class be_SessionTypeMembers
{
	public $SessionTypeMemberID;		//
	public $MemberRow;
	public $SessionTypeID;		//کپ نوع جلسه
	public $MemberPersonType;		//نوع عضو
	public $MemberPersonType_Desc;		/* شرح مربوط به نوع عضو */
	public $MemberPersonID;		//کد شخصی عضو
	public $MemberPersonID_FullName;		/* نام و نام خانوادگی مربوط به کد شخصی عضو */
	public $FirstName;		//نام
	public $LastName;		//نام خانوادگی
	public $MemberRoleID;		//کد نقش
	public $MemberRoleID_Desc;		/* شرح مربوط به نقش */
	public $NeedToConfirm;		//برگزاری جلسه منوط به تایید این کاربر است
	public $NeedToConfirm_Desc;		/* شرح مربوط به برگزاری جلسه منوط به تایید این کاربر است */
	public $AccessSign;		//اجازه امضای صورتجلسه
	public $AccessSign_Desc;		/* شرح مربوط به اجازه امضای صورتجلسه */
	public $NeedToSignSessionDecisions;		//برای قطعی شدن صورتجلسه نیاز به امضای الکترونیکی فرد می باشد
	public $NeedToSignSessionDecisions_Desc;		/* شرح مربوط به برای قطعی شدن صورتجلسه نیاز به امضای الکترونیکی فرد می باشد */
	public $NeedToConfirmPresence;		//مدعو باید درخواست حضور را تایید نماید
	public $NeedToConfirmPresence_Desc;		/* شرح مربوط به مدعو باید درخواست حضور را تایید نماید */
	public $AccessFinalAccept;		//نحوه دسترسی به تایید نهایی صورتجلسه
	public $AccessFinalAccept_Desc;		/* شرح مربوط به نحوه دسترسی به تایید نهایی صورتجلسه */
	public $AccessRejectSign;		//نحوه دسترسی به امکان لغو امضای اعضای جلسه
	public $AccessRejectSign_Desc;		/* شرح مربوط به نحوه دسترسی به امکان لغو امضای اعضای جلسه */

	function be_SessionTypeMembers() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SessionTypeMembers.* 
			, CASE SessionTypeMembers.MemberPersonType 
				WHEN 'PERSONEL' THEN 'پرسنل' 
				WHEN 'OTHER' THEN 'سایر' 
				END as MemberPersonType_Desc 
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName 
			, s6.title  as s6_title 
			, CASE SessionTypeMembers.NeedToConfirm 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر ' 
				END as NeedToConfirm_Desc 
			, CASE SessionTypeMembers.AccessSign 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as AccessSign_Desc 
			, CASE SessionTypeMembers.NeedToSignSessionDecisions 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as NeedToSignSessionDecisions_Desc 
			, CASE SessionTypeMembers.NeedToConfirmPresence 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as NeedToConfirmPresence_Desc 
			, CASE SessionTypeMembers.AccessFinalAccept 
				WHEN 'WRITE' THEN 'ویرایش' 
				WHEN 'READ' THEN 'خواندن' 
				WHEN 'NONE' THEN 'عدم دسترسی' 
				END as AccessFinalAccept_Desc 
			, CASE SessionTypeMembers.AccessRejectSign 
				WHEN 'WRITE' THEN 'ویرایش' 
				WHEN 'READ' THEN 'خواندن' 
				WHEN 'NONE' THEN 'عدم دسترسی' 
				END as AccessRejectSign_Desc from sessionmanagement.SessionTypeMembers 
			LEFT JOIN hrmstotal.persons persons3 on (persons3.PersonID=SessionTypeMembers.MemberPersonID) 
			LEFT JOIN sessionmanagement.MemberRoles  s6 on (s6.MemberRoleID=SessionTypeMembers.MemberRoleID)  where  SessionTypeMembers.SessionTypeMemberID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->MemberRow=$rec["MemberRow"];
			$this->SessionTypeMemberID=$rec["SessionTypeMemberID"];
			$this->SessionTypeID=$rec["SessionTypeID"];
			$this->MemberPersonType=$rec["MemberPersonType"];
			$this->MemberPersonType_Desc=$rec["MemberPersonType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->MemberPersonID=$rec["MemberPersonID"];
			$this->MemberPersonID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$this->FirstName=$rec["FirstName"];
			$this->LastName=$rec["LastName"];
			$this->MemberRoleID=$rec["MemberRoleID"];
			$this->MemberRoleID_Desc=$rec["s6_title"]; // محاسبه از روی جدول وابسته
			$this->NeedToConfirm=$rec["NeedToConfirm"];
			$this->NeedToConfirm_Desc=$rec["NeedToConfirm_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->AccessSign=$rec["AccessSign"];
			$this->AccessSign_Desc=$rec["AccessSign_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->NeedToSignSessionDecisions=$rec["NeedToSignSessionDecisions"];
			$this->NeedToSignSessionDecisions_Desc=$rec["NeedToSignSessionDecisions_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->NeedToConfirmPresence=$rec["NeedToConfirmPresence"];
			$this->NeedToConfirmPresence_Desc=$rec["NeedToConfirmPresence_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->AccessFinalAccept=$rec["AccessFinalAccept"];
			$this->AccessFinalAccept_Desc=$rec["AccessFinalAccept_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->AccessRejectSign=$rec["AccessRejectSign"];
			$this->AccessRejectSign_Desc=$rec["AccessRejectSign_Desc"];  // محاسبه بر اساس لیست ثابت
		}
	}
}
/*
کلاس مدیریت اعضای الگوهای جلسه
*/
class manage_SessionTypeMembers
{
	static function GetCount($SessionTypeID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(SessionTypeMemberID) as TotalCount from sessionmanagement.SessionTypeMembers";
			$query .= " where SessionTypeID='".$SessionTypeID."'";
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
		$query = "select max(SessionTypeMemberID) as MaxID from sessionmanagement.SessionTypeMembers";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $SessionTypeID: نوع جلسه
	* @param $MemberPersonType: نوع عضو
	* @param $MemberPersonID: کد شخصی عضو
	* @param $FirstName: نام
	* @param $LastName: نام خانوادگی
	* @param $MemberRoleID: نقش
	* @param $NeedToConfirm: برگزاری جلسه منوط به تایید این کاربر است
	* @param $AccessSign: اجازه امضای صورتجلسه
	* @param $NeedToSignSessionDecisions: برای قطعی شدن صورتجلسه نیاز به امضای الکترونیکی فرد می باشد
	* @param $NeedToConfirmPresence: مدعو باید درخواست حضور را تایید نماید
	* @param $AccessFinalAccept: نحوه دسترسی به تایید نهایی صورتجلسه
	* @param $AccessRejectSign: نحوه دسترسی به امکان لغو امضای اعضای جلسه
	* @return کد داده اضافه شده	*/
	static function Add($MemberRow, $SessionTypeID, $MemberPersonType, $MemberPersonID, $FirstName, $LastName, $MemberRoleID, $NeedToConfirm, $AccessSign, $NeedToSignSessionDecisions, $NeedToConfirmPresence, $AccessFinalAccept, $AccessRejectSign)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.SessionTypeMembers (";
		$query .= "  MemberRow";
		$query .= ", SessionTypeID";
		$query .= ", MemberPersonType";
		$query .= ", MemberPersonID";
		$query .= ", FirstName";
		$query .= ", LastName";
		$query .= ", MemberRoleID";
		$query .= ", NeedToConfirm";
		$query .= ", AccessSign";
		$query .= ", NeedToSignSessionDecisions";
		$query .= ", NeedToConfirmPresence";
		$query .= ", AccessFinalAccept";
		$query .= ", AccessRejectSign";
		$query .= ") values (";
		$query .= "?, ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $MemberRow);
		array_push($ValueListArray, $SessionTypeID); 
		array_push($ValueListArray, $MemberPersonType); 
		array_push($ValueListArray, $MemberPersonID); 
		array_push($ValueListArray, $FirstName); 
		array_push($ValueListArray, $LastName); 
		array_push($ValueListArray, $MemberRoleID); 
		array_push($ValueListArray, $NeedToConfirm); 
		array_push($ValueListArray, $AccessSign); 
		array_push($ValueListArray, $NeedToSignSessionDecisions); 
		array_push($ValueListArray, $NeedToConfirmPresence); 
		array_push($ValueListArray, $AccessFinalAccept); 
		array_push($ValueListArray, $AccessRejectSign); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SessionTypeMembers::GetLastID();
		$mysql->audit("ثبت داده جدید در اعضای الگوهای جلسه با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $MemberPersonType: نوع عضو
	* @param $MemberPersonID: کد شخصی عضو
	* @param $FirstName: نام
	* @param $LastName: نام خانوادگی
	* @param $MemberRoleID: نقش
	* @param $NeedToConfirm: برگزاری جلسه منوط به تایید این کاربر است
	* @param $AccessSign: اجازه امضای صورتجلسه
	* @param $NeedToSignSessionDecisions: برای قطعی شدن صورتجلسه نیاز به امضای الکترونیکی فرد می باشد
	* @param $NeedToConfirmPresence: مدعو باید درخواست حضور را تایید نماید
	* @param $AccessFinalAccept: نحوه دسترسی به تایید نهایی صورتجلسه
	* @param $AccessRejectSign: نحوه دسترسی به امکان لغو امضای اعضای جلسه
	* @return 	*/
	static function Update($UpdateRecordID, $MemberRow, $MemberPersonType, $MemberPersonID, $FirstName, $LastName, $MemberRoleID, $NeedToConfirm, $AccessSign, $NeedToSignSessionDecisions, $NeedToConfirmPresence, $AccessFinalAccept, $AccessRejectSign)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionTypeMembers set ";
		$query .= "MemberRow=? ";
		$query .= ", MemberPersonType=? ";
		$query .= ", MemberPersonID=? ";
		$query .= ", FirstName=? ";
		$query .= ", LastName=? ";
		$query .= ", MemberRoleID=? ";
		$query .= ", NeedToConfirm=? ";
		$query .= ", AccessSign=? ";
		$query .= ", NeedToSignSessionDecisions=? ";
		$query .= ", NeedToConfirmPresence=? ";
		$query .= ", AccessFinalAccept=? ";
		$query .= ", AccessRejectSign=? ";
		$query .= " where SessionTypeMemberID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $MemberRow);
		array_push($ValueListArray, $MemberPersonType); 
		array_push($ValueListArray, $MemberPersonID); 
		array_push($ValueListArray, $FirstName); 
		array_push($ValueListArray, $LastName); 
		array_push($ValueListArray, $MemberRoleID); 
		array_push($ValueListArray, $NeedToConfirm); 
		array_push($ValueListArray, $AccessSign); 
		array_push($ValueListArray, $NeedToSignSessionDecisions); 
		array_push($ValueListArray, $NeedToConfirmPresence); 
		array_push($ValueListArray, $AccessFinalAccept); 
		array_push($ValueListArray, $AccessRejectSign); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در اعضای الگوهای جلسه");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.SessionTypeMembers where SessionTypeMemberID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از اعضای الگوهای جلسه");
	}

	static function GetList($SessionTypeID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SessionTypeMembers.* 
			, CASE SessionTypeMembers.MemberPersonType 
				WHEN 'PERSONEL' THEN 'پرسنل' 
				WHEN 'OTHER' THEN 'سایر' 
				END as MemberPersonType_Desc 
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName 
			, s6.title  as s6_title 
			, CASE SessionTypeMembers.NeedToConfirm 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر ' 
				END as NeedToConfirm_Desc 
			, CASE SessionTypeMembers.AccessSign 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as AccessSign_Desc 
			, CASE SessionTypeMembers.NeedToSignSessionDecisions 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as NeedToSignSessionDecisions_Desc 
			, CASE SessionTypeMembers.NeedToConfirmPresence 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as NeedToConfirmPresence_Desc 
			, CASE SessionTypeMembers.AccessFinalAccept 
				WHEN 'WRITE' THEN 'ویرایش' 
				WHEN 'READ' THEN 'خواندن' 
				WHEN 'NONE' THEN 'عدم دسترسی' 
				END as AccessFinalAccept_Desc 
			, CASE SessionTypeMembers.AccessRejectSign 
				WHEN 'WRITE' THEN 'ویرایش' 
				WHEN 'READ' THEN 'خواندن' 
				WHEN 'NONE' THEN 'عدم دسترسی' 
				END as AccessRejectSign_Desc from sessionmanagement.SessionTypeMembers 
			LEFT JOIN hrmstotal.persons persons3 on (persons3.PersonID=SessionTypeMembers.MemberPersonID) 
			LEFT JOIN sessionmanagement.MemberRoles  s6 on (s6.MemberRoleID=SessionTypeMembers.MemberRoleID)  ";
		$query .= " where SessionTypeID=? order by MemberRow";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($SessionTypeID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionTypeMembers();
			$ret[$k]->SessionTypeMemberID=$rec["SessionTypeMemberID"];
			$ret[$k]->MemberRow=$rec["MemberRow"];
			$ret[$k]->SessionTypeID=$rec["SessionTypeID"];
			$ret[$k]->MemberPersonType=$rec["MemberPersonType"];
			$ret[$k]->MemberPersonType_Desc=$rec["MemberPersonType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->MemberPersonID=$rec["MemberPersonID"];
			$ret[$k]->MemberPersonID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->FirstName=$rec["FirstName"];
			$ret[$k]->LastName=$rec["LastName"];
			$ret[$k]->MemberRoleID=$rec["MemberRoleID"];
			$ret[$k]->MemberRoleID_Desc=$rec["s6_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->NeedToConfirm=$rec["NeedToConfirm"];
			$ret[$k]->NeedToConfirm_Desc=$rec["NeedToConfirm_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->AccessSign=$rec["AccessSign"];
			$ret[$k]->AccessSign_Desc=$rec["AccessSign_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->NeedToSignSessionDecisions=$rec["NeedToSignSessionDecisions"];
			$ret[$k]->NeedToSignSessionDecisions_Desc=$rec["NeedToSignSessionDecisions_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->NeedToConfirmPresence=$rec["NeedToConfirmPresence"];
			$ret[$k]->NeedToConfirmPresence_Desc=$rec["NeedToConfirmPresence_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->AccessFinalAccept=$rec["AccessFinalAccept"];
			$ret[$k]->AccessFinalAccept_Desc=$rec["AccessFinalAccept_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->AccessRejectSign=$rec["AccessRejectSign"];
			$ret[$k]->AccessRejectSign_Desc=$rec["AccessRejectSign_Desc"];  // محاسبه بر اساس لیست ثابت
			$k++;
		}
		return $ret;
	}
	
	function GetLastMemberRow($SessionTypeID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select max(MemberRow)+1 as MaxMemberRow from sessionmanagement.SessionTypeMembers	";
		$query .= " where SessionTypeID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($SessionTypeID));
		if($rec = $res->fetch())
			return $rec["MaxMemberRow"];
		return 1;
	}
	
	
	// لیست انواع در دسترس کاربر را به صورت لیست از آپشنهای یک سلکت باکس بر می گرداند
	function GetSelectOptions($PersonID)
	{
		$mysql = pdodb::getInstance();
		$ret = "";
		$query = "select distinct SessionTypeID, SessionTypes.SessionTypeTitle  
			from sessionmanagement.PersonPermissionsOnFields 
			JOIN sessionmanagement.SessionTypes on (SessionTypeID=RecID) 
			where 
			PersonPermissionsOnFields.TableName='SessionTypes' 
			and PersonPermissionsOnFields.AccessType='WRITE' 
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
	
	// مشخص می کند آیا فرد مجاز به ایجاد جلسه از این الگوی جلسه می باشد یا خیر
	function IsPersonPermittedToCreateSession($PersonID, $SessionTypeID)
	{
		$mysql = pdodb::getInstance();
		$query = "select distinct SessionTypeID, SessionTypes.SessionTypeTitle  
			from sessionmanagement.PersonPermissionsOnFields 
			JOIN sessionmanagement.SessionTypes on (SessionTypeID=RecID) 
			where 
			PersonPermissionsOnFields.TableName='SessionTypes' 
			and PersonPermissionsOnFields.AccessType='WRITE' 
			and PersonPermissionsOnFields.FieldName='CreateNewSession' 
			and PersonPermissionsOnFields.PersonID=? 
			and SessionTypeID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID, $SessionTypeID));
		if($rec=$res->fetch())
		{
			return true;
		}
		return false;
	}
}
?>
