<?php
include("header.inc.php");
HTMLBegin();
?>
<table width=90% align=center border=1 cellspacing=0 cellpadding=5>
<tr>
	<td class=HeaderOfTable align=center>راهنمای تولید سیستمی فرمهای خام برای کاربران</td>
</tr>
<tr>
	<td>
	
	ایجاد نسخه خام از بسیاری از فرمها منحصرا باید توسط یک سیستم نرم افزاری صورت بگیرد زیرا اینگونه فرمها شامل اطلاعات اولیه ای هستند که با توجه به شرایط از دیتابیس استخراج می شود.
	<br>
	به عنوان نمونه برای ایجاد یک فرم حق التدریس نخست باید درسهایی که استاد در بازه زمانی خاصی ارایه داده به فرم افزوده شود و سپس برای تکمیل اطلاعات به مرحله ثبت استاد ارسال شود.
	<br>
	ایجاد فرمهای اولیه توسط یک سیستم نرم افزاری به این معنی است که سیستم باید رکوردهای مورد نظر خود را در جداول مربوطه وارد کرده و سپس در جدول مراحل کد مربوط به مرحله اول (که کاربران به آن دسترسی دارند) را برای هر رکورد ثبت کند.
	<br>
	همچنین باید ایجاد کننده آن رکورد هم کاربر مورد نظر ثبت شود تا او بتواند فرم را مشاهده و تغییرات مورد نظر را در آن اعمال کند.
	<br>
	<br>
	<b>مراحل کار:</b>
	<br>
	<li>تعریف فرم در سیستم فرم ساز و تعیین مراحل مورد نیاز
	<br>
	پس از ایجاد هر فرم در سیستم فرم ساز
	به صورت پیش فرض  دو مرحله ایجاد و حذف را به جریان کاری فرم اضافه می شود.
	<br>
	مرحله ایجاد از نوع مراحل شروع تعریف شده است بنابراین کاربران می توانند اقدام به تولید فرم اولیه نمایند اما از آنجا که می خواهیم فرم اولیه توسط سیستم ایجاد شود بنابراین مرحله فوق الذکر
	را ویرایش نموده و پس از دادن عنوانی مناسب به آن (مثلا ثبت کاربر) نوع آن را به "عادی" تغییر می دهیم.
	<br>
	به صورت پیش فرض محدوده دسترسی هر کاربر در مرحله ایجاد فقط فرمهای خودش تعریف شده است که برای کار ما مناسب می باشد و آن را به همان صورت حفظ می کنیم.
	<br>
	<li>نوشتن برنامه برای تولید فرمهای اولیه به ازای افراد مورد نظر
	<br>
	به ازای هر یک از افرادی که می خواهیم فرم اولیه برایشان ایجاد کنیم ابتدا یک رکورد حاوی مقادیر اولیه را در جدول اصلی فرم اضافه کرده و سپس کد کلید آن را بدست می آوریم.
	<br>
	این کد باید در جدول FormsRecords درج شود تا مشخص شود این رکورد مربوط به چه کسی و در چه مرحله ای می باشد.
	<br>
	فیلدهای زیر در جدول FormsRecords وجود دارند:
	<br>
	1. FormFlowStepID: کد مرحله ای که فرم در آن قرار می گیرد. این کد را می توانید در بخش مدیریت مراحل یک فرم در کنار عنوان فرم مشاهده کنید<br>
	2. RelatedRecordID: کد رکورد داده در جدول <br>
	3. SendDate: تاریخ ارسال که باید now() باشد<br>
	4. SenderID: کد شخص ارسال کننده<br>
	5. CreatorID: کد کاربر ایجاد کننده<br>
	6. FormsStructID: کد ساختار فرم که در بخش مدیریت فرمها در ستون اول قابل مشاهده است
	کد کاربر ایجاد کننده و ارسال کننده را یکی در نظر گرفته و برابر کد شخصی کاربری قرار دهید که این فرم باید در بخش فرمهای دریافتی او دیده شود.
	<br>
	در نهایت چنانچه هر رکورد داده دارای جداول وابسته نیز می باشد داده های مربوطه در دیتابیس درج شود.
	<br>
	<br>
	<b>مثال:
	<br>
	<textarea cols=80 rows=20 dir=ltr style="font-size:12px">
$query = "select * from 
		hrms_total.persons JOIN hrms_total.staff using (PersonID)
		where UnitCode=200 ";
$res = $mysql->Execute($query);
while($rec=$res->FetchRow())
{
	$query = "insert into StaffEvaluationForms (PersonID, ouid) values ('".$rec["PersonID"]."', '".$rec["UnitCode"]."')";
	$mysql->Execute($query);
	
	$query = "select max(StaffEvaluationFormID) as MaxID from StaffEvaluationForms";
	$res2 = $mysql->Execute($query);
	$rec2 = $res2->fetchRow();
	$CurID = $rec2["MaxID"];
	
	$mysql->Execute("insert into FormsRecords (FormFlowStepID, RelatedRecordID, SendDate, SenderID, CreatorID, FormsStructID) values ('37', '".$CurID."', now(), '".$rec["PersonID"]."', '".$rec["PersonID"]."', '38')");
	
	$query = "select * from nazar.EVL_PersonDuties where PersonID='".$rec["PersonID"]."'";
	$res2 = $mysql->Execute($query);
	while($rec2 = $res2->fetchRow())
	{
		$mysql->Execute("insert into StaffJobEvaluations (StaffEvaluationFormID, JobDescription, priority) values ('".$CurID."', '".$rec2["duty"]."', '".$rec2["priority"]."')");
	}

	$mysql->Execute($query);
}	</textarea>
	</td>
</tr>
</table>