<?php
/*
 تعریف کلاسها و متدهای مربوط به : پیامهای شخصی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-21
*/

/*
کلاس پایه: پیامهای شخصی
*/
class be_PrivateMessages
{
	public $PrivateMessageID;		//
	public $MessageTitle;		//عنوان
	public $MessageBody;		//متن
	public $SenderID;		//فرستنده
	public $CreateTime;		//تاریخ ارسال
	public $CreateTime_Shamsi;		/* مقدار شمسی معادل با تاریخ ارسال */
	public $FileName;		//نام فایل مرتبط
	public $FileContent;		//محتوای فایل


	function be_PrivateMessages() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select PrivateMessages.* 
			, concat(g2j(CreateTime), ' ', substr(CreateTime, 12,10)) as CreateTime_Shamsi from projectmanagement.PrivateMessages  where  PrivateMessages.PrivateMessageID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->PrivateMessageID=$rec["PrivateMessageID"];
			$this->MessageTitle=$rec["MessageTitle"];
			$this->MessageBody=$rec["MessageBody"];
			$this->SenderID=$rec["SenderID"];
			$this->CreateTime=$rec["CreateTime"];
			$this->CreateTime_Shamsi=$rec["CreateTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->FileName=$rec["FileName"];
			$this->FileContent=$rec["FileContent"];
		}
	}
	
}
/*
کلاس مدیریت پیامهای شخصی
*/
class manage_PrivateMessages
{
	
	static function CreateTree($PrivateMessageID, $ParentID = 0, $Level = 0)
	{
	  if($Level>20)
	    return;
	  $result = "";
	  $query = "select 
	  PrivateMessageFollowID, comment, ReferFileName, 
  concat(projectmanagement.g2j(ReferTime), substr(ReferTime, 11, 10)) as gReferTime,
  concat(p1.pfname, ' ', p1.plname) as FromPerson,
  concat(p2.pfname, ' ', p2.plname) as ToPerson 
	  from projectmanagement.PrivateMessageFollows
  left JOIN projectmanagement.persons p1 on (p1.PersonID=PrivateMessageFollows.FromPersonID)
  left JOIN projectmanagement.persons p2 on (p2.PersonID=PrivateMessageFollows.ToPersonID)	  
	  where PrivateMessageID=? and UpperLevelID=? ";
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare ($query);
	  //echo $PrivateMessageID." ".$ParentID."<br>";
	  $res = $mysql->ExecuteStatement(array($PrivateMessageID, $ParentID));
	  while($rec = $res->fetch())
	  {
	    for($j=0; $j<$Level; $j++)
	      $result .= "&nbsp;&nbsp;&nbsp;";
	    $result .= "<img src='images/join.gif'>";
	    $result .= "<font title='".$rec["gReferTime"]."'> از ".$rec["FromPerson"]." به ".$rec["ToPerson"]."</font> :".$rec["comment"]." ";
	    if($rec["ReferFileName"]!="")
	      $result .= " <a href='DownloadFile.php?FileType=PrivateMessageFollows&RecID=".$rec["PrivateMessageFollowID"]."'>".ضمیمه."</a>";
	    $result .= "<br>";
	    $result .= manage_PrivateMessages::CreateTree($PrivateMessageID, $rec["PrivateMessageFollowID"], $Level+1);
	  }
	  return $result;
	}
	
