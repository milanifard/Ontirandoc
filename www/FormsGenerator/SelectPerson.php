<?
	include("header.inc.php");
	HTMLBegin();
?>

<br>
<form method=post name="f1" id="f1">
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
<tr>
        <td>  نوع ایجاد کننده:</td>
                    <td>
                        <select name="CreatorType" id="CreatorType">
                            <option value="0">-
                            <option value="stu">دانشجو
                            <option value="other">غیر دانشجو
                        </select>
                    </td>
                </tr>
<tr class='FooterOfTable'>
    <td colspan=2 align=center><input type=button value='جستجو' onclick="javascript : valCheck();"></td>
</tr>
</table>
<br>
<table width=80% align=center border=1 cellspacing=0 cellpadding=3>

<?
	$mysql = dbclass::getInstance();
	if(isset($_REQUEST["FName"]))
	{
            if (isset ($_REQUEST["CreatorType"]) && $_REQUEST["CreatorType"] == "other"){
                echo "<tr class='HeaderOfTable'>
                        <td>نام و نام خانوادگی</td>
                        <td>نوع</td>
                        <td>واحد محل خدمت</td>
                </tr>";
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
			
			echo "<a href='javascript: SelectPerson(\"".$arr_res["PersonID"]."\", \"".$arr_res["pfname"]." ".$arr_res["plname"]."\", \"PERSONEL\" ";
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
            else if(isset ($_REQUEST["CreatorType"]) && $_REQUEST["CreatorType"] == "stu"){

                echo "<tr class='HeaderOfTable'>
                        <td>نام و نام خانوادگی</td>
                        <td>مقطع</td>
                        <td>دانشکده</td>
                </tr>";
                $query= "select  PersonID , p.PFName, p.PLName, PFldName, PEduSecName   from educ.persons p
                            left JOIN educ.StudentSpecs s using (PersonID)
                            left join educ.StudyFields e using (fldCode)
                            left join educ.EducationalSections using (EduSecCode)
				where PLName like '%".$_REQUEST["LName"]."%' and PFName like '%".$_REQUEST["FName"]."%' limit 0,200";
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


			echo "<a href='javascript: SelectPerson(\"".$arr_res["PersonID"]."\", \"".$arr_res["PFName"]." ".$arr_res["PLName"]."\", \"STUDENT\" ";
			echo "); '>";
			echo $arr_res["PFName"]." ".$arr_res["PLName"];
			echo "</td>";

			echo "<td>".$arr_res["PEduSecName"]."</td>";
                        echo "<td>".$arr_res["PFldName"]."</td>";
			echo "</tr>";
		}


            }
        }
?>
</table>
</form>
<script>

        function valCheck()
        {
            if(document.f1.CreatorType.value=='0')
                {
                    alert('انتخاب نوع فرد الزامی است');
                    return;
                }
               document.getElementById("f1").submit();
        }
            
	function SelectPerson(PersonID, PersonName,PersonType)
	{
		window.opener.document.f1.<?php echo $_REQUEST["InputName"] ?>.value=PersonID;
                window.opener.document.f1.<?php echo $_REQUEST["InputType"] ?>.value= PersonType;
		window.opener.document.getElementById('<?php echo $_REQUEST["SpanName"] ?>').innerHTML=PersonName;                
		
		window.close();
		
	}
</script>
</body></html>
