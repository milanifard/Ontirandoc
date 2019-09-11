<?
class be_FormsStruct
{
	public $FormsStructID;		//
	public $RelatedDB;		//بانک اطلاعاتی مربوطه
	public $RelatedTable;		//جدول اطلاعاتی مربوطه
	public $FormTitle;		//عنوان فرم
	public $TopDescription;		//توضیحات بالای فرم
	public $ButtomDescription;		//توضیحات پایین فرم
	public $JavascriptCode;		//کد جاوا اسکریپت 
	public $SortByField;		//فیلد پیش فرض مرتب سازی لیست
	public $SortType;		//ترتیب مرتب سازی پیش فرض
	public $KeyFieldName;		//نام فیلد کلید
	public $PrintType;		//نوع صفحه چاپی (پیش فرض/اختصاصی)
	public $PrintPageAddress;		//آدرس صفحه چاپ اختصاصی
	public $CreatorUser;		//کد سازنده
	public $CreateDate;		//تاریخ ایجاد
	public $FormType;		//نوع فرم (سیستمی/کاربری)
	public $ParentID;		//کد فرم پدر (برای جداول جزییات)
	public $ShowType;		//نحوه نمایش فرم ورودی - یک ستونی یا دو ستونی
	public $ValidationExtraJavaScript; 
	public $IsQuestionnaire; // آیا این فرم پرسشنامه است؟
	public $ShowBorder; // ایا حاشیه برای ردیفهای فرم نمایش داده شود
	public $QuestionColumnWidth;
	
	function be_FormsStruct() {}

	function LoadDataFromDatabase($RecID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$mysql->Prepare("select *, projectmanagement.g2j(CreateDate) as GCreateDate from FormsStruct where FormsStructID='".$RecID."' ");
		$res = $mysql->ExecuteStatement(array());
		if($rec=$res->fetch())
		{
			$this->FormsStructID=$rec["FormsStructID"];
			$this->RelatedDB=$rec["RelatedDB"];
			$this->RelatedTable=$rec["RelatedTable"];
			$this->FormTitle=$rec["FormTitle"];
			$this->TopDescription=$rec["TopDescription"];
			$this->ButtomDescription=$rec["ButtomDescription"];
			$this->JavascriptCode=$rec["JavascriptCode"];
			$this->SortByField=$rec["SortByField"];
			$this->SortType=$rec["SortType"];
			$this->KeyFieldName=$rec["KeyFieldName"];
			$this->PrintType=$rec["PrintType"];
			$this->PrintPageAddress=$rec["PrintPageAddress"];
			$this->CreatorUser=$rec["CreatorUser"];
			$this->CreateDate=$rec["GCreateDate"];
			$this->FormType=$rec["FormType"];
			$this->ParentID=$rec["ParentID"];
			$this->ShowType=$rec["ShowType"];
			$this->ShowBorder=$rec["ShowBorder"];
			$this->ValidationExtraJavaScript=$rec["ValidationExtraJavaScript"];
			$this->IsQuestionnaire=$rec["IsQuestionnaire"];
			$this->QuestionColumnWidth = $rec["QuestionColumnWidth"];
		}
	}
	
	// لیست فیلدها را بر می گرداند
	function GetFieldsList()
	{
		require_once("FormFields.class.php");
		return manage_FormFields::GetList($this->FormsStructID, "OrderInInputForm");
	}
	
