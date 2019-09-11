<?
class be_FormFields
{
	public $FormFieldID;		//
	public $FormsStructID;		//کد فرم مربوطه
	public $RelatedFieldName;		//فیلد متناظر در جدول اطلاعاتی
	public $FieldTitle;		//عنوان فیلد
	public $FieldType;		//نوع فیلد کلید (کلید خارجی به جدول انواع فیلد)
	public $MaxLength;		//حداکثر طول داده مجاز
	public $InputWidth;		//طول جعبه ورود داده
	public $InputRows;		//ارتفاع جعبه ورود داده (مخصوص فیلدهای چند خطی)
	public $MinNumber;		//شروع بازه مجاز داده های عددی
	public $MaxNumber;		//انتهای بازه مجاز داده های عددی
	public $MaxFileSize;		//حداکثر حجم مجاز برای فایل
	public $CreatingListType;		//نحوه ساخت لیست برای فیلدهای لیستی
	public $AddAllItemsToList;		// آیا آیتمی به نام همه مقادیر در لیست آیتمها قرار داده شود؟ (با کد صفر)
	public $ListRelatedTable;		//نام جدول برای تولید لیست آیتمها
	public $ListRelatedValueField;		//نام فیلد معادل مقدار در تولید لیست
	public $ListRelatedDescriptionField;		//نام فیلد معدل شرح در تولید لیست
	public $ListRelatedDomainName;		//DomainName مربوطه در زمانیکه لیست از روی جدول domains ساخته می شود
	public $ListQuery;		//query ساخت لیست
	public $FieldInputType;		//نوع ورود داده به فیلد (اجباری/اختیاری)
	public $DefaultValue;		//مقدار پیش فرض
	public $ValidFileExtensions;		//پسوندهای مجاز فابل
	public $ShowInList;		//در لیست به عنوان یک ستون نمایش داده شود؟
	public $ColumnOrder;		//شماره ترتیب ستون در لیست
	public $ColumnWidth;		//عرض ستون در لیست
	public $ListShowType;		//نحوه نمایش فیلد (فیلدهای لیستی)
	public $LookUpPageAddress;		//آدرس صفحه مربوط به جستجوی داده (در فیلدهای لیستی)
	public $OrderInInputForm;		//شماره ترتیب در صفحه ورود داده
	public $ImageWidth;		//عرض تصویر
	public $ImageHeight;		//ارتفاع تصویر
	public $FieldHint; 			// متن راهنمای جلوی فیلد
	public $RelatedFileNameField; //نام فیلد برای نگهداری اسم فایل - مخصوص فیلدهای نوع فایل و تصویر
	public $HTMLEditor; // آیا ویرایشگر اچ تی ام ال روی متنی چند خطی فعال شود
	public $FormsSectionID; // کد بخش

	public $ShowSlider; // آیا فیلد به شکل اسلاید بار نشان داده شود - برای فیلدهای عددی
	public $SliderLength; // طول اسلایدر
	public $SliderStartLabel; // برچسب ابتدای اسلایدر
	public $SliderEndLabel; // برچسب آخر اسلایدر
	
	public $FieldTypeName; // نام نوع فیلد که بر اساس کد آن استخراج می شود
	
