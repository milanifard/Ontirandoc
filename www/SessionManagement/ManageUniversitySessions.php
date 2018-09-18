<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-6
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
/*if(config::$critical_status!=10){
echo "<br><br>
		<div style='color:red;font-family:tahoma;font-size:14px;font-weight:bold' align=center>" . 
						                                 ".به علت بار زیاد سرور این قسمت غیرفعال شده است" . 
		"</div>";
die();
}*/
//ini_set('display_errors','off');
HTMLBegin();
//if($_SESSION["PersonID"]=="201309")
//	$_SESSION["PersonID"] = "201391";
$NumberOfRec = 30;
 $k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
	$FromRec = $_REQUEST["PageNumber"]*$NumberOfRec;
	$PageNumber = $_REQUEST["PageNumber"];
}
else
{
	$FromRec = 0; 
}
//print_r($_REQUEST["SearchAction"]);
if(isset($_REQUEST["SearchAction"])) 
{
	$OrderByFieldName = "SessionDate";
	$OrderType = "DESC";
	if(isset($_REQUEST["OrderByFieldName"]))
	{
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		$OrderType = $_REQUEST["OrderType"];
	}
	$SessionTypeID=htmlentities($_REQUEST["Item_SessionTypeID"], ENT_QUOTES, 'UTF-8');
	$SessionNumber=htmlentities($_REQUEST["Item_SessionNumber"], ENT_QUOTES, 'UTF-8');
	$SessionTitle=htmlentities($_REQUEST["Item_SessionTitle"], ENT_QUOTES, 'UTF-8');
	$SessionFromDate = SharedClass::ConvertToMiladi($_REQUEST["SessionFromDate_YEAR"], $_REQUEST["SessionFromDate_MONTH"], $_REQUEST["SessionFromDate_DAY"]);
	$SessionToDate = SharedClass::ConvertToMiladi($_REQUEST["SessionToDate_YEAR"], $_REQUEST["SessionToDate_MONTH"], $_REQUEST["SessionToDate_DAY"]);
	$SessionLocation=htmlentities($_REQUEST["Item_SessionLocation"], ENT_QUOTES, 'UTF-8');
	$PreCommandKeyWord=htmlentities($_REQUEST["Item_PreCommandKeyWord"], ENT_QUOTES, 'UTF-8');
	$DecisionKeyWord=htmlentities($_REQUEST["Item_DecisionKeyWord"], ENT_QUOTES, 'UTF-8');
        $_REQUEST["view"]='';
} 
else
{ 
	$OrderByFieldName = "SessionDate";
	$OrderType = "DESC";
	$SessionTypeID='';
	$SessionNumber='';
	$SessionTitle='';
	$SessionDate='';
	$SessionLocation='';
	$SessionFromDate = '0000-00-00';
	$SessionToDate = '0000-00-00';
	$PreCommandKeyWord = "";
	$DecisionKeyWord = "";
}
$SessionTypesList = manage_UniversitySessions::GetOptions($_SESSION["PersonID"]);
$view = "";
if(isset($_REQUEST["view"]))
  $view = $_REQUEST["view"];
