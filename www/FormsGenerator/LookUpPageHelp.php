<?php
include("header.inc.php");
HTMLBegin();
?>
<div class="container">

    <div class="card">
        <div class="card-header" style="text-align: center">راهنمای تولید صفحه جستجوی مقادیر</div>
        <div class="card-body">
            بعضی از مواقع داده هایی که در یک فیلد لیستی قابل انتخاب هستند بسیاز زیاد بوده و نمایش آنها به صورت لیست
            کشویی علاوه بر اینکه حجم زیادی
            از صفحه را به خود اختصاص می دهد باعث کندی عمل کاربر در انتخاب گزینه مورد نظر خود می شود.
            <br>
            برای اینگونه موارد می توان نحوه نمایش لیست را Look Up تعیین کرد.
            <br>
            در این حالت در فرم ورود داده در جلوی نام این فیلد یک لینک برای انتخاب داده مورد نظر نمایش داده می شود که
            کاربر بتواند با کلیک روی آن
            در صفحه ای که در یک پنجره جداگانه باز می شود به جستجوی آیتم مورد نظر خود پرداخته و آن را انتخاب کند تا در
            مقدار فیلد قرار گیرد.
            <br>
            این صفحه باید توسط برنامه نویس نوشته شده و آدرس آن در مشخصه "آدرس صفحه جستجوی داده" وارد شود.
            <br>
            مواردی که سیستم در زمان فراخوانی این صفحه به آن پاس می دهد عبارتند از:
            <br>
            <li>FormName: نام فرمی که فیلد در آن قرار دارد
            <li>InputName: نام عنصری که داخل فرم به صورت مخفی قرار دارد و باید مقدار کلید آیتم انتخاب شده در آن قرار
                گیرد
            <li>SpanName: نام span ای را به همراه دارد که توضیحات مربوط به آیتم انتخاب شده در آن نوشته می شود
                <br>
                به عنوان مثال مقادیر زیر را در نظر بگیرید:
                <br>FormName=f1&InputName=PersonID&SpanName=MySpan
                <br>
                حال باید برنامه نویس در کد خود زمانیکه کاربر آیتم مورد نظر را انتخاب کرد یک تابع جاوا اسکریپ مشابه این
                را فراخوانی کند:
                <br>
                <table width=100% align=center dir=ltr border=0>
                    <tr>
                        <td>
		<pre>
        function SetPerson(PersonID, PersonName)
        {
            document.<\? echo $_REQUEST["FormName"]; >.<\? echo $_REQUEST["InputName"]; >.value=PersonID;
            window.opener.document.getElementById(<\? echo $_REQUEST["SpanName"]; >).innerHTML=PersonName;
            window.close();
        }
		</pre>

        </div>
    </div>
</div>