<?php
/*
 تعریف کلاسها و متدهای مربوط به : مستندات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-3
*/

/*
کلاس پایه: مستندات
*/
require_once("SessionHistory.class.php");
class be_SessionDocuments
{
	public $SessionDocumentID;		//
	public $UniversitySessionID;		//کد جلسه
	public $CreatorPersonID;		//کد شخص ایجاد کننده
	public $CreatorPersonID_FullName;		/* نام و نام خانوادگی مربوط به کد شخص ایجاد کننده */
	public $CreateTime;		//تاریخ ایجاد
	public $CreateTime_Shamsi;		/* مقدار شمسی معادل با تاریخ ایجاد */
	public $DocumentFile;		//فایل
	public $DocumentFileName;		//نام فایل
	public $DocumentDescription;		//شرح
	public $InputOrOutput;		//نوع
	public $InputOrOutput_Desc;		/* شرح مربوط به نوع */

	function be_SessionDocuments() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SessionDocuments.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, concat(g2j(CreateTime), ' ', substr(CreateTime, 12,10)) as CreateTime_Shamsi 
			, CASE SessionDocuments.InputOrOutput 
				WHEN 'INPUT' THEN 'ورودی' 
				WHEN 'OUTPUT' THEN 'خروجی' 
				END as InputOrOutput_Desc from sessionmanagement.SessionDocuments 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=SessionDocuments.CreatorPersonID)  where  SessionDocuments.SessionDocumentID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->SessionDocumentID=$rec["SessionDocumentID"];
			$this->UniversitySessionID=$rec["UniversitySessionID"];
			$this->CreatorPersonID=$rec["CreatorPersonID"];
			$this->CreatorPersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$this->CreateTime=$rec["CreateTime"];
			$this->CreateTime_Shamsi=$rec["CreateTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->DocumentFile=$rec["DocumentFile"];
			$this->DocumentFileName=$rec["DocumentFileName"];
			$this->DocumentDescription=$rec["DocumentDescription"];
			$this->InputOrOutput=$rec["InputOrOutput"];
			$this->InputOrOutput_Desc=$rec["InputOrOutput_Desc"];  // محاسبه بر اساس لیست ثابت
		}
	}
}
/*
کلاس مدیریت مستندات
*/
class manage_SessionDocuments
{
	static function GetCount($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(SessionDocumentID) as TotalCount from sessionmanagement.SessionDocuments";
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
		$query = "select max(SessionDocumentID) as MaxID from sessionmanagement.SessionDocuments";
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
	* @param $DocumentFile: فایل
	* @param $DocumentFileName: نام فایل
	* @param $DocumentDescription: شرح
	* @param $InputOrOutput: نوع
	* @return کد داده اضافه شده	*/
	static function Add($UniversitySessionID, $DocumentFile, $DocumentFileName, $DocumentDescription, $InputOrOutput)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.SessionDocuments (";
		$query .= " UniversitySessionID";
		$query .= ", CreatorPersonID";
		$query .= ", CreateTime";
		$query .= ", DocumentFile";
		$query .= ", DocumentFileName";
		$query .= ", DocumentDescription";
		$query .= ", InputOrOutput";
		$query .= ") values (";
		$query .= "? , ? , now() , '".$DocumentFile."', ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $UniversitySessionID); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		array_push($ValueListArray, $DocumentFileName); 
		array_push($ValueListArray, $DocumentDescription); 
		array_push($ValueListArray, $InputOrOutput); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SessionDocuments::GetLastID();
		$mysql->audit("ثبت داده جدید در مستندات با کد ".$LastID);
		manage_SessionHistory::Add($UniversitySessionID, $LastID, "DOCUMENT", "", "ADD");
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $DocumentFile: فایل
	* @param $DocumentFileName: نام فایل
	* @param $DocumentDescription: شرح
	* @param $InputOrOutput: نوع
	* @return 	*/
	static function Update($UpdateRecordID, $DocumentFile, $DocumentFileName, $DocumentDescription, $InputOrOutput)
	{
		$obj = new be_SessionDocuments();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionDocuments set ";
		$query .= " DocumentDescription=? ";
		$query .= ", InputOrOutput=? ";
		
		if($DocumentFileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", DocumentFileName=?, DocumentFile='".$DocumentFile."' ";
		}
		$query .= " where SessionDocumentID=?";
		$ValueListArray = array();
		if($DocumentFileName!="")
		{ 
			array_push($ValueListArray, $DocumentFileName); 
		} 
		array_push($ValueListArray, $DocumentDescription); 
		array_push($ValueListArray, $InputOrOutput); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در مستندات");
		manage_SessionHistory::Add($UniversitySessionID, $UpdateRecordID, "DOCUMENT", "", "EDIT");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_SessionDocuments();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.SessionDocuments where SessionDocumentID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از مستندات");
		manage_SessionHistory::Add($UniversitySessionID, $RemoveRecordID, "DOCUMENT", "", "REMOVE");
	}
	static function GetList($UniversitySessionID, $OrderByFieldName, $OrderType)
	{
		if(strtoupper($OrderType)!="ASC" && strtoupper($OrderType)!="DESC")
			$OrderType = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SessionDocuments.SessionDocumentID
				,SessionDocuments.UniversitySessionID
				,SessionDocuments.CreatorPersonID
				,SessionDocuments.CreateTime
				,SessionDocuments.DocumentFile
				,SessionDocuments.DocumentFileName
				,SessionDocuments.DocumentDescription
				,SessionDocuments.InputOrOutput
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, concat(g2j(CreateTime), ' ', substr(CreateTime, 12, 10)) as CreateTime_Shamsi 
			, CASE SessionDocuments.InputOrOutput 
				WHEN 'INPUT' THEN 'ورودی' 
				WHEN 'OUTPUT' THEN 'خروجی' 
				END as InputOrOutput_Desc from sessionmanagement.SessionDocuments 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=SessionDocuments.CreatorPersonID)  ";
		$query .= " where UniversitySessionID=? ";
		$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $UniversitySessionID);
		if($ppc->GetPermission("View_SessionDocuments")=="PRIVATE")
				$query .= " and SessionDocuments.CreatorPersonID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_SessionDocuments")=="NONE")
				$query .= " and 0=1 ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionDocuments();
			$ret[$k]->SessionDocumentID=$rec["SessionDocumentID"];
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->CreatorPersonID=$rec["CreatorPersonID"];
			$ret[$k]->CreatorPersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->CreateTime=$rec["CreateTime"];
			$ret[$k]->CreateTime_Shamsi=$rec["CreateTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->DocumentFile=$rec["DocumentFile"];
			$ret[$k]->DocumentFileName=$rec["DocumentFileName"];
			$ret[$k]->DocumentDescription=$rec["DocumentDescription"];
			$ret[$k]->InputOrOutput=$rec["InputOrOutput"];
			$ret[$k]->InputOrOutput_Desc=$rec["InputOrOutput_Desc"];  // محاسبه بر اساس لیست ثابت
			$k++;
		}
		return $ret;
	}
}
?>
