<?php
include("header.inc.php");
include_once("classes/FormFields.class.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormUtils.class.php");
HTMLBegin();
$mysql = dbclass::getInstance();
$FormFieldID = 0;
$Item_ListQuery = "";
if(isset($_REQUEST["UpdateID"]))
{
	$FormFieldID = $_REQUEST["UpdateID"];
}
if(isset($_REQUEST["Save"]))
{
	$Item_ShowInList = "NO";
	if(isset($_REQUEST["Item_ShowInList"]))
		$Item_ShowInList = "YES";
	$Item_AddAllItemsToList = "NO";
	if(isset($_REQUEST["Item_AddAllItemsToList"]))
		$Item_AddAllItemsToList = "YES";
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FormFields::Add($_REQUEST["Item_FormsStructID"]
				, $_REQUEST["Item_RelatedFieldName"]
				, $_REQUEST["Item_FieldTitle"]
				, $_REQUEST["Item_FieldType"]
				, $_REQUEST["Item_MaxLength"]
				, $_REQUEST["Item_InputWidth"]
				, $_REQUEST["Item_InputRows"]
				, $_REQUEST["Item_MinNumber"]
				, $_REQUEST["Item_MaxNumber"]
				, $_REQUEST["Item_MaxFileSize"]
				, $_REQUEST["Item_CreatingListType"]
				, $Item_AddAllItemsToList
				, $_REQUEST["Item_ListRelatedTable"]
				, $_REQUEST["Item_ListRelatedValueField"]
				, $_REQUEST["Item_ListRelatedDescriptionField"]
				, $_REQUEST["Item_ListRelatedDomainName"]
				, $_REQUEST["Item_ListQuery"]
				, $_REQUEST["Item_FieldInputType"]
				, $_REQUEST["Item_DefaultValue"]
				, $_REQUEST["Item_ValidFileExtensions"]
				, $Item_ShowInList
				, $_REQUEST["Item_ColumnOrder"]
				, $_REQUEST["Item_ColumnWidth"]
				, $_REQUEST["Item_ListShowType"]
				, $_REQUEST["Item_LookUpPageAddress"]
				, $_REQUEST["Item_OrderInInputForm"]
				, $_REQUEST["Item_ImageWidth"]
				, $_REQUEST["Item_ImageHeight"]
				, $_REQUEST["Item_FieldHint"]
				, $_REQUEST["Item_RelatedFileNameField"]
				, $_REQUEST["Item_HTMLEditor"]
				);
			$id = manage_FormFields::GetLastID();
			$mysql = dbclass::getInstance();
			$mysql->Execute("update FieldsItemList set FormFieldID='".$id."' where FormFieldID='0'");
	}	
	else 
	{	
		manage_FormFields::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_RelatedFieldName"]
				, $_REQUEST["Item_FieldTitle"]
				, $_REQUEST["Item_FieldType"]
				, $_REQUEST["Item_MaxLength"]
				, $_REQUEST["Item_InputWidth"]
				, $_REQUEST["Item_InputRows"]
				, $_REQUEST["Item_MinNumber"]
				, $_REQUEST["Item_MaxNumber"]
				, $_REQUEST["Item_MaxFileSize"]
				, $_REQUEST["Item_CreatingListType"]
				, $Item_AddAllItemsToList
				, $_REQUEST["Item_ListRelatedTable"]
				, $_REQUEST["Item_ListRelatedValueField"]
				, $_REQUEST["Item_ListRelatedDescriptionField"]
				, $_REQUEST["Item_ListRelatedDomainName"]
				, $_REQUEST["Item_ListQuery"]
				, $_REQUEST["Item_FieldInputType"]
				, $_REQUEST["Item_DefaultValue"]
				, $_REQUEST["Item_ValidFileExtensions"]
				, $Item_ShowInList
				, $_REQUEST["Item_ColumnOrder"]
				, $_REQUEST["Item_ColumnWidth"]
				, $_REQUEST["Item_ListShowType"]
				, $_REQUEST["Item_LookUpPageAddress"]
				, $_REQUEST["Item_OrderInInputForm"]
				, $_REQUEST["Item_ImageWidth"]
				, $_REQUEST["Item_ImageHeight"]
				, $_REQUEST["Item_FieldHint"]
				, $_REQUEST["Item_RelatedFileNameField"]
				, $_REQUEST["Item_HTMLEditor"]
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
	echo "<script>window.opener.document.location='ManageFormFields.php?FormsStructID=".$_REQUEST["Item_FormsStructID"]."'</script>";
}
$LoadDataJavascriptCode = '';
$ListRelatedDescription = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FormFields();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$Item_ListQuery = $obj->ListQuery;
	$LoadDataJavascriptCode .= "document.f1.Item_RelatedFieldName.value='".$obj->RelatedFieldName."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_FieldTitle.value='".$obj->FieldTitle."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_FieldType.value='".$obj->FieldType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_MaxLength.value='".$obj->MaxLength."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_InputWidth.value='".$obj->InputWidth."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_InputRows.value='".$obj->InputRows."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_MinNumber.value='".$obj->MinNumber."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_MaxNumber.value='".$obj->MaxNumber."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_MaxFileSize.value='".$obj->MaxFileSize."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_CreatingListType.value='".$obj->CreatingListType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ListRelatedTable.value='".$obj->ListRelatedTable."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ListRelatedValueField.value='".$obj->ListRelatedValueField."'; \r\n "; 
	
	//$LoadDataJavascriptCode .= "document.f1.Item_ListRelatedDescriptionField.value='".$obj->ListRelatedDescriptionField."';  \r\n ";
	$ListRelatedDescription = $obj->ListRelatedDescriptionField; 
	$LoadDataJavascriptCode .= "document.f1.Item_ListRelatedDomainName.value='".$obj->ListRelatedDomainName."'; \r\n "; 

	$LoadDataJavascriptCode .= "document.f1.Item_FieldInputType.value='".$obj->FieldInputType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_DefaultValue.value='".$obj->DefaultValue."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ValidFileExtensions.value='".$obj->ValidFileExtensions."'; \r\n ";
	if($obj->ShowInList=="YES") 
		$LoadDataJavascriptCode .= "document.f1.Item_ShowInList.checked=true; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.f1.Item_ShowInList.checked=false; \r\n ";
	if($obj->AddAllItemsToList=="YES") 
		$LoadDataJavascriptCode .= "document.f1.Item_AddAllItemsToList.checked=true; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.f1.Item_AddAllItemsToList.checked=false; \r\n ";
		
		
	$LoadDataJavascriptCode .= "document.f1.Item_ColumnOrder.value='".$obj->ColumnOrder."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ColumnWidth.value='".$obj->ColumnWidth."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ListShowType.value='".$obj->ListShowType."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_LookUpPageAddress.value=\"".$obj->LookUpPageAddress."\"; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_OrderInInputForm.value='".$obj->OrderInInputForm."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ImageWidth.value='".$obj->ImageWidth."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ImageHeight.value='".$obj->ImageHeight."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_FieldHint.value='".$obj->FieldHint."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_RelatedFileNameField.value='".$obj->RelatedFileNameField."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_HTMLEditor.value='".$obj->HTMLEditor."'; \r\n ";
}
else
{
	$LoadDataJavascriptCode .= "document.f1.Item_ColumnOrder.value='".(manage_FormFields::GetMaxColumnOrderNo($_REQUEST["Item_FormsStructID"])+1)."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ColumnWidth.value='10%'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_OrderInInputForm.value='".(manage_FormFields::GetMaxOrderInInputForm($_REQUEST["Item_FormsStructID"])+1)."'; \r\n "; 
}
$ParentObj = new be_FormsStruct();
$ParentObj->LoadDataFromDatabase($_REQUEST["Item_FormsStructID"]);

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
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش فیلدهای فرم</td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=Item_FormsStructID id=Item_FormsStructID value='<? echo $_REQUEST["Item_FormsStructID"]; ?>'>
<tr id=tr_RelatedFieldName name=tr_RelatedFieldName style='display:'>
<td width=15% nowrap>
	فیلد متناظر در جدول اطلاعاتی
