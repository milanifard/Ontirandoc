<?php
/*
 تعریف کلاسها و متدهای مربوط به : بخش بندیهای فرمها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 90-5-9
*/

/*
کلاس پایه: بخش بندیهای فرمها
*/
class be_FormsSections
{
	public $FormsSectionID;		//
	public $FormsStructID;		//کد ساختار فرم
	public $SectionName;		//نام بخش
	public $ShowOrder;		//ترتیب نمایش

	public $HeaderDesc; // سرتیتر
	public $FooterDesc; // پانویس
	
	function be_FormsSections() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select FormsSections.* from formsgenerator.FormsSections  where  FormsSections.FormsSectionID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->FormsSectionID=$rec["FormsSectionID"];
			$this->FormsStructID=$rec["FormsStructID"];
			$this->SectionName=$rec["SectionName"];
			$this->ShowOrder=$rec["ShowOrder"];
			$this->HeaderDesc=$rec["HeaderDesc"];
			$this->FooterDesc=$rec["FooterDesc"];
		}
	}
}
/*
کلاس مدیریت بخش بندیهای فرمها
*/
class manage_FormsSections
{
	static function GetCount($FormsStructID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(FormsSectionID) as TotalCount from formsgenerator.FormsSections";
			$query .= " where FormsStructID='".$FormsStructID."'";
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
		$query = "select max(FormsSectionID) as MaxID from formsgenerator.FormsSections";

		$mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $FormsStructID: کد ساختار فرم
	* @param $SectionName: نام بخش
	* @param $ShowOrder: ترتیب نمایش
	* @return کد داده اضافه شده	*/
	static function Add($FormsStructID, $SectionName, $ShowOrder, $HeaderDesc, $FooterDesc)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into formsgenerator.FormsSections (";
		$query .= " FormsStructID";
		$query .= ", SectionName";
		$query .= ", ShowOrder";
		$query .= ", HeaderDesc";
		$query .= ", FooterDesc";
		$query .= ") values (";
		$query .= "? , ? , ? , ?, ?";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $FormsStructID); 
		array_push($ValueListArray, $SectionName); 
		array_push($ValueListArray, $ShowOrder); 
		array_push($ValueListArray, $HeaderDesc);
		array_push($ValueListArray, $FooterDesc);
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_FormsSections::GetLastID();
		$mysql->audit("ثبت داده جدید در بخش بندیهای فرمها با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $SectionName: نام بخش
	* @param $ShowOrder: ترتیب نمایش
	* @return 	*/
	static function Update($UpdateRecordID, $SectionName, $ShowOrder, $HeaderDesc, $FooterDesc)
	{
		$k=0;
		$LogDesc = manage_FormsSections::ComparePassedDataWithDB($UpdateRecordID, $SectionName, $ShowOrder);
		$mysql = pdodb::getInstance();
		$query = "update formsgenerator.FormsSections set ";
			$query .= " SectionName=? ";
			$query .= ", ShowOrder=? ";
			$query .= ", HeaderDesc=? ";
			$query .= ", FooterDesc=? ";
		$query .= " where FormsSectionID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $SectionName); 
		array_push($ValueListArray, $ShowOrder); 
		array_push($ValueListArray, $HeaderDesc);
		array_push($ValueListArray, $FooterDesc);
		array_push($ValueListArray, $UpdateRecordID);		
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در بخش بندیهای فرمها - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from formsgenerator.FormsSections where FormsSectionID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از بخش بندیهای فرمها");
	}
	static function GetList($FormsStructID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select FormsSections.FormsSectionID
				,FormsSections.FormsStructID
				,FormsSections.SectionName
				,FormsSections.ShowOrder
				,FormsSections.HeaderDesc
				,FormsSections.FooterDesc
				from formsgenerator.FormsSections  ";
		$query .= " where FormsStructID=? order by ShowOrder";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($FormsStructID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_FormsSections();
			$ret[$k]->FormsSectionID=$rec["FormsSectionID"];
			$ret[$k]->FormsStructID=$rec["FormsStructID"];
			$ret[$k]->SectionName=$rec["SectionName"];
			$ret[$k]->ShowOrder=$rec["ShowOrder"];
			$ret[$k]->HeaderDesc=$rec["HeaderDesc"];
			$ret[$k]->FooterDesc=$rec["FooterDesc"];
			$k++;
		}
		return $ret;
	}
	
	static function CreateSelectBox($SelectBoxName, $FormsStructID, $HasAllValue=TRUE)
	{
		$ret = "<select name='".$SelectBoxName."' id='".$SelectBoxName."'>";
		if($HasAllValue)
			$ret .= "<option value='0'>-";
		$list = manage_FormsSections::GetList($FormsStructID);
		for($i=0; $i<count($list); $i++)
		{
			$ret .= "<option value='".$list[$i]->FormsSectionID."'>".$list[$i]->SectionName;
		}
		return $ret;
	}
	
	
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $SectionName: نام بخش
	* @param $ShowOrder: ترتیب نمایش
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $SectionName, $ShowOrder)
	{
		$ret = "";
		$obj = new be_FormsSections();
		$obj->LoadDataFromDatabase($CurRecID);
		if($SectionName!=$obj->SectionName)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نام بخش";
		}
		if($ShowOrder!=$obj->ShowOrder)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "ترتیب نمایش";
		}
		return $ret;
	}
}
?>