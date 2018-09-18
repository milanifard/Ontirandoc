<?php
/*
 تعریف کلاسها و متدهای مربوط به : مقادیر مجاز خصوصیت
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 95-6-1
*/

/*
کلاس پایه: مقادیر مجاز خصوصیت
*/
class be_OntologyPropertyPermittedValues
{
	public $OntologyPropertyPermittedValueID;		//
	public $OntologyPropertyID;		//خصوصیت
	public $PermittedValue;		//مقدار مجاز

	function be_OntologyPropertyPermittedValues() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select OntologyPropertyPermittedValues.* from projectmanagement.OntologyPropertyPermittedValues  where  OntologyPropertyPermittedValues.OntologyPropertyPermittedValueID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyPropertyPermittedValueID=$rec["OntologyPropertyPermittedValueID"];
			$this->OntologyPropertyID=$rec["OntologyPropertyID"];
			$this->PermittedValue=$rec["PermittedValue"];
		}
	}
}
/*
کلاس مدیریت مقادیر مجاز خصوصیت
*/
class manage_OntologyPropertyPermittedValues
{
	static function GetCount($OntologyPropertyID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(OntologyPropertyPermittedValueID) as TotalCount from projectmanagement.OntologyPropertyPermittedValues";
			$query .= " where OntologyPropertyID='".$OntologyPropertyID."'";
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
		$query = "select max(OntologyPropertyPermittedValueID) as MaxID from projectmanagement.OntologyPropertyPermittedValues";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $OntologyPropertyID: خصوصیت
	* @param $PermittedValue: مقدار مجاز
	* @return کد داده اضافه شده	*/
	static function Add($OntologyPropertyID, $PermittedValue)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.OntologyPropertyPermittedValues (";
		$query .= " OntologyPropertyID";
		$query .= ", PermittedValue";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyPropertyID); 
		array_push($ValueListArray, $PermittedValue); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_OntologyPropertyPermittedValues::GetLastID();
		$mysql->audit("ثبت داده جدید در مقادیر مجاز خصوصیت با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PermittedValue: مقدار مجاز
	* @return 	*/
	static function Update($UpdateRecordID, $PermittedValue)
	{
		$k=0;
		$LogDesc = manage_OntologyPropertyPermittedValues::ComparePassedDataWithDB($UpdateRecordID, $PermittedValue);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyPropertyPermittedValues set ";
			$query .= " PermittedValue=? ";
		$query .= " where OntologyPropertyPermittedValueID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PermittedValue); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در مقادیر مجاز خصوصیت - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyPermittedValueID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از مقادیر مجاز خصوصیت");
	}
	static function GetList($OntologyPropertyID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyPropertyPermittedValues.OntologyPropertyPermittedValueID
				,OntologyPropertyPermittedValues.OntologyPropertyID
				,OntologyPropertyPermittedValues.PermittedValue from projectmanagement.OntologyPropertyPermittedValues  ";
		$query .= " where OntologyPropertyID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_OntologyPropertyPermittedValues();
			$ret[$k]->OntologyPropertyPermittedValueID=$rec["OntologyPropertyPermittedValueID"];
			$ret[$k]->OntologyPropertyID=$rec["OntologyPropertyID"];
			$ret[$k]->PermittedValue=$rec["PermittedValue"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $PermittedValue: مقدار مجاز
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $PermittedValue)
	{
		$ret = "";
		$obj = new be_OntologyPropertyPermittedValues();
		$obj->LoadDataFromDatabase($CurRecID);
		if($PermittedValue!=$obj->PermittedValue)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "مقدار مجاز";
		}
		return $ret;
	}
}
?>