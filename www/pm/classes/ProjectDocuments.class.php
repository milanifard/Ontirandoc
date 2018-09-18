<?php
/*
 تعریف کلاسها و متدهای مربوط به : مستندات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/

/*
کلاس پایه: مستندات
*/
class be_ProjectDocuments
{
	public $ProjectDocumentID;		//
	public $ProjectID;		//پروژه
	public $ProjectDocumentTypeID;		//نوع سند
	public $ProjectDocumentTypeID_Desc;		/* شرح مربوط به نوع سند */
	public $FileContent;		//فایل
	public $FileName;		//نام فایل
	public $description;		//شرح
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $CreateDate;		//تاریخ ایجاد
	public $CreateDate_Shamsi;		/* مقدار شمسی معادل با تاریخ ایجاد */

	function be_ProjectDocuments() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectDocuments.* 
			, p2.title  as p2_title 
			, concat(persons6.pfname, ' ', persons6.plname) as persons6_FullName 
			, concat(g2j(CreateDate), ' ', substr(CreateDate, 12,10)) as CreateDate_Shamsi from projectmanagement.ProjectDocuments 
			LEFT JOIN projectmanagement.ProjectDocumentTypes  p2 on (p2.ProjectDocumentTypeID=ProjectDocuments.ProjectDocumentTypeID) 
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=ProjectDocuments.CreatorID)  where  ProjectDocuments.ProjectDocumentID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectDocumentID=$rec["ProjectDocumentID"];
			$this->ProjectID=$rec["ProjectID"];
			$this->ProjectDocumentTypeID=$rec["ProjectDocumentTypeID"];
			$this->ProjectDocumentTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$this->FileName=$rec["FileName"];
			$this->description=$rec["description"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons6_FullName"]; // محاسبه از روی جدول وابسته
			$this->CreateDate=$rec["CreateDate"];
			$this->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
		}
	}
}
/*
کلاس مدیریت مستندات
*/
class manage_ProjectDocuments
{
	static function GetCount($ProjectID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(ProjectDocumentID) as TotalCount from projectmanagement.ProjectDocuments";
			$query .= " where ProjectID='".$ProjectID."'";
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
		$query = "select max(ProjectDocumentID) as MaxID from projectmanagement.ProjectDocuments";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectID: پروژه
	* @param $ProjectDocumentTypeID: نوع سند
	* @param $FileContent: فایل
	* @param $FileName: نام فایل
	* @param $description: شرح
	* @return کد داده اضافه شده	*/
	/*static function Add($ProjectID, $ProjectDocumentTypeID, $FileName, $description)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectDocuments (";
		$query .= " ProjectID";
		$query .= ", ProjectDocumentTypeID";
		$query .= ", FileName";
		$query .= ", description";
		$query .= ", CreatorID";
		$query .= ", CreateDate";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , now() ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		array_push($ValueListArray, $ProjectDocumentTypeID); 
		array_push($ValueListArray, $FileName); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectDocuments::GetLastID();
		//$mysql->audit("ثبت داده جدید در مستندات با کد ".$LastID);

		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($ProjectID, "", "DOCUMENT", $LastID, "ADD");
		
		return $LastID;
	}*/
	static function Add($ProjectID, $ProjectDocumentTypeID, $FileContent, $FileName, $description)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectDocuments (";
		$query .= " ProjectID";
		$query .= ", ProjectDocumentTypeID";
		$query .= ", FileContent";
		$query .= ", FileName";
		$query .= ", description";
		$query .= ", CreatorID";
		$query .= ", CreateDate";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , ? , now() ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		array_push($ValueListArray, $ProjectDocumentTypeID); 
		array_push($ValueListArray, $FileContent);
		array_push($ValueListArray, $FileName); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectDocuments::GetLastID();
		//$mysql->audit("ثبت داده جدید در مستندات با کد ".$LastID);

		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$mysql = dbclass::getInstance();
			$query = "update projectmanagement.ProjectDocuments set ";
			$query .= " FileContent='".$FileContent."' ";
			$query .= " where ProjectDocumentID='".$LastID."'";
			$mysql->Execute($query);
 		}
				
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($ProjectID, "", "DOCUMENT", $LastID, "ADD");
		
		return $LastID;
	}

	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $ProjectDocumentTypeID: نوع سند
	* @param $FileContent: فایل
	* @param $FileName: نام فایل
	* @param $description: شرح
	* @return 	*/
	static function Update($UpdateRecordID, $ProjectDocumentTypeID, $FileName, $description)
	{
		$obj = new be_ProjectDocuments();
		$obj->LoadDataFromDatabase($UpdateRecordID);		
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectDocuments set ";
			$query .= " ProjectDocumentTypeID=? ";
		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", FileName=? ";
		}
			$query .= ", description=? ";
		$query .= " where ProjectDocumentID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectDocumentTypeID); 
		if($FileName!="")
		{ 
			array_push($ValueListArray, $FileName); 
		} 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);

		
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در مستندات");
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($obj->ProjectID, "", "DOCUMENT", $UpdateRecordID, "UPDATE");
		
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectDocuments();
		$obj->LoadDataFromDatabase($RemoveRecordID);		
		
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectDocuments where ProjectDocumentID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از مستندات");
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($obj->ProjectID, "", "DOCUMENT", $RemoveRecordID, "REMOVE");
		
	}
	static function GetList($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectDocuments.ProjectDocumentID
				,ProjectDocuments.ProjectID
				,ProjectDocuments.ProjectDocumentTypeID
				,ProjectDocuments.FileName
				,ProjectDocuments.description
				,ProjectDocuments.CreatorID
				,ProjectDocuments.CreateDate
			, p2.title  as p2_title 
			, concat(persons6.pfname, ' ', persons6.plname) as persons6_FullName 
			, concat(g2j(CreateDate), ' ', substr(CreateDate, 12, 10)) as CreateDate_Shamsi  from projectmanagement.ProjectDocuments 
			LEFT JOIN projectmanagement.ProjectDocumentTypes  p2 on (p2.ProjectDocumentTypeID=ProjectDocuments.ProjectDocumentTypeID) 
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=ProjectDocuments.CreatorID)  ";
		$query .= " where ProjectDocuments.ProjectID=? ";
		$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $ProjectID);
		if($ppc->GetPermission("View_ProjectDocuments")=="PRIVATE")
				$query .= " and ProjectDocuments.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectDocuments")=="NONE")
				$query .= " and 0=1 ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectDocuments();
			$ret[$k]->ProjectDocumentID=$rec["ProjectDocumentID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->ProjectDocumentTypeID=$rec["ProjectDocumentTypeID"];
			$ret[$k]->ProjectDocumentTypeID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->FileName=$rec["FileName"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons6_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$k++;
		}
		return $ret;
	}
}
?>
