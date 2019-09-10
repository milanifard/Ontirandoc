<?php
/*
 تعریف کلاسها و متدهای مربوط به : پرداختی ها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-1-2
*/

/*
کلاس پایه: پرداختی ها
*/
class be_payments
{
	public $PaymentID;		//
	public $PersonID;		//
	public $amount;		//مبلغ
	public $PaymentDate;		//تاریخ
	public $PaymentDate_Shamsi;		/* مقدار شمسی معادل با تاریخ */
	public $PayType;		//نوع پرداخت
	public $PayType_Desc;		/* شرح مربوط به نوع پرداخت */
	public $PaymentDescription;		//
	public $PaymentFile;		//فایل رسید
	public $PaymentFileName;		//نام فایل رسید

	function be_payments() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select payments.* 
			, g2j(payments.PaymentDate) as PaymentDate_Shamsi 
			, CASE payments.PayType 
				WHEN 'TRANSFER' THEN 'واریز به حساب' 
				WHEN 'CHECK' THEN 'چک' 
				WHEN 'CASH' THEN 'نقد' 
				END as PayType_Desc from projectmanagement.payments  where  payments.PaymentID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->PaymentID=$rec["PaymentID"];
			$this->PersonID=$rec["PersonID"];
			$this->amount=$rec["amount"];
			$this->PaymentDate=$rec["PaymentDate"];
			$this->PaymentDate_Shamsi=$rec["PaymentDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->PayType=$rec["PayType"];
			$this->PayType_Desc=$rec["PayType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->PaymentDescription=$rec["PaymentDescription"];
			$this->PaymentFileName=$rec["PaymentFileName"];
			$this->PaymentFile=$rec["PaymentFile"];
		}
	}
}
/*
کلاس مدیریت پرداختی ها
*/
class manage_payments
{
	static function GetCount($PersonID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(PaymentID) as TotalCount from projectmanagement.payments";
			$query .= " where PersonID='".$PersonID."'";
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
		$query = "select max(PaymentID) as MaxID from projectmanagement.payments";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $PersonID: 
	* @param $amount: مبلغ
	* @param $PaymentDate: تاریخ
	* @param $PayType: نوع پرداخت
	* @param $PaymentDescription: توضیحات
	* @param $PaymentFile: 
	* @param $PaymentFileName: نام فایل
	* @return کد داده اضافه شده	*/
	static function Add($PersonID, $amount, $PaymentDate, $PayType, $PaymentDescription, $PaymentFile, $PaymentFileName)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.payments (";
		$query .= " PersonID";
		$query .= ", amount";
		$query .= ", PaymentDate";
		$query .= ", PayType";
		$query .= ", PaymentDescription";
		$query .= ", PaymentFile";
		$query .= ", PaymentFileName";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , '".$PaymentFile."', ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $PersonID); 
		array_push($ValueListArray, $amount); 
		array_push($ValueListArray, $PaymentDate); 
		array_push($ValueListArray, $PayType); 
		array_push($ValueListArray, $PaymentDescription); 
		array_push($ValueListArray, $PaymentFileName); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_payments::GetLastID();
		$mysql->audit("ثبت داده جدید در پرداختی ها با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $amount: مبلغ
	* @param $PaymentDate: تاریخ
	* @param $PayType: نوع پرداخت
	* @param $PaymentDescription: توضیحات
	* @param $PaymentFile: 
	* @param $PaymentFileName: نام فایل
	* @return 	*/
	static function Update($UpdateRecordID, $amount, $PaymentDate, $PayType, $PaymentDescription, $PaymentFile, $PaymentFileName)
	{
		$k=0;
		$LogDesc = manage_payments::ComparePassedDataWithDB($UpdateRecordID, $amount, $PaymentDate, $PayType, $PaymentDescription);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.payments set ";
			$query .= " amount=? ";
			$query .= ", PaymentDate=? ";
			$query .= ", PayType=? ";
			$query .= ", PaymentDescription=? ";
		if($PaymentFileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", PaymentFileName=?, PaymentFile='".$PaymentFile."' ";
		}
		$query .= " where PaymentID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $amount); 
		array_push($ValueListArray, $PaymentDate); 
		array_push($ValueListArray, $PayType); 
		array_push($ValueListArray, $PaymentDescription); 
		if($PaymentFileName!="")
		{ 
			array_push($ValueListArray, $PaymentFileName); 
		} 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پرداختی ها - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.payments where PaymentID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پرداختی ها");
	}
	static function GetList($PersonID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select payments.PaymentID
				,payments.PersonID
				,payments.amount
				,payments.PaymentDate
				,payments.PayType
				,payments.PaymentDescription
				,payments.PaymentFileName
			, g2j(PaymentDate) as PaymentDate_Shamsi 
			, CASE payments.PayType 
				WHEN 'TRANSFER' THEN 'واریز به حساب' 
				WHEN 'CHECK' THEN 'چک' 
				WHEN 'CASH' THEN 'نقد' 
				END as PayType_Desc  from projectmanagement.payments  ";
		$query .= " where PersonID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PersonID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_payments();
			$ret[$k]->PaymentID=$rec["PaymentID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->amount=$rec["amount"];
			$ret[$k]->PaymentDate=$rec["PaymentDate"];
			$ret[$k]->PaymentDate_Shamsi=$rec["PaymentDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->PayType=$rec["PayType"];
			$ret[$k]->PayType_Desc=$rec["PayType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->PaymentDescription=$rec["PaymentDescription"];
			$ret[$k]->PaymentFileName=$rec["PaymentFileName"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $amount: مبلغ
	* @param $PaymentDate: تاریخ
	* @param $PayType: نوع پرداخت
	* @param $PaymentDescription: توضیحات
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $amount, $PaymentDate, $PayType, $PaymentDescription)
	{
		$ret = "";
		$obj = new be_payments();
		$obj->LoadDataFromDatabase($CurRecID);
		if($amount!=$obj->amount)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "مبلغ";
		}
		if($PaymentDate." 00:00:00"!=$obj->PaymentDate)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "تاریخ";
		}
		if($PayType!=$obj->PayType)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نوع پرداخت";
		}
		if($PaymentDescription!=$obj->PaymentDescription)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "توضیحات";
		}
		return $ret;
	}
}
?>