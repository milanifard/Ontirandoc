<?php
include("header.inc.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FormsStruct.class.php");
include_once("../organization/classes/OrganizationCharts.class.php");
include_once("../sharedClasses/SharedClass.class.php");

HTMLBegin();

function GetCount($StepID)
{
	$mysql = dbclass::getInstance();
	$res = $mysql->Execute("select count(*) from FormsRecords where FormFlowStepID='".$StepID."'");
	if($rec = $res->FetchRow())
		return $rec[0];
	return 0;
}

$ParentObj = new be_FormsStruct();
$ParentObj->LoadDataFromDatabase($_REQUEST["Item_FormStructID"]);
if($ParentObj->CreatorUser!=$_SESSION["UserID"] && !$ParentObj->HasThisPersonAccessToManageStruct($_SESSION["PersonID"]))
{
	echo "You don't have permission";
	die();
}
function ShowTick($Value)
{
	if($Value=="ALLOW" || $Value=="YES" || $Value=="START")
	{
		return "<span style=\"vertical-align:middle;\">X</span>";
	}
	return '&nbsp;';
}

function ShowFilterTypeName($Value)
{
	if($Value=="NO_FILTER")
		return "همه";
	if($Value=="UNITS")
		return "بعضی واحدها";
	if($Value=="SUB_UNITS")
		return "بعضی زیر واحدها";
	if($Value=="EDU_GROUPS")
		return "بعضی گروه ها";
}

function ShowRangeName($Value)
{
	if($Value=="HIM")
		return "خود کاربر";
	if($Value=="UNIT")
		return "واحد ";
	if($Value=="SUB_UNIT")
		return "زیر واحد ";
	if($Value=="EDU_GROUP")
		return "گروه آموزشی";
	if($Value=="BELOW_IN_CHART_ALL_LEVEL")
		return "زیر مجموعه در تمام سطوح";
	if($Value=="BELOW_IN_CHART_LEVEL1")
		return "زیر مجموعه سطح اول";
	if($Value=="BELOW_IN_CHART_LEVEL2")
		return "زیر مجموعه سطح دوم";
	if($Value=="BELOW_IN_CHART_LEVEL3")
		return "زیر مجموعه سطح سوم";
	if($Value=="UNDER_MANAGEMENT")
		return "زیر مجموعه تحت مدیریت";
	return "همه";	
}	

function IfTheseAreEqualShowTick($s1, $st2)
{
	if($st1==$st2)
		return "<span style=\"vertical-align:middle;\">X</span>";
	else
		return "&nbsp;";
}

