<?

include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/BusinessModelUploadedExcelFiles.class.php");
HTMLBegin();

$res = manage_BusinessModelUploadedExcelFiles::GetList(); 
?>
<!-- <form id="ListForm" name="ListForm" method="post">  -->
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td>نام جدول</td>
	<td>زمان انتقال</td>
	<td>کاربر</td>
	<td>وضعیت انتقال</td>
	<td>تعداد رکورد</td>
	<td>قالب نگاشت</td>
	<td>فایل</td>
</tr>
<br><br><br>

<?
	$table=$_REQUEST["table"];
	$MapID=$_REQUEST["MapID"];
	$count=0;
	echo "<p> ";
	echo "مشخصات ده فایل  اکسل آخر‌ که از<font color=red> جدول ".$table."</font> به دیتابیس اضافه گشته اند:";
	echo "</p>";
	for($k=0; $k<count($res); $k++)
	{
		if ($res[$k]->TableName==$table && $res[$k]->MappingID==$MapID){		
		$count++;
		}
	}	
	$minusCount=$count-9;	
	$count=0;
	for($k=0; $k<count($res); $k++)
	{
		if ($res[$k]->TableName==$table && $res[$k]->MappingID!=$MapID){
		  $minusCount--;
		}
		if ($count<10 && $res[$k]->TableName==$table && $minusCount<0 && $res[$k]->MappingID==$MapID){		
		if($k%2==0)
			echo "<tr class=\"OddRow\">";
		else
			echo "<tr class=\"EvenRow\">";
		echo "<td>".($k+1)."</td>";
		echo "<td>".$res[$k]->TableName."</td>";
		echo "<td>".$res[$k]->UploadTime_Shamsi."</td>";
		echo "<td>".$res[$k]->UploadUserID."</td>";
		echo "<td>";
		if($res[$k]->UploadStatus=="FAIL")
		  echo "<font color=red>FAIL</font>";
		else
		  echo "OK";
		echo "</td>";
		echo "<td>&nbsp;".$res[$k]->InsertedRecords."</td>";
		echo "<td>&nbsp;".manage_BusinessModelUploadedExcelFiles::getMappingName($res[$k]->MappingID)."</td>";
		//echo "<td>&nbsp;".$res[$k]->MappingID."</td>";
		echo "	<td>".htmlentities($res[$k]->FileName, ENT_QUOTES, 'UTF-8')."<a target=\"_blank\" href=\"ShowExcelFileData.php?ExcelFilesID=".$res[$k]->BusinessModelUploadedExcelFilesID."\">(اینجا)</a></td>";
		echo "</tr>";
		$count++;
		}else{
		//$minusCount--;
		}
	}
?>