<?
class be_StepPermittedSubUnits
{
	public $StepPermittedSubUnitsID;		//
	public $FormFlowStepID;		//کد مرحله
	public $UnitID;		//کد واحد سازمانی
	public $SubUnitID;		//کد زیر واحد سازمانی
	
	public $UnitName; // نام واحد سازمانی مستخرج از کد آن
	public $SubUnitName; // نام زیر واحد سازمانی مستخرج از کد آن

	function be_StepPermittedSubUnits() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select *, org_units.ptitle as UnitName, org_sub_units.ptitle as SubUnitName from StepPermittedSubUnits
								LEFT JOIN hrms_total.org_units on (UnitID=org_units.ouid)
								LEFT JOIN hrms_total.org_sub_units on (UnitID=org_sub_units.ouid and SubUnitID=org_sub_units.sub_ouid) 
								where StepPermittedSubUnitsID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->StepPermittedSubUnitsID=$rec["StepPermittedSubUnitsID"];
			$this->FormFlowStepID=$rec["FormFlowStepID"];
			$this->UnitID=$rec["UnitID"];
			$this->SubUnitID=$rec["SubUnitID"];
			$this->UnitName=$rec["UnitName"];
			$this->SubUnitName=$rec["SubUnitName"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->StepPermittedSubUnitsID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد مرحله </td><td>".$this->FormFlowStepID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد واحد سازمانی </td><td>".$this->UnitID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد زیر واحد سازمانی </td><td>".$this->SubUnitID."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_StepPermittedSubUnits
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(StepPermittedSubUnitsID) as TotalCount from StepPermittedSubUnits';
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
		$query = 'select max(StepPermittedSubUnitsID) as MaxID from StepPermittedSubUnits';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormFlowStepID, $UnitID, $SubUnitID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into StepPermittedSubUnits (FormFlowStepID
				, UnitID
				, SubUnitID
				) values ('".$FormFlowStepID."'
				, '".$UnitID."'
				, '".$SubUnitID."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در زیر واحدهای مجاز به دسترسی به یک مرحله با کد ".manage_StepPermittedSubUnits::GetLastID());
	}
	static function Update($UpdateRecordID, $UnitID, $SubUnitID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update StepPermittedSubUnits set UnitID='".$UnitID."'
				, SubUnitID='".$SubUnitID."'
				where StepPermittedSubUnitsID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در زیر واحدهای مجاز به دسترسی به یک مرحله");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from StepPermittedSubUnits where StepPermittedSubUnitsID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از زیر واحدهای مجاز به دسترسی به یک مرحله");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select *, org_units.ptitle as UnitName, org_sub_units.ptitle as SubUnitName from StepPermittedSubUnits LEFT JOIN hrms_total.org_units on (UnitID=org_units.ouid)
								LEFT JOIN hrms_total.org_sub_units on (UnitID=org_sub_units.ouid and SubUnitID=org_sub_units.sub_ouid) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_StepPermittedSubUnits();
			$ret[$k]->StepPermittedSubUnitsID=$rec["StepPermittedSubUnitsID"];
			$ret[$k]->FormFlowStepID=$rec["FormFlowStepID"];
			$ret[$k]->UnitID=$rec["UnitID"];
			$ret[$k]->SubUnitID=$rec["SubUnitID"];
			$ret[$k]->UnitName=$rec["UnitName"];
			$ret[$k]->SubUnitName=$rec["SubUnitName"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from StepPermittedSubUnits LEFT JOIN hrms_total.org_units on (UnitID=org_units.ouid)
								LEFT JOIN hrms_total.org_sub_units on (UnitID=org_sub_units.ouid and SubUnitID=org_sub_units.sub_ouid) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
