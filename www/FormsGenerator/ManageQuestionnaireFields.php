<?php
include("header.inc.php");
include_once("classes/FormFields.class.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormLabels.class.php");
HTMLBegin();
$k = 0;
$FormsStructID = $_REQUEST["FormsStructID"];

$obj = new be_FormsStruct();
$obj->LoadDataFromDatabase($FormsStructID);
if($obj->CreatorUser!=$_SESSION["UserID"] && !$obj->HasThisPersonAccessToManageStruct($_SESSION["PersonID"]))
{
	echo "You don't have permission";
	die();
}

echo "<br><table align=center width=80% border=1 cellspacing=0 cellpadding=6><tr bgcolor=#cccccc><td>مدیریت فیلدهای: <b>".$obj->FormTitle."</b></td></tr></table>";

$res = manage_FormFields::GetList($FormsStructID, "OrderInInputForm"); 
echo "<form id=f1 name=f1 method=post>"; 

echo "<input type=hidden name=FormsStructID value=".$_REQUEST["FormsStructID"].">";
if(isset($_REQUEST["PageNumber"]))
	echo "<input type=hidden name=PageNumber value=".$_REQUEST["PageNumber"].">"; 
echo "<table width=80% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%>&nbsp;</td>";
echo "	<td width=1%>کد</td>";
echo "	<td>گروه</td>";
echo "	<td>عنوان گزینه</td>";
echo "	<td width=1%>نوع</td>";
echo "	<td width=1%>اجباری</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FormFieldID])) 
	{
		manage_FormFields::Remove($res[$k]->FormFieldID); 
	}
	else
	{
		// لیست برچسبهای قبل از فیلد جاری را بر می گرداند
		$list = manage_FormLabels::GetList(" LocationType='BEFORE' and RelatedFieldID='".$res[$k]->FormFieldID."' ");
		for($c=0; $c<count($list); $c++)
		{
			if(isset($_REQUEST["cl_".$list[$c]->FormsLabelID])) 
			{
				manage_FormLabels::Remove($list[$c]->FormsLabelID); 
			}
			else
			{
				echo "<tr bgcolor=#bbbbbb>";
				echo "<td><input type=checkbox name=cl_".$list[$c]->FormsLabelID."></td>";
				echo "	<td colspan=2><a target=_blank href='NewQuestionnaireLabel.php?Item_FormsStructID=".$FormsStructID."&UpdateID=".$list[$c]->FormsLabelID."'>".$list[$c]->FormsLabelID."</a></td>";
				echo "	<td>".$list[$c]->LabelDescription."</td>";
				echo "	<td nowrap>برچسب</td>";
				echo "	<td>&nbsp;</td>";
				echo "</tr>";
			}			
		}
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FormFieldID."></td>";
		echo "	<td><a target=_blank href='NewQuestionnaireField.php?Item_FormsStructID=".$FormsStructID."&UpdateID=".$res[$k]->FormFieldID."'>".$res[$k]->FormFieldID."</a></td>";
		echo "	<td>&nbsp;".$res[$k]->SectionName."</td>";		
		echo "	<td>".$res[$k]->FieldTitle."</td>";
		echo "	<td nowrap>".$res[$k]->FieldTypeName."</td>";
		//echo "	<td>".$res[$k]->FieldInputType."</td>";
		
		if($res[$k]->FieldInputType=="MANDATORY")
			echo "	<td align=center valign=middle><font color=red>X</font></td>";
		else
			echo "	<td>&nbsp;</td>";
		
		echo "</tr>";
		// لیست برچسبهای بعد از فیلد جاری را بر می گرداند
		$list = manage_FormLabels::GetList(" LocationType='AFTER' and RelatedFieldID='".$res[$k]->FormFieldID."' ");
		for($c=0; $c<count($list); $c++)
		{
			if(isset($_REQUEST["cl_".$list[$c]->FormsLabelID])) 
			{
				manage_FormLabels::Remove($list[$c]->FormsLabelID); 
			}
			else
			{
				echo "<tr bgcolor=#bbbbbb>";
				echo "<td><input type=checkbox name=cl_".$list[$c]->FormsLabelID."></td>";
				echo "	<td><a target=_blank href='NewQuestionnaireLabel.php?Item_FormsStructID=".$FormsStructID."&UpdateID=".$list[$c]->FormsLabelID."'>".$list[$c]->FormsLabelID."</a></td>";
				echo "	<td>".$list[$c]->LabelDescription."</td>";
				echo "	<td nowrap>برچسب</td>";
				echo "	<td>&nbsp;</td>";
				echo "</tr>";
			}			
		}
		
	}
}
echo "<tr class=FooterOfTable><td colspan=6 align=center><input type=submit value='حذف'>&nbsp;";
echo "<input type=button value='اضافه کردن گزینه' onclick='javascript: window.open(\"NewQuestionnaireField.php?Item_FormsStructID=".$FormsStructID."\");'>&nbsp;";
echo "<input type=button value='اضافه کردن برچسب' onclick='javascript: window.open(\"NewQuestionnaireLabel.php?Item_FormsStructID=".$FormsStructID."\");'>";
echo "</td></tr>";
echo "<tr class=FooterOfTable><td colspan=6 align=center><input type=button value='بازگشت' onclick='javascript: document.location=\"ManageQuestionnaires.php\"'></td></tr>";
echo "</table>";
echo "</form>";
?>
</html>