	static function IsPermitted($PrivateMessageID)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select PrivateMessageFollowID from projectmanagement.PrivateMessageFollows
	  where (FromPersonID='".$_SESSION["PersonID"]."' or ToPersonID='".$_SESSION["PersonID"]."') and PrivateMessageID=?";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($PrivateMessageID));
	  if($rec = $res->fetch())
	    return true;
	  return false;
	}

	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(PrivateMessageID) as TotalCount from projectmanagement.PrivateMessages";
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
		$query = "select max(PrivateMessageID) as MaxID from projectmanagement.PrivateMessages";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $MessageTitle: عنوان
	* @param $MessageBody: متن
	* @param $FileContent: محتوای فایل
	* @param $FileName: نام فایل
	* @return کد داده اضافه شده	*/
	static function Add($MessageTitle, $MessageBody, $FileContent, $FileName)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.PrivateMessages (";
		$query .= " MessageTitle";
		$query .= ", MessageBody";
		$query .= ", SenderID";
		$query .= ", CreateTime";
		$query .= ", FileContent";
		$query .= ", FileName";
		$query .= ") values (";
		$query .= "? , ? , ? , now() , '".$FileContent."', ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $MessageTitle); 
		array_push($ValueListArray, $MessageBody); 
		array_push($ValueListArray, $_SESSION["UserID"]); 
		array_push($ValueListArray, $FileName); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_PrivateMessages::GetLastID();
		$mysql->audit("ثبت داده جدید در پیامهای شخصی با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $MessageTitle: عنوان
	* @param $MessageBody: متن
	* @param $FileContent: محتوای فایل
	* @param $FileName: نام فایل
	* @return 	*/
	static function Update($UpdateRecordID, $MessageTitle, $MessageBody, $FileContent, $FileName)
	{
		$k=0;
		$LogDesc = manage_PrivateMessages::ComparePassedDataWithDB($UpdateRecordID, $MessageTitle, $MessageBody, $FileContent, $FileName);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.PrivateMessages set ";
			$query .= " MessageTitle=? ";
			$query .= ", MessageBody=? ";
		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", FileName=?, FileContent='".$FileContent."' ";
		}
		$query .= " where PrivateMessageID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $MessageTitle); 
		array_push($ValueListArray, $MessageBody); 
		if($FileName!="")
		{ 
			array_push($ValueListArray, $FileName); 
		} 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پیامهای شخصی - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.PrivateMessages where PrivateMessageID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پیامهای شخصی");
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
		$query = "select PrivateMessages.PrivateMessageID
				,PrivateMessages.MessageTitle
				,PrivateMessages.MessageBody
				,PrivateMessages.SenderID
				,PrivateMessages.CreateTime
				,PrivateMessages.FileName
				,PrivateMessages.FileContent
			, concat(g2j(CreateTime), ' ', substr(CreateTime, 12, 10)) as CreateTime_Shamsi  from projectmanagement.PrivateMessages  ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_PrivateMessages();
			$ret[$k]->PrivateMessageID=$rec["PrivateMessageID"];
			$ret[$k]->MessageTitle=$rec["MessageTitle"];
			$ret[$k]->MessageBody=$rec["MessageBody"];
			$ret[$k]->SenderID=$rec["SenderID"];
			$ret[$k]->CreateTime=$rec["CreateTime"];
			$ret[$k]->CreateTime_Shamsi=$rec["CreateTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->FileName=$rec["FileName"];
			$ret[$k]->FileContent=$rec["FileContent"];
			$k++;
		}
		return $ret;
	}
	/**
	* @param $MessageTitle: عنوان
	* @param $MessageBody: متن
	* @param $SenderID: فرستنده
	* @param $CreateTime: تاریخ ارسال
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return لیست داده های حاصل جستجو
	*/
	static function Search($MessageTitle, $MessageBody, $SenderID, $CreateTime, $OtherConditions, $FromRec, $NumberOfRec , $OrderByFieldName="", $OrderType="")
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select PrivateMessages.PrivateMessageID
				,PrivateMessages.MessageTitle
				,PrivateMessages.MessageBody
				,PrivateMessages.SenderID
				,PrivateMessages.CreateTime
				,PrivateMessages.FileName
				,PrivateMessages.FileContent
			, concat(g2j(CreateTime), ' ', substr(CreateTime, 12, 10)) as CreateTime_Shamsi  from projectmanagement.PrivateMessages  ";
		$cond = "";
		if($MessageTitle!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "PrivateMessages.MessageTitle like ? ";
		}
		if($MessageBody!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "PrivateMessages.MessageBody like ? ";
		}
		if($SenderID!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "PrivateMessages.SenderID like ? ";
		}
		if($CreateTime!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "PrivateMessages.CreateTime like ? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		if($OrderByFieldName!="")
			$query .= " order by ".$OrderByFieldName." ".$OrderType;
		$query .= " limit ".$FromRec.", ".$NumberOfRec;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($MessageTitle!="") 
			array_push($ValueListArray, "%".$MessageTitle."%"); 
		if($MessageBody!="") 
			array_push($ValueListArray, "%".$MessageBody."%"); 
		if($SenderID!="") 
			array_push($ValueListArray, "%".$SenderID."%"); 
		if($CreateTime!="") 
			array_push($ValueListArray, "%".$CreateTime."%"); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_PrivateMessages();
			$ret[$k]->PrivateMessageID=$rec["PrivateMessageID"];
			$ret[$k]->MessageTitle=$rec["MessageTitle"];
			$ret[$k]->MessageBody=$rec["MessageBody"];
			$ret[$k]->SenderID=$rec["SenderID"];
			$ret[$k]->CreateTime=$rec["CreateTime"];
			$ret[$k]->CreateTime_Shamsi=$rec["CreateTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->FileName=$rec["FileName"];
			$ret[$k]->FileContent=$rec["FileContent"];
			$k++;
		}
		return $ret;
	}
	/**
	* @param $MessageTitle: عنوان
	* @param $MessageBody: متن
	* @param $SenderID: فرستنده
	* @param $CreateTime: تاریخ ارسال
	* @param $OtherConditions سایر مواردی که باید به انتهای شرایط اضافه شوند
	* @return تعداد داده های حاصل جستجو
	*/
	static function SearchResultCount($MessageTitle, $MessageBody, $SenderID, $CreateTime, $OtherConditions)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(*) as TotalCount from projectmanagement.PrivateMessages	";
 		$cond = "";
		if($MessageTitle!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "PrivateMessages.MessageTitle like ? ";
		}
		if($MessageBody!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "PrivateMessages.MessageBody like ? ";
		}
		if($SenderID!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "PrivateMessages.SenderID like ? ";
		}
		if($CreateTime!="") 
		{
			if($cond!="") $cond .= " and ";
				$cond .= "PrivateMessages.CreateTime like ? ";
		}
		if($cond!="" || $OtherConditions!="")
			$query .= " where ";
		$query .= $cond.$OtherConditions;
		$mysql->Prepare($query);
		$ValueListArray = array();
		if($MessageTitle!="") 
			array_push($ValueListArray, "%".$MessageTitle."%"); 
		if($MessageBody!="") 
			array_push($ValueListArray, "%".$MessageBody."%"); 
		if($SenderID!="") 
			array_push($ValueListArray, "%".$SenderID."%"); 
		if($CreateTime!="") 
			array_push($ValueListArray, "%".$CreateTime."%"); 
		$res = $mysql->ExecuteStatement($ValueListArray);
		if($rec = $res->fetch()) return $rec["TotalCount"];  else return 0;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $MessageTitle: عنوان
	* @param $MessageBody: متن
	* @param $FileContent: محتوای فایل
	* @param $FileName: نام فایل
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $MessageTitle, $MessageBody, $FileContent, $FileName)
	{
		$ret = "";
		$obj = new be_PrivateMessages();
		$obj->LoadDataFromDatabase($CurRecID);
		if($MessageTitle!=$obj->MessageTitle)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($MessageBody!=$obj->MessageBody)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "متن";
		}
		if($FileContent!=$obj->FileContent)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "محتوای فایل";
		}
		return $ret;
	}
}

