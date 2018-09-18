<?php
/*
 تعریف کلاسها و متدهای مربوط به : انواع اقدامات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/

/*
کلاس پایه: انواع اقدامات
*/
class be_ProjectTaskActivityTypes
{
	public $ProjectTaskActivityTypeID;		//
	public $title;		//عنوان
	public $ProjectID;		//پروژه مربوطه
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $RelatedActivityCount; // تعداد اقدامات
	
	function be_ProjectTaskActivityTypes() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTaskActivityTypes.* 
			, (select count(*) from projectmanagement.ProjectTaskActivities where ProjectTaskActivityTypeID=ProjectTaskActivityTypes.ProjectTaskActivityTypeID) as RelatedActivityCount
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName from projectmanagement.ProjectTaskActivityTypes 
			LEFT JOIN projectmanagement.persons persons3 on (persons3.PersonID=ProjectTaskActivityTypes.CreatorID)  where  ProjectTaskActivityTypes.ProjectTaskActivityTypeID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectTaskActivityTypeID=$rec["ProjectTaskActivityTypeID"];
			$this->title=$rec["title"];
			$this->ProjectID=$rec["ProjectID"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$this->RelatedActivityCount=$rec["RelatedActivityCount"];
		}
	}
}
/*
کلاس مدیریت انواع اقدامات
*/
class manage_ProjectTaskActivityTypes
{
	static function GetCount($ProjectID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(ProjectTaskActivityTypeID) as TotalCount from projectmanagement.ProjectTaskActivityTypes";
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
		$query = "select max(ProjectTaskActivityTypeID) as MaxID from projectmanagement.ProjectTaskActivityTypes";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
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
		$query = "insert into projectmanagement.ProjectTaskActivityTypes (";
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
		$LastID = manage_ProjectTaskActivityTypes::GetLastID();
		//$mysql->audit("ثبت داده جدید در انواع اقدامات با کد ".$LastID);
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($ProjectID, "", "ACTIVITY_TYPE", $LastID, "ADD");
		
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $title: عنوان
	* @return 	*/
	static function Update($UpdateRecordID, $title)
	{
		$obj = new be_ProjectTaskActivityTypes();
		$obj->LoadDataFromDatabase($UpdateRecordID);		
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectTaskActivityTypes set ";
			$query .= " title=? ";
		$query .= " where ProjectTaskActivityTypeID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $title); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در انواع اقدامات");
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($obj->ProjectID, "", "ACTIVITY_TYPE", $UpdateRecordID, "UPDATE");
		
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectTaskActivityTypes();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		if($obj->RelatedActivityCount==0)
		{
			$mysql = pdodb::getInstance();
			$query = "delete from projectmanagement.ProjectTaskActivityTypes where ProjectTaskActivityTypeID=?";
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array($RemoveRecordID));
			//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از انواع اقدامات");
			require_once("ProjectHistory.class.php");
			manage_ProjectHistory::Add($obj->ProjectID, "", "ACTIVITY_TYPE", $RemoveRecordID, "REMOVE");
			return true;
		}
		return false;
	}
	static function GetList($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskActivityTypes.ProjectTaskActivityTypeID
				,ProjectTaskActivityTypes.title
				,ProjectTaskActivityTypes.ProjectID
				,ProjectTaskActivityTypes.CreatorID
				,(select count(*) from projectmanagement.ProjectTaskActivities where ProjectTaskActivityTypeID=ProjectTaskActivityTypes.ProjectTaskActivityTypeID) as RelatedActivityCount
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName  from projectmanagement.ProjectTaskActivityTypes 
			LEFT JOIN projectmanagement.persons persons3 on (persons3.PersonID=ProjectTaskActivityTypes.CreatorID)  ";
		$query .= " where ProjectID=? ";
		$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $ProjectID);
		if($ppc->GetPermission("View_ProjectTaskActivityTypes")=="PRIVATE")
				$query .= " and ProjectTaskActivityTypes.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectTaskActivityTypes")=="NONE")
				$query .= " and 0=1 ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskActivityTypes();
			$ret[$k]->ProjectTaskActivityTypeID=$rec["ProjectTaskActivityTypeID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->RelatedActivityCount=$rec["RelatedActivityCount"];
			$k++;
		}
		return $ret;
	}

	static function CreateSelectOptions($ProjectID)
	{
		$ret = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$query = "select ProjectTaskActivityTypes.ProjectTaskActivityTypeID
				,ProjectTaskActivityTypes.title
				from projectmanagement.ProjectTaskActivityTypes 
				where ProjectID=? or ProjectID=0 order by title";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["ProjectTaskActivityTypeID"]."'>".$rec["title"];
		}
		return $ret;
	}
	
}
?>