</td>
<td nowrap>
	<span style="vertical-align:middle; font-size:12px;">
	<input type=text name=Item_RelatedFieldName id=Item_RelatedFieldName> 
	</span>
	<a href='SelectField.php?FormStructID=<? echo $_REQUEST["Item_FormsStructID"]; ?>&DBName=<?php echo $ParentObj->RelatedDB; ?>&TableName=<?php echo $ParentObj->RelatedTable; ?>' target=_blank>
	<img style="vertical-align:middle;" src='images/SelectField.gif' width=30 title='انتخاب' border=0 valign=buttom></a>
</td>
</tr>
<tr id=tr_FieldTitle name=tr_FieldTitle style='display:'>
<td width=15% nowrap>
	عنوان فیلد
</td>
<td nowrap>
	<input type=text name=Item_FieldTitle id=Item_FieldTitle size=60>
</td>
</tr>
<tr id=tr_FieldType name=tr_FieldType style='display:'>
<td width=15% nowrap>
	نوع	
</td>
<td nowrap>
	<select name=Item_FieldType id=Item_FieldType onchange='Deform();'>
	<?php echo FormUtils::CreateFormTypesOptions(0); ?>
	</select>
</td>
</tr>
<tr id=tr_FieldHint name=tr_FieldHint style='display:'>
<td width=15% nowrap>
	متن راهنما/توضیح جلوی فیلد