/*
 تعریف کلاسها و متدهای مربوط به : گردش پیامهای شخصی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-21
*/

/*
کلاس پایه: گردش پیامهای شخصی
*/
class be_PrivateMessageFollows extends be_PrivateMessages
{
	public $PrivateMessageFollowID;		//
	public $PrivateMessageID;		//
	public $comment;		//یادداشت
	public $ReferTime;		//زمان ارجاع
	public $ReferTime_Shamsi;		/* مقدار شمسی معادل با زمان ارجاع */
	public $FromPersonID;		//از کاربر
	public $FromPersonID_FullName;		/* نام و نام خانوادگی مربوط به از کاربر */
	public $ToPersonID;		//به کاربر
	public $ToPersonID_FullName;		/* نام و نام خانوادگی مربوط به به کاربر */
	public $UpperLevelID;		//کد ارجاع بالاتر
	public $ReferFileName;		//نام فایل
	public $ReferFileContent;		//محتوای فایل ضمیمه
	public $ReferStatus;		//وضعیت
	public $ArchiveFolderID;		//پوشه بایگانی
	
	function be_PrivateMessageFollows() {}

	function SetToRead()
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Execute("update projectmanagement.PrivateMessageFollows set ReferStatus='READ' where ReferStatus='NOT_READ' and PrivateMessageFollowID=".$this->PrivateMessageFollowID);
	}
	
	function Load($RecID)
	{
		$query = "select PrivateMessageFollows.* 
			, concat(g2j(ReferTime), ' ', substr(ReferTime, 12,10)) as ReferTime_Shamsi 
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName from projectmanagement.PrivateMessageFollows 
			LEFT JOIN projectmanagement.persons persons4 on (persons4.PersonID=PrivateMessageFollows.FromPersonID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=PrivateMessageFollows.ToPersonID)  where  PrivateMessageFollows.PrivateMessageFollowID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->PrivateMessageFollowID=$rec["PrivateMessageFollowID"];
			$this->PrivateMessageID=$rec["PrivateMessageID"];
			$this->comment=$rec["comment"];
			$this->ReferTime=$rec["ReferTime"];
			$this->ReferTime_Shamsi=$rec["ReferTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$this->FromPersonID=$rec["FromPersonID"];
			$this->FromPersonID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
			$this->ToPersonID=$rec["ToPersonID"];
			$this->ToPersonID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$this->UpperLevelID=$rec["UpperLevelID"];
			$this->ReferFileName=$rec["ReferFileName"];
			$this->ReferFileContent=$rec["ReferFileContent"];
			$this->ReferStatus=$rec["ReferStatus"];
			$this->ArchiveFolderID=$rec["ArchiveFolderID"];
			$this->LoadDataFromDatabase($rec["PrivateMessageID"]);
		}
	}
}
/*
کلاس مدیریت گردش پیامهای شخصی
*/

