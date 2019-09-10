<?php
/*
 تعریف کلاسها و متدهای مربوط به : دستور کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-1
*/

/*
کلاس پایه: دستور کار
*/
require_once("SessionHistory.class.php");
class be_SessionPreCommands
{
	public $SessionPreCommandID;		//
	public $UniversitySessionID;		//کد جلسه
	public $OrderNo;		//ردیف
	public $description;		//شرح
	public $ResponsiblePersonID;		//کد مسوول پیگیری
	public $ResponsiblePersonID_FullName;		/* نام و نام خانوادگی مربوط به کد مسوول پیگیری */
	public $RepeatInNextSession;		//در دستور کاری بعدی تکرار شود؟
	public $RepeatInNextSession_Desc;		/* شرح مربوط به در دستور کاری بعدی تکرار شود؟ */
	public $RelatedFile;		//فایل ضمیمه
	public $RelatedFileName;		//نام فایل ضمیمه
	public $DeadLine;
	public $DeadLine_Shamsi;
	public $priority;
	public $priority_Desc;		
	public $CreatorPersonID;		//ایجاد کننده
	public $CreatorPersonID_FullName;		/* نام و نام خانوادگی مربوط به ایجاد کننده */

	function be_SessionPreCommands() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SessionPreCommands.* 
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName 
			, CASE SessionPreCommands.RepeatInNextSession 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as RepeatInNextSession_Desc , CASE SessionPreCommands.priority
				WHEN '1' THEN 'بالا' 
				WHEN '2' THEN 'متوسط' 
				WHEN '3' THEN 'پایین' 
				END as priority_Desc, g2j(DeadLine) as DeadLine_Shamsi,SessionPreCommands.priority 
			, concat(persons8.pfname, ' ', persons8.plname) as persons8_FullName,ActRegister.*
			 from sessionmanagement.SessionPreCommands 
			LEFT JOIN hrmstotal.persons persons4 on (persons4.PersonID=SessionPreCommands.ResponsiblePersonID) 
			LEFT JOIN hrmstotal.persons persons8 on (persons8.PersonID=SessionPreCommands.CreatorPersonID)
			LEFT JOIN sessionmanagement.ActRegister on (SessionPreCommands.SessionPreCommandID=ActRegister.SessionPreCommandID) 
		 where  SessionPreCommands.SessionPreCommandID=? order by RowID Desc limit 1";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->SessionPreCommandID=$rec["SessionPreCommandID"];
			$this->UniversitySessionID=$rec["UniversitySessionID"];
			$this->OrderNo=$rec["OrderNo"];
			$this->description=$rec["description"];
			$this->ResponsiblePersonID=$rec["ResponsiblePersonID"];
			$this->ResponsiblePersonID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
			$this->RepeatInNextSession=$rec["RepeatInNextSession"];
			$this->RepeatInNextSession_Desc=$rec["RepeatInNextSession_Desc"];  // محاسبه بر اساس لیست ثابت
			$this->RelatedFile=$rec["RelatedFile"];
			$this->RelatedFileName=$rec["RelatedFileName"];
			$this->CreatorPersonID=$rec["CreatorPersonID"];
			$this->CreatorPersonID_FullName=$rec["persons8_FullName"]; // محاسبه از روی جدول وابسته
			$this->ActReg=$rec["ActReg"];
			$this->DeadLine_Shamsi=$rec["DeadLine_Shamsi"];
			$this->DeadLine=$rec["DeadLine"];
			$this->priority_Desc =$rec["priority_Desc"];
			$this->priority =$rec["priority"];

		}
	}
}
/*
کلاس مدیریت دستور کار
*/

class manage_SessionPreCommands
{
	static function GetCount($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(SessionPreCommandID) as TotalCount from sessionmanagement.SessionPreCommands";
			$query .= " where UniversitySessionID='".$UniversitySessionID."'";
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
		$query = "select max(SessionPreCommandID) as MaxID from sessionmanagement.SessionPreCommands";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $UniversitySessionID: کد جلسه
	* @param $OrderNo: ردیف
	* @param $description: شرح
	* @param $ResponsiblePersonID: کد مسوول پیگیری
	* @param $RepeatInNextSession: در دستور کاری بعدی تکرار شود؟
	* @param $RelatedFile: فایل ضمیمه
	* @param $RelatedFileName: نام فایل
	* @return کد داده اضافه شده	*/
	static function Add($UniversitySessionID, $OrderNo, $description, $ResponsiblePersonID, $RepeatInNextSession, $RelatedFile, $RelatedFileName 
	,$DeadLine ,$priority)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.SessionPreCommands (";
		$query .= " UniversitySessionID";
		$query .= ", OrderNo";
		$query .= ", description";
		$query .= ", ResponsiblePersonID";
		$query .= ", RepeatInNextSession";
		$query .= ", RelatedFile";
		$query .= ", RelatedFileName";
		$query .= ", DeadLine";
		$query .= ", priority";
		$query .= ", CreatorPersonID";
		$query .= ") values (";
		$query .= "? , ? , ? , ? , ? , '".$RelatedFile."', ? , ? ,? ,?";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $UniversitySessionID); 
		array_push($ValueListArray, $OrderNo); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $ResponsiblePersonID); 
		array_push($ValueListArray, $RepeatInNextSession); 
		array_push($ValueListArray, $RelatedFileName);
		array_push($ValueListArray, $DeadLine); 
		array_push($ValueListArray, $priority); 
		array_push($ValueListArray, $_SESSION["PersonID"]); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);

