<?
class be_FileTypes
{
	public $FileTypeID;		//
	public $FileTypeName;		//نام
	public $UserCanChangeLocation;		//آیا کاربر مجاز به تعیین دستی محل پرونده (واحد سازمانی - زیر واحد سازمانی - گروه آموزشی) می باشد
	public $SetLocationType;		//محل پرونده (واحد - زیر واحد - گروه آموزشی) بر چه اساس پر شود
	public $RelatedPersonCanBeProffessor;		//فرد مربوط به پرونده می تواند استاد باشد
	public $RelatedPersonCanBeStaff;		//فرد مربوط به پرونده می تواند کارمند باشد
	public $RelatedPersonCanBeStudent;		//فرد مربوط به پرونده می تواند دانشجو باشد
	public $RelatedPersonCanBeOther;		//فرد مربوط به پرونده می تواند فرد متفرقه باشد
	public $RelatedToPerson;		//پرونده مربوط به یک شخص می باشد؟

	function be_FileTypes() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from FileTypes where FileTypeID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FileTypeID=$rec["FileTypeID"];
			$this->FileTypeName=$rec["FileTypeName"];
			$this->UserCanChangeLocation=$rec["UserCanChangeLocation"];
			$this->SetLocationType=$rec["SetLocationType"];
			$this->RelatedPersonCanBeProffessor=$rec["RelatedPersonCanBeProffessor"];
			$this->RelatedPersonCanBeStaff=$rec["RelatedPersonCanBeStaff"];
			$this->RelatedPersonCanBeStudent=$rec["RelatedPersonCanBeStudent"];
			$this->RelatedPersonCanBeOther=$rec["RelatedPersonCanBeOther"];
			$this->RelatedToPerson=$rec["RelatedToPerson"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FileTypeID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>نام </td><td>".$this->FileTypeName."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>آیا کاربر مجاز به تعیین دستی محل پرونده (واحد سازمانی - زیر واحد سازمانی - گروه آموزشی) می باشد </td><td>".$this->UserCanChangeLocation."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>محل پرونده (واحد - زیر واحد - گروه آموزشی) بر چه اساس پر شود </td><td>".$this->SetLocationType."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>فرد مربوط به پرونده می تواند استاد باشد </td><td>".$this->RelatedPersonCanBeProffessor."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>فرد مربوط به پرونده می تواند کارمند باشد </td><td>".$this->RelatedPersonCanBeStaff."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>فرد مربوط به پرونده می تواند دانشجو باشد </td><td>".$this->RelatedPersonCanBeStudent."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>فرد مربوط به پرونده می تواند فرد متفرقه باشد </td><td>".$this->RelatedPersonCanBeOther."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>پرونده مربوط به یک شخص می باشد؟ </td><td>".$this->RelatedToPerson."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FileTypes
{
	// تعداد پرونده هایی از یک نوع را بر می گرداند
	static function GetRelatedFileCount($FileTypeID)
	{
		$mysql = dbclass::getInstance();
		$query = "select count(FileTypeID) as TotalCount from files where FileTypeID='".$FileTypeID."'";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["TotalCount"];
		}
		return 0;
		
	}
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(FileTypeID) as TotalCount from FileTypes';
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
		$query = 'select max(FileTypeID) as MaxID from FileTypes';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FileTypeName, $UserCanChangeLocation, $SetLocationType, $RelatedPersonCanBeProffessor, $RelatedPersonCanBeStaff, $RelatedPersonCanBeStudent, $RelatedPersonCanBeOther, $RelatedToPerson)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into FileTypes (FileTypeName
				, UserCanChangeLocation
				, SetLocationType
				, RelatedPersonCanBeProffessor
				, RelatedPersonCanBeStaff
				, RelatedPersonCanBeStudent
				, RelatedPersonCanBeOther
				, RelatedToPerson
				) values ('".$FileTypeName."'
				, '".$UserCanChangeLocation."'
				, '".$SetLocationType."'
				, '".$RelatedPersonCanBeProffessor."'
				, '".$RelatedPersonCanBeStaff."'
				, '".$RelatedPersonCanBeStudent."'
				, '".$RelatedPersonCanBeOther."'
				, '".$RelatedToPerson."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در انواع پرونده با کد ".manage_FileTypes::GetLastID());
	}
	static function Update($UpdateRecordID, $FileTypeName, $UserCanChangeLocation, $SetLocationType, $RelatedPersonCanBeProffessor, $RelatedPersonCanBeStaff, $RelatedPersonCanBeStudent, $RelatedPersonCanBeOther, $RelatedToPerson)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update FileTypes set FileTypeName='".$FileTypeName."'
				, UserCanChangeLocation='".$UserCanChangeLocation."'
				, SetLocationType='".$SetLocationType."'
				, RelatedPersonCanBeProffessor='".$RelatedPersonCanBeProffessor."'
				, RelatedPersonCanBeStaff='".$RelatedPersonCanBeStaff."'
				, RelatedPersonCanBeStudent='".$RelatedPersonCanBeStudent."'
				, RelatedPersonCanBeOther='".$RelatedPersonCanBeOther."'
				, RelatedToPerson='".$RelatedToPerson."'
				where FileTypeID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در انواع پرونده");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from FileTypes where FileTypeID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از انواع پرونده");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileTypes ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FileTypes();
			$ret[$k]->FileTypeID=$rec["FileTypeID"];
			$ret[$k]->FileTypeName=$rec["FileTypeName"];
			$ret[$k]->UserCanChangeLocation=$rec["UserCanChangeLocation"];
			$ret[$k]->SetLocationType=$rec["SetLocationType"];
			$ret[$k]->RelatedPersonCanBeProffessor=$rec["RelatedPersonCanBeProffessor"];
			$ret[$k]->RelatedPersonCanBeStaff=$rec["RelatedPersonCanBeStaff"];
			$ret[$k]->RelatedPersonCanBeStudent=$rec["RelatedPersonCanBeStudent"];
			$ret[$k]->RelatedPersonCanBeOther=$rec["RelatedPersonCanBeOther"];
			$ret[$k]->RelatedToPerson=$rec["RelatedToPerson"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileTypes ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
