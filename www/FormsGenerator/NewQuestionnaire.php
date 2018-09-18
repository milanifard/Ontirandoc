<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormsFlowStepRelations.class.php");
include("classes/FormsFlowSteps.class.php");
HTMLBegin();

$mysql = pdodb::getInstance();

if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"]))
	{
		$CurID = date("Y_m_d_H_i_s");
		$RelatedTable = "Q".$CurID;
		$query = "create table formsgenerator.".$RelatedTable." (".$RelatedTable."ID INT UNSIGNED NOT NULL AUTO_INCREMENT, MasterID INT, PRIMARY KEY (`".$RelatedTable."ID`))";
		//echo $query;
		//die();
		$mysql->Execute($query);

		$query = "insert into formsgenerator.FormsStruct (RelatedDB
				, RelatedTable
				, FormTitle
				, TopDescription
				, ButtomDescription
				, CreatorUser
				, CreateDate
				, KeyFieldName
				, ShowType
				, IsQuestionnaire
				, ShowBorder
				, QuestionColumnWidth
				) values ('formsgenerator'
				, ?, ?, ?, ?
				, '".$_SESSION["UserID"]."'
				, now()
				, ?
				, ?
				, 'YES'		
				, ?
				, ?
				)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RelatedTable
				, $_REQUEST["Item_FormTitle"]
				, $_REQUEST["Item_TopDescription"]
				, $_REQUEST["Item_ButtomDescription"]
				, $RelatedTable."ID"
				, $_REQUEST["Item_ShowType"]
				, $_REQUEST["Item_ShowBorder"]
				, $_REQUEST["Item_QuestionColumnWidth"]
		));
		$FormID = manage_FormsStruct::GetLastID();
		$mysql->audit("ایجاد پرسشنامه [".$FormID."]");

		echo "<script>";
		echo "document.location='NewQuestionnaire.php?UpdateID=".manage_FormsStruct::GetLastID()."';";
		echo "</script>";
		die();
	}
	else
	{
		$query = "update formsgenerator.FormsStruct set FormTitle=?
				, TopDescription=?
				, ButtomDescription=?
				, ShowType=?
				, ShowBorder=?
				, QuestionColumnWidth=?
				where FormsStructID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($_REQUEST["Item_FormTitle"]
								, $_REQUEST["Item_TopDescription"]
								, $_REQUEST["Item_ButtomDescription"]
								, $_REQUEST["Item_ShowType"]
								, $_REQUEST["Item_ShowBorder"]
								, $_REQUEST["Item_QuestionColumnWidth"]
								, $_REQUEST["UpdateID"]
								));
		$mysql->audit("بروزرسانی پرسشنامه [".$_REQUEST["UpdateID"]."]");

	}
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
$TopDescription = "";
$ButtomDescription = "";
$JavascriptCode = "";
if(isset($_REQUEST["UpdateID"]))
{

	$obj = new be_FormsStruct();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	if($obj->CreatorUser!=$_SESSION["UserID"] && !$obj->HasThisPersonAccessToManageStruct($_SESSION["PersonID"]))
	{
		echo "You don't have permission";
		die();
	}

	$obj = new be_FormsStruct();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$LoadDataJavascriptCode .= "document.getElementById('Item_RelatedDB').innerHTML='".$obj->RelatedDB."'; \r\n ";
	$LoadDataJavascriptCode .= "document.getElementById('Item_RelatedTable').innerHTML='".$obj->RelatedTable."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_FormTitle.value='".$obj->FormTitle."'; \r\n ";
	//$LoadDataJavascriptCode .= "document.f1.Item_SortByField.value='".$obj->SortByField."'; \r\n ";
	//$LoadDataJavascriptCode .= "document.f1.Item_SortType.value='".$obj->SortType."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_ShowType.value='".$obj->ShowType."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_ShowBorder.value='".$obj->ShowBorder."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_QuestionColumnWidth.value='".$obj->QuestionColumnWidth."'; \r\n ";
	$LoadDataJavascriptCode .= "document.getElementById('Item_CreatorID').innerHTML='".$obj->CreatorUser."'; \r\n ";
	$LoadDataJavascriptCode .= "document.getElementById('Item_CreateDate').innerHTML='".$obj->CreateDate."'; \r\n ";

	$TopDescription = $obj->TopDescription;
	$ButtomDescription = $obj->ButtomDescription;
	$JavascriptCode = $obj->JavascriptCode;
	$ValidationExtraJavaScriptCode = $obj->ValidationExtraJavaScript;
}
?>
<form method=post id=f1 name=f1><?
if(isset($_REQUEST["UpdateID"]))
{
	echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
}
?> <br>
<table width=90% border=1 cellspacing=0 align=center>
	<tr class=HeaderOfTable>
		<td align=center>ایجاد/ویرایش پرسشنامه</td>
	</tr>
	<tr>
		<td>
		<table width=100% border=0 cellpadding=3>
		<?
		if(isset($_REQUEST["UpdateID"]))
		{
			?>
			<tr id=tr_RelatedDB name=tr_RelatedDB style='display: '>
				<td width=1% nowrap valign=center>بانک اطلاعاتی مربوطه</td>
				<td nowrap><span name=Item_RelatedDB id=Item_RelatedDB></span></td>
			</tr>
			<tr id=tr_RelatedTable name=tr_RelatedTable style='display: '>
				<td width=1% nowrap>جدول اطلاعاتی مربوطه</td>
				<td nowrap><span name=Item_RelatedTable id=Item_RelatedTable></span>
				</td>
				<? } ?>
			</tr>
			<tr id=tr_FormTitle name=tr_FormTitle style='display: '>
				<td width=1% nowrap>عنوان فرم</td>
				<td nowrap><input type=text name=Item_FormTitle id=Item_FormTitle
					size=52></td>
			</tr>
			<tr id=tr_TopDescription name=tr_TopDescription style='display: '>
				<td width=1% nowrap>توضیحات بالای فرم</td>
				<td width=10% nowrap><textarea name=Item_TopDescription
					id=Item_TopDescription cols=40 rows=4><?php echo $TopDescription; ?></textarea>

				</td>
			</tr>
			<tr id=tr_ButtomDescription name=tr_ButtomDescription
				style='display: '>
				<td nowrap>توضیحات پایین فرم</td>
				<td width=1% nowrap><textarea name=Item_ButtomDescription
					id=Item_ButtomDescription cols=40 rows=4><?php echo $ButtomDescription; ?></textarea>
				</td>
			</tr>
			<tr>
				<td width=1% nowrap>نوع نمایش صفحه ورود داده</td>
				<td nowrap><select name=Item_ShowType id=Item_ShowType>
					<option value='2COLS'>دو ستونی
					<option value='1COLS'>یک ستونی
				</select></td>
			</tr>
			<tr >
				<td width=1% nowrap>عرض ستون سوالات</td>
				<td nowrap><input type=text name=Item_QuestionColumnWidth id=Item_QuestionColumnWidth size=4> در صورتیکه این گزینه خالی باشد عرض ستون به اندازه طول بلندترین سوال خواهد بود</td>
			</tr>
			<tr>
				<td width=1% nowrap>حاشیه برای ردیفهای فرم</td>
				<td nowrap><select name=Item_ShowBorder id=Item_ShowBorder>
					<option value='NO'>قرار داده نشود
					<option value='YES'>قرار داده شود
				</select></td>
			</tr>
			<?
			if(isset($_REQUEST["UpdateID"]))
			{
				?>

			<tr id=tr_CreatorID name=tr_CreatorID style='display: '>
				<td width=1% nowrap>کاربر سازنده</td>
				<td nowrap><span name=Item_CreatorID id=Item_CreatorID></span></td>
			</tr>
			<? } ?>
			<?
			if(isset($_REQUEST["UpdateID"]))
			{
				?>

			<tr id=tr_CreateDate name=tr_CreateDate style='display: '>
				<td width=1% nowrap>تاریخ ایجاد</td>
				<td nowrap><span name=Item_CreateDate id=Item_CreateDate></span></td>
			</tr>
			<tr>
				<td colspan=2><a
					href='ManageQuestionnaireFields.php?FormsStructID=<?php echo $_REQUEST["UpdateID"] ?>'><img
					width=35 title='مدیریت گزینه ها' src='images/Fields.gif' border=0></a>
				&nbsp; <a
					href='ManageQuestionnaireDetailTables.php?Item_FormStructID=<?php echo $_REQUEST["UpdateID"] ?>'><img
					width=35 title='مدیریت جداول جزییات' src='images/Tables.gif'
					border=0></a></td>
			</tr>
			<? } ?>
		</table>
		</td>
	</tr>
	<tr class=FooterOfTable>
		<td colspan=2 align=center><input type=button
			onclick='javascript: ValidateForm();' value='ذخیره'>&nbsp;<input
			type=button value='بازگشت'
			onclick='javascript: document.location="ManageQuestionnaires.php";'></td>
	</tr>
</table>
<input type=hidden name=Save id=Save value=1></form>
<script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
