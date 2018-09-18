<?
class be_files
{
	public $FileID;		//
	public $FileTypeID;		//نوع پرونده
	public $ouid;		//واحد سازمانی
	public $sub_ouid;		//زیر واحد سازمانی
	public $EduGrpCode;		//گروه آموزشی
	public $FileNo;		//شماره پرونده
	public $PersonType;		//نوع فردی که پرونده متعلق به اوست
	public $PersonID;		//کد شخصی فردی که پرونده متعلق به اوست
	public $StNo;		//شماره دانشجویی
	public $PFName;		//نام 
	public $PLName;		//نام خانوادگی
	public $FileTitle;		//عنوان
	public $FileStatus;		//وضعیت
	public $CreatorID;	// ایجاد کننده
	
	public $FileTypeName; // نام نوع پرونده - بدست آورده شده از روی کد نوع پرونده
	public $UnitName;
	public $SubUnitName;
	public $EduGrpName;
	public $RelatedToPerson; // از روی نوع پرونده استخراج می شود

	function be_files() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select f.*, ft.*, p.plname as pplname, p.pfname as ppfname, s.pfname as spfname, s.plname as splname,
										u.ptitle as UnitName, su.ptitle as SubUnitName, eg.PEduName  
								from files as f
								LEFT JOIN FileTypes as ft using (FileTypeID) 
								LEFT JOIN hrms_total.persons as p using (PersonID)
								LEFT JOIN StudentSpecs as s using (StNo)
								LEFT JOIN hrms_total.org_units as u using (ouid)
								LEFT JOIN hrms_total.org_sub_units su using (sub_ouid)
								LEFT JOIN EducationalGroups as eg on (f.EduGrpCode=eg.EduGrpCode)
								 where f.FileID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FileID=$rec["FileID"];
			$this->FileTypeID=$rec["FileTypeID"];
			$this->ouid=$rec["ouid"];
			$this->sub_ouid=$rec["sub_ouid"];
			$this->EduGrpCode=$rec["EduGrpCode"];
			$this->FileNo=$rec["FileNo"];
			$this->PersonType=$rec["PersonType"];
			$this->PersonID=$rec["PersonID"];
			$this->StNo=$rec["StNo"];
			$this->CreatorID=$rec["CreatorID"];
			$this->PFName = $this->PLName = "";
			if($rec["PersonType"]=="OTHER")
			{
				$this->PFName=$rec["PFName"];
				$this->PLName=$rec["PLName"];
			}
			else if($rec["PersonType"]=="PROF" || $rec["PersonType"]=="STAFF")
			{
				$this->PFName=$rec["ppfname"];
				$this->PLName=$rec["pplname"];
			}
			else if($rec["PersonType"]=="STUDENT")
			{
				$this->PFName=$rec["spfname"];
				$this->PLName=$rec["splname"];
			}
			$this->FileTitle=$rec["FileTitle"];
			$this->FileStatus=$rec["FileStatus"];
			$this->FileTypeName=$rec["FileTypeName"];
			$this->UnitName=$rec["UnitName"];
			$this->SubUnitName=$rec["SubUnitName"];
			$this->EduGrpName=$rec["PEduName"];
			$this->RelatedToPerson=$rec["RelatedToPerson"];
		}
	}
	
	function GetDeptedUsersName()
	{
		$ret = "";
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select concat(pfname, ' ', plname) as FullName from formsgenerator.FilesTemporarayAccessList
										JOIN hrms_total.persons on (ReceiverID=PersonID)  
												where FileID=".$this->FileID);
		while($rec = $res->FetchRow())
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= $rec["FullName"];
		}
		return $ret;
	}
	
}
class manage_files
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(FileID) as TotalCount from files';
		if($WhereCondition!="")
		{
			$query .= ' where '.$WhereCondition;
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
		$query = 'select max(FileID) as MaxID from files';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FileTypeID, $ouid, $sub_ouid, $EduGrpCode, $FileNo, $PersonType, $PersonID, $StNo, $PFName, $PLName, $FileTitle, $CreatorID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into files (FileTypeID
				, ouid
				, sub_ouid
				, EduGrpCode
				, FileNo
				, PersonType
				, PersonID
				, StNo
				, PFName
				, PLName
				, FileTitle
				, FileStatus
				, CreatorID
				) values ('".$FileTypeID."'
				, '".$ouid."'
				, '".$sub_ouid."'
				, '".$EduGrpCode."'
				, '".$FileNo."'
				, '".$PersonType."'
				, '".$PersonID."'
				, '".$StNo."'
				, '".$PFName."'
				, '".$PLName."'
				, '".$FileTitle."'
				, 'ENABLE'
				, '".$CreatorID."'
				)";
		$mysql->Execute($query);
		$FileID = manage_files::GetLastID();
		$mysql->audit("ثبت داده جدید در پرونده ها با کد ".$FileID);
		
		$mysql->Execute("insert into FileHistory (FileID, description, ActionTime, PersonID) values ('".$FileID."', 'ایجاد پرونده', now(), '".$_SESSION["PersonID"]."') ");
		require_once("FileTypeForms.class.php");
		require_once("FormsStruct.class.php");
		// اضافه کردن فرمهای الزامی به پرونده در ابتدای ایجاد پرونده
		$list = manage_FileTypeForms::GetList($FileTypeID);
		for($i=0; $i<count($list); $i++)
		{
			if($list[$i]->mandatory=="YES")
			{
				$CurForm = new be_FormsStruct();
				$CurForm->LoadDataFromDatabase($list[$i]->FormsStructID);
				$query = "select max(".$CurForm->KeyFieldName.")+1 from ".$CurForm->RelatedDB.".".$CurForm->RelatedTable;
				$res = $mysql->Execute($query);
				$rec = $res->FetchRow();
				$query = "insert into ".$CurForm->RelatedDB.".".$CurForm->RelatedTable." (".$CurForm->KeyFieldName.") values ('".$rec[0]."')";
				$mysql->Execute($query);
				$query = "insert into FileContents (FileID, ContentType, FormsStructID, FormRecordID) values ('".$FileID."', 'FORM', '".$CurForm->FormsStructID."', '".$rec[0]."')";
				$mysql->Execute($query);
			}
		}
	}
	static function Update($UpdateRecordID, $ouid, $sub_ouid, $EduGrpCode, $FileNo, $PersonType, $PersonID, $StNo, $PFName, $PLName, $FileTitle)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update files set 
				 ouid='".$ouid."'
				, sub_ouid='".$sub_ouid."'
				, EduGrpCode='".$EduGrpCode."'
				, FileNo='".$FileNo."'
				, PersonType='".$PersonType."'
				, PersonID='".$PersonID."'
				, StNo='".$StNo."'
				, PFName='".$PFName."'
				, PLName='".$PLName."'
				, FileTitle='".$FileTitle."'
				where FileID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در پرونده ها");
		$mysql->Execute("insert into FileHistory (FileID, description, ActionTime, PersonID) values ('".$UpdateRecordID."', 'ویرایش مشخصات پرونده', now(), '".$_SESSION["PersonID"]."') ");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update files set FileStatus='DISABLE' where FileID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از پرونده ها");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select f.*, ft.*, p.plname as pplname, p.pfname as ppfname, s.pfname as spfname, s.plname as splname,
								u.ptitle as UnitName, su.ptitle as SubUnitName, eg.PEduName   
								from files as f
								LEFT JOIN FileTypes as ft using (FileTypeID) 
								LEFT JOIN hrms_total.persons as p using (PersonID)
								LEFT JOIN StudentSpecs as s using (StNo)
								LEFT JOIN hrms_total.org_units as u using (ouid)
								LEFT JOIN hrms_total.org_sub_units su using (sub_ouid)
								LEFT JOIN EducationalGroups as eg on (f.EduGrpCode=eg.EduGrpCode)
								";
		$query .= "where FileStatus='ENABLE' and ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_files();
			$ret[$k]->FileID=$rec["FileID"];
			$ret[$k]->FileTypeID=$rec["FileTypeID"];
			$ret[$k]->FileTypeName=$rec["FileTypeName"];
			$ret[$k]->ouid=$rec["ouid"];
			$ret[$k]->sub_ouid=$rec["sub_ouid"];
			$ret[$k]->EduGrpCode=$rec["EduGrpCode"];
			$ret[$k]->FileNo=$rec["FileNo"];
			$ret[$k]->PersonType=$rec["PersonType"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->StNo=$rec["StNo"];
			$ret[$k]->FileTitle=$rec["FileTitle"];
			$ret[$k]->FileStatus=$rec["FileStatus"];
			$ret[$k]->CreatorID=$rec["CreatorID"];
			
			$ret[$k]->UnitName=$rec["UnitName"];
			$ret[$k]->SubUnitName=$rec["SubUnitName"];
			$ret[$k]->EduGrpName=$rec["PEduName"];
			$ret[$k]->RelatedToPerson=$rec["RelatedToPerson"];
			$ret[$k]->PFName = $ret[$k]->PLName = "";
			if($rec["PersonType"]=="OTHER")
			{
				$ret[$k]->PFName=$rec["PFName"];
				$ret[$k]->PLName=$rec["PLName"];
			}
			else if($rec["PersonType"]=="PROF" || $rec["PersonType"]=="STAFF")
			{
				$ret[$k]->PFName=$rec["ppfname"];
				$ret[$k]->PLName=$rec["pplname"];
			}
			else if($rec["PersonType"]=="STUDENT")
			{
				$ret[$k]->PFName=$rec["spfname"];
				$ret[$k]->PLName=$rec["splname"];
			}
			
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from files ";
		$query .= "where FileStatus='ENABLE' and ".$WhereCondition;		
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
	
}
?>