$res = manage_UniversitySessions::Search($_SESSION["PersonID"], $SessionTypeID,$view, $SessionNumber, $SessionTitle, $SessionFromDate, $SessionToDate, $SessionLocation, $PreCommandKeyWord, $DecisionKeyWord, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
/*if($_SESSION["UserID"]=='gholami-a'){
print_r($res[1]->UniversitySessionID);}*/
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->UniversitySessionID])) 
	{
		manage_UniversitySessions::Remove($res[$k]->UniversitySessionID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_UniversitySessions::Search($_SESSION["PersonID"], $SessionTypeID,$_REQUEST["view"], $SessionNumber, $SessionTitle, $SessionFromDate, $SessionToDate, $SessionLocation, $PreCommandKeyWord, $DecisionKeyWord, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 

if(isset($_REQUEST["SearchAction"])) 
{
?>
<script>
		document.SearchForm.Item_SessionTypeID.value='<? echo htmlentities($_REQUEST["Item_SessionTypeID"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_SessionNumber.value='<? echo htmlentities($_REQUEST["Item_SessionNumber"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_SessionTitle.value='<? echo htmlentities($_REQUEST["Item_SessionTitle"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.SessionFromDate_DAY.value='<? echo htmlentities($_REQUEST["SessionFromDate_DAY"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.SessionFromDate_MONTH.value='<? echo htmlentities($_REQUEST["SessionFromDate_MONTH"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.SessionFromDate_YEAR.value='<? echo htmlentities($_REQUEST["SessionFromDate_YEAR"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.SessionToDate_DAY.value='<? echo htmlentities($_REQUEST["SessionToDate_DAY"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.SessionToDate_MONTH.value='<? echo htmlentities($_REQUEST["SessionToDate_MONTH"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.SessionToDate_YEAR.value='<? echo htmlentities($_REQUEST["SessionToDate_YEAR"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_SessionLocation.value='<? echo htmlentities($_REQUEST["Item_SessionLocation"], ENT_QUOTES, 'UTF-8'); ?>';
</script>
<?
}
?> 
<br>
<?if($view=="" && (!isset($_REQUEST["SearchAction"]) || $_REQUEST["SearchAction"]!=1)) { ?>

<table width="50%" align="center" border="1" cellspacing="0"  >
<tr bgcolor="#cccccc">
	<td colspan="2">
	جلسات
	</td>
</tr>

<tr class="HeaderOfTable">	
	 <!--<td width="1%">ردیف</td>-->	
	 <td><a href="javascript: Sort('SessionTypeID', 'ASC');">نوع جلسه</a></td>
         <td width="2%">مشاهده</td>
</tr>
<?

$mysql = pdodb::getInstance();		
$query = "select distinct SessionTypeID, SessionTypes.SessionTypeTitle  
        from sessionmanagement.PersonPermissionsOnFields 
        JOIN sessionmanagement.SessionTypes on (SessionTypeID=RecID) 
        where 
        PersonPermissionsOnFields.TableName='SessionTypes' 

        and PersonPermissionsOnFields.FieldName='CreateNewSession' 
        and PersonPermissionsOnFields.PersonID=?  order by SessionTypeID";
$mysql->Prepare($query);
$result = $mysql->ExecuteStatement(array($_SESSION["PersonID"]));
while($rec=$result->fetch()){

        echo "<tr>";
        echo "<td>".htmlentities($rec["SessionTypeTitle"], ENT_QUOTES, 'UTF-8')."</td>";
        echo "<td>";
	echo "<a href=\"ManageUniversitySessions.php?view=".$rec["SessionTypeID"]."\">";
		echo "<img src='images/read.gif' title='مشاهده'>";
	echo "</a></td>";
        echo "</tr>";
}
?>
<tr class="FooterOfTable">
</tr>
</table>

<?}

if(isset($_REQUEST["view"]) ){

?>
<form id="SearchForm" name="SearchForm" method=post > 
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr class="HeaderOfTable">
<td><img src='images/search.gif'><b><a href="#" onclick='javascript: if(document.getElementById("SearchTr").style.display=="none") document.getElementById("SearchTr").style.display=""; else document.getElementById("SearchTr").style.display="none";'>جستجو</a></td>
</tr>
<tr id='SearchTr' style='display: none'>
<td>
<table width="100%" align="center" border="0" cellspacing="0">
<? 
if(isset($_REQUEST["UpdateID"]))
{
?> 
<? } else { ?>
<tr id="tr_SessionTypeID" name="tr_SessionTypeID" style='display:'>
<td width="1%" nowrap>
	نوع جلسه
</td>
	<td nowrap>
	<select name="Item_SessionTypeID" id="Item_SessionTypeID">
<option value=0>-
	<? echo $SessionTypesList ?>
       </select>
	</td>
<? } ?>

<tr>
	<td width="1%" nowrap>
 شماره جلسه
	</td>
	<td nowrap>
	<input type="text" name="Item_SessionNumber" id="Item_SessionNumber" maxlength="20" size="40">
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
 عنوان جلسه
	</td>
	<td nowrap>
	<input type="text" name="Item_SessionTitle" id="Item_SessionTitle" maxlength="500" size="40">
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
 تاریخ تشکیل
	</td>
	<td nowrap>
	از 
	<input maxlength="2" id="SessionFromDate_DAY"  name="SessionFromDate_DAY" type="text" size="2">/
	<input maxlength="2" id="SessionFromDate_MONTH" name="SessionFromDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="SessionFromDate_YEAR" name="SessionFromDate_YEAR" type="text" size="2" >
	تا
	<input maxlength="2" id="SessionToDate_DAY"  name="SessionToDate_DAY" type="text" size="2">/
	<input maxlength="2" id="SessionToDate_MONTH" name="SessionToDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="SessionToDate_YEAR" name="SessionToDate_YEAR" type="text" size="2" >
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
 محل تشکیل
	</td>
	<td nowrap>
	<input type="text" name="Item_SessionLocation" id="Item_SessionLocation" maxlength="200" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 کلمه کلیدی در دستور کار
	</td>
	<td nowrap>
	<input type="text" name="Item_PreCommandKeyWord" id="Item_PreCommandKeyWord" maxlength="200" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 کلمه کلیدی در مصوبه ها
	</td>
	<td nowrap>
	<input type="text" name="Item_DecisionKeyWord" id="Item_DecisionKeyWord" maxlength="200" size="40">
	</td>
</tr>
<tr class="HeaderOfTable">
<td colspan="2" align="center"><input type="submit" value="جستجو" ></td>
</tr>
</table>
</td>
</tr>
</table>
</form>

<form id="ListForm" name="ListForm" method="post"> 
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="13">
	جلسات
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td><a href="javascript: Sort('SessionTypeID', 'ASC');">نوع جلسه</a></td>
	<td><a href="javascript: Sort('SessionNumber', 'ASC');">شماره جلسه</a></td>
	<td><a href="javascript: Sort('SessionTitle', 'ASC');">عنوان جلسه</a></td>
	<td><a href="javascript: Sort('SessionDate', 'ASC');">تاریخ تشکیل</a></td>
	<td><a href="javascript: Sort('SessionLocation', 'ASC');">محل تشکیل</a></td>
	<td><a href="javascript: Sort('SessionStartTime', 'ASC');">زمان شروع</a></td>
	<td><a href="javascript: Sort('SessionDurationTime', 'ASC');">مدت جلسه</a></td>
	<td><a href="javascript: Sort('SessionStatus', 'ASC');">وضعیت جلسه</a></td>
	<td nowrap>
	سایر مشخصات
	</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{ 
    
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	if($res[$k]->CurrentUserHasRemoveAccess=="YES")
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->UniversitySessionID."\">";
	else
		echo "&nbsp;";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"UpdateUniversitySessions.php?UpdateID=".$res[$k]->UniversitySessionID."\">";
		echo "<img src='images/edit.gif' title='ویرایش'>";
	echo "</a></td>";
		echo "	<td>".$res[$k]->SessionTypeID_Desc."</td>";
	echo "	<td>".htmlentities($res[$k]->SessionNumber, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->SessionTitle, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".$res[$k]->SessionDate_Shamsi."</td>";
	echo "	<td>".htmlentities($res[$k]->SessionLocation, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".floor($res[$k]->SessionStartTime/60).":".($res[$k]->SessionStartTime%60)."</td>";
	echo "	<td>".floor($res[$k]->SessionDurationTime/60).":".($res[$k]->SessionDurationTime%60)."</td>";
		echo "	<td>".$res[$k]->SessionStatus_Desc."</td>";
	echo "<td nowrap>";
	echo "<a target=\"_blank\" href='ManageSessionPreCommands.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
	echo "<img src='images/draft.gif' border='0' title='دستور کار'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageSessionDecisions.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
	echo "<img src='images/list-accept.gif' border='0' title='مصوبات جلسه'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageMembersPAList.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
	echo "<img src='images/PAList.gif' border='0' title='حضور/غیاب'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageSessionDocuments.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
	echo "<img src='images/document.gif' border='0' title='مستندات'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageSessionMembers.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
	echo "<img src='images/members.gif' border='0' title='اعضا'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageSessionOtherUsers.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
	echo "<img src='images/people.gif' border='0' title='سایر کاربران'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageSessionHistory.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
	echo "<img src='images/log.gif' border='0' title='سابقه'>";
	echo "</a>  ";
	echo "</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="13" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
         &nbsp;
		<input type=button value='بازگشت' onclick='javascript: document.location="ManageUniversitySessions.php"'>
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="13" align="right">
<?

$SessionsCount = manage_UniversitySessions::GetSearchResultCount($_SESSION["PersonID"], $SessionTypeID,$_REQUEST["view"],$SessionNumber, $SessionTitle, $SessionFromDate, $SessionToDate, $SessionLocation, $PreCommandKeyWord, $DecisionKeyWord, "");
for($k=0; $k<$SessionsCount/$NumberOfRec; $k++)
{
	if($PageNumber!=$k)
		echo "<a href='javascript: ShowPage(".($k).")'>";
	echo ($k+1);
	if($PageNumber!=$k)
		echo "</a>";
	echo " ";
}
?>
</td></tr>
</table>
</form>
<form target="_blank" method="post" action="NewUniversitySessions.php" id="NewRecordForm" name="NewRecordForm">
</form>
<?}?>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ShowPage(PageNumber)
{
	SearchForm.PageNumber.value=PageNumber; 
	SearchForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	SearchForm.OrderByFieldName.value=OrderByFieldName; 
	SearchForm.OrderType.value=OrderType; 
	SearchForm.submit();
}
/*function hideTable() {

 document.getElementById('div2').style.display = 'block';
            return false;


}*/
 setInterval(function(){
        
        var xmlhttp;
            if (window.XMLHttpRequest)
            {
                // code for IE7 , Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            }
            else
            {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            
            xmlhttp.open("POST","header.inc.php",true);            
            xmlhttp.send();
        
    }, 60000);

</script>

</html>