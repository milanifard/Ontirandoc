<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormsFlowStepRelations.class.php");
include("classes/FormsFlowSteps.class.php");
HTMLBegin();

if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FormsStruct::Add($_REQUEST["Item_RelatedDB"]
				, $_REQUEST["Item_RelatedTable"]
				, $_REQUEST["Item_FormTitle"]
				, $_REQUEST["Item_TopDescription"]
				, $_REQUEST["Item_ButtomDescription"]
				, $_REQUEST["Item_JavascriptCode"]
				, $_REQUEST["Item_PrintType"]
				, $_REQUEST["Item_PrintPageAddress"]
				, $_REQUEST["Item_ShowType"]
				, $_REQUEST["ValidationExtraJavaScript"]
				);
		echo "<script>";
		echo "document.location='NewFormStruct.php?UpdateID=".manage_FormsStruct::GetLastID()."';";
		echo "</script>";
		die();
	}	
	else 
	{	
		manage_FormsStruct::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_FormTitle"]
				, $_REQUEST["Item_TopDescription"]
				, $_REQUEST["Item_ButtomDescription"]
				, $_REQUEST["Item_JavascriptCode"]
				, $_REQUEST["Item_SortByField"]
				, $_REQUEST["Item_SortType"]
				, $_REQUEST["Item_KeyFieldName"]
				, $_REQUEST["Item_PrintType"]
				, $_REQUEST["Item_PrintPageAddress"]
				, $_REQUEST["Item_ShowType"]
				, $_REQUEST["ValidationExtraJavaScript"]
				);
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
	$LoadDataJavascriptCode .= "document.f1.Item_SortByField.value='".$obj->SortByField."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_SortType.value='".$obj->SortType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_KeyFieldName.value='".$obj->KeyFieldName."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_PrintType.value='".$obj->PrintType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_PrintPageAddress.value='".$obj->PrintPageAddress."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_ShowType.value='".$obj->ShowType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.getElementById('Item_CreatorID').innerHTML='".$obj->CreatorUser."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.getElementById('Item_CreateDate').innerHTML='".$obj->CreateDate."'; \r\n ";

	$TopDescription = $obj->TopDescription;
	$ButtomDescription = $obj->ButtomDescription;
	$JavascriptCode = $obj->JavascriptCode;
	$ValidationExtraJavaScriptCode = $obj->ValidationExtraJavaScript; 
}	
?>
<script>
<? echo PersiateKeyboard() ?>
</script>
<form method=post id=f1 name=f1>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br>
<table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش ساختار فرم</td></tr>
 <tr>
 <td>
 <table width=100% border=0 cellpadding=3>
	<tr id=tr_RelatedDB name=tr_RelatedDB style='display:'>
	<td width=1% nowrap valign=center>
		بانک اطلاعاتی مربوطه
	</td>
	<? 
	if(!isset($_REQUEST["UpdateID"]))
	{
	?>
	<td  nowrap valign=middle>
		<span style="vertical-align:middle; font-size:12px;">
		<input valign=middle dir=ltr type=text name=Item_RelatedDB id=Item_RelatedDB>
		</span>
		<a href='SelectDB.php?FormName=f1&InputName=Item_RelatedDB' target=_blank>
		<img style="vertical-align:middle;" src='images/SelectTable.gif' width=25 title='انتخاب بانک اطلاعاتی و جدول' border=0 valign=middle></a>
	</td>
	<? } else {
	?> 
	<td  nowrap>
		<span name=Item_RelatedDB id=Item_RelatedDB></span>
	</td>
	<? } ?>
	</tr>
	<tr id=tr_RelatedTable name=tr_RelatedTable style='display:'>
	<td width=1% nowrap>
		جدول اطلاعاتی مربوطه
	</td>
	<? 
	if(!isset($_REQUEST["UpdateID"]))
	{
	?> 
	<td nowrap>
		<input dir=ltr type=text name=Item_RelatedTable id=Item_RelatedTable>
	</td>
	<? } else {
	?> 
	<td nowrap>
		<span name=Item_RelatedTable id=Item_RelatedTable></span>
	</td>
	<? } ?>
	</tr>
	<tr id=tr_FormTitle name=tr_FormTitle style='display:'>
	<td width=1% nowrap>
		عنوان فرم
	</td>
	<td nowrap>
		<input type=text name=Item_FormTitle id=Item_FormTitle size=52>
	</td>
	</tr>
	<tr id=tr_TopDescription name=tr_TopDescription style='display:'>
	<td width=1% nowrap>
		توضیحات بالای فرم
	</td>
	<td width=10% nowrap>
		<textarea name=Item_TopDescription id=Item_TopDescription cols=40 rows=4><?php echo $TopDescription; ?></textarea>
		
	</td>
	</tr>
	<tr id=tr_ButtomDescription name=tr_ButtomDescription style='display:'>
	<td nowrap>
		توضیحات پایین فرم
	</td>
	<td width=1% nowrap>
		<textarea name=Item_ButtomDescription id=Item_ButtomDescription cols=40 rows=4><?php echo $ButtomDescription; ?></textarea>
	</td>
	</tr>
	<tr id=tr_JavascriptCode name=tr_JavascriptCode style='display:'>
	<td width=1% nowrap>
		کد جاوا اسکریپت 
	</td>
	<td  nowrap>
		<textarea dir=ltr name=Item_JavascriptCode id=Item_JavascriptCode cols=40 rows=4><?php echo $JavascriptCode; ?></textarea>
	</td>
	</tr>
	<tr id=tr_JavascriptCode name=tr_JavascriptCode style='display:'>
	<td width=1% nowrap>
		کد کنترل محتویات فرم (جاوااسکریپت)
	</td>
	<td  nowrap>
		<textarea dir=ltr name=ValidationExtraJavaScript id=ValidationExtraJavaScript cols=40 rows=4><?php echo $ValidationExtraJavaScriptCode; ?></textarea>
	</td>
	</tr>
	<tr>
	<td width=1% nowrap>
		نوع نمایش صفحه ورود داده 
	</td>
	<td  nowrap>
		<select name=Item_ShowType id=Item_ShowType>
		<option value='2COLS'>دو ستونی
		<option value='1COLS'>یک ستونی
		</select>

	</td>
	</tr>
	<?php if(isset($_REQUEST["UpdateID"])) { ?>
	<tr id=tr_SortByField name=tr_SortByField style='display:'>
	<td width=1% nowrap>
		فیلد پیش فرض مرتب سازی لیست
	</td>
	<td  nowrap>
		<select name=Item_SortByField id=Item_SortByField>
		<?php echo manage_FormsStruct::CreateFieldsOptions($obj->RelatedDB, $obj->RelatedTable) ?>
		</select>
	</td>
	</tr>
	<tr id=tr_SortType name=tr_SortType style='display:'>
	<td width=1% nowrap>
		ترتیب مرتب سازی پیش فرض
	</td>
	<td  nowrap>
		<select name=Item_SortType id=Item_SortType>
		<option value='ASC'>نزولی</option>
		<option value='DESC'>صعودی</option>
		</select>
	</td>
	</tr>
	<tr id=tr_KeyFieldName name=tr_KeyFieldName style='display:'>
	<td width=1% nowrap>
		نام فیلد کلید
	</td>
	<td  nowrap>
		<select name=Item_KeyFieldName id=Item_KeyFieldName>
		<?php echo manage_FormsStruct::CreateFieldsOptions($obj->RelatedDB, $obj->RelatedTable) ?>
		</select>
	</td>
	</tr>
	<? } ?>
	<tr id=tr_PrintType name=tr_PrintType style='display:'>
	<td width=1% nowrap>
		نوع صفحه چاپ
	</td>
	<td  nowrap>
		<select name=Item_PrintType id=Item_PrintType onchange='javascript: ShowHidePrintAddress();'>
		<option value='DEFAULT'>عادی</option>
		<option value='SPECIAL'>اختصاصی</option>
		</select>
	</td>
	</tr>
	<tr id=tr_PrintPageAddress name=tr_PrintPageAddress style='display: none'>
	<td width=1% nowrap>
	 صفحه چاپ اختصاصی
	</td>
	<td nowrap>
		<input size=52 dir=ltr type=text name=Item_PrintPageAddress id=Item_PrintPageAddress>&nbsp;
		<a href='PrintPageHelp.php' target=_blank>
		<img src='../rcssimgs/question.gif' title='راهنما'>
		</a>
	</td>
	</tr>
	<? 
	if(isset($_REQUEST["UpdateID"]))
	{
	?> 
	
	<tr id=tr_CreatorID name=tr_CreatorID style='display:'>
	<td width=1% nowrap>
		کاربر سازنده
	</td>
	<td  nowrap>
		<span name=Item_CreatorID id=Item_CreatorID></span>
	</td>
	</tr>
	<? } ?>
	<? 
	if(isset($_REQUEST["UpdateID"]))
	{
	?> 
	
	<tr id=tr_CreateDate name=tr_CreateDate style='display:'>
	<td width=1% nowrap>
		تاریخ ایجاد
	</td>
	<td  nowrap>
		<span name=Item_CreateDate id=Item_CreateDate></span>
	</td>
	</tr>
	<tr>
		<td colspan=2>
		<a href='ManageFormFields.php?FormsStructID=<?php echo $_REQUEST["UpdateID"] ?>'><img width=35 title='مدیریت فیلدها' src='images/Fields.gif' border=0></a>
		&nbsp;
		<a href='ManageFormDetailTables.php?Item_FormStructID=<?php echo $_REQUEST["UpdateID"] ?>'><img width=35 title='مدیریت جداول جزییات' src='images/Tables.gif' border=0></a>
		&nbsp;
		<a href='ManageFormFlow.php?Item_FormStructID=<?php echo $_REQUEST["UpdateID"] ?>'><img width=35 title='مدیریت جریان کاری' src='images/chart.gif' border=0></a>
		
		</td>
	</tr>
	<? } ?>
	</table>
	</td>
	</tr>
	<tr class=FooterOfTable><td colspan=2 align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'>&nbsp;<input type=button value='بازگشت' onclick='javascript: document.location="ManageFormsStruct.php";'></td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
	function ShowHidePrintAddress()
	{
		if(document.f1.Item_PrintType.value=='DEFAULT')
			document.getElementById('tr_PrintPageAddress').style.display = 'none';
		else
			document.getElementById('tr_PrintPageAddress').style.display = '';
	}
	ShowHidePrintAddress();
</script>