class manage_PrivateMessageFollows
{
	static function GetNewMessagesCount()
	{
	    $query = "select count(*) as tcount from projectmanagement.PrivateMessageFollows 
			where ";	
	    $query .= " ReferStatus='NOT_READ' and ToPersonID='".$_SESSION["PersonID"]."' ";
	    //echo $query;
	    $mysql = pdodb::getInstance();
	    $res = $mysql->Execute($query);
	    if($rec = $res->fetch())
	      return $rec["tcount"];
	    return 0;
	}

	// $BoxType (InBox, SentBox)
	static function GetList($BoxType, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType)
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
		$query = "select PrivateMessageFollows.PrivateMessageFollowID
				,PrivateMessageFollows.PrivateMessageID
				,PrivateMessageFollows.comment
				,PrivateMessageFollows.ReferTime
				,PrivateMessageFollows.FromPersonID
				,PrivateMessageFollows.ToPersonID
				,PrivateMessageFollows.UpperLevelID
				,PrivateMessageFollows.ReferFileName
				,PrivateMessageFollows.ReferStatus
				,PrivateMessageFollows.ArchiveFolderID
			, concat(g2j(ReferTime), ' ', substr(ReferTime, 12, 10)) as ReferTime_Shamsi 
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName  from projectmanagement.PrivateMessageFollows 
			LEFT JOIN projectmanagement.persons persons4 on (persons4.PersonID=PrivateMessageFollows.FromPersonID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=PrivateMessageFollows.ToPersonID)  
			where ";
		
