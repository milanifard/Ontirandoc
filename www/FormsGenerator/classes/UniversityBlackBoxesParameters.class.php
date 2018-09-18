<?php
/*
 تعریف کلاسها و متدهای مربوط به : پارامترهای ورودی جعبه سیاه های محاسباتی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-4-24
*/

/*
کلاس پایه: پارامترهای ورودی جعبه سیاه های محاسباتی
*/
class be_UniversityBlackBoxesParameters
{
	public $UniversityBlackBoxesParameterID;		//
	public $UniversityCalculationBlackBoxID;		//جعبه سیاه مربوطه
	public $title;		//عنوان
	public $OrderNo;		//
	public $UniversityEntityID;		//مشخصه مربوطه
	public $UniversityEntityID_Desc;		/* شرح مربوط به مشخصه مربوطه */
	public $KeyName;		//کلید مورد استفاده در پرس و جو یا کد

	function be_UniversityBlackBoxesParameters() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select UniversityBlackBoxesParameters.* 
			, f4.title  as f4_title from formsgenerator.UniversityBlackBoxesParameters 
			LEFT JOIN formsgenerator.UniversityEntities  f4 on (f4.UniversityEntityID=UniversityBlackBoxesParameters.UniversityEntityID)  where  UniversityBlackBoxesParameters.UniversityBlackBoxesParameterID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->UniversityBlackBoxesParameterID=$rec["UniversityBlackBoxesParameterID"];
			$this->UniversityCalculationBlackBoxID=$rec["UniversityCalculationBlackBoxID"];
			$this->title=$rec["title"];
			$this->OrderNo=$rec["OrderNo"];
			$this->UniversityEntityID=$rec["UniversityEntityID"];
			$this->UniversityEntityID_Desc=$rec["f4_title"]; // محاسبه از روی جدول وابسته
			$this->KeyName=$rec["KeyName"];
		}
	}
}
/*
کلاس مدیریت پارامترهای ورودی جعبه سیاه های محاسباتی
*/
class manage_UniversityBlackBoxesParameters
{
	static function GetCount($UniversityCalculationBlackBoxID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(UniversityBlackBoxesParameterID) as TotalCount from formsgenerator.UniversityBlackBoxesParameters";
			$query .= " where UniversityCalculationBlackBoxID='".$UniversityCalculationBlackBoxID."'";
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
		$query = "select max(UniversityBlackBoxesParameterID) as MaxID from formsgenerator.UniversityBlackBoxesParameters";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $UniversityCalculationBlackBoxID: جعبه سیاه مربوطه
	* @param $title: عنوان
	* @param $OrderNo: ترتیب
	* @param $UniversityEntityID: مشخصه مربوطه
	* @param $KeyName: کلید مورد استفاده در پرس و جو یا کد
	* @return کد داده اضافه شده	*/
	static function Add($UniversityCalculationBlackBoxID, $title, $OrderNo, $UniversityEntityID, $KeyName)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into formsgenerator.UniversityBlackBoxesParameters (";
		$query .= " UniversityCalculationBlackBoxID";
		$query .= ", title";
		$query .= ", OrderNo";
		$query .= ", UniversityEntityID";
		$query .= ", KeyName";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $UniversityCalculationBlackBoxID); 
		array_push($ValueListArray, $title); 
		array_push($ValueListArray, $OrderNo); 
		array_push($ValueListArray, $UniversityEntityID); 
		array_push($ValueListArray, $KeyName); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_UniversityBlackBoxesParameters::GetLastID();
		$mysql->audit("ثبت داده جدید در پارامترهای ورودی جعبه سیاه های محاسباتی با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $title: عنوان
	* @param $OrderNo: ترتیب
	* @param $UniversityEntityID: مشخصه مربوطه
	* @param $KeyName: کلید مورد استفاده در پرس و جو یا کد
	* @return 	*/
	static function Update($UpdateRecordID, $title, $OrderNo, $UniversityEntityID, $KeyName)
	{
		$k=0;
		$LogDesc = manage_UniversityBlackBoxesParameters::ComparePassedDataWithDB($UpdateRecordID, $title, $OrderNo, $UniversityEntityID, $KeyName);
		$mysql = pdodb::getInstance();
		$query = "update formsgenerator.UniversityBlackBoxesParameters set ";
			$query .= " title=? ";
			$query .= ", OrderNo=? ";
			$query .= ", UniversityEntityID=? ";
			$query .= ", KeyName=? ";
		$query .= " where UniversityBlackBoxesParameterID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $title); 
		array_push($ValueListArray, $OrderNo); 
		array_push($ValueListArray, $UniversityEntityID); 
		array_push($ValueListArray, $KeyName); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پارامترهای ورودی جعبه سیاه های محاسباتی - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from formsgenerator.UniversityBlackBoxesParameters where UniversityBlackBoxesParameterID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پارامترهای ورودی جعبه سیاه های محاسباتی");
	}
	static function GetList($UniversityCalculationBlackBoxID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select UniversityBlackBoxesParameters.UniversityBlackBoxesParameterID
				,UniversityBlackBoxesParameters.UniversityCalculationBlackBoxID
				,UniversityBlackBoxesParameters.title
				,UniversityBlackBoxesParameters.OrderNo
				,UniversityBlackBoxesParameters.UniversityEntityID
				,UniversityBlackBoxesParameters.KeyName
			, f4.title  as f4_title  from formsgenerator.UniversityBlackBoxesParameters 
			LEFT JOIN formsgenerator.UniversityEntities  f4 on (f4.UniversityEntityID=UniversityBlackBoxesParameters.UniversityEntityID)  ";
		$query .= " where UniversityCalculationBlackBoxID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversityCalculationBlackBoxID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_UniversityBlackBoxesParameters();
			$ret[$k]->UniversityBlackBoxesParameterID=$rec["UniversityBlackBoxesParameterID"];
			$ret[$k]->UniversityCalculationBlackBoxID=$rec["UniversityCalculationBlackBoxID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->OrderNo=$rec["OrderNo"];
			$ret[$k]->UniversityEntityID=$rec["UniversityEntityID"];
			$ret[$k]->UniversityEntityID_Desc=$rec["f4_title"]; // محاسبه از روی جدول وابسته
			$ret[$k]->KeyName=$rec["KeyName"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $title: عنوان
	* @param $OrderNo: ترتیب
	* @param $UniversityEntityID: مشخصه مربوطه
	* @param $KeyName: کلید مورد استفاده در پرس و جو یا کد
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $title, $OrderNo, $UniversityEntityID, $KeyName)
	{
		$ret = "";
		$obj = new be_UniversityBlackBoxesParameters();
		$obj->LoadDataFromDatabase($CurRecID);
		if($title!=$obj->title)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($OrderNo!=$obj->OrderNo)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "ترتیب";
		}
		if($UniversityEntityID!=$obj->UniversityEntityID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "مشخصه مربوطه";
		}
		if($KeyName!=$obj->KeyName)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "کلید مورد استفاده در پرس و جو یا کد";
		}
		return $ret;
	}
}
?>