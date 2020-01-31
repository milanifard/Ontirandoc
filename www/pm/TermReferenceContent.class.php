<?php
/*
 تعریف کلاسها و متدهای مربوط به : محتوای پاراگرافهای مراجع
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-5-4
*/

/*
کلاس پایه: محتوای پاراگرافهای مراجع
*/
class be_TermReferenceContent
{
	public $TermReferenceContentID;		//
	public $TermReferenceID;		//
	public $PageNum;		//
	public $content;		//

	function be_TermReferenceContent() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select TermReferenceContent.* from projectmanagement.TermReferenceContent  where  TermReferenceContent.TermReferenceContentID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch()) {
			$this->TermReferenceContentID=$rec["TermReferenceContentID"];
			$this->TermReferenceID=$rec["TermReferenceID"];
			$this->PageNum=$rec["PageNum"];
			$this->content=$rec["content"];
		}
	}
}
/*
کلاس مدیریت محتوای پاراگرافهای مراجع
*/
class manage_TermReferenceContent
{
	static function GetContent($TermReferenceID, $PageNum)
	{
		$mysql = pdodb::getInstance();
		$query = "select content from projectmanagement.TermReferenceContent ";
		$query .= " where TermReferenceID=? and PageNum=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement($TermReferenceID, $PageNum);
		if($rec = $res->fetch()){
			return $rec["content"];
		} else {
			return "";
		}
	}
	
	static function GetCount($TermReferenceID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(TermReferenceContentID) as TotalCount from projectmanagement.TermReferenceContent";
		$query .= " where TermReferenceID='".$TermReferenceID."'";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow()) {
			return $rec["TotalCount"];
		} else {
			return 0;
		}
	}
	static function GetLastID()
	{
		$mysql = dbclass::getInstance();
		$query = "select max(TermReferenceContentID) as MaxID from projectmanagement.TermReferenceContent";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow()) {
			return $rec["MaxID"];
		} else {
			return -1;
		}
		
	}
	/**
	* @param $TermReferenceID: 
	* @param $PageNum: صفحه
	* @param $content: محتوا
	* @return کد داده اضافه شده	*/
	static function Add($TermReferenceID, $PageNum, $content)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.TermReferenceContent (";
		$query .= " TermReferenceID";
		$query .= ", PageNum";
		$query .= ", content";
		$query .= ") values (";
		$query .= "? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $TermReferenceID); 
		array_push($ValueListArray, $PageNum); 
		array_push($ValueListArray, $content); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_TermReferenceContent::GetLastID();
		$mysql->audit("ثبت داده جدید در محتوای پاراگرافهای مراجع با کد ".$LastID);

		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PageNum: صفحه
	* @param $content: محتوا
	* @return 	*/
	static function Update($UpdateRecordID, $PageNum, $content)
	{
		$k=0;
		$LogDesc = manage_TermReferenceContent::ComparePassedDataWithDB($UpdateRecordID, $PageNum, $content);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.TermReferenceContent set ";
		$query .= " PageNum=? ";
		$query .= ", content=? ";
		$query .= " where TermReferenceContentID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PageNum); 
		array_push($ValueListArray, $content); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در محتوای پاراگرافهای مراجع - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.TermReferenceContent where TermReferenceContentID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از محتوای پاراگرافهای مراجع");
	}
	static function GetList($TermReferenceID, $FromRec, $NumberOfRec)
	{
		if(!is_numeric($FromRec)){
			$FromRec = 0;
		} 

		if(!is_numeric($NumberOfRec)) {
			$NumberOfRec = 0;
		}
			
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select TermReferenceContent.TermReferenceContentID
				,TermReferenceContent.TermReferenceID
				,TermReferenceContent.PageNum
				,TermReferenceContent.content from projectmanagement.TermReferenceContent  ";
		$query .= " where TermReferenceID=? ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($TermReferenceID));
		$i=0;
		while($rec=$res->fetch()) {
			$ret[$k] = new be_TermReferenceContent();
			$ret[$k]->TermReferenceContentID=$rec["TermReferenceContentID"];
			$ret[$k]->TermReferenceID=$rec["TermReferenceID"];
			$ret[$k]->PageNum=$rec["PageNum"];
			$ret[$k]->content=$rec["content"];
			$k++;
		}

		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $PageNum: صفحه
	* @param $content: محتوا
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $PageNum, $content)
	{
		$ret = "";
		$obj = new be_TermReferenceContent();
		$obj->LoadDataFromDatabase($CurRecID);
		if($PageNum!=$obj->PageNum) {
			if($ret!=""){
				$ret .= " - ";
			}
			$ret .= "صفحه";
		}

		if($content!=$obj->content) {
			if($ret!="") {
				$ret .= " - ";
			}
			$ret .= "محتوا";
		} else {
			return $ret;
		}
		
	}
}
?>