<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : هستان نگارها جزو پروژه ادغام
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-10-15
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyMergeProjectMembers.class.php");
include ("classes/OntologyMergeProject.class.php");
HTMLBegin();

function GetNumberOfChilds($LevelNo, $ParentID)
{
  $mysql = pdodb::getInstance();
  $LevelNo++;
  if($LevelNo>20)
    return 0;
  $indent = "";

  $query = "select OntologyClasses.OntologyClassID
    from projectmanagement.OntologyClasses 
    JOIN projectmanagement.OntologyClassHirarchy on (OntologyClassHirarchy.OntologyClassParentID=OntologyClasses.OntologyClassID)
    where OntologyClassHirarchy.OntologyClassID=?";

  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($ParentID));
  $k = 0;

  while($rec = $res->fetch())
  {
    $k++;
    $k=$k+GetNumberOfChilds($LevelNo, $rec["OntologyClassID"]);
  }
  return $k;
}

function GetNumberOfFanout($ClassID, $path=array())
{
  $n = 0;
  // برای جلوگیری از افتادن در سیکل
  for($i=0; $i<count($path); $i++)
  {
  	if($path[$i]==$ClassID)
  		return 0;
  }
  array_push($path, $ClassID);
// تعداد روابطی که از این گره به دیگر گره ها وجود دارد
  $mysql = pdodb::getInstance();
  $query = "select * from projectmanagement.OntologyObjectPropertyRestriction
where RelationStatus='VALID' and DomainClassID=?";

  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($ClassID));
  while($rec = $res->fetch())
  {
  	$n++;
  	$n+=GetNumberOfFanout($rec["RangeClassID"], $path);
  }
  return $n;
}


function GetAllChildsID($Level, $OntologyClassID)
{
  $mysql = pdodb::getInstance();
  if($Level>20)
    return "";

 $query = "select OntologyClassParentID
    from projectmanagement.OntologyClassHirarchy 
    where OntologyClassHirarchy.OntologyClassID=?";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($OntologyClassID));
  $k = 0;
  $plist = "";
  while($rec = $res->fetch())
  {
  	if($plist!="")
  		$plist .= ", ";
  	$plist .= $rec["OntologyClassParentID"];
    	$pplist = GetAllChildsID($Level+1, $rec["OntologyClassParentID"]);
    	if($pplist!="")
    		$plist .= ", ";
    	$plist .= $pplist;
    		
  }
  return $plist;
}

function NoF($OntologyID)
{
	$n = 0;
// Number of Fanout: تعداد درجه خروجی مستقیم و غیر مستقیم برای همه نودهای گراف
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select * from projectmanagement.OntologyClasses where OntologyID=?");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	while($rec = $res->fetch())
	{	
		$n += GetNumberOfFanout($rec["OntologyClassID"]);
	}
	return $n;
}

function NoR($OntologyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select count(*) as tcount from projectmanagement.OntologyClasses 
JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
where OntologyID=?
and OntologyClassID not in 
(select OntologyClassParentID from projectmanagement.OntologyClassHirarchy)
and OntologyClassID not in
(select RangeClassID from projectmanagement.OntologyObjectPropertyRestriction  where RelationStatus='VALID')");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	$rec = $res->fetch();
	return $rec["tcount"];

}

function NoL($OntologyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select count(*) as tcount from projectmanagement.OntologyClasses 
where OntologyID=? and OntologyClassID not in (
select OntologyClassID from projectmanagement.OntologyClasses 
where OntologyID=?
and OntologyClassID not in 
(select OntologyClassParentID from projectmanagement.OntologyClassHirarchy)
and OntologyClassID not in
(select RangeClassID from projectmanagement.OntologyObjectPropertyRestriction  where RelationStatus='VALID'))
and OntologyClassID not in
(select DomainClassID from projectmanagement.OntologyObjectPropertyRestriction  where RelationStatus='VALID')");
	$res = $mysql->ExecuteStatement(array($OntologyID, $OntologyID));
	$rec = $res->fetch();
	return $rec["tcount"];

}

function GetClassCount($OntologyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select count(*) as tcount from projectmanagement.OntologyClasses where OntologyID=?");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	$rec = $res->fetch();
	return $rec["tcount"];
}

