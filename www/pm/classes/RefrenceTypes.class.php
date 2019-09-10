<?php
/*
 تعریف کلاسها و متدهای مربوط به : انواع مراجع
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-3-5
*/

/*
کلاس پایه: انواع مراجع
*/
class be_RefrenceTypes
{
	public $RefrenceTypeID;		//
	public $ResearchProjectID;		//
	public $RefrenceTypeTitle;		//عنوان

	function be_RefrenceTypes() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select RefrenceTypes.* from projectmanagement.RefrenceTypes  where  RefrenceTypes.RefrenceTypeID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->RefrenceTypeID=$rec["RefrenceTypeID"];
			$this->ResearchProjectID=$rec["ResearchProjectID"];
			$this->RefrenceTypeTitle=$rec["RefrenceTypeTitle"];
		}
	}
}
/*
کلاس مدیریت انواع مراجع
*/
class manage_RefrenceTypes
{
	static function GetCount($ResearchProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(RefrenceTypeID) as TotalCount from projectmanagement.RefrenceTypes";
			$query .= " where ResearchProjectID='".$ResearchProjectID."'";
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
		$query = "select max(RefrenceTypeID) as MaxID from projectmanagement.RefrenceTypes";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ResearchProjectID: 
	* @param $RefrenceTypeTitle: عنوان
	* @return کد داده اضافه شده	*/
	static function Add($ResearchProjectID, $RefrenceTypeTitle)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.RefrenceTypes (";
		$query .= " ResearchProjectID";
		$query .= ", RefrenceTypeTitle";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ResearchProjectID); 
		array_push($ValueListArray, $RefrenceTypeTitle); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_RefrenceTypes::GetLastID();
		$mysql->audit("ثبت داده جدید در انواع مراجع با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $RefrenceTypeTitle: عنوان
	* @return 	*/
	static function Update($UpdateRecordID, $RefrenceTypeTitle)
	{
		$k=0;
		$LogDesc = manage_RefrenceTypes::ComparePassedDataWithDB($UpdateRecordID, $RefrenceTypeTitle);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.RefrenceTypes set ";
			$query .= " RefrenceTypeTitle=? ";
		$query .= " where RefrenceTypeID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $RefrenceTypeTitle); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در انواع مراجع - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.RefrenceTypes where RefrenceTypeID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از انواع مراجع");
	}
	static function GetList($ResearchProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select RefrenceTypes.RefrenceTypeID
				,RefrenceTypes.ResearchProjectID
				,RefrenceTypes.RefrenceTypeTitle from projectmanagement.RefrenceTypes  ";
		$query .= " where ResearchProjectID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ResearchProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_RefrenceTypes();
			$ret[$k]->RefrenceTypeID=$rec["RefrenceTypeID"];
			$ret[$k]->ResearchProjectID=$rec["ResearchProjectID"];
			$ret[$k]->RefrenceTypeTitle=$rec["RefrenceTypeTitle"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $RefrenceTypeTitle: عنوان
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $RefrenceTypeTitle)
	{
		$ret = "";
		$obj = new be_RefrenceTypes();
		$obj->LoadDataFromDatabase($CurRecID);
		if($RefrenceTypeTitle!=$obj->RefrenceTypeTitle)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		return $ret;
	}
}
?>