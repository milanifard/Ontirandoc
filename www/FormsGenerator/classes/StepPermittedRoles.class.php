<?
class be_StepPermittedRoles
{
	public $StepPermittedRolesID;		//
	public $FormFlowStepID;		//کد مرحله
	public $RoleID;		//کد نقش
	public $SysCode;		//کد سیستم

	public $RoleName; // عنوان نقش استخراج شده از روی کد نقش 
	public $SystemTitle; // عنوان سیستم استخراج شده از روی کد سیستم
	
	function be_StepPermittedRoles() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from StepPermittedRoles
										LEFT JOIN systems using (SysCode)
										LEFT JOIN roles using (RoleID) 
										where StepPermittedRolesID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->StepPermittedRolesID=$rec["StepPermittedRolesID"];
			$this->FormFlowStepID=$rec["FormFlowStepID"];
			$this->RoleID=$rec["RoleID"];
			$this->SysCode=$rec["SysCode"];
			$this->SystemTitle=$rec["description"];
			$this->RoleName=$rec["RoleName"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->StepPermittedRolesID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد مرحله </td><td>".$this->FormFlowStepID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد نقش </td><td>".$this->RoleID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد سیستم </td><td>".$this->SysCode."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_StepPermittedRoles
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(StepPermittedRolesID) as TotalCount from StepPermittedRoles';
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
		$query = 'select max(StepPermittedRolesID) as MaxID from StepPermittedRoles';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormFlowStepID, $RoleID, $SysCode)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into StepPermittedRoles (FormFlowStepID
				, RoleID
				, SysCode
				) values ('".$FormFlowStepID."'
				, '".$RoleID."'
				, '".$SysCode."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در نقشهای مجاز به دسترسی به یک مرحله با کد ".manage_StepPermittedRoles::GetLastID());
	}
	static function Update($UpdateRecordID, $RoleID, $SysCode)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update StepPermittedRoles set RoleID='".$RoleID."'
				, SysCode='".$SysCode."'
				where StepPermittedRolesID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در نقشهای مجاز به دسترسی به یک مرحله");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from StepPermittedRoles where StepPermittedRolesID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از نقشهای مجاز به دسترسی به یک مرحله");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from StepPermittedRoles LEFT JOIN systems using (SysCode)
										LEFT JOIN roles using (RoleID) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_StepPermittedRoles();
			$ret[$k]->StepPermittedRolesID=$rec["StepPermittedRolesID"];
			$ret[$k]->FormFlowStepID=$rec["FormFlowStepID"];
			$ret[$k]->RoleID=$rec["RoleID"];
			$ret[$k]->SysCode=$rec["SysCode"];
			$ret[$k]->RoleName=$rec["RoleName"];
			$ret[$k]->SystemTitle=$rec["description"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from StepPermittedRoles ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
