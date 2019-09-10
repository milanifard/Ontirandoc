<?php
/*
 تعریف کلاسها و متدهای مربوط به : ارتباط اصطلاحات و مراجع
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-7
*/

/*
کلاس پایه: ارتباط اصطلاحات و مراجع
*/
class be_TermReferenceMapping
{
	public $TermReferenceMappingID;		//
	public $TermReferenceID;		//
	public $TermID;		//
	public $TermID_Desc;		/* شرح مربوط به کد اصطلاح */
	public $PageNum;		//شماره صفحه
	public $CreatorUserID;		//ایجاد کننده
	public $CreateDate;		//تاریخ ایجاد
	public $CreateDate_Shamsi;		/* مقدار شمسی معادل با زمان ایجاد */
	public $MappingComment;		//یادداشت
	public $ParagraphNo;		//شماره پاراگراف
	public $SentenceNo;		//شماره جمله

	
	function be_TermReferenceMapping() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select TermReferenceMapping.* 
			, p2.TermTitle  as p2_TermTitle 
			, concat(g2j(TermReferenceMapping.CreateDate), ' ', substr(TermReferenceMapping.CreateDate, 12,10)) as CreateDate_Shamsi from projectmanagement.TermReferenceMapping 
			LEFT JOIN projectmanagement.terms  p2 on (p2.TermID=TermReferenceMapping.TermID)  where  TermReferenceMapping.TermReferenceMappingID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->TermReferenceMappingID=$rec["TermReferenceMappingID"];
			$this->TermReferenceID=$rec["TermReferenceID"];
			$this->TermID=$rec["TermID"];
			$this->TermID_Desc=$rec["p2_TermTitle"]; // محاسبه از روی جدول وابسته
			$this->PageNum=$rec["PageNum"];
			$this->CreatorUserID=$rec["CreatorUserID"];
			$this->CreateDate=$rec["CreateDate"];
			$this->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->MappingComment=$rec["MappingComment"];
			$this->ParagraphNo=$rec["ParagraphNo"];
			$this->SentenceNo=$rec["SentenceNo"];
		}
	}
}
/*
کلاس مدیریت ارتباط اصطلاحات و مراجع
*/
class manage_TermReferenceMapping
{
	static function GetCount($TermReferenceID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(TermReferenceMappingID) as TotalCount from projectmanagement.TermReferenceMapping";
			$query .= " where TermReferenceID='".$TermReferenceID."'";
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
		$query = "select max(TermReferenceMappingID) as MaxID from projectmanagement.TermReferenceMapping";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $TermReferenceID: کد مرجع
	* @param $TermID: کد اصطلاح
	* @param $PageNum: شماره صفحه
	* @param $MappingComment: یادداشت
	* @return کد داده اضافه شده	*/
	static function Add($TermReferenceID, $TermID, $PageNum, $MappingComment, $ParagraphNo, $SentenceNo)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		
		$query = "select TermTitle from projectmanagement.terms where TermID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($TermID));
		$rec = $res->fetch();
		$TermTitle = $rec["TermTitle"];

		$query = "select title from projectmanagement.TermReferences where TermReferenceID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($TermReferenceID));
		$rec = $res->fetch();
		$TermReferenceTitle = $rec["title"];
		
		$query = "insert into projectmanagement.TermsReferHistory (TermID, TermTitle, TermReferenceID, TermReferenceTitle, PersonID, ATS, ActionType, ParagraphNo, PageNum)
			  values (?, ?, ?, ?, '".$_SESSION["PersonID"]."', now(), 'INSERT', ?, ?)";
			  
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($TermID, $TermTitle, $TermReferenceID, $TermReferenceTitle, $ParagraphNo, $PageNum));
		
