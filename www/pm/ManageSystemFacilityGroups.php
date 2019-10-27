<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/
include("header.inc.php");
//include("../sharedClasses/SharedClass.class.php");
include("classes/SystemFacilityGroups.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_GroupName"]))
		$Item_GroupName=$_REQUEST["Item_GroupName"];
	if(isset($_REQUEST["Item_OrderNo"]))
		$Item_OrderNo=$_REQUEST["Item_OrderNo"];
    if(isset($_REQUEST["Item_EGroupName"]))
        $Item_OrderNo=$_REQUEST["Item_EGroupName"];
	if(!isset($_REQUEST["UpdateID"]))
	{	
		manage_SystemFacilityGroups::Add($Item_GroupName, $Item_EGroupName
				, $Item_OrderNo
				);
	}	
	else 
	{	
		manage_SystemFacilityGroups::Update($_REQUEST["UpdateID"] 
				, $Item_GroupName
            , $Item_EGroupName
				, $Item_OrderNo
				);
	}	
	echo SharedClass::CreateMessageBox(C_DATA_SAVE_SUCCESS);
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_SystemFacilityGroups();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_GroupName.value='".htmlentities($obj->GroupName, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_EGroupName.value='".htmlentities($obj->EGroupName, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_OrderNo.value='".htmlentities($obj->OrderNo, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br>
<div class="row">
    <div class="col-1"></div>
    <div class="col-10">
        <table class="table table-sm table-borderless">
            <thead>
                <tr class="table-info">
                <td class="text-center"><? echo C_CREATE."/".C_EDIT ?></td>
                </tr>
            </thead>
        <tr>
        <td>
        <table>
        <tr>
            <td width="1%" nowrap>
                <label for="Item_GroupName">
                <? echo C_NAME." (".C_PERSIAN.")";; ?>
                </label>
            </td>
            <td nowrap>
            <input type="text" name="Item_GroupName" id="Item_GroupName" maxlength="145" size="40" required>
            </td>
        </tr>
        <tr>
            <td width="1%" nowrap>
                <label for="Item_EGroupName">
                <? echo C_NAME." (".C_ENGLISH.")";; ?>
                </label>
            </td>
            <td nowrap>
                <input type="text" name="Item_EGroupName" id="Item_EGroupName" maxlength="145" size="40" required>
            </td>
        </tr>
        <tr>
            <td width="1%" nowrap>
                <label for="Item_OrderNo">
                <? echo C_ORDER; ?>
                </label>
            </td>
            <td nowrap>
            <input type="text" name="Item_OrderNo" id="Item_OrderNo" maxlength="20" size="40">
            </td>
        </tr>
        </table>
        </td>
        </tr>
        <tr class="table-info">
        <td align="center">
        <input type="submit" class="btn btn-success"  value="<? echo C_SAVE; ?>">
         <input type="button" class="btn btn-info" onclick="document.location='ManageSystemFacilityGroups.php';" value="<? echo C_NEW; ?>">
        </td>
        </tr>
        </table>
    </div>
    <div class="col-1"></div>
</div>

<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
</script>
<?php 
$res = manage_SystemFacilityGroups::GetList(); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->GroupID])) 
	{
		manage_SystemFacilityGroups::Remove($res[$k]->GroupID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_SystemFacilityGroups::GetList(); 
?>
<form id="ListForm" name="ListForm" method="post"> 
<br>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
    <table class="table table-bordered table-sm table-striped">
        <thead class="table-info">
            <tr>
                <td width="1%"> </td>
                <td width="1%"><? echo C_ROW; ?></td>
                <td width="2%"><? echo C_EDIT; ?></td>
                <td><? echo C_NAME." (".C_ENGLISH.")"; ?></td>
                <td><? echo C_NAME." (".C_PERSIAN.")"; ?></td>
                <td><? echo C_ORDER; ?></td>
            </tr>
        </thead>
<?
for($k=0; $k<count($res); $k++)
{
	echo "<tr>";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->GroupID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageSystemFacilityGroups.php?UpdateID=".$res[$k]->GroupID."\"><i class='fas fa-edit'></i></a></td>";
    echo "	<td>".htmlentities($res[$k]->EGroupName, ENT_QUOTES, 'UTF-8')."</td>";
    echo "	<td>".htmlentities($res[$k]->GroupName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->OrderNo, ENT_QUOTES, 'UTF-8')."</td>";
	echo "</tr>";
}
?>
<tr class="table-info">
<td colspan="6" align="center">
	<input type="button" class="btn btn-danger" onclick="ConfirmDelete();" value="<? echo C_REMOVE; ?>">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewSystemFacilityGroups.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('<? echo C_ARE_YOU_SURE ?>')) document.ListForm.submit();
}
</script>
</html>