</td>
<td nowrap>
	<input type=text name=Item_FieldHint id=Item_FieldHint size=60 maxlength=500 value=''>
</td>
</tr>
<tr id=tr_MaxLength name=tr_MaxLength style='display:'>
<td width=15% nowrap>
	حداکثر طول داده مجاز
</td>
<td nowrap>
	<input type=text name=Item_MaxLength id=Item_MaxLength size=4 maxlength=4 value='255'>
</td>
</tr>
<tr id=tr_InputWidth name=tr_InputWidth style='display:'>
<td width=15% nowrap>
	طول جعبه ورود داده
</td>
<td nowrap>
	<input type=text name=Item_InputWidth id=Item_InputWidth size=4 maxlength=4 value='200'> پیکسل
</td>
</tr>
<tr id=tr_InputRows name=tr_InputRows style='display:'>
<td width=15% nowrap>
	ارتفاع جعبه ورود داده
</td>
<td nowrap>
	<input type=text name=Item_InputRows id=Item_InputRows size=4 maxlength=4 value='5'>
</td>
</tr>
<tr id=tr_Range name=tr_Range style='display:' size=6 maxlength=6>
<td width=15% nowrap>
	بازه مجاز داده 
</td>
<td>
از
<input type=text name=Item_MinNumber id=Item_MinNumber value='0' size=10> 
تا 
<input type=text name=Item_MaxNumber id=Item_MaxNumber value='6000000000' size=10>
</td>
</td>
</tr>
<tr id=tr_MaxFileSize name=tr_MaxFileSize style='display:'>
<td width=15% nowrap>
	حداکثر حجم مجاز برای فایل
</td>
<td nowrap>
	<input type=text name=Item_MaxFileSize id=Item_MaxFileSize maxlength=6 size=6> کیلوبایت
</td>
</tr>
<tr id=tr_CreatingListType name=tr_CreatingListType style='display:'>
<td width=15% nowrap>
	نحوه تهیه آیتمهای لیست
</td>
<td nowrap>
	<span style="vertical-align:middle; font-size:12px;">
	<select name=Item_CreatingListType id=Item_CreatingListType onchange='javascript: ChangeCreateList();'>
	<option value='STATIC_LIST'>لیست ثابت
	<option value='RELATED_TABLE'>از یک جدول دیگر
	<option value='QUERY'>با اجرای پرس و جو
	<option value='DOMAINS'>از جدول Domains
	</select>
	</span>
	<span name='CreateListSpan' id='CreateListSpan' style="vertical-align:middle;"> 
	<a href='CreateSelectOptions.php' target=_blank>
	<img style="vertical-align:middle;" src='images/list.gif' width=25 title='تعیین آیتمها' border=0></a>
	</span>
</td>
</tr>
<tr id=tr_AddAllItemsToList name=tr_AddAllItemsToList style='display: '>
	<td colspan=2>
	<input type=checkbox name=Item_AddAllItemsToList id=Item_AddAllItemsToList> آیتمی با مقدار صفر در ابتدای لیست اضافه شود ( به عنوان همه مقادیر یا هیچکدام ) 
	</td>
</tr>
<tr id=tr_ListRelatedTable name=tr_ListRelatedTable style='display:'>
<td width=15% nowrap>
	نام جدول برای تولید لیست
