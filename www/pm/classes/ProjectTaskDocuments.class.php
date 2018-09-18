<?php
/*
 تعریف کلاسها و متدهای مربوط به : اسناد کارها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/

/*
کلاس پایه: اسناد کارها
*/
class be_ProjectTaskDocuments
{
	public $ProjectTaskDocumentID;		//
	public $ProjectTaskID;		//کار مربوطه
	public $CreatorID;		//ایجاد کننده
	public $CreatorID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */
	public $CreateTime;		//تاریخ ایجاد
	public $CreateTime_Shamsi;		/* مقدار شمسی معادل با تاریخ ایجاد */
	public $DocumentDescription;		//شرح
	public $FileContent;		//فایل ضمیمه
	public $FileName;		//نام فایل

	function be_ProjectTaskDocuments() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ProjectTaskDocuments.* 
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, concat(g2j(CreateTime), ' ', substr(CreateTime, 12,10)) as CreateTime_Shamsi from projectmanagement.ProjectTaskDocuments 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskDocuments.CreatorID)  where  ProjectTaskDocuments.ProjectTaskDocumentID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->ProjectTaskDocumentID=$rec["ProjectTaskDocumentID"];
			$this->ProjectTaskID=$rec["ProjectTaskID"];
			$this->CreatorID=$rec["CreatorID"];
			$this->CreatorID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$this->CreateTime=$rec["CreateTime"];
			$this->CreateTime_Shamsi=$rec["CreateTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->DocumentDescription=$rec["DocumentDescription"];
			$this->FileContent=$rec["FileContent"];
			$this->FileName=$rec["FileName"];
		}
	}
}
/*
کلاس مدیریت اسناد کارها
*/
class manage_ProjectTaskDocuments
{
	static function GetCount($ProjectTaskID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(ProjectTaskDocumentID) as TotalCount from projectmanagement.ProjectTaskDocuments";
			$query .= " where ProjectTaskID='".$ProjectTaskID."'";
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
		$query = "select max(ProjectTaskDocumentID) as MaxID from projectmanagement.ProjectTaskDocuments";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $ProjectTaskID: کار مربوطه
	* @param $DocumentDescription: شرح
	* @param $FileContent: فایل ضمیمه
	* @param $FileName: نام فایل
	* @return کد داده اضافه شده	*/
	static function Add($ProjectTaskID, $DocumentDescription, $FileContent, $FileName)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ProjectTaskDocuments (";
		$query .= " ProjectTaskID";
		$query .= ", CreatorID";
		$query .= ", CreateTime";
		$query .= ", DocumentDescription";
		$query .= ", FileName";
		$query .= ") values (";
		$query .= "? , ? , now() , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $ProjectTaskID); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		array_push($ValueListArray, $DocumentDescription); 
		array_push($ValueListArray, $FileName); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ProjectTaskDocuments::GetLastID();
		//$mysql->audit("ثبت داده جدید در اسناد کارها با کد ".$LastID);
		
		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$mysql = dbclass::getInstance();
			$query = "update projectmanagement.ProjectTaskDocuments set ";
			$query .= " FileContent='".$FileContent."' ";
			$query .= " where ProjectTaskDocumentID='".$LastID."'";
			$mysql->ExecuteBinary($query);
 		}
		
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($ProjectTaskID, "", "DOCUMENT", $LastID, "ADD");
		
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $DocumentDescription: شرح
	* @param $FileContent: فایل ضمیمه
	* @param $FileName: نام فایل
	* @return 	*/
	static function Update($UpdateRecordID, $DocumentDescription, $FileContent, $FileName)
	{
		$obj = new be_ProjectTaskDocuments();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ProjectTaskDocuments set ";
			$query .= " DocumentDescription=? ";
		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", FileName=? ";
		}
		$query .= " where ProjectTaskDocumentID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $DocumentDescription); 
		if($FileName!="")
		{ 
			array_push($ValueListArray, $FileName); 
		} 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$mysql = dbclass::getInstance();
			$query = "update projectmanagement.ProjectTaskDocuments set ";
			$query .= " FileContent='".$FileContent."' ";
			$query .= " where ProjectTaskDocumentID='".$UpdateRecordID."'";
			$mysql->ExecuteBinary($query);
 		}
		
		//$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در اسناد کارها");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "DOCUMENT", $UpdateRecordID, "UPDATE");
		
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_ProjectTaskDocuments();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ProjectTaskDocuments where ProjectTaskDocumentID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		//$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از اسناد کارها");
		require_once("ProjectTaskHistory.class.php");
		manage_ProjectTaskHistory::Add($obj->ProjectTaskID, "", "DOCUMENT", $RemoveRecordID, "DELETE");
	}
	static function GetList($ProjectTaskID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ProjectTaskDocuments.ProjectTaskDocumentID
				,ProjectTaskDocuments.ProjectTaskID
				,ProjectTaskDocuments.CreatorID
				,ProjectTaskDocuments.CreateTime
				,ProjectTaskDocuments.DocumentDescription
				,ProjectTaskDocuments.FileName
			, concat(persons2.pfname, ' ', persons2.plname) as persons2_FullName 
			, concat(g2j(CreateTime), ' ', substr(CreateTime, 12, 10)) as CreateTime_Shamsi  from projectmanagement.ProjectTaskDocuments 
			LEFT JOIN projectmanagement.persons persons2 on (persons2.PersonID=ProjectTaskDocuments.CreatorID)  ";
		$query .= " where ProjectTaskID=? ";
		$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $ProjectTaskID);
		if($ppc->GetPermission("View_ProjectTaskDocuments")=="PRIVATE")
				$query .= " and ProjectTaskDocuments.CreatorID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_ProjectTaskDocuments")=="NONE")
				$query .= " and 0=1 ";
		$query .= " order by ProjectTaskDocuments.CreateTime desc;";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ProjectTaskID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ProjectTaskDocuments();
			$ret[$k]->ProjectTaskDocumentID=$rec["ProjectTaskDocumentID"];
			$ret[$k]->ProjectTaskID=$rec["ProjectTaskID"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			$ret[$k]->CreatorID_FullName=$rec["persons2_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->CreateTime=$rec["CreateTime"];
			$ret[$k]->CreateTime_Shamsi=$rec["CreateTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->DocumentDescription=$rec["DocumentDescription"];
			$ret[$k]->FileName=$rec["FileName"];
			$k++;
		}
		return $ret;
	}
}
?>