	function be_FormFields() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("select * from FormFields
									LEFT JOIN FieldTypes on (FieldTypes.FieldTypeID=FormFields.FieldType) 
									where FormFieldID='".$RecID."' ");
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			$this->FormFieldID=$rec["FormFieldID"];
			$this->FormsStructID=$rec["FormsStructID"];
			$this->RelatedFieldName=$rec["RelatedFieldName"];
			$this->FieldTitle=$rec["FieldTitle"];
			$this->FieldType=$rec["FieldType"];
			$this->MaxLength=$rec["MaxLength"];
			$this->InputWidth=$rec["InputWidth"];
			$this->InputRows=$rec["InputRows"];
			$this->MinNumber=$rec["MinNumber"];
			$this->MaxNumber=$rec["MaxNumber"];
			$this->MaxFileSize=$rec["MaxFileSize"];
			$this->CreatingListType=$rec["CreatingListType"];
			$this->AddAllItemsToList=$rec["AddAllItemsToList"];
			$this->ListRelatedTable=$rec["ListRelatedTable"];
			$this->ListRelatedValueField=$rec["ListRelatedValueField"];
			$this->ListRelatedDescriptionField=$rec["ListRelatedDescriptionField"];
			$this->ListRelatedDomainName=$rec["ListRelatedDomainName"];
			$this->ListQuery=$rec["ListQuery"];
			$this->FieldInputType=$rec["FieldInputType"];
			$this->DefaultValue=$rec["DefaultValue"];
			$this->ValidFileExtensions=$rec["ValidFileExtensions"];
			$this->ShowInList=$rec["ShowInList"];
			$this->ColumnOrder=$rec["ColumnOrder"];
			$this->ColumnWidth=$rec["ColumnWidth"];
			$this->ListShowType=$rec["ListShowType"];
			$this->LookUpPageAddress=$rec["LookUpPageAddress"];
			$this->OrderInInputForm=$rec["OrderInInputForm"];
			$this->ImageWidth=$rec["ImageWidth"];
			$this->ImageHeight=$rec["ImageHeight"];
			$this->FieldTypeName = $rec["TypeName"];
			$this->FieldHint = $rec["FieldHint"];
			$this->RelatedFileNameField=$rec["RelatedFileNameField"];
			$this->HTMLEditor=$rec["HTMLEditor"];
			$this->FormsSectionID=$rec["FormsSectionID"];
			
			$this->ShowSlider=$rec["ShowSlider"];
			$this->SliderLength=$rec["SliderLength"];
			$this->SliderStartLabel=$rec["SliderStartLabel"];
			$this->SliderEndLabel=$rec["SliderEndLabel"];
		}
	}
	
	
	// کد مربوط به رابط کاربری فیلد را بر می گرداند
	// AccessType: نوع دسترسی کاربر استفاده کننده را مشخص می کند که یکی از موارد زیر است:
	// READ_ONLY, HIDE, EDITABLE
	// FieldValue: مقدار فیلد را مشخص می کند که برای فیلدهای لیستی همان کد گزینه می باشد
	// در صورتیکه نوع فیلد تصویر یا فایل باشد نام فایل یا تصویر ثبت شده در دیتابیس در این فیلد باید پاس شود چنانچه نام فایل در دیتابیس نگهداری نمی شد این متغیر خالی خواهد بود
	// RecID: شماره رکورد جاری - برای فیلدهای نوع تصویر و فایل کاربرد دارد که باید این مقدار به صفحه نمایش دهنده تصویر یا دریافت کننده فایل پاس شود
	function CreateUserInterface($AccessType, $FieldValue, $RecID)
	{
		if(class_exists('FormUtils') != true)
			require_once("FormUtils.class.php");
		$ret = "";
		//1: متنی یک خطی
		//۲: متنی چند خطی
		//۳: لیست
		//۴: عدد
		//۵: فایل
		//۶: تصویر
		//۷: تاریخ شمسی
		//۸: این نوع فیلدها دو مقدار بلی و خیر دارند - گزینه دو انتخابی
		//۹: متغیر سیستمی
		if($AccessType=="HIDE")
			return "";
		if($AccessType=="READ_ONLY")
		{
			if($this->FieldType=="3") // لیستی
			{
				if($this->CreatingListType=="STATIC_LIST")
					$ret .= manage_FieldsItemList::GetItemDescriptionInOptionList($this->FormFieldID, $FieldValue);
				else if($this->CreatingListType=="RELATED_TABLE")
					$ret .= FormUtils::CreateItemDescriptionFromATable($this->ListRelatedTable, $this->ListRelatedValueField, $this->ListRelatedDescriptionField, $FieldValue);
				else if($this->CreatingListType=="QUERY")
					$ret .= FormUtils::CreateItemDescriptionAccordingToQuery($this->ListQuery, $FieldValue);
				else if($this->CreatingListType=="DOMAINS")
					$ret .= FormUtils::CreateItemDescriptionAccordingToDomainName($this->ListRelatedDomainName, $FieldValue);
			}
			else if($this->FieldType=="5") // فایل
			{
				if($FieldValue=="")
					$FieldValue = "Download.dat";
				if($RecID!="0")
					$ret .= "<a href='DownloadFile.php?FormsStructID=".$this->FormsStructID."&FieldName=".$this->RelatedFieldName."&RecID=".$RecID."&DownloadFileName=".$FieldValue."'>دریافت فایل</a>";
			}
			else if($this->FieldType=="6") // تصویر
			{
				if($FieldValue=="")
						$FieldValue = "Download.jpg";
				$ret .= "<img src='ShowPic.php?FormsStructID=".$this->FormsStructID."&FieldName=".$this->RelatedFieldName."&RecID=".$RecID."&DownloadFileName=".$FieldValue."' ";
				if($this->ImageWidth!="0" && $this->ImageWidth!="")
					$ret .= " width='".$this->ImageWidth."' ";
				if($this->ImageHeight!="0" && $this->ImageHeight!="")
					$ret .= " height='".$this->ImageHeight."' ";
					$ret .= ">";
			}
			else if($this->FieldType=="8") // دو گزینه ای
			{
				if(strtoupper($FieldValue)=="YES")
					$ret .= "<img src='images/tick.jpg'>";
				else
					$ret .= "&nbsp;";
			}
			else	// سایر انواع
				$ret .= htmlspecialchars($FieldValue);
			$ret .= " ".$this->FieldHint;
		}
		else if($AccessType=="EDITABLE")
		{
			if($this->FieldType=="1" || $this->FieldType=="4") // یا عددی متنی یک خطی
			{
				/*
				 <input class="dhtmlxSlider" type="text" name="slider1" style="width:200px" value="10"><br>
				<input class="dhtmlxSlider" skin="ball" min="10" max="20" step="0.5" type="text" name="slider2" style="width:200px" value="20"><br>
				 */
				if($this->FieldType=="4" && $this->ShowSlider=="YES") // اگر فیلد عددی بود و نوع نمایش اسلاید بار باشد
				{
					$ret = "<table><tr>";
					$ret .= "<td><input type=text name='FIELD_".$this->RelatedFieldName."' id='FIELD_".$this->RelatedFieldName."'   size=2></td>";					
					$ret .= "<td nowrap>";
					$ret .= " ".$this->SliderEndLabel." &nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
					/*
					$ret .= "<input class=\"dhtmlxSlider\" type=text name='FIELD_".$this->RelatedFieldName."' id='FIELD_".$this->RelatedFieldName."' ";
					$ret .= " value='".$FieldValue."' ";
					$ret .= " maxlength='".$this->MaxLength."' ";
					$ret .= " style=\"width: ".$this->SliderLength."px; \" ";
					$ret .= " skin=\"arrow\" min=\"".$this->MinNumber."\" max=\"".$this->MaxNumber."\" step=\"1\" ";
					$ret .= " onchange='javascript: document.getElementById(\"ShowValue_".$this->FormFieldID."\").value=this.value'> ";
					*/
					if($FieldValue=="")
						$FieldValue = 0;
					$ret .= "<script>\r\n";
					$ret .= "var sld = new dhtmlxSlider(null, {\r\n";
					$ret .= "									size: ".$this->SliderLength.",\r\n";
					$ret .= "									skin: \"arrow\",\r\n";
					$ret .= "									step: 1,\r\n";
					$ret .= "									min: ".$this->MinNumber.",\r\n";
					$ret .= "									max: ".$this->MaxNumber.",\r\n";
					$ret .= "									value: ".$FieldValue."\r\n";
					$ret .= "});\r\n";
					$ret .= "sld.linkTo('FIELD_".$this->RelatedFieldName."');\r\n \r\n";
					$ret .= "sld.setMin(".$this->MinNumber.");\r\n";
					$ret .= "sld.setMax(".$this->MaxNumber.");\r\n";
					$ret .= "sld.init();\r\n";
					$ret .= "</script>\r\n";
      				$ret .= "</td>";
					$ret .= "<td nowrap> ".$this->SliderStartLabel." </td>";
					$ret .= "</tr></table>";					
					
      /*    //or
      var sld = new dhtmlxSlider(null, {
              size:100,           
              skin: "ball",
              vertical:false,
              step:1,
              min:1,
              max:100,
              value:50           
          });
	   */			

					
				}
				else
				{
					$ret = "<input type=text name='FIELD_".$this->RelatedFieldName."' id='FIELD_".$this->RelatedFieldName."' ";
					$ret .= " value='".$FieldValue."' ";
					$ret .= " maxlength='".$this->MaxLength."' ";
					$ret .= " style=\"width: ".$this->InputWidth."px\" ";
					$ret .= ">";
				}
			}
			else if($this->FieldType=="2") // متنی چند خطی
			{
				if($this->HTMLEditor=="NO")
				{
					$ret = "<textarea name='FIELD_".$this->RelatedFieldName."' id='FIELD_".$this->RelatedFieldName."' ";
					$ret .= " style=\"width: ".$this->InputWidth."px\" ";
					$ret .= " rows='".$this->InputRows."' ";
					$ret .= ">";
					$ret .= $FieldValue;
					$ret .= "</textarea>";
				}
				else
				{
				
					$ret .= "<div id='DIV_".$this->RelatedFieldName."'></div>";
				}
			}
			else if($this->FieldType=="3") //لیستی 
			{
				if($this->ListShowType=="COMBOBOX")
				{
					$ret .= "<select name='FIELD_".$this->RelatedFieldName."' id='FIELD_".$this->RelatedFieldName."' ";
					$ret .= " style=\"width: ".$this->InputWidth."px\" ";
					$ret .= ">";
					if($this->AddAllItemsToList=="YES")
						$ret .= "<option value=0>-";
					if($this->CreatingListType=="STATIC_LIST")
						$ret .= manage_FieldsItemList::GetItemsInOptionList($this->FormFieldID, $FieldValue);
					else if($this->CreatingListType=="RELATED_TABLE")
						$ret .= FormUtils::CreateItemsListFromATable($this->ListRelatedTable, $this->ListRelatedValueField, $this->ListRelatedDescriptionField, $FieldValue);
					else if($this->CreatingListType=="QUERY")
						$ret .= FormUtils::CreateItemsListAccordingToQuery($this->ListQuery, $FieldValue);
					else if($this->CreatingListType=="DOMAINS")
						$ret .= FormUtils::CreateItemsListAccordingToDomainName($this->ListRelatedDomainName, $FieldValue);
					$ret .= "</select>";
				}
				else if($this->ListShowType=="RADIO")
				{
					if($this->CreatingListType=="STATIC_LIST")
						$ret .= manage_FieldsItemList::GetItemsInRadioList($this->FormFieldID, $FieldValue, "FIELD_".$this->RelatedFieldName);
					/*
					else if($this->CreatingListType=="RELATED_TABLE")
						$ret .= FormUtils::CreateItemsListFromATable($this->ListRelatedTable, $this->ListRelatedValueField, $this->ListRelatedDescriptionField, $FieldValue);
					else if($this->CreatingListType=="QUERY")
						$ret .= FormUtils::CreateItemsListAccordingToQuery($this->ListQuery, $FieldValue);
					else if($this->CreatingListType=="DOMAINS")
						$ret .= FormUtils::CreateItemsListAccordingToDomainName($this->ListRelatedDomainName, $FieldValue);
					$ret .= "</select>";
					*/
				}
				else // در صورتیکه نوع نمایش لیست به صورت LookUp باشد
				{
					$ret .= "<span name=SPAN_".$this->RelatedFieldName." id=SPAN_".$this->RelatedFieldName.">";
					if($this->CreatingListType=="STATIC_LIST")
						$ret .= manage_FieldsItemList::GetItemDescriptionInOptionList($this->FormFieldID, $FieldValue);
					else if($this->CreatingListType=="RELATED_TABLE")
						$ret .= FormUtils::CreateItemDescriptionFromATable($this->ListRelatedTable, $this->ListRelatedValueField, $this->ListRelatedDescriptionField, $FieldValue);
					else if($this->CreatingListType=="QUERY")
						$ret .= FormUtils::CreateItemDescriptionAccordingToQuery($this->ListQuery, $FieldValue);
					else if($this->CreatingListType=="DOMAINS")
						$ret .= FormUtils::CreateItemDescriptionAccordingToDomainName($this->ListRelatedDomainName, $FieldValue);
					$ret .= "</span>";
					$ret .= "&nbsp;<a href='".$this->LookUpPageAddress."?FormName=f1&InputName=FIELD_".$this->RelatedFieldName."&SpanName=SPAN_".$this->RelatedFieldName."' target=_blank>[انتخاب]</a>";
					$ret .= "<input type=hidden name='FIELD_".$this->RelatedFieldName."' id='FIELD_".$this->RelatedFieldName."' >";
				}
			}
			else  if($this->FieldType=="5" || $this->FieldType=="6") //فایل
			{
				$ret .= "<input type=file name='FIELD_".$this->RelatedFieldName."' id='FIELD_".$this->RelatedFieldName."'>";
				$ret .= "&nbsp;";
				if($this->FieldType=="6")
				{
					if($FieldValue=="")
						$FieldValue = "Download.jpg";
					$ret .= "<br><img src='ShowPic.php?FormsStructID=".$this->FormsStructID."&FieldName=".$this->RelatedFieldName."&RecID=".$RecID."&DownloadFileName=".$FieldValue."' ";
					if($this->ImageWidth!="0" && $this->ImageWidth!="")
						$ret .= " width='".$this->ImageWidth."' ";
					if($this->ImageHeight!="0" && $this->ImageHeight!="")
						$ret .= " height='".$this->ImageHeight."' ";
						$ret .= ">";
				}
				else
				{
					if($FieldValue=="")
						$FieldValue = "Download.dat";
					if($RecID!="0")
						$ret .= "<a href='DownloadFile.php?FormsStructID=".$this->FormsStructID."&FieldName=".$this->RelatedFieldName."&RecID=".$RecID."&DownloadFileName=".$FieldValue."'>دریافت فایل</a>";
				}
			}
			else if($this->FieldType=="7") // تاریخ شمسی 
			{
				// فرض می کنیم در متغیر مقدار فیلد تاریخ به شکل تاریخ شمسی قرار گرفته است
				$ret .= "<span style='vertical-align:middle; font-size:12px;'>";
				$ret .= "<input dir=ltr type=text maxlength=10 size=8 name='FIELD_".$this->RelatedFieldName."' id='FIELD_".$this->RelatedFieldName."' value='".$FieldValue."'>";
				$ret .= "</span>";
				$ret .= "&nbsp;<span style='vertical-align:middle;' > ";
				$ret .= "<a href=# onclick='javascript: window.open(\"calendar.php?FormName=f1&InputName=FIELD_".$this->RelatedFieldName."\")'><img title='انتخاب از تقویم' width=20 src='images/calendar.jpg' border=0></a>";
				$ret .= "</span>";
				$ret .= "&nbsp; (روز/ماه/سال دو رقم)";
			}
			else if($this->FieldType=="8") // آیتم دو انتخابی
			{
				$ret .= "<input type=checkbox name='FIELD_".$this->RelatedFieldName."' id='FIELD_".$this->RelatedFieldName."' ";
				if(strtoupper($FieldValue)=="YES")
					$ret .= " checked ";
				$ret .= ">";
			}
			else if($this->FieldType=="9")
			{
				$ret .= $FieldValue;
			}
		}
		//$ret .= "*".$this->FieldHint;
		return $ret;
	}

	// کد جاوا اسکریپت برای کنترل صحت داده ورودی به این فیلد را بر می گرداند
	function CreateCheckvalidityJavascriptCode()
	{
		$ret = "";
		//1: متنی یک خطی
		//۲: متنی چند خطی
		//۳: لیست
		//۴: عدد
		//۵: فایل
		//۶: تصویر
		//۷: تاریخ شمسی
		//۸: این نوع فیلدها دو مقدار بلی و خیر دارند - گزینه دو انتخابی
		//۹: متغیر سیستمی
		$FormInputName = "document.f1.FIELD_".$this->RelatedFieldName;
		if($this->FieldInputType=="MANDATORY")
		{
			// برای فیلدهای نوع فایل و تصویر اجباری بودن نباید در حالت بروز رسانی فعال باشد چون ممکن است کاربر نخواهد فایل قبلی را تغییر دهد
			// فعلا کلا اجباری بدن برای این دو نوع برداشته شد
			if($this->FieldType!="5" && $this->FieldType!="6" && $this->FieldType!="3")
			{
				$ret .= "if(isEmpty(".$FormInputName.".value))\r\n";
				$ret .= "{\r\n";
				$ret .= "	alert('در ".$this->FieldTitle." بایستی مقداری وارد شود - گزینه های مشخص شده با ستاره باید حتما پر شوند');\r\n";
				$ret .= "	return;\r\n";
				$ret .= "}\r\n";
			}
			
			// برای فیلدهای لیستی اجباری بودن به معنای آن است که یا مقدار نداشته باشد یا مقدار آن صفر باشد
			if($this->FieldType=="3")
			{
				if($this->ListShowType=="RADIO")
				{
					// زمانیکه نوع نمایش رادیویی است باید تمام گزینه ها را چک کند
					$ret .= "if(getRadioButtonValue(".$FormInputName.")=='')\r\n";
					$ret .= "{\r\n";
					$ret .= "	alert('در ".$this->FieldTitle." بایستی مقداری وارد شود - گزینه های مشخص شده با ستاره باید حتما پر شوند');\r\n";
					$ret .= "	return;\r\n";
					$ret .= "}\r\n";
				}
				else
				{ // برای حالت نمایش کمبوباکسی و یا لوک آپ
					$ret .= "if(isEmpty(".$FormInputName.".value))\r\n";
					$ret .= "{\r\n";
					$ret .= "	alert('در ".$this->FieldTitle." بایستی مقداری وارد شود - گزینه های مشخص شده با ستاره باید حتما پر شوند');\r\n";
					$ret .= "	return;\r\n";
					$ret .= "}\r\n";
				}
			}
		}
		if($this->FieldType=="4") // عددی
		{
			$ret .= "if(!isFloat(".$FormInputName.".value, true))\r\n";
			$ret .= "{\r\n";
			$ret .= "	alert('مقدار وارد شده در ".$this->FieldTitle." نامعتبر است');\r\n";
			$ret .= "	return;\r\n";
			$ret .= "}\r\n";
			$ret .= "if(!isEmpty(".$FormInputName.".value) && ".$FormInputName.".value<".$this->MinNumber.")\r\n";
			$ret .= "{\r\n";
			$ret .= "	alert('مقدار وارد شده در ".$this->FieldTitle." از حداقل تعیین شده کمتر است');\r\n";
			$ret .= "	return;\r\n";
			$ret .= "}\r\n";
			$ret .= "if(!isEmpty(".$FormInputName.".value) && ".$FormInputName.".value>".$this->MaxNumber.")\r\n";
			$ret .= "{\r\n";
			$ret .= "	alert('مقدار وارد شده در \"".$this->FieldTitle."\" از حداکثر تعیین شده بیشتر است');\r\n";
			$ret .= "	return;\r\n";
			$ret .= "}\r\n";
			
			if($this->ShowSlider=="YES") // برای نوع اسلایدر در صورت اجباری بودن عدد اول یا آخر بازه نباید انتخاب شده باشد
			{
				$ret .= "if(".$FormInputName.".value==".$this->MinNumber." || ".$FormInputName.".value==".$this->MaxNumber.")\r\n";
				$ret .= "{\r\n";
				$ret .= "	alert('مقدار وارد شده در گزینه \"".$this->FieldTitle."\" نباید عدد ابتدا یا انتهای بازه تعریف شده باشد');\r\n";
				$ret .= "	return;\r\n";
				$ret .= "}\r\n";
			}
			
		}
		else if($this->FieldType=="7") // تاریخ شمسی 
		{
			$ret .= "if(!isEmpty(".$FormInputName.".value))\r\n";
			$ret .= "{\r\n";
			$ret .= "	if(!IsShamsiDate(".$FormInputName.".value))\r\n";
			$ret .= "	{\r\n";
			$ret .= "		alert('مقدار وارد شده در ".$this->FieldTitle." نامعتبر است');\r\n";
			$ret .= "		return;\r\n";
			$ret .= "	}\r\n";
			$ret .= "}\r\n";
		}
		return $ret;
	}
	
	// نوع دسترسی به فیلد را در مرحله ذکر شده بر می گرداند
	function GetAccessType($StepID)
	{
		return manage_FormFields::GetFieldAccessType($this->FormFieldID, $StepID);
	}
}

