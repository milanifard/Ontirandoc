<?
class be_StepPermittedPersons
{
	public $StepPermittedPersonsID;		//
	public $FormFlowStepID;		//کد مرحله
	public $PersonID;		//

	public $PersonName; // نام فرد استخراج شده از کد شخص 
	
	function be_StepPermittedPersons() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from StepPermittedPersons
								LEFT JOIN hrms_total.persons using (PersonID) 
								where StepPermittedPersonsID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->StepPermittedPersonsID=$rec["StepPermittedPersonsID"];
			$this->FormFlowStepID=$rec["FormFlowStepID"];
			$this->PersonID=$rec["PersonID"];
			$this->PersonName=$rec["pfname"]." ".$rec["plname"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->StepPermittedPersonsID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد مرحله </td><td>".$this->FormFlowStepID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td> کد شخص</td><td>".$this->PersonID."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_StepPermittedPersons
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(StepPermittedPersonsID) as TotalCount from StepPermittedPersons';
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
		$query = 'select max(StepPermittedPersonsID) as MaxID from StepPermittedPersons';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormFlowStepID, $PersonID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into StepPermittedPersons (FormFlowStepID
				, PersonID
				) values ('".$FormFlowStepID."'
				, '".$PersonID."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در افراد مجاز به دسترسی به یک مرحله با کد ".manage_StepPermittedPersons::GetLastID());
	}
	static function Update($UpdateRecordID, $PersonID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update StepPermittedPersons set PersonID='".$PersonID."'
				where StepPermittedPersonsID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در افراد مجاز به دسترسی به یک مرحله");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from StepPermittedPersons where StepPermittedPersonsID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از افراد مجاز به دسترسی به یک مرحله");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from StepPermittedPersons LEFT JOIN hrms_total.persons using (PersonID) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_StepPermittedPersons();
			$ret[$k]->StepPermittedPersonsID=$rec["StepPermittedPersonsID"];
			$ret[$k]->FormFlowStepID=$rec["FormFlowStepID"];
			$ret[$k]->PersonID=$rec["PersonID"];
			$ret[$k]->PersonName=$rec["pfname"]." ".$rec["plname"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from StepPermittedPersons LEFT JOIN hrms_total.persons using (PersonID)";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
