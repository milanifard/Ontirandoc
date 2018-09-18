<?php
/*
 تعریف کلاسها و متدهای مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/

/*
کلاس پایه: 
*/
class be_FacilityPages
{
	public $FacilityPageID;		//
	public $FacilityID;		//
	public $PageName;		//

	function be_FacilityPages() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select FacilityPages.* from projectmanagement.FacilityPages  where  FacilityPages.FacilityPageID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->FacilityPageID=$rec["FacilityPageID"];
			$this->FacilityID=$rec["FacilityID"];
			$this->PageName=$rec["PageName"];
		}
	}
}
/*
کلاس مدیریت 
*/
class manage_FacilityPages
{
	static function GetCount($FacilityID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(FacilityPageID) as TotalCount from projectmanagement.FacilityPages";
			$query .= " where FacilityID='".$FacilityID."'";
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
		$query = "select max(FacilityPageID) as MaxID from projectmanagement.FacilityPages";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $FacilityID: امکان
	* @param $PageName: صفحه
	* @return کد داده اضافه شده	*/
	static function Add($FacilityID, $PageName)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.FacilityPages (";
		$query .= " FacilityID";
		$query .= ", PageName";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $FacilityID); 
		array_push($ValueListArray, $PageName); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_FacilityPages::GetLastID();
		$mysql->audit("ثبت داده جدید در  با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PageName: صفحه
	* @return 	*/
	static function Update($UpdateRecordID, $PageName)
	{
		$k=0;
		$LogDesc = manage_FacilityPages::ComparePassedDataWithDB($UpdateRecordID, $PageName);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.FacilityPages set ";
			$query .= " PageName=? ";
		$query .= " where FacilityPageID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PageName); 
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
		$query = "delete from projectmanagement.FacilityPages where FacilityPageID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از ");
	}
	static function GetList($FacilityID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select FacilityPages.FacilityPageID
				,FacilityPages.FacilityID
				,FacilityPages.PageName from projectmanagement.FacilityPages  ";
		$query .= " where FacilityID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($FacilityID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_FacilityPages();
			$ret[$k]->FacilityPageID=$rec["FacilityPageID"];
			$ret[$k]->FacilityID=$rec["FacilityID"];
			$ret[$k]->PageName=$rec["PageName"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $PageName: صفحه
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $PageName)
	{
		$ret = "";
		$obj = new be_FacilityPages();
		$obj->LoadDataFromDatabase($CurRecID);
		if($PageName!=$obj->PageName)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "صفحه";
		}
		return $ret;
	}
}
?>