	// بر اساس پارامترهای ورودی کیوری ثبت داده جدید در بانک را تولید می کند
	// در صورتیکه پارامتر سوم غیر صفر باشد یعنی کنترل دسترسی فیلدها باید بر اساس رکورد دسترسی مربوط به یک پرونده الکترونیکی صورت گیرد
	// برای فرمهایی که جریان کاری دارند کنترل دسترسی با توجه به مرحله فرم خواهد بود
	function CreateInsertQuery($StepID, $PersonID, $FileTypeUserPermittedFormID, $FileTemporaryAccessListID)
	{
		$ret = "";
		$FieldNamesList = "";
		$ValuesList = "";
		$FieldsList = $this->GetFieldsList();
		for($i=0; $i<count($FieldsList); $i++)
		{
			if($FileTypeUserPermittedFormID=="0" && $FileTemporaryAccessListID=="0")
				$AccessType = $FieldsList[$i]->GetAccessType($StepID);
			else if($FileTemporaryAccessListID=="0")
				$AccessType = manage_FormFields::GetFieldAccessTypeInFile($FieldsList[$i]->FormFieldID, $FileTypeUserPermittedFormID);
			else
				$AccessType = manage_FormFields::GetFieldAccessTypeInTempFile($FieldsList[$i]->FormFieldID, $FileTemporaryAccessListID);
			if($AccessType=="EDITABLE")
			{
				if($FieldNamesList!="")
					$FieldNamesList .= ",";
				$FieldNamesList .= $FieldsList[$i]->RelatedFieldName;

				if($ValuesList!="")
					$ValuesList .= ",";
				//1: متنی یک خطی
				//۲: متنی چند خطی
				//۳: لیست
				//۴: عدد
				//۵: فایل
				//۶: تصویر
				//۷: تاریخ شمسی
				//۸: این نوع فیلدها دو مقدار بلی و خیر دارند - گزینه دو انتخابی
				//۹: متغیر سیستمی
				if($FieldsList[$i]->FieldType=="1" || $FieldsList[$i]->FieldType=="2" || $FieldsList[$i]->FieldType=="3" || $FieldsList[$i]->FieldType=="4")
				{
					// اگر لیستی باشد ممکن است حالت رادیویی ایجاد کرده باشد که در صورت عدم انتخاب هیچ گزینه مقداری هم پاس نمی شود
					if($FieldsList[$i]->FieldType=="3")
					{
						if(isset($_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName]))
							$ValuesList .= "'".$_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName]."'";
						else
							$ValuesList .= "'0'";
					}
					else
						$ValuesList .= "'".$_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName]."'";
				}
				else if($FieldsList[$i]->FieldType=="7")
					$ValuesList .= "'".xdate($_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName])."'";
				else if($FieldsList[$i]->FieldType=="8")
				{
					if(isset($_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName]))
						$ValuesList .= "'YES'";
					else
						$ValuesList .= "'NO'";
				}
				else if($FieldsList[$i]->FieldType=="9")
				{
					// وابسته به کلید مربوطه داده از سیستم خوانده شده و قرار گیرد
					$ValuesList .= "'".FormUtils::GetValueAccordingToKeys($FieldsList[$i]->DefaultValue)."'";
				}
				else if($FieldsList[$i]->FieldType=="5" || $FieldsList[$i]->FieldType=="6")
				{
					// ذخیره سازی محتویات فایل
					$FileFieldName = "FIELD_".$FieldsList[$i]->RelatedFieldName;
					$FiledDataSwitch = false; 
					if (trim($_FILES[$FileFieldName]['name']) != '' )
					{
						 if ($_FILES[$FileFieldName]['error'] == 0 )
						 {							
                                                        $_size = $_FILES[$FileFieldName]['size'];
							$_name = $_FILES[$FileFieldName]['tmp_name'];
							$ActualFileName = $_FILES[$FileFieldName]['name'];
							$data = addslashes((fread(fopen($_name, 'r' ),$_size)));
							$ValuesList .= "'".$data."'";
							$FiledDataSwitch = true;
						 }
					}
					if(!$FiledDataSwitch)
						$ValuesList .= "''";
					// ذخیره سازی نام فایل
					// در صورتیکه فیلدی برای قراردادن نام فایل تعیین شده بود
					if(trim($FieldsList[$i]->RelatedFileNameField)!="")
					{
						$FieldNamesList .= ",".$FieldsList[$i]->RelatedFileNameField;
						if($FiledDataSwitch)
							$ValuesList .= ",'".$ActualFileName."'";
						else
							$ValuesList .= ",''";
					}
				}
			}
			else if($FieldsList[$i]->DefaultValue!="")
			{
				$FieldNamesList .= ",".$FieldsList[$i]->RelatedFieldName;
				$ValuesList .= ",'".$FieldsList[$i]->DefaultValue."'";
			}
		}
		$ret = "insert into ".$this->RelatedDB.".".$this->RelatedTable." (".$FieldNamesList.") values (".$ValuesList.")";
		return $ret;
	}

	// داده های فعلی رکورد را با مقادیر پاس شده کاربر مقایسه کرده و فیلدهای تغییر داده شده را در یک رشته بر می گرداند
	// در صورتیکه مرحله منهای یک پاس شود به معنی بدون مرحله بودن فرم است یعنی تمام فیلدها قابل ویرایش هستند
	// در صورتیکه پارامتر چهارم غیر صفر باشد یعنی فرم مربوط به یک پرونده است و بهع مرحله آن نباید توجهی شود	
	// در صورتیکه پارامتر پنجم غیر صفر باشد یعنی فرم مربوط به یک پرونده امانتی است و نباید به مرحله توجهی شود
	function CreateUpdatedFieldsDescription($RecID, $StepID, $PersonID, $FileTypeUserPermittedFormID, $FileTemporaryAccessListID)
	{

		$ret = "&nbsp;";
		$k = 0;
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from ".$this->RelatedDB.".".$this->RelatedTable." where ".$this->KeyFieldName."='".$RecID."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$CurRec = $res->fetch();
		$FieldsList = $this->GetFieldsList();
		for($i=0; $i<count($FieldsList); $i++)
		{
			$NewValue = "";
			if($FileTypeUserPermittedFormID==0 && $FileTemporaryAccessListID==0)
			{
				// در صورتیکه مرحله منهای یک پاس شود به معنی بدون مرحله بودن فرم است یعنی تمام فیلدها قابل ویرایش هستند
				if($StepID==-1)
					$AccessType = "EDITABLE";
				else
					$AccessType = $FieldsList[$i]->GetAccessType($StepID);
			}
			else if($FileTemporaryAccessListID==0)
			{
				$AccessType = manage_FormFields::GetFieldAccessTypeInFile($FieldsList[$i]->FormFieldID, $FileTypeUserPermittedFormID);
			}
			else
			{
				$AccessType = manage_FormFields::GetFieldAccessTypeInTempFile($FieldsList[$i]->FormFieldID, $FileTemporaryAccessListID);
			}
			if($AccessType=="EDITABLE")
			{
				if($FieldsList[$i]->FieldType=="1" || $FieldsList[$i]->FieldType=="2" || $FieldsList[$i]->FieldType=="3" || $FieldsList[$i]->FieldType=="4")
					$NewValue = $_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName];
				else if($FieldsList[$i]->FieldType=="7")
				{
					$NewValue = xdate($_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName]);
					$NewValue = substr($NewValue,0,4)."-".substr($NewValue,4,2)."-".substr($NewValue,6,2)." 00:00:00";
				}
				else if($FieldsList[$i]->FieldType=="8")
				{
					if(isset($_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName]))
						$NewValue = "YES";
					else
						$NewValue = "NO";
				}
				else if($FieldsList[$i]->FieldType=="9")
				{
					// وابسته به کلید مربوطه داده از سیستم خوانده شده و قرار گیرد
					$NewValue = FormUtils::GetValueAccordingToKeys($FieldsList[$i]->DefaultValue);
				}
				else if($FieldsList[$i]->FieldType=="5" || $FieldsList[$i]->FieldType=="6")
				{
					// در مورد فایل و تصویر چنانچه فایلی وارد نشده است به معنی حفظ داده قبلی می باشد
					if (trim($_FILES["FIELD_".$FieldsList[$i]->RelatedFieldName]['name']) == '' )
						$NewValue = $CurRec[$FieldsList[$i]->RelatedFieldName];
				}
				if($NewValue!=$CurRec[$FieldsList[$i]->RelatedFieldName])
				{
					if($k>0)
						$ret .= " - ";
					$ret .= $FieldsList[$i]->FieldTitle;
					$k++;
				}
			}
		}
		return $ret;
	}
	
		// بر اساس پارامترهای ورودی کیوری بروزرسانی رکورد در بانک را تولید می کند
		// در صورتیکه مرحله منهای یک پاس شود به معنی بدون مرحله بودن فرم است یعنی تمام فیلدها قابل ویرایش هستند
		// در صورتیکه پارامتر سوم غیر صفر باشد یعنی کنترل دسترسی فیلدها باید بر اساس رکورد دسترسی مربوط به یک پرونده الکترونیکی صورت گیرد
		// برای فرمهایی که جریان کاری دارند کنترل دسترسی با توجه به مرحله فرم خواهد بود
		// در صورتیکه پارامتر چهارم غیر صفر باشد یعنی کنترل دسترسی بر اساس پرونده امانتی باید صورت بگیرد
	function CreateUpdateQuery($RecID, $StepID, $PersonID, $FileTypeUserPermittedFormID, $FileTemporaryAccessListID)
	{
		$ret = "";
		$FieldNamesList = "";
		$ValuesList = "";
		$FieldsList = $this->GetFieldsList();
		for($i=0; $i<count($FieldsList); $i++)
		{
			if($FileTypeUserPermittedFormID=="0" && $FileTemporaryAccessListID=="0")
			{
				if($StepID==-1)
					$AccessType = "EDITABLE";
				else
					$AccessType = $FieldsList[$i]->GetAccessType($StepID);
			}
			else if($FileTemporaryAccessListID=="0")
			{
				$AccessType = manage_FormFields::GetFieldAccessTypeInFile($FieldsList[$i]->FormFieldID, $FileTypeUserPermittedFormID);
			}
			else
			{
				$AccessType = manage_FormFields::GetFieldAccessTypeInTempFile($FieldsList[$i]->FormFieldID, $FileTemporaryAccessListID);
			}
			if($AccessType=="EDITABLE")
			{
				if($FieldsList[$i]->FieldType=="5" || $FieldsList[$i]->FieldType=="6")
				{
					// ذخیره سازی محتویات فایل
					$FileFieldName = "FIELD_".$FieldsList[$i]->RelatedFieldName;
					$FiledDataSwitch = false; 
					if (trim($_FILES[$FileFieldName]['name']) != '' )
					{
						 if ($_FILES[$FileFieldName]['error'] == 0 )
						 {
							$_size = $_FILES[$FileFieldName]['size'];
							//echo $_size;
							$_name = $_FILES[$FileFieldName]['tmp_name'];
							$ActualFileName = $_FILES[$FileFieldName]['name'];
							$data = addslashes((fread(fopen($_name, 'r' ),$_size)));
							$FiledDataSwitch = true;
						 }
					}
					// اگر فایل فرستاده شده بود فیلد مربوطه هم باید بروز شوددر غیر اینصورت هیچ کاری صورت نمی گیرد 
					if($FiledDataSwitch)
					{
						if($ValuesList!="")
							$ValuesList .= ",";
						$ValuesList .= $FieldsList[$i]->RelatedFieldName."='".$data."'";
						// ذخیره سازی نام فایل
						// در صورتیکه فیلدی برای قراردادن نام فایل تعیین شده بود
						if(trim($FieldsList[$i]->RelatedFileNameField)!="")
							$ValuesList .= ",".$FieldsList[$i]->RelatedFileNameField."='".$ActualFileName."'";
					}
					
				}
				else
				{
					if($ValuesList!="")
						$ValuesList .= ",";
					//1: متنی یک خطی
					//۲: متنی چند خطی
					//۳: لیست
					//۴: عدد
					//۵: فایل
					//۶: تصویر
					//۷: تاریخ شمسی
					//۸: این نوع فیلدها دو مقدار بلی و خیر دارند - گزینه دو انتخابی
					//۹: متغیر سیستمی
					$ValuesList .= $FieldsList[$i]->RelatedFieldName."=";
					if($FieldsList[$i]->FieldType=="1" || $FieldsList[$i]->FieldType=="2" || $FieldsList[$i]->FieldType=="3" || $FieldsList[$i]->FieldType=="4")
						$ValuesList .= "'".$_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName]."'";
					else if($FieldsList[$i]->FieldType=="7")
						$ValuesList .= "'".xdate($_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName])."'";
					else if($FieldsList[$i]->FieldType=="8")
					{
						if(isset($_REQUEST["FIELD_".$FieldsList[$i]->RelatedFieldName]))
							$ValuesList .= "'YES'";
						else
							$ValuesList .= "'NO'";
					}
					else if($FieldsList[$i]->FieldType=="9")
					{
						// وابسته به کلید مربوطه داده از سیستم خوانده شده و قرار گیرد
						$ValuesList .= "'".FormUtils::GetValueAccordingToKeys($FieldsList[$i]->DefaultValue)."'";
					}
				}				
			}
		}
		if($ValuesList=="")
			return "";
		$ret = "update ".$this->RelatedDB.".".$this->RelatedTable." set ".$ValuesList." where ".$this->KeyFieldName."='".$RecID."'";
		return $ret;
	}
	
	function CreateUserInterface($StepID, $PersonID, $RelatedRecordID, $MasterFormsStructID = 0, $MasterRecordID = 0, $FileID = 0, $FileTypeUserPermittedFormID = 0, $FileTemporaryAccessListID = 0)
	{
		return manage_FormsStruct::CreateUserInterface($this->FormsStructID, $StepID, $PersonID, $RelatedRecordID, $MasterFormsStructID, $MasterRecordID, $FileID, $FileTypeUserPermittedFormID, $FileTemporaryAccessListID);
	}

	// پارامترهای ششم و هفتم زمانیست که فرم در حالتیکه جزو پرونده عادی یا امنتی است باید برای چاپ نمایش داده شود
	function CreatePrintableVersion($StepID, $PersonID, $RelatedRecordID, $MasterFormsStructID = 0, $MasterRecordID = 0, $FileTypeUserPermittedFormID = 0, $FileTemporaryAccessListID = 0)
	{
		return manage_FormsStruct::CreatePrintableVersion($this->FormsStructID, $StepID, $PersonID, $RelatedRecordID, $MasterFormsStructID, $MasterRecordID, $FileTypeUserPermittedFormID, $FileTemporaryAccessListID);
	}
	
	// یک داده را به صورت فیزیکی حذف می کند
	// در مواقع معمول تنها باید داده را به مرحله حذف فرستاد و نباید از این متد استفاده کرد
	//برای جداولی که مرحله ندارند و یا 
	//برای داده های جداول جزییات از این متد استفاده می شود
	function RemoveData($RecID, $PersonID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "delete from ".$this->RelatedDB.".".$this->RelatedTable." where ".$this->KeyFieldName."='".$RecID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$mysql->audit('حذف رکوردی با کد '.$RecID." از جدول ".$this->RelatedDB.".".$this->RelatedTable);
	}
	
	// رکوردی را ذخیره می کند - مقادیر را پاس شده به صفحه در نظر می گیرد
	// 
	//در نهایت کد رکورد اضافه شده را بر می گرداند
	// پارامتر سوم وقتی استفاده می شود که بخواهیم فرم را در یک پرونده الکترونیکی ذخیره کنیم
	// در این حالت کنترل دسترسی بر اساس رکورد مربوطه در دسترسی فرم به پرونده چک می شود نه بر اساس مرحله 
	// پارامتر چهارم وقتی استفاده می شود که می خواهیم فرم را در یک پرونده امانتی اضافه کنیم
	// در این حالت کنترل دسترسی فرم بر اساس کنترل پرونده امانتی صورت می گیرد
	function AddData($StepID, $PersonID, $FileTypeUserPermittedFormID = 0, $FileTemporaryAccessListID = 0)
	{
		$PersonType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$PersonType = "STUDENT";
		
		$RecID = 0;
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = $this->CreateInsertQuery($StepID, $PersonID, $FileTypeUserPermittedFormID, $FileTemporaryAccessListID);
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());
		$query = "select max(".$this->KeyFieldName.") as MaxID from ".$this->RelatedDB.".".$this->RelatedTable;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		if($rec = $res->fetch())
		{
			$RecID = $rec["MaxID"];
			// در جدول تاریخچه بروزرسانی درج می کند
			$query = "insert into FormsDataUpdateHistory (FormsStructID, RecID, PersonID, UpdateTime, description, PersonType) values ('".$this->FormsStructID."', '".$RecID."', '".$PersonID."', now(), 'ایجاد رکورد جدید', '".$PersonType."')";
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array());
		}
		return $RecID;
	}
	
	// رکوردی را در مرحله مربوطه بروز رسانی می کند - داده ها پاس شده به صفحه در نظر گرفته می شوند
	// پارامتر سوم وقتی استفاده می شود که بخواهیم فرم را در یک پرونده الکترونیکی ذخیره کنیم
	// در این حالت کنترل دسترسی بر اساس رکورد مربوطه در دسترسی فرم به پرونده چک می شود نه بر اساس مرحله 
	// پارامتر چهارم وقتی استفاده می شود که می خواهیم فرم را در یک پرونده امانتی اضافه کنیم
	// در این حالت کنترل دسترسی فرم بر اساس کنترل پرونده امانتی صورت می گیرد
	
	function UpdateData($RecID, $StepID, $PersonID, $FileTypeUserPermittedFormID = 0, $FileTemporaryAccessListID = 0)
	{
		$PersonType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$PersonType = "STUDENT";
		
		$description = "بروز رسانی فیلد(های): ".$this->CreateUpdatedFieldsDescription($RecID, $StepID, $PersonID, $FileTypeUserPermittedFormID, $FileTemporaryAccessListID);
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = $this->CreateUpdateQuery($RecID, $StepID, $PersonID, $FileTypeUserPermittedFormID, $FileTemporaryAccessListID);
		if($query!="")
		{
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array());
	
			// در جدول تاریخچه بروزرسانی درج می کند
			$query = "insert into FormsDataUpdateHistory (FormsStructID, RecID, PersonID, UpdateTime, description, PersonType) values ('".$this->FormsStructID."', '".$RecID."', '".$PersonID."', now(), '".$description."', '".$PersonType."')";
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array());

		}
	}
	
	// یک رکورد را به مرحله دیگری ارسال می کند
	// کد کاربر ارسال کننده آخرین پارامتر است
	function SendData($RecID, $FromStepID, $ToStepID, $SenderID)
	{
		$SenderType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$SenderType = "STUDENT";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		// ابتدا در تاریخچه اضافه می شود

		$query = "insert into FormsFlowHistory (FormsStructID, RecID, FromPersonID, FromStepID, ToStepID, SendDate, SenderType) ";
		$query .= " values ('".$this->FormsStructID."', '".$RecID."', '".$SenderID."', '".$FromStepID."', '".$ToStepID."', now(), '".$SenderType."')";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());

		
		if($FromStepID==0) // اگر داده جدیدا اضافه شده باشد باید ایجاد کننده آن هم در جدول نگهداری وضعیت داده بروز شود
		{
			$query = "insert into FormsRecords (RelatedRecordID, FormFlowStepID, SendDate, SenderID, CreatorID, FormsStructID, SenderType, CreatorType) values ('".$RecID."', '".$ToStepID."', now(), '".$SenderID."', '".$SenderID."', '".$this->FormsStructID."', '".$SenderType."', '".$SenderType."')";
		}
		else
			$query = "update FormsRecords set FormFlowStepID='".$ToStepID."', SendDate=now(), SenderID='".$SenderID."', SenderType='".$SenderType."' where RelatedRecordID='".$RecID."' and FormFlowStepID='".$FromStepID."'";

		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());

	}
	
	// لیست داده های مربوط به یک جدول جزییات را برای یک رکورد اصلی بر می گرداند
	// در اینجا فرم خودش را همان جدول جزییات در نظر می گیرد
	function CreateListOfDetailData($MasterRecordID, $MasterFormsStructID, $PersonID, $PrintVersion=FALSE)
	{
		$ParentForm = new be_FormsStruct();
		$ParentForm->LoadDataFromDatabase($MasterFormsStructID);
		if($ParentForm->IsQuestionnaire=="YES")
		{
			$CurStepID = -1;
			$EditAccessType = "ALL";
			$RemoveAccessType = "ALL";					
		}
		else
		{
			$CurStepID = FormUtils::GetCurrentStepID($MasterRecordID, $MasterFormsStructID);
			$EditAccessType = SecurityManager::GetUserEditAccessTypeToThisDetailForm($MasterFormsStructID, $this->FormsStructID, $CurStepID);
			$RemoveAccessType = SecurityManager::GetUserRemoveAccessTypeToThisDetailForm($MasterFormsStructID, $this->FormsStructID, $CurStepID);
		}
		if($this->RelatedDB=="")
			return "";
		// نام فیلد کلید خارجی در جدول جزییات
		$RelationField = FormUtils::GetRelationField($MasterFormsStructID, $this->FormsStructID);

		$ret = "";
		if($this->RelatedDB=="")
			return "";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);

		$query = "select * from ".$this->RelatedDB.".".$this->RelatedTable;
		$query .= " where ".$RelationField."='".$MasterRecordID."' ";
		if($this->SortByField!="")
			$query .= " order by ".$this->SortByField." ".$this->SortType;
		//$ret .= $query;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		// لیست داده ها در یک جدول برگردانده می شود که ستونهای آن بر اساس شماره ترتیب ذکر شده در تعریف فیلدها مرتب شده است
		$FieldsList = manage_FormFields::GetList($this->FormsStructID, "ColumnOrder");
		$i = 0;
		$ret .= "<table width=100% border=1 cellspacing=0 cellpadding=3 valign=top>";
		$ret .= "<tr class=FooterOfTable>";
		for($k=0; $k<count($FieldsList); $k++)
		{
			if($FieldsList[$k]->ShowInList=="YES")
			{
				$ret .= "<td width=".$FieldsList[$k]->ColumnWidth.">&nbsp;";
				$ret .= $FieldsList[$k]->FieldTitle;		
				$ret .= "</td>";
			}
		}
		if(!$PrintVersion && ($EditAccessType=="ALL" || $EditAccessType=="ONLY_USER"))
			$ret .= "<td width=1%>ویرایش</td>";
		if(!$PrintVersion && ($RemoveAccessType=="ALL"  || $RemoveAccessType=="ONLY_USER"))
			$ret .= "<td width=1%>حذف</td>";
		$ret .= "</tr>";
		while($rec = $res->fetch())
		{
			$i++;
			if($i%2==0)
				$ret .= "<tr class=OddRow>";
			else
				$ret .= "<tr class=EvenRow>";
			for($k=0; $k<count($FieldsList); $k++)
			{
				if($FieldsList[$k]->ShowInList=="YES")
				{
					$ret .= "<td>&nbsp;";
					if($FieldsList[$k]->FieldType==5 || $FieldsList[$k]->FieldType==6) // فایل یا تصویر
					{
						
					}
					else if($FieldsList[$k]->FieldType==7) // تاریخ شمسی
					{
						$tmp = shdate($rec[$FieldsList[$k]->RelatedFieldName]);
						$ret .= substr($tmp, 6, 2)."/".substr($tmp, 3, 2)."/".substr($tmp, 0, 2);
					} 
					else if($FieldsList[$k]->FieldType=="3") // لیستی
					{
						if($FieldsList[$k]->CreatingListType=="STATIC_LIST")
							$ret .= manage_FieldsItemList::GetItemDescriptionInOptionList($FieldsList[$k]->FormFieldID, $rec[$FieldsList[$k]->RelatedFieldName]);
						else if($FieldsList[$k]->CreatingListType=="RELATED_TABLE")
							$ret .= FormUtils::CreateItemDescriptionFromATable($FieldsList[$k]->ListRelatedTable, $FieldsList[$k]->ListRelatedValueField, $FieldsList[$k]->ListRelatedDescriptionField, $rec[$FieldsList[$k]->RelatedFieldName]);
						else if($FieldsList[$k]->CreatingListType=="QUERY")
							$ret .= FormUtils::CreateItemDescriptionAccordingToQuery($FieldsList[$k]->ListQuery, $rec[$FieldsList[$k]->RelatedFieldName]);
						else if($FieldsList[$k]->CreatingListType=="DOMAINS")
							$ret .= FormUtils::CreateItemDescriptionAccordingToDomainName($FieldsList[$k]->ListRelatedDomainName, $rec[$FieldsList[$k]->RelatedFieldName]);
					}
					else
					{
						$ret .= $rec[$FieldsList[$k]->RelatedFieldName];
					}		
					$ret .= "</td>";
				}
			}
			if(!$PrintVersion)
			{
				if($EditAccessType=="ALL" || ($EditAccessType=="ONLY_USER" && FormUtils::GetCreatorID($this->FormsStructID , $rec[$this->KeyFieldName])==$_SESSION["PersonID"]))
					$ret .= "<td><a href='javascript: GoEdit(".$rec[$this->KeyFieldName].")'>ویرایش</td>";
				else if($EditAccessType=="ALL" || $EditAccessType=="ONLY_USER")
					$ret .= "<td>&nbsp;</td>";
				if($RemoveAccessType=="ALL" || ($RemoveAccessType=="ONLY_USER" && FormUtils::GetCreatorID($this->FormsStructID , $rec[$this->KeyFieldName])==$_SESSION["PersonID"]))
					$ret .= "<td><a href='javascript: GoDelete(".$rec[$this->KeyFieldName].")'>حذف</td>";
				else if($RemoveAccessType=="ALL" || $RemoveAccessType=="ONLY_USER")
					$ret .= "<td>&nbsp;</td>";
			}
			$ret .= "</tr>";
		}
		$ret .= "</table>";
		if($ParentForm->IsQuestionnaire=="NO")
			$ret .= "<form method=post id=f1 name=f1 action='NewDetailRecord.php' target=_blank>";
		else
			$ret .= "<form method=post id=f1 name=f1 action='NewQuestionnaireDetailRecord.php' target=_blank>";
		$ret .= "<input type=hidden name='MasterRecordID' id='MasterRecordID' value='".$MasterRecordID."'>";
		$ret .= "<input type=hidden name='MasterFormsStructID' id='MasterFormsStructID' value='".$MasterFormsStructID."'>";
		$ret .= "<input type=hidden name='SelectedFormStructID' id='SelectedFormStructID' value='".$this->FormsStructID."'>";
		$ret .= "<input type=hidden name='ActionType' id='ActionType' value=''>";
		$ret .= "<input type=hidden name='RelatedRecordID' id='RelatedRecordID' value=''>";
		$ret .= "</form>";
		$ret .= "<script>";
		$ret .= "function GoEdit(RelatedRecordID)\r\n";
		$ret .= "{\r\n";
		
		$ret .= "	document.getElementById('RelatedRecordID').value=RelatedRecordID;\r\n";
		$ret .= "	document.getElementById('ActionType').value='EDIT';\r\n";
		$ret .= "	f1.submit();\r\n";
		
		$ret .= "}\r\n";
		$ret .= "function GoDelete(RelatedRecordID)\r\n";
		$ret .= "{\r\n";
		
		$ret .= "	document.getElementById('RelatedRecordID').value=RelatedRecordID;\r\n";
		$ret .= "	document.getElementById('ActionType').value='REMOVE';\r\n";
		$ret .= "	f1.submit();\r\n";
		
		$ret .= "}\r\n";
		$ret .= "</script>";
		return $ret;
	}

	// تعداد آیتمهای ذخیره شده را با توجه به شرط داده شده بر می گرداند
	function GetItemsCount($condition)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);

		$query = "select count(*) from ".$this->RelatedDB.".".$this->RelatedTable;
		if($condition!="")
			$query .= " where ".$condition;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		$rec = $res->fetch();
		return $rec[0];
	}
	
	// لیست کلیه داده های جدول را بدون توجه به مرحله آنها با توجه به ستونهای تعیین شده ایجاد می کند
	// EditOrRemovePageAddress: نام صفحه ای که لینک ویرایش و حذف شماره رکورد را به آن صفحه ارسال خواهد کرد
	// FromRec: شماره رکورد شروع
	// ItemsPerPage: تعداد رکوردهایی که باید نمایش داده شود
	function CreateListOfData($EditOrRemovePageAddress, $condition, $FromRec, $ItemsPerPage)
	{
		$ret = "";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);

		$query = "select * from ".$this->RelatedDB.".".$this->RelatedTable;
		if($condition!="")
			$query .= " where ".$condition;

		if($this->SortByField!="")
			$query .= " order by ".$this->SortByField." ".$this->SortType;
		$query .= " limit $FromRec, $ItemsPerPage";
			//$ret .= $query;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		// لیست داده ها در یک جدول برگردانده می شود که ستونهای آن بر اساس شماره ترتیب ذکر شده در تعریف فیلدها مرتب شده است
		$FieldsList = manage_FormFields::GetList($this->FormsStructID, "ColumnOrder");
		$i = 0;
		$ret .= "<table width=98% border=1 cellspacing=0 cellpadding=3 valign=top align=center>";
		$ret .= "<tr bgcolor=#cccccc>";
		$ret .= "<td width=1%>ردیف</td>";
		for($k=0; $k<count($FieldsList); $k++)
		{
			if($FieldsList[$k]->ShowInList=="YES")
			{
				$ret .= "<td width=".$FieldsList[$k]->ColumnWidth.">&nbsp;";
				$ret .= $FieldsList[$k]->FieldTitle;		
				$ret .= "</td>";
			}
		}
		$ret .= "<td width=1%>ویرایش</td>";
		$ret .= "<td width=1%>حذف</td>";
		$ret .= "</tr>";
		while($rec = $res->fetch())
		{
			$RecID = $rec[$this->KeyFieldName];
			$i++;
			if($i%2==0)
				$ret .= "<tr class=OddRow>";
			else
				$ret .= "<tr class=EvenRow>";
			$ret .= "<td>".($i)."</td>";
			for($k=0; $k<count($FieldsList); $k++)
			{
				$DFileName = $FieldsList[$k]->RelatedFileNameField;
				$FieldValue = $rec[$FieldsList[$k]->RelatedFieldName];
				if($FieldsList[$k]->ShowInList=="YES")
				{
					$ret .= "<td>&nbsp;";
					if($FieldsList[$k]->FieldType=="3") // لیستی
					{
						if($FieldsList[$k]->CreatingListType=="STATIC_LIST")
							$ret .= manage_FieldsItemList::GetItemDescriptionInOptionList($FieldsList[$k]->FormFieldID, $FieldValue);
						else if($FieldsList[$k]->CreatingListType=="RELATED_TABLE")
							$ret .= FormUtils::CreateItemDescriptionFromATable($FieldsList[$k]->ListRelatedTable, $FieldsList[$k]->ListRelatedValueField, $FieldsList[$k]->ListRelatedDescriptionField, $FieldValue);
						else if($FieldsList[$k]->CreatingListType=="QUERY")
							$ret .= FormUtils::CreateItemDescriptionAccordingToQuery($FieldsList[$k]->ListQuery, $FieldValue);
						else if($FieldsList[$k]->CreatingListType=="DOMAINS")
							$ret .= FormUtils::CreateItemDescriptionAccordingToDomainName($FieldsList[$k]->ListRelatedDomainName, $FieldValue);
					}					
					else if($FieldsList[$k]->FieldType=="6") // تصویر
					{
						$ShowFileName = "pic.jpg";
						if($DFileName!="")
							$ShowFileName = $rec[$DFileName];
						$ret .= "<img src='ShowPic.php?FormsStructID=".$this->FormsStructID."&FieldName=".$FieldsList[$k]->RelatedFieldName."&RecID=".$RecID."&DownloadFileName=".$ShowFileName."' ";
						if($FieldsList[$k]->ImageWidth!="0" && $FieldsList[$k]->ImageWidth!="")
							$ret .= " width='".$FieldsList[$k]->ImageWidth."' ";
						if($FieldsList[$k]->ImageHeight!="0" && $FieldsList[$k]->ImageHeight!="")
							$ret .= " height='".$FieldsList[$k]->ImageHeight."' ";
						$ret .= ">";
					}
					else if($FieldsList[$k]->FieldType=="5") // فایل
					{
						$ShowFileName = "download.dat";
						if($DFileName!="")
							$ShowFileName = $rec[$DFileName];
						$ret .= "<a href='DownloadFile.php?FormsStructID=".$this->FormsStructID."&FieldName=".$FieldsList[$k]->RelatedFieldName."&RecID=".$RecID."&DownloadFileName=".$ShowFileName."'>دریافت فایل</a>";
					}
					else if($FieldsList[$k]->FieldType=="8") // دو گزینه ای
					{
						if($FieldValue=="YES")
							$ret .= "<img src='images/tick.jpg'>";
						else
							$ret .= "&nbsp;";
					}
					else	
						$ret .= $rec[$FieldsList[$k]->RelatedFieldName];
					$ret .= "</td>";
				}
			}
			$ret .= "<td><a href='javascript: GoEdit(".$rec[$this->KeyFieldName].")'>ویرایش</td>";
			$ret .= "<td><a href='javascript: GoDelete(".$rec[$this->KeyFieldName].")'>حذف</td>";
			$ret .= "</tr>";
		}
		$ret .= "</table>";
		$ret .= "<form method=post id=f1 name=f1 action='".$EditOrRemovePageAddress."' target=_blank>";
		$ret .= "<input type=hidden name='SelectedFormStructID' id='SelectedFormStructID' value='".$this->FormsStructID."'>";
		$ret .= "<input type=hidden name='ActionType' id='ActionType' value=''>";
		$ret .= "<input type=hidden name='RelatedRecordID' id='RelatedRecordID' value=''>";
		$ret .= "</form>";
		$ret .= "<script>";
		$ret .= "function GoEdit(RelatedRecordID)\r\n";
		$ret .= "{\r\n";
		
		$ret .= "	document.getElementById('RelatedRecordID').value=RelatedRecordID;\r\n";
		$ret .= "	document.getElementById('ActionType').value='EDIT';\r\n";
		$ret .= "	f1.submit();\r\n";
		
		$ret .= "}\r\n";
		$ret .= "function GoDelete(RelatedRecordID)\r\n";
		$ret .= "{\r\n";
		$ret .= "	if(confirm('آیا مطمین هستید؟')) \r\n";
		$ret .= "	{\r\n";
		$ret .= "		document.getElementById('RelatedRecordID').value=RelatedRecordID;\r\n";
		$ret .= "		document.getElementById('ActionType').value='REMOVE';\r\n";
		$ret .= "		f1.submit();\r\n";
		$ret .= "	}\r\n";
		$ret .= "}\r\n";
		$ret .= "</script>";
		return $ret;
	}

	function HasThisPersonAccessToManageStruct($PersonID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "select * from FormManagers where FormsStructID='".$this->FormsStructID."' and PersonID='".$PersonID."' and AccessType in ('FULL', 'STRUCT')";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		if($rec=$res->fetch())
		{
			return true;
		}
		return false;
	}

   /*     function CheckFilesExtention(){
            $isAllowed = true;
            $FieldsList = $this->GetFieldsList();
            for($i=0; $i<count($FieldsList); $i++)
            {
                if($FileTypeUserPermittedFormID=="0" && $FileTemporaryAccessListID=="0")
                        $AccessType = $FieldsList[$i]->GetAccessType($StepID);
                else if($FileTemporaryAccessListID=="0")
                        $AccessType = manage_FormFields::GetFieldAccessTypeInFile($FieldsList[$i]->FormFieldID, $FileTypeUserPermittedFormID);
                else
                        $AccessType = manage_FormFields::GetFieldAccessTypeInTempFile($FieldsList[$i]->FormFieldID, $FileTemporaryAccessListID);
                if($AccessType=="EDITABLE")
                {
                   if($FieldsList[$i]->FieldType=="5" || $FieldsList[$i]->FieldType=="6")
                    {
                        $FileFieldName = "FIELD_".$FieldsList[$i]->RelatedFieldName;
                        if (trim($_FILES[$FileFieldName]['name']) != '' )
                        {
                             if ($_FILES[$FileFieldName]['error'] == 0 )
                             {
                                if($FieldsList[$i]->ValidFileExtensions != ''){
                                    $allowedExt = split ( '-', $FieldsList[$i]->ValidFileExtensions );
                                    $st = split ( '\.', $_FILES [$FileFieldName] ['name'] );
                                    $extension = $st [count ( $st ) - 1];
                                    $extension = strtolower($extension);
                                    for ($i = 0 ; i < sizeof($allowedExt) ; $i++){
                                       $allowedExt[$i] = strtolower($allowedExt[$i]);
                                       if ($allowedExt[$i] == $extension){
                                           $isAllowed = false;
                                           break;
                                       }
                                    }
                                }
                             }
                        }
                    }
                }
                if (!$isAllowed)
                    break;
            }
            return $isAllowed;
        }*/
     }



