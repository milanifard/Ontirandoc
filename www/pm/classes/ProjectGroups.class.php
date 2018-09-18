<?php
/*
 تعریف کلاسها و متدهای مربوط به : گروه های پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-5-13
*/

/*
کلاس پایه: گروه های پروژه
*/
class be_ProjectGroups
{
	public $ProjectGroupID;		//
	public $RelatedUnitID;		//کد واحد سازمانی مربوطه
	public $RelatedUnitID_Desc;		/* شرح مربوط به کد واحد سازمانی مربوطه */
	public $ProjectGroupName;		//نام گروه پروژه

	function be_ProjectGroups() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectGroups.* 
			, h1.ptitle  as h1_ptitle from projectmanagement.ProjectGroups 
			LEFT JOIN projectmanagement.org_units  h1 on (h1.ouid=ProjectGroups.RelatedUnitID)  where  ProjectGroups.ProjectGroupID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectGroupID=$rec["ProjectGroupID"];
			$this->RelatedUnitID=$rec["RelatedUnitID"];
			$this->RelatedUnitID_Desc=$rec["h1_ptitle"]; // محاسبه از روی جدول وابسته
			$this->ProjectGroupName=$rec["ProjectGroupName"];
		}
	}
}
/*
کلاس مدیریت گروه های پروژه
*/
class manage_ProjectGroups
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = "select count(ProjectGroupID) as TotalCount from projectmanagement.ProjectGroups";
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
		$query = "select max(ProjectGroupID) as MaxID from projectmanagement.ProjectGroups";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $RelatedUnitID: کد واحد سازمانی مربوطه
	* @param $ProjectGroupName: نام گروه پروژه
	* @return کد داده اضافه شده	*/
	static function Add($RelatedUnitID, $ProjectGroupName)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectGroups (";
		$query .= " RelatedUnitID";
		$query .= ", ProjectGroupName";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $RelatedUnitID); 
		array_push($ValueListArray, $ProjectGroupName); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectGroups::GetLastID();
		$mysql->audit("ثبت داده جدید در گروه های پروژه با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $RelatedUnitID: کد واحد سازمانی مربوطه
	* @param $ProjectGroupName: نام گروه پروژه
	* @return 	*/
	static function Update($UpdateRecordID, $RelatedUnitID, $ProjectGroupName)
	{
		$k=0;
		$LogDesc = manage_ProjectGroups::ComparePassedDataWithDB($UpdateRecordID, $RelatedUnitID, $ProjectGroupName);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectGroups set ";
			$query .= " RelatedUnitID=? ";
			$query .= ", ProjectGroupName=? ";
		$query .= " where ProjectGroupID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $RelatedUnitID); 
		array_push($ValueListArray, $ProjectGroupName); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در گروه های پروژه - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectGroups where ProjectGroupID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از گروه های پروژه");
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
		$query = "select ProjectGroups.ProjectGroupID
				,ProjectGroups.RelatedUnitID
				,ProjectGroups.ProjectGroupName
			, h1.ptitle  as h1_ptitle  from projectmanagement.ProjectGroups 
			LEFT JOIN projectmanagement.org_units  h1 on (h1.ouid=ProjectGroups.RelatedUnitID)  ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectGroups();
			$ret[$k]->ProjectGroupID=$rec["ProjectGroupID"];
			$ret[$k]->RelatedUnitID=$rec["RelatedUnitID"];
			$ret[$k]->RelatedUnitID_Desc=$rec["h1_ptitle"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectGroupName=$rec["ProjectGroupName"];
			$k++;
		}
		return $ret;
	}
	
	// لیست گروه های پروژه برای یک واحد سازمانی را به صورت اعضای یک سلکت باکس بر می گرداند
	static function CreateSelectOptions($ouid)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = "";
		$query = "select ProjectGroups.ProjectGroupID
				,ProjectGroups.RelatedUnitID
				,ProjectGroups.ProjectGroupName
			, h1.ptitle  as h1_ptitle  from projectmanagement.ProjectGroups 
			LEFT JOIN projectmanagement.org_units  h1 on (h1.ouid=ProjectGroups.RelatedUnitID)  ";
		$query .= " where RelatedUnitID=? ";
		$query .= " order by ProjectGroupName ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ouid));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["ProjectGroupID"]."'>".$rec["ProjectGroupName"];
		}
		return $ret;
	}
	
	
	/**
	* @param $RelatedUnitID: کد واحد سازمانی مربوطه
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($RelatedUnitID, $OtherConditions, $OrderByFieldName="", $OrderType="")
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectGroups.ProjectGroupID
				,ProjectGroups.RelatedUnitID
				,ProjectGroups.ProjectGroupName
			, h1.ptitle  as h1_ptitle  from projectmanagement.ProjectGroups 
			LEFT JOIN projectmanagement.org_units  h1 on (h1.ouid=ProjectGroups.RelatedUnitID)  ";
		$cond = "";
		if($RelatedUnitID!="0" && $RelatedUnitID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "ProjectGroups.RelatedUnitID=? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($RelatedUnitID!="0" && $RelatedUnitID!="") 
			array_push($ValueListArray, $RelatedUnitID); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectGroups();
			$ret[$k]->ProjectGroupID=$rec["ProjectGroupID"];
			$ret[$k]->RelatedUnitID=$rec["RelatedUnitID"];
			$ret[$k]->RelatedUnitID_Desc=$rec["h1_ptitle"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ProjectGroupName=$rec["ProjectGroupName"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $RelatedUnitID: کد واحد سازمانی مربوطه
	* @param $ProjectGroupName: نام گروه پروژه
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $RelatedUnitID, $ProjectGroupName)
	{
		$ret = "";
		$obj = new be_ProjectGroups();
		$obj->LoadDataFromDatabase($CurRecID);
		if($RelatedUnitID!=$obj->RelatedUnitID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "کد واحد سازمانی مربوطه";
		}
		if($ProjectGroupName!=$obj->ProjectGroupName)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نام گروه پروژه";
		}
		return $ret;
	}
}
?>
