<?php
/*
 تعریف کلاسها و متدهای مربوط به : کاربران منتسب به کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/

/*
کلاس پایه: کاربران منتسب به کار
*/
class be_ProjectTaskAssignedUsers
{
	public $ProjectTaskAssignedUserID;		//
	public $ProjectTaskID;		//کار مربوطه
	public $PersonID;		//شخص مربوطه
	public $PersonID_FullName;		/* نام و نام خانوادگی مربوط به شخص مربوطه */
	public $AssignDescription;		//شرح انتساب
	public $ParticipationPercent;		//درصد مشارکت
	public $CreatorID;		//
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $AssignType;		//نوع انتساب
	public $AssignType_Desc;		/* شرح مربوط به نقش */
	public $ProcessJudge;

	function be_ProjectTaskAssignedUsers() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTaskAssignedUsers.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE ProjectTaskAssignedUsers.AssignType 
				WHEN 'EXECUTOR' THEN 'مجری' 
				WHEN 'VIEWER' THEN 'ناظر' 
				END as AssignType_Desc from projectmanagement.ProjectTaskAssignedUsers 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskAssignedUsers.PersonID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTaskAssignedUsers.CreatorID)  where  ProjectTaskAssignedUsers.ProjectTaskAssignedUserID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectTaskAssignedUserID=$rec["ProjectTaskAssignedUserID"];
			$this->ProjectTaskID=$rec["ProjectTaskID"];
			$this->PersonID=$rec["PersonID"];
			$this->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$this->AssignDescription=$rec["AssignDescription"];
			$this->ParticipationPercent=$rec["ParticipationPercent"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$this->AssignType=$rec["AssignType"];
			$this->AssignType_Desc=$rec["AssignType_Desc"];  // محاسبه بر اساس لیست ثابت
		}
	}
}
/*
کلاس مدیریت کاربران منتسب به کار
*/
class manage_ProjectTaskAssignedUsers
{
	static function GetCount($ProjectTaskID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectTaskAssignedUserID) as TotalCount from projectmanagement.ProjectTaskAssignedUsers";
			$query .= " where ProjectTaskID='".$ProjectTaskID."'";
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
		$query = "select max(ProjectTaskAssignedUserID) as MaxID from projectmanagement.ProjectTaskAssignedUsers";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectTaskID: کار مربوطه
	* @param $PersonID: شخص مربوطه
	* @param $AssignDescription: شرح انتساب
	* @param $ParticipationPercent: درصد مشارکت
	* @param $AssignType: نقش
	* @return کد داده اضافه شده	*/
	static function Add($ProjectTaskID, $PersonID, $AssignDescription, $ParticipationPercent, $AssignType , $ProcessJudge)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectTaskAssignedUsers (";
		$query .= " ProjectTaskID";
		$query .= ", PersonID";
		$query .= ", AssignDescription";
		$query .= ", ParticipationPercent";
		$query .= ", CreatorID";
		$query .= ", AssignType";
		$query .= ", ProcessJudge";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectTaskID); 
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $AssignDescription); 
		array_push($ValueListArray, $ParticipationPercent); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		array_push($ValueListArray, $AssignType);
		array_push($ValueListArray, $ProcessJudge);   
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectTaskAssignedUsers::GetLastID();
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($ProjectTaskID, "", "USER", $LastID, "ADD");
		
		//$mysql->audit("ثبت داده جدید در کاربران منتسب به کار با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PersonID: شخص مربوطه
	* @param $AssignDescription: شرح انتساب
	* @param $ParticipationPercent: درصد مشارکت
	* @param $AssignType: نقش
	* @return 	*/
	static function Update($UpdateRecordID, $PersonID, $AssignDescription, $ParticipationPercent, $AssignType)
	{
		$obj = new be_ProjectTaskAssignedUsers();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectTaskAssignedUsers set ";
			$query .= " PersonID=? ";
			$query .= ", AssignDescription=? ";
			$query .= ", ParticipationPercent=? ";
			$query .= ", AssignType=? ";
		$query .= " where ProjectTaskAssignedUserID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $AssignDescription); 
		array_push($ValueListArray, $ParticipationPercent); 
		array_push($ValueListArray, $AssignType); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در کاربران منتسب به کار");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "USER", $UpdateRecordID, "UPDATE");
		
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectTaskAssignedUsers();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectTaskAssignedUsers where ProjectTaskAssignedUserID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از کاربران منتسب به کار");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "USER", $RemoveRecordID, "DELETE");
		
	}
	static function GetList($ProjectTaskID, $OrderByFieldName, $OrderType)
	{
		if(strtoupper($OrderType)!="ASC" && strtoupper($OrderType)!="DESC")
			$OrderType = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskAssignedUsers.ProjectTaskAssignedUserID
				,ProjectTaskAssignedUsers.ProjectTaskID
				,ProjectTaskAssignedUsers.PersonID
				,ProjectTaskAssignedUsers.AssignDescription
				,ProjectTaskAssignedUsers.ParticipationPercent
				,ProjectTaskAssignedUsers.CreatorID
				,ProjectTaskAssignedUsers.AssignType
			, concat(persons2.plname, ' ', persons2.pfname) as persons2_FullName 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName 
			, CASE ProjectTaskAssignedUsers.AssignType 
				WHEN 'EXECUTOR' THEN 'مجری' 
				WHEN 'VIEWER' THEN 'ناظر' 
				END as AssignType_Desc  from projectmanagement.ProjectTaskAssignedUsers 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskAssignedUsers.PersonID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=ProjectTaskAssignedUsers.CreatorID)  ";
		$query .= " where ProjectTaskID=? ";
		$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $ProjectTaskID);
		if($ppc->GetPermission("View_ProjectTaskAssignedUsers")=="PRIVATE")
				$query .= " and ProjectTaskAssignedUsers.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectTaskAssignedUsers")=="NONE")
				$query .= " and 0=1 ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectTaskID));
		/*if($_SESSION["UserID"]=="gholami-a") {
			       echo $query;die();
		}*/
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskAssignedUsers();
			$ret[$k]->ProjectTaskAssignedUserID=$rec["ProjectTaskAssignedUserID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->AssignDescription=$rec["AssignDescription"];
			$ret[$k]->ParticipationPercent=$rec["ParticipationPercent"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->AssignType=$rec["AssignType"];
			$ret[$k]->AssignType_Desc=$rec["AssignType_Desc"];  // محاسبه بر اساس لیست ثابت
			$k++;
		}

		return $ret;
	}
}
?>
