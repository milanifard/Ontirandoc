<?php
/*
 تعریف کلاسها و متدهای مربوط به : پیامها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-5
*/

/*
کلاس پایه: پیامها
*/
class be_messages
{
	public $MessageID;		//
	public $MessageBody;		//متن پیام
	public $RelatedFileName;		//
	public $FileContent;		//
	public $ImageFileName;		//
	public $ImageFileContent;		//
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $StartDate;		//زمان شروع نمایش
	public $StartDate_Shamsi;		/* مقدار شمسی معادل با زمان شروع نمایش */
	public $EndDate;		//زمان پایان نمایش
	public $EndDate_Shamsi;		/* مقدار شمسی معادل با زمان پایان نمایش */
	public $CreateDate;		//زمان ایجاد
	public $CreateDate_Shamsi;		/* مقدار شمسی معادل با زمان ایجاد */

	function be_messages() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select messages.MessageID
				,messages.MessageBody
				,messages.RelatedFileName
				,messages.FileContent
				,messages.ImageFileName
				,messages.CreatorID
				,messages.StartDate
				,messages.EndDate
				,messages.CreateDate 
			, concat(persons6.pfname, ' ', persons6.plname) as persons6_FullName 
			, g2j(messages.StartDate) as StartDate_Shamsi 
			, g2j(messages.EndDate) as EndDate_Shamsi 
			, concat(g2j(CreateDate), ' ', substr(CreateDate, 12,10)) as CreateDate_Shamsi from projectmanagement.messages 
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=messages.CreatorID)  where  messages.MessageID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->MessageID=$rec["MessageID"];
			$this->MessageBody=$rec["MessageBody"];
			$this->RelatedFileName=$rec["RelatedFileName"];
			$this->FileContent=$rec["FileContent"];
			$this->ImageFileName=$rec["ImageFileName"];
			//$this->ImageFileContent=$rec["ImageFileContent"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons6_FullName"]; // محاسبه از روی جدول وابسته
			$this->StartDate=$rec["StartDate"];
			$this->StartDate_Shamsi=$rec["StartDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->EndDate=$rec["EndDate"];
			$this->EndDate_Shamsi=$rec["EndDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->CreateDate=$rec["CreateDate"];
			$this->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
		}
	}
}
/*
کلاس مدیریت پیامها
*/
class manage_messages
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = "select count(MessageID) as TotalCount from projectmanagement.messages";
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
		$query = "select max(MessageID) as MaxID from projectmanagement.messages";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $MessageBody: متن پیام
	* @param $FileContent: فایل ضمیمه
	* @param $RelatedFileName: نام فایل
	* @param $ImageFileContent: تصویر
	* @param $ImageFileName: نام فایل
	* @param $StartDate: زمان شروع نمایش
	* @param $EndDate: زمان پایان نمایش
	* @return کد داده اضافه شده	*/
	static function Add($MessageBody, $FileContent, $RelatedFileName, $ImageFileContent, $ImageFileName, $StartDate, $EndDate)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.messages (";
		$query .= " MessageBody";
		$query .= ", FileContent";
		$query .= ", RelatedFileName";
		$query .= ", ImageFileContent";
		$query .= ", ImageFileName";
		$query .= ", CreatorID";
		$query .= ", StartDate";
		$query .= ", EndDate";
		$query .= ", CreateDate";
		$query .= ") values (";
		$query .= "? , '".$FileContent."', ? , '".$ImageFileContent."', ? , ? , ? , ? , now() ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $MessageBody); 
		array_push($ValueListArray, $RelatedFileName); 
		array_push($ValueListArray, $ImageFileName); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		array_push($ValueListArray, $StartDate); 
		array_push($ValueListArray, $EndDate); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_messages::GetLastID();
		$mysql->audit("ثبت داده جدید در پیامها با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $MessageBody: متن پیام
	* @param $FileContent: فایل ضمیمه
	* @param $RelatedFileName: نام فایل
	* @param $ImageFileContent: تصویر
	* @param $ImageFileName: نام فایل
	* @param $StartDate: زمان شروع نمایش
	* @param $EndDate: زمان پایان نمایش
	* @return 	*/
	static function Update($UpdateRecordID, $MessageBody, $FileContent, $RelatedFileName, $ImageFileContent, $ImageFileName, $StartDate, $EndDate)
	{
		$k=0;
		$LogDesc = manage_messages::ComparePassedDataWithDB($UpdateRecordID, $MessageBody, $FileContent, $RelatedFileName, $ImageFileContent, $ImageFileName, $StartDate, $EndDate);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.messages set ";
			$query .= " MessageBody=? ";
		if($RelatedFileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", RelatedFileName=?, FileContent='".$FileContent."' ";
		}
		if($ImageFileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", ImageFileName=?, ImageFileContent='".$ImageFileContent."' ";
		}
			$query .= ", StartDate=? ";
			$query .= ", EndDate=? ";
		$query .= " where MessageID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $MessageBody); 
		if($RelatedFileName!="")
		{ 
			array_push($ValueListArray, $RelatedFileName); 
		} 
		if($ImageFileName!="")
		{ 
			array_push($ValueListArray, $ImageFileName); 
		} 
		array_push($ValueListArray, $StartDate); 
		array_push($ValueListArray, $EndDate); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پیامها - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.messages where MessageID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پیامها");
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
		$query = "select messages.MessageID
				,messages.MessageBody
				,messages.RelatedFileName
				,messages.ImageFileName
				,messages.CreatorID
				,messages.StartDate
				,messages.EndDate
				,messages.CreateDate
			, concat(persons6.pfname, ' ', persons6.plname) as persons6_FullName 
			, g2j(StartDate) as StartDate_Shamsi 
			, g2j(EndDate) as EndDate_Shamsi 
			, concat(g2j(CreateDate), ' ', substr(CreateDate, 12, 10)) as CreateDate_Shamsi  from projectmanagement.messages 
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=messages.CreatorID)  ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_messages();
			$ret[$k]->MessageID=$rec["MessageID"];
			$ret[$k]->MessageBody=$rec["MessageBody"];
			$ret[$k]->RelatedFileName=$rec["RelatedFileName"];
			$ret[$k]->ImageFileName=$rec["ImageFileName"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons6_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->StartDate=$rec["StartDate"];
			$ret[$k]->StartDate_Shamsi=$rec["StartDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EndDate=$rec["EndDate"];
			$ret[$k]->EndDate_Shamsi=$rec["EndDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$k++;
		}
		return $ret;
	}
	
	static function GetActiveMessages()
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select messages.MessageID
				,messages.MessageBody
				,messages.RelatedFileName
				,messages.ImageFileName
				,messages.CreatorID
				,messages.StartDate
				,messages.EndDate
				,messages.CreateDate
			, concat(persons6.pfname, ' ', persons6.plname) as persons6_FullName 
			, g2j(StartDate) as StartDate_Shamsi 
			, g2j(EndDate) as EndDate_Shamsi 
			, concat(g2j(CreateDate), ' ', substr(CreateDate, 12, 10)) as CreateDate_Shamsi  from projectmanagement.messages 
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=messages.CreatorID)  
			where StartDate<=now() and EndDate>=now()
			";
		$query .= " order by CreateDate DESC ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_messages();
			$ret[$k]->MessageID=$rec["MessageID"];
			$ret[$k]->MessageBody=$rec["MessageBody"];
			$ret[$k]->RelatedFileName=$rec["RelatedFileName"];
			$ret[$k]->ImageFileName=$rec["ImageFileName"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons6_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->StartDate=$rec["StartDate"];
			$ret[$k]->StartDate_Shamsi=$rec["StartDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EndDate=$rec["EndDate"];
			$ret[$k]->EndDate_Shamsi=$rec["EndDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$k++;
		}
		return $ret;
	}
	
	/**
	* @param $MessageBody: متن پیام
	* @param $CreateDate: زمان ایجاد
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($MessageBody, $CreateDate, $OtherConditions, $FromRec, $NumberOfRec , $OrderByFieldName="", $OrderType="")
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select messages.MessageID
				,messages.MessageBody
				,messages.RelatedFileName
				,messages.ImageFileName
				,messages.CreatorID
				,messages.StartDate
				,messages.EndDate
				,messages.CreateDate
			, concat(persons6.pfname, ' ', persons6.plname) as persons6_FullName 
			, g2j(StartDate) as StartDate_Shamsi 
			, g2j(EndDate) as EndDate_Shamsi 
			, concat(g2j(CreateDate), ' ', substr(CreateDate, 12, 10)) as CreateDate_Shamsi  from projectmanagement.messages 
			LEFT JOIN projectmanagement.persons persons6 on (persons6.PersonID=messages.CreatorID)  ";
		$cond = "";
		if($MessageBody!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "messages.MessageBody like ? ";
		}
		if($CreateDate!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "messages.CreateDate like ? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$query .= " limit ".$FromRec.", ".$NumberOfRec;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($MessageBody!="") 
			array_push($ValueListArray, "%".$MessageBody."%"); 
		if($CreateDate!="") 
			array_push($ValueListArray, "%".$CreateDate."%"); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_messages();
			$ret[$k]->MessageID=$rec["MessageID"];
			$ret[$k]->MessageBody=$rec["MessageBody"];
			$ret[$k]->RelatedFileName=$rec["RelatedFileName"];
			$ret[$k]->ImageFileName=$rec["ImageFileName"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons6_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->StartDate=$rec["StartDate"];
			$ret[$k]->StartDate_Shamsi=$rec["StartDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->EndDate=$rec["EndDate"];
			$ret[$k]->EndDate_Shamsi=$rec["EndDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$k++;
		}
		return $ret;
	}
	/**
	* @param $MessageBody: متن پیام
	* @param $CreateDate: زمان ایجاد
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return تعداد داده های حاصل جستجو
	*/
	static function SearchResultCount($MessageBody, $CreateDate, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(*) as TotalCount from projectmanagement.messages	";
 		$cond = "";
		if($MessageBody!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "messages.MessageBody like ? ";
		}
		if($CreateDate!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "messages.CreateDate like ? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($MessageBody!="") 
			array_push($ValueListArray, "%".$MessageBody."%"); 
		if($CreateDate!="") 
			array_push($ValueListArray, "%".$CreateDate."%"); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec = $res->fetch()) return $rec["TotalCount"];  else return 0;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $MessageBody: متن پیام
	* @param $FileContent: فایل ضمیمه
	* @param $RelatedFileName: نام فایل
	* @param $ImageFileContent: تصویر
	* @param $ImageFileName: نام فایل
	* @param $StartDate: زمان شروع نمایش
	* @param $EndDate: زمان پایان نمایش
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $MessageBody, $FileContent, $RelatedFileName, $ImageFileContent, $ImageFileName, $StartDate, $EndDate)
	{
		$ret = "";
		$obj = new be_messages();
		$obj->LoadDataFromDatabase($CurRecID);
		if($MessageBody!=$obj->MessageBody)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "متن پیام";
		}
		if($FileContent!=$obj->FileContent)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "فایل ضمیمه";
		}
		if($ImageFileContent!=$obj->ImageFileContent)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "تصویر";
		}
		if($StartDate." 00:00:00"!=$obj->StartDate)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "زمان شروع نمایش";
		}
		if($EndDate." 00:00:00"!=$obj->EndDate)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "زمان پایان نمایش";
		}
		return $ret;
	}
}
?>