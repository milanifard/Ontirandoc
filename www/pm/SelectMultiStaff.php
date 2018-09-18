<?
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "on");

	include("header.inc.php");
	HTMLBegin();
	$mysql = pdodb::getInstance();	
	if(isset($_REQUEST["Selected"]))
	{
	  echo "<script>";
	  $res = $mysql->Execute("select PersonID, pfname, plname from projectmanagement.persons");
	  $i = 0;
	  while($rec = $res->fetch())
	  {
	    if(isset($_REQUEST["ch_".$rec["PersonID"]]))
	    {
	      echo "window.opener.document.f1.".$_REQUEST["InputName"].".value = ";
	      if($i>0)
		echo "window.opener.document.f1.".$_REQUEST["InputName"].".value+','+'".$rec["PersonID"]."';\r\n";
	      else
		echo "'".$rec["PersonID"]."';\r\n";
	      
	      
	      echo "window.opener.document.getElementById('".$_REQUEST["SpanName"]."').innerHTML = ";
	      if($i>0)
		echo "window.opener.document.getElementById('".$_REQUEST["SpanName"]."').innerHTML+','+'".$rec["pfname"]." ".$rec["plname"]."';\r\n";
	      else
		echo "'".$rec["pfname"]." ".$rec["plname"]."';\r\n";
		
	      $i++;
	    }
	  }
	  echo "\r\n";
	  echo "window.close();\r\n";
	  echo "</script>";

	  die();
	}
?>
<br>
<form method=post>
<?php
	$FormName = "f1";
// با پاس دادن این مقدار مشخص می شود که فیلتری روی خروجی جستجو گذاشته شود یا خیر
	if(isset($_REQUEST["FilterType"]))
		echo "<input type=hidden name=FilterType id=FilterType value='".$_REQUEST["FilterType"]."'>"; 
	
	if(isset($_REQUEST["FormName"]))
	{
		$FormName = $_REQUEST["FormName"];
		echo "<input type=hidden name=FormName id=FormName value='".$_REQUEST["FormName"]."'>"; 
	}	
	// متغیری برای اینکه فقط نام به آن منتقل شده باشد به صفحه ارسال شده یا خیر
	if(isset($_REQUEST["FInput"]))
		echo "<input type=hidden name=FInput id=FInput value='".$_REQUEST["FInput"]."'>"; 

	if(isset($_REQUEST["LInput"]))
		echo "<input type=hidden name=LInput id=LInput value='".$_REQUEST["LInput"]."'>"; 
?>
<input type=hidden name='InputName' value='<?= $_REQUEST["InputName"] ?>'>
<table width=50% align=center border=1 cellspacing=0 cellpadding=3>
<tr>
	<td>نام: </td><td><input type=text name=FName "></td>
</tr>
<tr>
	<td>نام خانوادگی: </td><td><input type=text name=LName "></td>
</tr>
<tr class='FooterOfTable'>
	<td colspan=2 align=center>
	<input type=submit value='جستجو'>
	&nbsp;
	<input type=button value='حذف انتخاب قبلی' onclick='javascript: ClearLastSelected();'>
	</td>
</tr>
</table>
</form>
<form method=post>
<? if(isset($_REQUEST["SpanName"])) { ?>
  <input type=hidden name=SpanName id=SpanName value='<? echo $_REQUEST["SpanName"] ?>'>
  <input type=hidden name=InputName id=InputName value='<? echo $_REQUEST["InputName"] ?>'>
<? } ?>
<input type=hidden name=Selected id=Selected value=1>
<br>
<table width=50% align=center border=1 cellspacing=0 cellpadding=3>
<tr class='HeaderOfTable'>
	<td width=1%>&nbsp;</td>
	<td>نام و نام خانوادگی</td>
	<td width=5% nowrap>نام کاربر</td>
