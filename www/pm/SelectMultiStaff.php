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
<div class="row">
<div class="col-3"></div>
<div class="col-6">
<table class="table table-bordered table-sm">
<tr>
	<td><? echo C_NAME.":"; ?></td><td><input type=text name=FName "></td>
</tr>
<tr>
	<td><? echo C_LAST_NAME.":"; ?></td><td><input type=text name=LName "></td>
</tr>
<tr class='table-info'>
	<td colspan=2 class="text-center">
	<input class="btn btn-light" type=submit value='<? echo C_SEARCH; ?>'>
	<input class="btn btn-danger" type=button value='<? echo C_REMOVE_PREVIOUS_CHOICE; ?>' onclick='javascript: ClearLastSelected();'>
	</td>
</tr>
</table>
</div>
<div class="col-3"></div>
</div>
</form>
<form method=post>
<? if(isset($_REQUEST["SpanName"])) { ?>
  <input type=hidden name=SpanName id=SpanName value='<? echo $_REQUEST["SpanName"] ?>'>
  <input type=hidden name=InputName id=InputName value='<? echo $_REQUEST["InputName"] ?>'>
<? } ?>
<input type=hidden name=Selected id=Selected value=1>
<br>
<div class="row">
<div class="col-3"></div>
<div class="col-6">
<table class="table table-bordered table-sm table-striped">
<thead class="table-info">
<tr>
	<td width=1%></td>
	<td><? echo C_FULL_NAME; ?></td>
	<td class="text-nowrap"><? echo C_USER_NAME; ?></td>
</tr>
</thead>
<?
	if(isset($_REQUEST["FName"]))
	{
		$query = "select persons.PersonID, pfname, plname, WebUserID from projectmanagement.persons 
								LEFT JOIN projectmanagement.AccountSpecs on (AccountSpecs.PersonID=persons.PersonID) 
								where plname like ? and pfname like ? ";
		
		$query .= " limit 0,200";
		
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array("%".$_REQUEST["LName"]."%", "%".$_REQUEST["FName"]."%"));

		$i = 0;
		while($arr_res=$res->fetch())
		{
			$i++;
			if($i>200)
				break;
			echo "<tr>";
			echo "<td><input type=checkbox name='ch_".$arr_res["PersonID"]."'></td>";
			
			echo "<td>".$arr_res["plname"]." ".$arr_res["pfname"]."</td>";

			echo "<td>&nbsp;".$arr_res["WebUserID"]."</td>";
			echo "</tr>";
		}
		
	}
	else 
	{
		if(isset($_REQUEST["ProjectID"]) && $_REQUEST["ProjectID"]!="0")
		  $query = "select persons.PersonID, pfname, plname, WebUserID from projectmanagement.persons 
								LEFT JOIN projectmanagement.AccountSpecs on (AccountSpecs.PersonID=persons.PersonID) 
								where persons.PersonID in (select PersonID from projectmanagement.ProjectMembers where ProjectID=?) order by plname";
		else
		  $query = "select persons.PersonID, pfname, plname,WebUserID from projectmanagement.persons 
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
			echo "<tr>";
			echo "<td><input type=checkbox name='ch_".$arr_res["PersonID"]."'></td>";
			echo "<td>".$arr_res["plname"]." ".$arr_res["pfname"]."</td>";

			echo "<td>&nbsp;".$arr_res["WebUserID"]."</td>";
			echo "</tr>";
		}
		
	}
?>
<tr class='table-info'>
	<td colspan=3 class="text-center">
	<input type=submit class="btn btn-light" value='<? echo C_SELECT; ?>'>
	</td>
</tr>
</table>
</div>
<div class="col-3"></div>
</div>
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
