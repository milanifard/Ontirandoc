<?
class be_StepPermittedEduGroups
{
	public $StepPermittedEduGroupsID;		//
	public $FormFlowStepID;		//کد مرحله
	public $EduGrpCode;		//کد گروه آموزشی
	public $EduGrpName; // نام گروه آموزشی مستخرج از کد آن

	function be_StepPermittedEduGroups() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from StepPermittedEduGroups
										LEFT JOIN EducationalGroups using (EduGrpCode) 
										where StepPermittedEduGroupsID='".$RecID."' ");
		if($rec=$res->FetchRow())
		{
			$this->StepPermittedEduGroupsID=$rec["StepPermittedEduGroupsID"];
			$this->FormFlowStepID=$rec["FormFlowStepID"];
			$this->EduGrpCode=$rec["EduGrpCode"];
			$this->EduGrpName=$rec["PEduName"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->StepPermittedEduGroupsID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد مرحله </td><td>".$this->FormFlowStepID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد گروه آموزشی </td><td>".$this->EduGrpCode."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_StepPermittedEduGroups
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(StepPermittedEduGroupsID) as TotalCount from StepPermittedEduGroups';
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
		$query = 'select max(StepPermittedEduGroupsID) as MaxID from StepPermittedEduGroups';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormFlowStepID, $EduGrpCode)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into StepPermittedEduGroups (FormFlowStepID
				, EduGrpCode
				) values ('".$FormFlowStepID."'
				, '".$EduGrpCode."'
				)";
		$mysql->Execute($query);
		$mysql->audit("ثبت داده جدید در گروه های آموزشی مجاز به دسترسی به یک مرحله با کد ".manage_StepPermittedEduGroups::GetLastID());
	}
	static function Update($UpdateRecordID, $EduGrpCode)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update StepPermittedEduGroups set EduGrpCode='".$EduGrpCode."'
				where StepPermittedEduGroupsID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در گروه های آموزشی مجاز به دسترسی به یک مرحله");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "delete from StepPermittedEduGroups where StepPermittedEduGroupsID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از گروه های آموزشی مجاز به دسترسی به یک مرحله");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from StepPermittedEduGroups LEFT JOIN EducationalGroups using (EduGrpCode)  ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_StepPermittedEduGroups();
			$ret[$k]->StepPermittedEduGroupsID=$rec["StepPermittedEduGroupsID"];
			$ret[$k]->FormFlowStepID=$rec["FormFlowStepID"];
			$ret[$k]->EduGrpCode=$rec["EduGrpCode"];
			$ret[$k]->EduGrpName=$rec["PEduName"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from StepPermittedEduGroups LEFT JOIN EducationalGroups using (EduGrpCode) ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
