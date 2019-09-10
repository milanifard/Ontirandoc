<?php
/*
 تعریف کلاسها و متدهای مربوط به : تاریخهای مهم
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/

/*
کلاس پایه: تاریخهای مهم
*/
class be_ProjectMilestones
{
	public $ProjectMilestoneID;		//
	public $ProjectID;		//پروژه مربوطه
	public $MilestoneDate;		//تاریخ
	public $MilestoneDate_Shamsi;		/* مقدار شمسی معادل با تاریخ */
	public $description;		//شرح
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */

	function be_ProjectMilestones() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectMilestones.* 
			, g2j(MilestoneDate) as MilestoneDate_Shamsi 
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName from projectmanagement.ProjectMilestones 
			LEFT JOIN projectmanagement.persons persons4 on (persons4.PersonID=ProjectMilestones.CreatorID)  where  ProjectMilestones.ProjectMilestoneID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectMilestoneID=$rec["ProjectMilestoneID"];
			$this->ProjectID=$rec["ProjectID"];
			$this->MilestoneDate=$rec["MilestoneDate"];
			$this->MilestoneDate_Shamsi=$rec["MilestoneDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->description=$rec["description"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت تاریخهای مهم
*/
class manage_ProjectMilestones
{
	static function GetCount($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectMilestoneID) as TotalCount from projectmanagement.ProjectMilestones";
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
		$query = "select max(ProjectMilestoneID) as MaxID from projectmanagement.ProjectMilestones";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectID: پروژه مربوطه
	* @param $MilestoneDate: تاریخ
	* @param $description: شرح
	* @return کد داده اضافه شده	*/
	static function Add($ProjectID, $MilestoneDate, $description)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectMilestones (";
		$query .= " ProjectID";
		$query .= ", MilestoneDate";
		$query .= ", description";
		$query .= ", CreatorID";
		$query .= ") values (";
		$query .= "? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		array_push($ValueListArray, $MilestoneDate); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectMilestones::GetLastID();
		//$mysql->audit("ثبت داده جدید در تاریخهای مهم با کد ".$LastID);
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($ProjectID, "", "MILESTONE", $LastID, "ADD");
		
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $MilestoneDate: تاریخ
	* @param $description: شرح
	* @return 	*/
	static function Update($UpdateRecordID, $MilestoneDate, $description)
	{
		$obj = new be_ProjectMilestones();
		$obj->LoadDataFromDatabase($UpdateRecordID);		
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectMilestones set ";
			$query .= " MilestoneDate=? ";
			$query .= ", description=? ";
		$query .= " where ProjectMilestoneID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $MilestoneDate); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در تاریخهای مهم");
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($obj->ProjectID, "", "MILESTONE", $UpdateRecordID, "UPDATE");
		
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectMilestones();
		$obj->LoadDataFromDatabase($RemoveRecordID);		
		
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectMilestones where ProjectMilestoneID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از تاریخهای مهم");
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($obj->ProjectID, "", "MILESTONE", $RemoveRecordID, "REMOVE");
		
	}
	static function GetList($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectMilestones.ProjectMilestoneID
				,ProjectMilestones.ProjectID
				,ProjectMilestones.MilestoneDate
				,ProjectMilestones.description
				,ProjectMilestones.CreatorID
			, g2j(MilestoneDate) as MilestoneDate_Shamsi 
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName  from projectmanagement.ProjectMilestones 
			LEFT JOIN projectmanagement.persons persons4 on (persons4.PersonID=ProjectMilestones.CreatorID)  ";
		$query .= " where ProjectID=? ";
		require_once('projectsSecurity.class.php');
		$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $ProjectID);
		if($ppc->GetPermission("View_ProjectMilestones")=="PRIVATE")
				$query .= " and ProjectMilestones.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectMilestones")=="NONE")
				$query .= " and 0=1 ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectMilestones();
			$ret[$k]->ProjectMilestoneID=$rec["ProjectMilestoneID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->MilestoneDate=$rec["MilestoneDate"];
			$ret[$k]->MilestoneDate_Shamsi=$rec["MilestoneDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->description=$rec["description"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}
}
?>
