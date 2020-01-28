<?php
/*
 تعریف کلاسها و متدهای مربوط به : هستان نگارها جزو پروژه ادغام
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-10-15
*/

// This file taken by MGhayour

/*
کلاس پایه: هستان نگارها جزو پروژه ادغام
*/
class be_OntologyMergeProjectMembers
{
	public $OntologyMergeProjectMemberID;		//
	public $OntologyMergeProjectID;		//پروژه مربوطه
	public $OntologyID;		//هستان نگار عضو
	public $OntologyID_Desc;		/* شرح مربوط به هستان نگار عضو */

	function be_OntologyMergeProjectMembers() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select OntologyMergeProjectMembers.* 
			, p2.OntologyTitle  as p2_OntologyTitle from projectmanagement.OntologyMergeProjectMembers 
			LEFT JOIN projectmanagement.ontologies  p2 on (p2.OntologyID=OntologyMergeProjectMembers.OntologyID)  where  OntologyMergeProjectMembers.OntologyMergeProjectMemberID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyMergeProjectMemberID=$rec["OntologyMergeProjectMemberID"];
			$this->OntologyMergeProjectID=$rec["OntologyMergeProjectID"];
			$this->OntologyID=$rec["OntologyID"];
			$this->OntologyID_Desc=$rec["p2_OntologyTitle"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت هستان نگارها جزو پروژه ادغام
*/
class manage_OntologyMergeProjectMembers
{
	static function GetCount($OntologyMergeProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(OntologyMergeProjectMemberID) as TotalCount from projectmanagement.OntologyMergeProjectMembers";
			$query .= " where OntologyMergeProjectID='".$OntologyMergeProjectID."'";
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
		$query = "select max(OntologyMergeProjectMemberID) as MaxID from projectmanagement.OntologyMergeProjectMembers";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $OntologyMergeProjectID: پروژه مربوطه
	* @param $OntologyID: هستان نگار عضو
	* @return کد داده اضافه شده	*/
	static function Add($OntologyMergeProjectID, $OntologyID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.OntologyMergeProjectMembers (";
		$query .= " OntologyMergeProjectID";
		$query .= ", OntologyID";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyMergeProjectID); 
		array_push($ValueListArray, $OntologyID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_OntologyMergeProjectMembers::GetLastID();
		$mysql->audit("ثبت داده جدید در هستان نگارها جزو پروژه ادغام با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $OntologyID: هستان نگار عضو
	* @return 	*/
	static function Update($UpdateRecordID, $OntologyID)
	{
		$k=0;
		$LogDesc = manage_OntologyMergeProjectMembers::ComparePassedDataWithDB($UpdateRecordID, $OntologyID);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyMergeProjectMembers set ";
			$query .= " OntologyID=? ";
		$query .= " where OntologyMergeProjectMemberID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyID); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در هستان نگارها جزو پروژه ادغام - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyMergeProjectMembers where OntologyMergeProjectMemberID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از هستان نگارها جزو پروژه ادغام");
	}
	static function GetList($OntologyMergeProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyMergeProjectMembers.OntologyMergeProjectMemberID
				,OntologyMergeProjectMembers.OntologyMergeProjectID
				,OntologyMergeProjectMembers.OntologyID
			, p2.OntologyTitle  as p2_OntologyTitle  from projectmanagement.OntologyMergeProjectMembers 
			LEFT JOIN projectmanagement.ontologies  p2 on (p2.OntologyID=OntologyMergeProjectMembers.OntologyID)  ";
		$query .= " where OntologyMergeProjectID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyMergeProjectID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_OntologyMergeProjectMembers();
			$ret[$k]->OntologyMergeProjectMemberID=$rec["OntologyMergeProjectMemberID"];
			$ret[$k]->OntologyMergeProjectID=$rec["OntologyMergeProjectID"];
			$ret[$k]->OntologyID=$rec["OntologyID"];
			$ret[$k]->OntologyID_Desc=$rec["p2_OntologyTitle"]; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $OntologyID: هستان نگار عضو
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $OntologyID)
	{
		$ret = "";
		$obj = new be_OntologyMergeProjectMembers();
		$obj->LoadDataFromDatabase($CurRecID);
		if($OntologyID!=$obj->OntologyID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "هستان نگار عضو";
		}
		return $ret;
	}
}
?>