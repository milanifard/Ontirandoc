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
		$res = $mysql->ExecuteStatement ([$RecID]);
		if($rec=$res->fetch())
		{
			foreach($rec as $key => $value){
				$this->$key = $value;
			}
			$this->UniversityEntityID_Desc=$rec["f4_title"]; // محاسبه از روی جدول وابسته
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
		$ValueListArray = [$UniversityCalculationBlackBoxID, 
											$title, 
											$OrderNo, 
											$UniversityEntityID, 
											$KeyName];
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
		$LogDesc = manage_UniversityBlackBoxesParameters::ComparePassedDataWithDB($UpdateRecordID, $title, $OrderNo, $UniversityEntityID, $KeyName);
		$mysql = pdodb::getInstance();
		$query = "update formsgenerator.UniversityBlackBoxesParameters set ";
			$query .= " title=? ";
			$query .= ", OrderNo=? ";
			$query .= ", UniversityEntityID=? ";
			$query .= ", KeyName=? ";
		$query .= " where UniversityBlackBoxesParameterID=?";
		$ValueListArray = [$title, 
											$OrderNo, 
											$UniversityEntityID, 
											$KeyName, 
											$UpdateRecordID];
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
		$mysql->ExecuteStatement([$RemoveRecordID]);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پارامترهای ورودی جعبه سیاه های محاسباتی");
	}
	static function GetList($UniversityCalculationBlackBoxID)
	{
		$mysql = pdodb::getInstance();
		$ret = [];
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
		$res = $mysql->ExecuteStatement([$UniversityCalculationBlackBoxID]);
		while($rec=$res->fetch())
		{
			$item = new be_UniversityBlackBoxesParameters();
			foreach($rec as $key => $value){
				$item->$key = $value;
			}
			$item->UniversityEntityID_Desc=$rec["f4_title"]; // محاسبه از روی جدول وابسته
			array_push($ret, $item);
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
			if(!empty($ret))
				$ret .= " - ";
			$ret .= C_TITLE;
		}
		if($OrderNo!=$obj->OrderNo)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= C_ORDER;
		}
		if($UniversityEntityID!=$obj->UniversityEntityID)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= C_ENTITY;
		}
		if($KeyName!=$obj->KeyName)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= C_USED_KEY;
		}
		return $ret;
	}
}
?>
