<?php
/*
 تعریف کلاسها و متدهای مربوط به : برچسبهای خصوصیات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-2
*/

/*
کلاس پایه: برچسبهای خصوصیات
*/
class be_OntologyPropertyLabels
{
	public $OntologyPropertyLabelID;		//
	public $OntologyPropertyID;		//
	public $label;		//برچسب

	function be_OntologyPropertyLabels() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select OntologyPropertyLabels.* from projectmanagement.OntologyPropertyLabels  where  OntologyPropertyLabels.OntologyPropertyLabelID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyPropertyLabelID=$rec["OntologyPropertyLabelID"];
			$this->OntologyPropertyID=$rec["OntologyPropertyID"];
			$this->label=$rec["label"];
		}
	}
}
/*
کلاس مدیریت برچسبهای خصوصیات
*/
class manage_OntologyPropertyLabels
{
	static function GetCount($OntologyPropertyID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(OntologyPropertyLabelID) as TotalCount from projectmanagement.OntologyPropertyLabels";
			$query .= " where OntologyPropertyID='".$OntologyPropertyID."'";
		$res = $mysql->Execute($query);
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	static function GetLastID()
	{
		$mysql = pdodb::getInstance();
		$query = "select max(OntologyPropertyLabelID) as MaxID from projectmanagement.OntologyPropertyLabels";
		$res = $mysql->Execute($query);
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $OntologyPropertyID: 
	* @param $label: برچسب
	* @return کد داده اضافه شده	*/
	static function Add($OntologyPropertyID, $label)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.OntologyPropertyLabels (";
		$query .= " OntologyPropertyID";
		$query .= ", label";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyPropertyID); 
		array_push($ValueListArray, $label); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_OntologyPropertyLabels::GetLastID();
		$mysql->audit("ثبت داده جدید در برچسبهای خصوصیات با کد ".$LastID);
		return $LastID;
	}
	
	static function GetFirstLabel($OntologyPropertyID)
	{
	    $mysql = pdodb::getInstance();
	    $mysql->Prepare("select * from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=? order by OntologyPropertyLabelID");
	    $res = $mysql->ExecuteStatement(array($OntologyPropertyID));
	    if($rec = $res->fetch())
	    {
		return $rec["label"];
	    }
	    return "";
	}

	static function UpdateOrInsertFirstLabel($OntologyPropertyID, $label)
	{
	    $LastID = 0;
	    $k=0;
	    $mysql = pdodb::getInstance();
	    $mysql->Prepare("select * from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=? order by OntologyPropertyLabelID");
	    $res = $mysql->ExecuteStatement(array($OntologyPropertyID));
	    if($rec = $res->fetch())
	    {
		manage_OntologyPropertyLabels::Update($rec["OntologyPropertyLabelID"], $label);
		$LastID = $rec["OntologyPropertyLabelID"];
	    }
	    else 
	    {
		$LastID = manage_OntologyPropertyLabels::Add($OntologyPropertyID, $label);
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
		$LogDesc = manage_OntologyPropertyLabels::ComparePassedDataWithDB($UpdateRecordID, $label);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyPropertyLabels set ";
			$query .= " label=? ";
		$query .= " where OntologyPropertyLabelID=?";
		//echo $query."<br>";
		//echo $label."<br>".$UpdateRecordID;
		$ValueListArray = array();
		array_push($ValueListArray, $label); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در برچسبهای خصوصیات - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyPropertyLabels where OntologyPropertyLabelID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از برچسبهای خصوصیات");
	}
	static function GetList($OntologyPropertyID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyPropertyLabels.OntologyPropertyLabelID
				,OntologyPropertyLabels.OntologyPropertyID
				,OntologyPropertyLabels.label from projectmanagement.OntologyPropertyLabels  ";
		$query .= " where OntologyPropertyID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_OntologyPropertyLabels();
			$ret[$k]->OntologyPropertyLabelID=$rec["OntologyPropertyLabelID"];
			$ret[$k]->OntologyPropertyID=$rec["OntologyPropertyID"];
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
		$obj = new be_OntologyPropertyLabels();
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