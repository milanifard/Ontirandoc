<?php
include("header.inc.php");
HTMLBegin();
$mysql = dbclass::getInstance();
$query = "select f.*, ft.*, p.plname as pplname, p.pfname as ppfname, s.pfname as spfname, s.plname as splname,
								u.ptitle as UnitName, su.ptitle as SubUnitName, eg.PEduName   
			 from formsgenerator.FilesTemporarayAccessList 
			JOIN formsgenerator.files as f using (FileID) 
			LEFT JOIN formsgenerator.FileTypes as ft using (FileTypeID) 
			LEFT JOIN hrms_total.persons as p using (PersonID)
			LEFT JOIN formsgenerator.StudentSpecs as s using (StNo)
			LEFT JOIN hrms_total.org_units as u using (ouid)
			LEFT JOIN hrms_total.org_sub_units su using (sub_ouid)
			LEFT JOIN formsgenerator.EducationalGroups as eg on (f.EduGrpCode=eg.EduGrpCode)
			where ReceiverID='".$_SESSION["PersonID"]."'";
echo "<br><table width=90% align=center cellspacing=0 cellpadding=3 border=1>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%>محتویات</td>";
echo "<td width=1%>نوع</td>";
echo "<td width=1%>شماره</td>";
echo "<td width=1%>نوع فرد</td>";
echo "<td width=1%>شخص مربوطه</td>";
echo "<td>مکان</td>";
echo "<td>عنوان</td>";
echo "</tr>";
$res = $mysql->Execute($query);
$i = 0;
while($rec = $res->FetchRow())
{
	$i++;
	if($i%2==0)
		echo "<tr class=OddRow>";
	else
		echo "<tr class=EvenRow>";
	echo "<td nowrap><a href='NewTempFile.php?UpdateID=".$rec["FileID"]."'><img border=0 width=30 src='images/edit.jpeg'></a></td>";
	echo "<td nowrap>".$rec["FileTypeName"]."</td>";
	echo "<td nowrap>&nbsp;".$rec["FileNo"]."</td>";
	if($rec["RelatedToPerson"]=="YES")
	{
		echo "<td nowrap>&nbsp;";
		if($rec["PersonType"]=="STAFF")
			echo "کارمند";
		else if($rec["PersonType"]=="PROF")
			echo "هیات علمی";
		else if($rec["PersonType"]=="STUDENT")
			echo "دانشجو";
		else if($rec["PersonType"]=="OTHER")
			echo "سایر";
		echo "</td>";
		echo "<td nowrap>&nbsp;";
		if($rec["PersonType"]=="OTHER")
		{
			echo $rec["PFName"]." ".$rec["PLName"];
		}
		else if($rec["PersonType"]=="PROF" || $rec["PersonType"]=="STAFF")
		{
			echo $rec["ppfname"]." ".$rec["pplname"];
		}
		else if($rec["PersonType"]=="STUDENT")
		{
			echo $rec["spfname"]." ".$rec["splname"];
		}
		echo "</td>";
	}
	else
	{
		echo "<td colspan=2>&nbsp;</td>";
	}
	echo "<td nowrap>&nbsp;".$rec["UnitName"]." - ".$rec["SubUnitName"]." - ".$rec["PEduName"]."</td>";
	echo "<td nowrap>&nbsp;".$rec["FileTitle"]."</td>";
	echo "</tr>";
}
echo "</table>";
?>
</html>