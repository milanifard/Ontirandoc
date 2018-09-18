<? 
include "header.inc.php"; 
HTMLBegin();
?>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<?php 
$mysql = dbclass::getInstance();

$query = "select f1.*,f2.FieldTitle from formsgenerator.FormsFlowStepRelationDetails f1
     left join FormFields f2 using(FormFieldID)
    where FormsFlowStepRelationID='".$_REQUEST["FormFlowStepRelationID"]."' order by OrderNo";
$res = $mysql->Execute($query);
//echo $query;die();
$list = "";
$i = 0;
while($rec = $res->FetchRow())
{
if(isset($_REQUEST["Delete"]) && isset($_REQUEST["ch_".$rec["FormsFlowStepRelationDetailID"]]))
	{
		$mysql->Execute("delete from formsgenerator.FormsFlowStepRelationDetails
                    where FormsFlowStepRelationDetailID='".$rec["FormsFlowStepRelationDetailID"]."'");
		//$mysql->Execute("update mis.WizardReports set ReadyForExecute='NO' where WizardReportID='".$_REQUEST["WizardReportID"]."'");
		//$mysql->audit("حذف کردن شرط فیلتر ردیف ".$rec["WizardReportRowsFilterID"]."  از گزارش ویزاردی کد ".$_REQUEST["WizardReportID"]);	
	}
        else{
                $i++;
                if($i%2==0)
                        $list .= "<tr class=OddRow>";
                else
                        $list .= "<tr class=EvenRow>";
                $list .= "<td>";
                $list .= "<input type=checkbox name=ch_".$rec["FormsFlowStepRelationDetailID"].">";
                $list .= "</td><td>";
                if($rec["Starter"]=="YES")
                    $list .= "(";
                else
                    $list .= "-";
                $list .= "</td><td dir=rtl>";
                $list .= "<a href='CreateFormFlowRole.php?FormFlowStepRelationID=".$_REQUEST['FormFlowStepRelationID']."&FormsFlowStepRelationDetailID=".$rec["FormsFlowStepRelationDetailID"]."' target=_blank>";
                $list .= $rec["FieldTitle"];
                $list .= "</td><td>";
                if($rec["OperationType"]=="eq")
                        $list .= "مساوی";
                else if($rec["OperationType"]=="nq")
                        $list .= "مخالف";
                else if($rec["OperationType"]=="gt")
                        $list .= "بزرگتر از";
                else if($rec["OperationType"]=="lt")
                        $list .= "کوچکتر از";
                else if($rec["OperationType"]=="gtq")
                        $list .= "بزرگتر یا مساوی";
                else if($rec["OperationType"]=="ltq")
                        $list .= "کوچکتر یا مساوی";
                else if($rec["OperationType"]=="LIKE")
                        $list .= "محتوی";
                else
                        $list .= " * ";

                $list .= "</td><td>";
                $list .= $rec["Value"];
                $list .= "</td><td>";
                if($rec["Ender"]=="YES")
                    $list .= ")";
                else
                    $list .= "-";
                $list .= "</td><td>";
                if($rec["Relation"]=="OR")
                        $list .= "یا";
                else if($rec["Relation"]=="AND")
                        $list .= "و";
                else
                        $list .= " - ";
                $list .= "</td>";
                $list .= "</tr>";
        }
}
?>
<br>
<form method=post id=f2 name=f2>
<table width=95% align=center border=1 cellspacing=0 cellpadding=3>
	<tr class=HeaderOfTable>
		<td align=center colspan=7>شرط های مربوط به مرحله بعد   </td>
	</tr>
	<tr bgcolor=#aaaaaa>
		<td>انتخاب</td>
		<td>آغازگر</td>
		<td>نام فیلد</td>
                <td>عملگر مقایسه ای</td>
                <td>مقدار</td>
                <td>خاتمه دهنده</td>
                <td>عملگر شرطی</td>
	</tr>
	<?php echo $list; ?>
	<tr class=FooterOfTable>
		<td colspan=7  align=center>
			<input type=submit value='حذف' name=Delete id=Delete>		
			&nbsp;
			<input type=button value='ایجاد شرط' onclick='javascript: window.open("CreateFormFlowRole.php?FormFlowStepRelationID=<?php echo $_REQUEST['FormFlowStepRelationID'] ?>")'>
		</td>
	</tr>
</table>
</form>
</html>