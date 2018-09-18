<?php
/*
 تعریف کلاسها و متدهای مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/

/*
کلاس پایه: 
*/
class be_persons
{
	public $PersonID;		//
	public $pfname;		//
	public $plname;		//
	public $CardNumber;		//
	public $EnterExitTypeID;		//

	function be_persons() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select persons.* from projectmanagement.persons  where  persons.PersonID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->PersonID=$rec["PersonID"];
			$this->pfname=$rec["pfname"];
			$this->plname=$rec["plname"];
			$this->CardNumber=$rec["CardNumber"];
			$this->EnterExitTypeID=$rec["EnterExitTypeID"];
		}
	}
}
/*
کلاس مدیریت 
*/
class manage_persons
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = "select count(PersonID) as TotalCount from projectmanagement.persons";
		if($WhereCondition!="")
		{
			$query .= " where ".$WhereCondition;
		}
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
		$query = "select max(PersonID) as MaxID from projectmanagement.persons";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $pfname: نام
	* @param $plname: نام خانوادگی
	* @param $CardNumber: شماره کارت
	* @return کد داده اضافه شده	*/
	static function Add($pfname, $plname, $CardNumber)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.persons (";
		$query .= " pfname";
		$query .= ", plname";
		$query .= ", CardNumber";
		$query .= ") values (";
		$query .= "? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $pfname); 
		array_push($ValueListArray, $plname); 
		array_push($ValueListArray, $CardNumber); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_persons::GetLastID();
		$mysql->audit("ثبت داده جدید در  با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $pfname: نام
	* @param $plname: نام خانوادگی
	* @param $CardNumber: شماره کارت
	* @return 	*/
	static function Update($UpdateRecordID, $pfname, $plname, $CardNumber)
	{
		$k=0;
		$LogDesc = manage_persons::ComparePassedDataWithDB($UpdateRecordID, $pfname, $plname, $CardNumber);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.persons set ";
			$query .= " pfname=? ";
			$query .= ", plname=? ";
			$query .= ", CardNumber=? ";
		$query .= " where PersonID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $pfname); 
		array_push($ValueListArray, $plname); 
		array_push($ValueListArray, $CardNumber); 
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
		$query = "delete from projectmanagement.persons where PersonID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از ");
	}
	static function GetList($FromRec, $NumberOfRec, $OrderByFieldName, $OrderType)
	{
		if(!is_numeric($FromRec))
			$FromRec=0;
		if(!is_numeric($NumberOfRec))
			$NumberOfRec=0;
		if(strtoupper($OrderType)!="ASC" && strtoupper($OrderType)!="DESC")
			$OrderType = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select persons.PersonID
				,persons.pfname
				,persons.plname
				,persons.CardNumber
				,persons.EnterExitTypeID from projectmanagement.persons  ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_persons();
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->pfname=$rec["pfname"];
			$ret[$k]->plname=$rec["plname"];
			$ret[$k]->CardNumber=$rec["CardNumber"];
			$ret[$k]->EnterExitTypeID=$rec["EnterExitTypeID"];
			$k++;
		}
		return $ret;
	}
	
	static function Search($pfname, $plname, $OrderByFieldName, $OrderType)
	{
		if(strtoupper($OrderType)!="ASC" && strtoupper($OrderType)!="DESC")
			$OrderType = "";
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select persons.PersonID
				,persons.pfname
				,persons.plname
				,persons.CardNumber
				,persons.EnterExitTypeID from projectmanagement.persons  ";
		$query .= " where pfname like ? and plname like ? ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array("%".$pfname."%", "%".$plname."%"));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_persons();
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->pfname=$rec["pfname"];
			$ret[$k]->plname=$rec["plname"];
			$ret[$k]->CardNumber=$rec["CardNumber"];
			$ret[$k]->EnterExitTypeID=$rec["EnterExitTypeID"];
			$k++;
		}
		return $ret;
	}

	static function SearchResultCount($pfname, $plname)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(*) as TotalCount from projectmanagement.persons where ";
		$query .= " pfname like ? and plname like ? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array("%".$pfname."%", "%".$plname."%"));
		$i=0;
		if($rec=$res->fetch())
		{
		  return $rec["TotalCount"];
		}
		return 0;
	}
	
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $pfname: نام
	* @param $plname: نام خانوادگی
	* @param $CardNumber: شماره کارت
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $pfname, $plname, $CardNumber)
	{
		$ret = "";
		$obj = new be_persons();
		$obj->LoadDataFromDatabase($CurRecID);
		if($pfname!=$obj->pfname)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نام";
		}
		if($plname!=$obj->plname)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نام خانوادگی";
		}
		if($CardNumber!=$obj->CardNumber)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "شماره کارت";
		}
		return $ret;
	}
}
?>