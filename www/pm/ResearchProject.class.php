<?php
/*
 تعریف کلاسها و متدهای مربوط به : پروژهی پژوهشی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-3-5
*/

/*
کلاس پایه: پروژهی پژوهشی
*/
class be_ResearchProject
{
	public $ResearchProjectID;		//
	public $title;		//عنوان
	public $ProjectType;		//نوع
	public $ProjectType_Desc;		/* شرح مربوط به نوع */
	public $OwnerID;		//مالک
	public $OwnerID_FullName;		/* نام و نام خانوادگی مربوط به مالک */

	function be_ResearchProject() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ResearchProject.* 
			, CASE ResearchProject.ProjectType 
				WHEN 'PAPER' THEN 'مقاله' 
				WHEN 'THESIS' THEN 'پایان نامه' 
				WHEN 'BOOK' THEN 'کتاب' 
				END as ProjectType_Desc 
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName from projectmanagement.ResearchProject 
			LEFT JOIN hrmstotal.persons persons3 on (persons3.PersonID=ResearchProject.OwnerID)  where  ResearchProject.ResearchProjectID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ResearchProjectID=$rec["ResearchProjectID"];
			$this->title=$rec["title"];
			$this->ProjectType=$rec["ProjectType"];
			$this->ProjectType_Desc=$rec["ProjectType_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->OwnerID=$rec["OwnerID"];
			$this->OwnerID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
		}
	}
}
/*
کلاس مدیریت پروژهی پژوهشی
*/
class manage_ResearchProject
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = "select count(ResearchProjectID) as TotalCount from projectmanagement.ResearchProject";
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
		$query = "select max(ResearchProjectID) as MaxID from projectmanagement.ResearchProject";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $title: عنوان
	* @param $ProjectType: نوع
	* @return کد داده اضافه شده	*/
	static function Add($title, $ProjectType)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ResearchProject (";
		$query .= " title";
		$query .= ", ProjectType";
		$query .= ", OwnerID";
		$query .= ") values (";
		$query .= "? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $title); 
		array_push($ValueListArray, $ProjectType); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ResearchProject::GetLastID();
		$mysql->audit("ثبت داده جدید در پروژهی پژوهشی با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $title: عنوان
	* @param $ProjectType: نوع
	* @return 	*/
	static function Update($UpdateRecordID, $title, $ProjectType)
	{
		$k=0;
		$LogDesc = manage_ResearchProject::ComparePassedDataWithDB($UpdateRecordID, $title, $ProjectType);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ResearchProject set ";
			$query .= " title=? ";
			$query .= ", ProjectType=? ";
		$query .= " where ResearchProjectID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $title); 
		array_push($ValueListArray, $ProjectType); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پروژهی پژوهشی - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ResearchProject where ResearchProjectID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ResearchProjectSessions where ResearchProjectID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.RefrenceTypes where ResearchProjectID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ResearchProjectRefrences where ResearchProjectID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.ResearchProjectComments where ResearchProjectID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پروژهی پژوهشی");
	}
	
	static function IsCurrentUserValid($ReserchProjectID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select * from projectmanagement.ResearchProjectAccessList where ResearchProjectID=? and AccessPersonID='".$_SESSION["PersonID"]."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ReserchProjectID));
		if($rec=$res->fetch())
		  return true;
		return false;
	}
	
	static function GetList($OwnerID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ResearchProject.ResearchProjectID
				,ResearchProject.title
				,ResearchProject.ProjectType
				,ResearchProject.OwnerID
			, CASE ResearchProject.ProjectType 
				WHEN 'PAPER' THEN 'مقاله' 
				WHEN 'THESIS' THEN 'پایان نامه' 
				WHEN 'BOOK' THEN 'کتاب' 
				END as ProjectType_Desc 
			, concat(persons3.pfname, ' ', persons3.plname) as persons3_FullName  from projectmanagement.ResearchProject 
			LEFT JOIN hrmstotal.persons persons3 on (persons3.PersonID=ResearchProject.OwnerID) 
			where OwnerID=? 
			or ResearchProject.ResearchProjectID in 
			(select ResearchProjectID from projectmanagement.ResearchProjectAccessList
			where AccessPersonID=?
			)";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OwnerID, $OwnerID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ResearchProject();
			$ret[$k]->ResearchProjectID=$rec["ResearchProjectID"];
			$ret[$k]->title=$rec["title"];
			$ret[$k]->ProjectType=$rec["ProjectType"];
			$ret[$k]->ProjectType_Desc=$rec["ProjectType_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->OwnerID=$rec["OwnerID"];
			$ret[$k]->OwnerID_FullName=$rec["persons3_FullName"]; // محاسبه از روی جدول وابسته
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $title: عنوان
	* @param $ProjectType: نوع
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $title, $ProjectType)
	{
		$ret = "";
		$obj = new be_ResearchProject();
		$obj->LoadDataFromDatabase($CurRecID);
		if($title!=$obj->title)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($ProjectType!=$obj->ProjectType)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "نوع";
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
		$obj = new be_ResearchProject();
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
		$ret .= "<td width=1%><a href='ManageResearchProject.php'><img src='images/up.jpg' width=15 title='فهرست کارهای پژوهشی'></a></td>";
		$ret .= "</tr>";
		$ret .= "</table>";
		return $ret;
	}
	function ShowTabs($RecID, $CurrentPageName)
	{
		$ret = "<table  cellspacing=\"0\" class=\"table-sm col-lg-11 table-bordered\" style=\"border-radius: 5px; float: none; margin: auto;\">";
 		$ret .= "<tr>";
		$ret .= "<td width=\"12\" ";
		if($CurrentPageName=="NewResearchProject")
			$ret .= "class=\"table-active\" ";
		$ret .= "><a href='NewResearchProject.php?UpdateID=".$RecID."'>".C_MAIN_PROPERTIES."</a></td>";
		$ret .= "<td width=\"12%\" ";
		if($CurrentPageName=="ManageResearchProjectSessions")
 			$ret .= "class=\"table-active\" ";
		$ret .= "><a href='ManageResearchProjectSessions.php?ResearchProjectID=".$RecID."'>".C_SEASONS."</a></td>";
		$ret .= "<td width=\"12%\" ";
		if($CurrentPageName=="ManageRefrenceTypes")
 			$ret .= "class=\"table-active\" ";
		$ret .= "><a href='ManageRefrenceTypes.php?ResearchProjectID=".$RecID."'>".C_REFERENCE_TYPES."</a></td>";
		$ret .= "<td width=\"12%\" ";
		if($CurrentPageName=="ManageResearchProjectRefrences")
 			$ret .= "class=\"table-active\" ";
		$ret .= "><a href='ManageResearchProjectRefrences.php?ResearchProjectID=".$RecID."'>".C_REFERENCES."</a></td>";
		$ret .= "<td width=\"12%\" ";
		if($CurrentPageName=="ManageResearchProjectComments")
 			$ret .= "class=\"table-active\" ";
		$ret .= "><a href='ManageResearchProjectComments.php?ResearchProjectID=".$RecID."'>".C_NOTES."</a></td>";
		$ret .= "<td width=\"12%\" ";
		if($CurrentPageName=="ManageResearchProjectResults")
 			$ret .= "class=\"table-active\" ";
		$ret .= "><a href='ManageResearchProjectResults.php?ResearchProjectID=".$RecID."'>".C_OUTPUTS."</a></td>";
		$ret .= "<td width=\"12%\" ";
		if($CurrentPageName=="ManageResearchProjectAccessList")
 			$ret .= "class=\"table-active\" ";
		$ret .= "><a href='ManageResearchProjectAccessList.php?ResearchProjectID=".$RecID."'>".C_PRIVILEGES."</a></td>";
		$ret .= "</table>";
		return $ret;
		
	}
}
?>