<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : بخش بندیهای فرمها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 90-5-9
*/

// This file taken by MGhayour
// local url: http://localhost:90/MyProject/Ontirandoc/www/FormsGenerator/ManageFormsSections.php

include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/FormsSections.class.php");
include_once("classes/FormsStruct.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["FormsStructID"]))
		$Item_FormsStructID=$_REQUEST["FormsStructID"];
	if(isset($_REQUEST["Item_SectionName"]))
		$Item_SectionName=$_REQUEST["Item_SectionName"];
	if(isset($_REQUEST["Item_ShowOrder"]))
	{
		$Item_ShowOrder=$_REQUEST["Item_ShowOrder"];
		$Item_HeaderDesc=$_REQUEST["Item_HeaderDesc"];
		$Item_FooterDesc=$_REQUEST["Item_FooterDesc"];
	}
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FormsSections::Add($Item_FormsStructID
				, $Item_SectionName
				, $Item_ShowOrder
				, $Item_HeaderDesc
				, $Item_FooterDesc
				);
	}	
	else 
	{	
		manage_FormsSections::Update($_REQUEST["UpdateID"] 
				, $Item_SectionName
				, $Item_ShowOrder
				, $Item_HeaderDesc
				, $Item_FooterDesc
				);
	}	
	echo SharedClass::CreateMessageBox(C_DATA_SAVE_SUCCESS);
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FormsSections();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_SectionName.value='".htmlentities($obj->SectionName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ShowOrder.value='".htmlentities($obj->ShowOrder, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	//$LoadDataJavascriptCode .= "document.f1.Item_HeaderDesc.value='".htmlentities($obj->HeaderDesc, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	//$LoadDataJavascriptCode .= "document.f1.Item_FooterDesc.value='".htmlentities($obj->FooterDesc, ENT_QUOTES, 'UTF-8')."'; \r\n ";
}	
?>
<div class="container">
<form method="post" id="f1" name="f1" >
	<?
		if(isset($_REQUEST["UpdateID"])) 
		{
			echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		}
	//echo manage_FormsStruct::ShowSummary($_REQUEST["FormsStructID"]);
	//echo manage_FormsStruct::ShowTabs($_REQUEST["FormsStructID"], "ManageFormsSections");
	?>
	<br>
	<div class="row justify-content-center">
		<div class="card">
			<div class="card-header">
				<i class="fa fa-file-signature"></i>
				<?php echo C_FORMPART_NEWEDIT; ?>
			</div>
			<div class="card-body">
				<table class="table">
					<? 
					if(!isset($_REQUEST["UpdateID"]))
					{
					?> 
					<input type="hidden" name="FormsStructID" id="FormsStructID" value='<? if(isset($_REQUEST["FormsStructID"])) echo htmlentities($_REQUEST["FormsStructID"], ENT_QUOTES, 'UTF-8'); ?>'>
					<? } ?>
					<tr>
						<td width="1%" nowrap>
							<?php echo C_FORMPART_NAME; ?>
						</td>
						<td nowrap>
						<input type="text" name="Item_SectionName" id="Item_SectionName" maxlength="250" size="40">
						</td>
					</tr>
					<tr>
						<td width="1%" nowrap>
							<?php echo C_FORMPART_ORDER; ?>
						</td>
						<td nowrap>
						<input type="text" name="Item_ShowOrder" id="Item_ShowOrder" maxlength="2" size="2">
						</td>
					</tr>
					<tr>
						<td width="1%" nowrap>
							<?php echo C_FORMPART_TOPTEXT; ?>
						</td>
						<td nowrap>
						<textarea name="Item_HeaderDesc" id="Item_HeaderDesc" cols="50" rows="5"><?php  if(isset($_REQUEST["UpdateID"])) echo $obj->HeaderDesc; ?></textarea>
						</td>
					</tr>
					<tr>
						<td width="1%" nowrap>
							<?php echo C_FORMPART_BOTTOMTEXT; ?>
						</td>
						<td nowrap>
						<textarea name="Item_FooterDesc" id="Item_FooterDesc" cols="50" rows="5"><?php  if(isset($_REQUEST["UpdateID"])) echo $obj->FooterDesc; ?></textarea>
						</td>
					</tr>

				</table>

				<input class="btn btn-primary" type="button" onclick="javascript: ValidateForm();" value="<?php echo C_SAVE; ?>">
				<input class="btn btn-light"   type="button" onclick="javascript: document.location='ManageFormsSections.php?FormsStructID=<?php echo $_REQUEST["FormsStructID"]; ?>'" value="<?php echo C_NEW; ?>">
			</div>
		</div>
	</div>
	<input type="hidden" name="Save" id="Save" value="1">
</form>
</div>
<script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$res = manage_FormsSections::GetList($_REQUEST["FormsStructID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FormsSectionID])) 
	{
		manage_FormsSections::Remove($res[$k]->FormsSectionID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_FormsSections::GetList($_REQUEST["FormsStructID"]); 
?>


<div class="container">
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_FormsStructID" name="Item_FormsStructID" value="<? echo htmlentities($_REQUEST["FormsStructID"], ENT_QUOTES, 'UTF-8'); ?>">
<br>

	
	<div class="row justify-content-center">
		<div class="card">
			<div class="card-header">
				<i class="fa fa-file-alt"></i>
				<?php echo C_FORMPART_TITLE; ?>
			</div>
			<div class="card-body">
				<table class="table table-striped">
					<tr class="HeaderOfTable">
						<td width="1%"> </td>
						<td width="1%"><?php echo C_ROW; ?></td>
						<td width="2%"><?php echo C_EDIT; ?></td>
						<td><?php echo C_NAME; ?></td>
						<td><?php echo C_ORDER; ?></td>
					</tr>
					<?
					for($k=0; $k<count($res); $k++)
					{
						echo "<tr>";
						echo "<td>";
						echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->FormsSectionID."\">";
						echo "</td>";
						echo "<td>".($k+1)."</td>";
						echo "	<td><a href=\"ManageFormsSections.php?UpdateID=".$res[$k]->FormsSectionID."&FormsStructID=".$_REQUEST["FormsStructID"]."\"><i class='fa fa-edit'></i></a></td>";
						echo "	<td>".htmlentities($res[$k]->SectionName, ENT_QUOTES, 'UTF-8')."</td>";
						echo "	<td>".htmlentities($res[$k]->ShowOrder, ENT_QUOTES, 'UTF-8')."</td>";
						echo "</tr>";
					}
					?>
				</table>

				<input class="btn btn-danger" type="button" onclick="javascript: ConfirmDelete();" value="<?php echo C_REMOVE; ?>">
				<input class="btn btn-light"   type="button" onclick="javascript: document.location='ManageQuestionnaires.php';" value="<?php echo C_RETURN; ?>">
			</div>
		</div>
	</div>


</form>
</div>

<form target="_blank" method="post" action="NewFormsSections.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="FormsStructID" name="FormsStructID" value="<? echo htmlentities($_REQUEST["FormsStructID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
<br>
<br>
</body>
</html>
