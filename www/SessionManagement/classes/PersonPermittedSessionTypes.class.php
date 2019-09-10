<?php
/*
 تعریف کلاسها و متدهای مربوط به : کاربران مجاز الگوهای جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-28
*/

/*
کلاس پایه: کاربران مجاز الگوهای جلسات
*/
class be_PersonPermittedSessionTypes
{
	public $PersonPermittedSessionTypeID;		//
	public $PersonID;		//کد شخصی
	public $PersonID_FullName;		/* نام و نام خانوادگی مربوط به فرد مجاز */
	public $SessionTypeID;		//کد الگوی جلسه

	function be_PersonPermittedSessionTypes() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select PersonPermittedSessionTypes.* 
			, concat(persons1.pfname, ' ', persons1.plname) as persons1_FullName from sessionmanagement.PersonPermittedSessionTypes 
			LEFT JOIN projectmanagement.persons persons1 on (persons1.PersonID=PersonPermittedSessionTypes.PersonID)  where  PersonPermittedSessionTypes.PersonPermittedSessionTypeID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->PersonPermittedSessionTypeID=$rec["PersonPermittedSessionTypeID"];
			$this->PersonID=$rec["PersonID"];
			$this->PersonID_FullName=$rec["persons1_FullName"]; // محاسبه از روی جدول وابسته
			$this->SessionTypeID=$rec["SessionTypeID"];
		}
	}
}
/*
کلاس مدیریت کاربران مجاز الگوهای جلسات
*/
class manage_PersonPermittedSessionTypes
{
	static function GetCount($SessionTypeID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(PersonPermittedSessionTypeID) as TotalCount from sessionmanagement.PersonPermittedSessionTypes";
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
		$query = "select max(PersonPermittedSessionTypeID) as MaxID from sessionmanagement.PersonPermittedSessionTypes";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $PersonID: فرد مجاز
	* @param $SessionTypeID: کد الگو جلسه
	* @return کد داده اضافه شده	*/
	static function Add($PersonID, $SessionTypeID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.PersonPermittedSessionTypes (";
			if($k>0) 
				$query .= ", ";
			$query .= "PersonID";
			$k++; 
			if($k>0) 
				$query .= ", ";
			$query .= "SessionTypeID";
			$k++; 
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $SessionTypeID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_PersonPermittedSessionTypes::GetLastID();
		$mysql->audit("ثبت داده جدید در کاربران مجاز الگوهای جلسات با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PersonID: فرد مجاز
	* @return 	*/
	static function Update($UpdateRecordID, $PersonID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.PersonPermittedSessionTypes set ";
		if($k>0) 
			$query .= ", ";
		$query .= "PersonID=? ";
			$k++;
		$query .= " where PersonPermittedSessionTypeID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در کاربران مجاز الگوهای جلسات");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.PersonPermittedSessionTypes where PersonPermittedSessionTypeID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از کاربران مجاز الگوهای جلسات");
	}
	static function GetList($SessionTypeID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select PersonPermittedSessionTypes.* 
			, concat(persons1.pfname, ' ', persons1.plname) as persons1_FullName from sessionmanagement.PersonPermittedSessionTypes 
			LEFT JOIN projectmanagement.persons persons1 on (persons1.PersonID=PersonPermittedSessionTypes.PersonID)  ";
		$query .= " where SessionTypeID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($SessionTypeID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_PersonPermittedSessionTypes();
			$ret[$k]->PersonPermittedSessionTypeID=$rec["PersonPermittedSessionTypeID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons1_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->SessionTypeID=$rec["SessionTypeID"];
			$k++;
		}
		return $ret;
	}
}
?>
