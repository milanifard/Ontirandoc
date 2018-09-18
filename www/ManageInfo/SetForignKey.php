<?
	include("header.inc.php");
	HTMLBegin();
	$mysql = pdodb::getInstance();
	
	if(isset($_REQUEST["ShowDomainKeys"]))
	{
	  $res = $mysql->Execute("select DomainName, group_concat(description) as members from baseinfo.domains group by DomainName order by DomainName");
	  echo "<table border=1 cellspacing=0 cellpadding=5><tr><td>نام کلید</td><td>مقادیر</td></tr>";
	  while($rec = $res->fetch())
	  {
	    echo "<tr>";
	    echo "<td><a href='javascript: SelectDomainName(\"".$rec["DomainName"]."\")'>".$rec["DomainName"]."</a></td>";
	    echo "<td>".$rec["members"]."</td>";
	    echo "</tr>";
	  }
	  echo "</table>";
?>
	  <script>
	  function SelectDomainName(DomainName) 
	  { 
	    window.opener.document.getElementById('RelationCondition').value="DomainName='"+DomainName+"'";
	    window.close(); 
	  } 
	  </script>
<?
	  die();
	}

	if(isset($_REQUEST["ShowResearchFormStatuses"]))
	{
	  $res = $mysql->Execute("select FormType, group_concat(StatusDesc) as members from research.FormStatuses group by FormType order by FormType");
	  echo "<table border=1 cellspacing=0 cellpadding=5><tr><td>نام کلید</td><td>مقادیر</td></tr>";
	  while($rec = $res->fetch())
	  {
	    echo "<tr>";
	    echo "<td><a href='javascript: SelectStatusName(\"".$rec["FormType"]."\")'>".$rec["FormType"]."</a></td>";
	    echo "<td>".$rec["members"]."</td>";
	    echo "</tr>";
	  }
	  echo "</table>";
?>
	  <script>
	  function SelectStatusName(FormType) 
	  { 
	    window.opener.document.getElementById('RelationCondition').value="FormType='"+FormType+"'";
	    window.close(); 
	  } 
	  </script>
<?
	  die();
	}

	if(isset($_REQUEST["ShowBasicType"]))
	{
	  $res = $mysql->Execute("select Basic_Type.TypeID, Basic_Type.Title, group_concat(Basic_Info.Title) as members
				  from hrmstotal.Basic_Info
				  JOIN hrmstotal.Basic_Type using (TypeID)
				  group by Basic_Type.TypeID, Basic_Type.Title");
	  echo "<table border=1 cellspacing=0 cellpadding=5><tr><td>نام کلید</td><td>مقادیر</td></tr>";
	  while($rec = $res->fetch())
	  {
	    echo "<tr>";
	    echo "<td><a href='javascript: SelectStatusName(\"".$rec["TypeID"]."\")'>".$rec["Title"]."</a></td>";
	    echo "<td>".$rec["members"]."</td>";
	    echo "</tr>";
	  }
	  echo "</table>";
?>
	  <script>
	  function SelectStatusName(TypeID) 
	  { 
	    window.opener.document.getElementById('RelationCondition').value="TypeID='"+TypeID+"'";
	    window.close(); 
	  } 
	  </script>
<?
	  die();
	}
	
	if(isset($_REQUEST["Save"]) && $_REQUEST["Save"]=="1")
	{
		$FieldName = $_REQUEST["FieldName"];
		$TableName = $_REQUEST["SelectedTableName"];
		$DBName = $_REQUEST["SelectedDBName"];
		
		$SelectedTableName = $_REQUEST["SelectedTableName"];
		$SelectedFieldName = $_REQUEST["SelectedFieldName"];
		$RelationCondition = $_REQUEST["RelationCondition"];
		
		echo "<script>";
		echo "window.opener.document.f1.".$FieldName."_RelatedDBName".$_REQUEST["FK"].".value='".$DBName."'; ";
		echo "window.opener.document.f1.".$FieldName."_RelatedTable".$_REQUEST["FK"].".value='".$TableName."'; ";
		echo "window.opener.document.f1.".$FieldName."_RelatedField".$_REQUEST["FK"].".value='".$SelectedFieldName."'; ";
		echo "window.opener.document.f1.".$FieldName."_RelationCondition".$_REQUEST["FK"].".value=\"".$RelationCondition."\"; ";
		if($TableName=="")
			echo "window.opener.document.getElementById('".$FieldName."_Span".$_REQUEST["FK"]."').innerHTML='___'; ";
		else
		{
		  $FKey = $DBName.".".$TableName.".".$SelectedFieldName;
		  if($RelationCondition!="")
		    $FKey .= " (".$RelationCondition.")";
		  echo "window.opener.document.getElementById('".$FieldName."_Span".$_REQUEST["FK"]."').innerHTML=\"".$FKey."\"; ";
		}
		echo "window.close(); ";
		echo "</script>";
	}
	$res = $mysql->Execute("select distinct DBName from mis.MIS_Tables order by DBName");
	$DBOptions = "<option value=''> ";
	while($rec = $res->fetch())
	{
		$DBOptions .= "<option value='".$rec["DBName"]."' ";
		if(isset($_REQUEST["SelectedDBName"]) &&  $_REQUEST["SelectedDBName"]==$rec["DBName"])
			$DBOptions .= " selected ";
		$DBOptions .= " >".$rec["DBName"];
	}
	
	$TableOptions = "";
	if(isset($_REQUEST["SelectedDBName"]))
	{
	  $DBName = $_REQUEST["SelectedDBName"];
	  $mysql->Prepare("select * from mis.MIS_Tables where DBName=? order by name");
	  $res = $mysql->ExecuteStatement(array($DBName));
	  $TableOptions = "<option value=''> ";
	  while($rec = $res->fetch())
	  {
		  $TableOptions .= "<option value='".$rec["name"]."' ";
		  if(isset($_REQUEST["SelectedTableName"]) &&  $_REQUEST["SelectedTableName"]==$rec["name"])
			  $TableOptions .= " selected ";
		  $TableOptions .= " >".$rec["name"];
	  }
	}

	$FieldOptions = "";
	if(isset($_REQUEST["SelectedTableName"]))
	{
	  $TableName = $_REQUEST["SelectedTableName"];
	  $mysql->Prepare("select * from mis.MIS_TableFields where DBName=? and TableName=? order by FieldName");
	  $res2 = $mysql->ExecuteStatement(array($DBName, $TableName));
	  while($rec2 = $res2->fetch())
		  $FieldOptions .= "<option value='".$rec2["FieldName"]."'>".$rec2["FieldName"];
	}
	if(isset($_REQUEST["FieldName"]))
		$FieldName = $_REQUEST["FieldName"];
	else
		$FieldName = "";
?>
<form name=f2>
<input type=hidden name=FieldName value='<?= $FieldName ?>'>
<input type=hidden name=FK value='<? echo $_REQUEST["FK"]; ?>'>
<input type=hidden name=TableName value='<? echo $_REQUEST["TableName"] ?>'>
<input type=hidden name=Save value='0'>
<table align=center border=1 width=80% cellspacing=0 cellpadding=5>
<tr>
	<td>
	<table width=100%>
		<tr>
			<td colspan=2 align=center bgcolor=#cccccc>
				انتخاب جدول و فیلد منبع برای
				<? echo $_REQUEST["TableName"].".".$_REQUEST["FieldName"] ?>
			</td>
		</tr>
		<tr>
		  <td colspan=2 dir=ltr>
		  <a href='SetForignKey.php?TableName=<? echo $_REQUEST["TableName"] ?>&FieldName=<? echo $_REQUEST["FieldName"] ?>&SelectedDBName=hrmstotal&SelectedTableName=Basic_Info'>Basic_Info</a>
		  <a href='SetForignKey.php?TableName=<? echo $_REQUEST["TableName"] ?>&FieldName=<? echo $_REQUEST["FieldName"] ?>&SelectedDBName=baseinfo&SelectedTableName=domains'>domains</a>
		  </td>
		</tr>
		<tr>
			<td>نام پایگاه داده: </td>
			<td>
			<select dir=ltr name='SelectedDBName' onchange='javascript: document.f2.submit();'><?= $DBOptions ?></select>
			</td>
		</tr>
		<tr>
			<td>نام جدول: </td>
			<td><select dir=ltr name='SelectedTableName' onchange='javascript: document.f2.submit();'><?= $TableOptions ?></select></td>
		</tr>
		<tr>
			<td>نام فیلد: </td>
			<td><select dir=ltr name='SelectedFieldName'><?= $FieldOptions ?></select></td>
		</tr>
		<tr>
			<td>شرط ارتباط: </td>
			<td>
			<input type=text id='RelationCondition' name='RelationCondition' dir=ltr value='' size=60>
			<? if(isset($_REQUEST["SelectedTableName"]) && $_REQUEST["SelectedTableName"]=="domains") { ?>
			<a href='SetForignKey.php?ShowDomainKeys=1' target=_blank>مشاهده لیست کلیدها</a>
			<? } ?>
			<? if(isset($_REQUEST["SelectedTableName"]) && $_REQUEST["SelectedTableName"]=="FormStatuses") { ?>
			<a href='SetForignKey.php?ShowResearchFormStatuses=1' target=_blank>مشاهده لیست کلیدها</a>
			<? } ?>
			<? if(isset($_REQUEST["SelectedTableName"]) && $_REQUEST["SelectedTableName"]=="Basic_Info") { ?>
			<a href='SetForignKey.php?ShowBasicType=1' target=_blank>مشاهده لیست کلیدها</a>
			<? } ?>
			
			
			</td>
		</tr>
		<tr>
			<td colspan=2 align=center bgcolor=#cccccc>
				<input type=button onclick="javascript: document.f2.Save.value='1'; document.f2.submit();" value='&nbsp;ذخیره&nbsp;'>
				&nbsp;
				<input type=button onclick="javascript: window.close();" value='&nbsp;انصراف&nbsp;'>
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>
</form>
<br>

<table width=80% align=center border=1 cellspacing=0 cellpadding=5>
    <tr class=HeaderOfTable>
      <td dir=rtl>
پیشنهاد کلید خارجی به جداول دیگر بر اساس نام مشابه
 (مرتب بر اساس تعداد استفاده به عنوان کلید مرتبط)
</td>
</tr>
<?
    if($_REQUEST["FieldName"]=="StNo")
    {
	echo "<tr>";
	echo "<td dir=ltr><a href=\"SetForignKey.php?FK=".$_REQUEST["FK"]."&SelectedDBName=educ&TableName=StudentSpecs&FieldName=StNo&Save=1&SelectedTableName=StudentSpecs&SelectedFieldName=StNo&RelationCondition=\">";
	echo "educ.StudentSpecs.StNo";
	echo "</td>";
	echo "</tr>";    
    }
    else if($_REQUEST["FieldName"]=="PersonID")
    {
	echo "<tr>";
	echo "<td dir=ltr><a href=\"SetForignKey.php?FK=".$_REQUEST["FK"]."&SelectedDBName=hrmstotal&TableName=persons&FieldName=PersonID&Save=1&SelectedTableName=persons&SelectedFieldName=PersonID&RelationCondition=\">";
	echo "hrmstotal.persons.PersonID";
	echo "</td>";
	echo "</tr>";    
	echo "<tr>";
	echo "<td dir=ltr><a href=\"SetForignKey.php?FK=".$_REQUEST["FK"]."&SelectedDBName=educ&TableName=persons&FieldName=PersonID&Save=1&SelectedTableName=persons&SelectedFieldName=PersonID&RelationCondition=\">";
	echo "educ.persons.PersonID";
	echo "</td>";
	echo "</tr>";    
    }
    else 
    {
      $mysql->Prepare("select *, (select count(*) from mis.MIS_TableFields m where 
			m.RelatedTable=m2.TableName and m.RelatedDBName=m2.DBName) as tcount
			from mis.MIS_TableFields m2 where 
				  m2.FieldName=? and m2.TableName<>?
				  and (m2.FieldType like 'INT%' or m2.FieldType like 'TINY%' or m2.FieldType like 'SMALL%')
				  and (m2.RelatedTable is null or m2.RelatedTable='')
				  and KeyType='PRI' order by tcount DESC limit 0,6");
      $res = $mysql->ExecuteStatement(array($_REQUEST["FieldName"], $_REQUEST["TableName"]));
      if($res->rowCount==0)
      {
	// در صورتیکه کلید مربوطه به صورت صریح در سایر جداول کلید اصلی تعریف نشده باشد هر فیلدی با آن نام را می آورد
	$mysql->Prepare("select *, (select count(*) from mis.MIS_TableFields m where 
			m.RelatedTable=m2.TableName and m.RelatedDBName=m2.DBName) as tcount
			from mis.MIS_TableFields m2 where 
				  m2.FieldName=? and m2.TableName<>?
				  and (m2.RelatedTable is null or m2.RelatedTable='') order by tcount DESC limit 0,10");
	$res = $mysql->ExecuteStatement(array($_REQUEST["FieldName"], $_REQUEST["TableName"]));

      }

      while($rec = $res->fetch())
      {
	  if($i%2==0)
		  echo "<tr bgcolor=#efefef>";
	  else
		  echo "<tr>";
	  echo "<td dir=ltr><a href=\"SetForignKey.php?FK=".$_REQUEST["FK"]."&SelectedDBName=".$rec["DBName"]."&TableName=".$_REQUEST["TableName"]."&FieldName=".$FieldName."&Save=1&SelectedTableName=".$rec["TableName"]."&SelectedFieldName=".$rec["FieldName"]."&RelationCondition=\">";
	  echo $rec["DBName"].".".$rec["TableName"].".".$rec["FieldName"];
	  echo "</td>";
	  echo "</tr>";
      }
    }
?>
</table>
<br>													   
<table width=80% align=center border=1 cellspacing=0 cellpadding=5 class=HeaderOfTable>
    <tr>
      <td colspan=3>
      لیست پر استفاده ترین کلیدها
	<br>														      
      </td>
    </tr>
	<? 
	    $res = $mysql->Execute("select RelatedDBName, RelatedTable, RelatedField, RelationCondition, count(*) from mis.MIS_TableFields where
										    RelatedTable<>'' Group by RelatedDBName, RelatedTable, RelatedField, RelationCondition order by count(*) DESC limit 0,20");
	    while($rec = $res->fetch())
	    {
		if($i%2==0)
			echo "<tr bgcolor=#efefef>";
		else
			echo "<tr >";
		echo "<td dir=ltr>";
		echo "<a href=\"SetForignKey.php?FK=".$_REQUEST["FK"]."&SelectedDBName=".$rec["RelatedDBName"]."&FieldName=".$FieldName."&Save=1&SelectedTableName=".$rec["RelatedTable"]."&SelectedFieldName=".$rec["RelatedField"]."&RelationCondition=".$rec["RelationCondition"]."\">";
		echo $rec["RelatedField"];
		if($rec["RelationCondition"]!="")
		  echo " (".$rec["RelationCondition"].")";
		echo "</td>";
		echo "<td dir=ltr width=1% nowrap>".$rec["RelatedTable"]."</td>";
		echo "<td dir=ltr width=1% nowrap>".$rec["RelatedDBName"]."</td>";
		echo "</tr>";
	    }
	?>
	<tr>
	</tr>
</table>
</body></html>