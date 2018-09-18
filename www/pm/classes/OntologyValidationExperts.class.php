<?php
/*
 تعریف کلاسها و متدهای مربوط به : خبرگان بررسی کننده هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 95-5-24
*/

/*
کلاس پایه: خبرگان بررسی کننده هستان نگار
*/
class be_OntologyValidationExperts
{
	public $OntologyValidationExpertID;		//
	public $ExpertFullName;		//نام و نام خانوادگی
	public $ExpertDesciption;		//شرح پست/شغل/تخصص
	public $ExpertEnterCode;		//کد ورود خبره به سایت
	public $ValidationStatus;		//وضعیت ارزیابی
	public $ValidationStatus_Desc;		/* شرح مربوط به وضعیت ارزیابی */
	public $OntologyID;		//هستان نگار

	function be_OntologyValidationExperts() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select OntologyValidationExperts.* 
			, CASE OntologyValidationExperts.ValidationStatus 
				WHEN 'NOT_START' THEN 'ارزیابی نشده' 
				WHEN 'IN_PROGRESS' THEN 'در حال ارزیابی' 
				WHEN 'DONE' THEN 'ارزیابی شده' 
				END as ValidationStatus_Desc from projectmanagement.OntologyValidationExperts  where  OntologyValidationExperts.OntologyValidationExpertID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyValidationExpertID=$rec["OntologyValidationExpertID"];
			$this->ExpertFullName=$rec["ExpertFullName"];
			$this->ExpertDesciption=$rec["ExpertDesciption"];
			$this->ExpertEnterCode=$rec["ExpertEnterCode"];
			$this->ValidationStatus=$rec["ValidationStatus"];
			$this->ValidationStatus_Desc=$rec["ValidationStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->OntologyID=$rec["OntologyID"];
		}
	}
}
/*
کلاس مدیریت خبرگان بررسی کننده هستان نگار
*/
class manage_OntologyValidationExperts
{
	static function GetCount($OntologyID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(OntologyValidationExpertID) as TotalCount from projectmanagement.OntologyValidationExperts";
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
		$query = "select max(OntologyValidationExpertID) as MaxID from projectmanagement.OntologyValidationExperts";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ExpertFullName: نام و نام خانوادگی
	* @param $ExpertDesciption: شرح پست/شغل/تخصص
	* @param $ExpertEnterCode: کد ورود خبره به سایت
	* @param $ValidationStatus: وضعیت ارزیابی
	* @param $OntologyID: هستان نگار
	* @return کد داده اضافه شده	*/
	static function Add($ExpertFullName, $ExpertDesciption, $ExpertEnterCode, $ValidationStatus, $OntologyID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.OntologyValidationExperts (";
		$query .= " ExpertFullName";
		$query .= ", ExpertDesciption";
		$query .= ", ExpertEnterCode";
		$query .= ", ValidationStatus";
		$query .= ", OntologyID";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ExpertFullName); 
		array_push($ValueListArray, $ExpertDesciption); 
		array_push($ValueListArray, $ExpertEnterCode); 
		array_push($ValueListArray, $ValidationStatus); 
		array_push($ValueListArray, $OntologyID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_OntologyValidationExperts::GetLastID();
		$mysql->audit("ثبت داده جدید در خبرگان بررسی کننده هستان نگار با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $ExpertFullName: نام و نام خانوادگی
	* @param $ExpertDesciption: شرح پست/شغل/تخصص
	* @param $ExpertEnterCode: کد ورود خبره به سایت
	* @param $ValidationStatus: وضعیت ارزیابی
	* @return 	*/
	static function Update($UpdateRecordID, $ExpertFullName, $ExpertDesciption, $ExpertEnterCode, $ValidationStatus)
	{
		$k=0;
		$LogDesc = manage_OntologyValidationExperts::ComparePassedDataWithDB($UpdateRecordID, $ExpertFullName, $ExpertDesciption, $ExpertEnterCode, $ValidationStatus);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyValidationExperts set ";
			$query .= " ExpertFullName=? ";
			$query .= ", ExpertDesciption=? ";
			$query .= ", ExpertEnterCode=? ";
			$query .= ", ValidationStatus=? ";
		$query .= " where OntologyValidationExpertID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $ExpertFullName); 
		array_push($ValueListArray, $ExpertDesciption); 
		array_push($ValueListArray, $ExpertEnterCode); 
		array_push($ValueListArray, $ValidationStatus); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در خبرگان بررسی کننده هستان نگار - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyValidationExperts where OntologyValidationExpertID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از خبرگان بررسی کننده هستان نگار");
	}
	static function GetList($OntologyID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyValidationExperts.OntologyValidationExpertID
				,OntologyValidationExperts.ExpertFullName
				,OntologyValidationExperts.ExpertDesciption
				,OntologyValidationExperts.ExpertEnterCode
				,OntologyValidationExperts.ValidationStatus
				,OntologyValidationExperts.OntologyID
			, CASE OntologyValidationExperts.ValidationStatus 
				WHEN 'NOT_START' THEN 'ارزیابی نشده' 
				WHEN 'IN_PROGRESS' THEN 'در حال ارزیابی' 
				WHEN 'DONE' THEN 'ارزیابی شده' 
				END as ValidationStatus_Desc  from projectmanagement.OntologyValidationExperts  ";
		$query .= " where OntologyID=? ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_OntologyValidationExperts();
			$ret[$k]->OntologyValidationExpertID=$rec["OntologyValidationExpertID"];
			$ret[$k]->ExpertFullName=$rec["ExpertFullName"];
			$ret[$k]->ExpertDesciption=$rec["ExpertDesciption"];
			$ret[$k]->ExpertEnterCode=$rec["ExpertEnterCode"];
			$ret[$k]->ValidationStatus=$rec["ValidationStatus"];
			$ret[$k]->ValidationStatus_Desc=$rec["ValidationStatus_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->OntologyID=$rec["OntologyID"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $ExpertFullName: نام و نام خانوادگی
	* @param $ExpertDesciption: شرح پست/شغل/تخصص
	* @param $ExpertEnterCode: کد ورود خبره به سایت
	* @param $ValidationStatus: وضعیت ارزیابی
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $ExpertFullName, $ExpertDesciption, $ExpertEnterCode, $ValidationStatus)
	{
		$ret = "";
		$obj = new be_OntologyValidationExperts();
		$obj->LoadDataFromDatabase($CurRecID);
		if($ExpertFullName!=$obj->ExpertFullName)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نام و نام خانوادگی";
		}
		if($ExpertDesciption!=$obj->ExpertDesciption)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "شرح پست/شغل/تخصص";
		}
		if($ExpertEnterCode!=$obj->ExpertEnterCode)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "کد ورود خبره به سایت";
		}
		if($ValidationStatus!=$obj->ValidationStatus)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "وضعیت ارزیابی";
		}
		return $ret;
	}
}
?>