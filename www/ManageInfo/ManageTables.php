<?
  include("header.inc.php");
  HTMLBegin();
  $mysql = pdodb::getInstance();
  
  if(!isset($_SESSION["sTableName"]))
  {
    $_SESSION["DB"] = "";
    $_SESSION["sTableName"] = "";
    $_SESSION["sdescription"] = "";
  }	
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
  
  if(isset($_POST["sTableName"])) 
  {
    $_SESSION["DB"] = $_POST["DB"];
    $_SESSION["sTableName"] = $_POST["sTableName"];
    $_SESSION["sdescription"] = $_POST["sdescription"];
  }

  if(isset($_REQUEST["UpdateTableID"]) && $_REQUEST["UpdateTableID"]!="0")
  {
      $query = "SELECT * from mis.TABLES_SCHEMA where TABLE_SCHEMA=? and TABLE_NAME=?";
      $mysql->Prepare($query);
      $tmp = $mysql->ExecuteStatement(array($_REQUEST["UpdateDBName"], $_REQUEST["UpdateTableName"]));
      if($trec = $tmp->fetch())
      {
	$mysql->Prepare("update mis.MIS_Tables set description='".$trec["TABLE_COMMENT"]."' where id=? and description=''");
	$mysql->ExecuteStatement(array($_REQUEST["UpdateTableID"]));
	
	$mysql->Prepare("select * from mis.COLUMNS where TABLE_SCHEMA=? and TABLE_NAME=? and COLUMN_COMMENT is not null and COLUMN_COMMENT<>''");
	$fields = $mysql->ExecuteStatement(array($_REQUEST["UpdateDBName"], $_REQUEST["UpdateTableName"]));
	while($frec = $fields->fetch())
	{
	  $mysql->Prepare("update mis.MIS_TableFields set description='".$frec["COLUMN_COMMENT"]."' where DBName=? and TableName=? and FieldName='".$frec["COLUMN_NAME"]."' and description=''");
	  $mysql->ExecuteStatement(array($_REQUEST["UpdateDBName"], $_REQUEST["UpdateTableName"]));
	}
      }
  }
?>

