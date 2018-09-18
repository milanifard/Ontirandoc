<?php
/*
 تعریف کلاسها و متدهای مربوط به : پروژه ادغام هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-10-15
*/

/*
کلاس پایه: پروژه ادغام هستان نگار
*/
class be_OntologyMergeProject
{
	public $OntologyMergeProjectID;		//
	public $TargetOntologyID;		//
	public $TargetOntologyID_Desc;		/* شرح مربوط به هستان نگار مقصد */
	public $MergeStatus;		//
	public $MergeProjectTitle;		//

	function be_OntologyMergeProject() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select OntologyMergeProject.* 
			, p1.OntologyTitle  as p1_OntologyTitle from projectmanagement.OntologyMergeProject 
			LEFT JOIN projectmanagement.ontologies  p1 on (p1.OntologyID=OntologyMergeProject.TargetOntologyID)  where  OntologyMergeProject.OntologyMergeProjectID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyMergeProjectID=$rec["OntologyMergeProjectID"];
			$this->TargetOntologyID=$rec["TargetOntologyID"];
			$this->TargetOntologyID_Desc=$rec["p1_OntologyTitle"]; // محاسبه از روی جدول وابسته
			$this->MergeStatus=$rec["MergeStatus"];
			$this->MergeProjectTitle=$rec["MergeProjectTitle"];
		}
	}
}
/*
کلاس مدیریت پروژه ادغام هستان نگار
*/
class manage_OntologyMergeProject
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = "select count(OntologyMergeProjectID) as TotalCount from projectmanagement.OntologyMergeProject";
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
		$query = "select max(OntologyMergeProjectID) as MaxID from projectmanagement.OntologyMergeProject";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $TargetOntologyID: هستان نگار مقصد
	* @param $MergeProjectTitle: عنوان پروژه
	* @return کد داده اضافه شده	*/
	static function Add($TargetOntologyID, $MergeProjectTitle)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.OntologyMergeProject (";
		$query .= " TargetOntologyID";
		$query .= ", MergeProjectTitle";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $TargetOntologyID); 
		array_push($ValueListArray, $MergeProjectTitle); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_OntologyMergeProject::GetLastID();
		$mysql->audit("ثبت داده جدید در پروژه ادغام هستان نگار با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $TargetOntologyID: هستان نگار مقصد
	* @param $MergeProjectTitle: عنوان پروژه
	* @return 	*/
	static function Update($UpdateRecordID, $TargetOntologyID, $MergeProjectTitle)
	{
		$k=0;
		$LogDesc = manage_OntologyMergeProject::ComparePassedDataWithDB($UpdateRecordID, $TargetOntologyID, $MergeProjectTitle);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyMergeProject set ";
			$query .= " TargetOntologyID=? ";
			$query .= ", MergeProjectTitle=? ";
		$query .= " where OntologyMergeProjectID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $TargetOntologyID); 
		array_push($ValueListArray, $MergeProjectTitle); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پروژه ادغام هستان نگار - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyMergeProject where OntologyMergeProjectID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.OntologyMergeProjectMembers where OntologyMergeProjectID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پروژه ادغام هستان نگار");
	}
	static function GetList()
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyMergeProject.OntologyMergeProjectID
				,OntologyMergeProject.TargetOntologyID
				,OntologyMergeProject.MergeStatus
				,OntologyMergeProject.MergeProjectTitle
			, p1.OntologyTitle  as p1_OntologyTitle  from projectmanagement.OntologyMergeProject 
			LEFT JOIN projectmanagement.ontologies  p1 on (p1.OntologyID=OntologyMergeProject.TargetOntologyID)  ";
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_OntologyMergeProject();
			$ret[$k]->OntologyMergeProjectID=$rec["OntologyMergeProjectID"];
			$ret[$k]->TargetOntologyID=$rec["TargetOntologyID"];
			$ret[$k]->TargetOntologyID_Desc=$rec["p1_OntologyTitle"]; // محاسبه از روی جدول وابسته
			$ret[$k]->MergeStatus=$rec["MergeStatus"];
			$ret[$k]->MergeProjectTitle=$rec["MergeProjectTitle"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $TargetOntologyID: هستان نگار مقصد
	* @param $MergeProjectTitle: عنوان پروژه
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $TargetOntologyID, $MergeProjectTitle)
	{
		$ret = "";
		$obj = new be_OntologyMergeProject();
		$obj->LoadDataFromDatabase($CurRecID);
		if($TargetOntologyID!=$obj->TargetOntologyID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "هستان نگار مقصد";
		}
		if($MergeProjectTitle!=$obj->MergeProjectTitle)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان پروژه";
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
		$obj = new be_OntologyMergeProject();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "<tr>";
		$ret .= "<td width=\"1%\" nowrap>";
		$ret .= "<b>عنوان پروژه: </b>";
		$ret .= "</td>";
		$ret .= "<td>";
		$ret .= htmlentities($obj->MergeProjectTitle, ENT_QUOTES, 'UTF-8');
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
		if($CurrentPageName=="NewOntologyMergeProject")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='NewOntologyMergeProject.php?UpdateID=".$RecID."'>مشخصات اصلی</a></td>";
		$ret .= "<td width=\"50%\" ";
		if($CurrentPageName=="ManageOntologyMergeProjectMembers")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageOntologyMergeProjectMembers.php?OntologyMergeProjectID=".$RecID."'>هستان نگارها جزو پروژه ادغام</a></td>";
		$ret .= "</table>";
		return $ret;
	}
}
?>