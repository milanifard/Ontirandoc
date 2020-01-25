<?php
/*
 تعریف کلاسها و متدهای مربوط به : اصطلاحات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-6
*/

/*
کلاس پایه: اصطلاحات
*/
class be_terms
{
	public $TermID;		//
	public $TermTitle;		//عنوان
	public $comment;		//یادداشت
	public $CreatorUserID;		//ایجاد کننده
	public $CreateDate;		//تاریخ ایجاد
	public $CreateDate_Shamsi;		/* مقدار شمسی معادل با زمان ایجاد */
	public $ReferCount; // تعداد ارجاعات به واژه
	function be_terms() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select terms.* 
			, concat((CreateDate), ' ', substr(CreateDate, 12,10)) as CreateDate_Shamsi from projectmanagement.terms  where  terms.TermID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->TermID=$rec["TermID"];
			$this->TermTitle=$rec["TermTitle"];
			$this->comment=$rec["comment"];
			$this->CreatorUserID=$rec["CreatorUserID"];
			$this->CreateDate=$rec["CreateDate"];
			$this->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
		}
	}
}
/*
کلاس مدیریت اصطلاحات
*/
class manage_terms
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(TermID) as TotalCount from projectmanagement.terms";
		if($WhereCondition!="")
		{
			$query .= " where ".$WhereCondition;
		}
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
		$query = "select max(TermID) as MaxID from projectmanagement.terms";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $TermTitle: عنوان
	* @param $comment: یادداشت
	* @return کد داده اضافه شده	*/
	static function Add($TermTitle, $comment)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.terms (";
		$query .= " TermTitle";
		$query .= ", comment";
		$query .= ", CreatorUserID";
		$query .= ", CreateDate";
		$query .= ") values (";
		$query .= "? , ? , ? , now() ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $TermTitle);
		array_push($ValueListArray, $comment);
		array_push($ValueListArray, $_SESSION["UserID"]);
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_terms::GetLastID();
		//$mysql->audit("ثبت داده جدید در اصطلاحات با کد ".$LastID);
		$query = "insert into projectmanagement.TermsManipulationHistory (PreTermID, PreTermTitle, NewTermID, NewTermTitle, ActionType, PersonID, ATS) 
			  values (?, ?, ?, ?, 'INSERT', '".$_SESSION["PersonID"]."', now()) ";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($LastID, $TermTitle, $LastID, $TermTitle));

		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $TermTitle: عنوان
	* @param $comment: یادداشت
	* @return 	*/
	static function Update($UpdateRecordID, $TermTitle, $comment)
	{
		$k=0;
		//$LogDesc = manage_terms::ComparePassedDataWithDB($UpdateRecordID, $TermTitle, $comment);

		$mysql = pdodb::getInstance();
		$query = "select TermTitle from projectmanagement.terms where TermID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UpdateRecordID));
		$rec = $res->fetch();
		$PreTermTitle = $rec["TermTitle"];


		$query = "update projectmanagement.terms set ";
		$query .= " TermTitle=?, comment=? ";
		$query .= " where TermID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $TermTitle);
		array_push($ValueListArray, $comment);
		array_push($ValueListArray, $UpdateRecordID);
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در اصطلاحات - موارد تغییر داده شده: ".$LogDesc);

		$query = "insert into projectmanagement.TermsManipulationHistory (PreTermID, PreTermTitle, NewTermID, NewTermTitle, ActionType, PersonID, ATS) 
			  values (?, ?, ?, ?, 'UPDATE', '".$_SESSION["PersonID"]."', now()) ";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($UpdateRecordID, $PreTermTitle, $UpdateRecordID, $TermTitle));

	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "select TermTitle from projectmanagement.terms where TermID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($RemoveRecordID));
		$rec = $res->fetch();
		$TermTitle = $rec["TermTitle"];

		$query = "insert into projectmanagement.TermsManipulationHistory (PreTermID, PreTermTitle, ActionType, PersonID, ATS)
			  values (?, ?, 'DELETE', '".$_SESSION["PersonID"]."', now()) ";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID, $TermTitle ));

		$query = "delete from projectmanagement.terms where TermID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.TermOntologyElementMapping where TermID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از اصطلاحات");

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
		$query = "select terms.TermID
				,terms.TermTitle
				,terms.comment
				,terms.CreatorUserID
				,terms.CreateDate
			, concat(g2j(CreateDate), ' ', substr(CreateDate, 12, 10)) as CreateDate_Shamsi  from projectmanagement.terms  ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_terms();
			$ret[$k]->TermID=$rec["TermID"];
			$ret[$k]->TermTitle=$rec["TermTitle"];
			$ret[$k]->comment=$rec["comment"];
			$ret[$k]->CreatorUserID=$rec["CreatorUserID"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$k++;
		}
		return $ret;
	}
	/**
	* @param $TermTitle: عنوان
	* @param $comment: یادداشت
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($TermTitle, $comment, $OtherConditions, $FromRec, $NumberOfRec , $OrderByFieldName="", $OrderType="")
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select distinct terms.TermID
				,terms.TermTitle
				,terms.comment
				,terms.CreatorUserID
				,terms.CreateDate
			, concat((terms.CreateDate), ' ', substr(terms.CreateDate, 12, 10)) as CreateDate_Shamsi  
			, (select count(*) from projectmanagement.TermReferenceMapping tm where tm.TermID=terms.TermID) as ReferCount
			from projectmanagement.terms 
			LEFT JOIN projectmanagement.TermReferenceMapping using (TermID) ";
		$cond = "";
		if($TermTitle!="")
		{
			if($cond!="") $cond .= " and ";
				$cond .= "terms.TermTitle like ? ";
		}
		if($comment!="")
		{
			if($cond!="") $cond .= " and ";
				$cond .= "terms.comment like ? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$query .= " limit ".$FromRec.", ".$NumberOfRec;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($TermTitle!="")
			array_push($ValueListArray, "%".$TermTitle."%");
		if($comment!="")
			array_push($ValueListArray, "%".$comment."%");
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_terms();
			$ret[$k]->TermID=$rec["TermID"];
			$ret[$k]->TermTitle=$rec["TermTitle"];
			$ret[$k]->comment=$rec["comment"];
			$ret[$k]->CreatorUserID=$rec["CreatorUserID"];
			$ret[$k]->CreateDate=$rec["CreateDate"];
			$ret[$k]->CreateDate_Shamsi=$rec["CreateDate_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->ReferCount=$rec["ReferCount"];

			$k++;
		}
		return $ret;
	}
	/**
	* @param $TermTitle: عنوان
	* @param $comment: یادداشت
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return تعداد داده های حاصل جستجو
	*/
	static function SearchResultCount($TermTitle, $comment, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(distinct TermID) as TotalCount from projectmanagement.terms	
		LEFT JOIN projectmanagement.TermReferenceMapping using (TermID)";
 		$cond = "";
		if($TermTitle!="")
		{
			if($cond!="") $cond .= " and ";
				$cond .= "terms.TermTitle like ? ";
		}
		if($comment!="")
		{
			if($cond!="") $cond .= " and ";
				$cond .= "terms.comment like ? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($TermTitle!="")
			array_push($ValueListArray, "%".$TermTitle."%");
		if($comment!="")
			array_push($ValueListArray, "%".$comment."%");
		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec = $res->fetch()) return $rec["TotalCount"];  else return 0;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $TermTitle: عنوان
	* @param $comment: یادداشت
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $TermTitle, $comment)
	{
		$ret = "";
		$obj = new be_terms();
		$obj->LoadDataFromDatabase($CurRecID);
		if($TermTitle!=$obj->TermTitle)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($comment!=$obj->comment)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "یادداشت";
		}
		return $ret;
	}

	//changed by Naghme Mohammadifar
	function ShowSummary($RecID)
	{
		$ret = "<br>";
		$ret .= "<table class='table table-bordered '>";
        $ret .= "<thead>";
		$ret .= "<tr class='table-info text-center'>";
		$ret .= "<th> ".C_SUMMARY_OF_INFORMATION ."</th></tr>";
        $ret .= "</thead> <tbody>";
        $ret .= "<tr class='text-center'> <th> ".C_CREATOR."</th>";
		$obj = new be_terms();
		$obj->LoadDataFromDatabase($RecID);
		// I didnt know what kind of information you wanted to show (cause noting showed in primary code ) *Naghme Mohammadifar
		$ret .="<tr class='text-center'> <td>".$obj->CreatorUserID."</td>";
		$ret .= "</tr>";
        $ret .= "</tbody>";
		$ret .= "</table>";
		return $ret;
	}
	function ShowTabs($RecID, $CurrentPageName)
	{
        $ret = "<br>";
		$ret = "<table class='table table-bordered'>";
 		$ret .= "<thead><tr class='table-info'>";
		$ret .= "<th> ";
		$ret .= "<a class= 'btn ' href='Newterms.php?UpdateID=".$RecID."'>".C_MAIN_PROPERTIES." </a></th>";
		$ret .= "<th> ";
		$ret .= "<a class = 'btn' href='ManageTermOntologyElementMapping.php?TermID=".$RecID."'>".C_MAPPING_OF_IDIOMS_AND_ELEMENTS_OF_HISTOGRAM."</a></th>";
		$ret .= "</thead></table>";
		return $ret;
	}
}
?>