class manage_FormsStruct
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = 'select count(FormsStructID) as TotalCount from FormsStruct';
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
		$query = 'select max(FormsStructID) as MaxID from FormsStruct';
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	static function Add($RelatedDB, $RelatedTable, $FormTitle, $TopDescription, $ButtomDescription, $JavascriptCode, $PrintType, $PrintPageAddress, $ShowType, $ValidationExtraJavaScript)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "insert into FormsStruct (RelatedDB
				, RelatedTable
				, FormTitle
				, TopDescription
				, ButtomDescription
				, JavascriptCode
				, PrintType
				, PrintPageAddress
				, CreatorUser
				, CreateDate
				, KeyFieldName
				, ShowType
				, ValidationExtraJavaScript
				) values ('".$RelatedDB."'
				, '".$RelatedTable."'
				, '".$FormTitle."'
				, '".$TopDescription."'
				, '".$ButtomDescription."'
				, '".$JavascriptCode."'
				, '".$PrintType."'
				, '".$PrintPageAddress."'
				, '".$_SESSION["UserID"]."'
				, now()
				, '".manage_FormsStruct::GetPrimaryKey($RelatedDB, $RelatedTable)."'
				, '".$ShowType."'		
				, '".$ValidationExtraJavaScript."'
				)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());

		$FormID = manage_FormsStruct::GetLastID();
		$mysql->audit("ایجاد ساختار فرم [".$FormID."]");
		// به صورت پیش فرض دو مرحله ایجاد می کند
		// مرحله ایجاد که هر کس فقط به فرمهای خود دسترسی دارد و از نوع شروع است
		manage_FormsFlowSteps::Add($FormID, "ایجاد", "START", "DENY", "DENY","DENY","DENY","NO_FILTER", "NO", "NO", "HIM", 0);
		$FromStepID = manage_FormsFlowSteps::GetLastID();
		// مرحله حذف که از نوع مرحله پایانی است و همه دسترسی دارند و فقط به فرمهای خود 
		manage_FormsFlowSteps::Add($FormID, "حذف", "ARCHIVE", "DENY", "DENY","DENY","DENY","NO_FILTER", "NO", "NO", "HIM", 0);
		$NextStepID = manage_FormsFlowSteps::GetLastID();
		// مرحله بعدی برای مرحله ایجاد به طور پیش فرض مرحله حذف تعریف می شود
		manage_FormsFlowStepRelations::Add($FromStepID, $NextStepID);
	}
	static function Update($UpdateRecordID, $FormTitle, $TopDescription, $ButtomDescription, $JavascriptCode, $SortByField, $SortType, $KeyFieldName, $PrintType, $PrintPageAddress, $ShowType, $ValidationExtraJavaScript)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "update FormsStruct set FormTitle='".$FormTitle."'
				, TopDescription='".$TopDescription."'
				, ButtomDescription='".$ButtomDescription."'
				, JavascriptCode='".$JavascriptCode."'
				, SortByField='".$SortByField."'
				, SortType='".$SortType."'
				, KeyFieldName='".$KeyFieldName."'
				, PrintType='".$PrintType."'
				, PrintPageAddress='".$PrintPageAddress."'
				, ShowType='".$ShowType."'
				, ValidationExtraJavaScript='".$ValidationExtraJavaScript."' 
				where FormsStructID='".$UpdateRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());

		$mysql->audit("بروزرسانی ساختار فرم [".$UpdateRecordID."]");
	}
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "delete from FormsStruct where FormsStructID='".$RemoveRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());

		$query = "delete from FormFields where FormsStructID='".$RemoveRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());

		$query = "delete from FormsFlowSteps where FormsStructID='".$RemoveRecordID."'";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array());

		$mysql->audit("حذف ساختار فرم [".$RemoveRecordID."]");
	}
	static function GetList($WhereCondition)
	{
		$k=0;
		$ret = array();
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = '';
		$query .= "select *, projectmanagement.g2j(CreateDate) as GCreateDate, 
					(select FormTitle from FormsStruct as DetailList
					JOIN FormsDetailTables on (DetailList.FormsStructID=FormsDetailTables.FormStructID) 
					where DetailFormStructID=FormsStruct.FormsStructID limit 0,1) as ParentTitle  
					from FormsStruct  
					";
		if($WhereCondition!="") 
			$query .= "where ".$WhereCondition;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_FormsStruct();
			$ret[$k]->FormsStructID=$rec["FormsStructID"];
			$ret[$k]->RelatedDB=$rec["RelatedDB"];
			$ret[$k]->RelatedTable=$rec["RelatedTable"];
			$ret[$k]->FormTitle=$rec["FormTitle"];
			$ret[$k]->TopDescription=$rec["TopDescription"];
			$ret[$k]->ButtomDescription=$rec["ButtomDescription"];
			$ret[$k]->JavascriptCode=$rec["JavascriptCode"];
			$ret[$k]->SortByField=$rec["SortByField"];
			$ret[$k]->SortType=$rec["SortType"];
			$ret[$k]->KeyFieldName=$rec["KeyFieldName"];
			$ret[$k]->PrintType=$rec["PrintType"];
			$ret[$k]->PrintPageAddress=$rec["PrintPageAddress"];
			$ret[$k]->CreatorUser=$rec["CreatorUser"];
			$ret[$k]->CreateDate=$rec["GCreateDate"];
			$ret[$k]->FormType=$rec["FormType"];
			$ret[$k]->ParentID=$rec["ParentID"];
			$ret[$k]->ShowType=$rec["ShowType"];
			$ret[$k]->ShowBorder=$rec["ShowBorder"];
			$ret[$k]->ParentTitle=$rec["ParentTitle"];
			$k++;
		}
		return $ret;
	}
	
	// لیست فیلدهای یک جدول را به صورت گزینه های یک لیست بر می گرداند
	static function CreateFieldsOptions($DBName, $TableName)
	{
		$ret = "";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "SELECT * from COLUMNS where TABLE_SCHEMA='".$DBName."' and TABLE_NAME='".$TableName."'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["COLUMN_NAME"]."'>".$rec["COLUMN_NAME"]." (".$rec["COLUMN_COMMENT"].")";
		}
		return $ret;
	}
	
	// کلید اصلی را با توجه به ساختار جدول در دیتابیس بر می گرداند
	static function GetPrimaryKey($DBName, $TableName)
	{
		$ret = "";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "SELECT * from COLUMNS where TABLE_SCHEMA='".$DBName."' and TABLE_NAME='".$TableName."' and COLUMN_KEY='PRI'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		if($rec=$res->fetch())
		{
			$ret = $rec["COLUMN_NAME"];
		}
		return $ret;
		
	}
	
	// لیست فرمها را به صورت گزینه های یک لیست اچ تی ام الی بر می گرداند
	static function CreateFormsStructOptions()
	{
		$ret = "";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$query = "SELECT FormsStructID, FormTitle from FormsStruct order by FormTitle";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		while($rec=$res->fetch())
		{
			$ret .= "<option value='".$rec["FormsStructID"]."'>".$rec["FormTitle"];
		}
		return $ret;
	}
	
	static function CreateInputFormHeader($FormsStructID)
	{
		$ret = "";
		require_once("FormsSections.class.php");
		$list = manage_FormsSections::GetList($FormsStructID);
		if(count($list)>0)
		{
			$ret .= "<tr>";
			$ret .= "<td style='padding: 0px;'>";
			$ret .= "<table width=100% border=1 cellspacing=0 style='margin: 0px'>";
			$ret .= "<tr>";
			for($i=0; $i<count($list); $i++)
			{
				$ret .= "<td id='td_section_".$list[$i]->FormsSectionID."' width='".floor(100/count($list))."%' ";
				if($i==0)
					$ret .= " style='background-color:#ccc;' ";
				else
					$ret .= " style='background-color:#fff;' ";				
				$ret .= "><a href='#' onclick='ShowTab(".$list[$i]->FormsSectionID.")'><b>".$list[$i]->SectionName."</b></a></td>";
			}
			$ret .= "</tr>";
			$ret .= "</table>";
			
			$ret .= "</td>";
			$ret .= "</tr>";
			
			$ret .= "<script>";
			$ret .= "var CurrentTabID = '".$list[0]->FormsSectionID."';\r\n";
			$ret .= "function ShowTab(SectionID)\r\n{\r\n";
			for($i=0; $i<count($list); $i++)
			{
				$ret .= "	document.getElementById('td_section_".$list[$i]->FormsSectionID."').style.backgroundColor='#fff';\r\n";
				$ret .= "	document.getElementById('tr_form_".$list[$i]->FormsSectionID."').style.display='none';\r\n";
			}
			$ret .= "	document.getElementById('td_section_'+SectionID).style.backgroundColor='#ccc';\r\n";
			$ret .= "	document.getElementById('tr_form_'+SectionID).style.display='';\r\n";
			$ret .= "	CurrentTabID = SectionID;\r\n";
			$ret .= "}";
			$ret .= "</script>";
		}
		return $ret;
	}
	
	// فرم ورود داده در یک فرم را به صورت کد اچ تی ام ال بر می گرداند
	// RelatedRecordID: کد رکورد مقادیر فرم می باشد - در صورتیکه فرم برای ویرایش یک رکورد از داده ایجاد شده باشد
	// StepID: کد مرحله مربوطه - در هر مرحله با توجه به سطح دسترسیها شکل نمایش فیلدهای فرم متغیر خواهد بود
	// MasterFormsStructID: چنانچه فرم جاری جزو جداول جزییات یک فرم دیگر بود باید این مقدار هم به متد پاس شود و برای حالت عادی به صورت پیش فرض صفر است یعنی جدول اصلی است
	// masterRecordID: کد رکورد اصلی - در صورتیکه فرم جاری یک جدول جزییات باشد
	// توجه: اگر کد مرحله منهای یک پاس شود یعنی جدول بدون مرحله می باشد و نباید دسترسیها چک شود
	// پارامتر آخر مربوط به کد یک دسترسی تعریف شده برای فرم در یک پرونده الکترونیکی است و چنانچه این پارامتر غیر صفر باشد مرحله در نظر گرفته نمی شود و کنترل دسترسی فیلدها بر اساس این پارامتر خواهد بود
	// پارامتر پنجم مربوط به کد دسترسی امانتی به یک پرونده است و چنانچه غیر صفر باشد مرحله در نظر گرفته نمی شود و فیلدها بر اساس این پارامتر کنترل دسترسی می شوند
	static function CreateUserInterface($FormsStructID, $StepID, $PersonID, $RelatedRecordID, $MasterFormsStructID = 0, $MasterRecordID = 0, $FileID = 0, $FileTypeUserPermittedFormID = 0, $FileTemporaryAccessListID = 0)
	{
		require_once("FormsFlowSteps.class.php");
		require_once("FormsSections.class.php");

		$slist = manage_FormsSections::GetList($FormsStructID);
		// اگر فرم بخش بندی نشده بود یک بخش به عنوان بخش اصلی در نظر می گیرد
		if(count($slist)==0)
		{
			$slist[0] = new be_FormsSections();
			$slist[0]->FormsSectionID=-1;
			$slist[0]->FormsStructID=$FormsStructID;
			$slist[0]->SectionName="فرم اصلی";
			$slist[0]->ShowOrder=0;
		}
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$CurForm = new be_FormsStruct();
		$CurForm->LoadDataFromDatabase($FormsStructID);

		if($CurForm->ShowBorder=="YES")
			$ExtraStyle = " style='border:1px solid' ";
		else
			$ExtraStyle = " ";
		$CurStep = new be_FormsFlowSteps();
		$CurStep->LoadDataFromDatabase($StepID);
		// برای اسلاید بارها
		$ret = "<script>\r\n";
		$ret .= "window.dhx_globalImgPath = \"codebase/imgs/\";\r\n";
		$ret .= "</script>\r\n";
		$ret .= "<script  src=\"codebase/dhtmlxcommon.js\"></script>\r\n";
		$ret .= "<script  src=\"codebase/dhtmlxslider.js\"></script>\r\n";
		$ret .= "<script  src=\"codebase/ext/dhtmlxslider_start.js\"></script>\r\n";
		$ret .= "<link rel=\"STYLESHEET\" type=\"text/css\" href=\"codebase/dhtmlxslider.css\">\r\n";
		
		$ret .= "<form method=post id=f1 name=f1 ENCTYPE='multipart/form-data'>\r\n";
		$ret .= "<input type=hidden name=ActionType id=ActionType value=''>";
		if($FileID>0)
		{
			$ret .= "<input type=hidden name=FileID id=FileID value='".$FileID."'>";
			$ret .= "<input type=hidden name=ContentType id=ContentType value='FORM'>";
		}
		$ret .= "<input type=hidden name=SelectedFormStructID id=SelectedFormStructID value='".$FormsStructID."'>";
                $ret .= "<input type=hidden name=CurrentStepID id=CurrentStepID value='".$StepID."'>";
		$ret .= "<input type=hidden name=MasterFormsStructID id=MasterFormsStructID value='".$MasterFormsStructID."'>";
		$ret .= "<input type=hidden name=MasterRecordID id=MasterRecordID value='".$MasterRecordID."'>";
		if(isset($_REQUEST["RelatedRecordID"]))
			$ret .= "<input type=hidden name=RelatedRecordID id=RelatedRecordID value='".$RelatedRecordID."'>";
		$ret .= "<table width=90% align=center border=1 cellspacing=0 cellpadding=3>";
		$ret .= "<tr class=HeaderOfTable>";
		$ret .= "<td colspan=2><b>".$CurForm->FormTitle."</b></td>";
		$ret .= "</tr>";
		$ret .= manage_FormsStruct::CreateInputFormHeader($FormsStructID);
		$ret .= "<tr>";
		$ret .= "<td>";
		$ret .= str_replace("\n", "<br>", $CurForm->TopDescription);
		$ret .= "<table width=100% border=0";
		$ret .= ">\r\n";
		
		$CreatorLastName = $CreatorFirstName = $CreatorPersonType = $CreatorFacultyName = $CreatorUnitName = $CreatorEduGrpName = $CreatorPostName = "";
		$CreatorSecName = $CreatorNationalCode = "";
		// در حالت پیش فرض در نظر گرفته می شود که فرد جاری ایجاد کننده فرم است 
		$CreatorID = $_SESSION["User"]->PersonID;
		$CreatorType = "PERSONEL";
		if($_SESSION["SystemCode"]=="10")
			$CreatorType = "STUDENT";
		// اگر فرم در مود ویرایش بود باید ایجاد کننده اصلی فرم مشخص شود
		if($RelatedRecordID>0)
		{
			$query = "SELECT * from formsgenerator.FormsRecords where RelatedRecordID='".$RelatedRecordID."' and FormsStructID='".$CurForm->FormsStructID."'";
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array());
            if($rec = $res->fetch())
			{
				$CreatorID = $rec["CreatorID"];
				$CreatorType = $rec["CreatorType"];
			}

		}

		// استخراج اطلاعات ایجاد کننده فرم از بانکهای اطلاعاتی برای جایگزینی در کلیدهای برچسبها
               
		$query = "SELECT * from projectmanagement.persons where persons.PersonID='".$CreatorID."'";


		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());

		if($rec = $res->fetch())
		{
            $CreatorLastName = $rec["plname"];
			$CreatorFirstName = $rec["pfname"];
			$CreatorPersonType = "";
			$CreatorStNo = "";
			$CreatorAddress = $rec["address1"];
			$CreatorTel = $rec["home_phone1"];
			$CreatorMobile = $rec["mobile_phone"];
			$CreatorEmail = $rec["email"];
			if($CreatorType=="PERSONEL")
			{
				$CreatorNationalCode = $rec["national_code"];
				$CreatorPostname = $rec["title"];
			}
			else
			{
				$CreatorNationalCode = $rec["NID"];
				$CreatorPostname = "";
			}
		}
		
		if($RelatedRecordID>0)
		{
			// در صورتیکه قرار بود اطلاعات رکوردی در فرم نمایش داده شود محتویات آن رکورد بارگذاری می شود
			$query = "SELECT * from ".$CurForm->RelatedDB.".".$CurForm->RelatedTable." where ".$CurForm->KeyFieldName."='".$RelatedRecordID."'";
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array());

			$rec = $res->fetch();
		}
		require_once("FormFields.class.php");
		require_once("FieldsItemList.class.php");
		require_once("FormsFlowStepRelations.class.php");
		require_once("FormsDetailTables.class.php");
		$FormHasEditableField = false; // مشخص می کند آیا این فرم آیتم قابل تغییری دارد را خیر
		
		for($l=0; $l<count($slist); $l++)
		{
			$TrName = "tr_form_";
			if($slist[$l]->FormsSectionID>=0)
				$TrName = "tr_form_".$slist[$l]->FormsSectionID;
			$ret .= "<tr id='".$TrName."' style='display: ";
			if($l>0)
				$ret .= "none"; // در نمایش اول فرم/بخش اول فیلدها نمایش داده می شود و بقیه مخفی هستند
			$ret .= "'>\r\n	<td>\r\n	";
			$ret .= str_replace("\n", "<br>", $slist[$l]->HeaderDesc);
			$ret .= "	<table width=100%>\r\n";
			$FieldsList = manage_FormFields::GetList($FormsStructID, "OrderInInputForm", $slist[$l]->FormsSectionID);
			$ExtOnLoad = "";
			for($i=0; $i<count($FieldsList); $i++)
			{
				if($RelatedRecordID>0 && $FieldsList[$i]->FieldType!=5 && $FieldsList[$i]->FieldType!=6)
				{
					if($FieldsList[$i]->FieldType==7) // تاریخ شمسی
					{
						$tmp = shdate($rec[$FieldsList[$i]->RelatedFieldName]);
						$FieldValue = substr($tmp, 6, 2)."/".substr($tmp, 3, 2)."/".substr($tmp, 0, 2);
					} 
					else
					// در صورتیکه قرار بود محتویات رکوردی در فیلدهای فرم بارگذاری شود و نوع فیلد فایل یا تصویر نبود آنگاه:
						$FieldValue = $rec[$FieldsList[$i]->RelatedFieldName];
				}
				else
				{
					// در غیر اینصورت باید محتویات فیلد برابر مقدار پیش فرض تعیین شده توسط کاربر طراح فرم قرار گیرد
					// برخی از این مقادیر پیش فرض کلیدهایی هستند که با توجه به مقادیر سرور پر می شوند و بنابراین باید جایگزین شوند
					// مثلا کد کاربر جاری یا تاریخ جاری
					$FieldValue = FormUtils::GetValueAccordingToKeys($FieldsList[$i]->DefaultValue);
				}
				// بهتر است از این نوع ویرایشگر استفاده نشود چون امکان حملات اسکریپتی وجود دارد
				if($FieldsList[$i]->FieldType==2 && $FieldsList[$i]->HTMLEditor=="YES") // متنی چند خطی که روی آن ویرایشگر اچ تی ام ال فعال شده باشد
				{
					$ExtOnLoad .= "new Ext.form.HtmlEditor({\r\n";
					$ExtOnLoad .= "id:\"FIELD_".$FieldsList[$i]->RelatedFieldName."\",	\r\n width: 550, \r\n	height: 250, \r\n renderTo : \"DIV_".$FieldsList[$i]->RelatedFieldName."\" \r\n	});\r\n";
					$ExtOnLoad .= "Ext.getCmp(\"FIELD_".$FieldsList[$i]->RelatedFieldName."\").setValue('".str_replace("\r\n", "", $FieldValue)."');\r\n";
				}
				
				if($FileID==0)
				{
					// نوع دسترسی به فیلد با توجه به کد فیلد و کد مرحله آن استخراج می شود تا از آن در نحوه نمایش استفاده شود
					// در صورتیکه کد مرحله منهای یک ارسال شده باشد یعنی جدول بدون مرحله است و دسترسی تمام فیلدها ویرایش می باشد
					if($StepID==-1)
						$AccessType = "EDITABLE";
					else
						$AccessType = manage_FormFields::GetFieldAccessType($FieldsList[$i]->FormFieldID, $StepID);
				}
				else
				{
					// در صورتیکه دسترسی باید بر اساس پرونده چک شود آنگاه اول چک می کند بر اساس پرونده امانتی است یا نوع پرونده
					if($FileTemporaryAccessListID>0)
						$AccessType = manage_FormFields::GetFieldAccessTypeInTempFile($FieldsList[$i]->FormFieldID, $FileTemporaryAccessListID);
					else
						$AccessType = manage_FormFields::GetFieldAccessTypeInFile($FieldsList[$i]->FormFieldID, $FileTypeUserPermittedFormID);
				}
				require_once('FormLabels.class.php');
				if($AccessType=="EDITABLE")
					$FormHasEditableField = true;
	
				// برچسبهایی که باید قبل از فیلد جاری قرار گیرند	
				$labels = manage_FormLabels::GetList(" LocationType='BEFORE' and RelatedFieldID='".$FieldsList[$i]->FormFieldID."' ");
				for($j=0; $j<count($labels); $j++)
				{
					$ret .= "<tr id='tr_label_".$labels[$j]->FormsLabelID."' ><td width=1%>&nbsp;</td>";
					$ret .= "<td colspan=2>";
					if($labels[$j]->ShowType=="BOLD")
						$ret .= "<b>";
					else if($labels[$j]->ShowType=="ITALIC")
						$ret .= "<i>";
	
					if($CurForm->IsQuestionnaire=="NO")
					{
						$labels[$j]->LabelDescription = str_replace("CreatorLastName", $CreatorLastName, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorFirstName", $CreatorFirstName, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorAddress", $CreatorAddress, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorTel", $CreatorTel, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorMobile", $CreatorMobile, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorEmail", $CreatorEmail, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorPostName", $CreatorPostName, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorNationalCode", $CreatorNationalCode, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("StudentNumber", $CreatorStNo, $labels[$j]->LabelDescription);
					}
					$ret .= $labels[$j]->LabelDescription;
					if($labels[$j]->ShowType=="BOLD")
						$ret .= "</b>";
					else if($labels[$j]->ShowType=="ITALIC")
						$ret .= "</i>";
					$ret .= "<span id='Label_".$labels[$j]->FormsLabelID."' name='Label_".$labels[$j]->FormsLabelID."'></span>";
					if($labels[$j]->ShowHorizontalLine=="YES")
						$ret .= "<hr>";
					$ret .= "</td>";
					$ret .= "<tr>";
				}
				
				if($AccessType!="HIDE")
				{
					$ret .= "<tr id='tr_".$FieldsList[$i]->FormFieldID."' style='display: '>";
					$ret .= "<td width=1%>";
					if($FieldsList[$i]->FieldInputType=="MANDATORY")
						$ret .= "<font color=red>*</font>";
					$ret .= "</td>";
					if($FieldsList[$i]->FieldType!="8") // اگر فیلد از نوع چک باکس نباشد اول عنوان فیلد نشان داده می شود و بعد خود فیلد
					{
						if($CurForm->ShowType=="2COLS")
						{
							if($CurForm->QuestionColumnWidth!="")
								$ret .= "<td  width='".$CurForm->QuestionColumnWidth."' ".$ExtraStyle.">&nbsp;";//start21
							else
								$ret .= "<td width=1% nowrap ".$ExtraStyle.">&nbsp;";//start21
						}
						else
						{
							$ret .= "<td id='td_".$FieldsList[$i]->FormFieldID."' ".$ExtraStyle.">";//start1
						}
						$ret .= $FieldsList[$i]->FieldTitle;
						if($CurForm->ShowType=="2COLS")
						{
							// اگر عنوان خالی بود یعنی کاربر نمی خواهد عنوان دیده شود بنابراین : هم نباید گذاشته شود
							if($FieldsList[$i]->FieldTitle!="")
								$ret .= ":";
							$ret .= "&nbsp;</td>";//end21
							$ret .= "<td id='td_".$FieldsList[$i]->FormFieldID."' ".$ExtraStyle.">";//start22
						}
						else
							$ret .= ": ";
			
						// در صورتیکه نوع متغیر فایل یا تصویر باشد به عنوان مقدار نام فایل یا تصویر را استفاده می کند - البته در صورتیکه این مقدار ذخیره شده باشد
						if($FieldsList[$i]->FieldType=="5" || $FieldsList[$i]->FieldType=="6")
						{
							if($RelatedRecordID!="0")
							{
							// در صورتیکه نام فایل وارد شده در دیتابیس ذخیره شده باشد آن را به عنوان نام فایل پاس می کند
								if($FieldsList[$i]->RelatedFileNameField!="")
									$FieldValue = $rec[$FieldsList[$i]->RelatedFileNameField];
							}
						}
						$ret .= $FieldsList[$i]->CreateUserInterface($AccessType, $FieldValue, $RelatedRecordID);
					}
					else
					{
						$ret .= "<td colspan=2 >";
						$ret .= $FieldsList[$i]->CreateUserInterface($AccessType, $FieldValue, $RelatedRecordID);
						$ret .= $FieldsList[$i]->FieldTitle;					
					}
					$ret .= "</td>";//end1 && end22
					
					$ret .= "</tr>";
				}
	
				// برچسبهایی که بعد از فیلد جاری باید نمایش داده شوند
				$labels = manage_FormLabels::GetList(" LocationType='AFTER' and RelatedFieldID='".$FieldsList[$i]->FormFieldID."' ");
				for($j=0; $j<count($labels); $j++)
				{
					$ret .= "<tr id='tr_label_".$labels[$j]->FormsLabelID."'><td width=1%>&nbsp;</td>";
					$ret .= "<td colspan=2>";
					if($labels[$j]->ShowType=="BOLD")
						$ret .= "<b>";
					else if($labels[$j]->ShowType=="ITALIC")
						$ret .= "<i>";
					if($CurForm->IsQuestionnaire=="NO")
					{
						$labels[$j]->LabelDescription = str_replace("CreatorLastName", $CreatorLastName, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorFirstName", $CreatorFirstName, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorAddress", $CreatorAddress, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorTel", $CreatorTel, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorMobile", $CreatorMobile, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorEmail", $CreatorEmail, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorPostName", $CreatorPostName, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorNationalCode", $CreatorNationalCode, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("CreatorEduSecName", $CreatorSecName, $labels[$j]->LabelDescription);
						$labels[$j]->LabelDescription = str_replace("StudentNumber", $CreatorStNo, $labels[$j]->LabelDescription);
					}
					$ret .= $labels[$j]->LabelDescription;
					if($labels[$j]->ShowType=="BOLD")
						$ret .= "</b>";
					else if($labels[$j]->ShowType=="ITALIC")
						$ret .= "</i>";
					$ret .= "<span id='Label_".$labels[$j]->FormsLabelID."' name='Label_".$labels[$j]->FormsLabelID."'></span>";
					if($labels[$j]->ShowHorizontalLine=="YES")
						$ret .= "<hr>";
					$ret .= "</td>";
					$ret .= "<tr>";
				}
				
			}
			$ret .= "\r\n		</table>\r\n";
			$ret .= str_replace("\n", "<br>", $slist[$l]->FooterDesc);
			$ret .= "	</td>\r\n</tr>\r\n";
		}
		
		$ret .= "</table>";
		$ret .= $CurForm->ButtomDescription;
		$ret .= "</td>";
		$ret .= "</tr>";
		/*
		$ret .= "<tr>\r\n";
		$ret .= "<td colspan=2>\r\n";
		$ret .= "<table width=100%><tr><td width=10%>&nbsp;";
		$ret .= "<a href='#' onclick='ShowTab(CurrentTabID)'><img border=0 src='images/pre.jpeg'> بخش قبل</a>";
		$ret .= "</td><td>&nbsp;</td><td width=10% align=left>";
		$ret .= "بخش بعد <img src='images/next.jpeg'>";
		$ret .= "</td></tr></table>\r\n";
		$ret .= "</td>\r\n";
		$ret .= "</tr>\r\n";
		*/
		if($RelatedRecordID=="0")
		{
			$NextSteps = manage_FormsFlowStepRelations::GetRelatedStepsForAnStartStep($StepID, $PersonID);
		}
		else
		{
			$NextSteps = manage_FormsFlowStepRelations::GetRelatedSteps($StepID, $RelatedRecordID);
			/*
			if($_SESSION["PersonID"]=="201309")
			{
				for($i=0; $i<count($NextSteps); $i++)
				{
					echo $NextSteps[$i]->FormsFlowStepID."<br>";
				}
			}
			*/
		}
		$ret .= "<tr>";
		$ret .= "<td align=center class=FooterOfTable>";
		
		$DetailList = manage_FormsDetailTables::GetList($CurForm->FormsStructID);
		
		// تعداد مراحل بعد از این مرحله را بدست می آورد
		$FlowingStepCount = 0;
		for($i=0; $i<count($NextSteps); $i++)
			if($NextSteps[$i]->PreviousStep==0)
				$FlowingStepCount++;
			
		//  در صورتیکه این فرم دارای جداول جزییاتی باشد باید قبل از ارسال به مرحله بعد آنها را نیز پر کند
		// بنابراین نباید امکان انتخاب به مرحله دیگر در اینجا فراهم بیاید 
		// و باید بعد از ذخیره به صفحه ویرایش برود
		// در حالت ویرایش هم که همیشه می تواند مرحله بعد یا قبل را انتخاب کند
		if($RelatedRecordID>0 || count($DetailList)==0)
		{
			// در صورتیکه از این مرحله به مرحله دیگری ارتباط نداشته باشیم یعنی تک مرحله باشد فقط کلید ذخیره را نشان خواهد داد
			// یا اینکه داده در یک جدول جزییات وارد شده باشد
			if($FileID!=0 || count($NextSteps)==0 || $MasterRecordID>0)
			{
				if($CurForm->IsQuestionnaire=="NO")
					$ret .= "<input type=button name=Send value='ذخیره' onclick='javascript: document.f1.ActionType.value=\"SEND\"; CheckValidity();'>";
				else
				{
					$ret .= "<input type=button name=Send value='ذخیره موقت' onclick='javascript: { document.f1.ActionType.value=\"SEND\"; document.f1.submit(); }'>";
					$ret .= "&nbsp;";
					$ret .= "<input type=button name=Send value='ذخیره و تایید نهایی' onclick='javascript: if(confirm(\"با ارسال پرسشنامه دیگر امکان ویرایش آن را نخواهید داشت. از ارسال مطمئن هستید؟\")) { document.f1.ActionType.value=\"CONFIRM\"; CheckValidity(); }'>";
				}
				$ret .= "<input type=hidden name=NewStepID id=NewStepID value='".$StepID."'>";
			}
			else
			{
					
				// در صورتیکه برای این مرحله مراحلی بعدی تعریف شده بود یا اینکه امکان برگشت به مراحل قبل را داشت
				if($FlowingStepCount>0 || $CurStep->UserCanBackward=="YES")
				{
					$ret .= "<select name=NewStepID id=NewStepID>";
					$ret .= "<option value='0'>ذخیره";
					for($i=0; $i<count($NextSteps); $i++)
					{
						if($NextSteps[$i]->StepTitle!="")
						{
							if($NextSteps[$i]->PreviousStep==0)
								$ret .= "<option value='".$NextSteps[$i]->FormsFlowStepID."'>ذخیره و ارسال به ".$NextSteps[$i]->StepTitle;
							else if($CurStep->UserCanBackward=="YES")
								$ret .= "<option value='".$NextSteps[$i]->FormsFlowStepID."'>ذخیره و برگشت به ".$NextSteps[$i]->StepTitle;
						}
					}
					$ret .= "</select>";
				}
				else
				{
					$ret .= "<input type=hidden name=NewStepID id=NewStepID value='".$StepID."'>";
				}

				// اگر فرم امکان ارسال به مراحل دیگر را داشت یا اینکه آیتم قابل ویرایشی در فرم وجود داشت
				if($FormHasEditableField || $FlowingStepCount>0 || $CurStep->UserCanBackward=="YES")
					$ret .= "<input type=button name=Send value='اعمال' onclick='javascript: document.f1.ActionType.value=\"SEND\"; CheckValidity();'>";
				
			}
		}
		else
		{		
			$ret .= "<input type=button name=Send value='ذخیره داده های اصلی و نمایش صفحه ویرایش برای ورود داده های جداول وابسته' onclick='javascript: document.f1.ActionType.value=\"SAVE_GO_EDIT\"; CheckValidity();'>";
		}
		if($RelatedRecordID>0)
		{
			 if($CurForm->IsQuestionnaire=="NO")
				$ret .= "&nbsp;<input type=button value='سابقه' onclick='javascript: window.open(\"ShowFormFlowHistory.php?SelectedFormStructID=".$FormsStructID."&RelatedRecordID=".$RelatedRecordID."\");'>";
			if($MasterFormsStructID==0)
			{
				if($CurForm->PrintType=="DEFAULT")
				{
					if($FileTemporaryAccessListID > 0)
					{
						// صفحه چاپ مربوط به فرمهای پرونده های امانتی
						$ret .= "&nbsp;<input type=button value='چاپ' onclick='javascript: window.open(\"PrintTempFileFormData.php?FileTemporaryAccessListID=".$FileTemporaryAccessListID."&SelectedFormStructID=".$FormsStructID."&RelatedRecordID=".$RelatedRecordID."\");'>";
					}
					else if ($FileTypeUserPermittedFormID > 0)
					{
						// صفحه چاپ مربوط به فرمهای پرونده ها
						$ret .= "&nbsp;<input type=button value='چاپ' onclick='javascript: window.open(\"PrintFileFormData.php?FileTypeUserPermittedFormID=".$FileTypeUserPermittedFormID."&SelectedFormStructID=".$FormsStructID."&RelatedRecordID=".$RelatedRecordID."\");'>"; 
					}
					else
					{
						if($CurForm->IsQuestionnaire=="NO")
							$ret .= "&nbsp;<input type=button value='چاپ' onclick='javascript: window.open(\"PrintFormData.php?SelectedFormStructID=".$FormsStructID."&RelatedRecordID=".$RelatedRecordID."\");'>";
						else
							$ret .= "&nbsp;<input type=button value='چاپ' onclick='javascript: window.open(\"PrintQuestionnaireData.php?SelectedFormStructID=".$FormsStructID."&RelatedRecordID=".$RelatedRecordID."\");'>";
					}
				}
				else
					$ret .= "&nbsp;<input type=button value='چاپ' onclick='javascript: window.open(\"".$CurForm->PrintPageAddress."?RecID=".$RelatedRecordID."\");'>";
			}
		}
		$ret .= "</td>";
		$ret .= "</tr>";
		$ret .= "</table>";
		$ret .= "</form>";
		
		if($RelatedRecordID>0 && count($DetailList))
		{
			//$CurDetail = new be_FormsDetailTables();
			for($i=0; $i<count($DetailList); $i++)
			{
				//$ret .= $DetailList[$i]->FormTitle;
				$IFrameName = "iDetail_".$DetailList[$i]->DetailFormStructID;
				if($CurForm->IsQuestionnaire=="NO")
					$DetailPageAddress = "ShowDetailTable.php?StepID=".$StepID."&RelatedRecordID=".$RelatedRecordID."&SelectedFormStructID=".$FormsStructID."&DetailFormID=".$DetailList[$i]->DetailFormStructID;
				else
					$DetailPageAddress = "ShowQuestionnaireDetailTable.php?StepID=".$StepID."&RelatedRecordID=".$RelatedRecordID."&SelectedFormStructID=".$FormsStructID."&DetailFormID=".$DetailList[$i]->DetailFormStructID;
				$ret .= "<br>";				
				$ret .= "<table width=90% cellspacing=0 cellpadding=5 border=1 align=center>";
				$ret .= "<tr class=HeaderOfTable><td>".$DetailList[$i]->FormTitle."</td></tr>";
				$ret .= "<tr><td>";
				$ret .= "<iframe src='".$DetailPageAddress."' id='".$IFrameName."' name='".$IFrameName."' width=100% height=50 align=center style='border: 0'></iframe>";
				$ret .= "</td></tr>";
				$ret .= "</table>";

			}
		}
		
		$ret .= manage_FormsStruct::CreateCheckValidityFunction($FormsStructID, $StepID, $PersonID);
		$ret .= "<script>\r\n";
		//$ret .= "setTimeout(\"ShowTab('".$slist[0]->FormsSectionID."')\",50);\r\n";
                if($slist[0]->FormsSectionID>=0)
                    $ret .= "ShowTab('".$slist[0]->FormsSectionID."');\r\n";
		$ret .= $CurForm->JavascriptCode;
		$ret .= "</script>";
		if($ExtOnLoad!="")
		{
			$header = "";
			$header = "<link rel=\"stylesheet\" type=\"text/css\" href=\"/sharedClasses/resources/css/ext-all.css\" />\r\n";
			$header .= "<script type=\"text/javascript\" src=\"/sharedClasses/resources/adapter/ext/ext-base.js\"></script>\r\n";
			$header .= "<script type=\"text/javascript\" src=\"/sharedClasses/resources/ext-all.js\"></script>\r\n";
			$header .= "<script>";
			$header .= "Ext.QuickTips.init();\r\n";
			$header .= "Ext.onReady(function(){\r\n";
			$header .= $ExtOnLoad;
			$header .= "})\r\n";
			$header .= "</script>";
			$ret = $header.$ret;
		}
		return $ret;
	}

	
	// نسخه مناسب چاپ از اطلاعات فرم را با توجه به مرحله مربوطه
	// RelatedRecordID: کد رکورد مقادیر فرم می باشد 
	// StepID: کد مرحله مربوطه 
	// MasterFormsStructID: چنانچه فرم جاری جزو جداول جزییات یک فرم دیگر بود باید این مقدار هم به متد پاس شود و برای حالت عادی به صورت پیش فرض صفر است یعنی جدول اصلی است
	// masterRecordID: کد رکورد اصلی - در صورتیکه فرم جاری یک جدول جزییات باشد
	// توجه: اگر کد مرحله منهای یک پاس شود یعنی جدول بدون مرحله می باشد و نباید دسترسیها چک شود	 
	// پارامترهای ششم و هفتم مربوط به نمایش نسخه مناسب چاپ فرم در زمانیکه جزو یک پرونده الکترونیکی عادی یا امانتی است می باشد
	static function CreatePrintableVersion($FormsStructID, $StepID, $PersonID, $RelatedRecordID, $MasterFormsStructID = 0, $MasterRecordID = 0, $FileTypeUserPermittedFormID = 0, $FileTemporaryAccessListID = 0)
	{
		$ret = "";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$CurForm = new be_FormsStruct();
		$CurForm->LoadDataFromDatabase($FormsStructID);

		$CurStep = new be_FormsFlowSteps();
		$CurStep->LoadDataFromDatabase($StepID);
		
		$ret .= "<table width=90% align=center border=1 cellspacing=0 cellpadding=3>";
		
		if($CurStep->PrintPageTitle!="")
		{
			$ret .= "<tr>";
			$ret .= "<td colspan=2 align=center><b>".$CurStep->PrintPageTitle."</b></td>";
			$ret .= "</tr>";
		}
		if($CurStep->ShowBarcodeInPrintPage=="YES") // بارکد نشان داده شود
		{
			$ret .= "<tr>";
			$ret .= "<td colspan=2>کد شناسایی: <img src='../shares/barcode.php?barcode=*".($RelatedRecordID+6000000)."*&width=300'></td>";
			$ret .= "</tr>";
		}
		$ret .= "<tr>";
		$ret .= "<td>";
		$ret .= str_replace("\n", "<br>", $CurForm->TopDescription);
		$ret .= str_replace("\n", "<br>", $CurStep->PrintPageHeader);
		$ret .= "<table width=100% border=0>";

		$CreatorLastName = $CreatorFirstName = $CreatorPersonType = $CreatorFacultyName = $CreatorUnitName = $CreatorEduGrpName = $CreatorPostName = "";
		$CreatorSecName = $CreatorNationalCode = "";
		// اگر فرم در مود ویرایش بود باید ایجاد کننده اصلی فرم مشخص شود
		if($RelatedRecordID>0)
		{
			$query = "SELECT * from formsgenerator.FormsRecords where RelatedRecordID='".$RelatedRecordID."' and FormsStructID='".$CurForm->FormsStructID."'";
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array());

			if($rec = $res->fetch())
			{
				$CreatorID = $rec["CreatorID"];
				$CreatorType = $rec["CreatorType"];
			}
		}

		if($CurForm->IsQuestionnaire=="NO")
		{
		
			// استخراج اطلاعات ایجاد کننده فرم از بانکهای اطلاعاتی برای جایگزینی در کلیدهای برچسبها		
			if($CreatorType=="PERSONEL")
				$query = "SELECT * from hrmstotal.persons 
									JOIN hrmstotal.staff using (PersonID, person_type)
									LEFT JOIN hrmstotal.org_units on (UnitCode=org_units.ouid)
									LEFT JOIN formsgenerator.EducationalGroups using (EduGrpCode)
									LEFT JOIN formsgenerator.faculties on (staff.FacCode=faculties.FacCode)
									LEFT JOIN hrmstotal.position using (post_id)  
									where persons.PersonID='".$CreatorID."'";
			else 
				$query = "SELECT * from educ.persons 
							JOIN educ.StudentSpecs using (PersonID)
							LEFT JOIN hrmstotal.org_units on (FacCode=org_units.ouid)
							LEFT JOIN educ.StudyFields using (FldCode)
							LEFT JOIN educ.EducationalSections using (EduSecCode)
							LEFT JOIN formsgenerator.EducationalGroups using (EduGrpCode)
							LEFT JOIN formsgenerator.faculties on (StudentSpecs.FacCode=faculties.FacCode)
							where persons.PersonID='".$CreatorID."'";

			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array());

			if($rec = $res->fetch())
			{
				if($CreatorType=="PERSONEL")
				{
	                            $CreatorLastName = $rec["plname"];
	                            $CreatorFirstName = $rec["pfname"];
	                        }
	                        else{
	                               $CreatorLastName = $rec["PLName"];
	                            $CreatorFirstName = $rec["PFName"];			
	                        }                          
	                        
				$CreatorPersonType = "";
				$CreatorStNo = "";
				if($CreatorType=="PERSONEL")
				{
					if($rec["person_type"]=="1")
						$CreatorPersonType = "هیات علمی رسمی/پیمانی";
					else if($rec["person_type"]=="2")
						$CreatorPersonType = "کارمند رسمی/پیمانی"; 
					else if($rec["person_type"]=="3")
						$CreatorPersonType = "کارمند روزمزد"; 
					else if($rec["person_type"]=="5" || $rec["person_type"]=="6")
						$CreatorPersonType = "کارمند قراردادی"; 
					else if($rec["person_type"]=="200")
						$CreatorPersonType = "هیات علمی حق التدریس";
					else if($rec["person_type"]=="300")
						$CreatorPersonType = "کارمند پیمانکار";
				}
				else
				{
					$CreatorPersonType = "دانشجو";
					$CreatorStNo = $rec["StNo"];
				} 
				$CreatorFacultyName = $rec["PFacName"];
				$CreatorUnitName = $rec["ptitle"];
				$CreatorEduGrpName = $rec["PEduName"];
				$CreatorAddress = $rec["address1"];
				$CreatorTel = $rec["home_phone1"];
				$CreatorMobile = $rec["mobile_phone"];
				$CreatorEmail = $rec["email"];
				if($CreatorType=="PERSONEL")
				{
					$CreatorNationalCode = $rec["national_code"];
					$CreatorPostname = $rec["title"];
				}
				else
				{
					$CreatorNationalCode = $rec["NID"];
					$CreatorPostname = "";
					$CreatorSecName = $rec["PEduSecName"];
				}
			}
		}		
		if($RelatedRecordID>0)
		{
			// در صورتیکه قرار بود اطلاعات رکوردی در فرم نمایش داده شود محتویات آن رکورد بارگذاری می شود
			$query = "SELECT * from ".$CurForm->RelatedDB.".".$CurForm->RelatedTable." where ".$CurForm->KeyFieldName."='".$RelatedRecordID."'";
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array());

			$rec = $res->fetch();
		}
				
		$FieldsList = manage_FormFields::GetList($FormsStructID, "OrderInInputForm");
	
		for($i=0; $i<count($FieldsList); $i++)
		{
			// اگر داده های مربوط به یک فرم از پیش موجود مد نظر باشد و نوع فیلد آن فایل یا تصویر نباشد 
			if($RelatedRecordID>0 && $FieldsList[$i]->FieldType!=5 && $FieldsList[$i]->FieldType!=6)
			{
				if($FieldsList[$i]->FieldType==7) // تاریخ شمسی
				{
					$tmp = shdate($rec[$FieldsList[$i]->RelatedFieldName]);
					$FieldValue = substr($tmp, 6, 2)."/".substr($tmp, 3, 2)."/".substr($tmp, 0, 2);
				} 
				else
				// در صورتیکه قرار بود محتویات رکوردی در فیلدهای فرم بارگذاری شود و نوع فیلد فایل یا تصویر نبود آنگاه:
					$FieldValue = $rec[$FieldsList[$i]->RelatedFieldName];
			}
			else
			{
				// در غیر اینصورت باید محتویات فیلد برابر مقدار پیش فرض تعیین شده توسط کاربر طراح فرم قرار گیرد
				// برخی از این مقادیر پیش فرض کلیدهایی هستند که با توجه به مقادیر سرور پر می شوند و بنابراین باید جایگزین شوند
				// مثلا کد کاربر جاری یا تاریخ جاری
				$FieldValue = FormUtils::GetValueAccordingToKeys($FieldsList[$i]->DefaultValue);
			}
		
			
			if($FileTemporaryAccessListID>0)
			{
				//کنترل دسترسی بر اساس فرم امانتی پرونده 
				$AccessType = manage_FormFields::GetFieldAccessTypeInTempFile($FieldsList[$i]->FormFieldID, $FileTemporaryAccessListID);
			}
			else if($FileTypeUserPermittedFormID>0)
			{
				// کنترل دسترسی بر اساس فرم یک پرونده الکترونیکی
				$AccessType = manage_FormFields::GetFieldAccessTypeInFile($FieldsList[$i]->FormFieldID, $FileTypeUserPermittedFormID);
			}
			else
			{
				// نوع دسترسی به فیلد با توجه به کد فیلد و کد مرحله آن استخراج می شود تا از آن در نحوه نمایش استفاده شود
				// در صورتیکه کد مرحله منهای یک ارسال شده باشد یعنی جدول بدون مرحله است و دسترسی تمام فیلدها ویرایش می باشد
				if($StepID==-1)
					$AccessType = "READ_ONLY";
				else
					$AccessType = manage_FormFields::GetFieldAccessType($FieldsList[$i]->FormFieldID, $StepID);
			}
			require_once('classes/FormLabels.class.php');
			$labels = manage_FormLabels::GetList(" LocationType='BEFORE' and RelatedFieldID='".$FieldsList[$i]->FormFieldID."' ");
		
			for($j=0; $j<count($labels); $j++)
			{
				$ret .= "<tr><td width=1%>&nbsp;</td>";
				$ret .= "<td colspan=2>";
				if($labels[$j]->ShowType=="BOLD")
					$ret .= "<b>";
				else if($labels[$j]->ShowType=="ITALIC")
					$ret .= "<i>";

				if($CurForm->IsQuestionnaire=="NO")
				{
					$labels[$j]->LabelDescription = str_replace("CreatorLastName", $CreatorLastName, $labels[$j]->LabelDescription);
					$labels[$j]->LabelDescription = str_replace("CreatorFirstName", $CreatorFirstName, $labels[$j]->LabelDescription);
					$labels[$j]->LabelDescription = str_replace("CreatorAddress", $CreatorAddress, $labels[$j]->LabelDescription);
					$labels[$j]->LabelDescription = str_replace("CreatorTel", $CreatorTel, $labels[$j]->LabelDescription);
					$labels[$j]->LabelDescription = str_replace("CreatorMobile", $CreatorMobile, $labels[$j]->LabelDescription);
					$labels[$j]->LabelDescription = str_replace("CreatorEmail", $CreatorEmail, $labels[$j]->LabelDescription);
					$labels[$j]->LabelDescription = str_replace("CreatorPostName", $CreatorPostName, $labels[$j]->LabelDescription);
					$labels[$j]->LabelDescription = str_replace("CreatorNationalCode", $CreatorNationalCode, $labels[$j]->LabelDescription);
					$labels[$j]->LabelDescription = str_replace("StudentNumber", $CreatorStNo, $labels[$j]->LabelDescription);
				}				
				$ret .= $labels[$j]->LabelDescription;
				if($labels[$j]->ShowType=="BOLD")
					$ret .= "</b>";
				else if($labels[$j]->ShowType=="ITALIC")
					$ret .= "</i>";
				$ret .= "<span id='Label_".$labels[$j]->FormsLabelID."' name='Label_".$labels[$j]->FormsLabelID."'></span>";
				if($labels[$j]->ShowHorizontalLine=="YES")
					$ret .= "<hr>";
				$ret .= "</td>";
				$ret .= "<tr>";
			}
		
			if($AccessType!="HIDE")
			{

				$ret .= "<tr>";
				$ret .= "<td width=1%>";
				if($FieldsList[$i]->FieldInputType=="MANDATORY")
					$ret .= "<font color=red>*</font>";
				$ret .= "</td>";
				$ret .= "<td width=1% nowrap>&nbsp;";
				$ret .= $FieldsList[$i]->FieldTitle;
				$ret .= ":&nbsp;</td>";
				$ret .= "<td>";
				// در صورتیکه نوع متغیر فایل یا تصویر باشد به عنوان مقدار نام فایل یا تصویر را استفاده می کند - البته در صورتیکه این مقدار ذخیره شده باشد
				if($FieldsList[$i]->FieldType=="5" || $FieldsList[$i]->FieldType=="6")
				{
					if($RelatedRecordID!="0")
					{
					// در صورتیکه نام فایل وارد شده در دیتابیس ذخیره شده باشد آن را به عنوان نام فایل پاس می کند
						if($FieldsList[$i]->RelatedFileNameField!="")
							$FieldValue = $rec[$FieldsList[$i]->RelatedFileNameField];
					}
				}
				$ret .= $FieldsList[$i]->CreateUserInterface("READ_ONLY", $FieldValue, $RelatedRecordID);
				$ret .= "</td>";
				$ret .= "</tr>";
			}
			$labels = manage_FormLabels::GetList(" LocationType='AFTER' and RelatedFieldID='".$FieldsList[$i]->FormFieldID."' ");
			for($j=0; $j<count($labels); $j++)
			{
				$ret .= "<tr><td width=1%>&nbsp;</td>";
				$ret .= "<td colspan=2>";
				if($labels[$j]->ShowType=="BOLD")
					$ret .= "<b>";
				else if($labels[$j]->ShowType=="ITALIC")
					$ret .= "<i>";
				$labels[$j]->LabelDescription = str_replace("CreatorLastName", $CreatorLastName, $labels[$j]->LabelDescription);
				$labels[$j]->LabelDescription = str_replace("CreatorFirstName", $CreatorFirstName, $labels[$j]->LabelDescription);
				$labels[$j]->LabelDescription = str_replace("CreatorAddress", $CreatorAddress, $labels[$j]->LabelDescription);
				$labels[$j]->LabelDescription = str_replace("CreatorTel", $CreatorTel, $labels[$j]->LabelDescription);
				$labels[$j]->LabelDescription = str_replace("CreatorMobile", $CreatorMobile, $labels[$j]->LabelDescription);
				$labels[$j]->LabelDescription = str_replace("CreatorEmail", $CreatorEmail, $labels[$j]->LabelDescription);
				$labels[$j]->LabelDescription = str_replace("CreatorPostName", $CreatorPostName, $labels[$j]->LabelDescription);
				$labels[$j]->LabelDescription = str_replace("CreatorNationalCode", $CreatorNationalCode, $labels[$j]->LabelDescription);
				$labels[$j]->LabelDescription = str_replace("StudentNumber", $CreatorStNo, $labels[$j]->LabelDescription);
										
				$ret .= $labels[$j]->LabelDescription;
				if($labels[$j]->ShowType=="BOLD")
					$ret .= "</b>";
				else if($labels[$j]->ShowType=="ITALIC")
					$ret .= "</i>";
				$ret .= "<span id='Label_".$labels[$j]->FormsLabelID."' name='Label_".$labels[$j]->FormsLabelID."'></span>";
				if($labels[$j]->ShowHorizontalLine=="YES")
					$ret .= "<hr>";
				$ret .= "</td>";
				$ret .= "<tr>";
			}
			
		}
		$ret .= "</table>";
		$ret .= str_replace("\n", "<br>", $CurForm->ButtomDescription);
		$ret .= str_replace("\n", "<br>", $CurStep->PrintPageFooter);
		if($CurStep->PrintPageSigniture!="NO" && $CurForm->IsQuestionnaire=="NO")
		{
			$ret .= "<br><br><p align=left><b>";
			$ret .= "امضا&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>";
			if($CurStep->PrintPageSigniture=="WITH_NAME" && isset($_SESSION["PersonID"]))
			{

				$mysql->Prepare("select * from hrmstotal.persons where PersonID='".$_SESSION["PersonID"]."'");
				$tmp = $mysql->ExecuteStatement(array());
				$trec = $tmp->fetch();
				$ret .= $trec["pfname"]." ".$trec["plname"];
				$ret .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			$ret .= "<br><br><br>";
		}
		if($CurStep->ShowHistoryInPrintPage=="YES")
		{
			$ret .= FormUtils::ShowFormFlowHistory($FormsStructID, $RelatedRecordID);
		}
		$ret .= "</td>";
		$ret .= "</tr>";
		$NextSteps = manage_FormsFlowStepRelations::GetRelatedSteps($StepID, $RelatedRecordID);
		$ret .= "</table>";
		$ret .= "</form>";
		$DetailList = manage_FormsDetailTables::GetList($CurForm->FormsStructID);
		if($RelatedRecordID>0 && count($DetailList))
		{
			//$CurDetail = new be_FormsDetailTables();
			for($i=0; $i<count($DetailList); $i++)
			{
				//$ret .= $DetailList[$i]->FormTitle;
				$IFrameName = "iDetail_".$DetailList[$i]->DetailFormStructID;
				if($CurForm->IsQuestionnaire=="NO")
					$DetailPageAddress = "ShowDetailTable.php?ForPrint=1&StepID=".$StepID."&RelatedRecordID=".$RelatedRecordID."&SelectedFormStructID=".$FormsStructID."&DetailFormID=".$DetailList[$i]->DetailFormStructID;
				else
					$DetailPageAddress = "ShowQuestionnaireDetailTable.php?ForPrint=1&StepID=".$StepID."&RelatedRecordID=".$RelatedRecordID."&SelectedFormStructID=".$FormsStructID."&DetailFormID=".$DetailList[$i]->DetailFormStructID;
				$ret .= "<br>";				
				$ret .= "<table width=90% cellspacing=0 cellpadding=5 border=1 align=center>";
				$ret .= "<tr class=HeaderOfTable><td>".$DetailList[$i]->FormTitle."</td></tr>";
				$ret .= "<tr><td>";
				$ret .= "<iframe src='".$DetailPageAddress."' id='".$IFrameName."' name='".$IFrameName."' width=100% height=50 align=center style='border: 0'></iframe>";
				$ret .= "</td></tr>";
				$ret .= "</table>";

			}
		}
		return $ret;
	}
	
	// کد جاوا اسکریپت برای کنترل صحت داده های ورودی در فرم و سپس ارسال فرم را تهیه می کند
	static function CreateCheckValidityFunction($FormsStructID, $StepID, $PersonID)
	{
		$ret = "<script>\r\n";
		$ret .= "function CheckValidity()\r\n";
		$ret .= "{\r\n";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$CurForm = new be_FormsStruct();
		$CurForm->LoadDataFromDatabase($FormsStructID);
		$FieldsList = manage_FormFields::GetList($FormsStructID, "OrderInInputForm");
		for($i=0; $i<count($FieldsList); $i++)
		{
			$AccessType = manage_FormFields::GetFieldAccessType($FieldsList[$i]->FormFieldID, $StepID);
			if($AccessType=="EDITABLE")
			{
				$ret .= $FieldsList[$i]->CreateCheckvalidityJavascriptCode();
			}
		}
		$ret .= "\r\n";
		$ret .= $CurForm->ValidationExtraJavaScript."\r\n";
		$ret .= "document.f1.submit();\r\n";
		$ret .= "}\r\n";
		$ret .= "</script>\r\n";
		return $ret;
	}

	// مخصوص فرمهای داخل پرونده: کد جاوا اسکریپت برای کنترل صحت داده های ورودی در فرم و سپس ارسال فرم را تهیه می کند
	static function CreateCheckValidityFunctionForFile($FormsStructID, $FileTypeUserPermittedFormID, $PersonID)
	{
		$ret = "<script>\r\n";
		$ret .= "function CheckValidity()\r\n";
		$ret .= "{\r\n";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$CurForm = new be_FormsStruct();
		$CurForm->LoadDataFromDatabase($FormsStructID);
		$FieldsList = manage_FormFields::GetList($FormsStructID, "OrderInInputForm");
		for($i=0; $i<count($FieldsList); $i++)
		{
			$AccessType = manage_FormFields::GetFieldAccessTypeInFile($FieldsList[$i]->FormFieldID, $FileTypeUserPermittedFormID);
			if($AccessType=="EDITABLE")
			{
				$ret .= $FieldsList[$i]->CreateCheckvalidityJavascriptCode();
			}
		}
		$ret .= "\r\n";
		$ret .= $CurForm->ValidationExtraJavaScript."\r\n";
		$ret .= "document.f1.submit();\r\n";
		$ret .= "}\r\n";
		$ret .= "</script>\r\n";
		return $ret;
	}

	// مخصوص فرمهای داخل پرونده های امانتی: کد جاوا اسکریپت برای کنترل صحت داده های ورودی در فرم و سپس ارسال فرم را تهیه می کند
	static function CreateCheckValidityFunctionForTempFile($FormsStructID, $FileTemporaryAccessListID, $PersonID)
	{
		$ret = "<script>\r\n";
		$ret .= "function CheckValidity()\r\n";
		$ret .= "{\r\n";
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$CurForm = new be_FormsStruct();
		$CurForm->LoadDataFromDatabase($FormsStructID);
		$FieldsList = manage_FormFields::GetList($FormsStructID, "OrderInInputForm");
		for($i=0; $i<count($FieldsList); $i++)
		{
			$AccessType = manage_FormFields::GetFieldAccessTypeInTempFile($FieldsList[$i]->FormFieldID, $FileTemporaryAccessListID);
			if($AccessType=="EDITABLE")
			{
				$ret .= $FieldsList[$i]->CreateCheckvalidityJavascriptCode();
			}
		}
		$ret .= "\r\n";
		$ret .= $CurForm->ValidationExtraJavaScript."\r\n";
		$ret .= "document.f1.submit();\r\n";
		$ret .= "}\r\n";
		$ret .= "</script>\r\n";
		return $ret;
	}

	public function HasUserAccessToThisQuestionnaire($FormsStructID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$CurForm = new be_FormsStruct();
		$CurForm->LoadDataFromDatabase($FormsStructID);
		if($CurForm->IsQuestionnaire=="NO")
			return false;
		$mysql->Prepare("select * from formsgenerator.TemporaryUsersAccessForms JOIN formsgenerator.TemporaryUsers using (WebUserID) where TemporaryUsersAccessForms.WebUserID='".$_SESSION["UserID"]."' and FormsStructID='".$CurForm->FormsStructID."' and UserStatus='ENABLE'");
		$res = $mysql->ExecuteStatement(array());
		if($rec = $res->fetch())
		{
			return true;
		}
		return false;
	}
	
	public function HasUserFilledThisQuestionnaire($FormsStructID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$CurForm = new be_FormsStruct();
		$CurForm->LoadDataFromDatabase($FormsStructID);
		if($CurForm->IsQuestionnaire=="NO")
			return false;
		$mysql->Prepare("select * from formsgenerator.QuestionnairesCreators where UserID='".$_SESSION["UserID"]."' and FormsStructID='".$CurForm->FormsStructID."'");
		$res = $mysql->ExecuteStatement(array());
		if($rec = $res->fetch())
		{
			return true;
		}
		return false;
	}

	public function HasUserCreateThisQuestionnaire($FormsStructID, $RecID)
	{
		$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
		$CurForm = new be_FormsStruct();
		$CurForm->LoadDataFromDatabase($FormsStructID);
		if($CurForm->IsQuestionnaire=="NO")
			return false;
		//echo "select * from formsgenerator.QuestionnairesCreators where UserID='".$_SESSION["UserID"]."' and FormsStructID='".$CurForm->FormsStructID."' and RelatedRecordID='".$RecID."'";
		$mysql->Prepare("select * from formsgenerator.QuestionnairesCreators where UserID='".$_SESSION["UserID"]."' and FormsStructID='".$CurForm->FormsStructID."' and RelatedRecordID='".$RecID."'");
		$res = $mysql->ExecuteStatement(array());
		if($rec = $res->fetch())
		{
			return true;
		}
		return false;
	}
	
}
?>
