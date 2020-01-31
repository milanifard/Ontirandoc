<?php
/*
 تعریف کلاسها و متدهای مربوط به : جعبه سیاه های محاسباتی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-4-24
*/

/*
کلاس پایه: جعبه سیاه های محاسباتی
*/
class be_UniversityCalculationBlackBoxs
{
	public $UniversityCalculationBlackBoxID;		//
	public $title;		//عنوان
	public $IsEducational;		//آموزشی؟
	public $IsEducational_Desc;		/* شرح مربوط به آموزشی؟ */
	public $IsResearch;		//پژوهشی؟
	public $IsResearch_Desc;		/* شرح مربوط به پژوهشی؟ */
	public $IsOfficial;		//اداری؟
	public $IsOfficial_Desc;		/* شرح مربوط به اداری؟ */
	public $IsFinancial;		//مالی؟
	public $IsFinancial_Desc;		/* شرح مربوط به مالی؟ */
	public $IsPersonal;		//شخصی؟
	public $IsPersonal_Desc;		/* شرح مربوط به شخصی؟ */
	public $IsOther;		//سایر؟
	public $IsOther_Desc;		/* شرح مربوط به سایر؟ */
	public $Output;		//نوع خروجی (مشخصه)
	public $Output_Desc;		/* شرح مربوط به نوع خروجی (مشخصه) */
	public $CalculationType;		//نوع محاسبه
	public $CalculationType_Desc;		/* شرح مربوط به نوع محاسبه */
	public $CalculationQuery;		//پرس و جوی محاسبه
	public $CodeFileName;		//نام فایل محتوی کد محاسبه

