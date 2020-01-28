<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
HTMLBegin();

function CreateFormIntoUserkartabl($SelectedPersonID, $FormsStructID, $FormFlowStepID, $TableName, $KeyName)
{
	$mysql = dbclass::getInstance();
	$sw = true;
	// کنترل می کند فرم قبلا ایجاد نشده باشد
	// فرمهایی که به این روش ایجاد می شوند حتما باید یک فیلد با نام PersonID 
	//داشته باشند
	// در صورتی این کنترل انجام می شود که فرم فیلدی برای PersonID هم داشته باشد
	if(isset($_REQUEST["HasPersonIDField"]))
	{
		$query = "select * from ".$TableName." where PersonID='".$SelectedPersonID."' ";
		$res2 = $mysql->Execute($query);
		if($res2->FetchRow())
		{
			echo "<font color=green>".$SelectedPersonID."</font><br>";
			$sw = false;
		}
	}
	if($sw==true)
	{
		echo $SelectedPersonID."<br>";

		$query = "select max(".$KeyName.")+1 from ".$TableName." ";
		$res = $mysql->Execute($query);
		$rec = $res->FetchRow();
		$CurID = $rec[0];	
		if($CurID=="")
			$CurID = 1;
		if(isset($_REQUEST["HasPersonIDField"]))
			$query = "insert into ".$TableName." (".$KeyName.", PersonID) values ('".$CurID."', '".$SelectedPersonID."')";
		else
			$query = "insert into ".$TableName." (".$KeyName.") values ('".$CurID."')";
		$mysql->Execute($query);
	
		$mysql->Execute("insert into FormsRecords (FormFlowStepID, RelatedRecordID, SendDate, SenderID, CreatorID, FormsStructID) values ('".$FormFlowStepID."', '".$CurID."', now(), '".$SelectedPersonID."', '".$SelectedPersonID."', '".$FormsStructID."')");
	}
}
/*
$mysql = pdodb::getInstance();
$query = "select p.PersonID, DarsadJanbazi, w.onduty_year as syear, w.onduty_month as smonth, w.onduty_day as sday, year as xyear, month as xmonth, day as xday FROM hrms.Temp_PDevotion tp 
					inner join hrms_total.persons p on tp.personid = p.personid
					left join hrms_total.staff s on p.personid = s.personid and p.person_type = s.person_type
					left join hrms_total.writs w on s.last_writ_id = w.writ_id and s.last_writ_ver = w.writ_ver and s.staff_id = w.staff_id";
$mysql->Prepare($query, PDO::FETCH_ASSOC, true);
$res = $mysql->ExecuteStatement(array(), PDO::FETCH_ASSOC, true);
while($rec = $res->fetch())
{
	$query = "update formsgenerator.VA30IsargaranExtraInfo set syear='".$rec["syear"]."', smonth='".$rec["smonth"]."', sday='".$rec["sday"]."', xyear='".$rec["xyear"]."', xmonth='".$rec["xmonth"]."', xday='".$rec["xday"]."' where PersonID='".$rec["PersonID"]."'";
	echo $query;
	$mysql->Execute($query); 
}
die();
*/
$mysql = dbclass::getInstance();
if(isset($_REQUEST["StepID"]))
{
	
	$res = $mysql->Execute("select * from FormsFlowSteps JOIN FormsStruct using (FormsStructID) where FormsFlowStepID='".$_REQUEST["StepID"]."'");
	$rec = $res->FetchRow();
	if($_REQUEST["ReceiverType"]=="Person")
		CreateFormIntoUserkartabl($_REQUEST["SelectedPersonID"], $rec["FormsStructID"], $rec["FormsFlowStepID"], $rec["RelatedDB"].".".$rec["RelatedTable"], $rec["KeyFieldName"]);
	else if($_REQUEST["ReceiverType"]=="AllRasmiProfs")
	{
		$res2 = $mysql->Execute("select persons.PersonID from hrms_total.persons JOIN hrms_total.staff using (PersonID, person_type) where staff.person_type=1");
		while($rec2 = $res2->FetchRow())
		{
			CreateFormIntoUserkartabl($rec2["PersonID"], $rec["FormsStructID"], $rec["FormsFlowStepID"], $rec["RelatedDB"].".".$rec["RelatedTable"], $rec["KeyFieldName"]);
		}
	}
	else if($_REQUEST["ReceiverType"]=="AllProfs")
	{
		$res2 = $mysql->Execute("select persons.PersonID from hrms_total.persons JOIN hrms_total.staff using (PersonID, person_type) where staff.person_type=1 or staff.person_type=200");
		while($rec2 = $res2->FetchRow())
		{
			CreateFormIntoUserkartabl($rec2["PersonID"], $rec["FormsStructID"], $rec["FormsFlowStepID"], $rec["RelatedDB"].".".$rec["RelatedTable"], $rec["KeyFieldName"]);
		}
	}
	else if($_REQUEST["ReceiverType"]=="AllRasmiStaffs")
	{
		$res2 = $mysql->Execute("select persons.PersonID from hrms_total.persons JOIN hrms_total.staff using (PersonID, person_type) where staff.person_type=2");
		while($rec2 = $res2->FetchRow())
		{
			CreateFormIntoUserkartabl($rec2["PersonID"], $rec["FormsStructID"], $rec["FormsFlowStepID"], $rec["RelatedDB"].".".$rec["RelatedTable"], $rec["KeyFieldName"]);
		}
	}
	else if($_REQUEST["ReceiverType"]=="AllGhararStaffs")
	{
		$res2 = $mysql->Execute("select persons.PersonID from hrms_total.persons JOIN hrms_total.staff using (PersonID, person_type) where staff.person_type=5 or staff.person_type=6");
		while($rec2 = $res2->FetchRow())
		{
			CreateFormIntoUserkartabl($rec2["PersonID"], $rec["FormsStructID"], $rec["FormsFlowStepID"], $rec["RelatedDB"].".".$rec["RelatedTable"], $rec["KeyFieldName"]);
		}
	}
	else if($_REQUEST["ReceiverType"]=="AllTempStaffs")
	{
		$res2 = $mysql->Execute("select persons.PersonID from hrms_total.persons JOIN hrms_total.staff using (PersonID, person_type) where staff.person_type=300");
		while($rec2 = $res2->FetchRow())
		{
			CreateFormIntoUserkartabl($rec2["PersonID"], $rec["FormsStructID"], $rec["FormsFlowStepID"], $rec["RelatedDB"].".".$rec["RelatedTable"], $rec["KeyFieldName"]);
		}
	}
	else if($_REQUEST["ReceiverType"]=="AllBimeStaffs")
	{
		$res2 = $mysql->Execute("select persons.PersonID from hrms_total.persons JOIN hrms_total.staff using (PersonID, person_type) where staff.person_type=3");
		while($rec2 = $res2->FetchRow())
		{
			CreateFormIntoUserkartabl($rec2["PersonID"], $rec["FormsStructID"], $rec["FormsFlowStepID"], $rec["RelatedDB"].".".$rec["RelatedTable"], $rec["KeyFieldName"]);
		}
	}
	else if($_REQUEST["ReceiverType"]=="AllIsargaran")
	{
		$res2 = $mysql->Execute("select distinct personid FROM hrms.Temp_PDevotion");
		while($rec2 = $res2->FetchRow())
		{
			CreateFormIntoUserkartabl($rec2["personid"], $rec["FormsStructID"], $rec["FormsFlowStepID"], $rec["RelatedDB"].".".$rec["RelatedTable"], $rec["KeyFieldName"]);
		}
	}
	
	else if($_REQUEST["ReceiverType"]=="GroupPerson")
	{
		//echo "select persons.PersonID from hrms_total.persons JOIN pas.PersonSettings using (PersonID) where WorkUnitCode='".$_REQUEST["WorkUnitCode"]."'";
		$res2 = $mysql->Execute("select persons.PersonID from hrms_total.persons JOIN pas.PersonSettings using (PersonID) where WorkUnitCode='".$_REQUEST["WorkUnitCode"]."'");
		while($rec2 = $res2->FetchRow())
		{
			CreateFormIntoUserkartabl($rec2["PersonID"], $rec["FormsStructID"], $rec["FormsFlowStepID"], $rec["RelatedDB"].".".$rec["RelatedTable"], $rec["KeyFieldName"]);
		}
	}
}
?>
<br>
<form method=post id=f1 name=f1>
<table width=80% align=center border=1 cellspacing=0 cellpadding=5>
	<tr class=HeaderOfTable>
		<td align=center>
		تولید فرم خام در کارتابل افراد
		</td>
	</tr>
	<tr>
		<td>
			<table width=100% border=0>
			<tr>
			<td width=10% nowrap>
				ساختار فرم مربوطه: 
			</td>
			<td>
				<select name=StepID>
					<?php
						$res = $mysql->Execute("select * from FormsFlowSteps JOIN FormsStruct using (FormsStructID) order by FormTitle, StepTitle");
						while($rec = $res->FetchRow())
						{
							echo "<option value='".$rec["FormsFlowStepID"]."' ";
							if(isset($_REQUEST["StepID"]) && $_REQUEST["StepID"]==$rec["FormsFlowStepID"])
								echo " selected ";
							echo ">".$rec["FormTitle"]." -> ".$rec["StepTitle"]; 
						}
					 ?>
				</select>
			</td>
			</tr>
			<tr>
				<td colspan=2>
				<select name=ReceiverType id=ReceiverType>
					<option value='Person'>برای یک شخص خاص فرم خام ارسال شود
					<option value='GroupPerson'>برای افرادی که در یک محل خاص کار می کنند ارسال شود
					<option value='AllRasmiProfs'>برای تمام اساتید رسمی فرم خام ارسال شود
					<option value='AllProfs'>برای تمام اساتید فرم خام ارسال شود
					<option value='AllRasmiStaffs'>برای تمام کارکنان رسمی/پیمانی فرم خام ارسال شود
					<option value='AllTempStaffs'>برای تمام کارکنان روزمزد بیمه ای فرم خام ارسال شود
					<option value='AllGhararStaffs'>برای تمام کارکنان قرارداد یکساله/معین فرم خام ارسال شود
					<option value='AllTempStaffs'>برای تمام کارکنان پیمانکاری فرم خام ارسال شود
					<option value='AllIsargaran'>برای تمام ایثارگران فرم خام ارسال شود
				</select>
				</td>
			</tr>
			<tr>
				<td>
				نام شخص مربوطه
				</td>
				<td>
				<input name=SelectedPersonID id=SelectedPersonID type=hidden>
				<span id=MySpan name=MySpan></span>&nbsp; 
				<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=SelectedPersonID&SpanName=MySpan")'>[انتخاب]</a>
				</td>
			</tr>
			<tr>
				<td>
				محل کار
				</td>
				<td>
				<select name=WorkUnitCode id=WorkUnitCode>
					<?php 
						$res = $mysql->Execute("select * from Units order by PName");
						while($rec = $res->FetchRow())
						{
							echo "<option value='".$rec["id"]."'>";
							echo $rec["PName"];							
						}
					?>
				</select>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
		<input type=checkbox name=HasPersonIDField id=HasPersonIDField>جدول مربوطه فیلدی هم برای PersonID دارد که باید با کد شخصی کاربر مربوطه پر شود
		</td>
	</tr>
	<tr class=FooterOfTable>
		<td align=center>
			<input type=submit value='ارسال'>
		</td>
	</tr>
</table>
</form>
</html>
	
