<?
class be_FileTypeForms
{
	public $FileTypeFormID;		//
	public $FileTypeID;
	public $FormsStructID;		//کلید خارجی به جدول ساختار فرمها
	public $mandatory;		//اجباری/اختیاری بودن فرم
	public $FormTitle;
	
	function be_FileTypeForms() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from FileTypeForms JOIN FormsStruct using (FormsStructID) where FileTypeFormID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->FileTypeFormID=$rec["FileTypeFormID"];
			$this->FormsStructID=$rec["FormsStructID"];
			$this->mandatory=$rec["mandatory"];
			$this->FormTitle=$rec["FormTitle"];
			$this->FileTypeID=$rec["FileTypeID"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FileTypeFormID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کلید خارجی به جدول ساختار فرمها </td><td>".$this->FormsStructID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>اجباری/اختیاری بودن فرم </td><td>".$this->mandatory."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FileTypeForms
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(FileTypeFormID) as TotalCount from FileTypeForms';
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
		$query = 'select max(FileTypeFormID) as MaxID from FileTypeForms';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FileTypeID, $FormsStructID, $mandatory)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into FileTypeForms (FileTypeID, FormsStructID
				, mandatory
				) values ('".$FileTypeID."', '".$FormsStructID."'
				, '".$mandatory."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در فرمهای مجاز برای اضافه شدن در این نوع پرونده با کد ".manage_FileTypeForms::GetLastID());
	}
	static function Update($UpdateRecordID, $mandatory)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update FileTypeForms set FileTypeID='".$FileTypeID."', mandatory='".$mandatory."'
				where FileTypeFormID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در فرمهای مجاز برای اضافه شدن در این نوع پرونده");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from FileTypeForms where FileTypeFormID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از فرمهای مجاز برای اضافه شدن در این نوع پرونده");
	}
	static function GetList($FileTypeID)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileTypeForms  JOIN FormsStruct using (FormsStructID) where FileTypeID='".$FileTypeID."' ";
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FileTypeForms();
			$ret[$k]->FileTypeFormID=$rec["FileTypeFormID"];
			$ret[$k]->FormsStructID=$rec["FormsStructID"];
			$ret[$k]->mandatory=$rec["mandatory"];
			$ret[$k]->FormTitle=$rec["FormTitle"];
			$ret[$k]->FileTypeID=$rec["FileTypeID"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileTypeForms ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
