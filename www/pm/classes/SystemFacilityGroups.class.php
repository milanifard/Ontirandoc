<?php
/*
 تعریف کلاسها و متدهای مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-5
*/

/*
کلاس پایه: 
*/
class be_SystemFacilityGroups
{
	public $GroupID;		//
	public $GroupName;		//
	public $OrderNo;		//

	function be_SystemFacilityGroups() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SystemFacilityGroups.* from projectmanagement.SystemFacilityGroups  where  SystemFacilityGroups.GroupID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->GroupID=$rec["GroupID"];
			$this->GroupName=$rec["GroupName"];
			$this->OrderNo=$rec["OrderNo"];
		}
	}
}
/*
کلاس مدیریت 
*/
class manage_SystemFacilityGroups
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(GroupID) as TotalCount from projectmanagement.SystemFacilityGroups";
		if($WhereCondition!="")
		{
			$query .= " where ".$WhereCondition;
		}
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
		$query = "select max(GroupID) as MaxID from projectmanagement.SystemFacilityGroups";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $GroupName: نام 
	* @param $OrderNo: شماره ترتیب
	* @return کد داده اضافه شده	*/
	static function Add($GroupName, $OrderNo)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.SystemFacilityGroups (";
		$query .= " GroupName";
		$query .= ", OrderNo";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $GroupName); 
		array_push($ValueListArray, $OrderNo); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SystemFacilityGroups::GetLastID();
		$mysql->audit("ثبت داده جدید در  با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $GroupName: نام 
	* @param $OrderNo: شماره ترتیب
	* @return 	*/
	static function Update($UpdateRecordID, $GroupName, $OrderNo)
	{
		$k=0;
		$LogDesc = manage_SystemFacilityGroups::ComparePassedDataWithDB($UpdateRecordID, $GroupName, $OrderNo);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.SystemFacilityGroups set ";
			$query .= " GroupName=? ";
			$query .= ", OrderNo=? ";
		$query .= " where GroupID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $GroupName); 
		array_push($ValueListArray, $OrderNo); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در  - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.SystemFacilityGroups where GroupID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از ");
	}
	static function GetList()
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SystemFacilityGroups.GroupID
				,SystemFacilityGroups.GroupName
				,SystemFacilityGroups.OrderNo from projectmanagement.SystemFacilityGroups order by OrderNo ";
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SystemFacilityGroups();
			$ret[$k]->GroupID=$rec["GroupID"];
			$ret[$k]->GroupName=$rec["GroupName"];
			$ret[$k]->OrderNo=$rec["OrderNo"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $GroupName: نام 
	* @param $OrderNo: شماره ترتیب
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $GroupName, $OrderNo)
	{
		$ret = "";
		$obj = new be_SystemFacilityGroups();
		$obj->LoadDataFromDatabase($CurRecID);
		if($GroupName!=$obj->GroupName)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نام ";
		}
		if($OrderNo!=$obj->OrderNo)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "شماره ترتیب";
		}
		return $ret;
	}
}
?>