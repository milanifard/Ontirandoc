<?
class be_FormLabels
{
	public $FormsLabelID;		//
	public $LabelDescription;		//شرح
	public $LocationType;		//محل قرار گرفتن
	public $RelatedFieldID;		//کد فیلدی که برچسب قبل یا بعد از آن قرار می گیرد
	public $ShowType;		//نحوه نمایش - توپر - زیر خط دار - ساده
	public $ShowHorizontalLine;		//خط افقی زیر برچسب کشیده شود؟

	function be_FormLabels() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("select * from FormLabels where FormsLabelID='".$RecID."' ");
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			$this->FormsLabelID=$rec["FormsLabelID"];
			$this->LabelDescription=$rec["LabelDescription"];
			$this->LocationType=$rec["LocationType"];
			$this->RelatedFieldID=$rec["RelatedFieldID"];
			$this->ShowType=$rec["ShowType"];
			$this->ShowHorizontalLine=$rec["ShowHorizontalLine"];
		}
	}
	
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FormsLabelID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>شرح </td><td>".$this->LabelDescription."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>محل قرار گرفتن </td><td>".$this->LocationType."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد فیلدی که برچسب قبل یا بعد از آن قرار می گیرد </td><td>".$this->RelatedFieldID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>نحوه نمایش - توپر - زیر خط دار - ساده </td><td>".$this->ShowType."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>خط افقی زیر برچسب کشیده شود؟ </td><td>".$this->ShowHorizontalLine."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FormLabels
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select count(FormsLabelID) as TotalCount from FormLabels';
		if($WhereCondition!="")
		{
			$query .= ' where '.$WhereCondition;
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
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select max(FormsLabelID) as MaxID from FormLabels';
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($LabelDescription, $LocationType, $RelatedFieldID, $ShowType, $ShowHorizontalLine)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FormLabels (LabelDescription
				, LocationType
				, RelatedFieldID
				, ShowType
				, ShowHorizontalLine
				) values ('".$LabelDescription."'
				, '".$LocationType."'
				, '".$RelatedFieldID."'
				, '".$ShowType."'
				, '".$ShowHorizontalLine."'
				)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("ثبت داده جدید در برچسبهای یک فرم با کد ".manage_FormLabels::GetLastID());
	}
	static function Update($UpdateRecordID, $LabelDescription, $LocationType, $RelatedFieldID, $ShowType, $ShowHorizontalLine)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FormLabels set LabelDescription='".$LabelDescription."'
				, LocationType='".$LocationType."'
				, RelatedFieldID='".$RelatedFieldID."'
				, ShowType='".$ShowType."'
				, ShowHorizontalLine='".$ShowHorizontalLine."'
				where FormsLabelID='".$UpdateRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در برچسبهای یک فرم");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "delete from FormLabels where FormsLabelID='".$RemoveRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از برچسبهای یک فرم");
	}
	
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormLabels ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_FormLabels();
			$ret[$k]->FormsLabelID=$rec["FormsLabelID"];
			$ret[$k]->LabelDescription=$rec["LabelDescription"];
			$ret[$k]->LocationType=$rec["LocationType"];
			$ret[$k]->RelatedFieldID=$rec["RelatedFieldID"];
			$ret[$k]->ShowType=$rec["ShowType"];
			$ret[$k]->ShowHorizontalLine=$rec["ShowHorizontalLine"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FormLabels ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		return $res->fetchall();
	}
}
?>
