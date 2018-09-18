<?php
/*
 تعریف کلاسها و متدهای مربوط به : پیشنیازها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/

/*
کلاس پایه: پیشنیازها
*/
class be_ProjectTaskRequisites
{
	public $ProjectTaskRequisiteID;		//
	public $ProjectTaskID;		//کار مربوطه
	public $RequisiteTaskID;		//کار پیشنیاز
	public $RequisiteTaskID_Desc;		/* شرح مربوط به کار پیشنیاز */
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */

	function be_ProjectTaskRequisites() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTaskRequisites.* 
			, p2.title  as p2_title 
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName from projectmanagement.ProjectTaskRequisites 
			LEFT JOIN projectmanagement.ProjectTasks  p2 on (p2.ProjectTaskID=ProjectTaskRequisites.RequisiteTaskID) 
			LEFT JOIN projectmanagement.persons persons3 on (persons3.PersonID=ProjectTaskRequisites.CreatorID)  where  ProjectTaskRequisites.ProjectTaskRequisiteID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectTaskRequisiteID=$rec["ProjectTaskRequisiteID"];
			$this->ProjectTaskID=$rec["ProjectTaskID"];
			$this->RequisiteTaskID=$rec["RequisiteTaskID"];
			$this->RequisiteTaskID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت پیشنیازها
*/
class manage_ProjectTaskRequisites
{
	static function GetCount($ProjectTaskID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(ProjectTaskRequisiteID) as TotalCount from projectmanagement.ProjectTaskRequisites";
			$query .= " where ProjectTaskID='".$ProjectTaskID."'";
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
		$query = "select max(ProjectTaskRequisiteID) as MaxID from projectmanagement.ProjectTaskRequisites";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectTaskID: کار مربوطه
	* @param $RequisiteTaskID: کار پیشنیاز
	* @return کد داده اضافه شده	*/
	static function Add($ProjectTaskID, $RequisiteTaskID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectTaskRequisites (";
		$query .= " ProjectTaskID";
		$query .= ", RequisiteTaskID";
		$query .= ", CreatorID";
		$query .= ") values (";
		$query .= "? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectTaskID); 
		array_push($ValueListArray, $RequisiteTaskID); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectTaskRequisites::GetLastID();
		//$mysql->audit("ثبت داده جدید در پیشنیازها با کد ".$LastID);
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($ProjectTaskID, "", "REQUISITE", $LastID, "ADD");
				
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $RequisiteTaskID: کار پیشنیاز
	* @return 	*/
	static function Update($UpdateRecordID, $RequisiteTaskID)
	{
		$obj = new be_ProjectTaskRequisites();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectTaskRequisites set ";
			$query .= " RequisiteTaskID=? ";
		$query .= " where ProjectTaskRequisiteID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $RequisiteTaskID); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پیشنیازها");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "REQUISITE", $UpdateRecordID, "UPDATE");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectTaskRequisites();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectTaskRequisites where ProjectTaskRequisiteID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پیشنیازها");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "REQUISITE", $RemoveRecordID, "DELETE");
		
	}
	static function GetList($ProjectTaskID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskRequisites.ProjectTaskRequisiteID
				,ProjectTaskRequisites.ProjectTaskID
				,ProjectTaskRequisites.RequisiteTaskID
				,ProjectTaskRequisites.CreatorID
			, p2.title  as p2_title 
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName  from projectmanagement.ProjectTaskRequisites 
			LEFT JOIN projectmanagement.ProjectTasks  p2 on (p2.ProjectTaskID=ProjectTaskRequisites.RequisiteTaskID) 
			LEFT JOIN projectmanagement.persons persons3 on (persons3.PersonID=ProjectTaskRequisites.CreatorID)  ";
		$query .= " where ProjectTaskRequisites.ProjectTaskID=? ";
		$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $ProjectTaskID);
		if($ppc->GetPermission("View_ProjectTaskRequisites")=="PRIVATE")
				$query .= " and ProjectTaskRequisites.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectTaskRequisites")=="NONE")
				$query .= " and 0=1 ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectTaskID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskRequisites();
			$ret[$k]->ProjectTaskRequisiteID=$rec["ProjectTaskRequisiteID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->RequisiteTaskID=$rec["RequisiteTaskID"];
			$ret[$k]->RequisiteTaskID_Desc=$rec["p2_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}
}
?>
