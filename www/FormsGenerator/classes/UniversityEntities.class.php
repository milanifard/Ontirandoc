<?php
/*
 تعریف کلاسها و متدهای مربوط به : مشخصه ها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-4-23
*/

/*
کلاس پایه: مشخصه ها
*/
class be_UniversityEntities
{
	public $UniversityEntityID;		//
	public $title;		//عنوان
	public $EntityType;		//نوع داده
	public $EntityType_Desc;		/* شرح مربوط به نوع داده */
	public $DataMask;		//عبار منظمی که داده های این مشخصه باید از آن پیروی کند
	public $MinValue;		//حداقل مقدار مجاز
	public $MaxValue;		//حداکثر مقدار مجاز
	public $DomainTableName;		//جدولی که داده های این مشخصه فقط می توانند از آن باشند
	public $OwnerType;		//مالک
	public $OwnerType_Desc;		/* شرح مربوط به مالک */
	public $DataCategory;		//طبقه داده
	public $DataCategory_Desc;		/* شرح مربوط به طبقه داده */

	function be_UniversityEntities() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select UniversityEntities.* 
			, CASE UniversityEntities.EntityType 
				WHEN 'INT' THEN 'INT' 
				WHEN 'VARCHAR' THEN 'VARCHAR' 
				WHEN 'BOOLEAN' THEN 'BOOLEAN' 
				WHEN 'DATE' THEN 'DATE' 
				END as EntityType_Desc 
			, CASE UniversityEntities.OwnerType 
				WHEN 'STUDENT' THEN 'دانشجو' 
				WHEN 'PROF' THEN 'استاد' 
				WHEN 'STAFF' THEN 'کارمند' 
				WHEN 'PERSONEL' THEN 'پرسنل' 
				WHEN 'NONE' THEN 'هیچکدام' 
				WHEN 'OTHER' THEN 'سایر' 
				END as OwnerType_Desc 
			, CASE UniversityEntities.DataCategory 
				WHEN 'EDUCATIONAL' THEN 'آموزشی' 
				WHEN 'RESEARCH' THEN 'پژوهشی' 
				WHEN 'OFFICIAL' THEN 'اداری' 
				WHEN 'FINANCIAL' THEN 'مالی' 
				WHEN 'PERSONAL' THEN 'شخصی' 
				WHEN 'OTHER' THEN 'سایر' 
				END as DataCategory_Desc from formsgenerator.UniversityEntities  where  UniversityEntities.UniversityEntityID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			foreach($rec as $key => $value){
				$this->$key = $value;
			}
		}
	}
}
/*
کلاس مدیریت مشخصه ها
*/
class manage_UniversityEntities
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = "select count(UniversityEntityID) as TotalCount from formsgenerator.UniversityEntities";
		if(!empty($WhereCondition))
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
		$query = "select max(UniversityEntityID) as MaxID from formsgenerator.UniversityEntities";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $title: عنوان
	* @param $EntityType: نوع داده
	* @param $DataMask: عبار منظم (کنترلی)
	* @param $MinValue: حداقل مقدار مجاز
	* @param $MaxValue: حداکثر مقدار مجاز
	* @param $DomainTableName: جدولی که داده های این مشخصه فقط می توانند از آن باشند
	* @param $OwnerType: مالک
	* @param $DataCategory: طبقه داده
	* @return کد داده اضافه شده	*/
	static function Add($title, $EntityType, $DataMask, $MinValue, $MaxValue, $DomainTableName, $OwnerType, $DataCategory)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into formsgenerator.UniversityEntities (";
		$query .= " title";
		$query .= ", EntityType";
		$query .= ", DataMask";
		$query .= ", MinValue";
		$query .= ", MaxValue";
		$query .= ", DomainTableName";
		$query .= ", OwnerType";
		$query .= ", DataCategory";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = [$title, 
											$EntityType, 
											$DataMask, 
											$MinValue, 
											$MaxValue, 
											$DomainTableName, 
											$OwnerType, 
											$DataCategory]; 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_UniversityEntities::GetLastID();
		$mysql->audit("ثبت داده جدید در مشخصه ها با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $title: عنوان
	* @param $EntityType: نوع داده
	* @param $DataMask: عبار منظم (کنترلی)
	* @param $MinValue: حداقل مقدار مجاز
	* @param $MaxValue: حداکثر مقدار مجاز
	* @param $DomainTableName: جدولی که داده های این مشخصه فقط می توانند از آن باشند
	* @param $OwnerType: مالک
	* @param $DataCategory: طبقه داده
	* @return 	*/
	static function Update($UpdateRecordID, $title, $EntityType, $DataMask, $MinValue, $MaxValue, $DomainTableName, $OwnerType, $DataCategory)
	{
		$k=0;
		$LogDesc = manage_UniversityEntities::ComparePassedDataWithDB($UpdateRecordID, $title, $EntityType, $DataMask, $MinValue, $MaxValue, $DomainTableName, $OwnerType, $DataCategory);
		$mysql = pdodb::getInstance();
		$query = "update formsgenerator.UniversityEntities set ";
			$query .= " title=? ";
			$query .= ", EntityType=? ";
			$query .= ", DataMask=? ";
			$query .= ", MinValue=? ";
			$query .= ", MaxValue=? ";
			$query .= ", DomainTableName=? ";
			$query .= ", OwnerType=? ";
			$query .= ", DataCategory=? ";
		$query .= " where UniversityEntityID=?";
		$ValueListArray = [$title, 
											$EntityType, 
											$DataMask, 
											$MinValue, 
											$MaxValue, 
											$DomainTableName, 
											$OwnerType, 
											$DataCategory, 
											$UpdateRecordID]; 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در مشخصه ها - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from formsgenerator.UniversityEntities where UniversityEntityID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از مشخصه ها");
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
		$ret = array();
		$query = "select UniversityEntities.UniversityEntityID
				,UniversityEntities.title
				,UniversityEntities.EntityType
				,UniversityEntities.DataMask
				,UniversityEntities.MinValue
				,UniversityEntities.MaxValue
				,UniversityEntities.DomainTableName
				,UniversityEntities.OwnerType
				,UniversityEntities.DataCategory
			, CASE UniversityEntities.EntityType 
				WHEN 'INT' THEN 'INT' 
				WHEN 'VARCHAR' THEN 'VARCHAR' 
				WHEN 'BOOLEAN' THEN 'BOOLEAN' 
				WHEN 'DATE' THEN 'DATE' 
				END as EntityType_Desc 
			, CASE UniversityEntities.OwnerType 
				WHEN 'STUDENT' THEN 'دانشجو' 
				WHEN 'PROF' THEN 'استاد' 
				WHEN 'STAFF' THEN 'کارمند' 
				WHEN 'PERSONEL' THEN 'پرسنل' 
				WHEN 'NONE' THEN 'هیچکدام' 
				WHEN 'OTHER' THEN 'سایر' 
				END as OwnerType_Desc 
			, CASE UniversityEntities.DataCategory 
				WHEN 'EDUCATIONAL' THEN 'آموزشی' 
				WHEN 'RESEARCH' THEN 'پژوهشی' 
				WHEN 'OFFICIAL' THEN 'اداری' 
				WHEN 'FINANCIAL' THEN 'مالی' 
				WHEN 'PERSONAL' THEN 'شخصی' 
				WHEN 'OTHER' THEN 'سایر' 
				END as DataCategory_Desc  from formsgenerator.UniversityEntities  ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement([]);
		while($rec=$res->fetch())
		{
			$item = new be_UniversityEntities();
			foreach($rec as $key=>$value){
				$item->$key = $value;
			}
			array_push($ret, $item);
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $title: عنوان
	* @param $EntityType: نوع داده
	* @param $DataMask: عبار منظم (کنترلی)
	* @param $MinValue: حداقل مقدار مجاز
	* @param $MaxValue: حداکثر مقدار مجاز
	* @param $DomainTableName: جدولی که داده های این مشخصه فقط می توانند از آن باشند
	* @param $OwnerType: مالک
	* @param $DataCategory: طبقه داده
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $title, $EntityType, $DataMask, $MinValue, $MaxValue, $DomainTableName, $OwnerType, $DataCategory)
	{
		$ret = "";
		$obj = new be_UniversityEntities();
		$obj->LoadDataFromDatabase($CurRecID);
		if($title!=$obj->title)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($EntityType!=$obj->EntityType)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "نوع داده";
		}
		if($DataMask!=$obj->DataMask)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "عبار منظم (کنترلی)";
		}
		if($MinValue!=$obj->MinValue)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "حداقل مقدار مجاز";
		}
		if($MaxValue!=$obj->MaxValue)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "حداکثر مقدار مجاز";
		}
		if($DomainTableName!=$obj->DomainTableName)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "جدولی که داده های این مشخصه فقط می توانند از آن باشند";
		}
		if($OwnerType!=$obj->OwnerType)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "مالک";
		}
		if($DataCategory!=$obj->DataCategory)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "طبقه داده";
		}
		return $ret;
	}
}
?>
