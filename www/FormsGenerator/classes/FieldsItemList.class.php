<?
class be_FieldsItemList
{
	public $FieldItemListID;		//
	public $FormFieldID;		//کد فیلد
	public $ItemValue;		//مقدار آیتم
	public $ItemDescription;		//شرح آیتم

	function be_FieldsItemList() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("select * from FieldsItemList where FieldItemListID='".$RecID."' ");
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			$this->FieldItemListID=$rec["FieldItemListID"];
			$this->FormFieldID=$rec["FormFieldID"];
			$this->ItemValue=$rec["ItemValue"];
			$this->ItemDescription=$rec["ItemDescription"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FieldItemListID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>کد فیلد </td><td>".$this->FormFieldID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>مقدار آیتم </td><td>".$this->ItemValue."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>شرح آیتم </td><td>".$this->ItemDescription."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FieldsItemList
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select count(FieldItemListID) as TotalCount from FieldsItemList';
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
		$query = 'select max(FieldItemListID) as MaxID from FieldsItemList';
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormFieldID, $ItemValue, $ItemDescription)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FieldsItemList (FormFieldID
				, ItemValue
				, ItemDescription
				) values ('".$FormFieldID."'
				, '".$ItemValue."'
				, '".$ItemDescription."'
				)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("ثبت داده جدید در آیتمهای لیست برای فیلدهای از نوع لیستی با کد ".manage_FieldsItemList::GetLastID());
	}
	static function Update($UpdateRecordID, $ItemValue, $ItemDescription)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FieldsItemList set ItemValue='".$ItemValue."'
				, ItemDescription='".$ItemDescription."'
				where FieldItemListID='".$UpdateRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در آیتمهای لیست برای فیلدهای از نوع لیستی");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "delete from FieldsItemList where FieldItemListID='".$RemoveRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از آیتمهای لیست برای فیلدهای از نوع لیستی");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FieldsItemList ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$query .= " order by ItemValue";

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_FieldsItemList();
			$ret[$k]->FieldItemListID=$rec["FieldItemListID"];
			$ret[$k]->FormFieldID=$rec["FormFieldID"];
			$ret[$k]->ItemValue=$rec["ItemValue"];
			$ret[$k]->ItemDescription=$rec["ItemDescription"];
			$k++;
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select * from FieldsItemList ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i=0;
		return $res->fetchall();
	}

	// لیست آیتمها را به صورت آپشنهای یک کمبو باکس بر می گرداند
	static function GetItemsInOptionList($FormFieldID, $DefaultValue)
	{
		$ret = "";
		$res = manage_FieldsItemList::GetList(" FormFieldID='".$FormFieldID."' ");
		for($i=0; $i<count($res); $i++)
		{
			$ret .= "<option value='".$res[$i]->ItemValue."' ";
			if($DefaultValue==$res[$i]->ItemValue)
				$ret .= " selected ";
			$ret .= ">".$res[$i]->ItemDescription;
		} 
		return $ret;
	}

	// لیست آیتمها را به صورت آپشنهای یک کمبو باکس بر می گرداند
	static function GetItemsInRadioList($FormFieldID, $DefaultValue, $RadioName)
	{
		$ret = "";
		$res = manage_FieldsItemList::GetList(" FormFieldID='".$FormFieldID."' ");
		for($i=0; $i<count($res); $i++)
		{
			$ret .= "<input type=radio name='".$RadioName."' id='".$RadioName."' value='".$res[$i]->ItemValue."' ";
			if($DefaultValue==$res[$i]->ItemValue)
				$ret .= " checked ";
			$ret .= ">".$res[$i]->ItemDescription;
		} 
		return $ret;
	}
	
	// شرح یک آیتم را بر اساس کد آن از لیست استخراج می کند
	static function GetItemDescriptionInOptionList($FormFieldID, $FieldValue)
	{
		$ret = "-";
		$res = manage_FieldsItemList::GetList(" FormFieldID='".$FormFieldID."' and  ItemValue='".$FieldValue."'");
		for($i=0; $i<count($res); $i++)
		{
			$ret = $res[$i]->ItemDescription;
		} 
		return $ret;
	}
	
}
?>
