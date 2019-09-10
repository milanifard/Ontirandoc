<?
  $NotAddSlashes = "1";
  include("header.inc.php");
//phpinfo();
  function IsNumbericField($FieldType)
  {
    if(strpos($FieldType, "enum")!==false)
      return true;
    if(strpos($FieldType, "int")!==false)
      return true;
    return false;	    
  }
  
  function GetDistinctValuesCount($DBName, $TableName, $FieldName)
  {
    $mysql = pdodb::getInstance();  
    $tmp = $mysql->Execute("select count(distinct(`".$FieldName."`)) as tcount from ".$DBName.".".$TableName);
    if($trec = $tmp->fetch())
    {
      $mysql->Execute("update mis.MIS_TableFields set ValuesCount='".$trec["tcount"]."' where DBName='".$DBName."' and TableName='".$TableName."' and FieldName='".$FieldName."'");
      return $trec["tcount"];
    }
    return 0;
  }

  $mysql = pdodb::getInstance();
  
  if(isset($_REQUEST["ShowRecCount"]))
  {
    $query = "select count(*) as tcount from ".$_REQUEST["ShowRecCount"];
    $res = $mysql->Execute($query);
    $rec = $res->fetch();
    echo $rec["tcount"];
    die();
  }
  
  $DBName = $sTableName = $sdescription = $SearchType = "";
  if(isset($_REQUEST["sTableName"]))
  {
    $DBName = $_REQUEST["DB"];
    $sTableName = $_REQUEST["sTableName"];
    $sdescription = $_REQUEST["sdescription"];
    $SearchType = $_REQUEST["SearchType"];
  }
  
    if(isset($_REQUEST["GetValue"]))
    {
      $mysql->Prepare("select description, count(*) from mis.MIS_TableFields where FieldName=? and description<>'' 
			group by description order by count(*) DESC");
      $res = $mysql->ExecuteStatement(array($_REQUEST["GetValue"]));
      if($rec = $res->fetch())
      {
	echo $rec["description"];
      }
      die();
    }

    if(isset($_REQUEST["SaveValue"]))
    {
      $mysql->Prepare("update mis.MIS_TableFields set description=? where id=?");
      $mysql->ExecuteStatement(array($_REQUEST["SaveValue"], $_REQUEST["SaveFieldID"]));
      die();
    }
    
    HTMLBegin();
    
	if(isset($_REQUEST["ShowRelatedMenu"]))
	{
	  $mysql->Prepare("select SysCode, SystemType, systems.path, ScriptPath, ScriptName, description, SysSubDesc from framework.SystemPages
			    JOIN framework.SystemFacilities using (FacilityID)
			    JOIN framework.systems using (SysCode) where PageName=? and FacilityStatus='ENABLE' and SystemStatus='ENABLE'");
	  $res = $mysql->ExecuteStatement(array($_REQUEST["ShowRelatedMenu"]));
	    echo "<table align=center cellspacing=0 cellpadding=5 border=1 dir=rtl>";
	    echo "<tr class=HeaderOfTable><td colspan=2>منوهایی که از صفحه ".$_REQUEST["ShowRelatedMenu"]." استفاده می کنند</td></tr>";
    
	  while($rec = $res->fetch())
	  {
	    $FullPath = "";
	    // پورتال جامع اعضا با کد ۵۰ 
	    if($rec["SystemType"]=="GENERAL" && $rec["SysCode"]!=50)
	      $FullPath = "https://sadaf.um.ac.ir".$rec["path"];
	    else if($rec["SysCode"]==50)
	      $FullPath = "https://pooya.um.ac.ir";
	    else
	      $FullPath .= $rec["path"];
	    $FullPath .= $rec["ScriptPath"];
	    $FullPath .= $rec["ScriptName"];
	      echo "<tr>";
	      echo "<td>".$rec["description"]."</td>";
	      echo "<td><a target=_blank href='".$FullPath."'>".$rec["SysSubDesc"]."</a></td>";
	      echo "</tr>";
	  }
	  die();
	}
	
	if(isset($_REQUEST["ShowPagesThatQueryThisTable"]))
	{
	  $mysql->Prepare("select distinct page from projectmanagement.SystemDBLog where query like ? limit 0,20");
	  $res = $mysql->ExecuteStatement(array("% ".$_REQUEST["ShowPagesThatQueryThisTable"]." %"));
	  if($res->rowCount()>0)
	  {
	    echo "<table align=center cellspacing=0 cellpadding=5 border=1 dir=ltr>";
	    echo "<tr class=HeaderOfTable><td colspan=2 dir=rtl>فایلهایی که پرس و جویی از سمت آنها برای جدول  ".$_REQUEST["ShowPagesThatQueryThisTable"]." فراخوانی شده است</td></tr>";
	    while($rec = $res->fetch())
	    {
	      echo "<tr>";
	      $idx = strrpos($rec["page"], '/');
	      $path = str_replace('sadaf.um.ac.ir/', '/codes/sadaf/', substr($rec["page"], 0, $idx));
	      $path = str_replace('pooya.um.ac.ir/', '/codes/puya/', $path);
	      $PageName = substr($rec["page"], $idx+1, strlen($rec["page"]));
	      echo "<td>".$path."</td><td>";
	      echo "<a href='EditTableInfo.php?ShowFilePath=".$path."&ShowFileName=".$PageName."' target=_blank>";
	      echo $PageName;
	      echo "</a>";
	      echo "</td></tr>";
	    }
	    echo "</table>";
	  }
	  die();
	}

	if(isset($_REQUEST["ShowFileName"]))
	{
	  $mysql->Prepare("select content from mis.PageContent where path=? and name=?");
	  $res = $mysql->ExecuteStatement(array($_REQUEST["ShowFilePath"], $_REQUEST["ShowFileName"]));
	  if($rec = $res->fetch())
	  {
	    echo "<table dir=ltr><tr><td>";
	    echo "<pre>";
	    echo htmlspecialchars($rec["content"])."</pre>";
	    echo "</td></tr></table>";
	  }
	  die();
	}
	
	if(isset($_REQUEST["ShowRelatedFile"]))
	{
	  $query = "select path, name from mis.PageContent 
			    where type='php' and 
			    (StaticContent like ?)";
	  
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array("%".$_REQUEST["ShowRelatedFile"]."%"));
	  echo "<table border=1 cellpadding=5 cellspacing=0 align=center width=90%>";
	  if($res->rowCount()>0)
	  {
	    echo "<tr bgcolor=#eeeeee><td colspan=4>فایلهایی که در محتوای کد آنها نام این صفحه استفاده شده است</td></tr>";	  
	    echo "<tr class=HeaderOfTable>";
	    echo "<td>منوی مرتبط</td><td>فایلهای مرتبط</td><td>نام فایل</td><td>مسیر</td>";
	    echo "</tr>";
	  }
	  else 
	  {
	    echo "<tr bgcolor=#eeeeee><td >فایلی که در کد آن نام این فایل باشد وجود ندارد</td></tr>";	  
	    echo "</tr>";
	  }
	  
	  while($rec = $res->fetch())
	  {
	    echo "<tr>";
	    echo "<td> <a target=_blank href='EditTableInfo.php?ShowRelatedMenu=".$rec["name"]."'>منوی مرتبط</a>";	    
	    echo "<td> <a target=_blank href='EditTableInfo.php?ShowRelatedFile=".$rec["name"]."'>فایلهای مرتبط</a>";	    
	    echo "<td dir=ltr><a href='EditTableInfo.php?ShowFilePath=".$rec["path"]."&ShowFileName=".$rec["name"]."' target=_blank>".$rec["name"]."</a></td>";
	    echo "</td>";
	    echo "<td dir=ltr>".$rec["path"]."</td>";	    
	    echo "</tr>";
	  }
	  echo "</table>";
	  echo "<br>";
	
	  die();
	}
	
	if(isset($_REQUEST["ShowRelatedActivitiesAndPages"]))
	{
	  $TableName = $_REQUEST["ShowRelatedActivitiesAndPages"];
	  $mysql->Prepare("select projects.title as ProjectTitle,
				   ProjectTasks.title as TaskTitle,
				   ProjectTasks.description,
				   pfname, plname,
				   ActivityDescription,
				   ChangedPages, ChangedTables,
				   projectmanagement.g2j(ActivityDate) as gActivityDate
			    from projectmanagement.ProjectTaskActivities
			    LEFT JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
			    LEFT JOIN projectmanagement.projects using (ProjectID)
			    LEFT JOIN projectmanagement.persons on (persons.PersonID=ProjectTaskActivities.CreatorID)
			    where ChangedTables like ? order by ActivityDate DESC");
	  $res = $mysql->ExecuteStatement(array("%".$TableName."%"));
	  echo "<table border=1 cellpadding=5 cellspacing=0 align=center width=90%>";
	  if($res->rowCount()>0)
	  {
	    //echo "<tr class=HeaderOfTable>";
	    //echo "<td>پروژه</td><td>کار مربوطه</td><td>شرح کار</td><td>اقدام</td><td>اقدام کننده</td><td>تاریخ اقدام</td><td>جداول تغییریافته</td><td>صفحات تغییریافته</td>";
	    //echo "</tr>";
	    echo "<tr class=HeaderOfTable>";
	    echo "<td>فهرست فعالیتهای صورت گرفته که منجر به اعمال تغییرات روی جدول ".$TableName." شده اند.</td>";
	    echo "</tr>";
	  }
	  else
	  {
	    echo "<tr class=HeaderOfTable>";
	    echo "<td>در سیستم مدیریت پروژه اقدامی که شامل این جدول باشد وجود ندارد</td>";
	    echo "</tr>";
	  }
	  while($rec = $res->fetch())
	  {
	    echo "<tr>";
	    echo "<td><b>پروژه</b>: ".$rec["ProjectTitle"]."<br>";
	    echo "<b>عنوان کار</b>: ".$rec["TaskTitle"]."<br>";
	    echo "<b>شرح</b>: ".str_replace("\n", "<br>", $rec["description"])."<br>";
	    echo "<b>اقدام</b>: ".$rec["ActivityDescription"]."<br>";
	    echo "<b>جداول تغییر یافته</b>: ".$rec["ChangedTables"]."<br>";
	    echo "<b>فایلهای تغییر یافته</b>: ".$rec["ChangedPages"]."<br>";
	    echo "<b>مجری</b>: ".$rec["plname"]." ".$rec["pfname"];
	    echo " - تاریخ انجام: ".$rec["gActivityDate"]."<br>";
	    echo "</td></tr>";
	  }
	  echo "</table>";
	  echo "<br>";
	  
	  $query = "select path, name from mis.PageContent 
			    where type='php' and 
			    (StaticContent like ? or StaticContent like ? ";
	  if(isset($_REQUEST["AliasName"]) && $_REQUEST["AliasName"]!="")
	    $query .= " or content like ?) ";
	  else {
	      $query .= ")";
	  }
	  //$query .= " order by path, name";
	  $mysql->Prepare($query);
	  if(isset($_REQUEST["AliasName"]) && $_REQUEST["AliasName"]!="")
	    $res = $mysql->ExecuteStatement(array("%.".$TableName." %", "% ".$TableName." %", "%::".$_REQUEST["AliasName"]."%"));
	  else
	    $res = $mysql->ExecuteStatement(array("%.".$TableName." %", "% ".$TableName." %"));	  
	  echo "<table border=1 cellpadding=5 cellspacing=0 align=center width=90%>";
	  if($res->rowCount()>0)
	  {
	    echo "<tr bgcolor=#eeeeee><td colspan=4>فایلهایی که در محتوای کد آنها نام جدول ".$TableName." استفاده شده است</td></tr>";	  
	    echo "<tr class=HeaderOfTable>";
	    echo "<td>منوی مرتبط</td><td>فایلهای مرتبط</td><td>نام فایل</td><td>مسیر</td>";
	    echo "</tr>";
	  }
	  else 
	  {
	    echo "<tr bgcolor=#eeeeee><td >فایلی که در کد آن نام این جدول باشد وجود ندارد</td></tr>";	  
	    echo "</tr>";
	  }
	  
	  while($rec = $res->fetch())
	  {
	    echo "<tr>";
	    echo "<td> <a target=_blank href='EditTableInfo.php?ShowRelatedMenu=".$rec["name"]."'>منوی مرتبط</a>";	    	    
	    echo "<td> <a target=_blank href='EditTableInfo.php?ShowRelatedFile=".$rec["name"]."'>فایلهای مرتبط</a></td>";	    
	    echo "<td dir=ltr><a href='EditTableInfo.php?ShowFilePath=".$rec["path"]."&ShowFileName=".$rec["name"]."' target=_blank>".$rec["name"]."</a></td>";
	    echo "<td dir=ltr>".$rec["path"]."</td>";	    
	    echo "</tr>";
	  }
	  echo "</table>";
	  echo "<br>";
	  /*
	  $mysql->Prepare("select PageName, SysSubDesc, description from framework.SystemPages
				  JOIN framework.SystemFacilities using (FacilityID)
				  JOIN framework.systems using (SysCode)
				  where PageName like ?");
	  $res = $mysql->ExecuteStatement(array("%".$TableName."%"));
	  echo "<table border=1 cellpadding=5 cellspacing=0 align=center width=90%>";
	  if($res->rowCount()>0)
	  {
	    echo "<tr class=HeaderOfTable>";
	    echo "<td>نام صفحه</td><td>سیستم</td><td>منو</td>";
	    echo "</tr>";
	  }
	  while($rec = $res->fetch())
	  {
	    echo "<tr>";
	    echo "<td>".$rec["PageName"]."</td>";
	    echo "<td>".$rec["description"]."</td>";
	    echo "<td>".$rec["SysSubDesc"]."</td>";
	    echo "</tr>";
	  }
	  */
	  echo "</table>";
	  
	  die();
	}
	
	if(isset($_REQUEST["FlagTable"]))
	{
	  $mysql->Prepare("update mis.MIS_Tables set FlagTable='YES' where DBName=? and name=?");
	  $mysql->ExecuteStatement(array($_REQUEST["FlagDBName"], $_REQUEST["FlagTable"]));
	  die();
	}

	if(isset($_REQUEST["UnFlagTable"]))
	{
	  $mysql->Prepare("update mis.MIS_Tables set FlagTable='NO' where DBName=? and name=?");
	  $mysql->ExecuteStatement(array($_REQUEST["UnFlagDBName"], $_REQUEST["UnFlagTable"]));
	  die();
	}

	if(isset($_REQUEST["FlagFieldName"]))
	{
	  //echo "update mis.MIS_TableFields set FlagField='YES' where DBName=? and TableName=? and FieldName=?";
	  //echo "<br>".$_REQUEST["DBName"]."<br>".$_REQUEST["Table"]."<br>".$_REQUEST["FlagFieldName"];
	  $mysql->Prepare("update mis.MIS_TableFields set FlagField='YES' where DBName=? and TableName=? and FieldName=?");
	  $mysql->ExecuteStatement(array($_REQUEST["DBName"], $_REQUEST["Table"], $_REQUEST["FlagFieldName"]));
	  die();
	}

	if(isset($_REQUEST["UnFlagFieldName"]))
	{
	  $mysql->Prepare("update mis.MIS_TableFields set FlagField='NO' where DBName=? and TableName=? and FieldName=?");
	  $mysql->ExecuteStatement(array($_REQUEST["DBName"], $_REQUEST["Table"], $_REQUEST["UnFlagFieldName"]));
	  die();
	}

	if(isset($_REQUEST["EnableFieldName"]))
	{
	  //echo "update mis.MIS_TableFields set FlagField='YES' where DBName=? and TableName=? and FieldName=?";
	  //echo "<br>".$_REQUEST["DBName"]."<br>".$_REQUEST["Table"]."<br>".$_REQUEST["FlagFieldName"];
	  $mysql->Prepare("update mis.MIS_TableFields set EnableField='YES' where DBName=? and TableName=? and FieldName=?");
	  $mysql->ExecuteStatement(array($_REQUEST["DBName"], $_REQUEST["Table"], $_REQUEST["EnableFieldName"]));
	  die();
	}

	if(isset($_REQUEST["DisableFieldName"]))
	{
	  $mysql->Prepare("update mis.MIS_TableFields set EnableField='NO' where DBName=? and TableName=? and FieldName=?");
	  $mysql->ExecuteStatement(array($_REQUEST["DBName"], $_REQUEST["Table"], $_REQUEST["DisableFieldName"]));
	  die();
	}
	
	
	$TableExist = 0;
	$mysql->Prepare("select count(*) as tcount from information_schema.TABLES where TABLE_NAME=? and TABLE_SCHEMA=?");
	$res = $mysql->ExecuteStatement(array($_REQUEST["TableName"], $_REQUEST["DBName"]));
	if($rec = $res->fetch())
	{
	  $TableExist = $rec["tcount"];
	}
	
	$RecordsCount = "-";

	$Link = "EditTableInfo.php?sTableName=".$sTableName."&sdescription=".$sdescription."&DB=".$_REQUEST["DB"];
	$Link .= "&SearchType=".$_REQUEST["SearchType"];
	if(isset($_REQUEST["SearchInT"]))
	  $Link .= "&SearchInT=1";
	if(isset($_REQUEST["SearchInF"]))
	  $Link .= "&SearchInF=1";
	if(isset($_REQUEST["SearchInD"]))
	  $Link .= "&SearchInD=1";
	
	if(isset($_REQUEST["OnlyIncomplete"]))
	{
	  $Link .= "&OnlyIncomplete=1";
	}

	if(isset($_POST["Save"]))
	{
		$now = date("Ymd"); 
		$yy = substr($now,0,4); 
		$mm = substr($now,4,2); 
		$dd = substr($now,6,2);
		list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
		if(strlen($mm)==1)
			$mm = "0".$mm;
		if(strlen($dd)==1)
			$dd = "0".$dd;
		$yy = substr($yy, 2, 2);
	
		//$TableType = $_POST["TableType"];
		//$EntityName = $_POST["EntityName"];
		$description = $_POST["description"];
		$TableName = $_POST["TableName"];
		$AliasName = $_POST["AliasName"];
		$EducationalDomain = $ResearchDomain = $SupportDomain = $StudentServiceDomain = $SystemRelatedDomain = "NO";
		if(isset($_POST["ch_educ"]))
		  $EducationalDomain = "YES";
		if(isset($_POST["ch_research"]))
		  $ResearchDomain = "YES";
		if(isset($_POST["ch_student"]))
		  $StudentServiceDomain = "YES";
		if(isset($_POST["ch_system"]))
		  $SystemRelatedDomain = "YES";
		if(isset($_POST["ch_support"]))
		  $SupportDomain = "YES";
		$mysql->Prepare("update mis.MIS_Tables set 
				  LastUpdateYear='13$yy', LastUpdateMonth='$mm', LastUpdateDay='$dd', 
				  EducationalDomain=?, ResearchDomain=?, StudentServiceDomain=?, SupportDomain=?, SystemRelatedDomain=?, 
				  description=?, LastUpdateUser='".$_SESSION["UserID"]."', TableStatus=?, 
				  AliasName=? where name=? and DBName=?");
		$mysql->ExecuteStatement(array($EducationalDomain, $ResearchDomain, $StudentServiceDomain, $SupportDomain, $SystemRelatedDomain, 
						$description, $_REQUEST["TableStatus"], $AliasName, $TableName, $_REQUEST["DBName"]));
		echo "<p align=center><font face=tahoma size=2 color=green>اطلاعات بروزرسانی شد</p>";
	}
	if(isset($_POST["SaveField"]))
	{
		$now = date("Ymd"); 
		$yy = substr($now,0,4); 
		$mm = substr($now,4,2); 
		$dd = substr($now,6,2);
		list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
		if(strlen($mm)==1)
			$mm = "0".$mm;
		if(strlen($dd)==1)
			$dd = "0".$dd;
		$yy = substr($yy, 2, 2);
	
		$TableName = $_POST["TableName"];
	
		$mysql->Prepare("select * from mis.MIS_TableFields where status=1 and TableName=? and DBName=?");
		$res = $mysql->ExecuteStatement(array($TableName, $_REQUEST["DBName"]));
		while($rec = $res->fetch())
		{
			$ch = $rec["FieldName"];
			$ch1 = $ch."_description";
			$ch2 = $ch."_RelatedTable";
			$ch3 = $ch."_RelatedField";
			$ch4 = $ch."_RelationCondition";
			$ch5 = $ch."_RelatedDBName";
			
			$ch6 = $ch."_RelatedTable2";
			$ch7 = $ch."_RelatedField2";
			$ch8 = $ch."_RelationCondition2";
			$ch9 = $ch."_RelatedDBName2";

			$ch10= $ch."_RelatedTable3";
			$ch11= $ch."_RelatedField3";
			$ch12= $ch."_RelationCondition3";
			$ch13= $ch."_RelatedDBName3";
			//echo $ch1." -> ".$_POST[$ch1]."<br>";

			
			$curres = $mysql->Execute("select * from mis.MIS_TableFields where id='".$rec["id"]."'");
			$currrec = $curres->fetch();
			if($currrec["description"]!=$_POST[$ch1] || 
			
			$currrec["RelatedTable"]!=$_POST[$ch2] || 
			$currrec["RelatedField"]!=$_POST[$ch3] ||
			$currrec["RelationCondition"]!=$_POST[$ch4] || 
			$currrec["RelatedDBName"]!=$_POST[$ch5] ||

			$currrec["RelatedTable2"]!=$_POST[$ch6] || 
			$currrec["RelatedField2"]!=$_POST[$ch7] ||
			$currrec["RelationCondition2"]!=$_POST[$ch8] || 
			$currrec["RelatedDBName2"]!=$_POST[$ch9] ||
			
			$currrec["RelatedTable3"]!=$_POST[$ch10] || 
			$currrec["RelatedField3"]!=$_POST[$ch11] ||
			$currrec["RelationCondition3"]!=$_POST[$ch12] || 
			$currrec["RelatedDBName3"]!=$_POST[$ch513])
			{
			  $mysql->Prepare("update mis.MIS_TableFields set description=?, 
			  RelatedTable=?, RelatedField=?, RelationCondition=?, RelatedDBName=?,
			  RelatedTable2=?, RelatedField2=?, RelationCondition2=?, RelatedDBName2=?, 
			  RelatedTable3=?, RelatedField3=?, RelationCondition3=?, RelatedDBName3=?, 
			  LastUpdateYear=?, LastUpdateMonth=?, LastUpdateDay=?, LastUpdateUser=? where id='".$rec["id"]."'");
			  $mysql->ExecuteStatement(array($_POST[$ch1], $_POST[$ch2], $_POST[$ch3], $_POST[$ch4], $_POST[$ch5], 
			  $_POST[$ch6],
			  $_POST[$ch7],
			  $_POST[$ch8],
			  $_POST[$ch9],
			  $_POST[$ch10],
			  $_POST[$ch11],
			  $_POST[$ch12],
			  $_POST[$ch13],
			  $yy, $mm, $dd, $_SESSION["UserID"]));
			}
		}	
		echo "<p align=center><font face=tahoma size=2 color=green>اطلاعات بروزرسانی شد</p>";
	}
	$TableName = $_REQUEST["TableName"];
	$DB = $_REQUEST["DB"];
	$query = "select * from mis.MIS_Tables where name=? and DBName=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($TableName, $_REQUEST["DBName"]));
	if($rec = $res->fetch())
	{
	  //$TableType = $rec["TempOrCompletelyTransactional"];
	  //$EntityName = $rec["EntityName"];	
	  $description = $rec["description"];
	  $TableStatus = $rec["TableStatus"];
	  $FlagTable = $rec["FlagTable"];
	  $AliasName = $rec["AliasName"];
	  $CurDBName = $rec["DBName"];
	  $CurTableName = $rec["name"];
	  
	  $EducationalDomain = $rec["EducationalDomain"];
	  $ResearchDomain = $rec["ResearchDomain"];
	  $StudentServiceDomain = $rec["StudentServiceDomain"];
	  $SupportDomain = $rec["SupportDomain"];
	  $SystemRelatedDomain = $rec["SystemRelatedDomain"];
	}
	else {
	    echo "جدول پیدا نشد";
	    die();
	}

	$query = "select * from mis.MIS_Tables where status=1 and name like ? and description like ? and DBName like ? ";
	if(isset($_REQUEST["OnlyIncomplete"]))
		$query .= " and (description='' or description is null) and (EntityName='' or EntityName is null) ";
	$query .= " and name>? ";
	if(isset($_SESSION["DomainCondition"]) && $_SESSION["DomainCondition"]!="")
	  $query .= " and (".$_SESSION["DomainCondition"].") ";
	$query .= " order by name limit 1";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array("%".$sTableName."%", "%".$sdescription."%", $_REQUEST["DB"]."%", $TableName));
	if($trec = $res->fetch())
	{
		$NextLink = $Link."&TableName=".$trec["name"]."&DBName=".$trec["DBName"]."&ServerName=".$trec["ServerName"];
		if(isset($_REQUEST["SaveAndNext"]) && $_REQUEST["SaveAndNext"]=="1")
		{
		  echo "<script>document.location=\"".$NextLink."\";</script>";
		  die();
		}
	}
	else
		$NextLink = "#";
  
	$query = "select * from mis.MIS_Tables where status=1 and name like ? and description like ? and DBName like ? ";
	if(isset($_REQUEST["OnlyIncomplete"]))
		$query .= " and (description='' or description is null) and (EntityName='' or EntityName is null) ";
	$query .= " and name<? ";
	if(isset($_SESSION["DomainCondition"]) && $_SESSION["DomainCondition"]!="")
	  $query .= " and (".$_SESSION["DomainCondition"].") ";
	$query .= " order by name DESC limit 1";

	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array("%".$sTableName."%", "%".$sdescription."%", $_REQUEST["DB"]."%", $TableName));
	if($trec = $res->fetch())
		$PreLink = $Link."&TableName=".$trec["name"]."&DBName=".$trec["DBName"]."&ServerName=".$trec["ServerName"];
	else
		$PreLink = "#";
	
		?>
<form method=post id=sf name=sf>
<input type=hidden name=DB value='<?= $_REQUEST["DB"] ?>'>
<? if(isset($_REQUEST["OnlyIncomplete"]))
{
?>
	<input type=hidden name=OnlyIncomplete value='1'>
<?
}
?>
<? if(isset($_REQUEST["SearchInT"]))
{
?>
	<input type=hidden name=SearchInT value='1'>
<?
}
?>
<? if(isset($_REQUEST["SearchInF"]))
{
?>
	<input type=hidden name=SearchInF value='1'>
<?
}
?>
<? if(isset($_REQUEST["SearchInD"]))
{
?>
	<input type=hidden name=SearchInD value='1'>
<?
}
?>
<input type=hidden name=TableName value='<?= $TableName ?>'>
<input type=hidden name=DBName value='<?= $_REQUEST["DBName"] ?>'>
<input type=hidden name=ServerName value='<?= $ServerName ?>'>
<input type=hidden name=sTableName value='<?= $sTableName ?>'>
<input type=hidden name=sdescription value='<?= $sdescription ?>'>
<input type=hidden name=SearchType value='<?= $SearchType ?>'>
<input type=hidden name=Save value='1'>
<table align=center border=1 width=98% cellspacing=0 cellpadding=5>
<tr>
	<td>
	<table width=100%>
		<tr>
			<td colspan=2 align=center bgcolor=#cccccc>
				<table width=100%>
				<tr>
				<td width=10%>
				<a href='<?= $PreLink ?>'>
				قبلی
				</a>
				</td>
				<td align=center>
				بروزرسانی اطلاعات
				</td>
				<td width=10%>
				<a href='<?= $NextLink ?>'>
				بعدی
				</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td> جدول: </td>
			<td>
			<a href='#' onclick='javascript: FlagOrUnFlagTable()'>
			<? if($FlagTable=="YES") { ?>
			  <img id='FlagImg' width=20 src='images/star2.png' border=0 title='برداشتن پرچم از جدول'>
			<? } else { ?>
			  <img id='FlagImg' width=20 src='images/star1.png' title='گذاشتن پرچم روی جدول' border=0>
			<? } ?>
			</a> 
			<?= $TableName ?> &nbsp;&nbsp;<a href='ShowTopRecords.php?ServerName=<?= $ServerName ?>&TableName=<? echo $_REQUEST["DBName"] ?>.<? echo $TableName ?>' target=_blank><img border=0 width=30 src='images/pages.png' title='مشاهده 20 رکورد اول'></a>
			 &nbsp;&nbsp;&nbsp;
			 <a href='#' onclick='javascript: OpenQueryFinder()'><img border=0 width=30 src='images/sql.jpeg' title='مشاهده صفحاتی که به این جدول کوئری زده اند'></a>
			 &nbsp;&nbsp;
			 <a href='#' onclick='javascript: OpenPageFinder()'><img border=0 width=30 src='images/project_files.png' title='اقدامات روی این جدول / صفحات با نام مشابه این جدول یا شامل نام این جدول'></a>
			</td>
		</tr>
		<tr>
		  <td>تعداد رکوردها:</td><td><span id=RecCountSpan name=RecCountSpan><img src='images/ajax-loader.gif'></span></td>
		</tr>
		<?
		  $fquery = " select distinct MIS_Tables.DBName, MIS_Tables.name, MIS_Tables.description from mis.MIS_TableFields 
			      JOIN mis.MIS_Tables on (MIS_TableFields.DBName=MIS_Tables.DBName and MIS_TableFields.TableName=MIS_Tables.name) 

			      where (RelatedDBName='".$CurDBName."' and RelatedTable='".$CurTableName."')
			      or (RelatedDBName2='".$CurDBName."' and RelatedTable2='".$CurTableName."')
			      or (RelatedDBName3='".$CurDBName."' and RelatedTable3='".$CurTableName."')
			      ";
		  $fres = $mysql->Execute($fquery);
		  $ForeignNum = $fres->rowCount();
		?>
		<tr>
		  <td>
		    تعداد جداول متصل به این جدول:
		  </td>
		  <td>
		    <a href=# onclick='ShowHide()'><? echo $ForeignNum ?></a>
		  </td>
		</tr>
		<tr id=FTR style="display: none;">
		  <td colspan=2>
		    <table>
		      <?
			while($frec = $fres->fetch())
			{
			  echo "<tr>";
			  echo "<td dir=ltr>";
			  echo $frec["DBName"];
			  echo "</td>";
			  echo "<td dir=ltr>";
			  echo " <a target=_blank href='EditTableInfo.php?sTableName=&sdescription=&DB=".$frec["DBName"]."&TableName=".$frec["name"]."&DBName=".$frec["DBName"]."&ServerName='>";
			  echo $frec["name"];
			  echo "</td>";
			  echo "<td>";
			  echo $frec["description"];
			  echo "</td>";
			  echo "</tr>";
			}
		      ?>
		    </table>
		  </td>
		</tr>
		<tr>
			<td>حوزه: </td>
			<td>
			  <input type=checkbox id=ch_educ name=ch_educ <? if($EducationalDomain=="YES") echo "checked"; ?> >آموزشی
			  <input type=checkbox id=ch_research name=ch_research <? if($ResearchDomain=="YES") echo "checked"; ?> >پژوهشی
			  <input type=checkbox id=ch_student name=ch_student <? if($StudentServiceDomain=="YES") echo "checked"; ?> >خدمات دانشجویی (رفاهی - فرهنگی)
			  <input type=checkbox id=ch_support name=ch_support <? if($SupportDomain=="YES") echo "checked"; ?> >پشتیبانی (اداری/مالی)
			  <input type=checkbox id=ch_system name=ch_system <? if($SystemRelatedDomain=="YES") echo "checked"; ?> >مرتبط با عملیات سیستمی
			</td>
		</tr>
		<!--- از نهاد دیگر استفاده نمی شود و به شرح مراجعه می شود
		<tr> 
			<td>نهاد: </td>
			<td>
				<input type=text name=EntityName value='' >
			</td>
		</tr>
		--->
		<tr>
			<td>شرح: </td>
			<td>
				<textarea name=description cols=80 rows=5 onkeypress="return submitenter(this, event);" style="font-size: 13"><?= $description ?></textarea>
				<br>
				<?php
				  /*
				  قبلا کامنت داخلی جدول را نشان می داد الان نیازی نیست چون هر بار می تواند بروز کند
					$mysql->Prepare("select TABLE_COMMENT from mis.TABLES_SCHEMA where TABLE_SCHEMA=? and TABLE_NAME=?");
					$tmp = $mysql->ExecuteStatement(array($_REQUEST["DBName"], $TableName));
					if($trec = $tmp->fetch())
					{
						echo $trec["TABLE_COMMENT"];
					}
				  */
				?>
			</td>
		</tr>
		<tr>
		  <td>وضعیت:</td>
		  <td>
		    <select name=TableStatus id=TableStatus>
		    <option value='ENABLE'>فعال
		    <option value='DISABLE' <? if($TableStatus=="DISABLE") echo "selected"; ?>>غیر فعال
		  </td>
		</tr>
		<tr>
			<td>نام ثابت تعریف شده در برنامه: </td>
			<td>
				<input type=text name=AliasName value='<?= $AliasName ?>' dir=ltr>
			</td>
		</tr>
		<tr>
		  <td colspan=2>در صورتیکه برای جدول یک ثابت تعریف شده وارد شود. در صفحه مشاهده صفحاتی که در محتوای آنها نام این جدول وجود دارد،
		  علاوه بر نام اصلی جدول رشته الصاق شده نام آن ثابت نیز جستجو خواهد شد
		  </td>
		</tr>
		<tr>
			<td colspan=2 align=center bgcolor=#cccccc>
				<input type=hidden name=SaveAndNext id=SaveAndNext value=0>
				<input type=submit value='&nbsp;ذخیره&nbsp;'>
				&nbsp;
				<input type=button onclick='javascript: document.getElementById("SaveAndNext").value=1; document.getElementById("sf").submit();' value='&nbsp;ذخیره و نمایش بعدی&nbsp;'>
				&nbsp;
				<input type=button onclick="javascript: document.location='ManageTables.php?sTableName=<?= $sTableName ?>&sdescription=<?= $sdescription ?>&DB=<?= $_REQUEST["DB"] ?>'" value='&nbsp;انصراف&nbsp;'>
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>
</form>

<form name=f1 method=post>
<input type=hidden name=DB value='<?= $_REQUEST["DB"] ?>'>
<? if(isset($_REQUEST["OnlyIncomplete"]))
{
?>
	<input type=hidden name=OnlyIncomplete value='1'>
<?
}
?>

<input type=hidden name=TableName value='<?= $TableName ?>'>
<input type=hidden name=sTableName value='<?= $sTableName ?>'>
<input type=hidden name=DBName value='<?= $_REQUEST["DBName"] ?>'>
<input type=hidden name=ServerName value='<?= $ServerName ?>'>
<input type=hidden name=sdescription value='<?= $sdescription ?>'>
<input type=hidden name=SaveField value='1'>
<table align=center border=1 width=98% cellspacing=0 cellpadding=2>
<tr>
	<td>
	<table width=100%>
		<tr bgcolor=#cccccc>
			<td>نام فیلد</td><td>نوع</td><td nowrap width=1%>جدول داده</td><td>توضیحات</td><td>کلید خارجی</td>
		</tr>
<?
	$query = "select *, 
			  (select count(*) from mis.FieldsDataMapping where 
			  FieldsDataMapping.DBName=MIS_TableFields.DBName and
			  FieldsDataMapping.TableName=MIS_TableFields.TableName and
			  FieldsDataMapping.FieldName=MIS_TableFields.FieldName 
			  ) as MappingCount 
			  from mis.MIS_TableFields where status=1 and TableName=? and DBName=? order by FieldName";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($TableName, $_REQUEST["DBName"]));
	while($rec = $res->fetch())
	{
	  $i++;
	    if($i%2==0)
		    echo "\r\n<tr>";
	    else
		    echo "\r\n<tr bgcolor=#cccccc>";
	    
	    
	    echo "\r\n<td dir=ltr align=left>&nbsp;";

	    if($rec["EnableField"]=="YES")
	      echo "<img onclick='javascript: EnableDisableField(\"".$rec["FieldName"]."\")' id='Flag2Img_".$rec["FieldName"]."' width=20 src='images/enable.png' border=0 title='فیلد فعال و دارای داده حوزه'>";
	    else 
	      echo "<img onclick='javascript: EnableDisableField(\"".$rec["FieldName"]."\")' id='Flag2Img_".$rec["FieldName"]."' width=20 src='images/disable.png' border=0 title='فیلد غیر فعال یا حوای داده سیستمی'>";

	    
	    if($rec["FlagField"]=="YES")
	      echo "<img onclick='javascript: FlagOrUnFlagField(\"".$rec["FieldName"]."\")' id='FlagImg_".$rec["FieldName"]."' width=20 src='images/star2.png' border=0 title='برداشتن پرچم از فیلد'>";
	    else 
	      echo "<img onclick='javascript: FlagOrUnFlagField(\"".$rec["FieldName"]."\")' id='FlagImg_".$rec["FieldName"]."' width=20 src='images/star1.png' border=0 title='گذاشتن پرچم روی فیلد'>";
	    
	    if($rec["KeyType"]=="PRI")
	      echo "<img src='images/key.gif' width=20>&nbsp;";
	    echo "<font title='".$rec["LastUpdateYear"]."-".$rec["LastUpdateMonth"]."-".$rec["LastUpdateDay"]."\n".$rec["LastUpdateUser"]."'>";
	    if($_SESSION["UserID"]=="omid")
	    {
	      echo "<a href='EditSimilarFields.php?FieldName=".$rec["FieldName"]."' target=_blank>";
	      echo $rec["FieldName"];
	      echo "</a>";
	    }
	    else {
	        echo $rec["FieldName"];
	    }
	    echo "</font>";
	    //if(isset($_REQUEST[$rec["FieldName"]."_description"]))
	      //echo "*".$_REQUEST[$rec["FieldName"]."_description"]."*";
	      
	    $query = "select MIS_Tables.DBName, MIS_Tables.name, MIS_Tables.description, FieldName from mis.MIS_TableFields
		      JOIN mis.MIS_Tables on (MIS_TableFields.DBName=MIS_Tables.DBName and MIS_TableFields.TableName=MIS_Tables.name) 
		      where 
		      (RelatedDBName='".$rec["DBName"]."' and RelatedTable='".$rec["TableName"]."' and RelatedField='".$rec["FieldName"]."')
		      or (RelatedDBName2='".$rec["DBName"]."' and RelatedTable2='".$rec["TableName"]."' and RelatedField2='".$rec["FieldName"]."')
		      or (RelatedDBName3='".$rec["DBName"]."' and RelatedTable3='".$rec["TableName"]."' and RelatedField3='".$rec["FieldName"]."') 
		      order by MIS_Tables.DBName, MIS_Tables.name";
	    $fkres = $mysql->Execute($query);
	    if($fkres->rowCount()>0)
	    {
	      echo "<p dir=rtl align=right><b>[<a href='#' onclick='javascript: document.getElementById(\"FK_".$rec["FieldName"]."\").style.display=\"\";'>".$fkres->rowCount()."</a>] اتصال از سایر جداول</b></p>";
	      echo "<span dir=ltr id='FK_".$rec["FieldName"]."' style='display:none'>";
	      while($fkrec = $fkres->fetch())
	      {
		echo $fkrec["DBName"].".";
		echo "<a target=_blank href='EditTableInfo.php?sTableName=&sdescription=&DB=".$fkrec["DBName"]."&TableName=".$fkrec["name"]."&DBName=".$fkrec["DBName"]."&ServerName='>";
		echo $fkrec["name"]."</a>.".$fkrec["FieldName"]."<br>";
	      }
	      echo "</span>";
	    }
	    echo "</td>";
	    /*
	    echo "<td width=1%>";
	    echo "<a href='#' onclick='javascript: OpenFieldPageFinder(\"".$rec["FieldName"]."\")'><img border=0 width=30 src='images/project_files.png' title='صفحات با محتوای نام جدول و این فیلد'></a>";
	    echo "</td>";
	    */
	    echo "\r\n<td dir=ltr>".str_replace(",",", ", $rec["FieldType"])."</td>";
	    echo "\r\n<td>";
	    
	    if(IsNumbericField($rec["FieldType"]) && $rec["KeyType"]!="PRI" && $rec["RelatedTable"]=="")
	    {
	      //echo "select count(distinct(".$rec["FieldName"].")) as tcount from ".$rec["DBName"].".".$rec["TableName"]."<br>";
	      if($TableExist=="1")
	      {
		  if($rec["ValuesCount"]>-1)
		    $ValuesCount = $rec["ValuesCount"];
		  else
		    $ValuesCount = GetDistinctValuesCount($rec["DBName"], $rec["TableName"], $rec["FieldName"]);
		  if($ValuesCount<50)
		  {
		    // تنها برای فیلدهایی که کمتر از مقدار معینی داده منحصر بقرد داشته باشند امکان تعریف کردن داده ها را نشان دهد
		      echo "<a target=_blank href='ManageFieldsDataSematics.php?DBName=".$_REQUEST["DBName"]."&TableName=".$TableName."&FieldName=".$rec["FieldName"]."'>";
		      $mysql->Prepare("select count(*) as tcount from mis.FieldsDataMapping where DBName=? and TableName=? and FieldName=?");
		      $mres = $mysql->ExecuteStatement(array($_REQUEST["DBName"], $TableName, $rec["FieldName"]));
		      $mrec = $mres->fetch();
		      if($mrec["tcount"]>0)
			      echo "<img src='images/DataTableFull.jpg' width=30 border=0 title='تعریف جدول معادلسازی داده ها'></a>";
		      else
			      echo "<img src='images/DataTable.jpg' width=30 border=0 title='تعریف جدول معادلسازی داده ها'></a>";
		  }
	      }
	      else {
		      echo "<a target=_blank href='ManageFieldsDataSematics.php?DBName=".$_REQUEST["DBName"]."&TableName=".$TableName."&FieldName=".$rec["FieldName"]."'>";
		      $mysql->Prepare("select count(*) as tcount from mis.FieldsDataMapping where DBName=? and TableName=? and FieldName=?");
		      $mres = $mysql->ExecuteStatement(array($_REQUEST["DBName"], $TableName, $rec["FieldName"]));
		      $mrec = $mres->fetch();
		      if($mrec["tcount"]>0)
			echo "<img src='images/DataTableFull.jpg' width=30 border=0 title='تعریف جدول معادلسازی داده ها'></a>";
	      }
	      
	    }
	    else {
		echo "&nbsp;";
	    }
	    
	    echo "</td>";
	    echo "\r\n<td>";
	    echo "<textarea style='font-size: 13' cols=40 rows=2 name='".$rec["FieldName"]."_description' id='".$rec["FieldName"]."_description'>".$rec["description"]."</textarea>";
	    //echo "<a href='#' onclick='javascript: GetValue(\"".$rec["FieldName"]."_description\", \"".$rec["FieldName"]."\")'>";
	    echo "<img onclick='javascript: GetValue(\"".$rec["FieldName"]."_description\", \"".$rec["FieldName"]."\")' width=20 src='images/Execute.png' border=0 title='دریافت شرح از میان داده های پیشین'>";
	    echo "<img onclick='javascript: SaveValue(\"".$rec["FieldName"]."_description\", \"".$rec["id"]."\")' width=20 src='images/Down.gif' border=0 title='ذخیره توضیح'>";
	    /*
			      قبلا کامنت داخلی جدول را نشان می داد الان نیازی نیست چون هر بار می تواند بروز کند		
	    $mysql->Prepare("select COLUMN_COMMENT from mis.COLUMNS where TABLE_SCHEMA=? and TABLE_NAME=? and COLUMN_NAME='".$rec["FieldName"]."'");
	    $tmp = $mysql->ExecuteStatement(array($_REQUEST["DBName"], $TableName));
	    if($trec=$tmp->fetch())
	    {
		    echo "<br>".$trec["COLUMN_COMMENT"];
	    }
	    */
	    echo "</td>";
	    if($rec["RelatedTable"]!="" || $rec["RelatedTable2"]!="" || $rec["RelatedTable3"]!="" || $rec["RelatedTable4"]!="")
	    {
	      $ts = $mysql->Execute("select MIS_Tables.*, MIS_CodingTablesID from mis.MIS_Tables 
	      LEFT JOIN MIS_CodingTables on (MIS_CodingTables.DBName=MIS_Tables.DBName and MIS_CodingTables.TableName=MIS_Tables.name) 
	      where MIS_Tables.DBName='".$rec["RelatedDBName"]."' and MIS_Tables.name='".$rec["RelatedTable"]."'");
	      if($tsrec = $ts->fetch())
		$font = "<font color=black>";
	      else 
		$font = "<font color=red>";
	      
		    echo "\r\n<td dir=ltr>";
		    
		    echo $font."<span name='".$rec["FieldName"]."_Span' id='".$rec["FieldName"]."_Span'>";
		    echo $rec["RelatedDBName"].".";
		    if($tsrec["MIS_CodingTablesID"]!="")
		      echo "<font color=green>";
		    echo $rec["RelatedTable"];
		    if($tsrec["MIS_CodingTablesID"]!="")
		      echo "</font>";
		    echo ".".$rec["RelatedField"];
		    if($rec["RelationCondition"]!="")
		      echo " (".$rec["RelationCondition"].")";
		    echo "</span></font><br>";
		    
		    echo "<a href='SetForignKey.php?FK=&SelectedDBName=".$_REQUEST["DBName"]."&TableName=".$_REQUEST["TableName"]."&FieldName=".$rec["FieldName"]."' target=_blank>";
		    echo "<img src='images/edit.jpg' border=0 title='ویرایش' width=20>";
		    echo "</a>";
		    echo " <a target=_blank href='EditTableInfo.php?sTableName=&sdescription=&DB=".$rec["RelatedDBName"]."&TableName=".$rec["RelatedTable"]."&DBName=".$rec["RelatedDBName"]."&ServerName='>";
		    echo "<img src='images/list.jpg' border=0 title='مشاهده جدول مربوطه' width=20>";
		    echo "</a><br>";

		    echo $font."<span name='".$rec["FieldName"]."_Span2' id='".$rec["FieldName"]."_Span2'>";
		    echo $rec["RelatedDBName2"].".".$rec["RelatedTable2"].".".$rec["RelatedField2"];
		    if($rec["RelationCondition2"]!="")
		      echo " (".$rec["RelationCondition2"].")";
		    echo "</span></font><br>";
		    echo "<a href='SetForignKey.php?FK=2&SelectedDBName=".$_REQUEST["DBName"]."&TableName=".$_REQUEST["TableName"]."&FieldName=".$rec["FieldName"]."' target=_blank>";
		    echo "<img src='images/edit.jpg' border=0 title='ویرایش' width=20>";
		    echo "</a>";
		    echo " <a target=_blank href='EditTableInfo.php?sTableName=&sdescription=&DB=".$rec["RelatedDBName2"]."&TableName=".$rec["RelatedTable2"]."&DBName=".$rec["RelatedDBName2"]."&ServerName='>";
		    echo "<img src='images/list.jpg' border=0 title='مشاهده جدول مربوطه' width=20>";
		    echo "</a><br>";

		    echo $font."<span name='".$rec["FieldName"]."_Span3' id='".$rec["FieldName"]."_Span3'>";
		    echo $rec["RelatedDBName3"].".".$rec["RelatedTable3"].".".$rec["RelatedField3"];
		    if($rec["RelationCondition3"]!="")
		      echo " (".$rec["RelationCondition3"].")";
		    echo "</span></font><br>";
		    echo "<a href='SetForignKey.php?FK=3&SelectedDBName=".$_REQUEST["DBName"]."&TableName=".$_REQUEST["TableName"]."&FieldName=".$rec["FieldName"]."' target=_blank>";
		    echo "<img src='images/edit.jpg' border=0 title='ویرایش' width=20>";
		    echo "</a>";
		    echo " <a target=_blank href='EditTableInfo.php?sTableName=&sdescription=&DB=".$rec["RelatedDBName3"]."&TableName=".$rec["RelatedTable3"]."&DBName=".$rec["RelatedDBName3"]."&ServerName='>";
		    echo "<img src='images/list.jpg' border=0 title='مشاهده جدول مربوطه' width=20>";
		    echo "</a>";
		    
		    echo "</td>";
	    }
	    else
	    {
		echo "\r\n<td>";
		echo "<span name='".$rec["FieldName"]."_Span' id='".$rec["FieldName"]."_Span'> </span>";
		echo "<a href='SetForignKey.php?FK=&SelectedDBName=".$_REQUEST["DBName"]."&TableName=".$_REQUEST["TableName"]."&FieldName=".$rec["FieldName"]."' target=_blank>";
		echo "<img src='images/edit.jpg' border=0 title='ویرایش' width=20>";
		echo "</a>";

		echo "<br><span name='".$rec["FieldName"]."_Span2' id='".$rec["FieldName"]."_Span2'> </span>";
		echo "<a href='SetForignKey.php?FK=2&SelectedDBName=".$_REQUEST["DBName"]."&TableName=".$_REQUEST["TableName"]."&FieldName=".$rec["FieldName"]."' target=_blank>";
		echo "<img src='images/edit.jpg' border=0 title='ویرایش' width=20>";
		echo "</a>";

		echo "<br><span name='".$rec["FieldName"]."_Span3' id='".$rec["FieldName"]."_Span3'> </span>";
		echo "<a href='SetForignKey.php?FK=3&SelectedDBName=".$_REQUEST["DBName"]."&TableName=".$_REQUEST["TableName"]."&FieldName=".$rec["FieldName"]."' target=_blank>";
		echo "<img src='images/edit.jpg' border=0 title='ویرایش' width=20>";
		echo "</a>";

		echo "</td>";
	    }
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelatedDBName' value='".$rec["RelatedDBName"]."'>";
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelatedTable' value='".$rec["RelatedTable"]."'>";
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelatedField' value='".$rec["RelatedField"]."'>";
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelationCondition' value=\"".$rec["RelationCondition"]."\">";

	    echo "<input type=hidden name='".$rec["FieldName"]."_RelatedDBName2' value='".$rec["RelatedDBName2"]."'>";
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelatedTable2' value='".$rec["RelatedTable2"]."'>";
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelatedField2' value='".$rec["RelatedField2"]."'>";
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelationCondition2' value=\"".$rec["RelationCondition2"]."\">";

	    echo "<input type=hidden name='".$rec["FieldName"]."_RelatedDBName3' value='".$rec["RelatedDBName3"]."'>";
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelatedTable3' value='".$rec["RelatedTable3"]."'>";
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelatedField3' value='".$rec["RelatedField3"]."'>";
	    echo "<input type=hidden name='".$rec["FieldName"]."_RelationCondition3' value=\"".$rec["RelationCondition3"]."\">";
	    
	    echo "</tr>";
		
	}
?>
		<tr>
			<td colspan=5 align=center bgcolor=#cccccc>
				<input type=submit value='&nbsp;ذخیره&nbsp;'>
				&nbsp;
				<input type=button onclick="javascript: document.location='ManageTables.php?sTableName=<?= $sTableName ?>&sdescription=<?= $sdescription ?>'" value='&nbsp;انصراف&nbsp;'>
			</td>
		</tr>
		<tr>
			<td colspan=5 align=center bgcolor=#cccccc>
				<table width=100%>
				<tr>
				<td width=10%>
				<a href='<?= $PreLink ?>'>
				قبلی
				</a>
				</td>
				<td align=center>
				بروزرسانی اطلاعات
				</td>
				<td width=10%>
				<a href='<?= $NextLink ?>'>
				بعدی
				</td>
				</tr>
				</table>
			</td>
		</tr>
		
	</table>
	</td>
</tr>
</table>
</form>
<script>
  function FlagOrUnFlagTable()
  {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
	if(document.getElementById('FlagImg').src.indexOf('images/star1.png')>0)
	  document.getElementById('FlagImg').src = 'images/star2.png';	
	else
	  document.getElementById('FlagImg').src = 'images/star1.png';	
      }
    };
    if(document.getElementById('FlagImg').src.indexOf('images/star1.png')>0) // means not flag
    {
      xhttp.open("GET", "EditTableInfo.php?FlagTable=<? echo $TableName ?>&FlagDBName=<? echo $_REQUEST["DBName"] ?>", true);
    }
    else
    {
      xhttp.open("GET", "EditTableInfo.php?UnFlagTable=<? echo $TableName ?>&UnFlagDBName=<? echo $_REQUEST["DBName"] ?>", true);
    }
    xhttp.send();    
  }

  function FlagOrUnFlagField(FieldName)
  {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
	if(document.getElementById('FlagImg_'+FieldName).src.indexOf('images/star1.png')>0)
	  document.getElementById('FlagImg_'+FieldName).src = 'images/star2.png';	
	else
	  document.getElementById('FlagImg_'+FieldName).src = 'images/star1.png';	
      }
    };
    if(document.getElementById('FlagImg_'+FieldName).src.indexOf('images/star1.png')>0)
    {
      xhttp.open("GET", "EditTableInfo.php?Table=<? echo $TableName ?>&DBName=<? echo $_REQUEST["DBName"] ?>&FlagFieldName="+FieldName, true);
    }
    else
    {
      xhttp.open("GET", "EditTableInfo.php?Table=<? echo $TableName ?>&DBName=<? echo $_REQUEST["DBName"] ?>&UnFlagFieldName="+FieldName, true);
    }
    xhttp.send();    
  }

  function EnableDisableField(FieldName)
  {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
	if(document.getElementById('Flag2Img_'+FieldName).src.indexOf('images/enable.png')>0)
	  document.getElementById('Flag2Img_'+FieldName).src = 'images/disable.png';	
	else
	  document.getElementById('Flag2Img_'+FieldName).src = 'images/enable.png';	
      }
    };
    if(document.getElementById('Flag2Img_'+FieldName).src.indexOf('images/enable.png')>0)
    {
      xhttp.open("GET", "EditTableInfo.php?Table=<? echo $TableName ?>&DBName=<? echo $_REQUEST["DBName"] ?>&DisableFieldName="+FieldName, true);
    }
    else
    {
      xhttp.open("GET", "EditTableInfo.php?Table=<? echo $TableName ?>&DBName=<? echo $_REQUEST["DBName"] ?>&EnableFieldName="+FieldName, true);
    }
    xhttp.send();    
  }
  
  function OpenQueryFinder()
  {
    if(confirm("این عملیات ممکن است طول بکشد. اطمینان دارید؟")) 
      window.open("EditTableInfo.php?ShowPagesThatQueryThisTable=<? echo $TableName ?>");
  }
  function OpenPageFinder()
  {
    window.open("EditTableInfo.php?ShowRelatedActivitiesAndPages=<? echo $TableName; if($AliasName!="") echo "&AliasName=".$AliasName; ?>");
  }
  function OpenFieldPageFinder(FieldName)
  {
    window.open("EditTableInfo.php?ShowRelatedActivitiesAndPages=<? echo $TableName ?>&FieldName="+FieldName);
  }
  
  function GetValue(InputName, ActualValue)
  {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
	//alert(xhttp.responseText);
	document.getElementById(InputName).value = xhttp.responseText;	
      }
    };
    xhttp.open("GET", "EditTableInfo.php?GetValue="+ActualValue, true);
    xhttp.send();    
  }

  function SaveValue(InputName, FieldID)
  {
    var description = document.getElementById(InputName).value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
	alert('ذخیره شد');
	//document.getElementById(InputName).value = xhttp.responseText;	
      }
    };
    xhttp.open("GET", "EditTableInfo.php?SaveFieldID="+FieldID+"&SaveValue="+description, true);
    xhttp.send();    
  }
  
  
  function ShowRecordsCount()
  {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
	document.getElementById('RecCountSpan').innerHTML = xhttp.responseText;	
      }
    };
    xhttp.open("GET", "EditTableInfo.php?ShowRecCount=<? echo $_REQUEST["DBName"].".".$TableName ?>", true);
    xhttp.send();    
  }
  <?
    if($TableExist=="1") echo "ShowRecordsCount();\r\n";
  ?>
  
  function ShowHide()
  {
    if(document.getElementById('FTR').style.display=='') 
      document.getElementById('FTR').style.display="none"; 
    else 
      document.getElementById('FTR').style.display=""; 
  }
 </script>
</body>
</html>