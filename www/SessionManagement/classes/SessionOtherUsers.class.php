<?php
/*
 تعریف کلاسها و متدهای مربوط به : سایر کاربران
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-30
*/

/*
کلاس پایه: سایر کاربران
*/
require_once("SessionHistory.class.php");
class be_SessionOtherUsers
{
	public $SessionOtherUserID;		//
	public $UniversitySessionID;		//کد جلسه
	public $PersonID;		//کد شخص
	public $PersonID_FullName;		/* نام و نام خانوادگی مربوط به کد شخص */

	function be_SessionOtherUsers() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SessionOtherUsers.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName from sessionmanagement.SessionOtherUsers 
			LEFT JOIN hrmstotal.persons persons2 on (persons2.PersonID=SessionOtherUsers.PersonID)  where  SessionOtherUsers.SessionOtherUserID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->SessionOtherUserID=$rec["SessionOtherUserID"];
			$this->UniversitySessionID=$rec["UniversitySessionID"];
			$this->PersonID=$rec["PersonID"];
			$this->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت سایر کاربران
*/
class manage_SessionOtherUsers
{
	static function GetCount($UniversitySessionID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(SessionOtherUserID) as TotalCount from sessionmanagement.SessionOtherUsers";
			$query .= " where UniversitySessionID='".$UniversitySessionID."'";
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
		$query = "select max(SessionOtherUserID) as MaxID from sessionmanagement.SessionOtherUsers";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $UniversitySessionID: کد جلسه
	* @param $PersonID: کد شخص
	* @return کد داده اضافه شده	*/
	static function Add($UniversitySessionID, $PersonID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.SessionOtherUsers (";
			if($k>0) 
				$query .= ", ";
			$query .= "UniversitySessionID";
			$k++; 
			if($k>0) 
				$query .= ", ";
			$query .= "PersonID";
			$k++; 
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $UniversitySessionID); 
		array_push($ValueListArray, $PersonID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SessionOtherUsers::GetLastID();
		$mysql->audit("ثبت داده جدید در سایر کاربران با کد ".$LastID);
		manage_SessionHistory::Add($UniversitySessionID, $LastID, "USER", "", "ADD");
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PersonID: کد شخص
	* @return 	*/
	static function Update($UpdateRecordID, $PersonID)
	{
		$k=0;
		$obj = new be_SessionOtherUsers();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionOtherUsers set ";
		if($k>0) 
			$query .= ", ";
		$query .= "PersonID=? ";
			$k++;
		$query .= " where SessionOtherUserID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در سایر کاربران");
		manage_SessionHistory::Add($UniversitySessionID, $UpdateRecordID, "USER", "", "EDIT");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_SessionOtherUsers();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.SessionOtherUsers where SessionOtherUserID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));

		$query = "delete from sessionmanagement.PersonPermissionsOnFields where PersonID=".$obj->PersonID." and RecID=".$obj->UniversitySessionID." and TableName='UniversitySessions'";
		$mysql->Execute($query);
		
		$query = "delete from sessionmanagement.PersonPermissionsOnTable where PersonID=".$obj->PersonID." and RecID=".$obj->UniversitySessionID." and TableName='UniversitySessions'";
		$mysql->Execute($query);
		
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از سایر کاربران");
		manage_SessionHistory::Add($UniversitySessionID, $RemoveRecordID, "USER", "", "REMOVE");
	}
	static function GetList($UniversitySessionID, $OrderByFieldName, $OrderType)
	{
		if(strtoupper($OrderType)!="ASC" && strtoupper($OrderType)!="DESC")
			$OrderType = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SessionOtherUsers.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName from sessionmanagement.SessionOtherUsers 
			LEFT JOIN hrmstotal.persons persons2 on (persons2.PersonID=SessionOtherUsers.PersonID)  ";
		$query .= " where UniversitySessionID=? ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionOtherUsers();
			$ret[$k]->SessionOtherUserID=$rec["SessionOtherUserID"];
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
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
		$obj = new be_SessionOtherUsers();
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
		if($CurrentPageName=="NewSessionOtherUsers")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='NewSessionOtherUsers.php?UpdateID=".$RecID."'>مشخصات اصلی</a></td>";
		$ret .= "</table>";
		return $ret;
	}
}
?>
