<?php
/*
 تعریف کلاسها و متدهای مربوط به : انواع کارها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/

/*
کلاس پایه: انواع کارها
*/
class be_ProjectTaskTypes
{
	public $ProjectTaskTypeID;		//
	public $title;		//عنوان
	public $ProjectID;		//پروژه مربوطه
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $RelatedTaskCount; // تعداد کارهای از این نوع در پروژه
	
	function be_ProjectTaskTypes() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTaskTypes.* 
			, (select count(*) from projectmanagement.ProjectTasks where ProjectTaskTypeID=ProjectTaskTypes.ProjectTaskTypeID) as RelatedTaskCount
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName from projectmanagement.ProjectTaskTypes 
			LEFT JOIN projectmanagement.persons persons3 on (persons3.PersonID=ProjectTaskTypes.CreatorID)  where  ProjectTaskTypes.ProjectTaskTypeID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$this->title=$rec["title"];
			$this->ProjectID=$rec["ProjectID"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$this->RelatedTaskCount=$rec["RelatedTaskCount"];
		}
	}
}
/*
کلاس مدیریت انواع کارها
*/
class manage_ProjectTaskTypes
{
	static function GetCount($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectTaskTypeID) as TotalCount from projectmanagement.ProjectTaskTypes";
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
		$query = "select max(ProjectTaskTypeID) as MaxID from projectmanagement.ProjectTaskTypes";
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
		$query = "insert into projectmanagement.ProjectTaskTypes (";
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
		$LastID = manage_ProjectTaskTypes::GetLastID();
		//$mysql->audit("ثبت داده جدید در انواع کارها با کد ".$LastID);
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($ProjectID, "", "TASK_TYPE", $LastID, "ADD");
		
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $title: عنوان
	* @return 	*/
	static function Update($UpdateRecordID, $title)
	{
		$obj = new be_ProjectTaskTypes();
		$obj->LoadDataFromDatabase($UpdateRecordID);		
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectTaskTypes set ";
			$query .= " title=? ";
		$query .= " where ProjectTaskTypeID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $title); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در انواع کارها");
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($obj->ProjectID, "", "TASK_TYPE", $UpdateRecordID, "UPDATE");
		
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectTaskTypes();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		if($obj->RelatedTaskCount==0)
		{
			$mysql = pdodb::getInstance();
			$query = "delete from projectmanagement.ProjectTaskTypes where ProjectTaskTypeID=?";
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array($RemoveRecordID));
			//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از انواع کارها");
			require_once("ProjectHistory.class.php");
			manage_ProjectHistory::Add($obj->ProjectID, "", "TASK_TYPE", $RemoveRecordID, "REMOVE");
			
			return true;
		}
		return false;;
	}
	static function GetList($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskTypes.ProjectTaskTypeID
				,ProjectTaskTypes.title
				,ProjectTaskTypes.ProjectID
				,ProjectTaskTypes.CreatorID
				,(select count(*) from projectmanagement.ProjectTasks where ProjectTaskTypeID=ProjectTaskTypes.ProjectTaskTypeID) as RelatedTaskCount
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName  from projectmanagement.ProjectTaskTypes 
			LEFT JOIN projectmanagement.persons persons3 on (persons3.PersonID=ProjectTaskTypes.CreatorID)  ";
		$query .= " where ProjectID=? ";
		$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $ProjectID);
		if($ppc->GetPermission("View_ProjectTaskTypes")=="PRIVATE")
				$query .= " and ProjectTaskTypes.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectTaskTypes")=="NONE")
				$query .= " and 0=1 ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskTypes();
			$ret[$k]->ProjectTaskTypeID=$rec["ProjectTaskTypeID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->RelatedTaskCount=$rec["RelatedTaskCount"];
			$k++;
		}
		return $ret;
	}

	static function CreateSelectOptions($ProjectID, $CurValue)
	{
		$ret = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$query = "select ProjectTaskTypes.ProjectTaskTypeID
				,ProjectTaskTypes.title
				from projectmanagement.ProjectTaskTypes 
				where ProjectID=? or ProjectID=0 order by title";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["ProjectTaskTypeID"]."' ";
			if($rec["ProjectTaskTypeID"]==$CurValue)
				$ret .= " selected ";
			$ret .= ">".$rec["title"];
		}
		return $ret;
	}
	
}
?>
