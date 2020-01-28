<?php
include("header.inc.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FormsStruct.class.php");
HTMLBegin();
	$mysql = dbclass::getInstance();
$SelectOptions = "";
$list = manage_FormsStruct::GetList(" ParentID=0 ");
for($i=0; $i<count($list); $i++)
{
	$SelectOptions .= "<option value='".$list[$i]->FormsStructID."'>".$list[$i]->FormTitle;	
}
if(isset($_REQUEST["NewStepID"]))
{
	$obj = new be_FormsStruct();
	$obj->LoadDataFromDatabase($_REQUEST["FormsStructID"]);
	$obj->SendData($_REQUEST["RecID"], $_REQUEST["FromStepID"], $_REQUEST["NewStepID"], $_SESSION["PersonID"]);
	echo "<b><p align=center><font color=green>تغییر مرحله انجام شد</p>";
	die();
}
if(isset($_REQUEST["FormsRecordsID"]))
{
	$query = "select StepTitle, FormsFlowSteps.FormsStructID, RelatedRecordID, FormsRecords.FormFlowStepID, SenderID, g2j(SendDate) as gSendDate
					, concat(p1.plname, ' ', p1.pfname) as SenderName
					, concat(p2.plname, ' ', p2.pfname) as CreatorName
					, FormTitle
					, CreatorID
					, SenderID
					, RelatedOrganzationChartID
					, FormsRecordsID
					 from FormsRecords
					JOIN FormsFlowSteps on (FormsFlowSteps.FormsFlowStepID=FormsRecords.FormFlowStepID)
					JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID)		
					JOIN ".$_REQUEST["DBName"].".persons as p1 on (FormsRecords.SenderID=p1.PersonID)
					JOIN ".$_REQUEST["DBName"].".persons as p2 on (FormsRecords.CreatorID=p2.PersonID)
					where FormsRecords.FormsRecordsID='".$_REQUEST["FormsRecordsID"]."' ";
	$res = $mysql->Execute($query);
	if($rec = $res->FetchRow())
	{
		echo "<form method=post name=f1 id=f1>";
		echo "<input type=hidden name=RecID id=RecID value='".$rec["RelatedRecordID"]."'>";
		echo "<input type=hidden name=FromStepID id=FromStepID value='".$rec["FormFlowStepID"]."'>";
		echo "<input type=hidden name=FormsStructID id=FormsStructID value='".$rec["FormsStructID"]."'>";
		echo "<br><table width=98% align=center border=1 cellspacing=0>";
		echo "<tr>";
		echo "<td>C_FORM_NAME_2</td>";
		echo "<td>".$rec["FormTitle"]."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>C_CREATOR</td>";
		echo "<td>".$rec["CreatorName"]."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>C_SENDER</td>";
		echo "<td>".$rec["SenderName"]."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>C_CURRENT_STEP_NAME</td>";
		echo "<td>".$rec["StepTitle"]."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>C_NEW_STEP</td>";
		echo "<td>";
		echo "<select name=NewStepID id=NewStepID>";
		echo manage_FormsFlowSteps::CreateListOptions(" FormsStructID='".$rec["FormsStructID"]."' ");
		echo "</select>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan=2 align=center><input type=submit value='ارسال'></td>";
		echo "</tr>";
		echo "</table>";
		echo "</form>";
	}
	die();
}
if(isset($_REQUEST["SelectedFormStructID"]))
{
	$db = "";
        if ($_REQUEST["Item_CreatorType"] == "STUDENT"){
            $db = 'educ';
        }
        else if ($_REQUEST["Item_CreatorType"] == "PERSONEL"){
            $db = 'hrms_total';
        }

        $query = "select StepTitle, FormsFlowSteps.FormsStructID, RelatedRecordID, FormsRecords.FormFlowStepID, SenderID, g2j(SendDate) as gSendDate
					, concat(p1.plname, ' ', p1.pfname) as SenderName
					, concat(p2.plname, ' ', p2.pfname) as CreatorName
					, FormTitle
					, CreatorID
					, SenderID
					, RelatedOrganzationChartID
					, FormsRecordsID
					 from FormsRecords
					JOIN FormsFlowSteps on (FormsFlowSteps.FormsFlowStepID=FormsRecords.FormFlowStepID)
					JOIN FormsStruct on (FormsFlowSteps.FormsStructID=FormsStruct.FormsStructID)		
					JOIN ".$db.".persons as p1 on (FormsRecords.SenderID=p1.PersonID)
					JOIN ".$db.".persons as p2 on (FormsRecords.CreatorID=p2.PersonID)
					where FormsRecords.CreatorID='".$_REQUEST["Item_PersonID"]."' ";
	if($_REQUEST["SelectedFormStructID"]!="0")
	{
		$query .= " and FormsStruct.FormsStructID='".$_REQUEST["SelectedFormStructID"]."' ";
	}
        
	$res = $mysql->Execute($query);
	echo "<br><table width=98% align=center border=1 cellspacing=0>";
	echo "<tr class=HeaderOfTable .container>";
	echo "<td width=1% class=\"col-sm-1\">".C_CODEC_CODE."</td>";
	echo "<td width=30% class=\"col-sm-3\">".C_FORM_NAME_2."</td>";
	echo "<td width=10% class=\"col-sm-2\">".CREATOR_M."</td>";
	echo "<td width=10% class=\"col-sm-2\">".C_LAST_SENDER."</td>";
	echo "<td width=10% class=\"col-sm-2\">".C_SEND_TIME."</td>";
	echo "<td width=10% class=\"col-sm-1\">".C_CURRENT_STEP."</td>";
	echo "<td width=10% class=\"col-sm-1\">".C_HISTORY."<?php echo C_HISTORY;?>""</td>";
	echo "</tr>";
	$i = 0 ;
	while($rec = $res->FetchRow())
	{
		$i++;
		if($i%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><a href='ControlForms.php?FormsRecordsID=".$rec["FormsRecordsID"]."&DBName=".$db."'>".$rec["RelatedRecordID"]."</a></td>";
		echo "<td>".$rec["FormTitle"]."</td>";
		echo "<td><a target=_blank  href='../organization/ShowPersonPosition.php?SelectedPersonID=".$rec["CreatorID"]."&ChartID=".$rec["RelatedOrganzationChartID"]."'>".$rec["CreatorName"]."</a></td>";
		echo "<td><a target=_blank  href='../organization/ShowPersonPosition.php?SelectedPersonID=".$rec["SenderID"]."&ChartID=".$rec["RelatedOrganzationChartID"]."'>".$rec["SenderName"]."</a></td>";
		echo "<td>".$rec["gSendDate"]."</td>";
		echo "<td>".$rec["StepTitle"]."</td>";
		echo "<td><a target=_blank href='ShowFormFlowHistory.php?SelectedFormStructID=".$rec["FormsStructID"]."&RelatedRecordID=".$rec["RelatedRecordID"]."'>تاریخچه</a></td>";
		echo "</tr>";
	}
	echo "</table>";
	die();
}
?>
<form method=post id=f1 name=f1>
    <table width=80% align=center border=1 cellspacing=0 class=".container">
        <tr class=HeaderOfTable, row>
            <td align=center>
                <?php
                echo C_SEARCH_SELECTED_FORM;
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <table content="width=device-width, initial-scale=1" width=100% class= row>
                    <tr>
                        <td class="col-sm-6">
                            <?php echo C_FORM_TYPE; ?>
                            </td>
                        <td class="col-sm-6">
                            <select name=SelectedFormStructID id=SelectedFormStructID>
                                <option value=0>-
                                    <?php echo $SelectOptions; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-sm-6">
                            <?php
                            echo C_CREATOR_NAME;
                            ?>
                        </td>
                        <td class="col-sm-6">
                            <input type=hidden name=Item_PersonID id=Item_PersonID value=0>
                            <input type=hidden name=Item_CreatorType id=Item_CreatorType value=0>
                            <span id=MySpan name=MySpan></span>
                            <a target=_blank href='SelectPerson.php?InputName=Item_PersonID&InputType=Item_CreatorType&SpanName=MySpan'>[انتخاب]</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class=FooterOfTable align=center>
                <input type=button value="<?php echo C_SEARCH; ?>" onclick='javascript: CheckValidity();'>

            </td>
        </tr>
    </table>
</form>
<script>
    function CheckValidity()
    {
        if(document.f1.Item_PersonID.value=='0')
        {
            alert('انتخاب فرد الزامی است');
            return;
        }
        document.f1.submit();
    }
</script>
</html>

