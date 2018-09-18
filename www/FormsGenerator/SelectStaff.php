<?
	include("header.inc.php");
	HTMLBegin();
?>
<script>
	<?= PersiateKeyboard() ?>
</script>
<br>
<form method=post>
<input type=hidden name='InputName' value='<?= $_REQUEST["InputName"] ?>'>
<input type=hidden name='SpanName' value='<?= $_REQUEST["SpanName"] ?>'>
<?php 
 if(isset($_REQUEST["UnitSpanName"]))
 {
 	?>
<input type=hidden name='UnitInputName' value='<?= $_REQUEST["UnitInputName"] ?>'>
<input type=hidden name='UnitSpanName' value='<?= $_REQUEST["UnitSpanName"] ?>'>
<input type=hidden name='SubUnitInputName' value='<?= $_REQUEST["SubUnitInputName"] ?>'>
<input type=hidden name='SubUnitSpanName' value='<?= $_REQUEST["SubUnitSpanName"] ?>'>
<input type=hidden name='EduGrpInputName' value='<?= $_REQUEST["EduGrpInputName"] ?>'>
<input type=hidden name='EduGrpSpanName' value='<?= $_REQUEST["EduGrpSpanName"] ?>'>
 	
 	<?php 	
 }
?>
<table width=80% align=center border=1 cellspacing=0 cellpadding=3>
<tr>
	<td>نام: </td><td><input type=text name=FName onkeypress="return submitenter(this, event);"></td>
</tr>
<tr>
	<td>نام خانوادگی: </td><td><input type=text name=LName onkeypress="return submitenter(this, event);"></td>
</tr>
<tr class='FooterOfTable'>
	<td colspan=2 align=center><input type=submit value='جستجو'></td>
</tr>
</table>
<br>
<table width=80% align=center border=1 cellspacing=0 cellpadding=3>
<tr class='HeaderOfTable'>
	<td>نام و نام خانوادگی</td>
	<td>نوع</td>
	<td>واحد محل خدمت</td>
</tr>
<?
	$mysql = dbclass::getInstance();
	if(isset($_REQUEST["FName"]))
	{
		$query = "select *, staff.person_type as sperson_type, org_units.ptitle as uptitle, org_sub_units.ptitle as suptitle from hrms_total.persons 
								LEFT JOIN hrms_total.staff on (staff.PersonID=persons.PersonID and staff.person_type=persons.person_type) 
								LEFT JOIN hrms_total.writs on (staff.last_writ_id=writs.writ_id and staff.last_writ_ver=writs.writ_ver and staff.staff_id=writs.staff_id)
								LEFT JOIN EducationalGroups on (EducationalGroups.EduGrpCode=staff.EduGrpCode)
								LEFT JOIN hrms_total.org_units on (org_units.ouid=writs.ouid)
								LEFT JOIN hrms_total.org_sub_units on (org_sub_units.sub_ouid=writs.sub_ouid)
								where plname like '%".$_REQUEST["LName"]."%' and pfname like '%".$_REQUEST["FName"]."%' limit 0,200";
		$res = $mysql->Execute($query);
		$i = 0;
		while($arr_res=$res->FetchRow())
		{
			$i++;
			if($i>200)
				break;
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td>";
			$ouid = $arr_res["UnitCode"];
			$sub_ouid = $ouid;
			//$UnitName = $arr_res["PName"];
			$UnitName = "";
			$SubUnitName = "&nbsp;";
			$EduGrpName = $arr_res["PEduName"];
			if($arr_res["writ_id"]!="")
			{
				$ouid = $arr_res["ouid"];
				$sub_ouid = $arr_res["sub_ouid"];
				$UnitName = $arr_res["uptitle"];
				$SubUnitName = $arr_res["suptitle"];
			}	
			
			echo "<a href='javascript: SelectPerson(\"".$arr_res["PersonID"]."\", \"".$arr_res["pfname"]." ".$arr_res["plname"]."\", ";
			echo "\"".$ouid."\", \"".$sub_ouid."\", \"".$arr_res["EduGrpCode"]."\", ";
			echo "\"".$UnitName."\", \"".$SubUnitName."\", \"".$EduGrpName."\" ";
			echo "); '>";
			echo $arr_res["pfname"]." ".$arr_res["plname"];
			echo "</td>";

			if($arr_res["sperson_type"]=="200")
				echo "<td>حق التدریس</td>";
			else if($arr_res["sperson_type"]=="1")
				echo "<td>استاد رسمی</td>";
			else if($arr_res["sperson_type"]=="2")
				echo "<td>کارمند رسمی</td>";
			else if($arr_res["sperson_type"]=="300")
				echo "<td>کارمند شرکتی</td>";
			else if($arr_res["sperson_type"]=="5" || $arr_res["sperson_type"]=="6" || $arr_res["sperson_type"]=="200")
				echo "<td>کارمند قراردادی</td>";
			else
				echo "<td>".$arr_res["sperson_type"]."</td>";
			echo "<td>".$UnitName."</td>";
			echo "</tr>";
		}
	}
?>
</table>
<script>
	function SelectPerson(PersonID, PersonName, ouid, sub_ouid, EduGrpCode, UnitName, SubUnitName, EduGrpName)
	{
		window.opener.document.f1.<?php echo $_REQUEST["InputName"] ?>.value=PersonID;
		window.opener.document.getElementById('<?php echo $_REQUEST["SpanName"] ?>').innerHTML=PersonName;
		<?php if(isset($_REQUEST["UnitSpanName"])) { ?>
		window.opener.document.f1.<?php echo $_REQUEST["UnitInputName"] ?>.value=ouid;
		window.opener.document.f1.<?php echo $_REQUEST["SubUnitInputName"] ?>.value=sub_ouid;
		window.opener.document.f1.<?php echo $_REQUEST["EduGrpInputName"] ?>.value=EduGrpCode;
		window.opener.document.getElementById('<?php echo $_REQUEST["UnitSpanName"] ?>').innerHTML=UnitName;
		window.opener.document.getElementById('<?php echo $_REQUEST["SubUnitSpanName"] ?>').innerHTML=SubUnitName;
		window.opener.document.getElementById('<?php echo $_REQUEST["EduGrpSpanName"] ?>').innerHTML=EduGrpName;
		<?php } ?>
		window.close();
		
	}
</script>
</body></html>
