<?php
/*
 تعریف کلاسها و متدهای مربوط به : خصوصیات هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/

/*
کلاس پایه: خصوصیات هستان نگار
*/
class be_OntologyProperties
{
	public $OntologyPropertyID;		//
	public $OntologyID;		//
	public $PropertyTitle;		//عنوان
	public $PropertyType;		//نوع
	public $PropertyType_Desc;		/* شرح مربوط به نوع */
	public $IsFunctional;		//
	public $IsFunctional_Desc;		/* شرح مربوط به Functional */
	public $domain;		//حوزه
	public $range;		//بازه

	public $inverseOf;		//معکوس

	function be_OntologyProperties() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select OntologyProperties.* 
			, CASE OntologyProperties.PropertyType 
				WHEN 'DATATYPE' THEN 'DATATYPE' 
				WHEN 'OBJECT' THEN 'OBJECT' 
				END as PropertyType_Desc 
			, CASE OntologyProperties.IsFunctional 
				WHEN 'NO' THEN 'خیر' 
				WHEN 'YES' THEN 'بلی' 
				END as IsFunctional_Desc from projectmanagement.OntologyProperties  where  OntologyProperties.OntologyPropertyID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyPropertyID=$rec["OntologyPropertyID"];
			$this->OntologyID=$rec["OntologyID"];
			$this->PropertyTitle=$rec["PropertyTitle"];
			$this->PropertyType=$rec["PropertyType"];
			$this->PropertyType_Desc=$rec["PropertyType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->IsFunctional=$rec["IsFunctional"];
			$this->IsFunctional_Desc=$rec["IsFunctional_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->domain=$rec["domain"];
			$this->range=$rec["range"];
			$this->inverseOf=$rec["inverseOf"];
		}
	}
}
/*
کلاس مدیریت خصوصیات هستان نگار
*/
class manage_OntologyProperties
{
	static function GetCount($OntologyID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(OntologyPropertyID) as TotalCount from projectmanagement.OntologyProperties";
			$query .= " where OntologyID='".$OntologyID."'";
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
		$query = "select max(OntologyPropertyID) as MaxID from projectmanagement.OntologyProperties";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $OntologyID: 
	* @param $PropertyTitle: عنوان
	* @param $PropertyType: نوع
	* @param $IsFunctional: Functional
	* @param $domain: حوزه
	* @param $range: بازه
	* @param $inverseOf: معکوس
	* @return کد داده اضافه شده	*/
	static function Add($OntologyID, $PropertyTitle, $PropertyType, $IsFunctional, $domain, $range, $inverseOf)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.OntologyProperties (";
		$query .= " OntologyID";
		$query .= ", PropertyTitle";
		$query .= ", PropertyType";
		$query .= ", IsFunctional";
		$query .= ", domain";
		$query .= ", range";
		$query .= ", inverseOf";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyID); 
		array_push($ValueListArray, $PropertyTitle); 
		array_push($ValueListArray, $PropertyType); 
		array_push($ValueListArray, $IsFunctional); 
		array_push($ValueListArray, $domain); 
		array_push($ValueListArray, $range); 
		array_push($ValueListArray, $inverseOf); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_OntologyProperties::GetLastID();
		$mysql->audit("ثبت داده جدید در خصوصیات هستان نگار با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $PropertyTitle: عنوان
	* @param $PropertyType: نوع
	* @param $IsFunctional: Functional
	* @param $domain: حوزه
	* @param $range: بازه
	* @param $inverseOf: معکوس
	* @return 	*/
	static function Update($UpdateRecordID, $PropertyTitle, $PropertyType, $IsFunctional, $domain, $range, $inverseOf)
	{
		$k=0;
		$LogDesc = manage_OntologyProperties::ComparePassedDataWithDB($UpdateRecordID, $PropertyTitle, $PropertyType, $IsFunctional, $domain, $range, $inverseOf);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyProperties set ";
			$query .= " PropertyTitle=? ";
			$query .= ", PropertyType=? ";
			$query .= ", IsFunctional=? ";
			$query .= ", domain=? ";
			$query .= ", range=? ";
			$query .= ", inverseOf=? ";
		$query .= " where OntologyPropertyID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $PropertyTitle); 
		array_push($ValueListArray, $PropertyType); 
		array_push($ValueListArray, $IsFunctional); 
		array_push($ValueListArray, $domain); 
		array_push($ValueListArray, $range); 
		array_push($ValueListArray, $inverseOf); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در خصوصیات هستان نگار - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyProperties where OntologyPropertyID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از خصوصیات هستان نگار");
	}
	static function GetList($OntologyID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyProperties.OntologyPropertyID
				,OntologyProperties.OntologyID
				,OntologyProperties.PropertyTitle
				,OntologyProperties.PropertyType
				,OntologyProperties.IsFunctional
				,OntologyProperties.domain
				,OntologyProperties.range
				,OntologyProperties.inverseOf
			, CASE OntologyProperties.PropertyType 
				WHEN 'DATATYPE' THEN 'DATATYPE' 
				WHEN 'OBJECT' THEN 'OBJECT' 
				END as PropertyType_Desc 
			, CASE OntologyProperties.IsFunctional 
				WHEN 'NO' THEN 'خیر' 
				WHEN 'YES' THEN 'بلی' 
				END as IsFunctional_Desc  from projectmanagement.OntologyProperties  ";
		$query .= " where OntologyID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_OntologyProperties();
			$ret[$k]->OntologyPropertyID=$rec["OntologyPropertyID"];
			$ret[$k]->OntologyID=$rec["OntologyID"];
			$ret[$k]->PropertyTitle=$rec["PropertyTitle"];
			$ret[$k]->PropertyType=$rec["PropertyType"];
			$ret[$k]->PropertyType_Desc=$rec["PropertyType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->IsFunctional=$rec["IsFunctional"];
			$ret[$k]->IsFunctional_Desc=$rec["IsFunctional_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->domain=$rec["domain"];
			$ret[$k]->range=$rec["range"];
			$ret[$k]->inverseOf=$rec["inverseOf"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $PropertyTitle: عنوان
	* @param $PropertyType: نوع
	* @param $IsFunctional: Functional
	* @param $domain: حوزه
	* @param $range: بازه
	* @param $inverseOf: معکوس
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $PropertyTitle, $PropertyType, $IsFunctional, $domain, $range, $inverseOf)
	{
		$ret = "";
		$obj = new be_OntologyProperties();
		$obj->LoadDataFromDatabase($CurRecID);
		if($PropertyTitle!=$obj->PropertyTitle)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($PropertyType!=$obj->PropertyType)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نوع";
		}
		if($IsFunctional!=$obj->IsFunctional)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "Functional";
		}
		if($domain!=$obj->domain)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "حوزه";
		}
		if($range!=$obj->range)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "بازه";
		}
		if($inverseOf!=$obj->inverseOf)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "معکوس";
		}
		return $ret;
	}
	function ShowSummary($RecID)
	{
		$ret = "<br>";
		$ret .= "<table width=\"90%\" align=\"center\" border=\"1\" cellspacing=\"0\">";
		$ret .= "<tr>";
		$ret .= "<td>";
		$ret .= "<table width=\"100%\" border=\"0\">";
		$obj = new be_OntologyProperties();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>عنوان: </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->PropertyTitle, ENT_QUOTES, 'UTF-8');
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</table>";
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</table>";
		return $ret;
	}
	function ShowTabs($RecID, $CurrentPageName)
	{
		$ret = "<table align=\"center\" width=\"90%\" border=\"1\" cellspacing=\"0\">";
 		$ret .= "<tr>";
		$ret .= "<td width=\"50%\" ";
		if($CurrentPageName=="NewOntologyProperties")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='NewOntologyProperties.php?UpdateID=".$RecID."'>مشخصات اصلی</a></td>";
		$ret .= "<td width=\"50%\" ";
		if($CurrentPageName=="ManageOntologyPropertyLabels")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageOntologyPropertyLabels.php?OntologyPropertyID=".$RecID."'>برچسبهای خصوصیات</a></td>";
		$ret .= "</table>";
		return $ret;
	}
}
?>