</td>
<td nowrap>
	<input type=text name=Item_ListRelatedTable id=Item_ListRelatedTable>
</td>
</tr>
<tr id=tr_ListRelatedValueField name=tr_ListRelatedValueField style='display:'>
<td width=15% nowrap>
	نام فیلد معادل مقدار
</td>
<td nowrap>
	<input type=text name=Item_ListRelatedValueField id=Item_ListRelatedValueField>
</td>
</tr>
<tr id=tr_ListRelatedDescriptionField name=tr_ListRelatedDescriptionField style='display:'>
<td width=15% nowrap>
	نام فیلد معادل شرح 
</td>
<td nowrap>
	<input type=text name=Item_ListRelatedDescriptionField id=Item_ListRelatedDescriptionField value="<?php echo $ListRelatedDescription; ?>">
</td>
</tr>
<tr id=tr_ListRelatedDomainName name=tr_ListRelatedDomainName style='display:'>
<td width=15% nowrap>
	نام کلید در Domains 
</td>
<td nowrap>
	<span style="vertical-align:middle;">
	<select dir=ltr name=Item_ListRelatedDomainName id=Item_ListRelatedDomainName>
		<?php
			$res = $mysql->Execute("select distinct DomainName from domains order by DomainName");
			while($rec=$res->FetchRow())
			{
				echo "<option value='".$rec["DomainName"]."'>".$rec["DomainName"]; 
			}
		?>
	</select> 
	</span>
	<a href=# onclick='javascript: window.open("ShowDomainValues.php?DomainName="+document.f1.Item_ListRelatedDomainName.value);' >
	<img style="vertical-align:middle;" src='images/list2.gif' width=30 title='مشاهده لیست آیتمها' border=0 valign=buttom></a>
</td>
</tr>
<tr id=tr_ListQuery name=tr_ListQuery style='display:'>
<td width=15% nowrap>
	query ساخت لیست
</td>
<td nowrap>
	<textarea dir=ltr name=Item_ListQuery id=Item_ListQuery rows=5 cols=60><?php echo $Item_ListQuery; ?></textarea>
</td>
</tr>
<tr id=tr_FieldInputType name=tr_FieldInputType style='display:'>
<td width=15% nowrap>
	نحوه ورود داده
</td>
<td nowrap>
	<select name=Item_FieldInputType id=Item_FieldInputType>
	<option value='OPTIONAL'>اختیاری
	<option value='MANDATORY'>اجباری
	</select>
</td>
</tr>
<tr id=tr_DefaultValue name=tr_DefaultValue style='display:'>
<td width=15% nowrap>
	مقدار پیش فرض
</td>
<td nowrap>
	<span style="vertical-align:middle;">
	<input type=text name=Item_DefaultValue id=Item_DefaultValue>
	</span>
	<a  href='SelectDefaultKey.php' target=_blank><img style="vertical-align:middle;" src='images/SelectItem.gif' width=30 title='انتخاب کلید' border=0 ></a>
</td>
</tr>
<tr id=tr_ValidFileName name=tr_ValidFileName style='display:'>
<td width=15% nowrap>
	فیلد متناظر برای نگهداری نام فایل
</td>
<td nowrap>
	<span style="vertical-align:middle; font-size:12px;">
	<input type=text name=Item_RelatedFileNameField id=Item_RelatedFileNameField> 
	</span>
	<a href='SelectFieldForFileName.php?FormStructID=<? echo $_REQUEST["Item_FormsStructID"]; ?>&DBName=<?php echo $ParentObj->RelatedDB; ?>&TableName=<?php echo $ParentObj->RelatedTable; ?>' target=_blank>
	<img style="vertical-align:middle;" src='images/SelectField.gif' width=30 title='انتخاب' border=0 valign=buttom></a>
	

</td>
</tr>

<tr id=tr_ValidFileExtensions name=tr_ValidFileExtensions style='display:'>
<td width=15% nowrap>
	پسوندهای مجاز فایل
</td>
<td nowrap>
	<input type=text name=Item_ValidFileExtensions id=Item_ValidFileExtensions>
</td>
</tr>
<tr id=tr_ShowInList name=tr_ShowInList style='display:'>
<td width=15% nowrap colspan=2>
	<input type=checkbox name=Item_ShowInList id=Item_ShowInList checked>
	به عنوان یک ستون در لیست نمایش داده شود
