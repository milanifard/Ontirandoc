z<?php

/*
صفحه  نمایش لیست و مدیریت داده ها مربوط به : برچسب کلاسها
برنامه نویس: امید میلانی فرد
تاریخ ایجاد: 94-3-1
 */

/*

edited by: Mohammad Kahani SID: 9512762447

 */

include "header.inc.php";
include "../sharedClasses/SharedClass.class.php";
include "classes/OntologyClassLabels.class.php";
include "classes/OntologyClasses.class.php";

HTMLBegin();
if (isset($_REQUEST["Save"])) {
	if (isset($_REQUEST["OntologyClassID"])) {
		$Item_OntologyClassID = $_REQUEST["OntologyClassID"];
	}

	if (isset($_REQUEST["Item_label"])) {
		$Item_label = $_REQUEST["Item_label"];
	}

	if (!isset($_REQUEST["UpdateID"])) {
		manage_OntologyClassLabels::Add(
			$Item_OntologyClassID,
			$Item_label
		);
	} else {
		manage_OntologyClassLabels::Update(
			$_REQUEST["UpdateID"],
			$Item_label
		);
	}
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$label = "";
if (isset($_REQUEST["UpdateID"])) {
	$obj = new be_OntologyClassLabels();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$label = htmlentities($obj->label, ENT_QUOTES, 'UTF-8');
}
?>
<form method="post" id="f1" name="f1">
	<?
	if (isset($_REQUEST["UpdateID"])) {
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
		echo manage_OntologyClasses::ShowSummary($_REQUEST["OntologyClassID"]);
		echo manage_OntologyClasses::ShowTabs($_REQUEST["OntologyClassID"], "ManageOntologyClassLabels");
	}
	
	?>
	<br>
	<div class="table-responsive container-fluid">
		<div class="row">
			<div class="col-1"></div>
			<div class="col-10">
				<table class="table table-sm table-borderless">
					<thead class="table-info">
						<tr>
							<th class="text-center">
								ایجاد/ویرایش برچسب کلاسها </th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<table>
									<tr>
										<td width="1%" nowrap>
											<label for="OntologyClassID">
												برچسب
											</label>
										</td>
										<td nowrap>
											<?
											if (!isset($_REQUEST["UpdateID"])) {
											?>
												<input class="form-control" type="text" name="OntologyClassID" id="OntologyClassID" maxlength="500" value='<? if (isset($_REQUEST["OntologyClassID"])) echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>'>
											<? } ?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
					<thead class="text-center table-info">
						<tr>
							<td>
								<button type="button" class="btn btn-success" onclick="javascript: ValidateForm();">ذخیره</button>
								<button type="button" class="btn btn-danger" onclick="javascript: window.close();">خروج</button>
							</td>
						</tr>
					</thead>
				</table>
			</div>
			<div class="col-1"></div>
		</div>
	</div>
	<input type="hidden" name="Save" id="Save" value="1">
</form>

<script>
	<? echo $LoadDataJavascriptCode; ?>

	function ValidateForm() {
		document.f1.submit();
	}
</script>
<?php
$res = manage_OntologyClassLabels::GetList($_REQUEST["OntologyClassID"]);
$SomeItemsRemoved = false;
for ($k = 0; $k < count($res); $k++) {
	if (isset($_REQUEST["ch_" . $res[$k]->OntologyClassLabelID])) {
		manage_OntologyClassLabels::Remove($res[$k]->OntologyClassLabelID);
		$SomeItemsRemoved = true;
	}
}
if ($SomeItemsRemoved) {
	$res = manage_OntologyClassLabels::GetList($_REQUEST["OntologyClassID"]);
}

?>

<form id="ListForm" name="ListForm" method="post">
	<input type="hidden" id="Item_OntologyCla7d45ccbea70e3559331f83a4adc1d4db08554337ssID" name="Item_OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
	<br>
	<div class="table-responsive container-fluid">
		<div class="row">
			<div class="col-1"></div>
			<div class="col-10">
				<table class="table table-bordered table-sm table-striped">
					<thead class="table-info">
						<tr>
							<th class="text-center" colspan="6">برچسب کلاسها</th>
						</tr>
						<tr>
							<td width="1%"> </td>
							<td width="1%">ردیف</td>
							<td width="2%">ویرایش</td>
							<td width="50%">برچسب</td>
						</tr>
					</thead>
					<?
					for ($k = 0; $k < count($res); $k++) {
						if ($k % 2 == 0)
							echo "<tr class=\"OddRow\">";
						else
							echo "<tr class=\"EvenRow\">";
						echo "<td>";
						echo "<input type=\"checkbox\" name=\"ch_" . $res[$k]->OntologyClassLabelID . "\">";
						echo "</td>";
						echo "<td>" . ($k + 1) . "</td>";
						echo "	<td><a href=\"ManageOntologyClassLabels.php?UpdateID=" . $res[$k]->OntologyClassLabelID . "&OntologyClassID=" . $_REQUEST["OntologyClassID"] . "\"><i class='fas fa-edit'></i></a></td>";
						echo "	<td>" . str_replace("\r", "<br>", htmlentities($res[$k]->label, ENT_QUOTES, 'UTF-8')) . "</td>";
						echo "</tr>";
					}
					?>
					<tr class="table-info">
						<td colspan="6" align="center">
							<input type="button" class="btn btn-danger" onclick="ConfirmDelete();" value="حذف">
						</td>
					</tr>
				</table>
			</div>
			<div class="col-1"></div>
		</div>
	</div>

</form>
<form target="_blank" method="post" action="NewOntologyClassLabels.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyClassID" name="OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
	function ConfirmDelete() {
		if (confirm('آیا مطمین هستید؟')) document.ListForm.submit();
	}
</script>

</html>