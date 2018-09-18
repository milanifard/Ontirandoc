<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : سلسله مراتب کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyClassHirarchy.class.php");
include ("classes/OntologyClasses.class.php");
HTMLBegin();
$mysql = pdodb::getInstance();
$obj = new be_OntologyClasses();
$obj->LoadDataFromDatabase($_REQUEST["OntologyClassID"]);
$OntologyID = $obj->OntologyID;
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["OntologyClassID"]))
		$Item_OntologyClassID=$_REQUEST["OntologyClassID"];
	if(isset($_REQUEST["Item_OntologyClassParentID"]))
		$Item_OntologyClassParentID=$_REQUEST["Item_OntologyClassParentID"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_OntologyClassHirarchy::Add($Item_OntologyClassID
				, $Item_OntologyClassParentID
				);
	}	
	else 
	{	
		manage_OntologyClassHirarchy::Update($_REQUEST["UpdateID"] 
				, $Item_OntologyClassParentID
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_OntologyClassHirarchy();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_OntologyClassParentID.value='".htmlentities($obj->OntologyClassParentID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_OntologyClasses::ShowSummary($_REQUEST["OntologyClassID"]);
echo manage_OntologyClasses::ShowTabs($_REQUEST["OntologyClassID"], "ManageOntologyClassHirarchy");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش زیر کلاس</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="OntologyClassID" id="OntologyClassID" value='<? if(isset($_REQUEST["OntologyClassID"])) echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 کلاس فرزند
	</td>
	<td nowrap>
	<select name="Item_OntologyClassParentID" id="Item_OntologyClassParentID">
	<option value=0>-
	  <?
	    $list = manage_OntologyClasses::GetList($obj->OntologyID);
	    for($i=0; $i<count($list); $i++)
	    {
	      echo "<option value='".$list[$i]->OntologyClassID."'>";
	      echo $list[$i]->label." (".$list[$i]->ClassTitle.")";
	    }
	  /*$res = $mysql->Execute("select OntologyClassID, ClassTitle from projectmanagement.OntologyClasses where OntologyID='".$obj->OntologyID."'");
	  while($rec = $res->fetch())
	  {
	    echo "<option value='".$rec["OntologyClassID"]."'>".$rec["ClassTitle"];
	  }*/
	?>
	</select>
	<a onclick='javascript: window.open("ShowOntologyClassTree.php?ReturnID=1&InputName=Item_OntologyClassParentID&OntologyID=<? echo $OntologyID;  ?>")' href="#">انتخاب</a>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageOntologyClassHirarchy.php?OntologyClassID=<?php echo $_REQUEST["OntologyClassID"]; ?>'" value="جدید">
 <input type="button" onclick="javascript: window.close();" value="بستن">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$res = manage_OntologyClassHirarchy::GetList($_REQUEST["OntologyClassID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->OntologyClassHirarchyID])) 
	{
		manage_OntologyClassHirarchy::Remove($res[$k]->OntologyClassHirarchyID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_OntologyClassHirarchy::GetList($_REQUEST["OntologyClassID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_OntologyClassID" name="Item_OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="5">
زیر کلاسها	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>کلاس</td>
	<td>زیر کلاسها</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyClassHirarchyID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageOntologyClassHirarchy.php?UpdateID=".$res[$k]->OntologyClassHirarchyID."&OntologyClassID=".$_REQUEST["OntologyClassID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
		echo "	<td>".$res[$k]->OntologyClassParentID_Desc."</td>";

	echo "<td width=1% nowrap><a  target=\"_blank\" href='ManageOntologyClassHirarchy.php?OntologyClassID=".$res[$k]->OntologyClassParentID ."'>زیر کلاسها";
	$ChildsCount = manage_OntologyClasses::GetChildsCount($res[$k]->OntologyClassParentID);
	if($ChildsCount>0)
	  echo "(".$ChildsCount.")";
	echo "</a></td>";
	
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="5" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewOntologyClassHirarchy.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyClassID" name="OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