</tr>
<?
	if(isset($_REQUEST["FName"]))
	{
		$query = "select persons.PersonID, pfname, plname, staff.person_type, WebUserID, ptitle from hrmstotal.persons 
								LEFT JOIN hrmstotal.staff using (PersonID) 
								LEFT JOIN hrmstotal.org_new_units on (org_new_units.ouid=staff.UnitCode)
								LEFT JOIN projectmanagement.AccountSpecs on (AccountSpecs.PersonID=persons.PersonID) 
								where plname like ? and pfname like ? ";
		
		if(isset($_REQUEST["FilterType"]))
		{
			if($_REQUEST["FilterType"]=="6")
				$query .= " and persons.person_type in (2, 3, 300) ";
			else if($_REQUEST["FilterType"]=="7")
				$query .= " and persons.person_type in (1, 200) ";
			else if($_REQUEST["FilterType"]=="8")
				$query .= " and staff.UnitCode='".$_SESSION["UserGroup"]."' ";
			else if($_REQUEST["FilterType"]=="9")
				$query .= " and persons.person_type in (2, 3, 200) and staff.UnitCode='".$_SESSION["UserGroup"]."' ";
			else if($_REQUEST["FilterType"]=="10")
				$query .= " and persons.person_type in (1, 200) and staff.UnitCode='".$_SESSION["UserGroup"]."' ";
		}
		$query .= " limit 0,200";
		
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array("%".$_REQUEST["LName"]."%", "%".$_REQUEST["FName"]."%"));

		$i = 0;
		while($arr_res=$res->fetch())
		{
			$i++;
			if($i>200)
				break;
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td><input type=checkbox name='ch_".$arr_res["PersonID"]."'></td>";
			
			echo "<td>".$arr_res["plname"]." ".$arr_res["pfname"]."</td>";

			echo "<td>&nbsp;".$arr_res["WebUserID"]."</td>";
			echo "</tr>";
		}
		
	}
	else 
	{
		if(isset($_REQUEST["ProjectID"]) && $_REQUEST["ProjectID"]!="0")
		  $query = "select persons.PersonID, pfname, plname, staff.person_type, WebUserID, ptitle from hrmstotal.persons 
								LEFT JOIN hrmstotal.staff using (PersonID) 
								LEFT JOIN hrmstotal.org_new_units on (org_new_units.ouid=staff.UnitCode)
								LEFT JOIN projectmanagement.AccountSpecs on (AccountSpecs.PersonID=persons.PersonID) 
								where persons.PersonID in (select PersonID from projectmanagement.ProjectMembers where ProjectID=?) order by plname";
		else
		  $query = "select persons.PersonID, pfname, plname, staff.person_type, WebUserID, ptitle from hrmstotal.persons 
								LEFT JOIN hrmstotal.staff using (PersonID) 
								LEFT JOIN hrmstotal.org_new_units on (org_new_units.ouid=staff.UnitCode)
								LEFT JOIN projectmanagement.AccountSpecs on (AccountSpecs.PersonID=persons.PersonID) 	order by plname";
		
		$mysql->Prepare($query);
		if(isset($_REQUEST["ProjectID"]) && $_REQUEST["ProjectID"]!="0")
		  $res = $mysql->ExecuteStatement(array($_REQUEST["ProjectID"]));
		else
		  $res = $mysql->ExecuteStatement(array());
		
		$i = 0;
		while($arr_res=$res->fetch())
		{
			$i++;
			if($i>200)
				break;
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td><input type=checkbox name='ch_".$arr_res["PersonID"]."'></td>";
			echo "<td>".$arr_res["plname"]." ".$arr_res["pfname"]."</td>";

			echo "<td>&nbsp;".$arr_res["WebUserID"]."</td>";
			echo "</tr>";
		}
		
	}
?>
<tr class='FooterOfTable'>
	<td colspan=3 align=center>
	<input type=submit value='انتخاب'>
	</td>
</tr>
</table>
</form>

<script>
	function SelectPerson(PersonID, PersonName, FName, LName)
	{
		window.opener.document.<?php echo $FormName ?>.<?php echo $_REQUEST["InputName"] ?>.value=PersonID;
		window.opener.document.getElementById('<?php echo $_REQUEST["SpanName"] ?>').innerHTML=PersonName;
		<?php if(isset($_REQUEST["FInput"])) { ?>
		window.opener.document.getElementById('<?php echo $_REQUEST["FInput"] ?>').value=FName;
		<?php } ?>
		<?php if(isset($_REQUEST["LInput"])) { ?>
		window.opener.document.getElementById('<?php echo $_REQUEST["LInput"] ?>').value=LName;
		<?php } ?>
		window.close();
	}
	function ClearLastSelected()
	{
		window.opener.document.<?php echo $FormName ?>.<?php echo $_REQUEST["InputName"] ?>.value='0';
		window.opener.document.getElementById('<?php echo $_REQUEST["SpanName"] ?>').innerHTML='';
		<?php if(isset($_REQUEST["FInput"])) { ?>
		window.opener.document.getElementById('<?php echo $_REQUEST["FInput"] ?>').value='';
		<?php } ?>
		<?php if(isset($_REQUEST["LInput"])) { ?>
		window.opener.document.getElementById('<?php echo $_REQUEST["LInput"] ?>').value='';
		<?php } ?>
		window.close();
	}
</script>
</body></html>
