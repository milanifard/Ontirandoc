<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : پروژهی پژوهشی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-3-5
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ResearchProject.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_title"]))
		$Item_title=$_REQUEST["Item_title"];
	if(isset($_REQUEST["Item_ProjectType"]))
		$Item_ProjectType=$_REQUEST["Item_ProjectType"];
	if(isset($_REQUEST["Item_OwnerID"]))
		$Item_OwnerID=$_REQUEST["Item_OwnerID"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_ResearchProject::Add($Item_title
				, $Item_ProjectType
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
	}	
	else 
	{	
		manage_ResearchProject::Update($_REQUEST["UpdateID"] 
				, $Item_title
				, $Item_ProjectType
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
		die();
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ResearchProject();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_title.value='".htmlentities($obj->title, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ProjectType.value='".htmlentities($obj->ProjectType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		echo manage_ResearchProject::ShowSummary($_REQUEST["UpdateID"]);
		echo manage_ResearchProject::ShowTabs($_REQUEST["UpdateID"], "NewResearchProject");
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش کار پژوهشی</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 عنوان
	</td>
	<td nowrap>
	<input type="text" name="Item_title" id="Item_title" maxlength="345" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نوع
	</td>
	<td nowrap>
	<select name="Item_ProjectType" id="Item_ProjectType" >
		<option value=0>-
		<option value='PAPER'>مقاله</option>
		<option value='THESIS'>پایان نامه</option>
		<option value='BOOK'>کتاب</option>
	</select>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: window.close();" value="بستن">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form>
<?
  if(isset($_REQUEST["UpdateID"])) 
  {
    if(isset($_REQUEST["KeyWord"]))
      $KeyWord = $_REQUEST["KeyWord"];
    else
      $KeyWord = "";
?>
<br>
<form id=sf name=sf method=post>
<input type="hidden" name="UpdateID" id="UpdateID" value='<? echo $_REQUEST["UpdateID"] ?>'>
<table width=80% align=center border=1 cellspacing=0 cellpadding=5>
  <tr>
    <td>کلمه کلیدی: <input type=text name=KeyWord id=KeyWord value='<? echo $KeyWord ?>'><input type=submit value='جستجو'></td>
  </tR>
</table>
</form>
<? 
    $k = 0;
    if(isset($_REQUEST["KeyWord"]))
    {
      echo "<br><table width=90% align=center border=1 cellspacing=0 cellpadding=5>";
      echo "<tr class=HeaderOfTable><td colspan=2>یادداشتهای منابع</td></tr>";
      $query = "select ResearchProjectRefrenceCommentID, ResearchProjectRefrenceID, CommentBody 
		from projectmanagement.ResearchProjectRefrenceComments 
		JOIN projectmanagement.ResearchProjectRefrences using (ResearchProjectRefrenceID)
		where  ResearchProjectID='".$_REQUEST["UpdateID"]."' and CommentBody like ? ";
      $mysql = pdodb::getInstance();
      //echo $query;
      $mysql->Prepare($query);
      $res = $mysql->ExecuteStatement(array("%".$KeyWord."%"));
      $i=0;
      while($rec = $res->fetch())
      {
	$k++;
	echo "<tr>";
	echo "<td><a target=_blank href='ManageResearchProjectRefrenceComments.php?UpdateID=".$rec["ResearchProjectRefrenceCommentID"];
	echo "&ResearchProjectRefrenceID=".$rec["ResearchProjectRefrenceID"];
	echo "'>".$k."</a></td>";
	if(strlen(preg_replace('/[^\00-\255]+/u', '', $rec["CommentBody"]))/strlen($rec["CommentBody"])>0.8)
	  echo "<td dir=ltr>";
	else {
	  echo "<td>";
	}
	echo str_replace($KeyWord, "<font color=blue><b>".$KeyWord."</b></font>", str_replace("\n", "<br>", $rec["CommentBody"]))."</td>";
	echo "</tr>";
      }
      echo "</table>";      

      echo "<br><table width=90% align=center border=1 cellspacing=0 cellpadding=5>";
      echo "<tr class=HeaderOfTable><td colspan=2>یادداشتهای متفرقه</td></tr>";
      $query = "select ResearchProjectCommentID, CommentBody 
		from projectmanagement.ResearchProjectComments 
		where  ResearchProjectID='".$_REQUEST["UpdateID"]."' and CommentBody like ? ";
      $mysql = pdodb::getInstance();
      //echo $query;
      $mysql->Prepare($query);
      $res = $mysql->ExecuteStatement(array("%".$KeyWord."%"));
      $i=0;
      while($rec = $res->fetch())
      {
	$k++;
	echo "<tr>";
	echo "<td><a target=_blank href='ManageResearchProjectComments.php?UpdateID=".$rec["ResearchProjectCommentID"];
	echo "&ResearchProjectRefrenceID=".$rec["ResearchProjectRefrenceID"];
	echo "'>".$k."</a></td>";
	if(strlen(preg_replace('/[^\00-\255]+/u', '', $rec["CommentBody"]))/strlen($rec["CommentBody"])>0.8)
	  echo "<td dir=ltr>";
	else {
	  echo "<td>";
	}
	echo str_replace($KeyWord, "<font color=blue><b>".$KeyWord."</b></font>", str_replace("\n", "<br>", $rec["CommentBody"]))."</td>";
	echo "</tr>";
      }
      echo "</table>";      
      
      echo "<br><table width=90% align=center border=1 cellspacing=0 cellpadding=5>";
      echo "<tr class=HeaderOfTable><td colspan=4>نتیجه جستجو در بخش چکیده و نظر کلی در مورد منابع</td></tr>";
      $query = "select ResearchProjectRefrenceID, RefrenceTitle, BriefComment, abstract
		from projectmanagement.ResearchProjectRefrences 
		where  ResearchProjectID='".$_REQUEST["UpdateID"]."' and (BriefComment like ? or abstract like ?)";
      $mysql = pdodb::getInstance();
      //echo $query."<br>";
      $mysql->Prepare($query);
      $res = $mysql->ExecuteStatement(array("%".$KeyWord."%", "%".$KeyWord."%"));
      $i=0;
      while($rec = $res->fetch())
      {
	$k++;
	echo "<tr>";
	echo "<td><a target=_blank href='NewResearchProjectRefrences.php?UpdateID=".$rec["ResearchProjectRefrenceID"];
	echo "'>".$k."</a></td>";
	echo "<td>&nbsp;".str_replace($KeyWord, "<font color=blue><b>".$KeyWord."</b></font>", str_replace("\n", "<br>", $rec["abstract"]))."</td>";
	echo "<td>&nbsp;".str_replace($KeyWord, "<font color=blue><b>".$KeyWord."</b></font>", str_replace("\n", "<br>", $rec["BriefComment"]))."</td>";
	echo "</tr>";
      }
      echo "</table>";      
      
    }
  } ?>
<script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
</html>