function GetPropsCount($OntologyID)
{
// مجموع خصوصیتهای متصل به کلاسها را محاسبه می کند و نه فقط تعداد خصوصیت ها در هستان نگار را
	$mysql = pdodb::getInstance();
	$totalcount = 0;
	$mysql->Prepare("select domain from projectmanagement.OntologyProperties where OntologyID=? and PropertyType='DATATYPE'");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	$rec = $res->fetch();
	while($rec = $res->fetch())
	{
		$classcount = count(explode(",", $rec["domain"]));
		$totalcount += $classcount;
		//echo $rec["domain"]." (".$classcount.")<br>";
	}
	// تا اینجا خصوصیات داده محاسبه شد
	$mysql->Prepare("select count(*) as tcount from projectmanagement.OntologyObjectPropertyRestriction 
JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
where RelationStatus='VALID' and OntologyID=?");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	$rec = $res->fetch();
	$totalcount += $rec["tcount"];
	return $totalcount;
}

function GetMaxDepth($ClassID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select OntologyClassParentID from projectmanagement.OntologyClassHirarchy where OntologyClassID=?");
	$res = $mysql->ExecuteStatement(array($ClassID));
	$maxd = 0;
	$i = 0;
	while($rec = $res->fetch())
	{
		$i++;
		$d = GetMaxDepth($rec["OntologyClassParentID"]);
		if($d>$maxd)
			$maxd = $d;
	}
	if($i==0)
		return 0;
	return $maxd+1;	
}

function MaxDIT($OntologyID)
{
	$MaxDepth = 0;
	$mysql = pdodb::getInstance();	
	$mysql->Prepare("select * from projectmanagement.OntologyClasses 
where OntologyID=?
and OntologyClassID not in 
(select OntologyClassParentID from projectmanagement.OntologyClassHirarchy)");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	while($rec = $res->fetch())
	{
	//echo $rec["OntologyClassID"].": ";
		$depth = GetMaxDepth($rec["OntologyClassID"]);
	//echo "(".$depth.")<br>";
		if($depth>$MaxDepth)
			$MaxDepth = $depth;
	}
	return $MaxDepth;
}


if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["OntologyMergeProjectID"]))
		$Item_OntologyMergeProjectID=$_REQUEST["OntologyMergeProjectID"];
	if(isset($_REQUEST["Item_OntologyID"]))
		$Item_OntologyID=$_REQUEST["Item_OntologyID"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_OntologyMergeProjectMembers::Add($Item_OntologyMergeProjectID
				, $Item_OntologyID
				);
	}	
	else 
	{	
		manage_OntologyMergeProjectMembers::Update($_REQUEST["UpdateID"] 
				, $Item_OntologyID
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_OntologyMergeProjectMembers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_OntologyID.value='".htmlentities($obj->OntologyID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_OntologyMergeProject::ShowSummary($_REQUEST["OntologyMergeProjectID"]);
echo manage_OntologyMergeProject::ShowTabs($_REQUEST["OntologyMergeProjectID"], "ManageOntologyMergeProjectMembers");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش هستان نگارها جزو پروژه ادغام</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="OntologyMergeProjectID" id="OntologyMergeProjectID" value='<? if(isset($_REQUEST["OntologyMergeProjectID"])) echo htmlentities($_REQUEST["OntologyMergeProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 هستان نگار عضو
	</td>
	<td nowrap>
	<select name="Item_OntologyID" id="Item_OntologyID">
	<option value=0>-
	<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.ontologies", "OntologyID", "OntologyTitle", "OntologyTitle"); ?>	</select>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageOntologyMergeProjectMembers.php?OntologyMergeProjectID=<?php echo $_REQUEST["OntologyMergeProjectID"]; ?>'" value="جدید">
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
$res = manage_OntologyMergeProjectMembers::GetList($_REQUEST["OntologyMergeProjectID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->OntologyMergeProjectMemberID])) 
	{
		manage_OntologyMergeProjectMembers::Remove($res[$k]->OntologyMergeProjectMemberID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_OntologyMergeProjectMembers::GetList($_REQUEST["OntologyMergeProjectID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_OntologyMergeProjectID" name="Item_OntologyMergeProjectID" value="<? echo htmlentities($_REQUEST["OntologyMergeProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="7">
	هستان نگارها جزو پروژه ادغام
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>هستان نگار عضو</td>
	<td>تعداد کل عناصر</td>	
	<td>NoC</td>
	<td>NoP</td>
	<td>AP-C</td>
	<td>AF-C</td>
	<td>AF-R</td>
	<td>NoR</td>	
	<td>MaxDIT</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	$ClassCount = GetClassCount($res[$k]->OntologyID);
	$PropsCount = GetPropsCount($res[$k]->OntologyID);
	$NumberOfRoot = NoR($res[$k]->OntologyID);
	$NumberOfFanout = NoF($res[$k]->OntologyID);
	$TotalCount = $ClassCount+$PropsCount;
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyMergeProjectMemberID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageOntologyMergeProjectMembers.php?UpdateID=".$res[$k]->OntologyMergeProjectMemberID."&OntologyMergeProjectID=".$_REQUEST["OntologyMergeProjectID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".$res[$k]->OntologyID_Desc."</td>";
	echo "	<td>".$TotalCount."</td>";
	echo "	<td>".$ClassCount."</td>";
	echo "	<td>".$PropsCount."</td>";
	echo "	<td>".round($PropsCount/$ClassCount, 2)."</td>";
	echo "<td>";
	echo round($NumberOfFanout/$ClassCount, 2);
	echo "</td>";
	echo "<td>";
	echo round($NumberOfFanout/$NumberOfRoot, 2);
	echo "</td>";

	echo "<td>";
	echo $NumberOfRoot;
	echo "</td>";
	
	echo "<td>";
	echo MaxDIT($res[$k]->OntologyID);
	echo "</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="7" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewOntologyMergeProjectMembers.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyMergeProjectID" name="OntologyMergeProjectID" value="<? echo htmlentities($_REQUEST["OntologyMergeProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
