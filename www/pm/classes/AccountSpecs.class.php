<?php
/*
 تعریف کلاسها و متدهای مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/

/*
کلاس پایه: 
*/
class be_AccountSpecs
{
	public $AccountSpecID;		//
	public $UserID;		//
	public $UserPassword;		//
	public $PersonID;		//
	public $PersonID_Desc;		/* شرح مربوط به نام و نام خانوادگی */

	function be_AccountSpecs() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select AccountSpecs.* 
			, concat(l3.pfname, ' ', l3.plname)  as l3_plname from projectmanagement.AccountSpecs 
			LEFT JOIN projectmanagement.persons  l3 on (l3.PersonID=AccountSpecs.PersonID)  where  AccountSpecs.AccountSpecID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->AccountSpecID=$rec["AccountSpecID"];
			$this->UserID=$rec["UserID"];
			$this->UserPassword=$rec["UserPassword"];
			$this->PersonID=$rec["PersonID"];
			$this->PersonID_Desc=$rec["l3_plname"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت 
*/
class manage_AccountSpecs
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(AccountSpecID) as TotalCount from projectmanagement.AccountSpecs";
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
	static function GetLastID()
	{
		$mysql = pdodb::getInstance();
		$query = "select max(AccountSpecID) as MaxID from projectmanagement.AccountSpecs";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $UserID: نام کاربری
	* @param $UserPassword: کلمه عبور
	* @param $PersonID: نام و نام خانوادگی
	* @return کد داده اضافه شده	*/
	static function Add($UserID, $UserPassword, $PersonID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.AccountSpecs (";
		$query .= " UserID";
		$query .= ", UserPassword";
		$query .= ", PersonID";
		$query .= ") values (";
		$query .= "? , sha1(md5(?)) , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $UserID); 
		array_push($ValueListArray, $UserPassword); 
		array_push($ValueListArray, $PersonID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_AccountSpecs::GetLastID();
		$mysql->audit("ثبت داده جدید در  با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $UserID: نام کاربری
	* @param $UserPassword: کلمه عبور
	* @param $PersonID: نام و نام خانوادگی
	* @return 	*/
	static function Update($UpdateRecordID, $UserID, $UserPassword, $PersonID)
	{
		$k=0;
		$LogDesc = manage_AccountSpecs::ComparePassedDataWithDB($UpdateRecordID, $UserID, $UserPassword, $PersonID);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.AccountSpecs set ";
			$query .= " UserID=? ";
			if($UserPassword!="")			
			  $query .= ", UserPassword=sha1(md5(?)) ";
			$query .= ", PersonID=? ";
		$query .= " where AccountSpecID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $UserID); 
		if($UserPassword!="")			
		  array_push($ValueListArray, $UserPassword); 
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در  - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.AccountSpecs where AccountSpecID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از ");
	}
	static function GetList()
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select AccountSpecs.AccountSpecID
				,AccountSpecs.UserID
				,AccountSpecs.UserPassword
				,AccountSpecs.PersonID
			, concat(l3.pfname, ' ', l3.plname)  as l3_plname  from projectmanagement.AccountSpecs 
			LEFT JOIN projectmanagement.persons  l3 on (l3.PersonID=AccountSpecs.PersonID)  ";
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_AccountSpecs();
			$ret[$k]->AccountSpecID=$rec["AccountSpecID"];
			$ret[$k]->UserID=$rec["UserID"];
			$ret[$k]->UserPassword=$rec["UserPassword"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_Desc=$rec["l3_plname"]; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}
	
	static function GetComboBoxOptions()
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = "";
		$query = "select AccountSpecs.AccountSpecID
				,AccountSpecs.UserID
				,AccountSpecs.UserPassword
				,AccountSpecs.PersonID
			, concat(l3.pfname, ' ', l3.plname)  as l3_plname  from projectmanagement.AccountSpecs 
			LEFT JOIN projectmanagement.persons  l3 on (l3.PersonID=AccountSpecs.PersonID)  ";
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->fetch())
		{
		  $ret .= "<option value='".$rec["PersonID"]."'>".$rec["l3_plname"];
		}
		return $ret;
	}	
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $UserID: نام کاربری
	* @param $UserPassword: کلمه عبور
	* @param $PersonID: نام و نام خانوادگی
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $UserID, $UserPassword, $PersonID)
	{
		$ret = "";
		$obj = new be_AccountSpecs();
		$obj->LoadDataFromDatabase($CurRecID);
		if($UserID!=$obj->UserID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نام کاربری";
		}
		if($UserPassword!=$obj->UserPassword)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "کلمه عبور";
		}
		if($PersonID!=$obj->PersonID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نام و نام خانوادگی";
		}
		return $ret;
	}
}
?>