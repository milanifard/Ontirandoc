<?php
/*
 تعریف کلاسها و متدهای مربوط به : هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-29
*/

/*
کلاس پایه: هستان نگار
*/
class be_ontologies
{
	public $OntologyID;		//
	public $OntologyTitle;		//عنوان
	public $OntologyURI;		//مسیر اینترنتی
	public $FileName;		//نام فایل
	public $FileContent;		//محتوا
	public $comment;		//یادداشت

	function be_ontologies() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select ontologies.* from projectmanagement.ontologies  where  ontologies.OntologyID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->OntologyID=$rec["OntologyID"];
			$this->OntologyTitle=$rec["OntologyTitle"];
			$this->OntologyURI=$rec["OntologyURI"];
			$this->FileName=$rec["FileName"];
			$this->FileContent=$rec["FileContent"];
			$this->comment=$rec["comment"];
		}
	}
}
/*
کلاس مدیریت هستان نگار
*/
class manage_ontologies
{
    static function LoadClassesAndWordnetSimilars($SelectedOnto)
    {
      $mysql = pdodb::getInstance();  
      $res = $mysql->Execute("select * from projectmanagement.OntologyClassLabels 
				JOIN projectmanagement.OntologyClasses using (OntologyClassID)
				JOIN projectmanagement.ontologies using (OntologyID)
				where OntologyID in (".$SelectedOnto.")
				");
      $items = array();
      $i = 0;
      while($rec = $res->fetch())
      {
	$ClassTitle = $rec["ClassTitle"];
	if(strrpos($ClassTitle, '#')>0)
	{
	  $ClassTitle = substr($ClassTitle, strrpos($ClassTitle, "#")+1, strlen($ClassTitle));
	}
	if(strrpos($ClassTitle, '/')>0)
	{
	  $ClassTitle = substr($ClassTitle, strrpos($ClassTitle, "/")+1, strlen($ClassTitle));
	}
	
	// بررسی اینکه قبلا این کلاس با همین عنوان و برچسب ثبت شده است یا خیر
	$found = false;
	for($j=0; $j<$i; $j++)
	{
	  if($items[$j]["ClassTitle"]==$ClassTitle && $items[$j]["label"]==$rec["label"])
	  {
	    $found = true;
	    break;
	  }
	}
	
	if(!$found)
	{
	  $query = "select synsetid from wordnet.senses 
				    JOIN wordnet.words using (wordid)
				    where lemma='".$ClassTitle."'";
	  $sres = $mysql->Execute($query);
	  $synsetlist = "";
	  while($srec = $sres->fetch())
	  {
	    if($synsetlist!="")
	      $synsetlist .= ", ";
	    $synsetlist .= $srec["synsetid"];
	  }
	  $WordnetSimilars = "";      
	  if($synsetlist!="")
	  {
	    $query = "select OntologyClasses.OntologyClassID, lemma, label, OntologyClassLabelID from wordnet.senses 
				      JOIN wordnet.words using (wordid)
				      JOIN projectmanagement.OntologyClasses on (ClassTitle=lemma COLLATE utf8_persian_ci)
				      JOIN projectmanagement.OntologyClassLabels on (OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID)
				      where synsetid in (".$synsetlist.")
				      and lemma<>'".$ClassTitle."'";
	    $wres = $mysql->Execute($query);
	    while($wrec = $wres->fetch())
	    {
	      if($WordnetSimilars!="")
		$WordnetSimilars .= ", ";
	      $WordnetSimilars .= "<a target=_blank href='ManageOntologyClassLabels.php?UpdateID=".$wrec["OntologyClassLabelID"]."&OntologyClassID=".$wrec["OntologyClassID"]."'>";
	      $WordnetSimilars .= $wrec["lemma"]."</a>: ".$wrec["label"];
	    }
	  }
	  $items[$i]["WordnetSimilars"] = $WordnetSimilars;
	}
	  
	if(!$found)
	{
	  $items[$i]["LabelID"] = $rec["OntologyClassLabelID"];
	  $items[$i]["ClassID"] = $rec["OntologyClassID"];
	  $items[$i]["OntologyID"] = $rec["OntologyID"];
	  $items[$i]["OntologyTitle"] = $rec["OntologyTitle"];
	  $items[$i]["ClassTitle"] = $ClassTitle;

	  $items[$i]["label"] = $rec["label"];
	  $i++;
	}
      }
      return $items;
    }

    static function LoadPropertiesAndWordnetSimilars($SelectedOnto)
    {
      $mysql = pdodb::getInstance();  
      $res = $mysql->Execute("select * from projectmanagement.OntologyPropertyLabels 
				JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
				JOIN projectmanagement.ontologies using (OntologyID)
				where OntologyID in (".$SelectedOnto.")
				");
      $items = array();
      $i = 0;
      while($rec = $res->fetch())
      {
	$PropertyTitle = $rec["PropertyTitle"];
	if(strrpos($PropertyTitle, '#')>0)
	{
	  $PropertyTitle = substr($PropertyTitle, strrpos($PropertyTitle, "#")+1, strlen($PropertyTitle));
	}
	if(strrpos($PropertyTitle, '/')>0)
	{
	  $PropertyTitle = substr($PropertyTitle, strrpos($PropertyTitle, "/")+1, strlen($PropertyTitle));
	}
	
	// بررسی اینکه قبلا این کلاس با همین عنوان و برچسب ثبت شده است یا خیر
	$found = false;
	for($j=0; $j<$i; $j++)
	{
	  if($items[$j]["PropertyTitle"]==$PropertyTitle && $items[$j]["label"]==$rec["label"])
	  {
	    $found = true;
	    break;
	  }
	}
	
	if(!$found)
	{
	  $query = "select synsetid from wordnet.senses 
				    JOIN wordnet.words using (wordid)
				    where lemma='".$PropertyTitle."'";
	  $sres = $mysql->Execute($query);
	  $synsetlist = "";
	  while($srec = $sres->fetch())
	  {
	    if($synsetlist!="")
	      $synsetlist .= ", ";
	    $synsetlist .= $srec["synsetid"];
	  }
	  $WordnetSimilars = "";      
	  if($synsetlist!="")
	  {
	    $query = "select OntologyProperties.OntologyPropertyID, lemma, label, OntologyPropertyLabelID from wordnet.senses 
				      JOIN wordnet.words using (wordid)
				      JOIN projectmanagement.OntologyProperties on (PropertyTitle=lemma COLLATE utf8_persian_ci)
				      JOIN projectmanagement.OntologyPropertyLabels on (OntologyPropertyLabels.OntologyPropertyID=OntologyProperties.OntologyPropertyID)
				      where synsetid in (".$synsetlist.")
				      and lemma<>'".$PropertyTitle."'";
	    $wres = $mysql->Execute($query);
	    
	    while($wrec = $wres->fetch())
	    {
	      if($WordnetSimilars!="")
		$WordnetSimilars .= ", ";
	      $WordnetSimilars .= "<a target=_blank href='ManageOntologyPropertyLabels.php?UpdateID=".$wrec["OntologyPropertyLabelID"]."&OntologyPropertyID=".$wrec["OntologyPropertyID"]."'>";
	      $WordnetSimilars .= $wrec["lemma"]."</a>: ".$wrec["label"];
	    }
	  }
	  $items[$i]["WordnetSimilars"] = $WordnetSimilars;
	}
	  
	if(!$found)
	{
	  $items[$i]["LabelID"] = $rec["OntologyPropertyLabelID"];
	  $items[$i]["PropertyID"] = $rec["OntologyPropertyID"];
	  $items[$i]["OntologyID"] = $rec["OntologyID"];
	  $items[$i]["OntologyTitle"] = $rec["OntologyTitle"];
	  $items[$i]["PropertyTitle"] = $PropertyTitle;

	  $items[$i]["label"] = $rec["label"];
	  $i++;
	}
      }
      return $items;
    }


    static function LoadClassesAndSimilarities($SelectedOnto, $thereshold)
    {
      $mysql = pdodb::getInstance();  
      $res = $mysql->Execute("select * from projectmanagement.OntologyClassLabels 
				JOIN projectmanagement.OntologyClasses using (OntologyClassID)
				JOIN projectmanagement.ontologies using (OntologyID)
				where OntologyID in (".$SelectedOnto.")
				");
      $items = array();
      $i = 0;
      while($rec = $res->fetch())
      {
	$ClassTitle = $rec["ClassTitle"];
	if(strrpos($ClassTitle, '#')>0)
	{
	  $ClassTitle = substr($ClassTitle, strrpos($ClassTitle, "#")+1, strlen($ClassTitle));
	}
	if(strrpos($ClassTitle, '/')>0)
	{
	  $ClassTitle = substr($ClassTitle, strrpos($ClassTitle, "/")+1, strlen($ClassTitle));
	}
	
	// بررسی اینکه قبلا این کلاس با همین عنوان و برچسب ثبت شده است یا خیر
	$found = false;
	for($j=0; $j<$i; $j++)
	{
	  if($items[$j]["ClassTitle"]==$ClassTitle && $items[$j]["label"]==$rec["label"])
	  {
	    $found = true;
	    break;
	  }
	}
	
	if(!$found)
	{
	  $items[$i]["LabelID"] = $rec["OntologyClassLabelID"];
	  $items[$i]["ClassID"] = $rec["OntologyClassID"];
	  $items[$i]["OntologyID"] = $rec["OntologyID"];
	  $items[$i]["OntologyTitle"] = $rec["OntologyTitle"];
	  $items[$i]["ClassTitle"] = $ClassTitle;

	  $items[$i]["label"] = $rec["label"];
	  $items[$i]["similars"] = "";
	  $items[$i]["similar_labels"] = "";
	  $i++;
	}
      }
      
      for($i=0; $i<count($items); $i++)
      {
	//echo "-----------------------------------------------------<br>";
	for($j=0; $j<count($items); $j++)
	{
	  if($i<>$j)
	  {
	    $distance = levenshtein($items[$i]["ClassTitle"], $items[$j]["ClassTitle"]);
	    $p = (1-($distance/max(strlen($items[$j]["ClassTitle"]), strlen($items[$i]["ClassTitle"]))) )*100;
	    // اگر متعلق به دو آنتولوژی مختلف بوده و بیش از ۸۰ درصد مطابقت داشته باشند و نام کلاس آنها مانند هم نباشد
	    if($p>$thereshold 
	    && $items[$j]["OntologyTitle"]!=$items[$i]["OntologyTitle"]
	    && $items[$j]["label"]!=$items[$i]["label"]
	    )
	    {
	      $items[$i]["similars"] .= "<a target=_blank href='ManageOntologyClassLabels.php?UpdateID=".$items[$j]["LabelID"]."&OntologyClassID=".$items[$j]["ClassID"]."'>";
	      $items[$i]["similars"] .= $items[$j]["ClassTitle"]."</a> (".$items[$j]["label"].")<br>";
	    }
	  }
	}
      }

      
      for($i=0; $i<count($items); $i++)
      {
	//echo "-----------------------------------------------------<br>";
	for($j=0; $j<count($items); $j++)
	{
	  if($i<>$j)
	  {
	    $distance = levenshtein($items[$i]["label"], $items[$j]["label"]);
	    if(max(strlen($items[$j]["label"]), strlen($items[$i]["label"]))>0)
	    $p = (1-($distance/max(strlen($items[$j]["label"]), strlen($items[$i]["label"]))) )*100;
	    else
	    $p = 0;
	    // اگر متعلق به دو آنتولوژی مختلف بوده و بیش از ۸۰ درصد مطابقت داشته باشند و برچسب فارسی آنها مانند هم نباشد
	    if($p>$thereshold 
	    && $items[$j]["ClassTitle"]!=$items[$i]["ClassTitle"]
	    && $items[$j]["OntologyTitle"]!=$items[$i]["OntologyTitle"])
	    {
	      $items[$i]["similar_labels"] .= "<a target=_blank href='ManageOntologyClassLabels.php?UpdateID=".$items[$j]["LabelID"]."&OntologyClassID=".$items[$j]["ClassID"]."'>";
	      $items[$i]["similar_labels"] .= $items[$j]["label"]."</a> (".$items[$j]["ClassTitle"].")<br>";
	    }
	  }
	}
      }
      
      return $items;
    }

    static function LoadPropertiesAndSimilarities($SelectedOnto, $thereshold)
    {
      $mysql = pdodb::getInstance();  
      $res = $mysql->Execute("select * from projectmanagement.OntologyPropertyLabels 
				JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
				JOIN projectmanagement.ontologies using (OntologyID)
				where OntologyID in (".$SelectedOnto.")
				");
      $items = array();
      $i = 0;
      while($rec = $res->fetch())
      {
	$PropertyTitle = $rec["PropertyTitle"];
	if(strrpos($PropertyTitle, '#')>0)
	{
	  $PropertyTitle = substr($PropertyTitle, strrpos($PropertyTitle, "#")+1, strlen($PropertyTitle));
	}
	if(strrpos($PropertyTitle, '/')>0)
	{
	  $PropertyTitle = substr($PropertyTitle, strrpos($PropertyTitle, "/")+1, strlen($PropertyTitle));
	}
	
	// بررسی اینکه قبلا این خصوصیت با همین عنوان و برچسب ثبت شده است یا خیر
	$found = false;
	for($j=0; $j<$i; $j++)
	{
	  if($items[$j]["PropertyTitle"]==$PropertyTitle && $items[$j]["label"]==$rec["label"])
	  {
	    $found = true;
	    break;
	  }
	}
	
	if(!$found)
	{
	  $items[$i]["LabelID"] = $rec["OntologyPropertyLabelID"];
	  $items[$i]["PropertyID"] = $rec["OntologyPropertyID"];
	  $items[$i]["OntologyID"] = $rec["OntologyID"];
	  $items[$i]["OntologyTitle"] = $rec["OntologyTitle"];
	  $items[$i]["PropertyTitle"] = $PropertyTitle;

	  $items[$i]["label"] = $rec["label"];
	  $items[$i]["similars"] = "";
	  $items[$i]["similar_labels"] = "";
	  $i++;
	}
      }
      
      for($i=0; $i<count($items); $i++)
      {
	//echo "-----------------------------------------------------<br>";
	for($j=0; $j<count($items); $j++)
	{
	  if($i<>$j)
	  {
	    $distance = levenshtein($items[$i]["PropertyTitle"], $items[$j]["PropertyTitle"]);
	    $p = (1-($distance/max(strlen($items[$j]["PropertyTitle"]), strlen($items[$i]["PropertyTitle"]))) )*100;
	    // اگر متعلق به دو آنتولوژی مختلف بوده و بیش از ۸۰ درصد مطابقت داشته باشند و نام کلاس آنها مانند هم نباشد
	    if($p>$thereshold 
	    && $items[$j]["OntologyTitle"]!=$items[$i]["OntologyTitle"]
	    && $items[$j]["label"]!=$items[$i]["label"]
	    )
	    {
	      $items[$i]["similars"] .= "<a target=_blank href='ManageOntologyPropertyLabels.php?UpdateID=".$items[$j]["LabelID"]."&OntologyPropertyID=".$items[$j]["PropertyID"]."'>";
	      $items[$i]["similars"] .= $items[$j]["PropertyTitle"]."</a> (".$items[$j]["label"].")<br>";
	    }
	  }
	}
      }

      
      for($i=0; $i<count($items); $i++)
      {
	//echo "-----------------------------------------------------<br>";
	for($j=0; $j<count($items); $j++)
	{
	  if($i<>$j)
	  {
	    $distance = levenshtein($items[$i]["label"], $items[$j]["label"]);
	    if(max(strlen($items[$j]["label"]), strlen($items[$i]["label"]))>0)
	    $p = (1-($distance/max(strlen($items[$j]["label"]), strlen($items[$i]["label"]))) )*100;
	    else
	    $p = 0;
	    // اگر متعلق به دو آنتولوژی مختلف بوده و بیش از ۸۰ درصد مطابقت داشته باشند و برچسب فارسی آنها مانند هم نباشد
	    if($p>$thereshold 
	    && $items[$j]["PropertyTitle"]!=$items[$i]["PropertyTitle"]
	    && $items[$j]["OntologyTitle"]!=$items[$i]["OntologyTitle"])
	    {
	      $items[$i]["similar_labels"] .= "<a target=_blank href='ManageOntologyPropertyLabels.php?UpdateID=".$items[$j]["LabelID"]."&OntologyPropertyID=".$items[$j]["PropertyID"]."'>";
	      $items[$i]["similar_labels"] .= $items[$j]["label"]."</a> (".$items[$j]["PropertyTitle"].")<br>";
	    }
	  }
	}
      }
      
      return $items;
    }

	static function GetClassCount($OntologyID)
	{
	    $mysql = pdodb::getInstance();
	    $query = "select count(*) as tcount from projectmanagement.OntologyClasses where OntologyID=?";	
	    $mysql->Prepare($query);
	    $res = $mysql->ExecuteStatement(array($OntologyID));
	    if($rec = $res->fetch())
	      return $rec["tcount"];
	    return 0;
	}

	static function GetPropertyCount($OntologyID)
	{
	    $mysql = pdodb::getInstance();
	    $query = "select count(*) as tcount from projectmanagement.OntologyProperties where OntologyID=?";	
	    $mysql->Prepare($query);
	    $res = $mysql->ExecuteStatement(array($OntologyID));
	    if($rec = $res->fetch())
	      return $rec["tcount"];
	    return 0;
	}
	
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(OntologyID) as TotalCount from projectmanagement.ontologies";
		if($WhereCondition!="")
		{
			$query .= " where ".$WhereCondition;
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
		$mysql = pdodb::getInstance();
		$query = "select max(OntologyID) as MaxID from projectmanagement.ontologies";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $OntologyTitle: عنوان
	* @param $OntologyURI: مسیر اینترنتی
	* @param $FileContent: 
	* @param $FileName: نام فایل
	* @param $comment: 
	* @return کد داده اضافه شده	*/
	static function Add($OntologyTitle, $OntologyURI, $FileContent, $FileName, $comment)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.ontologies (";
		$query .= " OntologyTitle";
		$query .= ", OntologyURI";
		$query .= ", FileContent";
		$query .= ", FileName";
		$query .= ", comment";
		$query .= ") values (";
		$query .= "? , ? , '".$FileContent."', ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyTitle); 
		array_push($ValueListArray, $OntologyURI); 
		array_push($ValueListArray, $FileName); 
		array_push($ValueListArray, $comment); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_ontologies::GetLastID();
		$mysql->audit("ثبت داده جدید در هستان نگار با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $OntologyTitle: عنوان
	* @param $OntologyURI: مسیر اینترنتی
	* @param $FileContent: 
	* @param $FileName: نام فایل
	* @param $comment: 
	* @return 	*/
	static function Update($UpdateRecordID, $OntologyTitle, $OntologyURI, $FileContent, $FileName, $comment)
	{
		$k=0;
		$LogDesc = manage_ontologies::ComparePassedDataWithDB($UpdateRecordID, $OntologyTitle, $OntologyURI, $FileContent, $FileName, $comment);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.ontologies set ";
			$query .= " OntologyTitle=? ";
			$query .= ", OntologyURI=? ";
		if($FileName!="") // در صورتیکه فایل ارسال شده باشد
 		{
			$query .= ", FileName=?, FileContent='".$FileContent."' ";
		}
			$query .= ", comment=? ";
		$query .= " where OntologyID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $OntologyTitle); 
		array_push($ValueListArray, $OntologyURI); 
		if($FileName!="")
		{ 
			array_push($ValueListArray, $FileName); 
		} 
		array_push($ValueListArray, $comment); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در هستان نگار - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.ontologies where OntologyID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.OntologyClassHirarchy where OntologyClassID in (select OntologyClassID from projectmanagement.OntologyClasses where OntologyID=?)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));		
		
		$query = "delete from projectmanagement.OntologyClasses where OntologyID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		
		$query = "delete from projectmanagement.OntologyProperties where OntologyID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از هستان نگار");
	}
	static function GetList()
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select ontologies.OntologyID
				,ontologies.OntologyTitle
				,ontologies.OntologyURI
				,ontologies.FileName
				,ontologies.FileContent
				,ontologies.comment from projectmanagement.ontologies  ";
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_ontologies();
			$ret[$k]->OntologyID=$rec["OntologyID"];
			$ret[$k]->OntologyTitle=$rec["OntologyTitle"];
			$ret[$k]->OntologyURI=$rec["OntologyURI"];
			$ret[$k]->FileName=$rec["FileName"];
			$ret[$k]->FileContent=$rec["FileContent"];
			$ret[$k]->comment=$rec["comment"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $OntologyTitle: عنوان
	* @param $OntologyURI: مسیر اینترنتی
	* @param $FileContent: 
	* @param $FileName: نام فایل
	* @param $comment: 
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $OntologyTitle, $OntologyURI, $FileContent, $FileName, $comment)
	{
		$ret = "";
		$obj = new be_ontologies();
		$obj->LoadDataFromDatabase($CurRecID);
		if($OntologyTitle!=$obj->OntologyTitle)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($OntologyURI!=$obj->OntologyURI)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "مسیر اینترنتی";
		}
		if($FileContent!=$obj->FileContent)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "";
		}
		if($comment!=$obj->comment)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "";
		}
		return $ret;
	}
	function ShowSummary($RecID)
	{
		$ret = "<br>";
		$ret .= "<table class=\"table table-sm table-bordered\">";
		$ret .= "<tr>";
		$ret .= "<td>";
		$ret .= "<table class=\"table table-sm table-borderless\">";
		$obj = new be_ontologies();
		$obj->LoadDataFromDatabase($RecID); 
		$ret .= "<tr><td width=10%>".C_TITLE.": </td><td>".$obj->OntologyTitle."</td></tr>";
		$ret .= "<tr><td width=10%>".C_DESCRIPTION.": </td><td>".str_replace("\n", "<br>", $obj->comment)."</td></tr>";
		$ret .= "</table>";
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</table>";
		return $ret;
	}
	function ShowTabs($RecID, $CurrentPageName)
	{
	    return "";
		$ret = "<table class=\"table table-sm table-bordered\">";
 		$ret .= "<tr>";
		$ret .= "<td width=\"25%\" ";
		if($CurrentPageName=="Newontologies")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='Newontologies.php?UpdateID=".$RecID."'>".C_SESSION_INFO."</a></td>";
		$ret .= "<td width=\"25%\" ";
		if($CurrentPageName=="Manage")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='Manage.php?OntologyID=".$RecID."'></a></td>";
		$ret .= "<td width=\"25%\" ";
		if($CurrentPageName=="ManageOntologyClasses")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageOntologyClasses.php?OntologyID=".$RecID."'>".C_ONTOLOGY_CLASSES."</a></td>";
		$ret .= "<td width=\"25%\" ";
		if($CurrentPageName=="ManageOntologyProperties")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageOntologyProperties.php?OntologyID=".$RecID."'>".C_ONTOLOGY_FEATURES."</a></td>";
		$ret .= "</table>";
		return $ret;
	}
}
?>