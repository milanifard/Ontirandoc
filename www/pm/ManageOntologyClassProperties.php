<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : خصوصیات هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/OntologyProperties.class.php");
include_once("classes/OntologyPropertyLabels.class.php");
include_once("classes/ontologies.class.php");
include_once("classes/OntologyClasses.class.php");
HTMLBegin();
$obj = new be_OntologyClasses();
$obj->LoadDataFromDatabase($_REQUEST["OntologyClassID"]);

$mysql = pdodb::getInstance();
if (isset($_REQUEST["Save"])) {
	$SelectedPropType = $_REQUEST["Item_PropertyType"];
	$OntologyPropertyID = $_REQUEST["OntologyPropertyID"];
	if ($SelectedPropType == "DATATYPE") {
		manage_OntologyProperties::AddToClass($obj->ClassTitle, $OntologyPropertyID, "domain");
	} else if ($SelectedPropType == "OBJECT") {
		manage_OntologyProperties::AddToClass($_REQUEST["DomainClass"], $OntologyPropertyID, "domain");
		manage_OntologyProperties::AddToClass($_REQUEST["RangeClass"], $OntologyPropertyID, "range");

		$DomainClassIDLabel = manage_OntologyClasses::GetClassIDAndLabel($_REQUEST["OntologyID"], $_REQUEST["DomainClass"]);
		$DomainClassID = $DomainClassIDLabel["OntologyClassID"];

		$RangeClassIDLabel = manage_OntologyClasses::GetClassIDAndLabel($_REQUEST["OntologyID"], $_REQUEST["RangeClass"]);
		$RangeClassID = $RangeClassIDLabel["OntologyClassID"];

		$mysql->Prepare("delete from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID=? and DomainClassID='" . $DomainClassID . "' and RangeClassID='" . $RangeClassID . "'");
		$mysql->ExecuteStatement(array($OntologyPropertyID));

		$mysql->Prepare("insert into projectmanagement.OntologyObjectPropertyRestriction (OntologyPropertyID, DomainClassID, RangeClassID, RelationStatus) values (?, '" . $DomainClassID . "', '" . $RangeClassID . "', 'VALID')");
		$mysql->ExecuteStatement(array($OntologyPropertyID));
	}
	echo SharedClass::CreateMessageBox(C_DATA_STORED);
}
$SelectedPropType = "DATATYPE";
if (isset($_REQUEST["Item_PropertyType"])) {
	$SelectedPropType = $_REQUEST["Item_PropertyType"];
}
$range = $domain = '';
?>
<form method="post" id="f1" name="f1">
	<?
	echo manage_OntologyClasses::ShowSummary($_REQUEST["OntologyClassID"]);
	?>
	<br>
	<div class="row">
		<div class="col-1"></div>
		<div class="col-10">
		<table width="90%" border="1" cellspacing="0" align="center">
		<tr class="table-info">
			<td align="center"><? echo C_ADD_FEATURE_TO_ONTOLOGY;?></td>
		</tr>
		<tr>
			<td>
				<table class="table table-sm table-borderless" border="0">
					<input type="hidden" name="OntologyID" id="OntologyID" value='<? echo $_REQUEST["OntologyID"]; ?>'>
					<input type="hidden" name="OntologyClassID" id="OntologyClassID" value='<? echo $_REQUEST["OntologyClassID"]; ?>'>
					<tr>
						<td nowrap>
							<div class="form-group">
								<label for="Item_PropertyType"><?php echo C_FEATURE_TYPE?></label>
							<select class="form-control" name="Item_PropertyType" id="Item_PropertyType" onchange='javascript: document.location="ManageOntologyClassProperties.php?OntologyID=<? echo $_REQUEST["OntologyID"] ?>&OntologyClassID=<? echo $_REQUEST["OntologyClassID"] ?>&Item_PropertyType="+this.value'>
								<option value='DATATYPE'><?php echo C_DATATYPE_FEATURE?></option>
								<option value='OBJECT' <? if ($SelectedPropType == "OBJECT") echo "selected"; ?>><? echo C_OBJECT_RELATION;?>)</option>
							</select>
							</div>
							<br>
							<? if ($SelectedPropType == "OBJECT") {
								$ClassList = manage_OntologyClasses::GetList($_REQUEST["OntologyID"]);
								echo C_FIRST_PART_RELATION;
								echo "<select name=DomainClass id=DomainClass>";
								for ($i = 0; $i < count($ClassList); $i++) {
									echo "<option value='" . $ClassList[$i]->ClassTitle . "' ";
									if ($obj->OntologyClassID == $ClassList[$i]->OntologyClassID)
										echo " selected ";
									echo ">" . $ClassList[$i]->label . " (" . $ClassList[$i]->ClassTitle . ")";
								}
								echo "</select><br>" . C_RELATION;
							}
							?>
							<select name=OntologyPropertyID id=OntologyPropertyID>
								<?
								$PropList = manage_OntologyProperties::GetList($_REQUEST["OntologyID"]);
								for ($i = 0; $i < count($PropList); $i++) {
									if ($PropList[$i]->PropertyType == $SelectedPropType) {
										echo "<option value='" . $PropList[$i]->OntologyPropertyID . "'>" . $PropList[$i]->label . " (" . $PropList[$i]->PropertyTitle . ")";
									}
								}
								?>
							</select>
							<? if ($SelectedPropType == "OBJECT") {
								echo "<br>";
								echo C_SECOND_PART_RELATION;
								echo "<select name=RangeClass id=RangeClass>";
								for ($i = 0; $i < count($ClassList); $i++) {
									echo "<option value='" . $ClassList[$i]->ClassTitle . "' ";
									if ($obj->OntologyClassID == $ClassList[$i]->OntologyClassID)
										echo " selected ";
									echo ">" . $ClassList[$i]->label . " (" . $ClassList[$i]->ClassTitle . ")";
								}
								echo "</select>";
							}
							?>


						</td>
					</tr>

			</td>
		</tr>
	</table>
		</div>
		<div class="col-1"></div>
	</div>
	</td>
	</tr>
	<tr class="FooterOfTable">
		<td align="center">
			<input class="btn btn-primary" type="button" onclick="javascript: ValidateForm();" value="<? echo C_NEW;?>">
			<input class="btn btn-danger" type="button" onclick="javascript: window.close();" value="<? echo C_CLOSE;?>">
		</td>
	</tr>
	</table>
	<input type="hidden" name="Save" id="Save" value="1">
