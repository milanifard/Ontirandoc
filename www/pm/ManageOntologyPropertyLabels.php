<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : برچسبهای خصوصیات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-2
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/OntologyPropertyLabels.class.php");
include_once("classes/OntologyProperties.class.php");
HTMLBegin();
if (isset($_REQUEST["Save"])) {
	if (isset($_REQUEST["OntologyPropertyID"]))
		$Item_OntologyPropertyID = $_REQUEST["OntologyPropertyID"];
	if (isset($_REQUEST["Item_label"]))
		$Item_label = $_REQUEST["Item_label"];
	if (!isset($_REQUEST["UpdateID"])) {
		manage_OntologyPropertyLabels::Add(
			$Item_OntologyPropertyID,
			$Item_label
		);
	} else {
		manage_OntologyPropertyLabels::Update(
			$_REQUEST["UpdateID"],
			$Item_label
		);
	}
	echo SharedClass::CreateMessageBox(C_INFORMATION_SAVED);
}
$LoadDataJavascriptCode = '';
$comment = "";
if (isset($_REQUEST["UpdateID"])) {
	$obj = new be_OntologyPropertyLabels();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$comment = htmlentities($obj->label, ENT_QUOTES, 'UTF-8');;
}
?>
<form method="post" id="f1" name="f1">
	<?
	if (isset($_REQUEST["UpdateID"])) {
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
	}
	echo manage_OntologyProperties::ShowSummary($_REQUEST["OntologyPropertyID"]);
	echo manage_OntologyProperties::ShowTabs($_REQUEST["OntologyPropertyID"], "ManageOntologyPropertyLabels");
	?>
	<br>
	<div class="container">
		<div class="row" style="margin-bottom: 10px">
			<div class="col-12 text-center"><? echo C_CREATE_EDIT_LABELS ?></div>
		</div>
		<div class="row">
			<div class="col-12">
				<?
				if (!isset($_REQUEST["UpdateID"])) {
				?>
					<input type="hidden" name="OntologyPropertyID" id="OntologyPropertyID" value='<? if (isset($_REQUEST["OntologyPropertyID"])) echo htmlentities($_REQUEST["OntologyPropertyID"], ENT_QUOTES, 'UTF-8'); ?>'>
				<? } ?>
				<div class="row">
					<div class="col-1" nowrap>
						<? echo C_LABEL ?>
					</div>
					<div class="col-11" nowrap>
						<textarea name="Item_label" id="Item_label" class="form-control" cols="80" rows="5"><? echo $comment ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row justify-content-center" style="margin-top: 10px">
			<td align="center">
				<input type="button" class="btn btn-success mx-2" onclick="javascript: ValidateForm();" value="<? echo C_SAVE ?>">
				<input type="button" class="btn btn-primary mx-2" onclick="javascript: document.location='ManageOntologyPropertyLabels.php?OntologyPropertyID=<?php echo $_REQUEST["OntologyPropertyID"]; ?>'" value="<? echo C_NEW ?>">
				<input type="button" class="btn btn-danger mx-2" onclick="javascript: window.close();" value="<? echo C_CLOSE ?>">
			</td>
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
$res = manage_OntologyPropertyLabels::GetList($_REQUEST["OntologyPropertyID"]);
$SomeItemsRemoved = false;
for ($k = 0; $k < count($res); $k++) {
	if (isset($_REQUEST["ch_" . $res[$k]->OntologyPropertyLabelID])) {
		manage_OntologyPropertyLabels::Remove($res[$k]->OntologyPropertyLabelID);
		$SomeItemsRemoved = true;
	}
}
if ($SomeItemsRemoved)
	$res = manage_OntologyPropertyLabels::GetList($_REQUEST["OntologyPropertyID"]);
?>
<form id="ListForm" name="ListForm" method="post">
	<input type="hidden" id="Item_OntologyPropertyID" name="Item_OntologyPropertyID" value="<? echo htmlentities($_REQUEST["OntologyPropertyID"], ENT_QUOTES, 'UTF-8'); ?>">
	<br>
	<div class="container">
		<table class="table table-striped table-bordered table-hover">
			<thead class="thead-dark">
				<tr bgcolor="#cccccc">
					<td colspan="4">
						<? echo C_LABELS ?>
					</td>
				</tr>
				<tr>
					<th width="1%"> </th>
					<th width="1%"><? echo C_ROW ?></th>
					<th width="2%"><? echo C_EDIT ?></th>
					<th><? echo C_LABEL ?></th>
				</tr>
			</thead>
			<tbody>
				<?
				for ($k = 0; $k < count($res); $k++) {
					echo "<tr>";
					echo "<td>";
					echo "<input type=\"checkbox\" class=\"form-check-input position-static\" name=\"ch_" . $res[$k]->OntologyPropertyLabelID . "\">";
					echo "</td>";
					echo "<td>" . ($k + 1) . "</td>";
					echo "	<td><a href=\"ManageOntologyPropertyLabels.php?UpdateID=" . $res[$k]->OntologyPropertyLabelID . "&OntologyPropertyID=" . $_REQUEST["OntologyPropertyID"] . "\"><i class='fa fa-edit'></i></a></td>";
					echo "	<td>" . str_replace("\r", "<br>", htmlentities($res[$k]->label, ENT_QUOTES, 'UTF-8')) . "</td>";
					echo "</tr>";
				}
				?>
			</tbody>
			<tfoot>
				<tr class="FooterOfTable">
					<td colspan="4" align="center">
						<input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<? echo C_DELETE ?>">
					</td>
				</tr>
			</tfoot>

		</table>
	</div>
</form>
<form target="_blank" method="post" action="NewOntologyPropertyLabels.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyPropertyID" name="OntologyPropertyID" value="<? echo htmlentities($_REQUEST["OntologyPropertyID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
	function ConfirmDelete() {
		if (confirm('<? echo C_T_AREUSURE ?>')) document.ListForm.submit();
	}
</script>

</html>