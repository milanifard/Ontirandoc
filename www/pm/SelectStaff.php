<?
	include("header.inc.php");
	HTMLBegin();
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
<div class="col-1"></div>
<div class="col-10">
<table class="table table-bordered table-sm">
<tr>
	<td><? echo C_NAME.": "; ?></td><td><input type=text name=FName class="form-control col-lg-3""></td>
</tr>
<tr>
	<td><? echo C_LAST_NAME.": "; ?></td><td><input type=text name=LName class="form-control col-lg-3""></td>
</tr>
<tfoot class='table-info'>
	<td class="text-center" colspan=2>
	<input type=submit class="btn btn-light" value="<? echo C_SEARCH; ?>">
	<input type=button class="btn btn-danger" value="<? echo C_REMOVE_PREVIOUS_CHOICE; ?>" onclick='javascript: ClearLastSelected();'>
	</td>
</tfoot>
</table>
<br>
<table class="table table-bordered table-sm table-striped">
<thead class='table-info'>
	<td><? echo C_FULL_NAME; ?></td>
</thead>
<?
	$mysql = pdodb::getInstance();
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
			echo "<td><a href='javascript: SelectPerson(\"".$arr_res["PersonID"]."\", \"".$arr_res["pfname"]." ".$arr_res["plname"]."\", \"".$arr_res["pfname"]."\", \"".$arr_res["plname"]."\"); '>".$arr_res["pfname"]." ".$arr_res["plname"]."</td>";
			echo "</tr>";
		}
		
	}
	else if(isset($_REQUEST["ProjectID"]) && $_REQUEST["ProjectID"]!="0")
	{
		$query = "select persons.PersonID, pfname, plname, WebUserID from projectmanagement.persons 
								LEFT JOIN projectmanagement.AccountSpecs on (AccountSpecs.PersonID=persons.PersonID) 
								where persons.PersonID in (select PersonID from projectmanagement.ProjectMembers where ProjectID=?)";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($_REQUEST["ProjectID"]));
		$i = 0;
		while($arr_res=$res->fetch())
		{
			$i++;
			if($i>200)
				break;
			echo "<tr>";
			echo "<td><a href='javascript: SelectPerson(\"".$arr_res["PersonID"]."\", \"".$arr_res["pfname"]." ".$arr_res["plname"]."\", \"".$arr_res["pfname"]."\", \"".$arr_res["plname"]."\"); '>".$arr_res["pfname"]." ".$arr_res["plname"]."</td>";
			echo "</tr>";
		}
		
	}
?>
</table>
</div>
<div class="col-1"></div>
</div>

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
