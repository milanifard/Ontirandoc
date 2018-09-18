<?php
/*
 تعریف کلاسها و متدهای مربوط به : گروه های کار داخل پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-10-8
*/

/*
کلاس پایه: گروه های کار داخل پروژه
*/
class be_ProjectTaskGroups
{
	public $ProjectTaskGroupID;		//
	public $ProjectID;		//کد پروژه
	public $TaskGroupName;		//عنوان

	function be_ProjectTaskGroups() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTaskGroups.* from projectmanagement.ProjectTaskGroups  where  ProjectTaskGroups.ProjectTaskGroupID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectTaskGroupID=$rec["ProjectTaskGroupID"];
			$this->ProjectID=$rec["ProjectID"];
			$this->TaskGroupName=$rec["TaskGroupName"];
		}
	}
}
/*
کلاس مدیریت گروه های کار داخل پروژه
*/
class manage_ProjectTaskGroups
{
	static function GetCount($ProjectID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(ProjectTaskGroupID) as TotalCount from projectmanagement.ProjectTaskGroups";
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
		$query = "select max(ProjectTaskGroupID) as MaxID from projectmanagement.ProjectTaskGroups";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectID: کد پروژه
	* @param $TaskGroupName: عنوان
	* @return کد داده اضافه شده	*/
	static function Add($ProjectID, $TaskGroupName)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectTaskGroups (";
		$query .= " ProjectID";
		$query .= ", TaskGroupName";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		array_push($ValueListArray, $TaskGroupName); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectTaskGroups::GetLastID();
		$mysql->audit("ثبت داده جدید در گروه های کار داخل پروژه با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $TaskGroupName: عنوان
	* @return 	*/
	static function Update($UpdateRecordID, $TaskGroupName)
	{
		$k=0;
		$LogDesc = manage_ProjectTaskGroups::ComparePassedDataWithDB($UpdateRecordID, $TaskGroupName);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectTaskGroups set ";
			$query .= " TaskGroupName=? ";
		$query .= " where ProjectTaskGroupID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $TaskGroupName); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در گروه های کار داخل پروژه - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectTaskGroups where ProjectTaskGroupID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از گروه های کار داخل پروژه");
	}
	static function GetList($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskGroups.ProjectTaskGroupID
				,ProjectTaskGroups.ProjectID
				,ProjectTaskGroups.TaskGroupName from projectmanagement.ProjectTaskGroups  ";
		$query .= " where ProjectID=? ";
		$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $ProjectID);
		if($ppc->GetPermission("View_ProjectTaskGroups")=="PRIVATE")
				$query .= " and ProjectTaskGroups.='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectTaskGroups")=="NONE")
				$query .= " and 0=1 ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskGroups();
			$ret[$k]->ProjectTaskGroupID=$rec["ProjectTaskGroupID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->TaskGroupName=$rec["TaskGroupName"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $TaskGroupName: عنوان
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $TaskGroupName)
	{
		$ret = "";
		$obj = new be_ProjectTaskGroups();
		$obj->LoadDataFromDatabase($CurRecID);
		if($TaskGroupName!=$obj->TaskGroupName)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		return $ret;
	}

	static function CreateSelectOptions($ProjectID, $CurValue = 0)
	{
		$ret = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$query = "select ProjectTaskGroupID
				,TaskGroupName
				from projectmanagement.ProjectTaskGroups 
				where ProjectID=? order by TaskGroupName";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["ProjectTaskGroupID"]."' ";
			if($CurValue==$rec["ProjectTaskGroupID"])
				$ret .= " selected ";
			$ret .= ">".$rec["TaskGroupName"];
		}
		return $ret;
	}
	
}


?>