		$query = "insert into projectmanagement.TermReferenceMapping (";
		$query .= " TermReferenceID";
		$query .= ", TermID";
		$query .= ", PageNum";
		$query .= ", CreatorUserID";
		$query .= ", CreateDate";
		$query .= ", MappingComment, ParagraphNo, SentenceNo) values (? , ? , ? , ? , now() , ? , ? , ? )";
		$ValueListArray = array();
		array_push($ValueListArray, $TermReferenceID); 
		array_push($ValueListArray, $TermID); 
		array_push($ValueListArray, $PageNum); 
		array_push($ValueListArray, $_SESSION["UserID"]); 
		array_push($ValueListArray, $MappingComment);
		array_push($ValueListArray, $ParagraphNo);
		array_push($ValueListArray, $SentenceNo); 
		
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_TermReferenceMapping::GetLastID();
		//$mysql->audit("ثبت داده جدید در ارتباط اصطلاحات و مراجع با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $TermID: کد اصطلاح
	* @param $PageNum: شماره صفحه
	* @param $MappingComment: یادداشت
	* @return 	*/
	static function Update($UpdateRecordID, $TermID, $PageNum, $MappingComment, $ParagraphNo, $SentenceNo)
	{
		$k=0;
		//$LogDesc = manage_TermReferenceMapping::ComparePassedDataWithDB($UpdateRecordID, $TermID, $PageNum, $MappingComment);
		$mysql = pdodb::getInstance();		
		
		$query = "select TermTitle from projectmanagement.terms where TermID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($TermID));
		$rec = $res->fetch();
		$TermTitle = $rec["TermTitle"];
		
		$query = "select TermID, TermTitle, TermReferenceID, title from projectmanagement.TermReferenceMapping 
			    JOIN projectmanagement.terms using (TermID) 
			    JOIN projectmanagement.TermReferences using (TermReferenceID)
			    where TermReferenceMappingID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UpdateRecordID));
		$rec = $res->fetch();
		
		$query = "insert into projectmanagement.TermsReferHistory (TermID, TermTitle, TermReferenceID, TermReferenceTitle, ReplacedTermID, ReplacedTermTitle, PersonID, ATS, ActionType, ParagraphNo, PageNum)
			  values (?, ?, ?, ?, '".$TermID."', '".$TermTitle."', '".$_SESSION["PersonID"]."', now(), 'REPLACE', ?, ?)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($rec["TermID"], $rec["TermTitle"], $rec["TermReferenceID"], $rec["title"], $ParagraphNo, $PageNum));
		
		$query = "update projectmanagement.TermReferenceMapping set ";
			$query .= " TermID=? ";
			$query .= ", PageNum=? ";
			$query .= ", MappingComment=? ";
			$query .= ", ParagraphNo=? ";
			$query .= ", SentenceNo=? ";
			$query .= " where TermReferenceMappingID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $TermID); 
		array_push($ValueListArray, $PageNum); 
		array_push($ValueListArray, $MappingComment); 
		array_push($ValueListArray, $ParagraphNo); 
		array_push($ValueListArray, $SentenceNo); 
		
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در ارتباط اصطلاحات و مراجع - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		
		$query = "select TermID, TermTitle, TermReferenceID, title, PageNum, paragraphNo from projectmanagement.TermReferenceMapping 
			    JOIN projectmanagement.terms using (TermID) 
			    JOIN projectmanagement.TermReferences using (TermReferenceID)
			    where TermReferenceMappingID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($RemoveRecordID));
		$rec = $res->fetch();
		
		$query = "insert into projectmanagement.TermsReferHistory (TermID, TermTitle, TermReferenceID, TermReferenceTitle, PersonID, ATS, ActionType, ParagraphNo, PageNum)
			  values (?, ?, ?, ?, '".$_SESSION["PersonID"]."', now(), 'REMOVE', ?, ?)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($rec["TermID"], $rec["TermTitle"], $rec["TermReferenceID"], $rec["title"], $rec["ParagraphNo"], $rec["PageNum"]));
		
		$query = "delete from projectmanagement.TermReferenceMapping where TermReferenceMappingID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از ارتباط اصطلاحات و مراجع");
	}
	
	static function GetList($TermReferenceID, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType)
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
		$query = "select TermReferenceMapping.TermReferenceMappingID
				,TermReferenceMapping.TermReferenceID
				,TermReferenceMapping.TermID
				,TermReferenceMapping.PageNum
				,TermReferenceMapping.CreatorUserID
				,TermReferenceMapping.CreateDate
				,TermReferenceMapping.MappingComment
				,TermReferenceMapping.ParagraphNo
				,TermReferenceMapping.SentenceNo
				
			, p2.TermTitle  as p2_TermTitle 
			, concat(g2j(TermReferenceMapping.CreateDate), ' ', substr(TermReferenceMapping.CreateDate, 12, 10)) as CreateDate_Shamsi  from projectmanagement.TermReferenceMapping 
			LEFT JOIN projectmanagement.terms  p2 on (p2.TermID=TermReferenceMapping.TermID)  ";
		$query .= " where TermReferenceID=? ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($TermReferenceID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_TermReferenceMapping();
			$ret[$k]->TermReferenceMappingID=$rec["TermReferenceMappingID"];
			$ret[$k]->TermReferenceID=$rec["TermReferenceID"];
			$ret[$k]->TermID=$rec["TermID"];
			$ret[$k]->TermID_Desc=$rec["p2_TermTitle"]; // محاسبه از روی جدول وابسته
			$ret[$k]->PageNum=$rec["PageNum"];
			$ret[$k]->CreatorUserID=$rec["CreatorUserID"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->MappingComment=$rec["MappingComment"];
			$ret[$k]->ParagraphNo=$rec["ParagraphNo"];
			$ret[$k]->SentenceNo=$rec["SentenceNo"];
			
			$k++;
		}
		return $ret;
	}
	/**
	* @param $TermReferenceID کد آیتم پدر
	* @param $TermID: کد اصطلاح
	* @param $PageNum: شماره صفحه
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($TermReferenceID, $TermID, $PageNum, $OtherConditions, $FromRec, $NumberOfRec , $OrderByFieldName="", $OrderType="")
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select TermReferenceMapping.TermReferenceMappingID
				,TermReferenceMapping.TermReferenceID
				,TermReferenceMapping.TermID
				,TermReferenceMapping.PageNum
				,TermReferenceMapping.CreatorUserID
				,TermReferenceMapping.CreateDate
				,TermReferenceMapping.MappingComment
				,TermReferenceMapping.ParagraphNo
				,TermReferenceMapping.SentenceNo
			, p2.TermTitle  as p2_TermTitle 
			, concat(g2j(TermReferenceMapping.CreateDate), ' ', substr(TermReferenceMapping.CreateDate, 12, 10)) as CreateDate_Shamsi  from projectmanagement.TermReferenceMapping 
			LEFT JOIN projectmanagement.terms  p2 on (p2.TermID=TermReferenceMapping.TermID)  ";
		$cond = "TermReferenceID=? ";
		if($TermID!="0" && $TermID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "TermReferenceMapping.TermID=? ";
		}
		if($PageNum!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "TermReferenceMapping.PageNum=? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$query .= " limit ".$FromRec.", ".$NumberOfRec;
		//echo $query;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $TermReferenceID); 
		if($TermID!="0" && $TermID!="") 
			array_push($ValueListArray, $TermID); 
		if($PageNum!="") 
			array_push($ValueListArray, $PageNum); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_TermReferenceMapping();
			$ret[$k]->TermReferenceMappingID=$rec["TermReferenceMappingID"];
			$ret[$k]->TermReferenceID=$rec["TermReferenceID"];
			$ret[$k]->TermID=$rec["TermID"];
			$ret[$k]->TermID_Desc=$rec["p2_TermTitle"]; // محاسبه از روی جدول وابسته
			$ret[$k]->PageNum=$rec["PageNum"];
			$ret[$k]->CreatorUserID=$rec["CreatorUserID"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->MappingComment=$rec["MappingComment"];
			$ret[$k]->ParagraphNo=$rec["ParagraphNo"];
			$ret[$k]->SentenceNo=$rec["SentenceNo"];
			
			$k++;
		}
		return $ret;
	}
	/**
	* @param $TermReferenceID کد آیتم پدر
	* @param $TermID: کد اصطلاح
	* @param $PageNum: شماره صفحه
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return تعداد داده های حاصل جستجو
	*/
	static function SearchResultCount($TermReferenceID, $TermID, $PageNum, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(*) as TotalCount from projectmanagement.TermReferenceMapping	";
 		$cond = "TermReferenceID=? ";
		if($TermID!="0" && $TermID!="") 
		{
			if($cond!="") $cond .= " and ";
			$cond .= "TermReferenceMapping.TermID=? ";
		}
		if($PageNum!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "TermReferenceMapping.PageNum=? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$mysql->Prepare($query);
		$ValueListArray = array();
		array_push($ValueListArray, $TermReferenceID); 
		if($TermID!="0" && $TermID!="") 
			array_push($ValueListArray, $TermID); 
		if($PageNum!="") 
			array_push($ValueListArray, $PageNum); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec = $res->fetch()) return $rec["TotalCount"];  else return 0;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $TermID: کد اصطلاح
	* @param $PageNum: شماره صفحه
	* @param $MappingComment: یادداشت
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $TermID, $PageNum, $MappingComment)
	{
		$ret = "";
		$obj = new be_TermReferenceMapping();
		$obj->LoadDataFromDatabase($CurRecID);
		if($TermID!=$obj->TermID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "کد اصطلاح";
		}
		if($PageNum!=$obj->PageNum)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "شماره صفحه";
		}
		if($MappingComment!=$obj->MappingComment)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "یادداشت";
		}
		return $ret;
	}
}
?>