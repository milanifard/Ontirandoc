<?php
/*
 تعریف کلاسها و متدهای مربوط به : سلسله مراتب کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/

/*
کلاس پایه: سلسله مراتب کلاسهای هستان نگار
*/
class be_OntologyClassHirarchy
{
	public $OntologyClassHirarchyID;		//
	public $OntologyClassID;		//کلاس فرزند
	public $OntologyClassParentID;		//کلاس پدر
	public $OntologyClassParentID_Desc;		/* شرح مربوط به کلاس پدر */

	function be_OntologyClassHirarchy() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select OntologyClassHirarchy.* 
			, p2.ClassTitle  as p2_ClassTitle from projectmanagement.OntologyClassHirarchy 
			LEFT JOIN projectmanagement.OntologyClasses  p2 on (p2.OntologyClassID=OntologyClassHirarchy.OntologyClassParentID)  where  OntologyClassHirarchy.OntologyClassHirarchyID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyClassHirarchyID=$rec["OntologyClassHirarchyID"];
			$this->OntologyClassID=$rec["OntologyClassID"];
			$this->OntologyClassParentID=$rec["OntologyClassParentID"];
			$this->OntologyClassParentID_Desc=$rec["p2_ClassTitle"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت سلسله مراتب کلاسهای هستان نگار
*/
class manage_OntologyClassHirarchy
{
	static function GetCount($OntologyClassID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(OntologyClassHirarchyID) as TotalCount from projectmanagement.OntologyClassHirarchy";
			$query .= " where OntologyClassID='".$OntologyClassID."'";
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
		$query = "select max(OntologyClassHirarchyID) as MaxID from projectmanagement.OntologyClassHirarchy";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $OntologyClassID: کلاس فرزند
	* @param $OntologyClassParentID: کلاس پدر
	* @return کد داده اضافه شده	*/
	static function Add($OntologyClassID, $OntologyClassParentID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.OntologyClassHirarchy (";
		$query .= " OntologyClassID";
		$query .= ", OntologyClassParentID";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyClassID); 
		array_push($ValueListArray, $OntologyClassParentID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_OntologyClassHirarchy::GetLastID();
		$mysql->audit("ثبت داده جدید در سلسله مراتب کلاسهای هستان نگار با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $OntologyClassParentID: کلاس پدر
	* @return 	*/
	static function Update($UpdateRecordID, $OntologyClassParentID)
	{
		$k=0;
		$LogDesc = manage_OntologyClassHirarchy::ComparePassedDataWithDB($UpdateRecordID, $OntologyClassParentID);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyClassHirarchy set ";
			$query .= " OntologyClassParentID=? ";
		$query .= " where OntologyClassHirarchyID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyClassParentID); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در سلسله مراتب کلاسهای هستان نگار - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyClassHirarchy where OntologyClassHirarchyID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از سلسله مراتب کلاسهای هستان نگار");
	}

	static function RemoveRelation($UpdateRecordID, $OntologyClassParentID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyClassHirarchy where ";
		$query .= " OntologyClassParentID=? ";
		$query .= " and OntologyClassID=?";
		//echo $query;
		$ValueListArray = array();
		//echo $UpdateRecordID."<br>";
		//echo $OntologyClassParentID."<br>";
		array_push($ValueListArray, $OntologyClassParentID); 		
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از سلسله مراتب کلاسهای هستان نگار");
	}
	
	static function GetList($OntologyClassID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyClassHirarchy.OntologyClassHirarchyID
				,OntologyClassHirarchy.OntologyClassID
				,OntologyClassHirarchy.OntologyClassParentID
			, p2.ClassTitle  as p2_ClassTitle  
			,(select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClassHirarchy.OntologyClassParentID) as label
			from projectmanagement.OntologyClassHirarchy 
			LEFT JOIN projectmanagement.OntologyClasses  p2 on (p2.OntologyClassID=OntologyClassHirarchy.OntologyClassParentID)  ";
		$query .= " where OntologyClassHirarchy.OntologyClassID=? order by label";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_OntologyClassHirarchy();
			$ret[$k]->OntologyClassHirarchyID=$rec["OntologyClassHirarchyID"];
			$ret[$k]->OntologyClassID=$rec["OntologyClassID"];
			$ret[$k]->OntologyClassParentID=$rec["OntologyClassParentID"];
			$ret[$k]->OntologyClassParentID_Desc=$rec["label"]." (".$rec["p2_ClassTitle"].")"; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}
	
	static function GetParentList($OntologyClassID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = "";
		$query = "select OntologyClassHirarchy.OntologyClassHirarchyID
				,OntologyClassHirarchy.OntologyClassID
				,OntologyClassHirarchy.OntologyClassParentID
			, p2.ClassTitle  as p2_ClassTitle  from projectmanagement.OntologyClassHirarchy 
			LEFT JOIN projectmanagement.OntologyClasses  p2 
			on (p2.OntologyClassID=OntologyClassHirarchy.OntologyClassID)  ";
		$query .= " where OntologyClassHirarchy.OntologyClassParentID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		$i=0;
		while($rec=$res->fetch())
		{
		  if($i>0)
		    $ret .= ", ";
		  $ret .= $rec["p2_ClassTitle"];
		  $i++;
		}
		return $ret;
	}	

	static function GetParentListArray($OntologyClassID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyClassHirarchy.OntologyClassHirarchyID
				,OntologyClassHirarchy.OntologyClassID
				,OntologyClassHirarchy.OntologyClassParentID
			, p2.ClassTitle  as p2_ClassTitle  
			, (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClassHirarchy.OntologyClassID) as label
			from projectmanagement.OntologyClassHirarchy 
			LEFT JOIN projectmanagement.OntologyClasses  p2 
			on (p2.OntologyClassID=OntologyClassHirarchy.OntologyClassID)  ";
		$query .= " where OntologyClassHirarchy.OntologyClassParentID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		$i=0;
		while($rec=$res->fetch())
		{
		  $ret[$i]["OntologyClassHirarchyID"] = $rec["OntologyClassHirarchyID"];
		  $ret[$i]["ClassTitle"] = $rec["p2_ClassTitle"];
		  $ret[$i]["OntologyClassID"] = $rec["OntologyClassID"];
		  $ret[$i]["label"] = $rec["label"];
		  $i++;
		}
		return $ret;
	}	


	static function GetChildListArray($OntologyClassID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyClassHirarchy.OntologyClassHirarchyID
				,OntologyClassHirarchy.OntologyClassID
				,OntologyClassHirarchy.OntologyClassParentID
			, p2.ClassTitle  as p2_ClassTitle  
			, (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClassHirarchy.OntologyClassParentID) as label
			from projectmanagement.OntologyClassHirarchy 
			LEFT JOIN projectmanagement.OntologyClasses  p2 
			on (p2.OntologyClassID=OntologyClassHirarchy.OntologyClassParentID)  ";
		$query .= " where OntologyClassHirarchy.OntologyClassID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		$i=0;
		while($rec=$res->fetch())
		{
		  $ret[$i]["OntologyClassHirarchyID"] = $rec["OntologyClassHirarchyID"];
		  $ret[$i]["ClassTitle"] = $rec["p2_ClassTitle"];
		  $ret[$i]["OntologyClassID"] = $rec["OntologyClassParentID"];
		  $ret[$i]["label"] = $rec["label"];
		  $i++;
		}
		return $ret;
	}	
	
	static function HasHirarchyRelation($OntologyClassID1, $OntologyClassID2)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(*) as tcount from projectmanagement.OntologyClassHirarchy ch where
				(OntologyClassID=? and OntologyClassParentID=?) or
				(OntologyClassID=? and OntologyClassParentID=?) or 
				(OntologyClassID=? and OntologyClassParentID in (select OntologyClassParentID from projectmanagement.OntologyClassHirarchy where OntologyClassID=ch.OntologyClassParentID and OntologyClassParentID=?)
				)";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID1, $OntologyClassID2, $OntologyClassID2, $OntologyClassID1, $OntologyClassID1, $OntologyClassID2));	
		$rec = $res->fetch();
		if($rec["tcount"]>0)
			return true;
		return false;
	}
	
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $OntologyClassParentID: کلاس پدر
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $OntologyClassParentID)
	{
		$ret = "";
		$obj = new be_OntologyClassHirarchy();
		$obj->LoadDataFromDatabase($CurRecID);
		if($OntologyClassParentID!=$obj->OntologyClassParentID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "کلاس پدر";
		}
		return $ret;
	}
}
?>