class manage_FormFields
{
	// بزرگترین شماره ترتیب در ستون را بر می گرداند 
	static function GetMaxColumnOrderNo($FormStructID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select Max(ColumnOrder) as MaxOrder from FormFields where FormsStructID='".$FormStructID."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			if($rec["MaxOrder"]!="")
				return $rec["MaxOrder"];
		}
		return 0;
	}
	
		// 	بزرگترین شماره ترتیب در فرم ورود داده را بر می گرداند 
	static function GetMaxOrderInInputForm($FormStructID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select Max(OrderInInputForm) as MaxOrder from FormFields where FormsStructID='".$FormStructID."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			if($rec["MaxOrder"]!="")
				return $rec["MaxOrder"];
		}
		return 0;
	}
	
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select count(FormFieldID) as TotalCount from FormFields';
		if($WhereCondition!="")
		{
			$query .= ' where '.$WhereCondition;
		}
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	static function GetLastID()
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select max(FormFieldID) as MaxID from FormFields';
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($FormsStructID, $RelatedFieldName, $FieldTitle, $FieldType, $MaxLength, $InputWidth, $InputRows, $MinNumber, $MaxNumber, $MaxFileSize, $CreatingListType, $AddAllItemsToList, $ListRelatedTable, $ListRelatedValueField, $ListRelatedDescriptionField, $ListRelatedDomainName, $ListQuery, $FieldInputType, $DefaultValue, $ValidFileExtensions, $ShowInList, $ColumnOrder, $ColumnWidth, $ListShowType, $LookUpPageAddress, $OrderInInputForm, $ImageWidth, $ImageHeight, $FieldHint, $RelatedFileNameField, $HTMLEditor)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FormFields (FormsStructID
				, RelatedFieldName
				, FieldTitle
				, FieldType
				, MaxLength
				, InputWidth
				, InputRows
				, MinNumber
				, MaxNumber
				, MaxFileSize
				, CreatingListType
				, AddAllItemsToList
				, ListRelatedTable
				, ListRelatedValueField
				, ListRelatedDescriptionField
				, ListRelatedDomainName
				, ListQuery
				, FieldInputType
				, DefaultValue
				, ValidFileExtensions
				, ShowInList
				, ColumnOrder
				, ColumnWidth
				, ListShowType
				, LookUpPageAddress
				, OrderInInputForm
				, ImageWidth
				, ImageHeight
				, FieldHint
				, RelatedFileNameField
				, HTMLEditor
				) values ('".$FormsStructID."'
				, '".$RelatedFieldName."'
				, '".$FieldTitle."'
				, '".$FieldType."'
				, '".$MaxLength."'
				, '".$InputWidth."'
				, '".$InputRows."'
				, '".$MinNumber."'
				, '".$MaxNumber."'
				, '".$MaxFileSize."'
				, '".$CreatingListType."'
				, '".$AddAllItemsToList."'
				, '".$ListRelatedTable."'
				, '".$ListRelatedValueField."'
				, '".$ListRelatedDescriptionField."'
				, '".$ListRelatedDomainName."'
				, '".$ListQuery."'
				, '".$FieldInputType."'
				, '".$DefaultValue."'
				, '".$ValidFileExtensions."'
				, '".$ShowInList."'
				, '".$ColumnOrder."'
				, '".$ColumnWidth."'
				, '".$ListShowType."'
				, '".$LookUpPageAddress."'
				, '".$OrderInInputForm."'
				, '".$ImageWidth."'
				, '".$ImageHeight."'
				, '".$FieldHint."'
				, '".$RelatedFileNameField."'
				, '".$HTMLEditor."'
				)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("ایجاد فیلد [".manage_FormFields::GetLastID()."]");
	}
	static function Update($UpdateRecordID, $RelatedFieldName, $FieldTitle, $FieldType, $MaxLength, $InputWidth, $InputRows, $MinNumber, $MaxNumber, $MaxFileSize, $CreatingListType, $AddAllItemsToList, $ListRelatedTable, $ListRelatedValueField, $ListRelatedDescriptionField, $ListRelatedDomainName, $ListQuery, $FieldInputType, $DefaultValue, $ValidFileExtensions, $ShowInList, $ColumnOrder, $ColumnWidth, $ListShowType, $LookUpPageAddress, $OrderInInputForm, $ImageWidth, $ImageHeight, $FieldHint, $RelatedFileNameField, $HTMLEditor)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FormFields set FieldType='".$FieldType."'
				, RelatedFieldName = '".$RelatedFieldName."'
				, FieldTitle = '".$FieldTitle."'
				, MaxLength='".$MaxLength."'
				, InputWidth='".$InputWidth."'
				, InputRows='".$InputRows."'
				, MinNumber='".$MinNumber."'
				, MaxNumber='".$MaxNumber."'
				, MaxFileSize='".$MaxFileSize."'
				, CreatingListType='".$CreatingListType."'
				, AddAllItemsToList='".$AddAllItemsToList."'
				, ListRelatedTable='".$ListRelatedTable."'
				, ListRelatedValueField='".$ListRelatedValueField."'
				, ListRelatedDescriptionField='".$ListRelatedDescriptionField."'
				, ListRelatedDomainName='".$ListRelatedDomainName."'
				, ListQuery='".$ListQuery."'
				, FieldInputType='".$FieldInputType."'
				, DefaultValue='".$DefaultValue."'
				, ValidFileExtensions='".$ValidFileExtensions."'
				, ShowInList='".$ShowInList."'
				, ColumnOrder='".$ColumnOrder."'
				, ColumnWidth='".$ColumnWidth."'
				, ListShowType='".$ListShowType."'
				, LookUpPageAddress='".$LookUpPageAddress."'
				, OrderInInputForm='".$OrderInInputForm."'
				, ImageWidth='".$ImageWidth."'
				, ImageHeight='".$ImageHeight."'
				, FieldHint='".$FieldHint."'
				, RelatedFileNameField='".$RelatedFileNameField."'
				, HTMLEditor='".$HTMLEditor."'
				where FormFieldID='".$UpdateRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit("بروزرسانی فیلد [".$UpdateRecordID."]");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "delete from FormFields where FormFieldID='".$RemoveRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());

		$mysql->audit("حذف فیلد [".$RemoveRecordID."]");
	}
	static function GetList($FormsStructID, $OrderBy="", $FormsSectionID = -1)
	{
		  
		$k=0;
		$ret = array();
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select FormFields.*, FieldTypes.*, SectionName from FormFields 
					LEFT JOIN FieldTypes on (FieldTypes.FieldTypeID=FormFields.FieldType) 
					LEFT JOIN FormsSections using (FormsSectionID)
					where FormFields.FormsStructID='".$FormsStructID."' ";
		if($FormsSectionID>=0)
			$query .= " and FormsSectionID=".$FormsSectionID;
		if($OrderBy!="") 
			$query .= " order by ".$OrderBy;
		//echo $query;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_FormFields();
			$ret[$k]->FormFieldID=$rec["FormFieldID"];
			$ret[$k]->FormsStructID=$rec["FormsStructID"];
			$ret[$k]->RelatedFieldName=$rec["RelatedFieldName"];
			$ret[$k]->FieldTitle=$rec["FieldTitle"];
			$ret[$k]->FieldType=$rec["FieldType"];
			$ret[$k]->MaxLength=$rec["MaxLength"];
			$ret[$k]->InputWidth=$rec["InputWidth"];
			$ret[$k]->InputRows=$rec["InputRows"];
			$ret[$k]->MinNumber=$rec["MinNumber"];
			$ret[$k]->MaxNumber=$rec["MaxNumber"];
			$ret[$k]->MaxFileSize=$rec["MaxFileSize"];
			$ret[$k]->CreatingListType=$rec["CreatingListType"];
			$ret[$k]->AddAllItemsToList=$rec["AddAllItemsToList"];
			$ret[$k]->ListRelatedTable=$rec["ListRelatedTable"];
			$ret[$k]->ListRelatedValueField=$rec["ListRelatedValueField"];
			$ret[$k]->ListRelatedDescriptionField=$rec["ListRelatedDescriptionField"];
			$ret[$k]->ListRelatedDomainName=$rec["ListRelatedDomainName"];
			$ret[$k]->ListQuery=$rec["ListQuery"];
			$ret[$k]->FieldInputType=$rec["FieldInputType"];
			$ret[$k]->DefaultValue=$rec["DefaultValue"];
			$ret[$k]->ValidFileExtensions=$rec["ValidFileExtensions"];
			$ret[$k]->ShowInList=$rec["ShowInList"];
			$ret[$k]->ColumnOrder=$rec["ColumnOrder"];
			$ret[$k]->ColumnWidth=$rec["ColumnWidth"];
			$ret[$k]->ListShowType=$rec["ListShowType"];
			$ret[$k]->LookUpPageAddress=$rec["LookUpPageAddress"];
			$ret[$k]->OrderInInputForm=$rec["OrderInInputForm"];
			$ret[$k]->ImageWidth=$rec["ImageWidth"];
			$ret[$k]->ImageHeight=$rec["ImageHeight"];
			$ret[$k]->FieldTypeName=$rec["TypeName"];
			$ret[$k]->FieldHint=$rec["FieldHint"];
			$ret[$k]->RelatedFileNameField=$rec["RelatedFileNameField"];
			$ret[$k]->HTMLEditor=$rec["HTMLEditor"];
			$ret[$k]->SectionName=$rec["SectionName"];
			
			$ret[$k]->ShowSlider=$rec["ShowSlider"];
			$ret[$k]->SliderLength=$rec["SliderLength"];
			$ret[$k]->SliderStartLabel=$rec["SliderStartLabel"];
			$ret[$k]->SliderEndLabel=$rec["SliderEndLabel"];
			
			$k++;
		}
		return $ret;
	}
	
	// نوع دسترسی به فیلد را در یک مرحله ثبت می کند
	static function SetFieldAccessType($FormFieldID, $FormFlowStepID, $AccessType)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("delete from FieldsAccessType where FormFieldID='".$FormFieldID."' and FormFlowStepID='".$FormFlowStepID."'");
		$mysql->ExecuteStatement(array());
		$mysql->Prepare("insert into FieldsAccessType (FormFieldID, FormFlowStepID, AccessType) values ('".$FormFieldID."','".$FormFlowStepID."','".$AccessType."')");
		$mysql->ExecuteStatement(array());
	}
	
	// نوع دسترسی به فیلد در یک مرحله را بر می گرداند
	static function GetFieldAccessType($FormFieldID, $FormFlowStepID)
	{
		// کد مرحله منهای یک به معنی این است که فرم فاقد جریان کاری می باشد
		if($FormFlowStepID==-1)
			return "EDITABLE";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("select * from FieldsAccessType where FormFieldID='".$FormFieldID."' and FormFlowStepID='".$FormFlowStepID."'");
		$res = $mysql->ExecuteStatement(array());
		// چنانچه دسترسی برای فیلد تعریف نشده باشد آن را خواندنی در نظر می گیرد
		if($rec=$res->fetch())
		{
			return $rec["AccessType"];
		}
		return "READ";
	}

		// نوع دسترسی به فیلد در در یک نوع دسترسی تعریف شده برای یک فرم در یک پرونده الکترونیکی را بر می گرداند
	static function GetFieldAccessTypeInFile($FormFieldID, $FileTypeUserPermittedFormID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("select * from FileTypeUserPermittedFormDetails where FormFieldID='".$FormFieldID."' and FileTypeUserPermittedFormID='".$FileTypeUserPermittedFormID."'");
		$res = $mysql->ExecuteStatement(array());
		// چنانچه دسترسی برای فیلد تعریف نشده باشد آن را خواندنی در نظر می گیرد
		if($rec=$res->fetch())
		{
			return $rec["AccessType"];
		}
		return "READ";
	}

		// نوع دسترسی به فیلد در در یک نوع دسترسی تعریف شده برای یک فرم در یک پرونده الکترونیکی امانتی را بر می گرداند
	static function GetFieldAccessTypeInTempFile($FormFieldID, $FileTemporaryAccessListID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("select * from FileFormsTemporarayAccessList where FormFieldID='".$FormFieldID."' and FilesTemporarayAccessListID='".$FileTemporaryAccessListID."'");
		$res = $mysql->ExecuteStatement(array());
		// چنانچه دسترسی برای فیلد تعریف نشده باشد آن را خواندنی در نظر می گیرد
		if($rec=$res->fetch())
		{
			return $rec["AccessType"];
		}
		return "READ";
	}
	
}
?>
