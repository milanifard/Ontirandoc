<?php
/*
 تعریف کلاسها و متدهای مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-19
*/

/*
کلاس پایه: 
*/
class be_TermEquivalentEnglishTerms
{
	public $TermEquivalentEnglishTermID;		//
	public $TermID;		//اصطلاح
	public $EnglishTerm;		//معادل انگلیسی

	function be_TermEquivalentEnglishTerms() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select TermEquivalentEnglishTerms.* from projectmanagement.TermEquivalentEnglishTerms  where  TermEquivalentEnglishTerms.TermEquivalentEnglishTermID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->TermEquivalentEnglishTermID=$rec["TermEquivalentEnglishTermID"];
			$this->TermID=$rec["TermID"];
			$this->EnglishTerm=$rec["EnglishTerm"];
		}
	}
}
/*
کلاس مدیریت 
*/
class manage_TermEquivalentEnglishTerms
{
	static function GetCount($TermID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(TermEquivalentEnglishTermID) as TotalCount from projectmanagement.TermEquivalentEnglishTerms";
			$query .= " where TermID='".$TermID."'";
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
		$query = "select max(TermEquivalentEnglishTermID) as MaxID from projectmanagement.TermEquivalentEnglishTerms";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $TermID: اصطلاح
	* @param $EnglishTerm: معادل انگلیسی
	* @return کد داده اضافه شده	*/
	static function Add($TermID, $EnglishTerm)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.TermEquivalentEnglishTerms (";
		$query .= " TermID";
		$query .= ", EnglishTerm";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $TermID); 
		array_push($ValueListArray, $EnglishTerm); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_TermEquivalentEnglishTerms::GetLastID();
		$mysql->audit("ثبت داده جدید در  با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $EnglishTerm: معادل انگلیسی
	* @return 	*/
	static function Update($UpdateRecordID, $EnglishTerm)
	{
		$k=0;
		$LogDesc = manage_TermEquivalentEnglishTerms::ComparePassedDataWithDB($UpdateRecordID, $EnglishTerm);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.TermEquivalentEnglishTerms set ";
			$query .= " EnglishTerm=? ";
		$query .= " where TermEquivalentEnglishTermID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $EnglishTerm); 
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
		$query = "delete from projectmanagement.TermEquivalentEnglishTerms where TermEquivalentEnglishTermID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از ");
	}
	static function GetList($TermID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select TermEquivalentEnglishTerms.TermEquivalentEnglishTermID
				,TermEquivalentEnglishTerms.TermID
				,TermEquivalentEnglishTerms.EnglishTerm from projectmanagement.TermEquivalentEnglishTerms  ";
		$query .= " where TermID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($TermID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_TermEquivalentEnglishTerms();
			$ret[$k]->TermEquivalentEnglishTermID=$rec["TermEquivalentEnglishTermID"];
			$ret[$k]->TermID=$rec["TermID"];
			$ret[$k]->EnglishTerm=$rec["EnglishTerm"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $EnglishTerm: معادل انگلیسی
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $EnglishTerm)
	{
		$ret = "";
		$obj = new be_TermEquivalentEnglishTerms();
		$obj->LoadDataFromDatabase($CurRecID);
		if($EnglishTerm!=$obj->EnglishTerm)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "معادل انگلیسی";
		}
		return $ret;
	}
}
?>