		if($BoxType=="InBox")
		  $query .= " ReferStatus<>'REFER' and ReferStatus<>'ARCHIVE' and ToPersonID='".$_SESSION["PersonID"]."' ";
		else if($BoxType=="SentBox")
		  $query .= " FromPersonID='".$_SESSION["PersonID"]."' ";
		$query .= " order by ".$OrderByFieldName." ".$OrderType." ";
		$query .= " limit ".$FromRec.",".$NumberOfRec." ";

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_PrivateMessageFollows();
			$ret[$k]->PrivateMessageFollowID=$rec["PrivateMessageFollowID"];
			$ret[$k]->PrivateMessageID=$rec["PrivateMessageID"];
			$ret[$k]->comment=$rec["comment"];
			$ret[$k]->ReferTime=$rec["ReferTime"];
			$ret[$k]->ReferTime_Shamsi=$rec["ReferTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->FromPersonID=$rec["FromPersonID"];
			$ret[$k]->FromPersonID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ToPersonID=$rec["ToPersonID"];
			$ret[$k]->ToPersonID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->UpperLevelID=$rec["UpperLevelID"];
			$ret[$k]->ReferFileName=$rec["ReferFileName"];
			//$ret[$k]->ReferFileContent=$rec["ReferFileContent"];
			$ret[$k]->ReferStatus=$rec["ReferStatus"];
			$ret[$k]->ArchiveFolderID=$rec["ArchiveFolderID"];
			
			$ret[$k]->LoadDataFromDatabase($ret[$k]->PrivateMessageID);

			$k++;
		}
		return $ret;
	}

	static function GetCount($BoxType)
	{

		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select count(*) as TotalCount
			from projectmanagement.PrivateMessageFollows 
			where ";
		if($BoxType=="InBox")
		  $query .= " ReferStatus<>'REFER' and ReferStatus<>'ARCHIVE' and ToPersonID='".$_SESSION["PersonID"]."' ";
		else if($BoxType=="SentBox")
		  $query .= " FromPersonID='".$_SESSION["PersonID"]."' ";
	
		$res = $mysql->Execute($query);
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	static function GetLastID()
	{
		$mysql = pdodb::getInstance();
		$query = "select max(PrivateMessageFollowID) as MaxID from projectmanagement.PrivateMessageFollows";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $PrivateMessageID: 
	* @param $comment: یادداشت
	* @param $FromPersonID: از کاربر
	* @param $ToPersonID: به کاربر
	* @param $UpperLevelID: کد ارجاع بالاتر
	* @param $ReferFileContent: محتوای فایل ضمیمه
	* @param $ReferFileName: نام فایل
	* @param $ReferStatus: وضعیت
	* @param $ArchiveFolderID: پوشه بایگانی
	* @return کد داده اضافه شده	*/
	static function Add($PrivateMessageID, $comment, $FromPersonID, $ToPersonID, $UpperLevelID, $ReferFileContent, $ReferFileName, $ReferStatus, $ArchiveFolderID)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.PrivateMessageFollows (";
		$query .= " PrivateMessageID";
		$query .= ", comment";
		$query .= ", ReferTime";
		$query .= ", FromPersonID";
		$query .= ", ToPersonID";
		$query .= ", UpperLevelID";
		$query .= ", ReferFileContent";
		$query .= ", ReferFileName";
		$query .= ", ReferStatus";
		$query .= ", ArchiveFolderID";
		$query .= ") values (";
		$query .= "? , ? , now() , ? , ? , ? , '".$ReferFileContent."', ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $PrivateMessageID); 
		array_push($ValueListArray, $comment); 
		array_push($ValueListArray, $FromPersonID); 
		array_push($ValueListArray, $ToPersonID); 
		array_push($ValueListArray, $UpperLevelID); 
		array_push($ValueListArray, $ReferFileName); 
		array_push($ValueListArray, $ReferStatus); 
		array_push($ValueListArray, $ArchiveFolderID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_PrivateMessageFollows::GetLastID();
		$mysql->audit("ثبت داده جدید در گردش پیامهای شخصی با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $comment: یادداشت
	* @param $ReferFileContent: محتوای فایل ضمیمه
	* @param $ReferFileName: نام فایل
	* @param $ReferStatus: وضعیت
	* @param $ArchiveFolderID: پوشه بایگانی
	* @return 	*/
	static function Update($UpdateRecordID, $comment, $ReferFileContent, $ReferFileName, $ReferStatus, $ArchiveFolderID)
	{
		$k=0;
		$LogDesc = manage_PrivateMessageFollows::ComparePassedDataWithDB($UpdateRecordID, $comment, $ReferFileContent, $ReferFileName, $ReferStatus, $ArchiveFolderID);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.PrivateMessageFollows set ";
			$query .= " comment=? ";
		if($ReferFileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", ReferFileName=?, ReferFileContent='".$ReferFileContent."' ";
		}
			$query .= ", ReferStatus=? ";
			$query .= ", ArchiveFolderID=? ";
		$query .= " where PrivateMessageFollowID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $comment); 
		if($ReferFileName!="")
		{ 
			array_push($ValueListArray, $ReferFileName); 
		} 
		array_push($ValueListArray, $ReferStatus); 
		array_push($ValueListArray, $ArchiveFolderID); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در گردش پیامهای شخصی - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.PrivateMessageFollows where PrivateMessageFollowID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از گردش پیامهای شخصی");
	}

	static function Archive($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.PrivateMessageFollows set ReferStatus='ARCHIVE' where PrivateMessageFollowID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("آرشیو داده با شماره شناسایی ".$RemoveRecordID." از گردش پیامهای شخصی");
	}
	
	static function GetAllList()
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select PrivateMessageFollows.PrivateMessageFollowID
				,PrivateMessageFollows.PrivateMessageID
				,PrivateMessageFollows.comment
				,PrivateMessageFollows.ReferTime
				,PrivateMessageFollows.FromPersonID
				,PrivateMessageFollows.ToPersonID
				,PrivateMessageFollows.UpperLevelID
				,PrivateMessageFollows.ReferFileName
				,PrivateMessageFollows.ReferFileContent
				,PrivateMessageFollows.ReferStatus
				,PrivateMessageFollows.ArchiveFolderID
			, concat(g2j(ReferTime), ' ', substr(ReferTime, 12, 10)) as ReferTime_Shamsi 
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName 
			, concat(persons5.pfname, ' ', persons5.plname) as persons5_FullName  from projectmanagement.PrivateMessageFollows 
			LEFT JOIN projectmanagement.persons persons4 on (persons4.PersonID=PrivateMessageFollows.FromPersonID) 
			LEFT JOIN projectmanagement.persons persons5 on (persons5.PersonID=PrivateMessageFollows.ToPersonID)  ";
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_PrivateMessageFollows();
			$ret[$k]->PrivateMessageFollowID=$rec["PrivateMessageFollowID"];
			$ret[$k]->PrivateMessageID=$rec["PrivateMessageID"];
			$ret[$k]->comment=$rec["comment"];
			$ret[$k]->ReferTime=$rec["ReferTime"];
			$ret[$k]->ReferTime_Shamsi=$rec["ReferTime_Shamsi"];  // محاسبه معادل شمسی مربوطه
			$ret[$k]->FromPersonID=$rec["FromPersonID"];
			$ret[$k]->FromPersonID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ToPersonID=$rec["ToPersonID"];
			$ret[$k]->ToPersonID_FullName=$rec["persons5_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->UpperLevelID=$rec["UpperLevelID"];
			$ret[$k]->ReferFileName=$rec["ReferFileName"];
			$ret[$k]->ReferFileContent=$rec["ReferFileContent"];
			$ret[$k]->ReferStatus=$rec["ReferStatus"];
			$ret[$k]->ArchiveFolderID=$rec["ArchiveFolderID"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $comment: یادداشت
	* @param $ReferFileContent: محتوای فایل ضمیمه
	* @param $ReferFileName: نام فایل
	* @param $ReferStatus: وضعیت
	* @param $ArchiveFolderID: پوشه بایگانی
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $comment, $ReferFileContent, $ReferFileName, $ReferStatus, $ArchiveFolderID)
	{
		$ret = "";
		$obj = new be_PrivateMessageFollows();
		$obj->Load($CurRecID);
		if($comment!=$obj->comment)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "یادداشت";
		}
		if($ReferFileContent!=$obj->ReferFileContent)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "محتوای فایل ضمیمه";
		}
		if($ReferStatus!=$obj->ReferStatus)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "وضعیت";
		}
		if($ArchiveFolderID!=$obj->ArchiveFolderID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "پوشه بایگانی";
		}
		return $ret;
	}
}
?>