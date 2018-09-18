<?
class be_FileContents
{
	public $FileContentID;		//
	public $FileID;		//پرونده الکترونیکی مربوطه
	public $ContentType;		//نوع محتوا
	public $FileName;		//نام فایل
	public $description;		//شرح/خلاصه نامه/خلاصه جلسه
	public $FileContent;		//محتوای فایل ضمیمه
	public $LetterType;		//نوع نامه
	public $ContentNumber;		//شماره نامه/جلسه
	public $ContentDate;		//تاریخ نامه/جلسه
	public $FormsStructID;		//ساختار فرم مربوطه
	public $FormRecordID;		//فرم مربوطه
	public $ContentStatus;		//وضعیت محتوا (برای حذف منطقی)
	public $OrderNo;		//شماره ترتیب
	public $RelatedContentID; // کد آیتم مربوطه در صورتیکه محتو لینکی به محتوای دیگر باشد
	
	public $FormTitle; // بر اساس کد ساختار فرم بدست می آید

	function be_FileContents() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = dbclass::getInstance();
		$res = $mysql->Execute("select * from FileContents  LEFT JOIN FormsStruct using (FormsStructID) where FileContentID='".$RecID."'");
		if($rec=$res->FetchRow())
		{
			$this->FileContentID=$rec["FileContentID"];
			$this->FileID=$rec["FileID"];
			$this->ContentType=$rec["ContentType"];
			$this->FileName=$rec["FileName"];
			$this->description=$rec["description"];
			$this->FileContent=$rec["FileContent"];
			$this->LetterType=$rec["LetterType"];
			$this->ContentNumber=$rec["ContentNumber"];
			$this->ContentDate=$rec["ContentDate"];
			$this->FormsStructID=$rec["FormsStructID"];
			$this->FormRecordID=$rec["FormRecordID"];
			$this->ContentStatus=$rec["ContentStatus"];
			$this->OrderNo=$rec["OrderNo"];
			$this->FormTitle=$rec["FormTitle"];
			$this->RelatedContentID=$rec["RelatedContentID"];
		}
	}
	function ShowInfo()
	{
		echo "<table width=80% align=center border=1 cellsapcing=0>";
		echo "<tr>";
		echo "<td>کد شناسایی </td><td>".$this->FileContentID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>پرونده الکترونیکی مربوطه </td><td>".$this->FileID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>نوع محتوا </td><td>".$this->ContentType."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>نام فایل </td><td>".$this->FileName."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>شرح/خلاصه نامه/خلاصه جلسه </td><td>".$this->description."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>محتوای فایل ضمیمه </td><td>".$this->FileContent."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>نوع نامه </td><td>".$this->LetterType."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>شماره نامه/جلسه </td><td>".$this->ContentNumber."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>تاریخ نامه/جلسه </td><td>".$this->ContentDate."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>ساختار فرم مربوطه </td><td>".$this->FormsStructID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>فرم مربوطه </td><td>".$this->FormRecordID."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>وضعیت محتوا (برای حذف منطقی) </td><td>".$this->ContentStatus."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>شماره ترتیب </td><td>".$this->OrderNo."</td>";
		echo "</tr>";
		echo "</table>";
	}
}
class manage_FileContents
{
	static function GetCount($WhereCondition="")
	{
		$mysql = dbclass::getInstance();
		$query = 'select count(FileContentID) as TotalCount from FileContents';
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
	
	//بزرگترین شماره ترتیب را در بین محتویات یک پرونده بر می گرداند
	static function GetMaxOrderNo($FileID, $ContentType)
	{
		$mysql = dbclass::getInstance();
		$query = "select max(OrderNo) as MaxID from FileContents where FileID='".$FileID."' and ContentType='".$ContentType."'";
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return 0;
		
	}
	
	static function GetLastID()
	{
		$mysql = dbclass::getInstance();
		$query = 'select max(FileContentID) as MaxID from FileContents';
		$res = $mysql->Execute($query);
		if($rec=$res->FetchRow())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FileID, $ContentType, $FileName, $description, $FileContent, $LetterType, $ContentNumber, $ContentDate, $FormsStructID, $FormRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "insert into FileContents (FileID
				, ContentType
				, FileName
				, description
				, FileContent
				, LetterType
				, ContentNumber
				, ContentDate
				, FormsStructID
				, FormRecordID
				, ContentStatus
				, OrderNo
				) values ('".$FileID."'
				, '".$ContentType."'
				, '".$FileName."'
				, '".$description."'
				, '".$FileContent."'
				, '".$LetterType."'
				, '".$ContentNumber."'
				, '".xdate($ContentDate)."'
				, '".$FormsStructID."'
				, '".$FormRecordID."'
				, 'ENABLE'
				, '".(manage_FileContents::GetMaxOrderNo($FileID, $ContentType)+1)."'
				)";
		$mysql->Execute($query);
		$FileContentID = manage_FileContents::GetLastID();
		$mysql->audit("ثبت داده جدید در محتویات پرونده با کد ".$FileContentID);
		$mysql->Execute("insert into FileContentHistory (FileContentID, ActionType, ActionTime, PersonID) values ('".$FileContentID."', 'ADD', now(), '".$_SESSION["PersonID"]."') ");
	}
	