if(isset($_REQUEST["Save"]))
{
	$Item_StartDate = SharedClass::ConvertToMiladi($_REQUEST["StartDate_YEAR"], $_REQUEST["StartDate_MONTH"], $_REQUEST["StartDate_DAY"]);
	$Item_EndDate = SharedClass::ConvertToMiladi($_REQUEST["EndDate_YEAR"], $_REQUEST["EndDate_MONTH"], $_REQUEST["EndDate_DAY"]);
	
	$Item_StepType = "OTHER";
	$Item_StudentPortalAccess = "DENY";
	$Item_StaffPortalAccess = "DENY";
	$Item_ProfPortalAccess = "DENY";
	$Item_OtherPortalAccess = "DENY";
	$Item_FilterOnUserRoles = "NO";
	$Item_FilterOnSpecifiedUsers = "NO";
	
	$Item_StepType = $_REQUEST["Item_StepType"];

	if(isset($_REQUEST["Item_StudentPortalAccess"]))
		$Item_StudentPortalAccess = "ALLOW";
	if(isset($_REQUEST["Item_StaffPortalAccess"]))
		$Item_StaffPortalAccess = "ALLOW";
	if(isset($_REQUEST["Item_ProfPortalAccess"]))
		$Item_ProfPortalAccess = "ALLOW";
	if(isset($_REQUEST["Item_OtherPortalAccess"]))
		$Item_OtherPortalAccess = "ALLOW";
	
		
	if(isset($_REQUEST["Item_FilterOnUserRoles"]))
		$Item_FilterOnUserRoles = "YES";
	if(isset($_REQUEST["Item_FilterOnSpecifiedUsers"]))
		$Item_FilterOnSpecifiedUsers = "YES";
		
		
		
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FormsFlowSteps::Add($_REQUEST["Item_FormStructID"]
				, $_REQUEST["Item_StepTitle"]
				, $Item_StepType
				, $Item_StudentPortalAccess
				, $Item_StaffPortalAccess
				, $Item_ProfPortalAccess
				, $Item_OtherPortalAccess
				, $_REQUEST["Item_FilterType"]
				, $Item_FilterOnUserRoles
				, $Item_FilterOnSpecifiedUsers
				, $_REQUEST["Item_UserAccessRange"]
				, $_REQUEST["Item_RelatedOrganzationChartID"]
				, $_REQUEST["Item_AccessRangeRelatedPersonType"]
				
				, $_REQUEST["Item_ShowBarcodeInPrintPage"]
				, $_REQUEST["Item_UserCanBackward"]
				, $_REQUEST["Item_PrintPageHeader"]
				, $_REQUEST["Item_PrintPageFooter"]
				, $_REQUEST["Item_PrintPageTitle"]
				, $_REQUEST["Item_PrintPageSigniture"]
				, $_REQUEST["Item_ShowHistoryInPrintPage"]
				, $_REQUEST["Item_NumberOfPermittedSend"]
				, "FOREVER"
				, $Item_StartDate
				, $Item_EndDate
				);
	}	
	else 
	{	
		manage_FormsFlowSteps::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_StepTitle"]
				, $Item_StepType
				, $Item_StudentPortalAccess
				, $Item_StaffPortalAccess
				, $Item_ProfPortalAccess
				, $Item_OtherPortalAccess
				, $_REQUEST["Item_FilterType"]
				, $Item_FilterOnUserRoles
				, $Item_FilterOnSpecifiedUsers
				, $_REQUEST["Item_UserAccessRange"]
				, $_REQUEST["Item_RelatedOrganzationChartID"]
				, $_REQUEST["Item_AccessRangeRelatedPersonType"]
				
				, $_REQUEST["Item_ShowBarcodeInPrintPage"]
				, $_REQUEST["Item_UserCanBackward"]
				, $_REQUEST["Item_PrintPageHeader"]
				, $_REQUEST["Item_PrintPageFooter"]
				, $_REQUEST["Item_PrintPageTitle"]
				, $_REQUEST["Item_PrintPageSigniture"]
				, $_REQUEST["Item_ShowHistoryInPrintPage"]
				, $_REQUEST["Item_NumberOfPermittedSend"]
				, "FOREVER"
				, $Item_StartDate
				, $Item_EndDate
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
$PrintPageHeader = $PrintPageFooter = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FormsFlowSteps();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_StepTitle.value='".$obj->StepTitle."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_StepType.value='".$obj->StepType."'; \r\n ";
	if($obj->StudentPortalAccess=="ALLOW")
		$LoadDataJavascriptCode .= "document.f1.Item_StudentPortalAccess.checked='true'; \r\n ";
	if($obj->StaffPortalAccess=="ALLOW")
		$LoadDataJavascriptCode .= "document.f1.Item_StaffPortalAccess.checked='true'; \r\n ";
	if($obj->ProfPortalAccess=="ALLOW") 
		$LoadDataJavascriptCode .= "document.f1.Item_ProfPortalAccess.checked='true'; \r\n ";
	if($obj->OtherPortalAccess=="ALLOW")
		$LoadDataJavascriptCode .= "document.f1.Item_OtherPortalAccess.checked='true'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_FilterType.value='".$obj->FilterType."'; \r\n ";
	if($obj->FilterOnUserRoles=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_FilterOnUserRoles.checked='true'; \r\n ";
	if($obj->FilterOnSpecifiedUsers=="YES") 
		$LoadDataJavascriptCode .= "document.f1.Item_FilterOnSpecifiedUsers.checked='true'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_UserAccessRange.value='".$obj->UserAccessRange."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_RelatedOrganzationChartID.value='".$obj->RelatedOrganzationChartID."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_AccessRangeRelatedPersonType.value='".$obj->AccessRangeRelatedPersonType."'; \r\n ";
	
	$LoadDataJavascriptCode .= "document.f1.Item_ShowBarcodeInPrintPage.value='".$obj->ShowBarcodeInPrintPage."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_UserCanBackward.value='".$obj->UserCanBackward."'; \r\n ";
	$PrintPageHeader = $obj->PrintPageHeader;
	$PrintPageFooter = $obj->PrintPageFooter;
	$LoadDataJavascriptCode .= "document.f1.Item_PrintPageTitle.value='".$obj->PrintPageTitle."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_PrintPageSigniture.value='".$obj->PrintPageSigniture."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_ShowHistoryInPrintPage.value='".$obj->ShowHistoryInPrintPage."'; \r\n ";
	
	$LoadDataJavascriptCode .= "document.f1.Item_NumberOfPermittedSend.value='".$obj->NumberOfPermittedSend."'; \r\n ";
	
	if($obj->SendDatePermittedStartDate!="0000-00-00 00:00:00")
	{
		$LoadDataJavascriptCode .= "document.getElementById('StartDate_YEAR').value='".substr($obj->Shamsi_SendDatePermittedStartDate, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('StartDate_MONTH').value='".substr($obj->Shamsi_SendDatePermittedStartDate, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('StartDate_DAY').value='".substr($obj->Shamsi_SendDatePermittedStartDate, 8, 2)."'; \r\n ";
	} 
	if($obj->SendDatePermittedEndDate!="0000-00-00 00:00:00")
	{
		$LoadDataJavascriptCode .= "document.getElementById('EndDate_YEAR').value='".substr($obj->Shamsi_SendDatePermittedEndDate, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('EndDate_MONTH').value='".substr($obj->Shamsi_SendDatePermittedEndDate, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('EndDate_DAY').value='".substr($obj->Shamsi_SendDatePermittedEndDate, 8, 2)."'; \r\n "; 
	}
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
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش مراحل گردش <b><?php echo $ParentObj->FormTitle ?></b></td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=Item_FormStructID id=Item_FormStructID value='<? echo $_REQUEST["Item_FormStructID"]; ?>'>
<tr id=tr_StepTitle name=tr_StepTitle style='display:'>
<td width=1% nowrap>
<font color=red>*</font>	عنوان
</td>
<td nowrap>
	<input type=text name=Item_StepTitle id=Item_StepTitle size=100>
</td>
</tr>

<tr id=tr_StepType name=tr_StepType style='display:'>
<td colspan=2>نوع مرحله: 
	<select name=Item_StepType id=Item_StepType onchange='javascript: ChangeStepType()'>
	<option value='OTHER'>عادی
	<option value='START'>شروع
	<option value='ARCHIVE'>بایگانی/نهایی
	</select>
	<font color=red>
	<b>
	توجه:
	</b>
	</font>
	اگر نوع مرحله شروع باشد آنگاه کاربران مجاز در پورتالها از طریق گزینه ارسال فرم به تعداد تنظیم شده و در مهلت مقرر اقدام به ارسال فرم نمایند.
</td>
<tr id="StartFeatures" style="display: none">
	<td colspan=2>
	 حداکثر تعداد قابل ارسال
	<select name=Item_NumberOfPermittedSend id=Item_NumberOfPermittedSend>
		<option value='0'>نامحدود
		<option value='1'>فقط یکبار
	</select>
	بازه مجاز ارسال از 
	<input maxlength="2" id="StartDate_DAY"  name="StartDate_DAY" type="text" size="2">/
	<input maxlength="2" id="StartDate_MONTH"  name="StartDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="StartDate_YEAR" name="StartDate_YEAR" type="text" size="2" >
	تا
	<input maxlength="2" id="EndDate_DAY"  name="EndDate_DAY" type="text" size="2">/
	<input maxlength="2" id="EndDate_MONTH"  name="EndDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="EndDate_YEAR" name="EndDate_YEAR" type="text" size="2" >
	</td>
</tr>
</tr>
<tr id=tr_StudentPortalAccess name=tr_StudentPortalAccess style='display:'>
<td colspan=2 nowrap>
	قابل دسترسی از طریق: 
	<input type=checkbox name=Item_StudentPortalAccess id=Item_StudentPortalAccess>
	پورتال دانشجویی
	<input type=checkbox name=Item_StaffPortalAccess id=Item_StaffPortalAccess>
	پورتال کارمندی
	<input type=checkbox name=Item_ProfPortalAccess id=Item_ProfPortalAccess>
	پورتال اساتید
	<input type=checkbox name=Item_OtherPortalAccess id=Item_OtherPortalAccess>
	پورتال سایر
</td>
</tr>

<tr id=tr_UserAccessRange name=tr_FilterOnUserRoles style='display:'>
<td nowrap colspan=2>
	<input type=checkbox name=Item_FilterOnUserRoles id=Item_FilterOnUserRoles>
	قابل دسترس برای کاربران دارای نقش خاص
</td>
</tr>
<tr id=tr_FilterOnSpecifiedUsers name=tr_FilterOnSpecifiedUsers style='display:'>
<td colspan=2 nowrap>
	<input type=checkbox name=Item_FilterOnSpecifiedUsers id=Item_FilterOnSpecifiedUsers>
	قابل دسترس برای کاربران خاص
</td>
</tr>
<tr id=tr_FilterType name=tr_FilterType style='display:'>
<td width=1% nowrap>
	مجاز برای دسترسی
	</td>
	<td>
	<select name=Item_FilterType id=Item_FilterType>
		<option value='NO_FILTER'>تمامی واحدها و زیر واحدها
		<option value='UNITS'>واحدهای سازمانی خاص
		<option value='SUB_UNITS'>زیر واحدهای سازمانی خاص
		<option value='EDU_GROUPS'>گروه های آموزشی خاص
	</select>
</td>
</tr>
<tr id=tr_UserCanOnlySeeHisUnitForms name=tr_UserCanOnlySeeHisUnitForms style='display:'>
<td width=1% nowrap>
	محدوده دسترسی هر کاربر
</td>
<td nowrap>
	<select name=Item_UserAccessRange id=Item_UserAccessRange onchange='javascript: ChangeFilter();'>
		<option value='ALL'>-
		<option value='HIM'>فقط فرمهای خودش (برای مراحل شروع یعنی ایجاد کننده اولیه و برای سایر مراحل یعنی کاربر ارسال کننده به این مرحله باشد)
		<option value='UNIT'>فقط فرمهای افراد واحد خودش
		<option value='SUB_UNIT'>فقط فرمهای افراد زیر واحد خودش
		<option value='EDU_GROUP'>فقط فرمهای افراد گروه آموزشی خودش
		<option value='BELOW_IN_CHART_ALL_LEVEL'> فقط فرمهای افراد زیر مجموعه خودش در تمام سطوح
		<option value='BELOW_IN_CHART_LEVEL1'>فقط فرمهای افراد زیر مجموعه خودش در یک سطح پایینتر
		<option value='BELOW_IN_CHART_LEVEL2'>فقط فرمهای افراد زیر مجموعه خودش در دو سطح پایینتر
		<option value='BELOW_IN_CHART_LEVEL3'>فقط فرمهای افراد زیر مجموعه خودش در سه سطح پایینتر
		<option value='UNDER_MANAGEMENT'>فقط فرمهای افراد تحت مدیریت
	</select>
</td>
</tr>
<tr id=tr_RelatedOrganzationChartID name=tr_RelatedOrganzationChartID style='display:'>
<td width=1% nowrap>
	در چارت سازمانی
</td>
<td nowrap>
	<select name=Item_RelatedOrganzationChartID id=Item_RelatedOrganzationChartID>
		<?php echo manage_OrganizationCharts::GetListAsSelectOptions(); ?>
	</select>
	<select name=Item_AccessRangeRelatedPersonType id=Item_AccessRangeRelatedPersonType>
		<option value='CREATOR'>بر اساس ایجاد کننده اولیه فرم کنترل صورت گیرد
		<option value='SENDER'>بر اساس آخرین ارسال کننده فرم کنترل صورت گیرد
	</select>
</td>
</tr>
<tr>
	<td colspan=2><a href='#' onclick='javascript: if(document.getElementById("TR_ADV").style.display=="none") document.getElementById("TR_ADV").style.display=""; else document.getElementById("TR_ADV").style.display="none";'>تنظیمات پیشرفته</a></td>
</tr>
<tr style="display: none" id=TR_ADV name=TR_ADV>
	<td colspan=2>
		<table width=100%>
		<tr>
			<td colspan=2>
				کاربر امکان برگشت از این مرحله به مراحل قبل را دارد؟
				<select name='Item_UserCanBackward' id='Item_UserCanBackward'>
					<option value='YES'>بلی
					<option value='NO'>خیر
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				نمایش کد فرم به صورت بارکد در صفحه چاپی:  
				<select name=Item_ShowBarcodeInPrintPage id=Item_ShowBarcodeInPrintPage>
					<option value='NO'>خیر
					<option value='YES'>بلی
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			تیتر بالای صفحه هنگام چاپ:
			<input type=text size=60 name=Item_PrintPageTitle id=Item_PrintPageTitle>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			متن بالای صفحه چاپی:
			<textarea name=Item_PrintPageHeader id=Item_PrintPageHeader cols=40 rows=4><?php echo $PrintPageHeader; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			متن پایین صفحه چاپی:
			<textarea name=Item_PrintPageFooter id=Item_PrintPageFooter cols=40 rows=4><?php echo $PrintPageFooter; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				نحوه نمایش امضا در پایین صفحه چاپی:  
				<select name=Item_PrintPageSigniture id=Item_PrintPageSigniture>
					<option value='NO'>نشان داده نشود
					<option value='WITHOUT_NAME'>نشان داده شود
					<option value='WITH_NAME'>امضا به همراه نام کاربر
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				نحوه سابقه در پایین صفحه چاپی:  
				<select name=Item_ShowHistoryInPrintPage id=Item_ShowHistoryInPrintPage>
					<option value='NO'>خیر
					<option value='YES'>بلی
				</select>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan=2>
	در صورتیکه برای گزینه "مجاز برای دسترسی" مقدار "تمامی واحدها و زیرواحدها" تعیین شود و کاربران یا نقشهای خاص هم انتخاب شده باشند
	در اینصورت تنها افراد یا نقشهای انتخابی می توانند به این مرحله دسترسی داشته باشند.
	<br>
	اما اگر واحد - زیر واحد یا گروه آموزشی انتخاب شده باشد اجتماع افراد مجموعه های انتخاب شده و افراد خاص (کاربران/نقشهای خاص) در نظر گرفته می شود.
	</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
&nbsp;<input type=button value='جدید' onclick='javascript: document.location="ManageFormFlow.php?Item_FormStructID=<?php echo $_REQUEST["Item_FormStructID"] ?>";'>
&nbsp;<input type=button value='بازگشت' onclick='javascript: document.location="ManageFormsStruct.php";'>

</td></tr>
</table>
<input type=hidden name=Save id=Save value=1>

</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	ChangeFilter();
	function ValidateForm()
	{
		if(document.f1.Item_StepTitle.value=='')
		{
			alert('در بخش عنوان باید مقداری وارد شود');
			return;
		}
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
	function ChangeFilter()
	{
		if(document.f1.Item_UserAccessRange.value=='BELOW_IN_CHART_ALL_LEVEL' || document.f1.Item_UserAccessRange.value=='BELOW_IN_CHART_LEVEL1' || document.f1.Item_UserAccessRange.value=='BELOW_IN_CHART_LEVEL2' || document.f1.Item_UserAccessRange.value=='BELOW_IN_CHART_LEVEL3' || document.f1.Item_UserAccessRange.value=='UNDER_MANAGEMENT' )
			Show('tr_RelatedOrganzationChartID');
		else
			Hide('tr_RelatedOrganzationChartID');
		 
	}
</script>
<?php 
 $k=0;
$ListCondition = " FormsStructID='".$_REQUEST["Item_FormStructID"]."' "; 
$res = manage_FormsFlowSteps::GetList($ListCondition); 
echo "<form id=f2 name=f2 method=post>"; 
?><!--  -->
<input type=hidden name=Item_FormStructID id=Item_FormStructID value='<? echo $_REQUEST["Item_FormStructID"]; ?>'>
<?php 
echo "<br><table width=98% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td rowspan=2 width=1%>&nbsp;</td>";
echo "	<td rowspan=2 width=2%>کد</td>";
echo "	<td rowspan=2 >عنوان</td>";
echo "	<td rowspan=2 width=1%>شروع</td>";
echo "	<td colspan=4 align=center width=1% nowrap>دسترسی از طریق پورتال</td>";
echo "	<td rowspan=2 align=center width=1% nowrap>محدوده دسترسی کاربران</td>";
echo "	<td rowspan=2 align=center colspan=7 width=150>تنظیمات</td>";
echo "	<td rowspan=2 width=1%>رکورد</td>";
echo "	<td rowspan=2 width=1%>&nbsp;</td>";
echo "</tr>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%>دانشجویی</td>";
echo "<td width=1%>کارمندی</td>";
echo "<td width=1%>اساتید</td>";
echo "<td width=1%>سایر</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FormsFlowStepID])) 
	{
		manage_FormsFlowSteps::Remove($res[$k]->FormsFlowStepID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FormsFlowStepID."></td>";
		echo "	<td><a href='ManageFormFlow.php?Item_FormStructID=".$_REQUEST["Item_FormStructID"]."&UpdateID=".$res[$k]->FormsFlowStepID."'>".$res[$k]->FormsFlowStepID."</a></td>";
		echo "	<td>&nbsp;".$res[$k]->StepTitle."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->StepType)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->StudentPortalAccess)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->StaffPortalAccess)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->ProfPortalAccess)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->OtherPortalAccess)."</td>";
		
		echo "	<td align=center>".ShowRangeName($res[$k]->UserAccessRange)."</td>";
		echo "	<td width=30>";
		echo "<a href='ManageFormFlowStepRelations.php?FormFlowStepID=".$res[$k]->FormsFlowStepID."'><img title='تعیین مراحل بعدی' width=30 src='images/NextLevel.jpg' border=0></a>";
		echo "	</td>";		
		echo "	<td width=30>";
		echo "<a href='ManageFormFlowFieldsAccess.php?FormFlowStepID=".$res[$k]->FormsFlowStepID."'><img title='تعیین نحوه دسترسی کاربر به اجزای فرم در این مرحله' width=30 src='images/FieldsAccess.jpg' border=0></a>";
		echo "	</td>";		
		echo "	<td width=30>";
		if($res[$k]->FilterOnSpecifiedUsers=="YES")
			echo "<a href='ManageFormFlowUsers.php?FormFlowStepID=".$res[$k]->FormsFlowStepID."'><img title='کاربران مجاز به دسترسی به این مرحله' width=30 src='images/users.gif' border=0></a>";
		else
			echo "&nbsp;";
		echo "</td><td width=30>";
		if($res[$k]->FilterOnUserRoles=="YES")
			echo "<a href='ManageFormFlowRoles.php?FormFlowStepID=".$res[$k]->FormsFlowStepID."'><img title='نقشهای مجاز به دسترسی به این مرحله' width=30 src='images/roles.gif' border=0></a>";
		else
			echo "&nbsp;";
		echo "</td><td width=30>";
		if($res[$k]->FilterType=="UNITS")
			echo "<a href='ManageFormFlowUnits.php?FormFlowStepID=".$res[$k]->FormsFlowStepID."'><img title='واحدهای سازمانی مجاز به دسترسی به این مرحله' width=30 src='images/building.jpg' border=0></a>";
		else
			echo "&nbsp;";
		echo "</td><td width=30>";
		if($res[$k]->FilterType=="SUB_UNITS")
			echo "<a href='ManageFormFlowSubUnits.php?FormFlowStepID=".$res[$k]->FormsFlowStepID."'><img title='زیر واحدهای سازمانی مجاز به دسترسی به این مرحله' width=30 src='images/subunit.jpg' border=0></a>";
		else
			echo "&nbsp;";
		echo "</td><td width=30>";
		if($res[$k]->FilterType=="EDU_GROUPS")
			echo "<a href='ManageFormFlowEduGrps.php?FormFlowStepID=".$res[$k]->FormsFlowStepID."'><img title='گروه های آموزشی مجاز به دسترسی به این مرحله' width=30 src='images/group.jpg' border=0></a>";
		else
			echo "&nbsp;";
		echo "	</td>";
		echo "	<td>";
		echo GetCount($res[$k]->FormsFlowStepID);
		echo "	</td>";
		
		echo "	<td>";
		echo "	<a href='DownloadExcelFile.php?FormFlowStepID=".$res[$k]->FormsFlowStepID."'><img title='دریافت فایل اکسل' border=0 src='images/excel.jpg'></a>";
		echo "	</td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=18 align=center><input type=submit value='حذف'></tr>";
echo "</table>";
echo "</form>";
?>
<script>
	function ChangeStepType()
	{
		if(document.getElementById('Item_StepType').value=='START')
			document.getElementById('StartFeatures').style.display = '';
		else
			document.getElementById('StartFeatures').style.display = 'none';
	}
	ChangeStepType()
</script>
</html>