/*if($_SESSION["UserID"]=='gholami-a'){
echo $query;//die();
}*/

		$LastID = manage_SessionPreCommands::GetLastID();
		$mysql->audit("ثبت داده جدید در دستور کار با کد ".$LastID);
		manage_SessionHistory::Add($UniversitySessionID, $LastID, "PRECOMMAND", "", "ADD");
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $OrderNo: ردیف
	* @param $description: شرح
	* @param $ResponsiblePersonID: کد مسوول پیگیری
	* @param $RepeatInNextSession: در دستور کاری بعدی تکرار شود؟
	* @param $RelatedFile: فایل ضمیمه
	* @param $RelatedFileName: نام فایل
	* @return 	*/
	static function Update($UpdateRecordID, $OrderNo, $description, $ResponsiblePersonID, $RepeatInNextSession, $RelatedFile, $RelatedFileName 	
	,$DeadLine ,$priority)
	{
		$k=0;
		$obj = new be_SessionPreCommands();
		$obj->LoadDataFromDatabase($UpdateRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		$mysql = pdodb::getInstance();
		$query = "update sessionmanagement.SessionPreCommands set ";
			$query .= " OrderNo=? ";
			$query .= ", description=? ";
			$query .= ", ResponsiblePersonID=? ";
			$query .= ", RepeatInNextSession=? ";
		if($RelatedFileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", RelatedFileName=?, RelatedFile='".$RelatedFile."' ";
		}
			$query .= ", DeadLine=? ";
			$query .= ", priority=? ";
		$query .= " where SessionPreCommandID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $OrderNo); 
		array_push($ValueListArray, $description); 
		array_push($ValueListArray, $ResponsiblePersonID); 
		array_push($ValueListArray, $RepeatInNextSession); 
		if($RelatedFileName!="")
		{ 
			array_push($ValueListArray, $RelatedFileName); 
		}
		array_push($ValueListArray, $DeadLine); 
		array_push($ValueListArray, $priority);  
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در دستور کار");
		manage_SessionHistory::Add($UniversitySessionID, $UpdateRecordID, "PRECOMMAND", "", "EDIT");
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$obj = new be_SessionPreCommands();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		$UniversitySessionID = $obj->UniversitySessionID;
		$mysql = pdodb::getInstance();
		$query = "delete from sessionmanagement.SessionPreCommands where SessionPreCommandID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از دستور کار");
		manage_SessionHistory::Add($UniversitySessionID, $RemoveRecordID, "PRECOMMAND", "", "REMOVE");
		manage_SessionPreCommands::ReOrderItems($UniversitySessionID);
	}
	static function GetList($UniversitySessionID, $OrderBy="SessionPreCommands.OrderNo")
	{
		if($OrderBy!="OrderNo" &&  $OrderBy!="persons4.plname,OrderNo" && $OrderBy!="priority,OrderNo" && $OrderBy!="DeadLine,OrderNo")
			$OrderBy="SessionPreCommands.OrderNo ";
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SessionPreCommands.SessionPreCommandID
				,SessionPreCommands.UniversitySessionID
				,SessionPreCommands.OrderNo
				,SessionPreCommands.description
				,SessionPreCommands.ResponsiblePersonID
				,SessionPreCommands.RepeatInNextSession
				,SessionPreCommands.RelatedFileName
				,SessionPreCommands.CreatorPersonID
				,SessionPreCommands.DeadLine
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName 
			, CASE SessionPreCommands.RepeatInNextSession 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as RepeatInNextSession_Desc, CASE SessionPreCommands.priority
				WHEN '1' THEN 'بالا' 
				WHEN '2' THEN 'متوسط' 
				WHEN '3' THEN 'پایین' 
				END as priority_Desc,g2j(DeadLine) as DeadLine_Shamsi,SessionPreCommands.priority  
			, concat(persons8.pfname, ' ', persons8.plname) as persons8_FullName,d.ActReg,s.SessionTypeID
 from sessionmanagement.SessionPreCommands 
			LEFT JOIN hrmstotal.persons persons4 on (persons4.PersonID=SessionPreCommands.ResponsiblePersonID) 
			LEFT JOIN hrmstotal.persons persons8 on (persons8.PersonID=SessionPreCommands.CreatorPersonID) 
LEFT JOIN
    sessionmanagement.UniversitySessions ON (UniversitySessions.UniversitySessionID = SessionPreCommands.UniversitySessionID)

      
left join sessionmanagement.SessionTypes s on (UniversitySessions.SessionTypeID=s.SessionTypeID )

			 LEFT JOIN 
			(select RowID,SessionPreCommandID,ActReg from sessionmanagement.ActRegister 
			order by RowID DESC  ) as d
			on (SessionPreCommands.SessionPreCommandID=d.SessionPreCommandID) 
		 ";
		$query .= " where SessionPreCommands.UniversitySessionID=? ";
		$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $UniversitySessionID);
		if($ppc->GetPermission("View_SessionPreCommands")=="PRIVATE")
				$query .= " and SessionPreCommands.CreatorPersonID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_SessionPreCommands")=="NONE")
				$query .= " and 0=1 ";
				
		$query .= " group by SessionPreCommands.SessionPreCommandID ";
		$query .= " order by ".$OrderBy;
		$mysql->Prepare($query);