</form>
<script>
	function ValidateForm() {
		document.f1.submit();
	}
</script>
<form id=ListForm name=ListForm method=post>
	<input type="hidden" name="OntologyID" id="OntologyID" value='<? echo $_REQUEST["OntologyID"]; ?>'>
	<input type="hidden" name="OntologyClassID" id="OntologyClassID" value='<? echo $_REQUEST["OntologyClassID"]; ?>'>
	<table class="table table-sm table-borderless" align=center border=1 cellspacing=0 cellpadding=5>
		<tr class="table-info">
			<td width=1%>&nbsp;</td>
			<td><? echo C_FEATURE;?></td>
		</tr>
		<?
		$plist = manage_OntologyClasses::GetClassRelatedProperties($obj->ClassTitle, $obj->OntologyID);
		for ($m = 0; $m < count($plist); $m++) {
			if ($plist[$m]["PropertyType"] == "DATATYPE") {
				if (isset($_REQUEST["ch_" . $plist[$m]["PropertyID"]]))
					manage_OntologyProperties::RemoveFromClass($obj->ClassTitle, $plist[$m]["PropertyID"]);
				else {
					echo "<tr>";
					echo "<td>";
					echo "<input type=checkbox name=ch_" . $plist[$m]["PropertyID"] . " id=ch_" . $plist[$m]["PropertyID"] . ">";
					echo "</td>";
					echo "<td >";
					echo "<a href='ManageOntologyProperties.php?DoNotShowList=1&UpdateID=" . $plist[$m]["PropertyID"] . "&OntologyID=" . $obj->OntologyID . "' target=_blank>";
					echo "<b>" . $plist[$m]["PropertyLabel"] . "</b>";
					echo "</a>";
					$query = "select distinct PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID='" . $plist[$m]["PropertyID"] . "'";
					$res = $mysql->Execute($query);
					$j = 0;
					while ($rec = $res->fetch()) {
						if ($j == 0)
							echo " (";
						else
							echo " - ";
						echo $rec["PermittedValue"];
						$j++;
					}
					if ($j > 0)
						echo ")";
					echo "</td></tr>";
				}
			}
		}
		$mysql->Prepare("select OntologyObjectPropertyRestriction.*, 
			  (select group_concat(label) from projectmanagement.OntologyClassLabels 
			  where OntologyClassID=DomainClassID) as DomainClassLabel,
			  (select group_concat(label) from projectmanagement.OntologyClassLabels 
			  where OntologyClassID=RangeClassID) as RangeClassLabel,
			  (select group_concat(label) from projectmanagement.OntologyPropertyLabels 
			  where OntologyPropertyLabels.OntologyPropertyID=OntologyObjectPropertyRestriction.OntologyPropertyID) as PropertyLabel
			  from projectmanagement.OntologyObjectPropertyRestriction
			  JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
			  where (DomainClassID=? or RangeClassID=?) and RelationStatus='VALID'");
		$res = $mysql->ExecuteStatement(array($_REQUEST["OntologyClassID"], $_REQUEST["OntologyClassID"]));
		while ($rec = $res->fetch()) {
			if (isset($_REQUEST["och_" . $rec["OntologyObjectPropertyRestrictionID"]])) {
				$mysql->Prepare("update projectmanagement.OntologyObjectPropertyRestriction set RelationStatus='INVALID' where OntologyObjectPropertyRestrictionID=?");
				$mysql->ExecuteStatement(array($rec["OntologyObjectPropertyRestrictionID"]));
			} else {
				echo "<tr>";
				echo "<td>";
				echo "<input type=checkbox name=och_" . $rec["OntologyObjectPropertyRestrictionID"] . " id=och_" . $rec["OntologyObjectPropertyRestrictionID"] . ">";
				echo "</td>";
				echo "<td >";
				echo $rec["DomainClassLabel"];
				echo "<a href='ManageOntologyProperties.php?DoNotShowList=1&UpdateID=" . $rec["OntologyPropertyID"] . "&OntologyID=" . $obj->OntologyID . "' target=_blank>";
				echo " <b>" . $rec["PropertyLabel"] . "</b> ";
				echo "</a>";
				echo $rec["RangeClassLabel"];
				echo "</td></tr>";
			}
		}
		?>
		<tr class=FooterOfTable>
			<td colspan=2 align=center>
				<input class="btn btn-danger" type=button value='حذف' onclick='javascript: ConfirmDelete();'>
			</td>
		</tr>
</form>
<script>
	function ConfirmDelete() {
		if (confirm(C_ARE_U_SURE)) document.ListForm.submit();
	}
</script>

</html>