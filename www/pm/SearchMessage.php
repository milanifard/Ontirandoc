<?php
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "on");
/*
 صفحه جستجوی پیام
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-21
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/PrivateMessageFollows.class.php");
HTMLBegin();
$SearchResult = "";
$Item_FromPersonID = $Item_ToPersonID = "0";
$Item_MessageTitle = $Item_MessageBody = $FromPerson = $ToPerson = "";
$StartTime_YEAR = $StartTime_MONTH = $StartTime_DAY = "";
$EndTime_YEAR = $EndTime_MONTH = $EndTime_DAY = "";
if(isset($_REQUEST["Search"])) 
{
  $Item_MessageTitle = $_REQUEST["Item_MessageTitle"];
  $Item_MessageBody = $_REQUEST["Item_MessageBody"];
  $Item_FromPersonID = $_REQUEST["Item_FromPersonID"];
  $Item_ToPersonID = $_REQUEST["Item_ToPersonID"];

  $StartTime_YEAR = $_REQUEST["StartTime_YEAR"];
  $StartTime_MONTH = $_REQUEST["StartTime_MONTH"];
  $StartTime_DAY = $_REQUEST["StartTime_DAY"];
  
  $EndTime_YEAR = $_REQUEST["EndTime_YEAR"];
  $EndTime_MONTH = $_REQUEST["EndTime_MONTH"];
  $EndTime_DAY = $_REQUEST["EndTime_DAY"];
  
  $mysql->Prepare("select * from projectmanagement.persons where PersonID=?");
  $res = $mysql->ExecuteStatement(array($Item_FromPersonID));
  $rec = $res->fetch();
  $FromPerson = $rec["pfname"]." ".$rec["plname"];

  $mysql->Prepare("select * from projectmanagement.persons where PersonID=?");
  $res = $mysql->ExecuteStatement(array($Item_ToPersonID));
  $rec = $res->fetch();
  $ToPerson = $rec["pfname"]." ".$rec["plname"];
  
  $Item_StartTime = $Item_EndTime = "";
  if(isset($_REQUEST["StartTime_DAY"]))
  {
	  $Item_StartTime = SharedClass::ConvertToMiladi($_REQUEST["StartTime_YEAR"], $_REQUEST["StartTime_MONTH"], $_REQUEST["StartTime_DAY"]);
	  //echo $Item_StartTime;
  }
  if(isset($_REQUEST["EndTime_DAY"]))
  {
	  $Item_EndTime = SharedClass::ConvertToMiladi($_REQUEST["EndTime_YEAR"], $_REQUEST["EndTime_MONTH"], $_REQUEST["EndTime_DAY"]);
  }

  $mysql = pdodb::getInstance();
  $query = "select PrivateMessages.PrivateMessageID, PrivateMessageFollowID, MessageTitle, MessageBody, comment, ReferStatus,
  concat(p1.pfname, ' ', p1.plname) as FromPerson,
  concat(p2.pfname, ' ', p2.plname) as ToPerson,
  concat(projectmanagement.g2j(ReferTime), substr(ReferTime, 11, 10)) as gReferTime
  from projectmanagement.PrivateMessages
  JOIN projectmanagement.PrivateMessageFollows using (PrivateMessageID)
  left JOIN projectmanagement.persons p1 on (p1.PersonID=PrivateMessageFollows.FromPersonID)
  left JOIN projectmanagement.persons p2 on (p2.PersonID=PrivateMessageFollows.ToPersonID)
  where (FromPersonID='".$_SESSION["PersonID"]."' or ToPersonID='".$_SESSION["PersonID"]."') 
  and MessageTitle like ? and MessageBody like ? 
  ";
  if($_REQUEST["Item_FromPersonID"]!="0")
    $query .= " and FromPersonID=? ";
  if($_REQUEST["Item_ToPersonID"]!="0")
    $query .= " and ToPersonID=? ";
  if($Item_StartTime!="0000-00-00")
    $query .= " and ReferTime>='".$Item_StartTime." 00:00:00' ";
  if($Item_EndTime!="0000-00-00")
    $query .= " and ReferTime<='".$Item_EndTime." 23:59:59' ";

   $query .= " order by ReferTime DESC ";
  //echo $query;
  $mysql->Prepare($query);
  
  $ValueListArray = array();
  array_push($ValueListArray, "%".$_REQUEST["Item_MessageTitle"]."%"); 
  array_push($ValueListArray, "%".$_REQUEST["Item_MessageBody"]."%"); 
  if($_REQUEST["Item_FromPersonID"]!="0")
    array_push($ValueListArray, $Item_FromPersonID); 
  if($_REQUEST["Item_ToPersonID"]!="0")
    array_push($ValueListArray, $Item_ToPersonID); 
  $res = $mysql->ExecuteStatement($ValueListArray);
  $i = 0;
  while($rec = $res->fetch())
  {
    $i++;
    $SearchResult .= "<tr>";
    $SearchResult .= "<td>";
    $SearchResult .= "<a target=_blank href=\"ShowMessage.php?BackPage=SearchMessage&MessageFollowID=".$rec["PrivateMessageFollowID"]."\">";
    $SearchResult .= $i;
    $SearchResult .= "</a>";
    $SearchResult .= "</td>";
    $SearchResult .= "<td>";
    if($rec["ReferStatus"]=="ARCHIVE")
      $SearchResult .= "<img src='images/DeleteMessage.gif'>";
    $SearchResult .= $rec["MessageTitle"]."</td>";
    $SearchResult .= "<td nowrap>".$rec["FromPerson"]."</td>";
    $SearchResult .= "<td nowrap>".$rec["ToPerson"]."</td>";
    $SearchResult .= "<td nowrap>".$rec["gReferTime"]."</td>";
    $SearchResult .= "<td>".$rec["comment"]."</td>";
    
    $SearchResult .= "</tr>";
  }
  if($i>0)
  {
    $Header = "<table border=1 cellspacing=0 cellpadding=4 width=98% align=center>";
    $Header .= "<tr class=HeaderOfTable><td width=1%>ردیف</td><td>عنوان</td><td width=10%>فرستنده</td><td width=10%>گیرنده</td><td width=10%>زمان ارسال</td><td>شرح ارجاع</td></tr>";
    $SearchResult = $Header.$SearchResult;
  }
}

?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">جستجوی نامه</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 عنوان
	</td>
	<td nowrap>
	<input type="text" name="Item_MessageTitle" id="Item_MessageTitle" value='<? echo $Item_MessageTitle ?>' maxlength="1000" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
بخشی از متن
	</td>
	<td nowrap>
	<input type="text" name="Item_MessageBody" id="Item_MessageBody" value='<? echo $Item_MessageBody ?>' maxlength="1000" size="40">
	</td>
</tr>
<tr id="tr_ToPersonID" name="tr_ToPersonID" style='display:'>
      <td width="1%" nowrap>
		  فرستنده
      </td>
	<td nowrap>
	<input type=hidden name="Item_FromPersonID" id="Item_FromPersonID" value="<? echo $Item_FromPersonID ?>">
	<span id="Span_FromPersonID_FullName" name="Span_FromPersonID_FullName"><? echo $FromPerson ?></span> 	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_FromPersonID&SpanName=Span_FromPersonID_FullName");'>[انتخاب]</a>
	</td>
</tr>
<tr id="tr_ToPersonID" name="tr_ToPersonID" style='display:'>
      <td width="1%" nowrap>
		  گیرنده
      </td>
	<td nowrap>
	<input type=hidden name="Item_ToPersonID" id="Item_ToPersonID" value="<? echo $Item_ToPersonID ?>">
	<span id="Span_ToPersonID_FullName" name="Span_ToPersonID_FullName"><? echo $ToPerson ?></span> 	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_ToPersonID&SpanName=Span_ToPersonID_FullName");'>[انتخاب]</a>
	</td>
</tr>
<tr id="tr_ToPersonID" name="tr_ToPersonID" style='display:'>
      <td colspan=2>
		  تاریخ ارسال از
	<input maxlength="2" value="<? echo $StartTime_DAY ?>" id="StartTime_DAY"  name="StartTime_DAY" type="text" size="2">/
	<input maxlength="2" value="<? echo $StartTime_MONTH ?>" id="StartTime_MONTH"  name="StartTime_MONTH" type="text" size="2" >/
	<input maxlength="2" value="<? echo $StartTime_YEAR ?>" id="StartTime_YEAR" name="StartTime_YEAR" type="text" size="2" > 
		  تا
	<input maxlength="2" value="<? echo $EndTime_DAY ?>" id="EndTime_DAY"  name="EndTime_DAY" type="text" size="2">/
	<input maxlength="2" value="<? echo $EndTime_MONTH ?>" id="EndTime_MONTH"  name="EndTime_MONTH" type="text" size="2" >/
	<input maxlength="2" value="<? echo $EndTime_YEAR ?>" id="EndTime_YEAR" name="EndTime_YEAR" type="text" size="2" >
		  
	</td>
</tr>

</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="جستجو">
</td>
</tr>
</table>
<input type="hidden" name="Search" id="Search" value="1">
</form><script>
	function ValidateForm()
	{
	  document.f1.submit();
	}
</script>
<?
  echo $SearchResult;
?>
</html>
