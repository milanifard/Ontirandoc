<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : سلسله مراتب کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/

/*
edited by: Mohammad Kahani SID: 9512762447
*/

include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyClassHirarchy.class.php");
include ("classes/OntologyClasses.class.php");
HTMLBegin();
$mysql = pdodb::getInstance();
$obj = new be_OntologyClasses();
$obj->LoadDataFromDatabase($_REQUEST["OntologyClassID"]);
$OntologyID = $obj->OntologyID;
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["OntologyClassID"]))
		$Item_OntologyClassID=$_REQUEST["OntologyClassID"];
	if(isset($_REQUEST["Item_OntologyClassParentID"]))
		$Item_OntologyClassParentID=$_REQUEST["Item_OntologyClassParentID"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_OntologyClassHirarchy::Add($Item_OntologyClassID
				, $Item_OntologyClassParentID
				);
	}	
	else 
	{	
		manage_OntologyClassHirarchy::Update($_REQUEST["UpdateID"] 
				, $Item_OntologyClassParentID
				);
	}	
	echo SharedClass::CreateMessageBox(C_DATA_SAVE_SUCCESS);
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_OntologyClassHirarchy();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_OntologyClassParentID.value='".htmlentities($obj->OntologyClassParentID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		echo manage_OntologyClasses::ShowSummary($_REQUEST["OntologyClassID"]);
		echo manage_OntologyClasses::ShowTabs($_REQUEST["OntologyClassID"], "ManageOntologyClassHirarchy");
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
						<th class="text-center"><? echo C_T_CREATE_EDIT_SUBCLASS; ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<table>
								<tr>
									<td width="25%" nowrap>
											<?
											if (!isset($_REQUEST["UpdateID"])) {
											?>
												<input class="form-control" type="text" name="OntologyClassID" id="OntologyClassID" maxlength="25" value='<? if (isset($_REQUEST["OntologyClassID"])) echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>'>
											<? } ?>
									</td>
								</tr>
								<tr>
									<th>
										<label for="OntologyClassID">
											<? echo C_T_CHILD_CLASS ?>
										</label>
									</th>
									<td width="" nowrap>
										<select class="browser-default custom-select" name="Item_OntologyClassParentID" id="Item_OntologyClassParentID">
										<option value=0>-
											<?
												$list = manage_OntologyClasses::GetList($obj->OntologyID);
												for($i=0; $i<count($list); $i++)
												{
												echo "<option value='".$list[$i]->OntologyClassID."'>";
												echo $list[$i]->label." (".$list[$i]->ClassTitle.")";
												}
											/*$res = $mysql->Execute("select OntologyClassID, ClassTitle from projectmanagement.OntologyClasses where OntologyID='".$obj->OntologyID."'");
											while($rec = $res->fetch())
											{
												echo "<option value='".$rec["OntologyClassID"]."'>".$rec["ClassTitle"];
											}*/
											?>
										</select>
										<a onclick='javascript: window.open("ShowOntologyClassTree.php?ReturnID=1&InputName=Item_OntologyClassParentID&OntologyID=<? echo $OntologyID;  ?>")' href="#"><? echo C_SELECT ?></a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
				<thead class="text-center table-info">
						<tr>
							<td>
								<button type="button" class="btn btn-success" onclick="javascript: ValidateForm();"><? echo C_SAVE ?></button>
								<button type="button" class="btn btn-info" onclick="javascript: document.location='ManageOntologyClassHirarchy.php?OntologyClassID=<?php echo $_REQUEST["OntologyClassID"]; ?>'"><? echo C_NEW ?></button>
								<button type="button" class="btn btn-danger" onclick="javascript:windows.close();"><? echo C_CLOSE ?></button>

							</td>
						</tr>
					</thead>
			</table>
		</div>
	</div>
</div>
<input type="hidden" name="Save" id="Save" value="1">

</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$res = manage_OntologyClassHirarchy::GetList($_REQUEST["OntologyClassID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->OntologyClassHirarchyID])) 
	{
		manage_OntologyClassHirarchy::Remove($res[$k]->OntologyClassHirarchyID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_OntologyClassHirarchy::GetList($_REQUEST["OntologyClassID"]); 
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
							<th class="text-center" colspan="6"><? echo C_T_SUBCLASS; ?></th>
						</tr>
						<tr>
							<td width="1%"> </td>
							<td width="1%"><? echo C_ROW ?></td>
							<td width="2%"><? echo C_EDIT ?></td>
							<td width="30%"><? echo C_T_CLASS; ?></td>
							<td width="20%"><? echo C_T_SUBCLASS ?></td>
						</tr>
					</thead>
					<?
					for ($k = 0; $k < count($res); $k++) {
						if ($k % 2 == 0)
							echo "<tr class=\"OddRow\">";
						else
							echo "<tr class=\"EvenRow\">";
						echo "<td>";
						echo "<input type=\"checkbox\" name=\"ch_" . $res[$k]->OntologyClassHirarchyID . "\">";
						echo "</td>";
						echo "<td>" . ($k + 1) . "</td>";
						echo "	<td><a href=\"ManageOntologyClassHirarchy.php?UpdateID=" . $res[$k]->OntologyClassHirarchyID . "&OntologyClassID=" . $_REQUEST["OntologyClassID"] . "\"><i class='fas fa-edit'></i></a></td>";
						echo "	<td>" . str_replace("\r", "<br>", htmlentities($res[$k]->label, ENT_QUOTES, 'UTF-8')) . "</td>";
						echo "</tr>";
					}
					?>
					<tr class="table-info">
						<td colspan="6" align="center">
							<input type="button" class="btn btn-danger" onclick="ConfirmDelete();" value="<? echo C_DELETE; ?>">
						</td>
					</tr>
				</table>
			</div>
			<div class="col-1"></div>
		</div>
	</div>

</form>

<form target="_blank" method="post" action="NewOntologyClassHirarchy.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyClassID" name="OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('<? echo C_ARE_YOU_SURE ?>')) document.ListForm.submit();
}
</script>
</html>
