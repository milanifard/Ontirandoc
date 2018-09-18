<?php
/*
 تعریف کلاسها و متدهای مربوط به : پاسخگویان به درخواستهای خارجی در پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-31
*/

/*
کلاس پایه: پاسخگویان به درخواستهای خارجی در پروژه
*/
class be_ProjectResponsibles
{
	public $ProjectResponsibleID;		//
	public $ProjectID;		//
	public $PersonID;		//
	public $PersonID_FullName;		/* نام و نام خانوادگی مربوط به  */

	function be_ProjectResponsibles() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectResponsibles.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName from projectmanagement.ProjectResponsibles 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectResponsibles.PersonID)  where  ProjectResponsibles.ProjectResponsibleID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectResponsibleID=$rec["ProjectResponsibleID"];
			$this->ProjectID=$rec["ProjectID"];
			$this->PersonID=$rec["PersonID"];
			$this->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت پاسخگویان به درخواستهای خارجی در پروژه
*/
class manage_ProjectResponsibles
{
	static function GetCount($ProjectID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(ProjectResponsibleID) as TotalCount from projectmanagement.ProjectResponsibles";
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
		$query = "select max(ProjectResponsibleID) as MaxID from projectmanagement.ProjectResponsibles";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectID: 
	* @param $PersonID: 
	* @return کد داده اضافه شده	*/
	static function Add($ProjectID, $PersonID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectResponsibles (";
		$query .= " ProjectID";
		$query .= ", PersonID";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectID); 
		array_push($ValueListArray, $PersonID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectResponsibles::GetLastID();
		$mysql->audit("ثبت داده جدید در پاسخگویان به درخواستهای خارجی در پروژه با کد ".$LastID);
		return $LastID;
	}
		/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PersonID: 
	* @return 	*/
	static function Update($UpdateRecordID, $PersonID)
	{
		$k=0;
		$LogDesc = manage_ProjectResponsibles::ComparePassedDataWithDB($UpdateRecordID, $PersonID);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectResponsibles set ";
			$query .= " PersonID=? ";
		$query .= " where ProjectResponsibleID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پاسخگویان به درخواستهای خارجی در پروژه - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectResponsibles where ProjectResponsibleID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پاسخگویان به درخواستهای خارجی در پروژه");
	}
/*		static function GetList($ProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectResponsibles.ProjectResponsibleID
				,ProjectResponsibles.ProjectID
				,ProjectResponsibles.PersonID
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName  from projectmanagement.ProjectResponsibles 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectResponsibles.PersonID)  ";
		$query .= " where ProjectID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectResponsibles();
			$ret[$k]->ProjectResponsibleID=$rec["ProjectResponsibleID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}*/
	static function GetList($ProjectID,$ouid='')
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$temp='';
		$re = array($ProjectID);
		$query = "select ProjectResponsibles.ProjectResponsibleID
				,ProjectResponsibles.ProjectID
				,ProjectResponsibles.PersonID
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName  from projectmanagement.ProjectResponsibles 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectResponsibles.PersonID) 
			";
			if($ouid!=''){
			$query .="LEFT JOIN projectmanagement.ResponsibleUnit on (ProjectResponsibles.ProjectResponsibleID=ResponsibleUnit.ProjectResponsibleID)";	
			$temp = " and ResponsibleUnit.ouid=? ";
			$re = array($ProjectID,$ouid);
			}

		
		$query .= " where ProjectID=? ".$temp;
	/*	if($ouid!='')
		echo $query;*/
		$mysql->Prepare($query);
		 
		$res = $mysql->ExecuteStatement($re);
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectResponsibles();
			$ret[$k]->ProjectResponsibleID=$rec["ProjectResponsibleID"];
			$ret[$k]->ProjectID=$rec["ProjectID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $PersonID: 
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $PersonID)
	{
		$ret = "";
		$obj = new be_ProjectResponsibles();
		$obj->LoadDataFromDatabase($CurRecID);
		if($PersonID!=$obj->PersonID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "";
		}
		return $ret;
	}
}
?>
