<?
	include("header.inc.php");

	function GenerateMergeSimilarPropertySuggestions($TargetOnto, $label, $PropertyType)
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare("select * from projectmanagement.OntologyProperties 
		      JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID) 
		      where OntologyID=? and label='".$label."' and PropertyType='".$PropertyType."'");
	  $res = $mysql->ExecuteStatement(array($TargetOnto));
	  $SimilarList = array();
	  $i = 0;
	  while($rec = $res->fetch())
	  {
	    $SimilarList[$i] = $rec["OntologyPropertyID"];
	    $i++;
	  }

	  if($PropertyType=="DATATYPE")
	  {
	    $EntityType1 = $EntityType2 = "DATAPROP";
	  }
	  else 
	  {
	    $EntityType1 = $EntityType2 = "OBJPROP";  
	  }
	  for($i=0; $i<count($SimilarList); $i++)
	  {
	  	for($j=$i+1; $j<count($SimilarList); $j++)
	  	{
	  		$EntityID1 = $SimilarList[$i];
	  		$EntityID2 = $SimilarList[$j];
		    $query = "insert into projectmanagement.OntologyMergeReviewedPotentials (EntityID1, EntityID2, EntityType1, EntityType2, ActionType, SimilartyType, TargetOntologyID) 
			      values ('".$EntityID1."', '".$EntityID2."', '".$EntityType1."', '".$EntityType2."', 'NOT_DECIDE', 'SAME_LABEL', ?)";
		    $mysql->Prepare($query);
		    
		    $mysql->ExecuteStatement(array($TargetOnto));
		  		
	  	}
	  }	  
	}

	function GenerateMergePropertySuggestions($TargetOnto)
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare("select label, count(*) from projectmanagement.OntologyProperties 
			  JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID) 
			  where OntologyID=? and label<>'' and label<>'کد'
			  group by label
			  having count(*)>1
			  order by count(*) desc");
	  $res = $mysql->ExecuteStatement(array($TargetOnto));
	  while($rec = $res->fetch())
	  {
	      GenerateMergeSimilarPropertySuggestions($TargetOnto, $rec["label"], "DATATYPE");
	      GenerateMergeSimilarPropertySuggestions($TargetOnto, $rec["label"], "OBJECT");
	  }
	}
	
	function RemovePreviousData($TargetOnto)
	{
	  $mysql = pdodb::getInstance();

	  $query = "delete from projectmanagement.RDB2OntoLog where TargetOntologyID=?";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($TargetOnto));
	  
	  $query = "delete from projectmanagement.OntologyClassLabels where OntologyClassID in (select OntologyClassID from projectmanagement.OntologyClasses where OntologyID=?)";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($TargetOnto));

	  $query = "delete from projectmanagement.OntologyClasses where OntologyID=?";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($TargetOnto));

	  $query = "delete from projectmanagement.OntologyPropertyLabels where OntologyPropertyID in (select OntologyPropertyID from projectmanagement.OntologyProperties where OntologyID=?)";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($TargetOnto));
	  
	  $query = "delete from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID in (select OntologyPropertyID from projectmanagement.OntologyProperties where OntologyID=?)";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($TargetOnto));
	  
	  $query = "delete from projectmanagement.OntologyProperties where OntologyID=?";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($TargetOnto));
	}

	function CreateDomainFilterCondition()
	{
	  $SystemRelatedDomain = $SupportDomain = $ResearchDomain = $StudentServiceDomain = $EducationalDomain = "NO";
	  if(isset($_REQUEST["ch_educ"]))
	    $EducationalDomain = "YES";
	  if(isset($_REQUEST["ch_research"]))
	    $ResearchDomain = "YES";
	  if(isset($_REQUEST["ch_student"]))
	    $StudentServiceDomain = "YES";
	  if(isset($_REQUEST["ch_support"]))
	    $SupportDomain = "YES";
	  if(isset($_REQUEST["ch_system"]))
	    $SystemRelatedDomain = "YES";
	  $cond = "";  
	  
	  if($EducationalDomain=="YES")
	    $cond = "EducationalDomain='YES' ";
	  if($ResearchDomain=="YES")
	  {
	    if($cond!="") $cond .= " OR ";
	    $cond .= "ResearchDomain='YES'";
	  }
	  if($StudentServiceDomain=="YES")
	  {
	    if($cond!="") $cond .= " OR ";
	    $cond .= "StudentServiceDomain='YES'";
	  }
	  if($SupportDomain=="YES")
	  {
	    if($cond!="") $cond .= " OR ";
	    $cond .= "SupportDomain='YES'";
	  }
	  if($SystemRelatedDomain=="YES")
	  {
	    if($cond!="") $cond .= " OR ";
	    $cond .= "SystemRelatedDomain='YES'";
	  }
	  if($cond!="")
	  {
	    $cond = " AND (".$cond.") ";
	    // اگر سیستمی را انتخاب نکرده باشد نمی خواهیم در فهرستی باشد
	    if($SystemRelatedDomain == "NO")
	    {
	      $cond .= " AND SystemRelatedDomain='NO' "; 
	    }
	  }  
	  return $cond;
	}
	
	function GenerateSQLForPotentialClassesRelatedTables()
	{
	// جداولی که حداقل یک فیلد غیر کلید دارند که کلید خارجی به جدول دیگری نباشد به عنوان کلاس شناخته می شوند
	// اگر کلید خارجی به جدول کدینگ باشد آن را به عنوان فیلد ارتباطی در نظر نمی گیرد (اصلاح شده)
	// تجمیع کلاسها بعدا در مرحله ادغام صورت می گیرد
	// شرط کدینگ نبودن جدول هم اضافه شد یعنی از آن جدول صرفا برای توصیف شرح کدها استفاده نشده باشد
	  $query = "select distinct MIS_Tables.* from mis.MIS_TableFields 
		  JOIN mis.MIS_Tables on (TableStatus='ENABLE' and TableName=name and MIS_TableFields.DBName=MIS_Tables.DBName)
		  where 
		  (RelatedTable is null or RelatedTable='' or RelatedTable='domains' or RelatedTable='Basic_Info' 
or concat(RelatedDBName,'.',RelatedTable) in (select concat(DBName,'.',TableName) from mis.MIS_CodingTables)) 
		  and KeyType<>'PRI' and MIS_TableFields.EnableField='YES' ".CreateDomainFilterCondition();
	  $query .= " and concat(MIS_Tables.DBName,'.',MIS_Tables.name) not in (select concat(DBName,'.',TableName) from mis.MIS_CodingTables) ";
		  
	  return $query;
	}

	function InsertClass($TargetOnto, $ClassName, $ClassLabel)
	{
	  $mysql = pdodb::getInstance();
	  $query = "insert into projectmanagement.OntologyClasses (ClassTitle, OntologyID) values ('".$ClassName."', ?)";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($TargetOnto));
	  $res2 = $mysql->Execute("select max(OntologyClassID) as MaxID from projectmanagement.OntologyClasses");
	  $rec2 = $res2->fetch();
	  $query = "insert into projectmanagement.OntologyClassLabels (OntologyClassID, label) values ('".$rec2["MaxID"]."', '".$ClassLabel."')";
	  $mysql->Execute($query);
	  return $rec2["MaxID"];
	}
	
	function InsertProperty($TargetOnto, $PropertyTitle, $PropertyLabel, $PropertyType, $domain, $range)
	{
	  $mysql = pdodb::getInstance();
	  $query = "insert into projectmanagement.OntologyProperties (OntologyID, PropertyTitle, PropertyType, domain, `range`) ";
	  $query .= " values (?, '".$PropertyTitle."', '".$PropertyType."', '".$domain."', '".$range."')";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($TargetOnto));
	  $res2 = $mysql->Execute("select max(OntologyPropertyID) as MaxID from projectmanagement.OntologyProperties");
	  $rec2 = $res2->fetch();
	  $query = "insert into projectmanagement.OntologyPropertyLabels (OntologyPropertyID, label) values ('".$rec2["MaxID"]."', '".$PropertyLabel."')";
	  $mysql->Execute($query);
	  return $rec2["MaxID"];
	}
	
	function InsertPermittedValue($PropertyID, $PermittedValue)
	{
	  if($PermittedValue=="" || $PermittedValue=="نامشخص")
	  	return;
	  $mysql = pdodb::getInstance();	
	  $query = "insert into projectmanagement.OntologyPropertyPermittedValues (OntologyPropertyID, PermittedValue) values ('".$PropertyID."', '".$PermittedValue."')";
	  $mysql->Execute($query);
	}
	
	function InsertLog($OntoEntityID, $OntoEntityType, $RDBEntityID, $RDBEntityType, $TargetOnto)
	{
	  $mysql = pdodb::getInstance();	
	  $query = "insert into projectmanagement.RDB2OntoLog (OntoEntityID, OntoEntityType, RDBEntityID, RDBEntityType, TargetOntologyID) 
		    values (?, ?, ?, ?, ?)";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($OntoEntityID, $OntoEntityType, $RDBEntityID, $RDBEntityType, $TargetOnto));
	}
	
	// مشخص می کند آیا به فیلد مربوطه ارجاعی از سایر جداول وجود دارد یا خیر
	function ReferExist($DBNameName, $TableName, $FieldName, $FieldType)
	{
		// آگر فیلد نوع عددی نبود بقیه مسیر را نمی رود و فرض می کند به آن ارجاع نشده است
		// موارد خاصی که ارجاع به فیلدهای غیر عددی دارند آن فیلد یک ویژگی اصلی است مثل شابک مجله
		$pos = strpos($FieldType, "int");
		if($pos===false)
			return false;
		$mysql = pdodb::getInstance();	
		$query = "select count(*) as tcount from mis.MIS_TableFields where 
		(RelatedDBName=? and RelatedTable=? and RelatedField=?) or
		(RelatedDBName2=? and RelatedTable2=? and RelatedField2=?) or
		(RelatedDBName3=? and RelatedTable3=? and RelatedField3=?) ";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($DBNameName, $TableName, $FieldName, $DBNameName, $TableName, $FieldName, $DBNameName, $TableName, $FieldName));
		$rec = $res->fetch();
		if($rec["tcount"]>0)
		{
			echo $DBNameName.".".$TableName.".".$FieldName."<br>";
			return true;
		}
		return false;
	}
	
	// فهرست جداولی که به عنوان کدینگ تعریف شده اند در یک ساختار بر می گرداند
	function GetCodingTablesInfo()
	{
	  $ret = array();
	  $mysql = pdodb::getInstance();	
	  $res = $mysql->Execute("select * from mis.MIS_CodingTables");
	  $i = 0;
	  while($rec = $res->fetch())
	  {
	    $ret[$i]["DBName"] = $rec["DBName"];
	    $ret[$i]["TableName"] = $rec["TableName"];
	    $ret[$i]["CodeFieldName"] = $rec["CodeFieldName"];
	    $ret[$i]["DescriptionFieldName"] = $rec["DescriptionFieldName"];
	    
	    //$ret[$i]["Values"][0] = "دیتابیس موجود نیست";
	    
	    $res2 = $mysql->Execute("select * from ".$rec["DBName"].".".$rec["TableName"]);
	    $j = 0;
	    while($rec2 = $res2->fetch())
	    {
	      $ret[$i]["Values"][$j++] = $rec2[$rec["DescriptionFieldName"]];
	    }
	    
	    $i++;
	  }
	  return $ret;
	}
	
	// آیا جدول مربوطه یک جدول کدینگ است
	// اگر بود عددی بزرگتر مساوی صفر بر می گرداند
	function CodingTableIndex($CodingTableList, $DBName, $TableName)
	{
	  for($i=0; $i<count($CodingTableList); $i++)
	  {
	    if($CodingTableList[$i]["DBName"]==$DBName && $CodingTableList[$i]["TableName"]==$TableName)
	    {
	      return $i;
	    }
	  }
	  return -1;
	}
	
	function ApplyRule1And7($TargetOnto)
	{
	  $CodingTableList = GetCodingTablesInfo();
	  $mysql = pdodb::getInstance();	
	  //echo GenerateSQLForPotentialClassesRelatedTables();
	  $res = $mysql->Execute(GenerateSQLForPotentialClassesRelatedTables());

	  while($rec = $res->fetch())
	  {
	    $ClassName = $rec["DBName"]."_".$rec["name"];
	    $ClassLabel = $rec["description"];
	    $ClassID = InsertClass($TargetOnto, $ClassName, $ClassLabel);
	    InsertLog($ClassID, "CLASS", $rec["id"], "TABLE", $TargetOnto);
	    
	    // بررسی فیلدهای جدول و نگاشت آنها به خصوصیات داده یا شیء
	    $fres = $mysql->Execute("select * from mis.MIS_TableFields where DBName='".$rec["DBName"]."' and TableName='".$rec["name"]."' and EnableField='YES'");
	    while($frec = $fres->fetch())
	    {
	      $PropertyTitle = $frec["TableName"]."_".$frec["FieldName"];
	      $PropertyLabel = $frec["description"];
	      
	      //echo $PropertyTitle."<br>";
	      
	      $CodingTableIndex = -1;
	      if($frec["RelatedTable"]!="")
	      {
		//echo "[1]";
	      	// اگر فیلد به جایی ارجاع داده باشد بررسی می شود که آیا جدول مربوطه کدینگ است یا خیر
	      	// شماره ایندکس آن جدول در فهرست جداول کدینگ به دست می آید
		 $CodingTableIndex = CodingTableIndex($CodingTableList, $frec["RelatedDBName"], $frec["RelatedTable"]);
		 //echo "[1-1]";
	      }
	      
	      if($frec["RelatedTable"]=="") // اگر کلید خارجی نبود یک خصوصیت داده است
	      {
		//echo "[2]";
	      	// اگر فیلد کلید اصلی جدول بود یا ارجاعی از سایر جداول به آن وجود داشت به عنوان یک خصوصیت اضافه نمی شود
	      	if($frec["KeyType"]!="PRI" && !ReferExist($frec["DBName"], $frec["TableName"], $frec["FieldName"], $frec["FieldType"]))
	      	{
		  //echo "[3]";
			$PropertyID = InsertProperty($TargetOnto, $PropertyTitle, $PropertyLabel, "DATATYPE", $ClassName, "");
		//echo "[3-1]";
			InsertLog($PropertyID, "DATAPROP", $frec["id"], "FIELD", $TargetOnto);
		//echo "[3-2]";
	
			// بررسی اینکه آیا بازه مقادیر فیلد مشخص شده است - موارد شمارشی
			$query = "select * from mis.FieldsDataMapping where DBName='".$rec["DBName"]."' and TableName='".$rec["name"]."' and FieldName='".$frec["FieldName"]."'";
			$pres = $mysql->Execute($query);
		//echo "[3-3]";
			while($prec = $pres->fetch())
			{
			//echo "[3-4]";
			  InsertPermittedValue($PropertyID, $prec["ShowValue"]);
			}
		}
	      }
	      // این جداول کدینگ هستند و به عنوان مقادیر مجاز برای یک خصوصیت داده تعریف می شوند
	      // با وجود اینکه در حالت عام به عنوان کلید خارجی و خصوصیت شیء به آن باید شناخته شوند
	      else if($frec["RelatedTable"]=="domains" || $frec["RelatedTable"]=="Basic_Info") 
	      {
		//echo "[4]";
		$PropertyID = InsertProperty($TargetOnto, $PropertyTitle, $PropertyLabel, "DATATYPE", $ClassName, "");
		InsertLog($PropertyID, "DATAPROP", $frec["id"], "FIELD", $TargetOnto);
		// برداشتن مقادیر مجاز از جدول کدینگ مربوطه
		if($frec["RelatedTable"]=="domains")
		  $DescField = "description";
		else
		  $DescField = "Title";
		if($frec["RelationCondition"]!="")
		{
		  $query = "select ".$DescField." from ".$frec["RelatedDBName"].".".$frec["RelatedTable"]." where ".$frec["RelationCondition"];
		  $pres = $mysql->Execute($query);
		  while($prec = $pres->fetch())
		  {
		    InsertPermittedValue($PropertyID, $prec[$DescField]);
		  }
		}
	      }
	      else if($CodingTableIndex>-1) // کلید به یک جدول کدینگ است بنابراین خصوصیت داده با مقادیر محدود شده است
	      {
		//echo $CodingTableList[$CodingTableIndex]["TableName"]."<br>";
		$PropertyID = InsertProperty($TargetOnto, $PropertyTitle, $PropertyLabel, "DATATYPE", $ClassName, "");
		InsertLog($PropertyID, "DATAPROP", $frec["id"], "FIELD", $TargetOnto);
		// اضافه کردن مقادیر مجاز
		// اگر تعداد مقادیر مجاز بیش از ۵۰ شد آنها را ثبت نکند
		  if(count($CodingTableList[$CodingTableIndex]["Values"])<80)
		  for($i=0; $i<count($CodingTableList[$CodingTableIndex]["Values"]); $i++)
		  {
		    InsertPermittedValue($PropertyID, $CodingTableList[$CodingTableIndex]["Values"][$i]);
		  }
	      }
	      else
	      {
	      // در صورتیکه فیلد کلید خارجی باشد به عنوان یک خصوصیت شیء در نظر گرفته می شود
		$RelatedClassName = $frec["RelatedDBName"]."_".$frec["RelatedTable"];
		$PropertyID = InsertProperty($TargetOnto, $PropertyTitle, $PropertyLabel, "OBJECT", $ClassName, $RelatedClassName);
		InsertLog($PropertyID, "OBJPROP", $frec["id"], "FIELD", $TargetOnto);
	      }
	    }
	  }
	}
	
	
	function ApplyRule2And5($TargetOnto)
	{
	// جداول واسط بین سایر جداول
	
	  $mysql = pdodb::getInstance();
	  $CodingTableList = GetCodingTablesInfo();
	  $cond = CreateDomainFilterCondition();
	  $query = "select * from mis.MIS_Tables where TableStatus='ENABLE' ".$cond;
	  $res = $mysql->Execute($query);
	  while($rec = $res->fetch())
	  {
	    $PropertyTitle = $rec["DBName"]."_".$rec["name"];
	    $PropertyLabel = $rec["description"];
	  
	    $RelationConunts = 0;
	    $sw = true;
	    $RelationArray = array();
	    $fres = $mysql->Execute("select * from mis.MIS_TableFields where DBName='".$rec["DBName"]."' and TableName='".$rec["name"]."' and EnableField='YES'");
	    while($frec = $fres->fetch())
	    {
	      
	      // اگر فیلد غیر کلید خارجی پیدا شود. این جدول جزوجداول رابطه دوتایی در نظر گرفته نمی شود
	      if($frec["RelatedTable"]=="" && $frec["KeyType"]!="PRI")
	      {
		$sw = false;
		break;
	      }
	      // اگر کلید خارجی یافت شده از نوع کدینگ باشد در واقع یک خصوصیت شیء است و در نظر گرفته نمی شود
	      if($frec["RelatedTable"]=="domains" || $frec["RelatedTable"]=="Basic_Info" || CodingTableIndex($CodingTableList, $frec["RelatedDBName"], $frec["RelatedTable"])>-1)
	      {
		$sw = false;
		break;
	      }
	      
	      if($frec["RelatedTable"]!="")
	      {
	      // نکته: ممکن است جدول رابطه ای دومین یا کدینگ باشد
		$RelationArray[$RelationConunts]["RelatedDBName"] = $frec["RelatedDBName"];
		$RelationArray[$RelationConunts]["RelatedTableName"] = $frec["RelatedTable"];

		$RelationConunts++;
	      }
	    }
	    /*
	    if($rec["name"]=="WorkshopPapers")
	    {
	    	print_r($RelationArray);
	    	echo "<br>";
	    }
	    */
	    if($sw) // یعنی هیچ فیلد غیر کلید خارجی ندارد
	    {
	      $DomainClasses = $RangeClasses = "";
	    // در این حالت باید جدول تبدیل به یک خصوصیت شیء شود که ارتباط بین کلاسهای متناظر هر یک از جداولی که به آنها لینک شده برقرار کند
	    // تمام حالات ارتباط بین کلاسهای متناظر را به دست می آورد و آنها را به عنوان حوزه و برد خصوصیت شیء پر می کند
		for($i=0; $i<count($RelationArray)-1; $i++) 
		{
			if($DomainClasses!="")
		    		$DomainClasses .= ", ";
			$DomainClasses .= $RelationArray[$i]["RelatedDBName"]."_".$RelationArray[$i]["RelatedTableName"];
		}
		for($j=1; $j<count($RelationArray); $j++)
		{
		  	if($RangeClasses!="")
		    		$RangeClasses .= ", ";
		  	$RangeClasses .= $RelationArray[$j]["RelatedDBName"]."_".$RelationArray[$j]["RelatedTableName"];
		}
		      
	      //echo $PropertyTitle."<br>";
	      $PropertyID = InsertProperty($TargetOnto, $PropertyTitle, $PropertyLabel, "OBJECT", $DomainClasses, $RangeClasses);
	      InsertLog($PropertyID, "OBJPROP", $frec["id"], "FIELD", $TargetOnto);
	    }
	  }
	}
	
	function GetClassID($ClassTitle, $TargetOnto)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select OntologyClassID from projectmanagement.OntologyClasses 
			where ClassTitle=? and OntologyID=?";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($ClassTitle, $TargetOnto));
	  if($rec = $res->fetch())
	  {
	    return $rec["OntologyClassID"];
	  }
	  return 0;
	}
	
	
	// پیشنهادات تجمیع را در جدول مربوط به ادغامها اضافه می کند
	// ممکن است رابطه سلسله مراتبی یا تجمیع باشد
	function ApplyRule3And6($TargetOnto)
	{
	  $mysql = pdodb::getInstance();	
	  $cond = CreateDomainFilterCondition();

	  // کلیدهای اصلی که خودشان کلید خارجی نیستند را به دست می آورد
	  // برای کلیدهای unique هم مانند کلید اصلی برخورد شود (مثل شماره دانشجویی)
	  $query = "select MIS_TableFields.* from mis.MIS_Tables
		    JOIN mis.MIS_TableFields on 
		    (MIS_Tables.DBName=MIS_TableFields.DBName and 
		    MIS_Tables.name=MIS_TableFields.TableName and 
		    (KeyType='PRI' or KeyType='UNI' or KeyType='MUL') and
		    RelatedTable='')
		    where TableStatus='ENABLE' and EnableField='YES' ".$cond;
// این قسمت اختصاصی جدول خاص اطلاعات شخصی دانشجو ایجاد شد چون به صورت غیر معمول آن را اطلاعات فردی لینک کردیم
// در سامانه واقعی این دو جدول مستقل از هم هستند ولی باید مفهومی یکی در نظر گرفته شوند
	  $query .= "union select * from mis.MIS_TableFields where EnableField='YES' and DBName='educ' and TableName='persons' and FieldName='PersonID'";
	  $res = $mysql->Execute($query);
	  while($rec = $res->fetch())
	  {
	  // بررسی می کند آیا از کلید اصلی سایر جداول به این کلید اصلی اتصالی وجود دارد یا نه. اگر باشد امکان تجمیع یا رابطه سلسله مراتبی وجود دارد
	  // جدولی که کلید اصلی آن کلید خارجی نیست جدول پایه است
	  // ممکن است در سایر جداول کلید اصلی به این جدول لینک نشده باشد بلکه یک کلید unique لینک شده باشد که باز هم همان تعبیر را دارد
	    $MainClassTitle = $rec["DBName"]."_".$rec["TableName"];
	    $query = "select * from mis.MIS_TableFields where 
		      RelatedField='".$rec["FieldName"]."' and 
		      RelatedTable='".$rec["TableName"]."' and 
		      RelatedDBName='".$rec["DBName"]."' and 
		      (KeyType='PRI' or KeyType='UNI' or KeyType='MUL')  and EnableField='YES' ";
	    //echo $query."<br>";
	    $ores = $mysql->Execute($query);
	    if($ores->rowCount()>0)
	    {
	      $MainClassID = GetClassID($MainClassTitle, $TargetOnto);
	      while($orec = $ores->fetch())
	      {
	      // زمانیکه پیشنهاد ادغام دو کلاس را اضافه می کند نام خصوصیت شیء مربوطه را هم 
	      // اضافه می کند تا در صورت ادغام یا ایجاد رابطه سلسله مراتبی خصوصیت شیء مربوطه حذف شود
		$PotentialPartOfClassID = GetClassID($orec["DBName"]."_".$orec["TableName"], $TargetOnto);
// ممکن است کلاس پیشنهادی برای ادغام به دلیل مرتبط نبودن جدول اصلا اضافه نشده باشد
		if($PotentialPartOfClassID>0)
		{
			$query = "insert into projectmanagement.OntologyMergeReviewedPotentials (EntityID1, EntityID2, EntityType1, EntityType2, ActionType, SimilartyType, TargetOntologyID, ExtraInfo, ExtraInfo2) 
				  values ('".$MainClassID."', '".$PotentialPartOfClassID."', 'CLASS', 'CLASS', 'NOT_DECIDE', 'SAME_PARENT', ?, '".$orec["description"]."', '".$orec["TableName"]."_".$orec["FieldName"]."')";
				  // به انتهای اینفو 2 نوع کلید هم اضافه شده بود که چون از این فیلد استفاده عملیاتی می شود برداشته شد
				  //  (".$orec["KeyType"].")
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array($TargetOnto));
		}
	      }
	    }
	  }
	}
	
	function ApplyObjectPropertyRestrictions($TargetOnto)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.OntologyObjectPropertyRestriction
		where OntologyPropertyID in (select OntologyPropertyID from projectmanagement.OntologyProperties where OntologyID=?)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($TargetOnto));

		$query = "insert into projectmanagement.OntologyObjectPropertyRestriction
		(OntologyPropertyID, DomainClassID, RangeClassID, RelationStatus) select OntologyPropertyID, d.OntologyClassID as DomainID, r.OntologyClassID as RangeID, 'VALID' from projectmanagement.OntologyProperties 
		JOIN projectmanagement.OntologyClasses d on (d.ClassTitle=domain and d.OntologyID=?)
		JOIN projectmanagement.OntologyClasses r on (r.ClassTitle=`range` and r.OntologyID=?)
		where OntologyProperties.OntologyID=? and PropertyType='OBJECT'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($TargetOnto, $TargetOnto, $TargetOnto));
	}
	
	function RemovePreviousMergeSuggestions($TargetOnto)
	{
	  $mysql = pdodb::getInstance();
	  $query = "delete from projectmanagement.OntologyMergeReviewedPotentials where TargetOntologyID=?";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($TargetOnto));
	}
	
	
	$mysql = pdodb::getInstance();
	HTMLBegin();
	if(!isset($_REQUEST["TargetOnto"]))
	{
	  ?>
	  <form method=post>
	  <table width=50% align=center border=1 cellspacing=0>
	  <tr class=HeaderOfTable>
	    <td align=center>انتخاب شرایط برای مهندسی معکوس</td>
	  </tr>
	  <tr>
	    <td>
	    حوزه های مورد نظر: 
	    <br><input type=checkbox id=ch_educ name=ch_educ >آموزشی
	    <br><input type=checkbox id=ch_research name=ch_research  >پژوهشی
	    <br><input type=checkbox id=ch_student name=ch_student >خدمات دانشجویی (رفاهی - فرهنگی)
	    <br><input type=checkbox id=ch_support name=ch_support >پشتیبانی (اداری/مالی)
	    <br><input type=checkbox id=ch_system name=ch_system >مرتبط با عملیات سیستمی
	    </td>
	  </tr>
	  <tr>
	    <td>
	    هستان نگار مقصد:
	    <select id=TargetOnto name=TargetOnto>
	    <?
	      $res = $mysql->Execute("select OntologyID, OntologyTitle, comment from projectmanagement.ontologies order by OntologyID DESC");
	      while($rec = $res->fetch())
	      {
		echo "<option value='".$rec["OntologyID"]."'>";
		echo $rec["OntologyTitle"]." (".substr($rec["comment"],0,80).")";
	      }
	    ?>
	    </select>
	    </td>
	  </tr>
	  <tr>
	    <td>
	      <input type=checkbox name=ch_RemovePMS id=ch_RemovePMS checked> حذف پیشنهادهای ادغام قبلی
	      <br><input checked type=checkbox name=ch_RemovePD id=ch_RemovePD> حذف عناصر موجود در هستان نگار
	      <BR>
	      <!---
	      <br><input checked type=checkbox name=ch_Rule1 id=ch_Rule1> اعمال قانون اول و هفتم: تبدیل جداول به کلاس، فیلدها به خصوصیت داده و شیء و محدودیت مقادیر مجاز
	      <br><input checked type=checkbox name=ch_Rule2 id=ch_Rule2> اعمال قانون دوم و پنجم: روابط چندتایی و دوتایی و تبدیل به خصوصیات شیء
	      <br><input checked type=checkbox name=ch_Rule6 id=ch_Rule6>اعمال قانون ششم: ثبت پیشنهاد تجمیع کلاسها
	      <br><input checked type=checkbox name=ch_PropMerge id=ch_PropMerge> ثبت پیشنهاد ادغام خصوصیتها - بر اساس مشابهت ساختاری و معنایی برچسب عناصر هستان نگار
	      --->
	    </td>
	  </tr>
	  <tr>
	    <td align=center><input type=submit value='انجام مهندسی معکوس'>
	    <br>
	    &nbsp;
	    <input type=button value='بررسی پیشنهاد ادغام خصوصیت ها' onclick='javascript: window.open("OntologyMergeProperties.php?TargetOnto="+document.getElementById("TargetOnto").value);'>
	    &nbsp;
	    <input type=button value='بررسی پیشنهادهای تجمیع/روابط سلسله مراتبی بین کلاس ها' onclick='javascript: window.open("OntologyMergeClasses.php?TargetOnto="+document.getElementById("TargetOnto").value);'>
	    </td>
	  </tr>
	  </table>
	  </form>
	  <? die();
	}
	
	$TargetOnto = $_REQUEST["TargetOnto"];
	if(isset($_REQUEST["ch_RemovePMS"]))
	  RemovePreviousMergeSuggestions($TargetOnto);
	if(isset($_REQUEST["ch_RemovePD"]))
	  RemovePreviousData($TargetOnto);
	  
	//if(isset($_REQUEST["ch_Rule1"]))
	  ApplyRule1And7($TargetOnto);
	//if(isset($_REQUEST["ch_Rule2"]))
	  ApplyRule2And5($TargetOnto);
	//if(isset($_REQUEST["ch_Rule6"]))
	  ApplyRule3And6($TargetOnto);
	//if(isset($_REQUEST["ch_PropMerge"]))
	  GenerateMergePropertySuggestions($TargetOnto);
	//if(isset($_REQUEST["ch_Rule1"]))
	{
		// برای کلاسهای شیء ساخته شده باید رابطه بین حوزه و برد را مجاز ثبت کند
		ApplyObjectPropertyRestrictions($TargetOnto);
	}
	echo "<p align=center>تبدیل انجام شد. </p>";
?>
</body></html>