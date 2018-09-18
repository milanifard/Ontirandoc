<?
  $NotAddSlashes = "1";
  include("header.inc.php");

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
	  $mysql->Prepare("select distinct page from data_analysis.SystemDBLog where query like ?");
	  $res = $mysql->ExecuteStatement(array("% ".$_REQUEST["ShowPagesThatQueryThisTable"]." %"));
	  if($res->rowCount()>0)
	  {
	    echo "<table align=center cellspacing=0 cellpadding=5 border=1 dir=ltr>";
	    echo "<tr class=HeaderOfTable><td colspan=2>صفحاتی که به این جدول کوئری زده اند</td></tr>";
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
	  $mysql->Prepare("select content from monitoring.PageContent where path=? and name=?");
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
	  $query = "select path, name from monitoring.PageContent 
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
				   educ.g2j(ActivityDate) as gActivityDate
			    from projectmanagement.ProjectTaskActivities
			    LEFT JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
			    LEFT JOIN projectmanagement.projects using (ProjectID)
			    LEFT JOIN hrmstotal.persons on (persons.PersonID=ProjectTaskActivities.CreatorID)
			    where ChangedTables like ?");
	  $res = $mysql->ExecuteStatement(array("%".$TableName."%"));
	  echo "<table border=1 cellpadding=5 cellspacing=0 align=center width=90%>";
	  if($res->rowCount()>0)
	  {
	    echo "<tr class=HeaderOfTable>";
	    echo "<td>پروژه</td><td>کار مربوطه</td><td>شرح کار</td><td>اقدام</td><td>اقدام کننده</td><td>تاریخ اقدام</td><td>جداول تغییریافته</td><td>صفحات تغییریافته</td>";
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
	    echo "<td>".$rec["ProjectTitle"]."</td>";
	    echo "<td>";
	    echo $rec["TaskTitle"];
	    echo "</td>";
	    echo "<td>";
	    echo str_replace("\n", "<br>", $rec["description"]);
	    echo "</td>";
	    echo "<td>".$rec["ActivityDescription"]."</td>";
	    echo "<td>".$rec["plname"]." ".$rec["pfname"]."</td>";
	    echo "<td>".$rec["gActivityDate"]."</td>";
	    echo "<td>".$rec["ChangedTables"]."</td>";
	    echo "<td>".$rec["ChangedPages"]."</td>";
	    echo "</tr>";
	  }
	  echo "</table>";
	  echo "<br>";
	  
	  $query = "select path, name from monitoring.PageContent 
			    where type='php' and 
			    (StaticContent like ? or StaticContent like ? ";
	  if(isset($_REQUEST["AliasName"]) && $_REQUEST["AliasName"]!="")
	    $query .= " or content like ?) ";
	  else {
	      $query .= ")";
	  }
	  
	  $mysql->Prepare($query);
	  if(isset($_REQUEST["AliasName"]) && $_REQUEST["AliasName"]!="")
	    $res = $mysql->ExecuteStatement(array("%.".$TableName." %", "% ".$TableName." %", "%::".$_REQUEST["AliasName"]."%"));
	  else
	    $res = $mysql->ExecuteStatement(array("%.".$TableName." %", "% ".$TableName." %"));	  
	  echo "<table border=1 cellpadding=5 cellspacing=0 align=center width=90%>";
	  if($res->rowCount()>0)
	  {
	    echo "<tr bgcolor=#eeeeee><td colspan=4>فایلهایی که در محتوای کد آنها نام این جدول استفاده شده است</td></tr>";	  
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
	  echo "</table>";
	  
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
	if($TableExist=="1")	
	{
	  $query = "select count(*) as tcount from ".$_REQUEST["DBName"].".".$_REQUEST["TableName"];
	  $res = $mysql->Execute($query);
	  $rec = $res->fetch();
	  $RecordsCount = $rec["tcount"];
	  //echo $RecordsCount;
	}
	

	$Link = "ShowTableInfo.php?sTableName=".$sTableName."&sdescription=".$sdescription."&DB=".$DBName;
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

	$TableName = $_REQUEST["TableName"];
	$DB = $_REQUEST["DB"];
	$query = "select * from mis.MIS_Tables where name=? and DBName=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($TableName, $_REQUEST["DBName"]));
	if($rec = $res->fetch())
	{
	  $TableType = $rec["TableType"];
	  $EntityName = $rec["EntityName"];	
	  $description = $rec["description"];
	  $TableStatus = $rec["TableStatus"];
	  $FlagTable = $rec["FlagTable"];
	  $AliasName = $rec["AliasName"];
	  $CurDBName = $rec["DBName"];
	  $CurTableName = $rec["name"];
	}
	else {
	    echo "جدول پیدا نشد";
	    die();
	}

	$query = "select * from mis.MIS_Tables where status=1 and name like ? and description like ? and DBName like ? ";
	if(isset($_REQUEST["OnlyIncomplete"]))
		$query .= " and (description='' or description is null) and (EntityName='' or EntityName is null) ";
	$query .= " and name>? ";
	$query .= " order by name limit 1";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array("%".$sTableName."%", "%".$sdescription."%", $DBName."%", $TableName));
	if($trec = $res->fetch())
		$NextLink = $Link."&TableName=".$trec["name"]."&DBName=".$trec["DBName"]."&ServerName=".$trec["ServerName"];
	else
		$NextLink = "#";

	$query = "select * from mis.MIS_Tables where status=1 and name like ? and description like ? and DBName like ? ";
	if(isset($_REQUEST["OnlyIncomplete"]))
		$query .= " and (description='' or description is null) and (EntityName='' or EntityName is null) ";
	$query .= " and name<? ";
	$query .= " order by name DESC limit 1";

	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array("%".$sTableName."%", "%".$sdescription."%", $DBName."%", $TableName));
	if($trec = $res->fetch())
		$PreLink = $Link."&TableName=".$trec["name"]."&DBName=".$trec["DBName"]."&ServerName=".$trec["ServerName"];
	else
		$PreLink = "#";
	
		?>
<form method=post>
<input type=hidden name=DB value='<?= $DBName ?>'>
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
			<td width=1%> جدول: </td>
			<td>
			<?= $TableName ?> 
			</td>
		</tr>
		<tr>
		  <td nowrap>تعداد رکورد:</td><td><? echo $RecordsCount; ?></td>
		</tr>
		<?
		  $fquery = " select distinct MIS_Tables.DBName, MIS_Tables.name, MIS_Tables.description from mis.MIS_TableFields 
			      JOIN mis.MIS_Tables on (MIS_TableFields.DBName=MIS_Tables.DBName and MIS_TableFields.TableName=MIS_Tables.name) 

			      where RelatedDBName='".$CurDBName."' and RelatedTable='".$CurTableName."' ";
		  $fres = $mysql->Execute($fquery);
		  $ForeignNum = $fres->rowCount();
		?>
		<tr>
		  <td nowrap>
		    جداول متصل:
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
			  echo " <a target=_blank href='ShowTableInfo.php?sTableName=&sdescription=&DB=".$frec["DBName"]."&TableName=".$frec["name"]."&DBName=".$frec["DBName"]."&ServerName='>";
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
			<td>شرح: </td>
			<td><? echo str_replace("\n", "<br>", $description); ?></td>
		</tr>
		<tr>
		  <td>وضعیت:</td>
		  <td>
		    <? if($TableStatus=="DISABLE") echo "<font color=red>غیر فعال</font>"; else echo "فعال"; ?>
		  </td>
		</tr>
		<tr>
			<td colspan=2 align=center bgcolor=#cccccc>
				<input type=button onclick="javascript: document.location='SearchTables.php?sTableName=<?= $sTableName ?>&sdescription=<?= $sdescription ?>'" value='&nbsp;بازگشت&nbsp;'>
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>
</form>

<form name=f1 method=post>
<input type=hidden name=DB value='<?= $DB ?>'>
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
	<table width=100% border=1 cellspacing=0 cellpadding=4>
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
			  from mis.MIS_TableFields where status=1 and TableName=? and DBName=?";
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

	    if($rec["KeyType"]=="PRI")
	      echo "<img src='images/key.gif' width=20>&nbsp;";
	    echo "<font title='".$rec["LastUpdateYear"]."-".$rec["LastUpdateMonth"]."-".$rec["LastUpdateDay"]."\n".$rec["LastUpdateUser"]."'>";
	    echo $rec["FieldName"];
	    echo "</font>";
	    echo "</td>";
	    echo "\r\n<td dir=ltr>".str_replace(",",", ", $rec["FieldType"])."</td>";
	    echo "\r\n<td>";
	    
	    if(IsNumbericField($rec["FieldType"]) && $rec["KeyType"]!="PRI" && $rec["RelatedTable"]=="")
	    {
		$mysql->Prepare("select count(*) as tcount from mis.FieldsDataMapping where DBName=? and TableName=? and FieldName=?");
		$mres = $mysql->ExecuteStatement(array($_REQUEST["DBName"], $TableName, $rec["FieldName"]));
		$mrec = $mres->fetch();
		if($mrec["tcount"]>0)
		{
		  echo "<a target=_blank href='ManageFieldsDataSematics.php?DBName=".$_REQUEST["DBName"]."&TableName=".$TableName."&FieldName=".$rec["FieldName"]."'>";
		  echo "<img src='images/DataTableFull.jpg' width=30 border=0 title='تعریف جدول معادلسازی داده ها'></a>";
		}
	    }
	    else 
	    {
		echo "&nbsp;";
	    }
	    
	    echo "</td>";
	    echo "\r\n<td>";
	    echo str_replace("\n", "<br>", $rec["description"]);
	    echo "</td>";
	    if($rec["RelatedTable"]!="" || $rec["RelatedTable2"]!="" || $rec["RelatedTable3"]!="" || $rec["RelatedTable4"]!="")
	    {
	      $ts = $mysql->Execute("select * from mis.MIS_Tables where DBName='".$rec["RelatedDBName"]."' and name='".$rec["RelatedTable"]."'");
	      if($tsrec = $ts->fetch())
		$font = "<font color=black>";
	      else 
		$font = "<font color=red>";
	      
		    echo "\r\n<td dir=ltr>";
		    if($rec["RelatedTable"]!="")
		    {
		      echo $font."<span name='".$rec["FieldName"]."_Span' id='".$rec["FieldName"]."_Span'>";
		      echo $rec["RelatedDBName"].".".$rec["RelatedTable"].".".$rec["RelatedField"];
		      if($rec["RelationCondition"]!="")
			echo " (".$rec["RelationCondition"].")";
		      echo "</span></font><br>";
		      echo " <a target=_blank href='ShowTableInfo.php?sTableName=&sdescription=&DB=".$rec["RelatedDBName"]."&TableName=".$rec["RelatedTable"]."&DBName=".$rec["RelatedDBName"]."&ServerName='>";
		      echo "<img src='images/list.jpg' border=0 title='مشاهده جدول مربوطه' width=20>";
		      echo "</a><br>";
		    }
		    if($rec["RelatedTable2"]!="")
		    {
		      echo $font."<span name='".$rec["FieldName"]."_Span2' id='".$rec["FieldName"]."_Span2'>";
		      echo $rec["RelatedDBName2"].".".$rec["RelatedTable2"].".".$rec["RelatedField2"];
		      if($rec["RelationCondition2"]!="")
			echo " (".$rec["RelationCondition2"].")";
		      echo "</span></font><br>";
		      echo " <a target=_blank href='ShowTableInfo.php?sTableName=&sdescription=&DB=".$rec["RelatedDBName2"]."&TableName=".$rec["RelatedTable2"]."&DBName=".$rec["RelatedDBName2"]."&ServerName='>";
		      echo "<img src='images/list.jpg' border=0 title='مشاهده جدول مربوطه' width=20>";
		      echo "</a><br>";
		    }
		    if($rec["RelatedTable3"]!="")
		    {
		      echo $font."<span name='".$rec["FieldName"]."_Span3' id='".$rec["FieldName"]."_Span3'>";
		      echo $rec["RelatedDBName3"].".".$rec["RelatedTable3"].".".$rec["RelatedField3"];
		      if($rec["RelationCondition3"]!="")
			echo " (".$rec["RelationCondition3"].")";
		      echo "</span></font><br>";
		      echo " <a target=_blank href='ShowTableInfo.php?sTableName=&sdescription=&DB=".$rec["RelatedDBName3"]."&TableName=".$rec["RelatedTable3"]."&DBName=".$rec["RelatedDBName3"]."&ServerName='>";
		      echo "<img src='images/list.jpg' border=0 title='مشاهده جدول مربوطه' width=20>";
		      echo "</a>";
		    }
		    echo "</td>";
	    }
	    else
	    {
		echo "\r\n<td>&nbsp;";
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
				<input type=button onclick="javascript: document.location='SearchTables.php?sTableName=<?= $sTableName ?>&sdescription=<?= $sdescription ?>'" value='&nbsp;بازگشت&nbsp;'>
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