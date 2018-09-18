<?php
/*
 تعریف کلاسها و متدهای مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-5
*/

/*
کلاس پایه: 
*/
class be_UserFacilities
{
	public $FacilityPageID;		//
	public $UserID;		//
	public $UserID_Desc;		/* شرح مربوط به کاربر */
	public $FacilityID;		//

	function be_UserFacilities() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select UserFacilities.* 
			, l1.UserID  as l1_UserID from projectmanagement.UserFacilities 
			LEFT JOIN projectmanagement.AccountSpecs  l1 on (l1.UserID=UserFacilities.UserID)  where  UserFacilities.FacilityPageID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->FacilityPageID=$rec["FacilityPageID"];
			$this->UserID=$rec["UserID"];
			$this->UserID_Desc=$rec["l1_UserID"]; // محاسبه از روی جدول وابسته
			$this->FacilityID=$rec["FacilityID"];
		}
	}
}
/*
کلاس مدیریت 
*/
class manage_UserFacilities
{
	static function HasAccess($UserID, $FacilityID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.UserFacilities where ";
		$query .= " UserID=? and FacilityID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $UserID); 
		array_push($ValueListArray, $FacilityID); 
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec = $res->fetch())
		  return true;
		return false;
	}
	
	static function GetCount($FacilityID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(FacilityPageID) as TotalCount from projectmanagement.UserFacilities";
			$query .= " where FacilityID='".$FacilityID."'";
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
		$query = "select max(FacilityPageID) as MaxID from projectmanagement.UserFacilities";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	
	static function RemoveAllUserFacilities($UserID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.UserFacilities where";
		$query .= " UserID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $UserID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("حذف مجوزهای کاربر ".$UserID);
	}

	
	/**
	* @param $UserID: کاربر
	* @param $FacilityID: امکان
	* @return کد داده اضافه شده	*/
	static function Add($UserID, $FacilityID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.UserFacilities (";
		$query .= " UserID";
		$query .= ", FacilityID";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $UserID); 
		array_push($ValueListArray, $FacilityID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_UserFacilities::GetLastID();
		$mysql->audit("ثبت داده جدید در  با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $UserID: کاربر
	* @return 	*/
	static function Update($UpdateRecordID, $UserID)
	{
		$k=0;
		$LogDesc = manage_UserFacilities::ComparePassedDataWithDB($UpdateRecordID, $UserID);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.UserFacilities set ";
			$query .= " UserID=? ";
		$query .= " where FacilityPageID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $UserID); 
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
		$query = "delete from projectmanagement.UserFacilities where FacilityPageID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از ");
	}
	static function GetList($FacilityID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select UserFacilities.FacilityPageID
				,UserFacilities.UserID
				,UserFacilities.FacilityID
			, l1.UserID  as l1_UserID  from projectmanagement.UserFacilities 
			LEFT JOIN projectmanagement.AccountSpecs  l1 on (l1.UserID=UserFacilities.UserID)  ";
		$query .= " where FacilityID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($FacilityID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_UserFacilities();
			$ret[$k]->FacilityPageID=$rec["FacilityPageID"];
			$ret[$k]->UserID=$rec["UserID"];
			$ret[$k]->UserID_Desc=$rec["l1_UserID"]; // محاسبه از روی جدول وابسته
			$ret[$k]->FacilityID=$rec["FacilityID"];
			$k++;
		}
		return $ret;
	}
	/**
	* @param $FacilityID کد آیتم پدر
	* @param $UserID: کاربر
	* @param $FacilityID: امکان
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($FacilityID, $UserID, $FacilityID, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select UserFacilities.FacilityPageID
				,UserFacilities.UserID
				,UserFacilities.FacilityID
			, l1.UserID  as l1_UserID  from projectmanagement.UserFacilities 
			LEFT JOIN projectmanagement.AccountSpecs  l1 on (l1.UserID=UserFacilities.UserID)  ";
		$cond = "FacilityID=? ";
		if($UserID!="0" && $UserID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "UserFacilities.UserID=? ";
		}
		if($FacilityID!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UserFacilities.FacilityID like ? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $FacilityID); 
		if($UserID!="0" && $UserID!="") 
			array_push($ValueListArray, $UserID); 
		if($FacilityID!="") 
			array_push($ValueListArray, "%".$FacilityID."%"); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_UserFacilities();
			$ret[$k]->FacilityPageID=$rec["FacilityPageID"];
			$ret[$k]->UserID=$rec["UserID"];
			$ret[$k]->UserID_Desc=$rec["l1_UserID"]; // محاسبه از روی جدول وابسته
			$ret[$k]->FacilityID=$rec["FacilityID"];
			$k++;
		}
		return $ret;
	}
	/**
	* @param $FacilityID کد آیتم پدر
	* @param $UserID: کاربر
	* @param $FacilityID: امکان
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return تعداد داده های حاصل جستجو
	*/
	static function SearchResultCount($FacilityID, $UserID, $FacilityID, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(*) as TotalCount from projectmanagement.UserFacilities	";
 		$cond = "FacilityID=? ";
		if($UserID!="0" && $UserID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "UserFacilities.UserID=? ";
		}
		if($FacilityID!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "UserFacilities.FacilityID like ? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $FacilityID); 
		if($UserID!="0" && $UserID!="") 
			array_push($ValueListArray, $UserID); 
		if($FacilityID!="") 
			array_push($ValueListArray, "%".$FacilityID."%"); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec = $res->fetch()) return $rec["TotalCount"];  else return 0;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $UserID: کاربر
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $UserID)
	{
		$ret = "";
		$obj = new be_UserFacilities();
		$obj->LoadDataFromDatabase($CurRecID);
		if($UserID!=$obj->UserID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "کاربر";
		}
		return $ret;
	}
}
?>
