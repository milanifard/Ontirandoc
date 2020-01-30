<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : قرارداد پرسنل
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-12-26
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/PersonAgreements.class.php");
include ("classes/persons.class.php");
HTMLBegin();
$PersonName = "";
$pobj = new be_persons();
$pobj->LoadDataFromDatabase($_REQUEST["PersonID"]);
$PersonName = $pobj->pfname." ".$pobj->plname;
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["PersonID"]))
		$Item_PersonID=$_REQUEST["PersonID"];
	if(isset($_REQUEST["FromDate_DAY"]))
	{
		$Item_FromDate = SharedClass::ConvertToMiladi($_REQUEST["FromDate_YEAR"], $_REQUEST["FromDate_MONTH"], $_REQUEST["FromDate_DAY"]);
	}
	if(isset($_REQUEST["ToDate_DAY"]))
	{
		$Item_ToDate = SharedClass::ConvertToMiladi($_REQUEST["ToDate_YEAR"], $_REQUEST["ToDate_MONTH"], $_REQUEST["ToDate_DAY"]);
	}
	if(isset($_REQUEST["Item_AgreementDescription"]))
		$Item_AgreementDescription=$_REQUEST["Item_AgreementDescription"];
	if(isset($_REQUEST["Item_HourlyPrice"]))
		$Item_HourlyPrice=$_REQUEST["Item_HourlyPrice"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_PersonAgreements::Add($Item_PersonID
				, $Item_FromDate
				, $Item_ToDate
				, $Item_AgreementDescription
				, $Item_HourlyPrice
				);
	}	
	else 
	{	
		manage_PersonAgreements::Update($_REQUEST["UpdateID"] 
				, $Item_FromDate
				, $Item_ToDate
				, $Item_AgreementDescription
				, $Item_HourlyPrice
				);
	}	
	echo SharedClass::CreateMessageBox(C_INFORMATION_SAVED);
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_PersonAgreements();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	if($obj->FromDate_Shamsi!="date-error") 
	{
		$LoadDataJavascriptCode .= "document.f1.FromDate_YEAR.value='".substr($obj->FromDate_Shamsi, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.FromDate_MONTH.value='".substr($obj->FromDate_Shamsi, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.FromDate_DAY.value='".substr($obj->FromDate_Shamsi, 8, 2)."'; \r\n "; 
	}
	if($obj->ToDate_Shamsi!="date-error") 
	{
		$LoadDataJavascriptCode .= "document.f1.ToDate_YEAR.value='".substr($obj->ToDate_Shamsi, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.ToDate_MONTH.value='".substr($obj->ToDate_Shamsi, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.ToDate_DAY.value='".substr($obj->ToDate_Shamsi, 8, 2)."'; \r\n "; 
	}
	$LoadDataJavascriptCode .= "document.f1.Item_AgreementDescription.value='".htmlentities($obj->AgreementDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_HourlyPrice.value='".htmlentities($obj->HourlyPrice, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
//echo manage_persons::ShowSummary($_REQUEST["PersonID"]);
//echo manage_persons::ShowTabs($_REQUEST["PersonID"], "ManagePersonAgreements");

?>
<br>
<div class="row">
    <div class="col-1"></div>
    <div class="col-10">
		<table class="table table-sm table-borderless">
			<thead>
                <tr class="table-info">
					<td class="text-center"><? echo C_CREATE."/".C_EDIT." ".C_PERSONEL_CONTRACT ?></td>
                </tr>
            </thead>
			<tr><td><table>
				<? 
				if(!isset($_REQUEST["UpdateID"]))
				{
				?> 
				<input type="hidden" name="PersonID" id="PersonID" value='<? if(isset($_REQUEST["PersonID"])) echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>'>
				<? } ?>
				<tr>
					<td width="1%" nowrap><? echo C_FROM_DATE; ?></td>
					<td nowrap>
						<input maxlength="2" id="FromDate_DAY"  name="FromDate_DAY" type="text" size="2" required>/
						<input maxlength="2" id="FromDate_MONTH" name="FromDate_MONTH" type="text" size="2" required>/
						<input maxlength="2" id="FromDate_YEAR" name="FromDate_YEAR" type="text" size="2" required>
					</td>
				</tr>
				<tr>
					<td width="1%" nowrap><? echo C_TO_DATE; ?></td>
					<td nowrap>
						<input maxlength="2" id="ToDate_DAY"  name="ToDate_DAY" type="text" size="2" required>/
						<input maxlength="2" id="ToDate_MONTH" name="ToDate_MONTH" type="text" size="2" required>/
						<input maxlength="2" id="ToDate_YEAR" name="ToDate_YEAR" type="text" size="2" required>
					</td>
				</tr>
				<tr>
					<td width="1%" nowrap><? echo C_CONTRACT_DESCRIPTION; ?></td>
					<td nowrap>
						<textarea name="Item_AgreementDescription" id="Item_AgreementDescription" cols="80" rows="5"></textarea>
					</td>
				</tr>
				<tr>
					<td width="1%" nowrap><? echo C_HOURLY_PRICE; ?></td>
					<td nowrap>
						<input type="text" name="Item_HourlyPrice" id="Item_HourlyPrice" maxlength="10" size="10" required> <? echo C_RIAL; ?>
					</td>
				</tr>
			</table></td></tr>
			<tfoot>
                <tr class="table-info">
					<td align="center">
						<input type="submit" class="btn btn-success" value="<? echo C_SAVE; ?>">
						<input type="button" class="btn btn-info" onclick="javascript: document.location='ManagePersonAgreements.php?PersonID=<?php echo $_REQUEST["PersonID"]; ?>'" value="<? echo C_NEW; ?>">
						<input type="button" class="btn btn-warning" onclick="javascript: document.location='Managepersons.php';" value="<? echo C_RETURN; ?>">
					</td>
                </tr>
            </tfoot>
		</table>
	</div>
</div>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
</script>
<?php 
$res = manage_PersonAgreements::GetList($_REQUEST["PersonID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->PersonAgreementID])) 
	{
		manage_PersonAgreements::Remove($res[$k]->PersonAgreementID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_PersonAgreements::GetList($_REQUEST["PersonID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_PersonID" name="Item_PersonID" value="<? echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>">
<br>
	<div class="row">
        <div class="col-1"></div>
        <div class="col-10">
			<table class="table table-bordered table-sm table-striped">
				<tr><td colspan="6">
					<? echo C_CONTRACTS_OF; ?> <b><? echo $PersonName; ?> </b>
				</td></tr>
				<thead class="table-info">
					<tr>
						<td width="1%"> </td>
						<td width="1%"><? echo C_ROW; ?></td>
						<td width="2%"><? echo C_EDIT; ?></td>
						<td><? echo C_FROM_DATE; ?></td>
						<td><? echo C_TO_DATE; ?></td>
						<td><? echo C_HOURLY_PRICE; ?></td>
					</tr>
				</thead>
				<?
				for($k=0; $k<count($res); $k++)
				{
					echo "<tr><td>";
					echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->PersonAgreementID."\">";
					echo "</td>";
					echo "<td>".($k+1)."</td>";
					echo "	<td><a href=\"ManagePersonAgreements.php?UpdateID=".$res[$k]->PersonAgreementID."&PersonID=".$_REQUEST["PersonID"]."\"><i class='fas fa-edit'></i></a></td>";
					echo "	<td>".$res[$k]->FromDate_Shamsi."</td>";
					echo "	<td>".$res[$k]->ToDate_Shamsi."</td>";
					echo "	<td>".htmlentities($res[$k]->HourlyPrice, ENT_QUOTES, 'UTF-8')."</td>";
					echo "</tr>";
				}
				?>
				<tfoot>
					<tr class="table-info">
						<td colspan="6" align="center">
							<input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<? echo C_REMOVE; ?>">
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</form>
<form target="_blank" method="post" action="NewPersonAgreements.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="PersonID" name="PersonID" value="<? echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('<? echo C_ARE_YOU_SURE ?>')) document.ListForm.submit();
}
</script>
</html>