	function be_UniversityCalculationBlackBoxs() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select UniversityCalculationBlackBoxs.* 
			, CASE UniversityCalculationBlackBoxs.IsEducational 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsEducational_Desc 
			, CASE UniversityCalculationBlackBoxs.IsResearch 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsResearch_Desc 
			, CASE UniversityCalculationBlackBoxs.IsOfficial 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsOfficial_Desc 
			, CASE UniversityCalculationBlackBoxs.IsFinancial 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsFinancial_Desc 
			, CASE UniversityCalculationBlackBoxs.IsPersonal 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsPersonal_Desc 
			, CASE UniversityCalculationBlackBoxs.IsOther 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsOther_Desc 
			, f8.title  as f8_title 
			, CASE UniversityCalculationBlackBoxs.CalculationType 
				WHEN 'QUERY' THEN 'پرس و جو' 
				WHEN 'CODE' THEN 'کد' 
				END as CalculationType_Desc from formsgenerator.UniversityCalculationBlackBoxs 
			LEFT JOIN formsgenerator.UniversityEntities  f8 on (f8.UniversityEntityID=UniversityCalculationBlackBoxs.Output)  where  UniversityCalculationBlackBoxs.UniversityCalculationBlackBoxID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			foreach($res as $key => $value){
			$this->$key = $value;
			}
		}
	}
}
/*
کلاس مدیریت جعبه سیاه های محاسباتی
*/
class manage_UniversityCalculationBlackBoxs
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = "select count(UniversityCalculationBlackBoxID) as TotalCount from formsgenerator.UniversityCalculationBlackBoxs";
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
		$query = "select max(UniversityCalculationBlackBoxID) as MaxID from formsgenerator.UniversityCalculationBlackBoxs";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $title: عنوان
	* @param $IsEducational: آموزشی؟
	* @param $IsResearch: پژوهشی؟
	* @param $IsOfficial: اداری؟
	* @param $IsFinancial: مالی؟
	* @param $IsPersonal: شخصی؟
	* @param $IsOther: سایر؟
	* @param $Output: نوع خروجی (مشخصه)
	* @param $CalculationType: نوع محاسبه
	* @param $CalculationQuery: پرس و جوی محاسبه
	* @param $CodeFileName: نام فایل محتوی کد محاسبه
	* @return کد داده اضافه شده	*/
	static function Add($title, $IsEducational, $IsResearch, $IsOfficial, $IsFinancial, $IsPersonal, $IsOther, $Output, $CalculationType, $CalculationQuery, $CodeFileName)
	{
		$mysql = pdodb::getInstance();
		$query = "insert into formsgenerator.UniversityCalculationBlackBoxs (";
		$query .= " title";
		$query .= ", IsEducational";
		$query .= ", IsResearch";
		$query .= ", IsOfficial";
		$query .= ", IsFinancial";
		$query .= ", IsPersonal";
		$query .= ", IsOther";
		$query .= ", Output";
		$query .= ", CalculationType";
		$query .= ", CalculationQuery";
		$query .= ", CodeFileName";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = [$title, 
											$IsEducational, 
											$IsResearch, 
											$IsOfficial, 
											$IsFinancial, 
											$IsPersonal, 
											$IsOther, 
											$Output, 
											$CalculationType, 
											$CalculationQuery, 
											$CodeFileName
											];
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_UniversityCalculationBlackBoxs::GetLastID();
		$mysql->audit("ثبت داده جدید در جعبه سیاه های محاسباتی با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $title: عنوان
	* @param $IsEducational: آموزشی؟
	* @param $IsResearch: پژوهشی؟
	* @param $IsOfficial: اداری؟
	* @param $IsFinancial: مالی؟
	* @param $IsPersonal: شخصی؟
	* @param $IsOther: سایر؟
	* @param $Output: نوع خروجی (مشخصه)
	* @param $CalculationType: نوع محاسبه
	* @param $CalculationQuery: پرس و جوی محاسبه
	* @param $CodeFileName: نام فایل محتوی کد محاسبه
	* @return 	*/
	static function Update($UpdateRecordID, $title, $IsEducational, $IsResearch, $IsOfficial, $IsFinancial, $IsPersonal, $IsOther, $Output, $CalculationType, $CalculationQuery, $CodeFileName)
	{
		$k=0;
		$LogDesc = manage_UniversityCalculationBlackBoxs::ComparePassedDataWithDB($UpdateRecordID, $title, $IsEducational, $IsResearch, $IsOfficial, $IsFinancial, $IsPersonal, $IsOther, $Output, $CalculationType, $CalculationQuery, $CodeFileName);
		$mysql = pdodb::getInstance();
		$query = "update formsgenerator.UniversityCalculationBlackBoxs set ";
			$query .= " title=? ";
			$query .= ", IsEducational=? ";
			$query .= ", IsResearch=? ";
			$query .= ", IsOfficial=? ";
			$query .= ", IsFinancial=? ";
			$query .= ", IsPersonal=? ";
			$query .= ", IsOther=? ";
			$query .= ", Output=? ";
			$query .= ", CalculationType=? ";
			$query .= ", CalculationQuery=? ";
			$query .= ", CodeFileName=? ";
		$query .= " where UniversityCalculationBlackBoxID=?";
		$ValueListArray = [$title, 
											$IsEducational, 
											$IsResearch, 
											$IsOfficial, 
											$IsFinancial, 
											$IsPersonal, 
											$IsOther, 
											$Output, 
											$CalculationType, 
											$CalculationQuery, 
											$CodeFileName,
											$UpdateRecordID
											];
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در جعبه سیاه های محاسباتی - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from formsgenerator.UniversityCalculationBlackBoxs where UniversityCalculationBlackBoxID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement([$RemoveRecordID]);
		$query = "delete from formsgenerator.UniversityBlackBoxesParameters where UniversityCalculationBlackBoxID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement([$RemoveRecordID]);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از جعبه سیاه های محاسباتی");
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
		$query = "select UniversityCalculationBlackBoxs.UniversityCalculationBlackBoxID
				,UniversityCalculationBlackBoxs.title
				,UniversityCalculationBlackBoxs.IsEducational
				,UniversityCalculationBlackBoxs.IsResearch
				,UniversityCalculationBlackBoxs.IsOfficial
				,UniversityCalculationBlackBoxs.IsFinancial
				,UniversityCalculationBlackBoxs.IsPersonal
				,UniversityCalculationBlackBoxs.IsOther
				,UniversityCalculationBlackBoxs.Output
				,UniversityCalculationBlackBoxs.CalculationType
				,UniversityCalculationBlackBoxs.CalculationQuery
				,UniversityCalculationBlackBoxs.CodeFileName
			, CASE UniversityCalculationBlackBoxs.IsEducational 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsEducational_Desc 
			, CASE UniversityCalculationBlackBoxs.IsResearch 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsResearch_Desc 
			, CASE UniversityCalculationBlackBoxs.IsOfficial 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsOfficial_Desc 
			, CASE UniversityCalculationBlackBoxs.IsFinancial 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsFinancial_Desc 
			, CASE UniversityCalculationBlackBoxs.IsPersonal 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsPersonal_Desc 
			, CASE UniversityCalculationBlackBoxs.IsOther 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as IsOther_Desc 
			, f8.title  as f8_title 
			, CASE UniversityCalculationBlackBoxs.CalculationType 
				WHEN 'QUERY' THEN 'پرس و جو' 
				WHEN 'CODE' THEN 'کد' 
				END as CalculationType_Desc  from formsgenerator.UniversityCalculationBlackBoxs 
			LEFT JOIN formsgenerator.UniversityEntities  f8 on (f8.UniversityEntityID=UniversityCalculationBlackBoxs.Output)  ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		while($rec=$res->fetch())
		{
			$item = new be_UniversityCalculationBlackBoxs();
			foreach($rec as $key=>$value){
				$item->$key = $value;
			}
			$item->Output_Desc=$rec["f8_title"]; // محاسبه از روی جدول وابسته
			array_push($ret, $item);
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $title: عنوان
	* @param $IsEducational: آموزشی؟
	* @param $IsResearch: پژوهشی؟
	* @param $IsOfficial: اداری؟
	* @param $IsFinancial: مالی؟
	* @param $IsPersonal: شخصی؟
	* @param $IsOther: سایر؟
	* @param $Output: نوع خروجی (مشخصه)
	* @param $CalculationType: نوع محاسبه
	* @param $CalculationQuery: پرس و جوی محاسبه
	* @param $CodeFileName: نام فایل محتوی کد محاسبه
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $title, $IsEducational, $IsResearch, $IsOfficial, $IsFinancial, $IsPersonal, $IsOther, $Output, $CalculationType, $CalculationQuery, $CodeFileName)
	{
		$ret = "";
		$obj = new be_UniversityCalculationBlackBoxs();
		$obj->LoadDataFromDatabase($CurRecID);
		if($title!=$obj->title)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($IsEducational!=$obj->IsEducational)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "آموزشی؟";
		}
		if($IsResearch!=$obj->IsResearch)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "پژوهشی؟";
		}
		if($IsOfficial!=$obj->IsOfficial)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "اداری؟";
		}
		if($IsFinancial!=$obj->IsFinancial)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "مالی؟";
		}
		if($IsPersonal!=$obj->IsPersonal)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "شخصی؟";
		}
		if($IsOther!=$obj->IsOther)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "سایر؟";
		}
		if($Output!=$obj->Output)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "نوع خروجی (مشخصه)";
		}
		if($CalculationType!=$obj->CalculationType)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "نوع محاسبه";
		}
		if($CalculationQuery!=$obj->CalculationQuery)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "پرس و جوی محاسبه";
		}
		if($CodeFileName!=$obj->CodeFileName)
		{
			if(!empty($ret))
				$ret .= " - ";
			$ret .= "نام فایل محتوی کد محاسبه";
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
		$obj = new be_UniversityCalculationBlackBoxs();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>عنوان: </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->title, ENT_QUOTES, 'UTF-8');
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
		if($CurrentPageName=="NewUniversityCalculationBlackBoxs")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='NewUniversityCalculationBlackBoxs.php?UpdateID=".$RecID."'>مشخصات اصلی</a></td>";
		$ret .= "<td width=\"50%\" ";
		if($CurrentPageName=="ManageUniversityBlackBoxesParameters")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageUniversityBlackBoxesParameters.php?UniversityCalculationBlackBoxID=".$RecID."'>پارامترهای ورودی جعبه سیاه های محاسباتی</a></td>";
		$ret .= "</table>";
		return $ret;
	}
}
?>
