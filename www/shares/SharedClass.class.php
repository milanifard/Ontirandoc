<?php
class SharedClass
{
	
	/*
	 * @param $ShamsiYear: سال شمسی
	 * @param $ShamsiMonth: ماه شمسی
	 * @param $ShamsiDay: روز شمسی
	 * @return تاریخ میلادی
	 */
	static function xdate2($date)
	  {
	   if($date==NULL)
	     return '0000-00-00';
	   else{
	   $yy=substr($date,0,4);
	   $mm=substr($date,5,2);
	   $dd=substr($date,8,2);
	   $xdate2 = ConvertS2XDate($dd,$mm,$yy);
	   return $xdate2;}
	  }
	static function ConvertToMiladi2($ShamsiYear, $ShamsiMonth, $ShamsiDay)
	{
		//!ereg("^[0-9]{2}", $ShamsiMonth) || !ereg("^[0-9]{2}", $ShamsiDay))
		if(!is_numeric($ShamsiYear) || !is_numeric($ShamsiMonth) || !is_numeric($ShamsiDay))
			return "0000-00-00";
		if($ShamsiMonth>12 || $ShamsiDay>31 || $ShamsiYear==0)
			return "0000-00-00";
		if(strlen($ShamsiDay)==1)
			$ShamsiDay = "0".$ShamsiDay;
		if(strlen($ShamsiMonth)==1)
			$ShamsiMonth = "0".$ShamsiMonth;
		$ShamsiDate = SharedClass::xdate2($ShamsiYear."/".$ShamsiMonth."/".$ShamsiDay);
		return substr($ShamsiDate,0,4)."-".substr($ShamsiDate,4,2)."-".substr($ShamsiDate,6,2);
	}
	
	/*
	 * @param $ShamsiYear: سال شمسی
	 * @param $ShamsiMonth: ماه شمسی
	 * @param $ShamsiDay: روز شمسی
	 * @return تاریخ میلادی
	 */
	static function ConvertToMiladi($ShamsiYear, $ShamsiMonth, $ShamsiDay)
	{
		//!ereg("^[0-9]{2}", $ShamsiMonth) || !ereg("^[0-9]{2}", $ShamsiDay))
		if(!is_numeric($ShamsiYear) || !is_numeric($ShamsiMonth) || !is_numeric($ShamsiDay))
			return "0000-00-00";
		if($ShamsiMonth>12 || $ShamsiDay>31 || $ShamsiYear==0)
			return "0000-00-00";
		if(strlen($ShamsiDay)==1)
			$ShamsiDay = "0".$ShamsiDay;
		if(strlen($ShamsiMonth)==1)
			$ShamsiMonth = "0".$ShamsiMonth;
		$ShamsiDate = xdate($ShamsiYear."/".$ShamsiMonth."/".$ShamsiDay);
		//echo $ShamsiYear."/".$ShamsiMonth."/".$ShamsiDay." -> ".substr($ShamsiDate,0,4)."-".substr($ShamsiDate,4,2)."-".substr($ShamsiDate,6,2)."<br>";
		return substr($ShamsiDate,0,4)."-".substr($ShamsiDate,4,2)."-".substr($ShamsiDate,6,2);
	}
	
	// بر اساس یک کلید از جدول دومین آپشنهای یک لیست را بر می گرداند
	static function CreateADomainNameSelectOptions($DomainName, $OrderByColumn = "description")
	{
		$ret = "";
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from baseinfo.domains where DomainName='".$DomainName."' order by ".$OrderByColumn);
		while($rec = $res->FetchRow())
		{
			$ret .= "<option value='".$rec["DomainValue"]."'>";
			$ret .= $rec["description"];
			$ret .= "</option>";
		}
		return $ret;
	}

	// آپشنهای یک لیست را بر اساس یک جدول و فیلد مقدار و متن مربوطه می سازد
	static function CreateARelatedTableSelectOptions($RelatedTable, $RelatedValueField, $RelatedDescriptionField, $OrderBy = "")
	{
		if($OrderBy=="")
			$OrderBy = $RelatedValueField;
		$ret = "";
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from ".$RelatedTable." order by ".$OrderBy);
		while($rec = $res->FetchRow())
		{
			$ret .= "<option value='".$rec[$RelatedValueField]."'>";
			$ret .= $rec[$RelatedDescriptionField];
			$ret .= "</option>";
		}
		return $ret;
	}



	static function CreateAdvanceRelatedTableSelectOptions($RelatedTable, $RelatedValueField, $RelatedDescriptionField, $SelectOptions, $OrderBy = "")
	{
		if($OrderBy=="")
			$OrderBy = $RelatedValueField;
		$ret = "";
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select ".$SelectOptions." from ".$RelatedTable." order by ".$OrderBy);
		while($rec = $res->FetchRow())
		{
			$ret .= "<option value='".$rec[$RelatedValueField]."'>";
			$ret .= $rec[$RelatedDescriptionField];
			$ret .= "</option>";
		}
		return $ret;
	}
	
	static function CreateMessageBox($MessageBody, $MessageColor='green')
	{
		$ret = "";
		$ret .= "<table align=center><tr id=\"MessageBox\" style=\"display: \"><td><font color='".$MessageColor."'>".$MessageBody."</font></td></tr></table>\r\n";
		$ret .= "<script>setTimeout('document.getElementById(\"MessageBox\").style.display=\"none\";', 3000);</script>";
		return $ret;			
	}
	
	static function GetPersonFullName($PersonID)
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("select concat(pfname, ' ', plname) as FullName from projectmanagement.persons where PersonID=?");
		$res = $mysql->ExecuteStatement(array($PersonID));
		if($rec = $res->fetch())
		{
			return $rec["FullName"]; 		
		}
		return "-";
	}
}

class PermissionsContainer
{
	private $PermissionTable = array();
	private $ItemsCount = 0;
	public $HasWriteAccessOnOneItemAtLeast = false; // کاربر حداقل به یکی از آیتمها دسترسی کامل دارد
	
	public function Add($ObjectName, $PermissionType)
	{
		$this->PermissionTable[$this->ItemsCount]["ObjectName"] = $ObjectName;
		$this->PermissionTable[$this->ItemsCount]["PermissionType"] = $PermissionType;
		if($PermissionType=="WRITE")
			$this->OneItemAccessAtLeast = true;
		$this->ItemsCount++; 
	}
	
	public function Reset()
	{
		$this->ItemsCount = 0;
	}
	
	public function GetPermission($ObjectName)
	{
		for($i=0; $i<$this->ItemsCount; $i++)
		{
			if($this->PermissionTable[$i]["ObjectName"] ==  $ObjectName)
				return $this->PermissionTable[$i]["PermissionType"];
		}
		return "NONE";
	}
	
	public function Show()
	{
		for($i=0; $i<$this->ItemsCount; $i++)
		{
			echo $this->PermissionTable[$i]["ObjectName"]." -> ".$this->PermissionTable[$i]["PermissionType"]."<br>";
		}
	}
}
?>