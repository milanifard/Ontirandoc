<?php
/*
 تعریف کلاسها و متدهای مربوط به : برچسب کلاسها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/

/*
کلاس پایه: برچسب کلاسها
*/
class be_OntologyClassLabels
{
	public $OntologyClassLabelID;		//
	public $OntologyClassID;		//
	public $label;		//برچسب

	function be_OntologyClassLabels() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select OntologyClassLabels.* from projectmanagement.OntologyClassLabels  where  OntologyClassLabels.OntologyClassLabelID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyClassLabelID=$rec["OntologyClassLabelID"];
			$this->OntologyClassID=$rec["OntologyClassID"];
			$this->label=$rec["label"];
		}
	}
}
/*
کلاس مدیریت برچسب کلاسها
*/
class manage_OntologyClassLabels
{
	static function GetCount($OntologyClassID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(OntologyClassLabelID) as TotalCount from projectmanagement.OntologyClassLabels";
			$query .= " where OntologyClassID='".$OntologyClassID."'";
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
		$query = "select max(OntologyClassLabelID) as MaxID from projectmanagement.OntologyClassLabels";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $OntologyClassID: 
	* @param $label: برچسب
	* @return کد داده اضافه شده	*/
	static function Add($OntologyClassID, $label)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.OntologyClassLabels (";
		$query .= " OntologyClassID";
		$query .= ", label";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyClassID); 
		array_push($ValueListArray, $label); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_OntologyClassLabels::GetLastID();
		$mysql->audit("ثبت داده جدید در برچسب کلاسها با کد ".$LastID);
		return $LastID;
	}
	
	static function GetFirstLabel($OntologyClassID)
	{
	    $mysql = pdodb::getInstance();
	    $mysql->Prepare("select * from projectmanagement.OntologyClassLabels where OntologyClassID=? order by OntologyClassLabelID");
	    $res = $mysql->ExecuteStatement(array($OntologyClassID));
	    if($rec = $res->fetch())
	    {
		return $rec["label"];
	    }
	    return "";
	}

	static function UpdateOrInsertFirstLabel($OntologyClassID, $label)
	{
	    $k=0;
	    $mysql = pdodb::getInstance();
	    $mysql->Prepare("select * from projectmanagement.OntologyClassLabels where OntologyClassID=? order by OntologyClassLabelID");
	    $res = $mysql->ExecuteStatement(array($OntologyClassID));
	    if($rec = $res->fetch())
	    {
		$LastID = manage_OntologyClassLabels::Update($rec["OntologyClassLabelID"], $label);
	    }
	    else 
	    {
		$LastID = manage_OntologyClassLabels::Add($OntologyClassID, $label);
	    }
	    return $LastID;
	}
	
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $label: برچسب
	* @return 	*/
	static function Update($UpdateRecordID, $label)
	{
		$k=0;
		$LogDesc = manage_OntologyClassLabels::ComparePassedDataWithDB($UpdateRecordID, $label);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyClassLabels set ";
			$query .= " label=? ";
		$query .= " where OntologyClassLabelID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $label); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در برچسب کلاسها - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyClassLabels where OntologyClassLabelID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از برچسب کلاسها");
	}
	static function GetList($OntologyClassID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyClassLabels.OntologyClassLabelID
				,OntologyClassLabels.OntologyClassID
				,OntologyClassLabels.label from projectmanagement.OntologyClassLabels  ";
		$query .= " where OntologyClassID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_OntologyClassLabels();
			$ret[$k]->OntologyClassLabelID=$rec["OntologyClassLabelID"];
			$ret[$k]->OntologyClassID=$rec["OntologyClassID"];
			$ret[$k]->label=$rec["label"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $label: برچسب
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $label)
	{
		$ret = "";
		$obj = new be_OntologyClassLabels();
		$obj->LoadDataFromDatabase($CurRecID);
		if($label!=$obj->label)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "برچسب";
		}
		return $ret;
	}
}
?>