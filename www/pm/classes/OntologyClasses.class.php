<?php
/*
 تعریف کلاسها و متدهای مربوط به : کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-29
*/

/*
کلاس پایه: کلاسهای هستان نگار
*/
class be_OntologyClasses
{
	public $OntologyClassID;		//
	public $OntologyID;		//
	public $ClassTitle;		//عنوان کلاس
	public $OntologyTitle;
	public $UpperClassID; // کد کلاس والد
	public $label;
	
	function be_OntologyClasses() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select OntologyClasses.*, OntologyTitle
		,(select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID) as label
		from projectmanagement.OntologyClasses  
		LEFT JOIN projectmanagement.ontologies using (OntologyID)
		where  OntologyClasses.OntologyClassID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyClassID=$rec["OntologyClassID"];
			$this->OntologyID=$rec["OntologyID"];
			$this->ClassTitle=$rec["ClassTitle"];
			$this->OntologyTitle=$rec["OntologyTitle"];
			$this->label=$rec["label"];
		}
		$query = "select * from projectmanagement.OntologyClassHirarchy where OntologyClassParentID=?";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec = $res->fetch())
		{
		  $this->UpperClassID = $rec["OntologyClassID"];
		}
	}

	function LoadDataFromDatabaseByTitle($ClassTitle, $OntologyID)
	{
		$query = "select OntologyClasses.*, OntologyTitle
		,(select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID) as label
		from projectmanagement.OntologyClasses  
		LEFT JOIN projectmanagement.ontologies using (OntologyID)
		where  OntologyClasses.ClassTitle=? and OntologyID=?";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($ClassTitle, $OntologyID));
		//echo "\r\n";
		//echo $ClassTitle."\r\n";
		//echo $OntologyID."\r\n";
		if($rec=$res->fetch())
		{
		    $this->OntologyClassID=$rec["OntologyClassID"];
		    $this->OntologyID=$rec["OntologyID"];
		    $this->ClassTitle=$rec["ClassTitle"];
		    $this->OntologyTitle=$rec["OntologyTitle"];
		    $this->label=$rec["label"];
		    $query = "select * from projectmanagement.OntologyClassHirarchy where OntologyClassParentID=?";
		    $mysql = pdodb::getInstance();
		    $mysql->Prepare ($query);
		    $res = $mysql->ExecuteStatement (array($rec["OntologyClassID"]));
		    if($rec = $res->fetch())
		    {
		      $this->UpperClassID = $rec["OntologyClassID"];
		    }
		}
	}
	
	
	function GetRelatedClasses()
	{
	  $mysql = pdodb::getInstance();
	  /*
	  $query = "select OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, domain, `range`, group_concat(label) as PropertyLabel from 
	  projectmanagement.OntologyProperties 
	  JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
	  where 
	  OntologyID=".$this->OntologyID."
	  and
	  (domain like '".$this->ClassTitle."' or domain like '".$this->ClassTitle.", %' or domain like '% ".$this->ClassTitle."' or domain like '% ".$this->ClassTitle.",%')
	  and PropertyType = 'OBJECT'
	  group by OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, Domain, `range`
	  order by PropertyType, PropertyTitle";
	
	  $res = $mysql->Execute($query);
	  //echo "\r\n";
	  $query = "select OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, domain, `range`, group_concat(label) as PropertyLabel from 
	  projectmanagement.OntologyProperties 
	  JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
	  where 
	  OntologyID=".$this->OntologyID."
	  and
	  (`range` like '".$this->ClassTitle."' or `range` like '".$this->ClassTitle.", %' or `range` like '% ".$this->ClassTitle."' or `range` like '% ".$this->ClassTitle.",%')
	  and PropertyType = 'OBJECT' 
	  group by OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, Domain, `range`
	  order by PropertyType, PropertyTitle";
	
	  $res2 = $mysql->Execute($query);
	  */
	  $query = "select DomainClassID, RangeClassID, OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, domain, `range`
	  , (select group_concat(label) from OntologyPropertyLabels where OntologyPropertyID=OntologyObjectPropertyRestriction.OntologyPropertyID) as PropertyLabel 
	  from projectmanagement.OntologyObjectPropertyRestriction 
	  JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
	  where DomainClassID='".$this->OntologyClassID."' and RelationStatus='VALID'";
	  $res = $mysql->Execute($query);

	  $query = "select DomainClassID, RangeClassID, OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, domain, `range`
	  , (select group_concat(label) from OntologyPropertyLabels where OntologyPropertyID=OntologyObjectPropertyRestriction.OntologyPropertyID) as PropertyLabel 
	  from projectmanagement.OntologyObjectPropertyRestriction 
	  JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
	  where RangeClassID='".$this->OntologyClassID."' and RelationStatus='VALID'";
	  $res2 = $mysql->Execute($query);
	  
	  $RelatedClasses = array();
	  $k = 0;

	  while($rec = $res->fetch())
	  {
	    $obj = new be_OntologyClasses();
	    $obj->LoadDataFromDatabase($rec["RangeClassID"], $this->OntologyID);

	    $RelatedClasses[$k]["PropName"] = $rec["PropertyLabel"];
	    $RelatedClasses[$k]["Direction"] = "range";
	    $RelatedClasses[$k++]["Class"] = $obj;
	  }

	  while($rec = $res2->fetch())
	  {
	    $obj = new be_OntologyClasses();
	    $obj->LoadDataFromDatabase($rec["DomainClassID"], $this->OntologyID);

	    $RelatedClasses[$k]["PropName"] = $rec["PropertyLabel"];
	    $RelatedClasses[$k]["Direction"] = "domain";
	    $RelatedClasses[$k++]["Class"] = $obj;
	  }
	  
	  return $RelatedClasses;
	}
	
}
/*
کلاس مدیریت کلاسهای هستان نگار
*/
class manage_OntologyClasses
{

static function GetClassRelatedProperties($ClassTitle, $OntologyID)
{
  $ret = array();
  $query = "select OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, domain, `range`, group_concat(label) as PropertyLabel from 
  projectmanagement.OntologyProperties 
  JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
  where 
  OntologyID=".$OntologyID."
  and
  (
  (domain like '".$ClassTitle."' or domain like '".$ClassTitle.", %' or domain like '% ".$ClassTitle."' or domain like '% ".$ClassTitle.",%')
  or
  (`range` like '".$ClassTitle."' or `range` like '".$ClassTitle.", %' or `range` like '% ".$ClassTitle."' or `range` like '% ".$ClassTitle.",%')
  )
  group by OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, Domain, `range`
  order by PropertyType, PropertyTitle";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute($query);
  $k=0;
  while($rec = $res->fetch())
  {
    $ret[$k]["PropertyType"] = $rec["PropertyType"];
    $ret[$k]["PropertyID"] = $rec["OntologyPropertyID"];
    $ret[$k]["PropertyTitle"] = $rec["PropertyTitle"];
    $ret[$k]["domain"] = $rec["domain"];
    $ret[$k]["range"] = $rec["range"];
    $ret[$k]["PropertyLabel"] = $rec["PropertyLabel"];
    $k++;
  }
  return $ret;
}

