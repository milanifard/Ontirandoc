<?php
/*
 تعریف کلاسها و متدهای مربوط به : یادداشتها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/

/*
کلاس پایه: یادداشتها
*/

class be_ProjectTaskComments
{
	public $ProjectTaskCommentID;		//
	public $ProjectTaskID;		//کار مربوطه
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $CreateTime;		//زمان ایجاد
	public $CreateTime_Shamsi;		/* مقدار شمسی معادل با زمان ایجاد */
	public $CommentBody;		//متن

	function be_ProjectTaskComments() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTaskComments.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, concat(g2j(CreateTime), ' ', substr(CreateTime, 12,10)) as CreateTime_Shamsi from projectmanagement.ProjectTaskComments 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskComments.CreatorID)  where  ProjectTaskComments.ProjectTaskCommentID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectTaskCommentID=$rec["ProjectTaskCommentID"];
			$this->ProjectTaskID=$rec["ProjectTaskID"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$this->CreateTime=$rec["CreateTime"];
			$this->CreateTime_Shamsi=$rec["CreateTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->CommentBody=$rec["CommentBody"];
		}
	}
}
/*
کلاس مدیریت یادداشتها
*/
class manage_ProjectTaskComments
{
	static function GetCount($ProjectTaskID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(ProjectTaskCommentID) as TotalCount from projectmanagement.ProjectTaskComments";
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
		$query = "select max(ProjectTaskCommentID) as MaxID from projectmanagement.ProjectTaskComments";
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
	* @param $CommentBody: متن
	* @return کد داده اضافه شده	*/
	static function Add($ProjectTaskID, $CommentBody)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectTaskComments (";
		$query .= " ProjectTaskID";
		$query .= ", CreatorID";
		$query .= ", CreateTime";
		$query .= ", CommentBody";
		$query .= ") values (";
		$query .= "? , ? , now() , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectTaskID); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		array_push($ValueListArray, $CommentBody); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectTaskComments::GetLastID();
		//$mysql->audit("ثبت داده جدید در یادداشتها با کد ".$LastID);
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($ProjectTaskID, "", "COMMENT", $LastID, "ADD");
		
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $CommentBody: متن
	* @return 	*/
	static function Update($UpdateRecordID, $CommentBody)
	{
		$obj = new be_ProjectTaskComments();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectTaskComments set ";
			$query .= " CommentBody=? ";
		$query .= " where ProjectTaskCommentID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $CommentBody); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در یادداشتها");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "COMMENT", $UpdateRecordID, "UPDATE");
		
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectTaskComments();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectTaskComments where ProjectTaskCommentID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از یادداشتها");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "COMMENT", $RemoveRecordID, "DELETE");
		
	}
	static function GetList($ProjectTaskID, $FromRec = 0, $NumberOfRec = 0)
	{
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskComments.ProjectTaskCommentID
				,ProjectTaskComments.ProjectTaskID
				,ProjectTaskComments.CreatorID
				,ProjectTaskComments.CreateTime
				,ProjectTaskComments.CommentBody
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, concat(g2j(CreateTime), ' ', substr(CreateTime, 12, 10)) as CreateTime_Shamsi  from projectmanagement.ProjectTaskComments 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskComments.CreatorID)  ";
		$query .= " where ProjectTaskID=? ";
		$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $ProjectTaskID);
		if($ppc->GetPermission("View_ProjectTaskComments")=="PRIVATE")
				$query .= " and ProjectTaskComments.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectTaskComments")=="NONE")
				$query .= " and 0=1 ";
		$query .= " order by ProjectTaskComments.CreateTime DESC ";
		if ($NumberOfRec != 0)
			$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectTaskID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskComments();
			$ret[$k]->ProjectTaskCommentID=$rec["ProjectTaskCommentID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->CreateTime=$rec["CreateTime"];
			$ret[$k]->CreateTime_Shamsi=$rec["CreateTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->CommentBody=$rec["CommentBody"];
			$k++;
		}
		return $ret;
	}
}
?>