</td>
</tr>
<tr id=tr_ColumnOrder name=tr_ColumnOrder style='display:'>
<td width=15% nowrap>
	شماره ترتیب ستون در لیست
</td>
<td nowrap>
	<input type=text name=Item_ColumnOrder id=Item_ColumnOrder size=2 maxlength=2>
</td>
</tr>
<tr id=tr_ColumnWidth name=tr_ColumnWidth style='display:'>
<td width=15% nowrap>
	عرض ستون در لیست
</td>
<td nowrap>
	<input type=text name=Item_ColumnWidth id=Item_ColumnWidth size=3 maxlength=4>
</td>
</tr>
<tr id=tr_ListShowType name=tr_ListShowType style='display:'>
<td width=15% nowrap>
	نحوه نمایش
</td>
<td nowrap>
	<select name=Item_ListShowType id=Item_ListShowType  onchange='javascript: ChangeShowTypeList();'>
		<option value='COMBOBOX'>لیست کشویی
		<option value='LOOKUP'>Look Up
	</select>
</td>
</tr>
<tr id=tr_TextShowType name=tr_TextShowType style='display:'>
<td width=15% nowrap>
	نحوه نمایش
</td>
<td nowrap>
	<select name=Item_HTMLEditor id=Item_HTMLEditor >
		<option value='NO'>عادی
		<option value='YES'>HTML Editor
	</select>
</td>
</tr>
<tr id=tr_LookUpPageAddress name=tr_LookUpPageAddress style='display:'>
<td width=15% nowrap>
	آدرس صفحه جستجوی داده
</td>
<td nowrap>
	<input type=text name=Item_LookUpPageAddress id=Item_LookUpPageAddress dir=ltr size=60>
	<a href='LookUpPageHelp.php' target=_blank>
		<img src='../rcssimgs/question.gif' title='راهنما'>
	</a>
</td>
</tr>
<tr id=tr_OrderInInputForm name=tr_OrderInInputForm style='display:'>
<td width=15% nowrap>
	شماره ترتیب در صفحه ورود داده
</td>
<td nowrap>
	<input type=text name=Item_OrderInInputForm id=Item_OrderInInputForm size=2 maxlength=2>
</td>
</tr>
<tr id=tr_ImageWidth name=tr_ImageWidth style='display:'>
<td width=15% nowrap>
	عرض تصویر
</td>
<td nowrap>
	<input type=text name=Item_ImageWidth id=Item_ImageWidth size=3 maxlength=4> پیکسل 
</td>
</tr>
<tr id=tr_ImageHeight name=tr_ImageHeight style='display:'>
<td width=15% nowrap>
	ارتفاع تصویر
</td>
<td nowrap>
	<input type=text name=Item_ImageHeight id=Item_ImageHeight size=3 maxlength=4> پیکسل
</td>
</tr>
</table>
</td></tr>
<tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'></td></tr>
</table>
<table width=90% align=center border=0>
<tr id=tr_info1 name=tr_info1 style='display:none'>
<td>
<img style="vertical-align:middle;" src='images/info.gif' width=35>
<span style="vertical-align:middle; font-size:12px;"> فیلد مربوطه باید به صورت ENUM تعریف شده باشد و مقدار 'YES' برای حالت تیک خورده و 'NO' برای حالت تیک نخورده ذخیره خواهد شد.</span></td>
</td>
</tr>
<tr id=tr_info2 name=tr_info2 style='display:none'>
<td>
<img style="vertical-align:middle;" src='images/info.gif' width=35>
<span style="vertical-align:middle; font-size:12px;"> نتیجه query بایستی شامل دو ستون باشد که ستون اول به عنوان مقدار و ستون دوم به عنوان شرح در لیست آیتمها مورد استفاده قرار می گیرد.</span></td>
</td>
</tr>
<tr id=tr_info3 name=tr_info3 style='display:none'>
<td>
<img style="vertical-align:middle;" src='images/info.gif' width=35>
<span style="vertical-align:middle; font-size:12px;">از فیلد متناظر در بانک اطلاعاتی برای ذخیره سازی محتویات فایل/تصویر استفاده می شود بنابراین باید از نوع BLOB باشد
<br>
برای ذخیره سازی نام فایل می توانید فیلد متناظر آن را تعیین نمایید تا نام فایل در آن ذخیره شود. تعیین این گزینه اختیاری است
</span></td>
</td>
</tr>