	static function GetSuggestedLabel($ClassTitle)
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare("select label from projectmanagement.OntologyClassLabels 
			    JOIN projectmanagement.OntologyClasses using (OntologyClassID)
			    where (ClassTitle=?)");
	  $res = $mysql->ExecuteStatement(array($ClassTitle));
	  if($rec = $res->fetch())
	  {
	    return $rec["label"];
	  }

	  $mysql->Prepare("select label from projectmanagement.OntologyClassLabels 
			    JOIN projectmanagement.OntologyClasses using (OntologyClassID)
			    where (ClassTitle like ?)");
	  $res = $mysql->ExecuteStatement(array("%".$ClassTitle."%"));
	  if($rec = $res->fetch())
	  {
	    return $rec["label"];
	  }
	  /*
	  $res = $mysql->Execute("select ClassTitle, label from projectmanagement.OntologyClassLabels 
			    JOIN projectmanagement.OntologyClasses using (OntologyClassID) order by length(ClassTitle) DESC");
	  while($rec = $res->fetch())
	  {
	    $LastIndex = strlen($ClassTitle)-strlen($rec["ClassTitle"]);
	    if(strpos($ClassTitle, $rec["ClassTitle"])===0 || strpos($ClassTitle, $rec["ClassTitle"])===$LastIndex)
	    {
	      return $rec["label"];
	    }
	  }
	  */
	  return "";
	}

    static function GetChildsCount($OntologyClassID)
    {
      $mysql = pdodb::getInstance();
      $mysql->Prepare("select count(*) as tcount from projectmanagement.OntologyClassHirarchy where OntologyClassID=?");
      $res = $mysql->ExecuteStatement(array($OntologyClassID));
      if($rec = $res->fetch())
	return $rec["tcount"];
      return 0;
    }
    
	static function GetCount($OntologyID)
	{
		$mysql = pdodb::getInstance();
		$query = "select count(OntologyClassID) as TotalCount from projectmanagement.OntologyClasses";
			$query .= " where OntologyID='".$OntologyID."'";
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
		$mysql = pdodb::getInstance();
		$query = "select max(OntologyClassID) as MaxID from projectmanagement.OntologyClasses";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $OntologyID: 
	* @param $ClassTitle: عنوان کلاس
	* @return کد داده اضافه شده	*/
	static function Add($OntologyID, $ClassTitle)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.OntologyClasses (";
		$query .= " OntologyID";
		$query .= ", ClassTitle";
		$query .= ") values (";
		$query .= "? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyID); 
		array_push($ValueListArray, $ClassTitle); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_OntologyClasses::GetLastID();
		$mysql->audit("ثبت داده جدید در کلاسهای هستان نگار با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $ClassTitle: عنوان کلاس
	* @return 	*/
	static function Update($UpdateRecordID, $ClassTitle)
	{
		$k=0;
		$LogDesc = manage_OntologyClasses::ComparePassedDataWithDB($UpdateRecordID, $ClassTitle);
		
		$OldObj = new be_OntologyClasses();
		$OldObj->LoadDataFromDatabase($UpdateRecordID);
		
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyClasses set ";
			$query .= " ClassTitle=? ";
		$query .= " where OntologyClassID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $ClassTitle); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در کلاسهای هستان نگار - موارد تغییر داده شده: ".$LogDesc);
		// اگر نام کلاس تغییر کرده بود برای در دامنه و برد همه خصوصیات عوض شود
		if($OldObj->ClassTitle!=$ClassTitle)
		{
		  $PropRes = manage_OntologyClasses::GetProperties($OldObj->ClassTitle, $OldObj->OntologyID);
		  while($rec = $PropRes->fetch())
		  {
		    manage_OntologyClasses::ReplaceClass1WithClass2($rec["OntologyPropertyID"], $rec["domain"], $rec["range"], $OldObj->ClassTitle, $ClassTitle);
		  }
		}
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	
	static function GetProperties($ClassTitle, $OntologyID)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select OntologyPropertyID, PropertyTitle, label, domain, `range` 
	  from projectmanagement.OntologyProperties 
	  LEFT JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
	  where 
	  (
	  (domain like '".$ClassTitle.",%' or domain like '%, ".$ClassTitle.",%' or domain like '%, ".$ClassTitle."' or domain='".$ClassTitle."') 
	  or
	  (`range` like '".$ClassTitle.",%' or `range` like '%, ".$ClassTitle.",%' or `range` like '%, ".$ClassTitle."' or `range`='".$ClassTitle."') 
	  )
	  and OntologyID=?";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($OntologyID));
	  return $res;
	}

	static function GetHirarchyID($ParentClassID, $ChildClassID)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select * from projectmanagement.OntologyClassHirarchy where OntologyClassID=? and OntologyClassParentID=?";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($ParentClassID, $ChildClassID));
	  if($rec = $res->fetch())
	  {
	    return $rec["OntologyClassHirarchyID"];
	  }
	  return 0;
	}
	
	// عنوان کلاس اول را به رشته اضافه و عنوان کلاس دوم را از رشته حذف می کند
	static function RemoveClass1AndAddClass2To($DomainOrRangeString, $SourceClassTitle, $TargetClassTitle)
	{
	  $slist = explode(", ", $DomainOrRangeString);
	  $flist = array();
	  $k=0;
	  $TargetClassTitleExist = false;
	  for($i=0; $i<count($slist); $i++)
	  {
	    if($slist[$i]!=$SourceClassTitle)
	    {
	      $flist[$k] = $slist[$i];
	      $k++;
	    }
	    if($slist[$i]==$TargetClassTitle)
	      $TargetClassTitleExist = true;
	  }
	  if(!$TargetClassTitleExist)
	    $flist[$k] = $TargetClassTitle;
          $ret = "";
          for($k=0; $k<count($flist); $k++)
	  {
	    if($k>0)
	      $ret .= ", ";
	    $ret .= $flist[$k];
	  }
	  return $ret;
	}

	static function ReplaceClass1WithClass2($PropID, $PropDomain, $PropRange, $SourceClassTitle, $TargetClassTitle)
	{
	  $mysql = pdodb::getInstance();
	  $slist = explode(", ", $PropDomain);
	  $flist = array();
	  $k=0;
	  $TargetClassTitleExistInDomain = false;
	  for($i=0; $i<count($slist); $i++)
	  {
	    if($slist[$i]!=$SourceClassTitle)
	    {
	      $flist[$k] = $slist[$i];
	      $k++;
	    }
	    else 
	    {
	      $flist[$k] = $TargetClassTitle;
	      $k++;
	      $TargetClassTitleExistInDomain = true;
	    }
	  }
          $domain = "";
          for($k=0; $k<count($flist); $k++)
	  {
	    if($k>0)
	      $domain .= ", ";
	    $domain .= $flist[$k];
	  }
	  
	  $slist = explode(", ", $PropRange);
	  $flist = array();
	  $k=0;
	  $TargetClassTitleExistInRange = false;
	  for($i=0; $i<count($slist); $i++)
	  {
	    if($slist[$i]!=$SourceClassTitle)
	    {
	      $flist[$k] = $slist[$i];
	      $k++;
	    }
	    else 
	    {
	      $flist[$k] = $TargetClassTitle;
	      $k++;
	      $TargetClassTitleExistInRange = true;
	    }
	  }
          $range = "";
          for($k=0; $k<count($flist); $k++)
	  {
	    if($k>0)
	      $range .= ", ";
	    $range .= $flist[$k];
	  }
	  
	  if($TargetClassTitleExistInDomain || $TargetClassTitleExistInRange)
	  {
	    $query = "update projectmanagement.OntologyProperties set domain='".$domain."', `range`='".$range."' where OntologyPropertyID=".$PropID;
	    $mysql->Execute($query);
	  }
	}
	
	static function RemoveClassFromDomainRange($DomainOrRangeString, $SourceClassTitle)
	{
	  $slist = explode(", ", $DomainOrRangeString);
	  $flist = array();
	  $k=0;
	  for($i=0; $i<count($slist); $i++)
	  {
	    if($slist[$i]!=$SourceClassTitle)
	    {
	      $flist[$k] = $slist[$i];
	      $k++;
	    }
	  }
          $ret = "";
          for($k=0; $k<count($flist); $k++)
	  {
	    if($k>0)
	      $ret .= ", ";
	    $ret .= $flist[$k];
	  }
	  return $ret;
	}
	
	static function MergeClasses($OntologyID, $SourceClassID, $SourceClassTitle, $TargetClassID, $TargetClassTitle)
	{
	    $mysql = pdodb::getInstance();
	  // کلاس ۱ را به کلاس ۲ تبدیل می کند
// ابتدا تبدیل روابط رده بندی	    
	    // تمام زیر کلاسهای مربوط به کلاسی که باید حذف شود
	    $query = "select * from projectmanagement.OntologyClassHirarchy where OntologyClassID=?";
	    $mysql->Prepare($query);
	    $res = $mysql->ExecuteStatement(array($SourceClassID));
	    while($rec = $res->fetch())
	    {
	      $SourceChildID = $rec["OntologyClassParentID"];
	      $SourceLinkID = $rec["OntologyClassHirarchyID"];
	      $CurHirarchyID = manage_OntologyClasses::GetHirarchyID($TargetClassID, $SourceChildID);
	      if($CurHirarchyID>0)
	      {
		// اگر در حال حاضر اتصالی بین کلاس هدف و کلاس فرزند کلاس مورد حذف وجود داشته باشد کافیست اتصال رده بندی فعلی حذف شود
		$mysql->Execute("delete from projectmanagement.OntologyClassHirarchy where OntologyClassHirarchyID=".$SourceLinkID);
	      }
	      else 
	      {
	      // اتصال رده بندی قدیمی به اتصال رده بندی جدید تبدیل می شود
		$mysql->Execute("update projectmanagement.OntologyClassHirarchy set OntologyClassID=".$TargetClassID." where OntologyClassHirarchyID=".$SourceLinkID);
	      }
	    }

// تمام کلاسهای پدر کلاسی که باید حذف شود
	    $query = "select * from projectmanagement.OntologyClassHirarchy where OntologyClassParentID=?";
	    $mysql->Prepare($query);
	    $res = $mysql->ExecuteStatement(array($SourceClassID));
	    while($rec = $res->fetch())
	    {
	      $SourceParentID = $rec["OntologyClassID"];
	      $SourceLinkID = $rec["OntologyClassHirarchyID"];
	      $CurHirarchyID = manage_OntologyClasses::GetHirarchyID($SourceParentID, $TargetClassID);
	      if($CurHirarchyID>0)
	      {
		// اگر در حال حاضر اتصالی بین کلاس هدف و کلاس فرزند کلاس مورد حذف وجود داشته باشد کافیست اتصال رده بندی فعلی حذف شود
		$mysql->Execute("delete from projectmanagement.OntologyClassHirarchy where OntologyClassHirarchyID=".$SourceLinkID);
	      }
	      else 
	      {
	      // اتصال رده بندی قدیمی به اتصال رده بندی جدید تبدیل می شود
		$mysql->Execute("update projectmanagement.OntologyClassHirarchy set OntologyClassParentID=".$TargetClassID." where OntologyClassHirarchyID=".$SourceLinkID);
	      }
	    }
	    
// تبدیل عنوان کلاس در حوزه و برد خصوصیات
	    $query = "select * from projectmanagement.OntologyProperties where OntologyID=? and 
	    (domain like '".$SourceClassTitle.",%' or domain like '%, ".$SourceClassTitle.",%' or domain like '%, ".$SourceClassTitle."' or domain='".$SourceClassTitle."')";
	    $mysql->Prepare($query);
	    $res = $mysql->ExecuteStatement(array($OntologyID));
	    while($rec = $res->fetch())
	    {
	      $NewDomain = manage_OntologyClasses::RemoveClass1AndAddClass2To($rec["domain"], $SourceClassTitle, $TargetClassTitle);
	      $query = "update projectmanagement.OntologyProperties set domain='".$NewDomain."' where OntologyPropertyID=".$rec["OntologyPropertyID"];
	      $mysql->Execute($query);
	    }

	    $query = "select * from projectmanagement.OntologyProperties where OntologyID=? and 
	    (`range` like '".$SourceClassTitle.",%' or `range` like '%, ".$SourceClassTitle.",%' or `range` like '%, ".$SourceClassTitle."' or `range`='".$SourceClassTitle."')";
	    $mysql->Prepare($query);
	    $res = $mysql->ExecuteStatement(array($OntologyID));
	    while($rec = $res->fetch())
	    {
	      $NewRange = manage_OntologyClasses::RemoveClass1AndAddClass2To($rec["range"], $SourceClassTitle, $TargetClassTitle);
	      $query = "update projectmanagement.OntologyProperties set `range`='".$NewRange."' where OntologyPropertyID=".$rec["OntologyPropertyID"];
	      $mysql->Execute($query);
	    }
	    // محدودیتهای رابطه را هم بر اساس این تعویض کلاس بروز می کند
	    $query = "update projectmanagement.OntologyObjectPropertyRestriction set DomainClassID='".$TargetClassID."' where DomainClassID='".$SourceClassID."'";
	    $mysql->Execute($query);
	    $query = "update projectmanagement.OntologyObjectPropertyRestriction set RangeClassID='".$TargetClassID."' where RangeClassID='".$SourceClassID."'";
	    $mysql->Execute($query);
	    
	    manage_OntologyClasses::Remove($SourceClassID);
	}
	
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$obj = new be_OntologyClasses();
		$obj->LoadDataFromDatabase($RemoveRecordID);
		
    // تبدیل عنوان کلاس در حوزه و برد خصوصیات
		$query = "select * from projectmanagement.OntologyProperties where OntologyID=? and 
		(domain like '".$obj->ClassTitle.",%' or domain like '%, ".$obj->ClassTitle.",%' or domain like '%, ".$obj->ClassTitle."' or domain='".$obj->ClassTitle."')";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($obj->OntologyID));
		while($rec = $res->fetch())
		{
		  $NewDomain = manage_OntologyClasses::RemoveClassFromDomainRange($rec["domain"], $obj->ClassTitle);
		  $query = "update projectmanagement.OntologyProperties set domain='".$NewDomain."' where OntologyPropertyID=".$rec["OntologyPropertyID"];
		  $mysql->Execute($query);
		}

		$query = "select * from projectmanagement.OntologyProperties where OntologyID=? and 
		(`range` like '".$obj->ClassTitle.",%' or `range` like '%, ".$obj->ClassTitle.",%' or `range` like '%, ".$obj->ClassTitle."' or `range`='".$obj->ClassTitle."')";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($obj->OntologyID));
		while($rec = $res->fetch())
		{
		  $NewRange = manage_OntologyClasses::RemoveClassFromDomainRange($rec["range"], $obj->ClassTitle);
		  $query = "update projectmanagement.OntologyProperties set `range`='".$NewRange."' where OntologyPropertyID=".$rec["OntologyPropertyID"];
		  $mysql->Execute($query);
		}		
		
		$query = "delete from projectmanagement.OntologyClasses where OntologyClassID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.OntologyClassLabels where OntologyClassID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		
		// حذف زیر کلاسها
		$query = "select OntologyClassParentID from projectmanagement.OntologyClassHirarchy where OntologyClassID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($RemoveRecordID));
		while($rec = $res->fetch())
		{
		  manage_OntologyClasses::Remove($rec["OntologyClassParentID"]);
		}
		

		$query = "delete from projectmanagement.OntologyClassHirarchy where OntologyClassParentID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از کلاسهای هستان نگار");
	}
	static function GetList($OntologyID)
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select OntologyClasses.OntologyClassID
				,OntologyClasses.OntologyID
				,OntologyClasses.ClassTitle 
				,(select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID) as label
				from projectmanagement.OntologyClasses  ";
		$query .= " where OntologyID=? order by label";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyID));
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_OntologyClasses();
			$ret[$k]->OntologyClassID=$rec["OntologyClassID"];
			$ret[$k]->OntologyID=$rec["OntologyID"];
			$ret[$k]->ClassTitle=$rec["ClassTitle"];
			$ret[$k]->label=$rec["label"];
			$k++;
		}
		return $ret;
	}
	
// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $ClassTitle: عنوان کلاس
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $ClassTitle)
	{
		$ret = "";
		$obj = new be_OntologyClasses();
		$obj->LoadDataFromDatabase($CurRecID);
		if($ClassTitle!=$obj->ClassTitle)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان کلاس";
		}
		return $ret;
	}
	
	/* 
	edited by: Mohammad Kahani	SID: 9512762447
	*/
	
	function ShowSummary($RecID)
	{
		$ret = "<br>";
		$ret .= "<div class=\"table-responsive container-fluid\">";
		$ret .= "<div class=\"row\">";
		$ret .= "<div class=\"col-1\"></div><div class=\"col-10\">";
		$ret .= "<tbody><tr><td><table>";
		$obj = new be_OntologyClasses();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "<tr><td width=\"1%\" nowrap><? echo C_ONTOLOGY;?></td>".$obj->OntologyTitle."</tr>";
		$ret .= "<tr><td width=\"1%\" nowrap><? echo C_T_CLASS;?></td><td>".$obj->ClassTitle."</tr>";
		$ret .= "</table></td></tr></tbody></table>";
		$ret .= "<div class=\"col-1\"></div></div></div>";
		return $ret;
	}

	function ShowTabs($RecID, $CurrentPageName)
	{
	  return "";
		$ret = "<div class='container-fluid'>";
 		$ret .= "<div class='row'>";
		$ret .= "<div class='col-1'></div>";
		$ret .= "<div class='table-responsive col-10'>";
		$ret .= "<table class='table text-center'>";
		$ret .= "<tr class='row table-borderless'>";
		$ret .= "<td class='col-md-4'>";
		if($CurrentPageName=="NewOntologyClasses")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='NewOntologyClasses.php?UpdateID=".$RecID."'><? echo C_SESSION_INFO; ?></a></td>";
		$ret .= "<td class='col-md-4'";
		if($CurrentPageName=="ManageOntologyClassLabels")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageOntologyClassLabels.php?OntologyClassID=".$RecID."'><? echo C_T_CLASS_LABELS ?></a></td>";
		$ret .= "<td class='col-md-4'";
		if($CurrentPageName=="ManageOntologyClassHirarchy")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageOntologyClassHirarchy.php?OntologyClassID=".$RecID."'><? echo C_T_HIERARCHY_ONTOLOGY_CLASSES; ?></a></td>";
		$ret .= "</table>";
		$ret .= "</div>";
		$ret .= "<div class='col-md-2'></div>";
		$ret .= "</div>";
        $ret .= "</div>";
		return $ret;
	}
	
	function GetClassIDAndLabel($OntologyID, $ClassTitle)
	{
	    $mysql = pdodb::getInstance();
	    $k=0;
	    $ret = array();
	    $query = "select OntologyClassID, label from projectmanagement.OntologyClasses
	    LEFT JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)";
	    $query .= " where OntologyID=? and ClassTitle=? order by ClassTitle";
	    //echo "<br>";
	    //echo $query;
	    //echo $OntologyID." *".$ClassTitle."*<br>";
	    $mysql->Prepare($query);
	    $res = $mysql->ExecuteStatement(array($OntologyID, $ClassTitle));
	    if($rec = $res->fetch())
	      return $rec;
	    return null;
	}
	
	function IsTwoClassRelatedToTheSameClass($ClassID1, $ClassID2, $PropertyID)
	{
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyObjectPropertyRestriction 
where DomainClassID=? and OntologyPropertyID=? and RelationStatus='VALID' and 
RangeClassID in (
select RangeClassID from projectmanagement.OntologyObjectPropertyRestriction 
 where 
DomainClassID=? and OntologyPropertyID=? 
and 
RelationStatus='VALID')";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ClassID1, $PropertyID, $ClassID2, $PropertyID));
		if($rec = $res->fetch())
			return true;

		$query = "select * from projectmanagement.OntologyObjectPropertyRestriction 
where RangeClassID=? and OntologyPropertyID=? and RelationStatus='VALID' and 
DomainClassID in (
select DomainClassID from projectmanagement.OntologyObjectPropertyRestriction 
 where 
RangeClassID=? and OntologyPropertyID=? 
and 
RelationStatus='VALID')";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ClassID1, $PropertyID, $ClassID2, $PropertyID));
		if($rec = $res->fetch())
			return true;
		return false;
	}
	
	function GetClassID($OntologyID, $ClassTitle)
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("select OntologyClassID from projectmanagement.OntologyClasses where OntologyID=? and ClassTitle=?");	
		$res = $mysql->ExecuteStatement(array($OntologyID, $ClassTitle));
		if($rec = $res->fetch())
		{
			return $rec["OntologyClassID"];
		}
		return 0;
	}
}
?>