/*if($_SESSION["UserID"]=='gholami-a'){
echo $query;
}*/
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		
		//if($_SESSION["PersonID"]=="201309")
		//	echo "<font color=red>".$OrderBy."</b>";		
		
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionPreCommands();
			$ret[$k]->SessionPreCommandID=$rec["SessionPreCommandID"];
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->OrderNo=$rec["OrderNo"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->ResponsiblePersonID=$rec["ResponsiblePersonID"];
			$ret[$k]->ResponsiblePersonID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->RepeatInNextSession=$rec["RepeatInNextSession"];
			$ret[$k]->RepeatInNextSession_Desc=$rec["RepeatInNextSession_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->RelatedFileName=$rec["RelatedFileName"];
			$ret[$k]->CreatorPersonID=$rec["CreatorPersonID"];
			$ret[$k]->CreatorPersonID_FullName=$rec["persons8_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActReg=$rec["ActReg"];
			$ret[$k]->DeadLine_Shamsi=$rec["DeadLine_Shamsi"];
			$ret[$k]->priority_Desc =$rec["priority_Desc"];
			$ret[$k]->priority =$rec["priority"];
			$ret[$k]->DeadLine=$rec["DeadLine"];
			$ret[$k]->SessionTypeID=$rec["SessionTypeID"];
			$k++;
		}
		return $ret;
	}
	
	// آخرین شماره ردیف دستور کار جلسه را بر می گرداند
	static function GetMaxOrderNo($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select max(OrderNo) as MaxNo from sessionmanagement.SessionPreCommands where  UniversitySessionID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		$rec = $res->fetch();
		if($rec["MaxNo"]=="")
			return 0;
		return $rec["MaxNo"];
	}
	
		// شماره ردیف را مرتب سازی دوباره می کند
	function ReOrderItems($UniversitySessionID)
	{
		$mysql = pdodb::getInstance();
		$query = "select *  from sessionmanagement.SessionPreCommands where  UniversitySessionID=? order by OrderNo";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($UniversitySessionID));
		$i = 1;
		while($rec = $res->fetch())
		{
			$mysql->Execute("update sessionmanagement.SessionPreCommands set OrderNo=".$i." where SessionPreCommandID=".$rec["SessionPreCommandID"]);
			$i++;
		}
	}


	static function Getli($OrderNo,$session,$type,$OrderBy="SessionPreCommands.OrderNo")
	{
		
               
		if($OrderBy!="OrderNo" &&  $OrderBy!="persons4.plname,OrderNo")
			$OrderBy="SessionPreCommands.OrderNo";
                $mysql = pdodb::getInstance();

		$k=0;
		$ret = array();
		$query = "select SessionPreCommands.SessionPreCommandID
				,SessionPreCommands.UniversitySessionID
				,SessionPreCommands.OrderNo
				,SessionPreCommands.description
				,SessionPreCommands.ResponsiblePersonID
				,SessionPreCommands.RepeatInNextSession
				,SessionPreCommands.RelatedFileName
				,SessionPreCommands.CreatorPersonID
			, concat(persons4.pfname, ' ', persons4.plname) as persons4_FullName 
			, CASE SessionPreCommands.RepeatInNextSession 
				WHEN 'YES' THEN 'بلی' 
				WHEN 'NO' THEN 'خیر' 
				END as RepeatInNextSession_Desc, CASE SessionPreCommands.priority
				WHEN '1' THEN 'بالا' 
				WHEN '2' THEN 'متوسط' 
				WHEN '3' THEN 'پایین' 
				END as priority_Desc,g2j(SessionPreCommands.DeadLine) as DeadLine_Shamsi  
			, concat(persons8.pfname, ' ', persons8.plname) as persons8_FullName,ActReg,SessionTitle,UniversitySessions.SessionDate,
                         SessionDecisions.description,s.SessionTypeID
                       
			 from sessionmanagement.SessionPreCommands 

			LEFT JOIN hrmstotal.persons persons4 on (persons4.PersonID=SessionPreCommands.ResponsiblePersonID) 
			LEFT JOIN hrmstotal.persons persons8 on (persons8.PersonID=SessionPreCommands.CreatorPersonID) 
			LEFT JOIN sessionmanagement.UniversitySessions on (UniversitySessions.UniversitySessionID=SessionPreCommands.UniversitySessionID)
                        LEFT JOIN sessionmanagement.SessionDecisions ON (UniversitySessions.UniversitySessionID = SessionDecisions.UniversitySessionID
                        and SessionPreCommands.SessionPreCommandID=SessionDecisions.SessionPreCommandID)
                        left join sessionmanagement.SessionTypes s on (UniversitySessions.SessionTypeID=s.SessionTypeID )

			LEFT JOIN sessionmanagement.ActRegister d on (SessionPreCommands.SessionPreCommandID=d.SessionPreCommandID) 
		";
		$query .= " where   SessionPreCommands.OrderNo=? and UniversitySessions.UniversitySessionID>=860 and UniversitySessions.UniversitySessionID<=? 
                                   and s.SessionTypeID=? ";
		//$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"] ,$OrderNo ,$session,$type);
		/*if($ppc->GetPermission("View_SessionPreCommands")=="PRIVATE")
				$query .= " and SessionPreCommands.CreatorPersonID='".$_SESSION["PersonID"]." ";
		else if($ppc->GetPermission("View_SessionPreCommands")=="NONE")
				$query .= " and 0=1 ";*/
				
		$query .= " group by SessionPreCommands.SessionPreCommandID ";
		$query .= " order by UniversitySessions.SessionDate";
		$mysql->Prepare($query);
		/*if($_SESSION["UserID"]=='gholami-a'){
		echo $query;
		echo "<br>";
		echo $session;
		echo "<br>";
		echo $type;
                echo "<br>";
		echo $OrderNo;
		}*/
		$res = $mysql->ExecuteStatement(array($OrderNo,$session,$type));
		
		/*if($_SESSION["PersonID"]=="401366873")
			echo "<font color=red>".$OrderBy."</b>";*/		
		
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SessionPreCommands();
			$ret[$k]->SessionPreCommandID=$rec["SessionPreCommandID"];
			$ret[$k]->UniversitySessionID=$rec["UniversitySessionID"];
			$ret[$k]->OrderNo=$rec["OrderNo"];
			$ret[$k]->description=$rec["description"];
			$ret[$k]->ResponsiblePersonID=$rec["ResponsiblePersonID"];
			$ret[$k]->ResponsiblePersonID_FullName=$rec["persons4_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->RepeatInNextSession=$rec["RepeatInNextSession"];
			$ret[$k]->RepeatInNextSession_Desc=$rec["RepeatInNextSession_Desc"];  // محاسبه بر اساس لیست ثابت
			$ret[$k]->RelatedFileName=$rec["RelatedFileName"];
			$ret[$k]->CreatorPersonID=$rec["CreatorPersonID"];
			$ret[$k]->CreatorPersonID_FullName=$rec["persons8_FullName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->ActReg=$rec["ActReg"];
			$ret[$k]->SessionTitle=$rec["SessionTitle"];
			$ret[$k]->SessionDate=$rec["SessionDate"];
			$ret[$k]->DeadLine_Shamsi=$rec["DeadLine_Shamsi"];
			$ret[$k]->priority_Desc =$rec["priority_Desc"];
			//$ret[$k]->priority =$rec["priority"];
			$ret[$k]->description =$rec["description"];
/*$ret[$k]->SessionTypeID =$rec["SessionTypeID"];*/
												
			$k++;
		}
		return $ret;
	}





	
}
?>
