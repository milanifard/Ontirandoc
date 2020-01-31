<?
class be_StepPermittedUnits
{
	public $StepPermittedUnitsID;		//
	public $FormFlowStepID;		//کد مرحله
	public $UnitID;		//کد واحد سازمانی
	
	public $UnitName; // نام واحد سازمانی استخراج شده از روی کد واحد
	
	function be_StepPermittedUnits() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from StepPermittedUnits LEFT JOIN hrms_total.org_units on (UnitID=ouid) where StepPermittedUnitsID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			foreach($rec as $key => $value){
				$this->$$key = $value;
			}
			$this->UnitName=$ptitle;
		}
	}
	function ShowInfo()
	{
		echo "<table width='80%' align='center' border='1px' cellsapcing='0'>";
		echo "<tr>";
		echo "<td>".C_IDENTIFICATION_CODE."</td><td>".$this->StepPermittedUnitsID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>".C_STEP_CODE."</td><td>".$this->FormFlowStepID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>".C_DEPARTMENT_CODE."</td><td>".$this->UnitID."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_StepPermittedUnits
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(StepPermittedUnitsID) as TotalCount from StepPermittedUnits';
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
		$query = 'select max(StepPermittedUnitsID) as MaxID from StepPermittedUnits';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormFlowStepID, $UnitID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into StepPermittedUnits (FormFlowStepID
				, UnitID
				) values ('".$FormFlowStepID."'
				, '".$UnitID."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در واحدهای مجاز به دسترسی به یک مرحله با کد ".manage_StepPermittedUnits::GetLastID());
	}
	static function Update($UpdateRecordID, $UnitID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update StepPermittedUnits set UnitID='".$UnitID."'
				where StepPermittedUnitsID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در واحدهای مجاز به دسترسی به یک مرحله");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from StepPermittedUnits where StepPermittedUnitsID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از واحدهای مجاز به دسترسی به یک مرحله");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from StepPermittedUnits  LEFT JOIN hrms_total.org_units on (UnitID=ouid) ";
		if($WhereCondition!="") 
			$query .= " where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_StepPermittedUnits();
			$ret[$k]->StepPermittedUnitsID=$rec["StepPermittedUnitsID"];
			$ret[$k]->FormFlowStepID=$rec["FormFlowStepID"];
			$ret[$k]->UnitID=$rec["UnitID"];
			$ret[$k]->UnitName=$rec["ptitle"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from StepPermittedUnits  LEFT JOIN hrms_total.org_units on (UnitID=ouid) ";
		if(!empty($WhereCondition)) 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