</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	Deform();
	ChangeCreateList();
	ChangeShowTypeList();
	function ValidateForm()
	{
		document.f1.submit();
	}
	function Show(FieldName)
	{
		document.getElementById(FieldName).style.display='';
	}
	function Hide(FieldName)
	{
		document.getElementById(FieldName).style.display='none';
	}
	
	function DeformAccordingListType()
	{
		if(document.f1.Item_FieldType.value!='3')
			return;
		Show('tr_ListRelatedTable');
		Show('tr_ListRelatedValueField');
		Show('tr_ListRelatedDescriptionField');
		Show('tr_ListRelatedDomainName');
		Show('tr_ListQuery');
		
		var TypeName = document.f1.Item_CreatingListType.value;
		if(TypeName=='STATIC_LIST')
		{
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
		}
		else if(TypeName=='RELATED_TABLE')
		{
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
		}
		else if(TypeName=='QUERY')
		{
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Show('tr_info2');
		}
		else if(TypeName=='DOMAINS')
		{
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListQuery');
		}
	}
	
	function ChangeShowTypeList()
	{
		if(document.f1.Item_ListShowType.value=='LOOKUP')
			Show('tr_LookUpPageAddress');
		else
			Hide('tr_LookUpPageAddress');
	}
	
	function ChangeCreateList()
	{
		var Name = document.f1.Item_CreatingListType.value;
		if(Name=='STATIC_LIST')
		{
			document.getElementById('CreateListSpan').innerHTML="<a href='CreateSelectOptions.php?FormFieldID=<?php echo $FormFieldID; ?>' target=_blank><img style=\"vertical-align:middle;\" src='images/list.gif' width=30 title='تعیین آیتمها' border=0 valign=buttom></a>";
		}
		else
		{
			document.getElementById('CreateListSpan').innerHTML="";
		}
		DeformAccordingListType();
	}
	
	function Deform()
	{
		var TypeName = document.f1.Item_FieldType.value;

		Show('tr_MaxLength');
		Show('tr_InputWidth');
		Show('tr_InputRows');
		Show('tr_Range');
		Show('tr_MaxFileSize');
		Show('tr_CreatingListType');
		Show('tr_AddAllItemsToList');
		Show('tr_ListRelatedTable');
		Show('tr_ListRelatedValueField');
		Show('tr_ListRelatedDescriptionField');
		Show('tr_ListRelatedDomainName');
		Show('tr_ListQuery');
		Show('tr_FieldInputType');
		Show('tr_DefaultValue');
		Show('tr_ValidFileExtensions');
		Show('tr_ValidFileName');
		Show('tr_ShowInList');
		Show('tr_ColumnOrder');
		Show('tr_ColumnWidth');
		Show('tr_ListShowType');
		//Show('tr_LookUpPageAddress');
		Show('tr_OrderInInputForm');
		Show('tr_ImageWidth');
		Show('tr_ImageHeight');
		Show('tr_TextShowType');
		
		Hide('tr_info1');
		Hide('tr_info2');
		Hide('tr_info3');
		if(TypeName=="1") // متنی یک خطی
		{
			Hide('tr_InputRows');
			Hide('tr_Range');
			Hide('tr_MaxFileSize');
			Hide('tr_CreatingListType');
			Hide('tr_AddAllItemsToList');
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
			Hide('tr_ValidFileExtensions');
			Hide('tr_ValidFileName');
			Hide('tr_ListShowType');
			Hide('tr_LookUpPageAddress');
			Hide('tr_ImageWidth');
			Hide('tr_ImageHeight');
			Hide('tr_TextShowType');
		}
		else if(TypeName=="2") // متنی چند خطی
		{
			Hide('tr_MaxLength');
			Hide('tr_Range');
			Hide('tr_MaxFileSize');
			Hide('tr_CreatingListType');
			Hide('tr_AddAllItemsToList');
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
			Hide('tr_ValidFileExtensions');
			Hide('tr_ValidFileName');
			Hide('tr_ListShowType');
			Hide('tr_LookUpPageAddress');
			Hide('tr_ImageWidth');
			Hide('tr_ImageHeight');
			
		}
		else if(TypeName=="3") // لیستی
		{
			Hide('tr_InputRows');
			Hide('tr_MaxLength');
			Hide('tr_Range');
			Hide('tr_MaxFileSize');
			Hide('tr_ValidFileExtensions');
			Hide('tr_ValidFileName');
			Hide('tr_ImageWidth');
			Hide('tr_ImageHeight');
			Hide('tr_TextShowType');
		}
		else if(TypeName=="4") // عددی
		{
			Hide('tr_InputRows');
			Hide('tr_MaxFileSize');
			Hide('tr_CreatingListType');
			Hide('tr_AddAllItemsToList');
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
			Hide('tr_ValidFileExtensions');
			Hide('tr_ValidFileName');
			Hide('tr_ListShowType');
			Hide('tr_LookUpPageAddress');
			Hide('tr_ImageWidth');
			Hide('tr_ImageHeight');
			Hide('tr_TextShowType');
		}
		else if(TypeName=="5") // فایل
		{
			Hide('tr_InputWidth');
			Hide('tr_InputRows');
			Hide('tr_MaxLength');
			Hide('tr_Range');
			Hide('tr_InputRows');
			Hide('tr_CreatingListType');
			Hide('tr_AddAllItemsToList');
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
			Hide('tr_ListShowType');
			Hide('tr_LookUpPageAddress');
			Hide('tr_ImageWidth');
			Hide('tr_ImageHeight');
			Hide('tr_DefaultValue');
			Hide('tr_ColumnWidth');
			Show('tr_info3');
			Hide('tr_TextShowType');
		}
		else if(TypeName=="6") // تصویر
		{
			Hide('tr_InputWidth');
			Hide('tr_InputRows');
			Hide('tr_MaxLength');
			Hide('tr_Range');
			Hide('tr_InputRows');
			Hide('tr_CreatingListType');
			Hide('tr_AddAllItemsToList');
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
			Hide('tr_ListShowType');
			Hide('tr_LookUpPageAddress');
			Hide('tr_DefaultValue');
			Show('tr_info3');
			Hide('tr_TextShowType');
		}
		else if(TypeName=="7") // تاریخ شمسی
		{
			Hide('tr_MaxLength');
			Hide('tr_InputWidth');
			Hide('tr_InputRows');
			Hide('tr_Range');
			Hide('tr_MaxFileSize');
			Hide('tr_CreatingListType');
			Hide('tr_AddAllItemsToList');
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
			Hide('tr_ValidFileExtensions');
			Hide('tr_ValidFileName');
			Hide('tr_ListShowType');
			Hide('tr_ImageWidth');
			Hide('tr_ImageHeight');
			Hide('tr_TextShowType');
		}
		else if(TypeName=="8") // Checkbox
		{
			Hide('tr_MaxLength');
			Hide('tr_InputWidth');
			Hide('tr_InputRows');
			Hide('tr_Range');
			Hide('tr_MaxFileSize');
			Hide('tr_CreatingListType');
			Hide('tr_AddAllItemsToList');
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
			Hide('tr_FieldInputType');
			Hide('tr_ValidFileExtensions');
			Hide('tr_ValidFileName');
			Hide('tr_ListShowType');
			Hide('tr_ImageWidth');
			Hide('tr_ImageHeight');		
			Show('tr_info1');
			Hide('tr_TextShowType');
		}
		else if(TypeName=="9") // متغیر مخفی
		{
			Hide('tr_MaxLength');
			Hide('tr_InputWidth');
			Hide('tr_InputRows');
			Hide('tr_Range');
			Hide('tr_MaxFileSize');
			Hide('tr_CreatingListType');
			Hide('tr_AddAllItemsToList');
			Hide('tr_ListRelatedTable');
			Hide('tr_ListRelatedValueField');
			Hide('tr_ListRelatedDescriptionField');
			Hide('tr_ListRelatedDomainName');
			Hide('tr_ListQuery');
			Hide('tr_ValidFileExtensions');
			Hide('tr_ValidFileName');
			Hide('tr_ListShowType');
			Hide('tr_FieldInputType');
			Hide('tr_ImageWidth');
			Hide('tr_ImageHeight');
			Hide('tr_OrderInInputForm');
			Hide('tr_TextShowType');
		}
		
		DeformAccordingListType();
	}
</script>
