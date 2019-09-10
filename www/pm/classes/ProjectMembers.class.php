<?php
/*
 تعریف کلاسها و متدهای مربوط به : اعضای پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/

/*
کلاس پایه: اعضای پروژه
*/
class be_ProjectMembers
{
	public $ProjectmemberID;		//
	public $ProjectID;		//پروژه
	public $PersonID;		//کد شخص
	public $PersonID_FullName;		/* نام و نام خانوادگی مربوط به نام و نام خانوادگی */
	public $AccessType;		//نوع دسترسی
	public $AccessType_Desc;		/* شرح مربوط به نوع دسترسی */
	public $ParticipationPercent;		//درصد مشارکت در پروژه
	public $CreatorID;		//کد شخص ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */

	function be_ProjectMembers() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectMembers.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, CASE ProjectMembers.AccessType 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'VIEWER' THEN 'ناظر' 
				WHEN 'MANAGER' THEN 'مدیر' 
				WHEN 'PMMANAGER' THEN 'کارشناس مدیریت فرآیندها' 
				END as AccessType_Desc 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName from projectmanagement.ProjectMembers 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectMembers.PersonID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectMembers.CreatorID)  where  ProjectMembers.ProjectmemberID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectmemberID=$rec["ProjectmemberID"];
			$this->ProjectID=$rec["ProjectID"];
			$this->PersonID=$rec["PersonID"];
			$this->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$this->AccessType=$rec["AccessType"];
			$this->AccessType_Desc=$rec["AccessType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->ParticipationPercent=$rec["ParticipationPercent"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت اعضای پروژه
*/
class manage_ProjectMembers
{
	static function GetCount($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectmemberID) as TotalCount from projectmanagement.ProjectMembers";
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
		$query = "select max(ProjectmemberID) as MaxID from projectmanagement.ProjectMembers";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectID: پروژه
	* @param $PersonID: نام و نام خانوادگی
	* @param $AccessType: نوع دسترسی
	* @param $ParticipationPercent: درصد مشارکت
	* @return کد داده اضافه شده	*/
	static function Add($ProjectID, $PersonID, $AccessType, $ParticipationPercent)
	{
		$k=0;
		$ParticipationPercent = SharedClass::FixNumber($ParticipationPercent);
		$mysql = pdodb::getInstance();

		// چک میکند فرد قبلا اضافه نشده باشد
		$query = "select * from projectmanagement.ProjectMembers where ProjectID=? and PersonID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		array_push($ValueListArray, $PersonID); 
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec = $res->fetch())
		{
			manage_ProjectMembers::Remove($rec["ProjectmemberID"]);
		}	
		$query = "insert into projectmanagement.ProjectMembers (";
		$query .= " ProjectID";
		$query .= ", PersonID";
		$query .= ", AccessType";
		$query .= ", ParticipationPercent";
		$query .= ", CreatorID";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $AccessType); 
		array_push($ValueListArray, $ParticipationPercent); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectMembers::GetLastID();
		//$mysql->audit("ثبت داده جدید در اعضای پروژه با کد ".$LastID);
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($ProjectID, "", "MEMBER", $LastID, "ADD");
		
		$SelectedPersonID = $PersonID;
		require_once("projectsSecurity.class.php");
		if($AccessType=="MANAGER")
		{
			security_projects::SaveFieldPermission($ProjectID, 'title', $SelectedPersonID, "WRITE");
			security_projects::SaveFieldPermission($ProjectID, 'ouid', $SelectedPersonID, "WRITE");
			security_projects::SaveFieldPermission($ProjectID, 'description', $SelectedPersonID, "WRITE");
			security_projects::SaveFieldPermission($ProjectID, 'StartTime', $SelectedPersonID, "WRITE");
			security_projects::SaveFieldPermission($ProjectID, 'EndTime', $SelectedPersonID, "WRITE");
			security_projects::SaveFieldPermission($ProjectID, 'SysCode', $SelectedPersonID, "WRITE");
			security_projects::SaveFieldPermission($ProjectID, 'ProjectPriority', $SelectedPersonID, "WRITE");
			security_projects::SaveFieldPermission($ProjectID, 'ProjectStatus', $SelectedPersonID, "WRITE");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectDocumentTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectDocuments', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectMembers', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectMilestones', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectTaskActivityTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectTaskTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
		}
		else
		{
			security_projects::SaveFieldPermission($ProjectID, 'title', $SelectedPersonID, "READ");
			security_projects::SaveFieldPermission($ProjectID, 'ouid', $SelectedPersonID, "READ");
			security_projects::SaveFieldPermission($ProjectID, 'description', $SelectedPersonID, "READ");
			security_projects::SaveFieldPermission($ProjectID, 'StartTime', $SelectedPersonID, "READ");
			security_projects::SaveFieldPermission($ProjectID, 'EndTime', $SelectedPersonID, "READ");
			security_projects::SaveFieldPermission($ProjectID, 'SysCode', $SelectedPersonID, "READ");
			security_projects::SaveFieldPermission($ProjectID, 'ProjectPriority', $SelectedPersonID, "READ");
			security_projects::SaveFieldPermission($ProjectID, 'ProjectStatus', $SelectedPersonID, "READ");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectDocumentTypes', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectDocuments', $SelectedPersonID, "YES", "PRIVATE", "PRIVATE", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectMembers', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectMilestones', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectTaskActivityTypes', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
			security_projects::SaveDetailTablePermission($ProjectID, 'ProjectTaskTypes', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
		}
		
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PersonID: نام و نام خانوادگی
	* @param $AccessType: نوع دسترسی
	* @param $ParticipationPercent: درصد مشارکت
	* @return 	*/
	static function Update($UpdateRecordID, $PersonID, $AccessType, $ParticipationPercent)
	{
		$obj = new be_ProjectMembers();
		$obj->LoadDataFromDatabase($UpdateRecordID);		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectMembers set ";
			$query .= " PersonID=? ";
			$query .= ", AccessType=? ";
			$query .= ", ParticipationPercent=? ";
		$query .= " where ProjectmemberID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $AccessType); 
		array_push($ValueListArray, $ParticipationPercent); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در اعضای پروژه");
		require_once("ProjectHistory.class.php");
		manage_ProjectHistory::Add($obj->ProjectID, "", "MEMBER", $UpdateRecordID, "UPDATE");
		$SelectedPersonID = $PersonID;
		require_once("projectsSecurity.class.php");
		// در صورتیکه نوع دسترسی را تغییر داده باشد بر اساس آن دوباره دسترسیها را تنظیم می کند
		if($obj->AccessType!=$AccessType)
		{
			$ProjectID = $obj->ProjectID;
			security_projects::ResetRecordFieldsPermission($ProjectID, $SelectedPersonID);
			security_projects::ResetRecordDetailTablesPermission($ProjectID, $SelectedPersonID);
			if($AccessType=="MANAGER")
			{
				security_projects::SaveFieldPermission($ProjectID, 'title', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($ProjectID, 'description', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($ProjectID, 'StartTime', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($ProjectID, 'EndTime', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($ProjectID, 'SysCode', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($ProjectID, 'ProjectPriority', $SelectedPersonID, "WRITE");
				security_projects::SaveFieldPermission($ProjectID, 'ProjectStatus', $SelectedPersonID, "WRITE");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectDocumentTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectDocuments', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectMembers', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectMilestones', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectTaskActivityTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectTaskTypes', $SelectedPersonID, "YES", "PUBLIC", "PUBLIC", "PUBLIC");
			}
			else
			{
				security_projects::SaveFieldPermission($ProjectID, 'title', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($ProjectID, 'description', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($ProjectID, 'StartTime', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($ProjectID, 'EndTime', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($ProjectID, 'SysCode', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($ProjectID, 'ProjectPriority', $SelectedPersonID, "READ");
				security_projects::SaveFieldPermission($ProjectID, 'ProjectStatus', $SelectedPersonID, "READ");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectDocumentTypes', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectDocuments', $SelectedPersonID, "YES", "PRIVATE", "PRIVATE", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectMembers', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectMilestones', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectTaskActivityTypes', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
				security_projects::SaveDetailTablePermission($ProjectID, 'ProjectTaskTypes', $SelectedPersonID, "NO", "NONE", "NONE", "PUBLIC");
			}
		}
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectMembers();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectMembers where ProjectmemberID=?";
		$mysql->Prepare($query);
		//echo $query;die();
		$mysql->ExecuteStatement(array($RemoveRecordID));
		
		require_once("projectsSecurity.class.php");
		require_once("ProjectHistory.class.php");
		security_projects::ResetRecordFieldsPermission($obj->ProjectID, $obj->PersonID);
		security_projects::ResetRecordDetailTablesPermission($obj->ProjectID, $obj->PersonID);
		
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از اعضای پروژه");
		manage_ProjectHistory::Add($obj->ProjectID, "", "MEMBER", $RemoveRecordID, "REMOVE");
	}
	static function GetList($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectMembers.ProjectmemberID
				,ProjectMembers.ProjectID
				,ProjectMembers.PersonID
				,ProjectMembers.AccessType
				,ProjectMembers.ParticipationPercent
				,ProjectMembers.CreatorID
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, CASE ProjectMembers.AccessType 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'VIEWER' THEN 'ناظر' 
				WHEN 'MANAGER' THEN 'مدیر' 
				WHEN 'PMMANAGER' THEN 'کارشناس مدیریت فرآیندها' 
				END as AccessType_Desc 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName  from projectmanagement.ProjectMembers 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectMembers.PersonID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectMembers.CreatorID)  ";
		$query .= " where ProjectID=? ";
		$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $ProjectID);
		if($ppc->GetPermission("View_ProjectMembers")=="PRIVATE")
				$query .= " and ProjectMembers.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectMembers")=="NONE")
				$query .= " and 0=1 ";
		$query .= " order by AccessType";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectMembers();
			$ret[$k]->ProjectmemberID=$rec["ProjectmemberID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->AccessType=$rec["AccessType"];
			$ret[$k]->AccessType_Desc=$rec["AccessType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->ParticipationPercent=$rec["ParticipationPercent"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}

	static function GetListOptions($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = "";
		$query = "select ProjectMembers.PersonID
			, concat(persons2.plname, ' ', persons2.pfname) as persons2_FullName 
			from projectmanagement.ProjectMembers 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectMembers.PersonID) "; 
		$query .= " where ProjectID=? order by plname, pfname";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["PersonID"]."'>".$rec["persons2_FullName"];
		}
		return $ret;
	}

	// لیست کدهای پروژه ای که فرد مدیر آنهاست بر می گرداند
	static function GetProjectIDsOfManager($PersonID)
	{
		$i = 0;
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectMembers.ProjectID
			from projectmanagement.ProjectMembers 
			 where PersonID=? and AccessType='MANAGER'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID));
		while($rec=$res->fetch())
		{
			$ret[$i] = $rec["ProjectID"];
			$i++;
		}
		return $ret;
	}
	
	static function GetSumPercentageUse($PersonID)
	{
		$i = 0;
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select sum(ParticipationPercent) as TotalPercent
			from projectmanagement.ProjectMembers 
			 where PersonID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID));
		if($rec=$res->fetch())
		{
			return $rec["TotalPercent"];
		}
		return 0;
	}
	
	static function GetProjectsListOfPerson($PersonID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectMembers.ProjectmemberID
				,ProjectMembers.ProjectID
				,ProjectMembers.PersonID
				,ProjectMembers.AccessType
				,ProjectMembers.ParticipationPercent
				,ProjectMembers.CreatorID
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, CASE ProjectMembers.AccessType 
				WHEN 'MEMBER' THEN 'عضو' 
				WHEN 'VIEWER' THEN 'ناظر' 
				WHEN 'MANAGER' THEN 'مدیر' 
				END as AccessType_Desc 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName
			, projects.title  
			from projectmanagement.ProjectMembers
			JOIN projectmanagement.projects using (ProjectID)  
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectMembers.PersonID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectMembers.CreatorID)  ";
		$query .= " where ProjectMembers.PersonID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k]["ProjectTitle"]=$rec["title"];
			$ret[$k]["ParticipationPercent"]=$rec["ParticipationPercent"];
			$k++;
		}
		return $ret;
	}
	
	public function GetMemberShipType($PersonID, $ProjectID)
	{
		$query = "select AccessType from projectmanagement.ProjectMembers 
				  where  PersonID=? and ProjectID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($PersonID, $ProjectID));
		if($rec=$res->fetch())
		{
			return $rec["AccessType"];
		}
		return "NONE";
		
	}
}
?>
