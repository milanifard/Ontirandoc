<?php
/*
 تعریف کلاسها و متدهای مربوط به : الگوهای جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-26
*/

/*
کلاس پایه: الگوهای جلسه
*/
class be_SessionTypes
{
	public $SessionTypeID;		//
	public $SessionTypeTitle;		//عنوان
	public $SessionTypeLocation;		//محل تشکیل
	public $SessionTypeStartTime;		//زمان شروع
	public $SessionTypeDurationTime;		//مدت جلسه

	function be_SessionTypes() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SessionTypes.* from sessionmanagement.SessionTypes  where  SessionTypes.SessionTypeID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->SessionTypeID=$rec["SessionTypeID"];
			$this->SessionTypeTitle=$rec["SessionTypeTitle"];
			$this->SessionTypeLocation=$rec["SessionTypeLocation"];
			$this->SessionTypeStartTime=$rec["SessionTypeStartTime"];
			$this->SessionTypeDurationTime=$rec["SessionTypeDurationTime"];
		}
	}
	
	// آخرین شماره جلسه مربوط به این نوع را بر می گرداند
	function GetLastSessionNumber()
	{
		$query = "select max(SessionNumber) as MaxNo from sessionmanagement.UniversitySessions where SessionTypeID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($this->SessionTypeID));
		if($rec = $res->fetch())
			return $rec["MaxNo"];
		return 0;
	}
}
/*
کلاس مدیریت الگوهای جلسه
*/
class manage_SessionTypes
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(SessionTypeID) as TotalCount from sessionmanagement.SessionTypes";
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
		$query = "select max(SessionTypeID) as MaxID from sessionmanagement.SessionTypes";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $SessionTypeTitle: عنوان
	* @param $SessionTypeLocation: محل تشکیل
	* @param $SessionTypeStartTime: زمان شروع
	* @param $SessionTypeDurationTime: مدت زمان
	* @return کد داده اضافه شده	*/
	static function Add($SessionTypeTitle, $SessionTypeLocation, $SessionTypeStartTime, $SessionTypeDurationTime)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.SessionTypes (";
			if($k>0) 
				$query .= ", ";
			$query .= "SessionTypeTitle";
			$k++; 
			if($k>0) 
				$query .= ", ";
			$query .= "SessionTypeLocation";
			$k++; 
			if($k>0) 
				$query .= ", ";
			$query .= "SessionTypeStartTime";
			$k++; 
			if($k>0) 
				$query .= ", ";
			$query .= "SessionTypeDurationTime";
			$k++; 
		$query .= ") values (";
		$query .= "? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $SessionTypeTitle); 
		array_push($ValueListArray, $SessionTypeLocation); 
		array_push($ValueListArray, $SessionTypeStartTime); 
		array_push($ValueListArray, $SessionTypeDurationTime); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SessionTypes::GetLastID();
		$mysql->audit("ثبت داده جدید در الگوهای جلسه با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $SessionTypeTitle: عنوان
	* @param $SessionTypeLocation: محل تشکیل
	* @param $SessionTypeStartTime: زمان شروع
	* @param $SessionTypeDurationTime: مدت زمان
	* @return 	*/
	static function Update($UpdateRecordID, $SessionTypeTitle, $SessionTypeLocation, $SessionTypeStartTime, $SessionTypeDurationTime)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionTypes set ";
		if($k>0) 
			$query .= ", ";
		$query .= "SessionTypeTitle=? ";
			$k++;
		if($k>0) 
			$query .= ", ";
		$query .= "SessionTypeLocation=? ";
			$k++;
		if($k>0) 
			$query .= ", ";
		$query .= "SessionTypeStartTime=? ";
			$k++;
		if($k>0) 
			$query .= ", ";
		$query .= "SessionTypeDurationTime=? ";
			$k++;
		$query .= " where SessionTypeID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $SessionTypeTitle); 
		array_push($ValueListArray, $SessionTypeLocation); 
		array_push($ValueListArray, $SessionTypeStartTime); 
		array_push($ValueListArray, $SessionTypeDurationTime); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در الگوهای جلسه");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.SessionTypes where SessionTypeID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from sessionmanagement.SessionTypeMembers where SessionTypeID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از الگوهای جلسه");
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
		$query = "select SessionTypes.* from sessionmanagement.SessionTypes  ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($FromRec, $NumberOfRec));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionTypes();
			$ret[$k]->SessionTypeID=$rec["SessionTypeID"];
			$ret[$k]->SessionTypeTitle=$rec["SessionTypeTitle"];
			$ret[$k]->SessionTypeLocation=$rec["SessionTypeLocation"];
			$ret[$k]->SessionTypeStartTime=$rec["SessionTypeStartTime"];
			$ret[$k]->SessionTypeDurationTime=$rec["SessionTypeDurationTime"];
			$k++;
		}
		return $ret;
	}
	/**
	* @param $SessionTypeTitle: عنوان
	* @param $SessionTypeLocation: محل تشکیل
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($SessionTypeTitle, $SessionTypeLocation, $OtherConditions, $OrderByFieldName="", $OrderType="")
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SessionTypes.* from sessionmanagement.SessionTypes  ";
		$cond = "";
		if($SessionTypeTitle!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "SessionTypes.SessionTypeTitle like ? ";
		}
		if($SessionTypeLocation!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "SessionTypes.SessionTypeLocation like ? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($SessionTypeTitle!="") 
			array_push($ValueListArray, "%".$SessionTypeTitle."%"); 
		if($SessionTypeLocation!="") 
			array_push($ValueListArray, "%".$SessionTypeLocation."%"); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionTypes();
			$ret[$k]->SessionTypeID=$rec["SessionTypeID"];
			$ret[$k]->SessionTypeTitle=$rec["SessionTypeTitle"];
			$ret[$k]->SessionTypeLocation=$rec["SessionTypeLocation"];
			$ret[$k]->SessionTypeStartTime=$rec["SessionTypeStartTime"];
			$ret[$k]->SessionTypeDurationTime=$rec["SessionTypeDurationTime"];
			$k++;
		}
		return $ret;
	}
	function ShowSummary($RecID)
	{
		$ret = "<br>";
		$ret .= "<div class='container-fluid'>";
        $ret .= "<div class='row'>";
        $ret .= "<div class='col-md-2'></div>";
		$ret .= "<div class='alert alert-dark col-md-4 '>";
		$obj = new be_SessionTypes();
		$obj->LoadDataFromDatabase($RecID);
		$ret .= "<tr>";
		$ret .= "<td>";
		$ret .= "<b>". C_TITLE .": </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->SessionTypeTitle, ENT_QUOTES, 'UTF-8');
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</div>";
        $ret .= "<div class='alert alert-dark col-md-4 '>";
        $ret .= "<tr>";
		$ret .= "<td>";
		$ret .= "<b> ". C_SESSION_LOCATION.": </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->SessionTypeLocation, ENT_QUOTES, 'UTF-8');
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</div>";
        $ret .= "<div class='col-md-2'></div>";
        $ret .= "</div>";
        $ret .= "</div>";
		return $ret;
	}
	
	function ShowTabs($RecID, $CurrentPageName)
	{
	    $ret = "<div class='container-fluid'>";
        $ret .= "<div class='row'>";
        $ret .= "<div class='col-md-2'></div>";
        $ret .= "<div class='table-responsive col-md-8'>";
        $ret .= "<table class='table text-center'>";
 		$ret .= "<tr class='row table-bordered'>";
		$ret .= "<td class='col-md-4'";
		if($CurrentPageName=="NewSessionTypes")
			$ret .= "bgcolor=\"#dee2e6\" ";
		$ret .= "><a href='NewSessionTypes.php?UpdateID=".$RecID."'>".C_SESSION_INFO." </a></td>";
		$ret .= "<td class='col-md-4'";
		if($CurrentPageName=="ManagePersonPermittedSessionTypes")
 			$ret .= " bgcolor=\"#dee2e6\" ";
		$ret .= "><a href='ManagePersonPermittedSessionTypes.php?SessionTypeID=".$RecID."'>".C_SESSION_PERMITTED_PERSON." </a></td>";
		$ret .= "<td class='col-md-4'";
		if($CurrentPageName=="ManageSessionTypeMembers")
 			$ret .= " bgcolor=\"#dee2e6\" ";
		$ret .= "><a href='ManageSessionTypeMembers.php?SessionTypeID=".$RecID."'>".C_SESSION_MEMBERS."</a></td>";
		$ret .= "</table>";
		$ret .= "</div>";
		$ret .= "<div class='col-md-2'></div>";
		$ret .= "</div>";
        $ret .= "</div>";

        return $ret;
	}
	
	
}
	

?>