<form method=post id=f1 name=f1>
<input type=hidden name=UpdateTableID id=UpdateTableID value=0>
<input type=hidden name=UpdateTableName id=UpdateTableName value=''>
<input type=hidden name=UpdateDBName id=UpdateDBName value=''>
<table align=center border=1 width=80% cellspacing=0 cellpadding=5>
<tr>
	<td>
	<table width=100%>
		<tr>
			<td colspan=2 align=center bgcolor=#cccccc>
				جستجوی جداول
			</td>
		</tr>
		<tr>
			<td>DataBase: </td>
			<td>
			<select name=DB>
			<option value=''>-
			<?php
				$res = $mysql->Execute("select distinct DBName from mis.MIS_Tables order by DBName");
				while($rec = $res->fetch())
				{
					echo "<option value='".$rec["DBName"]."' ";
					if($rec["DBName"]==$_SESSION["DB"])
						echo " selected ";
					echo ">";
					echo  $rec["DBName"];
				}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td>نام جدول: </td>
			<td><input type=text name=sTableName dir=ltr value='<?= $_SESSION["sTableName"] ?>'></td>
		</tr>
		<tr>
			<td>کلمه کلیدی: </td>
			<td><input type=text name=sdescription value='<?= $_SESSION["sdescription"] ?>'>
			<input type=checkbox name=SearchInT id=SearchInT <? if(isset($_REQUEST["sTableName"])) { if(isset($_REQUEST["SearchInT"])) echo "checked"; } else echo "checked"; ?> > شرح جدول
			<input type=checkbox name=SearchInF id=SearchInF <? if(isset($_REQUEST["SearchInF"])) echo "checked"; ?> > شرح فیلد
			<input type=checkbox name=SearchInD id=SearchInD <? if(isset($_REQUEST["SearchInD"])) echo "checked"; ?> > شرح مقادیر شمارشی
			</td>
		</tr>
		<tr>
			<td>نوع جستجو</td>
			<td><select name=SearchType id=SearchType>
			<option value='LIKE' >جستجوی مواردی که حاوی کلمه کلیدی وارد شده باشند
			<option value='EXACT' <? if(isset($_REQUEST["SearchType"]) && $_REQUEST["SearchType"]=="EXACT") echo "selected"; ?> >جستجوی مواردی که دقیقا برابر کلمه کلیدی وارد شده باشند
		</tr>
		<tr>
			<td>وضعیت جدول</td>
			<td><select name=TableStatus id=TableStatus>
			<option value=''>-
			<option value='ENABLE'>فعال
			<option value='DISABLE'>غیرفعال
		</tr>
		<tr>
			<td colspan=2><input type=checkbox name=OnlyIncomplete>فقط جداول مستند نشده </td>
		</tr>
		<tr>
			<td colspan=2><input type=checkbox name=OnlyFlag>فقط جداول نشان گذاری شده</td>
		</tr>
		<tr>
			<td colspan=2><input type=checkbox name=OnlyTableName>تنها نمایش نام جداول</td>
		</tr>
		<tr>
			<td colspan=2><input type=checkbox name=OnlyNotSystem>جداول سیستمی نشان داده نشود</td>
		</tr>
		<tr>
			<td>فقط جداول مرتبط با حوزه: </td>
			<td>
			  <input type=checkbox id=ch_educ name=ch_educ <? if($EducationalDomain=="YES") echo "checked"; ?> >آموزشی
			  <input type=checkbox id=ch_research name=ch_research <? if($ResearchDomain=="YES") echo "checked"; ?> >پژوهشی
			  <input type=checkbox id=ch_student name=ch_student <? if($StudentServiceDomain=="YES") echo "checked"; ?> >خدمات دانشجویی
			  <input type=checkbox id=ch_support name=ch_support <? if($SupportDomain=="YES") echo "checked"; ?> >پشتیبانی 
			  <input type=checkbox id=ch_system name=ch_system <? if($SystemRelatedDomain=="YES") echo "checked"; ?> >سیستمی
			</td>
		</tr>		
		<tr>
			<td colspan=2 align=center bgcolor=#cccccc>
				<input type=submit value='&nbsp;جستجو&nbsp;'>
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>
</form>
<? 
if(isset($_POST["sTableName"])) 
{
  $Params = array();
	$_SESSION["DB"] = $_POST["DB"];
	$_SESSION["sTableName"] = $_POST["sTableName"];
	$_SESSION["sdescription"] = $_POST["sdescription"];
	
	if($_REQUEST["SearchType"]=="LIKE")
	{
	  $query = "select * from mis.MIS_Tables where status=1 and name like ?  ";
	  array_push($Params, "%".$_POST["sTableName"]."%");
	}
	else 
	{
	  $query = "select * from mis.MIS_Tables where status=1 and name=?  ";
	  array_push($Params, $_POST["sTableName"]);

	}
	$cond = "";
	if($_POST["sdescription"]!="")
	{
	  if(isset($_REQUEST["SearchInT"]))
	  {
	    if($_REQUEST["SearchType"]=="LIKE")
	    {
	      $cond .= " description like ? ";
	      array_push($Params, "%".$_POST["sdescription"]."%");	    
	    }
	    else
	    {
	      $cond .= " description=? ";
	      array_push($Params, $_POST["sdescription"]);	    
	    }
	  }
	  
	  if(isset($_REQUEST["SearchInF"]))
	  {
	    if($cond!="")
	      $cond .= " or ";
	    if($_REQUEST["SearchType"]=="LIKE")
	    {
	      $cond .= " concat(DBName, name) in (select concat(DBName, TableName) from mis.MIS_TableFields where FieldName like ?) ";
	      array_push($Params, "%".$_POST["sdescription"]."%");	    
	    }
	    else
	    {
	      $cond .= " concat(DBName, name) in (select concat(DBName, TableName) from mis.MIS_TableFields where FieldName=?) ";
	      array_push($Params, $_POST["sdescription"]);	    
	    }
	  }

	  if(isset($_REQUEST["SearchInD"]))
	  {
	    if($cond!="")
	      $cond .= " or ";
	    $cond .= " concat(DBName, name) in (
		      select concat(MIS_TableFields.DBName, MIS_TableFields.TableName) 
			      from 
				  mis.MIS_TableFields 
				  JOIN mis.FieldsDataMapping using (DBName, TableName, FieldName) where ";
	    if($_REQUEST["SearchType"]=="LIKE")
	    {
	      $cond .= " ShowValue like ? ";
	      array_push($Params, "%".$_POST["sdescription"]."%");	    
	    }
	    else
	    {
	      $cond .= " ShowValue = ? ";
	      array_push($Params, $_POST["sdescription"]);	    
	    }
	    $cond .= ") ";
	  }
	}
	if($cond!="")
	  $query .= " and (".$cond.") ";
	
	if($_POST["DB"]!="")
	{
	  $query .= " and DBName=? ";
	  array_push($Params, $_POST["DB"]);
	}
	
	if(isset($_REQUEST["OnlyNotSystem"]))	
		$query .= " and SystemRelatedDomain='NO' ";
	
	if(isset($_REQUEST["OnlyIncomplete"]))
	  $query .= " and (description='' or description is null) and (EntityName='' or EntityName is null) ";
	if(isset($_REQUEST["OnlyFlag"]))
	  $query .= " and FlagTable='YES' ";
	if(isset($_REQUEST["TableStatus"]) && $_REQUEST["TableStatus"]!="")
	{
	  if($_REQUEST["TableStatus"]=="ENABLE")
	    $query .= " and TableStatus='ENABLE' ";
	  else 
  	    $query .= " and TableStatus='DISABLE' ";
	}
	if($EducationalDomain=="YES" || $ResearchDomain=="YES" || $SupportDomain=="YES" || $StudentServiceDomain=="YES" || $SystemRelatedDomain=="YES")
	{
	  $query .= " and (";
	  $econd = "";
	  if($EducationalDomain=="YES")
	    $econd .= "EducationalDomain='YES'";
	  if($ResearchDomain=="YES")
	  {
	    if($econd!="")
	      $econd .= " OR ";
	    $econd .= "ResearchDomain='YES'";
	  }
	  if($SupportDomain=="YES")
	  {
	    if($econd!="")
	      $econd .= " OR ";
	    $econd .= "SupportDomain='YES'";
	  }
	  if($StudentServiceDomain=="YES")
	  {
	    if($econd!="")
	      $econd .= " OR ";
	    $econd .= "StudentServiceDomain='YES'";
	  }
	  if($SystemRelatedDomain=="YES")
	  {
	    if($econd!="")
	      $econd .= " OR ";
	    $econd .= "SystemRelatedDomain='YES'";
	  }
	  
	  $query .= $econd." ) ";
	  $_SESSION["DomainCondition"] = $econd;
	}
	else 
	{
	    $_SESSION["DomainCondition"] = "";
	}
	
	$query .= " order by name";
	
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement($Params);
	
	if(isset($_REQUEST["OnlyTableName"]))
	{
	  while($rec = $res->fetch())
	    echo $rec["name"]."<br>";
	  die();
	}
	
	echo "<table width=98% align=center border=1 cellspacing=0 cellpadding=5 dir=rtl>";
	echo "<tr bgcolor=#cccccc>";
	echo "<td>ردیف</td><td>DB</td><td>نام</td><td>شرح</td><td width=1% nowrap>حوزه</td><td>آخرین بروزرسانی</td>";
	echo "</tr>";
	$i = 0;
	while($rec = $res->fetch())
	{
	  $i++;
	  if($rec["TableStatus"]=="DISABLE")
	    echo "<tr bgcolor=#adad85>";
	  else if($rec["TempOrCompletelyTransactional"]=="YES")
	    echo "<tr bgcolor=#ffccff>";
	  else
	  {
	    if($i%2==0)
		    echo "<tr>";
	    else
		    echo "<tr bgcolor=#efefef>";
	  }
	  echo "<td>&nbsp;".($i)."</td>";
	  echo "<td>&nbsp;".$rec["DBName"]."</td>";
	  echo "<td align=left>";
	  if($rec["FlagTable"]=="YES")
	    echo "<img src='images/star2.png' width=20> ";
	  echo "<a href='EditTableInfo.php?sTableName=".$_POST["sTableName"]."&sdescription=".$_POST["sdescription"]."&DB=".$_POST["DB"]."&TableName=".$rec["name"]."&DBName=".$rec["DBName"]."&ServerName=".$rec["ServerName"]."'>".$rec["name"]."</a></td>";
	  //echo "<td>&nbsp;".$rec["TableType"]."</td>";
	  //echo "<td>&nbsp;".$rec["EntityName"]."</td>";
	  echo "<td width=50%>&nbsp;".$rec["description"]."</td>";
	  echo "<td nowrap>";
	  if($rec["EducationalDomain"]=="YES") echo "آموزشی<br>";
	  if($rec["ResearchDomain"]=="YES") echo "پژوهشی<br>";
	  if($rec["StudentServiceDomain"]=="YES") echo "خدمات دانشجویی<br>";
	  if($rec["SupportDomain"]=="YES") echo "پشتیبانی<br>";
	  if($rec["SystemRelatedDomain"]=="YES") echo "سیستمی<br>";
	  echo "</td>";
	  //echo "<td width=1% nowrap><a href='javascript: document.f1.UpdateTableID.value=\"".$rec["id"]."\"; document.f1.UpdateTableName.value=\"".$rec["name"]."\"; document.f1.UpdateDBName.value=\"".$rec["DBName"]."\"; document.f1.submit();'><img width=30 title='بروزرسانی مستندات از روی خود جدول' src='images/copy2.gif' border=0></a></td>";
	  echo "<td>";
	  if($rec["LastUpdateYear"]!="")
	    echo $rec["LastUpdateYear"]."-".$rec["LastUpdateMonth"]."-".$rec["LastUpdateDay"];
	  echo "<br>";
	  echo $rec["LastUpdateUser"];
	  echo "</td>";
	  echo "</tr>";
	}
	echo "</table>";
} 
?>
</body>
</html>