	static function Update($UpdateRecordID, $description, $LetterType, $ContentNumber, $ContentDate, $FormsStructID, $FormRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update FileContents set 
				 description='".$description."'
				, LetterType='".$LetterType."'
				, ContentNumber='".$ContentNumber."'
				, ContentDate='".xdate($ContentDate)."'
				, FormsStructID='".$FormsStructID."'
				, FormRecordID='".$FormRecordID."'
				where FileContentID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در محتویات پرونده");
		$mysql->Execute("insert into FileContentHistory (FileContentID, ActionType, ActionTime, PersonID) values ('".$UpdateRecordID."', 'UPDATE', now(), '".$_SESSION["PersonID"]."') ");
	}

	static function UpdateAttachFile($UpdateRecordID, $FileName, $FileContent)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update FileContents set FileName='".$FileName."'
				, FileContent='".$FileContent."'
				where FileContentID='".$UpdateRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("بروز رسانی فایل ضمیمه با شماره شناسایی ".$UpdateRecordID." در محتویات پرونده");
	}
	
	// ترتیب یک محتوا را تغییر می دهد
	// نوع تغییر می تواند UP - DOWN باشد
	static function ChangeOrder($FileContentID, $ChangeType)
	{
		$mysql = dbclass::getInstance();
		$query = "select FileID, ContentType, OrderNo from FileContents where FileContentID='".$FileContentID."'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			if($ChangeType=="UP")
			{
				// بدست آوردن رکورد بعدی در ترتیب محتویات
				$query = "select FileContentID, OrderNo from FileContents where ContentStatus='ENABLE' and OrderNo>'".$rec["OrderNo"]."' and FileID='".$rec["FileID"]."' and ContentType='".$rec["ContentType"]."' order by OrderNo";
				$res2 = $mysql->Execute($query);
				if($rec2 = $res2->FetchRow())
				{
					$mysql->Execute("update FileContents set OrderNo='".$rec2["OrderNo"]."' where FileContentID='".$FileContentID."'");
					$mysql->Execute("update FileContents set OrderNo='".$rec["OrderNo"]."' where FileContentID='".$rec2["FileContentID"]."'");
				}
			}
			else
			{
				// بدست آوردن رکورد قبلی در ترتیب محتویات
				$query = "select FileContentID, OrderNo from FileContents where  ContentStatus='ENABLE' and OrderNo<'".$rec["OrderNo"]."' and FileID='".$rec["FileID"]."' and ContentType='".$rec["ContentType"]."' order by OrderNo DESC";
				$res2 = $mysql->Execute($query);
				if($rec2 = $res2->FetchRow())
				{
					$mysql->Execute("update FileContents set OrderNo='".$rec2["OrderNo"]."' where FileContentID='".$FileContentID."'");
					$mysql->Execute("update FileContents set OrderNo='".$rec["OrderNo"]."' where FileContentID='".$rec2["FileContentID"]."'");
				}
			}
		}
		$obj = new be_FileContents();
		$obj->LoadDataFromDatabase($FileContentID);
		
	}
	
	static function Remove($RemoveRecordID)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "update FileContents set ContentStatus='DISABLE' where FileContentID='".$RemoveRecordID."'";
		$mysql->Execute($query);
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از محتویات پرونده");
		$mysql->Execute("insert into FileContentHistory (FileContentID, ActionType, ActionTime, PersonID) values ('".$RemoveRecordID."', 'REMOVE', now(), '".$_SESSION["PersonID"]."') ");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select 
						  fc1.FileContentID, fc1.FileID, fc1.ContentType, fc1.FileName as FileName1, fc1.description as description1, 
						  fc1.LetterType as LetterType1, fc1.ContentNumber as ContentNumber1, fc1.ContentDate as ContentDate1, fc1.FormsStructID as FormsStructID1, 
						  fc1.FormRecordID as FormRecordID1, fc1.ContentStatus, fc1.RelatedContentID, fc1.OrderNo, fs1.FormTitle as FormTitle1, 
						  
						  fc2.FileName as FileName2, fc2.description as description2, 
						  fc2.LetterType as LetterType2, fc2.ContentNumber as ContentNumber2, fc2.ContentDate as ContentDate2, fc2.FormsStructID as FormsStructID2, 
						  fc2.FormRecordID as FormRecordID2, fs2.FormTitle as FormTitle2
						  
						  from FileContents fc1  
						  LEFT JOIN FormsStruct as fs1 on (fc1.FormsStructID=fs1.FormsStructID) 
						  LEFT JOIN FileContents AS fc2 on (fc1.RelatedContentID=fc2.FileContentID)
						  LEFT JOIN FormsStruct as fs2 on (fc2.FormsStructID=fs2.FormsStructID) 
						  ";
		$query .= "where fc1.ContentStatus='ENABLE' ";
		if($WhereCondition!="")
			$query .= " and ".$WhereCondition;
		$query .= " order by fc1.OrderNo ";
		//echo $query;
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->FetchRow())
		{
			$ret[$k] = new be_FileContents();
			$ret[$k]->FileContentID=$rec["FileContentID"];
			$ret[$k]->FileID=$rec["FileID"];
			$ret[$k]->ContentType=$rec["ContentType"];
			$ret[$k]->ContentStatus=$rec["ContentStatus"];
			$ret[$k]->OrderNo=$rec["OrderNo"];			
			$ret[$k]->RelatedContentID=$rec["RelatedContentID"];
			if($rec["RelatedContentID"]>0)
			{
				$ret[$k]->FileName=$rec["FileName2"];
				$ret[$k]->description=$rec["description2"];
				$ret[$k]->LetterType=$rec["LetterType2"];
				$ret[$k]->ContentNumber=$rec["ContentNumber2"];
				$ret[$k]->ContentDate=$rec["ContentDate2"];
				$ret[$k]->FormsStructID=$rec["FormsStructID2"];
				$ret[$k]->FormRecordID=$rec["FormRecordID2"];
				$ret[$k]->FormTitle=$rec["FormTitle2"];
			}
			else
			{
				$ret[$k]->FileName=$rec["FileName1"];
				$ret[$k]->description=$rec["description1"];
				$ret[$k]->LetterType=$rec["LetterType1"];
				$ret[$k]->ContentNumber=$rec["ContentNumber1"];
				$ret[$k]->ContentDate=$rec["ContentDate1"];
				$ret[$k]->FormsStructID=$rec["FormsStructID1"];
				$ret[$k]->FormRecordID=$rec["FormRecordID1"];
				$ret[$k]->FormTitle=$rec["FormTitle1"];
			}
			$k++;			
		}
		return $ret;
	}
	static function GetRows($WhereCondition)
	{
		$mysql = dbclass::getInstance();
		$query = '';
		$query .= "select * from FileContents ";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$res = $mysql->Execute($query);
		$i=0;
		return $res->GetRows();
	}
}
?>
