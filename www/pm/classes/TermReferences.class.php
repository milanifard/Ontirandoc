<?php
/*
 تعریف کلاسها و متدهای مربوط به : مراجع اصطلاحات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-6
*/

/*
کلاس پایه: مراجع اصطلاحات
*/
class be_TermReferences
{
	public $TermReferenceID;		//
	public $title;		//عنوان
	public $FileContent;		//فایل
	public $RelatedFileName;		//

	function be_TermReferences() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select TermReferences.* from projectmanagement.TermReferences  where  TermReferences.TermReferenceID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->TermReferenceID=$rec["TermReferenceID"];
			$this->title=$rec["title"];
			$this->FileContent=$rec["FileContent"];
			$this->RelatedFileName=$rec["RelatedFileName"];
		}
	}
}
/*
کلاس مدیریت مراجع اصطلاحات
*/
class manage_TermReferences
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = "select count(TermReferenceID) as TotalCount from projectmanagement.TermReferences";
		if($WhereCondition!="")
		{
			$query .= " where ".$WhereCondition;
		}
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
		$query = "select max(TermReferenceID) as MaxID from projectmanagement.TermReferences";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $title: عنوان
	* @param $FileContent: فایل
	* @param $RelatedFileName: نام فایل
	* @return کد داده اضافه شده	*/
	static function Add($title, $FileContent, $RelatedFileName)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.TermReferences (";
		$query .= " title";
		$query .= ", FileContent";
		$query .= ", RelatedFileName";
		$query .= ") values (";
		$query .= "? , '".$FileContent."', ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $title); 
		array_push($ValueListArray, $RelatedFileName); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_TermReferences::GetLastID();
		$mysql->audit("ثبت داده جدید در مراجع اصطلاحات با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $title: عنوان
	* @param $FileContent: فایل
	* @param $RelatedFileName: نام فایل
	* @return 	*/
	static function Update($UpdateRecordID, $title, $FileContent, $RelatedFileName)
	{
		$k=0;
		$LogDesc = manage_TermReferences::ComparePassedDataWithDB($UpdateRecordID, $title, $FileContent, $RelatedFileName);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.TermReferences set ";
			$query .= " title=? ";
		if($RelatedFileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", RelatedFileName=?, FileContent='".$FileContent."' ";
		}
		$query .= " where TermReferenceID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $title); 
		if($RelatedFileName!="")
		{ 
			array_push($ValueListArray, $RelatedFileName); 
		} 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در مراجع اصطلاحات - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.TermReferences where TermReferenceID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.TermReferenceMapping where TermReferenceID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از مراجع اصطلاحات");
	}
	static function GetList()
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select TermReferences.TermReferenceID
				,TermReferences.title
				,TermReferences.FileContent
				,TermReferences.RelatedFileName from projectmanagement.TermReferences  ";
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_TermReferences();
			$ret[$k]->TermReferenceID=$rec["TermReferenceID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->FileContent=$rec["FileContent"];
			$ret[$k]->RelatedFileName=$rec["RelatedFileName"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $title: عنوان
	* @param $FileContent: فایل
	* @param $RelatedFileName: نام فایل
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $title, $FileContent, $RelatedFileName)
	{
		$ret = "";
		$obj = new be_TermReferences();
		$obj->LoadDataFromDatabase($CurRecID);
		if($title!=$obj->title)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($FileContent!=$obj->FileContent)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "فایل";
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
		$obj = new be_TermReferences();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>عنوان: </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->title, ENT_QUOTES, 'UTF-8');
		$ret .= "</td>";
		$ret .= "</tr>";
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
		$ret .= "<td width=\"50%\" ";
		if($CurrentPageName=="NewTermReferences")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageTermReferences.php'>لیست منابع</a></td>";
		$ret .= "<td width=\"50%\" ";
		if($CurrentPageName=="ManageTermReferenceMapping")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= ">ارتباط اصطلاحات و منابع</td>";
		$ret .= "</table>";
		return $ret;
	}
}
?>