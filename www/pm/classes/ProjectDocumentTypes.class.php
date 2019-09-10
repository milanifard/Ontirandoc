<?php
/*
 تعریف کلاسها و متدهای مربوط به : انواع سند پروژه ها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/

/*
کلاس پایه: انواع سند پروژه ها
*/
class be_ProjectDocumentTypes
{
	public $ProjectDocumentTypeID;		//
	public $title;		//عنوان
	public $ProjectID;		//پروژه مربوطه
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $RelatedDocumentsCount; // تعداد اسناد از این نوع

	function be_ProjectDocumentTypes() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectDocumentTypes.ProjectDocumentTypeID
				,ProjectDocumentTypes.title
				,ProjectDocumentTypes.ProjectID
				,ProjectDocumentTypes.CreatorID
				, (select count(*) from projectmanagement.ProjectDocuments where ProjectDocumentTypeID=ProjectDocumentTypes.ProjectDocumentTypeID) as RelatedDocumentsCount
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName  from projectmanagement.ProjectDocumentTypes 
			LEFT JOIN projectmanagement.persons persons3 on (persons3.PersonID=ProjectDocumentTypes.CreatorID) 
			where  ProjectDocumentTypes.ProjectDocumentTypeID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectDocumentTypeID=$rec["ProjectDocumentTypeID"];
			$this->title=$rec["title"];
			$this->ProjectID=$rec["ProjectID"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$this->RelatedDocumentsCount=$rec["RelatedDocumentsCount"];
		}
	}
}
/*
کلاس مدیریت انواع سند پروژه ها
*/
class manage_ProjectDocumentTypes
{
	static function GetCount($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectDocumentTypeID) as TotalCount from projectmanagement.ProjectDocumentTypes";
			$query .= " where ProjectID='".$ProjectID."'";
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
		$query = "select max(ProjectDocumentTypeID) as MaxID from projectmanagement.ProjectDocumentTypes";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $title: عنوان
	* @param $ProjectID: پروژه مربوطه
	* @return کد داده اضافه شده	*/
	static function Add($title, $ProjectID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectDocumentTypes (";
		$query .= " title";
		$query .= ", ProjectID";
		$query .= ", CreatorID";
		$query .= ") values (";
		$query .= "? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $title); 
		array_push($ValueListArray, $ProjectID); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectDocumentTypes::GetLastID();
		//$mysql->audit("ثبت داده جدید در انواع سند پروژه ها با کد ".$LastID);
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($ProjectID, "", "DOCUMENT_TYPE", $LastID, "ADD");
		
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $title: عنوان
	* @return 	*/
	static function Update($UpdateRecordID, $title)
	{
		$obj = new be_ProjectDocumentTypes();
		$obj->LoadDataFromDatabase($UpdateRecordID);		
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectDocumentTypes set ";
			$query .= " title=? ";
		$query .= " where ProjectDocumentTypeID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $title); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در انواع سند پروژه ها");
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($obj->ProjectID, "", "DOCUMENT_TYPE", $UpdateRecordID, "UPDATE");
		
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectDocumentTypes();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		if($obj->RelatedDocumentsCount==0)
		{
			$mysql = pdodb::getInstance();
			$query = "delete from projectmanagement.ProjectDocumentTypes where ProjectDocumentTypeID=?";
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array($RemoveRecordID));
			//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از انواع سند پروژه ها");
			require_once("ProjectHistory.class.php");
			manage_ProjectHistory::Add($obj->ProjectID, "", "DOCUMENT_TYPE", $RemoveRecordID, "REMOVE");
			return true;
		}
		return false;
	}
	static function GetList($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectDocumentTypes.ProjectDocumentTypeID
				,ProjectDocumentTypes.title
				,ProjectDocumentTypes.ProjectID
				,ProjectDocumentTypes.CreatorID
				, (select count(*) from projectmanagement.ProjectDocuments where ProjectDocumentTypeID=ProjectDocumentTypes.ProjectDocumentTypeID) as RelatedDocumentsCount
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName  from projectmanagement.ProjectDocumentTypes 
			LEFT JOIN projectmanagement.persons persons3 on (persons3.PersonID=ProjectDocumentTypes.CreatorID)  ";
		$query .= " where ProjectID=? ";
		$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $ProjectID);
		if($ppc->GetPermission("View_ProjectDocumentTypes")=="PRIVATE")
				$query .= " and ProjectDocumentTypes.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectDocumentTypes")=="NONE")
				$query .= " and 0=1 ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectDocumentTypes();
			$ret[$k]->ProjectDocumentTypeID=$rec["ProjectDocumentTypeID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->RelatedDocumentsCount=$rec["RelatedDocumentsCount"];
			$k++;
		}
		return $ret;
	}
	
	static function CreateSelectOptions($ProjectID)
	{
		$ret = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectDocumentTypes.ProjectDocumentTypeID
				,ProjectDocumentTypes.title
				from projectmanagement.ProjectDocumentTypes 
				where ProjectID=? or ProjectID=0 order by title";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["ProjectDocumentTypeID"]."'>".$rec["title"];
		}
		return $ret;
	}
}
?>
