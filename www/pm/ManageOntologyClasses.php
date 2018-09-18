<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-29
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyClasses.class.php");
include("classes/OntologyClassLabels.class.php");
include("classes/OntologyClassHirarchy.class.php");
include ("classes/ontologies.class.php");
HTMLBegin();

function ShowValidRelations($ClassID, $OntologyID, $label, $level)
{
  if($level>5)
  	return;
  	
  $mysql = pdodb::getInstance();
  
  
      // ابتدا روابط مربوط به پدران این کلاس نشان داده می شود
      $query = "select OntologyClassHirarchy.OntologyClassID, label from projectmanagement.OntologyClassHirarchy 
		JOIN projectmanagement.OntologyClassLabels on (OntologyClassLabels.OntologyClassID=OntologyClassParentID)
		where OntologyClassHirarchy.OntologyClassParentID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($ClassID));
	while($rec = $res->fetch())
	{
		if($label!="")
			$NewLabel = $rec["label"]." - ".$label;
		else
			$NewLabel = $rec["label"];
      		ShowValidRelations($rec["OntologyClassID"], $OntologyID, $NewLabel, $level+1);
      	}      
  
  
  $query = "select OntologyProperties.OntologyPropertyID
	    , DomainClassID, RangeClassID
	    , (select group_concat(label) from projectmanagement.OntologyClassLabels dl where dl.OntologyClassID=DomainClassID) as DomainClassLabel
	    , (select group_concat(label) from projectmanagement.OntologyClassLabels rl where rl.OntologyClassID=RangeClassID) as RangeClassLabel
	    , (select group_concat(label) from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=OntologyObjectPropertyRestriction.OntologyPropertyID) as PropertyLabel
	    from projectmanagement.OntologyObjectPropertyRestriction 
	    JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
	    where RelationStatus='VALID' and (DomainClassID=? or RangeClassID=?)";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($ClassID, $ClassID));
  while($rec = $res->fetch())
  {
    echo "رابطه: ";
    echo "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$rec["DomainClassID"]."&OntologyID=".$OntologyID."&OnlyEditForm=1'> ";
    if($rec["DomainClassID"]==$ClassID)
    {
    	if($label!="")
    		echo "<i>".$rec["DomainClassLabel"]." [".$label."]</i>";
    	else
    		echo "<i>".$rec["DomainClassLabel"]."</i>";
    }
    else
    	echo $rec["DomainClassLabel"];
    echo "</a> &nbsp;";
    echo " <b><a href=# onclick=\"window.open('ManageOntologyProperties.php?DoNotShowList=1&UpdateID=".$rec["OntologyPropertyID"]."&OntologyID=".$OntologyID."');\" >".$rec["PropertyLabel"]."</a></b> &nbsp;";    
    echo " <a target=_blank href='ManageOntologyClasses.php?UpdateID=".$rec["RangeClassID"]."&OntologyID=".$OntologyID."&OnlyEditForm=1'>";
    if($rec["RangeClassID"]==$ClassID)
    {
    	if($label!="")
    		echo "<i>".$rec["RangeClassLabel"]." [".$label."]</i>";
    	else
    		echo "<i>".$rec["RangeClassLabel"]."</i>";
    }
    else
    	echo $rec["RangeClassLabel"];
    echo "</a>";
    echo "<br>";
  }
  
}

function SuggestClassification()
{
	echo "<table border=1 cellspacing=0 cellpadding=5 width=90% align=center>";
	echo "<tr class=HeaderOfTable><td>";
	echo "پیشنهادهای دسته بندی";
	echo "</td></tr>";
	$res = manage_OntologyClasses::GetList($_REQUEST["OntologyID"]); 

	for($i=0; $i<count($res); $i++)
	{
		$c = 0;
		$ClassMembers = array();		
		$plist1 = manage_OntologyClasses::GetClassRelatedProperties($res[$i]->ClassTitle, $_REQUEST["OntologyID"]);
		for($j=0; $j<count($res); $j++)
		{
			if($i==$j) continue;
			//echo $res[$j]->ClassTitle."<br>";
			
			$plist2 = manage_OntologyClasses::GetClassRelatedProperties($res[$j]->ClassTitle, $_REQUEST["OntologyID"]);
			$similars = array();
			$s = 0;
			for($k=0; $k<count($plist1); $k++)
			{
				for($m=0; $m<count($plist2); $m++)
				{
					if($plist1[$k]["PropertyTitle"]==$plist2[$m]["PropertyTitle"] && $plist1[$k]["PropertyType"]==$plist2[$m]["PropertyType"])
					{
						// اگر دو خصوصیت شیء یکسان داشتند برای شبیه بودن باید به کلاس یکسانی هم اشاره کرده باشند
						if($plist2[$m]["PropertyType"]=="OBJECT")
						{ 
							if(manage_OntologyClasses::IsTwoClassRelatedToTheSameClass($res[$i]->OntologyClassID, $res[$j]->OntologyClassID, $plist1[$k]["PropertyID"]))
							$similars[$s++] = $plist1[$k]["PropertyLabel"];
							
						}
						else
							$similars[$s++] = $plist1[$k]["PropertyLabel"];
					}
				}
			}
			// به دو صورت می توان مقایسه کرد یکی نسبتی (تعداد خصوصیات دو کلاس در نظر گرفته شود) و تعدادی (از یک تعداد خاص خصوصیت بیشتر مشابه داشته باشد 
			//if(count($plist1)>0 && count($similars)/count($plist1)>0.5)
			if(count($plist1)>0 && count($similars)>4)
			{
				$ClassMembers[$c] = $res[$j]->label." (";
				for($s = 0; $s<count($similars); $s++)
				{
					if($s>0)
						$ClassMembers[$c] .= ",";
					$ClassMembers[$c] .= $similars[$s];
				}
				$ClassMembers[$c] .= ")";
				$c++;
				
			}
			
		}
		if(count($ClassMembers)>0)
		{
			echo "<tr><td><b>".$res[$i]->label."</b><br>";
			for($k=0; $k<count($ClassMembers); $k++)
			{
				echo $ClassMembers[$k]."<br>";
			}
			echo "</tr>";
		}
	}
	echo "<tr class=HeaderOfTable><td>";
	echo "پیشنهادهای ادغام یا برقراری رابطه سلسله مراتبی (کلاسهایی که فرزند مشترک دارند)";
	echo "</td></tr>";
	for($i=0; $i<count($res); $i++)
	{
		$plist = manage_OntologyClassHirarchy::GetParentListArray($res[$i]->OntologyClassID);
		if(count($plist)>1)
		{
			echo "<tr><td>";
			for($k=0; $k<count($plist); $k++)
			{

				if($k>0)
					echo " - ";
				echo $plist[$k]["label"];
			}
			echo "</td></tr>";			
		}
	}
	
	echo "<tr class=HeaderOfTable><td>";
	echo "روابط پدر - فرزندی نامعتبر (تکراری): کلاس پدر و فرزند هر دو زیر کلاس مستقیم یک کلاس هستند";
	echo "</td></tr>";
	for($i=0; $i<count($res); $i++)
	{
		$plist = manage_OntologyClassHirarchy::GetChildListArray($res[$i]->OntologyClassID);
		if(count($plist)>1)
		{
			for($j=0; $j<count($plist); $j++)
			{
				for($k=0; $k<count($plist); $k++)
				{
					if(manage_OntologyClassHirarchy::HasHirarchyRelation($plist[$j]["OntologyClassID"], $plist[$k]["OntologyClassID"]))
					{
						echo "<tr>";
						echo "<td><b>".$res[$i]->label.": </b>";
						echo $plist[$j]["label"]." - ".$plist[$k]["label"];
						echo "</td>";
						echo "<tr>";
					}
				}
			}
		}
	}
	
	echo "</table>";
}

function ShowDataTypeProps($ClassTitle, $OntologyID, $label, $level)
{
      if($level>5)
      	return;
      $mysql = pdodb::getInstance();
      // ابتدا خصوصیات داده مربوط به پدران این کلاس نشان داده می شود
      $query = "select c1.ClassTitle, label from projectmanagement.OntologyClassHirarchy 
		JOIN projectmanagement.OntologyClasses c1 on (c1.OntologyClassID=OntologyClassHirarchy.OntologyClassID)
		JOIN projectmanagement.OntologyClassLabels on (OntologyClassLabels.OntologyClassID=c1.OntologyClassID)
		JOIN projectmanagement.OntologyClasses c2 on (c2.OntologyClassID=OntologyClassParentID)
		where c2.ClassTitle=? and c1.OntologyID=? and c2.OntologyID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($ClassTitle, $OntologyID, $OntologyID));
	while($rec = $res->fetch())
	{
      		ShowDataTypeProps($rec["ClassTitle"], $OntologyID, $rec["label"], $level+1);
      	}      
      
      $plist = manage_OntologyClasses::GetClassRelatedProperties($ClassTitle, $OntologyID);
      for($m=0; $m<count($plist); $m++)
      {
	  if($plist[$m]["PropertyType"]=="DATATYPE")
	  {
	    echo "<tr>";
	    echo "<td >";
	    echo "<b><a href=# onclick=\"window.open('ManageOntologyProperties.php?UpdateID=".$plist[$m]["PropertyID"]."&OntologyID=".$OntologyID;
	    echo "&DoNotShowList=1";
	    echo "');\" >";
	    if($level>0)
	    	echo "[".$label."] ";
	    echo $plist[$m]["PropertyLabel"]."</a></b>";
	    $query = "select distinct PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID='".$plist[$m]["PropertyID"]."'";
	    $res = $mysql->Execute($query);
	    $j = 0;
	    while($rec = $res->fetch())
	    {
	      if($j==0)
		echo " (";
	      else
		echo " - ";
	      echo $rec["PermittedValue"];
	      $j++;
	    }
	    if($j>0)
	      echo ")";
	    echo "</td></tr>";    
	 }
      }

}

if(isset($_REQUEST["SuggestClassification"]))
{
	SuggestClassification();
	die();	
}

$mysql = pdodb::getInstance();

if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="DoMerge")
{
  $TargetClassID = $_REQUEST["TargetClassID"];
  $tobj = new be_OntologyClasses();
  $tobj->LoadDataFromDatabase($TargetClassID);
  $res = manage_OntologyClasses::GetList($_REQUEST["OntologyID"]); 
  $Options = "";
  for($k=0; $k<count($res); $k++)
  {
    if(isset($_REQUEST["ch_".$res[$k]->OntologyClassID])) 
    {
      if($res[$k]->OntologyClassID!=$TargetClassID)
	manage_OntologyClasses::MergeClasses($res[$k]->OntologyID, $res[$k]->OntologyClassID, $res[$k]->ClassTitle, $TargetClassID, $tobj->ClassTitle);
    }
  }
  echo "<table align=center><tr><td>ادغام انجام شد</td></tr></table>";
}

if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="Merge")
{
  echo "<form method=post>";
  echo "<input type=\"hidden\" id=\"OntologyID\" name=\"OntologyID\" value=\"".$_REQUEST["OntologyID"]."\">";
  echo "<table>";
  echo "<tr class=HeaderOfTable><td colspan=4>ادغام کلاس ها</td></tr>";
  echo "<tr bgcolor=#cccccc><td>کلاس</td><td>کلاسهای پدر</td><td>زیر کلاسها</td><td>خصوصیات</td></tr>";
  
  $res = manage_OntologyClasses::GetList($_REQUEST["OntologyID"]); 
  $Options = "";
  for($k=0; $k<count($res); $k++)
  {
	  if(isset($_REQUEST["ch_".$res[$k]->OntologyClassID])) 
	  {
	    $CName = "ch_".$res[$k]->OntologyClassID;
	    echo "<input type=hidden name='".$CName."' id='".$CName."' value=1>";
	    echo "<input type=hidden name='ActionType' id='ActionType' value='DoMerge'>";
	    $Options .= "<option value='".$res[$k]->OntologyClassID."'>".$res[$k]->ClassTitle;
	    $LabelsList = "";
	    $list = manage_OntologyClassLabels::GetList($res[$k]->OntologyClassID);
	    for($m=0; $m<count($list); $m++)
	    {
	      if($m>0)
		$LabelsList .= ", ";
	      $LabelsList .= $list[$m]->label;
	    }
	    $SubClassesList = "";
	    $list = manage_OntologyClassHirarchy::GetList($res[$k]->OntologyClassID);
	    for($m=0; $m<count($list); $m++)
	    {
	      if($m>0)
		$SubClassesList .= ", ";
	      $SubClassesList .= $list[$m]->OntologyClassParentID_Desc;
	    }
	  
	    echo "<tr>";
	    echo "<td>".$res[$k]->ClassTitle." (".$LabelsList.")"."</td>";
	    echo "<td>".manage_OntologyClassHirarchy::GetParentList($res[$k]->OntologyClassID)."</td>";
	    echo "<td>".$SubClassesList."</td>";
	    $plist = manage_OntologyClasses::GetClassRelatedProperties($res[$k]->ClassTitle, $res[$k]->OntologyID);
	    echo "<td>"; 
	    for($m=0; $m<count($plist); $m++)
	    {
		echo $plist[$m]["PropertyType"].": <a href=# onclick=\"window.open('ManageOntologyProperties.php?UpdateID=".$plist[$m]["PropertyID"]."&OntologyID=".$_REQUEST["OntologyID"]."');\" >".$plist[$m]["PropertyTitle"]."</a> (".$plist[$m]["PropertyLabel"].")";
		echo "<br>";    
	    }
	    echo "</td>";
	    echo "</tr>";
	  }
  }
  echo "<tr class=HeaderOfTable><td colspan=4>ادغام به کلاس: ";
  echo "<select name=TargetClassID id=TargetClassID>".$Options."</select>";
  echo "<input type=submit value='انجام'>";
  echo "</td></tr>";	  
  echo "</table>";
  echo "</form>";
  die();
}
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["OntologyID"]))
		$Item_OntologyID=$_REQUEST["OntologyID"];
	if(isset($_REQUEST["Item_ClassTitle"]))
		$Item_ClassTitle=$_REQUEST["Item_ClassTitle"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		$NewID = manage_OntologyClasses::Add($Item_OntologyID
				, $Item_ClassTitle
				);
		manage_OntologyClassLabels::Add($NewID, $_REQUEST["Item_ClassLabel"]);
		if($_REQUEST["UpperClass"]!="0")
		  manage_OntologyClassHirarchy::Add($_REQUEST["UpperClass"], $NewID);
		  
		if(isset($_REQUEST["FromTermOnto"]))
		{
		  echo "<script>\r\n";
		  echo "var obj = window.opener.document.getElementById('RelatedClassID');\r\n";
		  echo "var option = document.createElement(\"option\");\r\n";
		  echo "option.text = '".$_REQUEST["Item_ClassLabel"]." (".$Item_ClassTitle.")';\r\n";
		  echo "option.value = '".$NewID."';\r\n";
		  echo "obj.add(option);\r\n";
		  echo "obj.value='".$NewID."';\r\n";
		  echo "window.close();\r\n";
		  echo "</script>";
		}
		  
	}	
	else 
	{	
		$CurObj = new be_OntologyClasses();
		$CurObj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
		manage_OntologyClasses::Update($_REQUEST["UpdateID"] 
				, $Item_ClassTitle
				);
		/*
		if($_REQUEST["UpperClass"]!="0" && $_REQUEST["UpperClass"]!=$CurObj->UpperClassID)
		  manage_OntologyClassHirarchy::Add($_REQUEST["UpperClass"], $_REQUEST["UpdateID"]);
		if($_REQUEST["UpperClass"]=="0")
		  manage_OntologyClassHirarchy::RemoveRelation($CurObj->UpperClassID, $_REQUEST["UpdateID"]);
		*/
		if($_REQUEST["Item_ClassLabel"]!="")
		  manage_OntologyClassLabels::UpdateOrInsertFirstLabel($_REQUEST["UpdateID"], $_REQUEST["Item_ClassLabel"]);
		
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$UpperClassID = 0;
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_OntologyClasses();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_ClassTitle.value='".htmlentities($obj->ClassTitle, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_ClassLabel.value='".manage_OntologyClassLabels::GetFirstLabel($_REQUEST["UpdateID"])."'; \r\n "; 
	$UpperClassID = $obj->UpperClassID;
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["FromTermOnto"])) 
	{
	  echo "<input type=\"hidden\" name=\"FromTermOnto\" id=\"FromTermOnto\" value='1'>";
	}
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		echo manage_OntologyClasses::ShowSummary($_REQUEST["UpdateID"]);
		echo manage_OntologyClasses::ShowTabs($_REQUEST["UpdateID"], "NewOntologyClasses");
	}
echo manage_ontologies::ShowSummary($_REQUEST["OntologyID"]);
echo manage_ontologies::ShowTabs($_REQUEST["OntologyID"], "ManageOntologyClasses");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش کلاسهای هستان نگار</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="OntologyID" id="OntologyID" value='<? if(isset($_REQUEST["OntologyID"])) echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 عنوان کلاس
	</td>
	<td nowrap>
	<input dir=ltr type="text" name="Item_ClassTitle" id="Item_ClassTitle" maxlength="145" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 برچسب
	</td>
	<td nowrap>
	<input type="text" name="Item_ClassLabel" id="Item_ClassLabel" maxlength="150" size="40">
	</td>
</tr>

<? if(!isset($_REQUEST["UpdateID"])) { ?>
<tr>
	<td width="1%" nowrap>
 کلاس بالاتر
	</td>
	<td nowrap>
	<select name='UpperClass' id='UpperClass'>
	  <option value='0'>-
	  <?
	    $list = manage_OntologyClasses::GetList($_REQUEST["OntologyID"]);
	    for($i=0; $i<count($list); $i++)
	    {
	      echo "<option value='".$list[$i]->OntologyClassID."' ";
	      if($UpperClassID==$list[$i]->OntologyClassID)
		echo " selected ";
	      echo ">";
	      echo $list[$i]->label." (".$list[$i]->ClassTitle.")";
	    }
	  ?>
	</select>
<a href='#' onclick='javascript: window.open("ShowOntologyClassTree.php?ReturnID=1&InputName=UpperClass&OntologyID=<? echo $_REQUEST["OntologyID"] ?>")'>انتخاب</a>
	</td>
</tr>
<? } ?>

<? if(isset($_REQUEST["UpdateID"])) { ?>
<!---
<tr>
  <td colspan=2>
  <a href='CopyClass.php?OntologyClassID=<? echo $_REQUEST["UpdateID"]; ?>' target=_blank>Copy</a>
  </td>
</tr>
--->
<tr>
  <td colspan=2>
  <a href='ShowGraph.php?OntologyClassID=<? echo $_REQUEST["UpdateID"]; ?>' target=_blank>نمایش گراف</a>
  </td>
</tr>
<tr>
  <td>
  <a href='ManageOntologyClassProperties.php?OntologyID=<? echo $_REQUEST["OntologyID"]; ?>&OntologyClassID=<? echo $_REQUEST["UpdateID"]; ?>' target=_blank>
خصوصیات: 
  </a>
  </td>
<td>
<?
      echo "<br><table>";
      $ClassTitle = $obj->ClassTitle;
      $level = 0;
      ShowDataTypeProps($ClassTitle, $_REQUEST["OntologyID"], "", $level);
      
      ShowValidRelations($_REQUEST["UpdateID"], $_REQUEST["OntologyID"], "", $level);
      echo "</table>";
?>
</td>
</tr>
<? if(isset($_REQUEST["UpdateID"])) { ?>
<tr>
  <td colspan=2><hr></td>
</tr>
<tr>
  <td nowrap>
  <a href='ManageOntologyClassParents.php?OntologyClassID=<? echo $_REQUEST["UpdateID"] ?>&OntologyID=<? echo $_REQUEST["OntologyID"] ?>' target=_blank>
  کلاسهای پدر:
  </a>
  </td>
  <td>
  <?
    $list = manage_OntologyClassHirarchy::GetParentListArray($_REQUEST["UpdateID"]);
    //print_r($list);
    for($m=0; $m<count($list); $m++)
    {
      echo "<b><a target=_blank href='ManageOntologyClasses.php?UpdateID=".$list[$m]["OntologyClassID"]."&OntologyID=".$_REQUEST["OntologyID"]."&OnlyEditForm=1'>";
      echo $list[$m]["label"]." (".$list[$m]["ClassTitle"].")";
      echo "</a></b>";
      echo "<br>";
    }

  ?>
  </td>
</tr>
<tr>
  <td colspan=2><hr></td>
</tr>
<tr>
  <td nowrap>
  <a href='ManageOntologyClassChilds.php?OntologyClassID=<? echo $_REQUEST["UpdateID"] ?>&OntologyID=<? echo $_REQUEST["OntologyID"] ?>' target=_blank>
  کلاسهای فرزند:
  </a>
  </td>
  <td>
  <?
    $list = manage_OntologyClassHirarchy::GetList($_REQUEST["UpdateID"]);
    for($m=0; $m<count($list); $m++)
    {
      echo "<b><a target=_blank href='ManageOntologyClasses.php?OnlyEditForm=1&UpdateID=".$list[$m]->OntologyClassParentID."&OntologyID=".$_REQUEST["OntologyID"]."'>";
      echo $list[$m]->OntologyClassParentID_Desc;
      echo "</a></b>";
      echo "<br>";
    }

  ?>
  </td>
</tr>
<? } ?>
<tr>
  <td colspan=2><hr></td>
</tr>
<tr>
  <td nowrap>حاصل ادغام: </td>
  <td>
  <table dir=ltr>
<?
  $mysql->Prepare("select distinct OntologyClasses.OntologyID, OntologyTitle, ClassTitle, EntityID from projectmanagement.OntologyMergeEntities 
			    JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyMergeEntities.EntityID)
			    JOIN projectmanagement.ontologies using (OntologyID)
				  where TargetEntityType='CLASS' and TargetEntityID=?");
  $res = $mysql->ExecuteStatement(array($_REQUEST["UpdateID"]));
  while($rec = $res->fetch())
  {
    echo "<tr><td>".$rec["OntologyTitle"]."</td>";
    echo "<td><a target=_blank href='ManageOntologyClasses.php?OnlyEditForm=1&UpdateID=".$rec["EntityID"]."&OntologyID=".$rec["OntologyID"]."'>".$rec["ClassTitle"]."</a></td></tr>";
  }
?>
  </tr>
  </table>
  </td>
</tr>
<? } ?>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageOntologyClasses.php?OntologyID=<?php echo $_REQUEST["OntologyID"]; ?>'" value="جدید">
 <input type="button" onclick="javascript: window.close();" value="بستن">
</td>
</tr>
</table>
<? if(isset($_REQUEST["OnlyEditForm"])) { ?>
<input type="hidden" name="OnlyEditForm" id="OnlyEditForm" value="1">
<? } ?>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
if(isset($_REQUEST["OnlyEditForm"]))
	die();
$res = manage_OntologyClasses::GetList($_REQUEST["OntologyID"]); 
$SomeItemsRemoved = false;
if(isset($_REQUEST["ActionType"]))
{
  for($k=0; $k<count($res); $k++)
  {
	  if($_REQUEST["ActionType"]=="Remove" && isset($_REQUEST["ch_".$res[$k]->OntologyClassID])) 
	  {
		  manage_OntologyClasses::Remove($res[$k]->OntologyClassID); 
		  $SomeItemsRemoved = true;
	  }
	  if(isset($_REQUEST["label_".$res[$k]->OntologyClassID]) && $_REQUEST["label_".$res[$k]->OntologyClassID]!="") 
	  {
		  manage_OntologyClassLabels::Add($res[$k]->OntologyClassID, $_REQUEST["label_".$res[$k]->OntologyClassID]);
		  $SomeItemsRemoved = true;
	  }
  }
}
if($SomeItemsRemoved)
	$res = manage_OntologyClasses::GetList($_REQUEST["OntologyID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_OntologyID" name="Item_OntologyID" value="<? echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	کلاسهای هستان نگار
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>عنوان کلاس</td>
	<td nowrap>برچسبها</td>
	<td nowrap>زیر کلاسها</td>
	<td nowrap>کلاسهای پدر</td>
        <td nowrap>خصوصیات مرتبط</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
    $LabelsList = "";
    $list = manage_OntologyClassLabels::GetList($res[$k]->OntologyClassID);
    for($m=0; $m<count($list); $m++)
    {
      if($m>0)
	$LabelsList .= ", ";
      $LabelsList .= $list[$m]->label;
    }
    
    $SubClassesList = "";
    $list = manage_OntologyClassHirarchy::GetList($res[$k]->OntologyClassID);
    for($m=0; $m<count($list); $m++)
    {
      if($m>0)
	$SubClassesList .= ", ";
      $SubClassesList .= $list[$m]->OntologyClassParentID_Desc;
    }
    
    $RelatedPropList = "";
    $plist = manage_OntologyClasses::GetClassRelatedProperties($res[$k]->ClassTitle, $_REQUEST["OntologyID"]);
    for($m=0; $m<count($plist); $m++)
    {
        $RelatedPropList .= $plist[$m]["PropertyType"].": ".$plist[$m]["PropertyTitle"]." (".$plist[$m]["PropertyLabel"].")";
        $RelatedPropList .= "<br>";    
    }
    
    $ParentClassList = manage_OntologyClassHirarchy::GetParentList($res[$k]->OntologyClassID);
    
    if($RelatedPropList=="" && $SubClassesList=="" && $ParentClassList=="")
	echo "<tr bgcolor=\"#ff4d4d\">";
    else if($RelatedPropList=="")
	echo "<tr bgcolor=\"#ffbbbb\">";        
    else if($k%2==0)
	    echo "<tr class=\"OddRow\">";
    else
	    echo "<tr class=\"EvenRow\">";
	   
    echo "<td>";
    echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyClassID."\">";
    echo "</td>";
    echo "<td>".($k+1)."</td>";
    echo "	<td><a target=_blank href=\"ManageOntologyClasses.php?UpdateID=".$res[$k]->OntologyClassID."&OntologyID=".$_REQUEST["OntologyID"]."&OnlyEditForm=1\"><img src='images/edit.gif' title='ویرایش'></a></td>";
    echo "	<td dir=ltr>".htmlentities($res[$k]->ClassTitle, ENT_QUOTES, 'UTF-8');
    echo "	</td>";
    echo "	<td>".$LabelsList." ";
    $SuggestedLabel = "";
    if($LabelsList=="")
    {
      $SuggestedLabel = manage_OntologyClasses::GetSuggestedLabel($res[$k]->ClassTitle);
      // ok      
    }
    
    echo "<br><input type=text name='label_".$res[$k]->OntologyClassID."' id='label_".$res[$k]->OntologyClassID."' value='".$SuggestedLabel."'>";
    echo "<a  target=\"_blank\" href='ManageOntologyClassLabels.php?OntologyClassID=".$res[$k]->OntologyClassID ."'>[ویرایش]</a>";
    echo " </td>";
    echo "<td>".str_replace(", ","<br>", $SubClassesList)." ";
    echo "<a  target=\"_blank\" href='ManageOntologyClassHirarchy.php?OntologyClassID=".$res[$k]->OntologyClassID ."'>[ویرایش]</a>";
    echo "</td>";
    echo "<td>".str_replace(", ","<br>", $ParentClassList)."</td>";

    echo "<td>";
    echo $RelatedPropList;
    echo "</td>";

    echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="8" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
	<input type="button" onclick="javascript: SendMerge();" value="ادغام">
	<input type=submit value='ذخیره'>
	<input type="button" onclick="javascript: document.location='ManageOntologyClasses.php?OntologyID=<? echo $_REQUEST["OntologyID"] ?>&SuggestClassification=1'" value="پیشنهاد دسته بندی">
</td>
</tr>
</table>
<input type=hidden name='ActionType' id='ActionType' value=''>
</form>
<form target="_blank" method="post" action="NewOntologyClasses.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyID" name="OntologyID" value="<? echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) 
	{
	  document.getElementById('ActionType').value='Remove';
	  document.ListForm.submit();
	}
}
function SendMerge()
{
  document.getElementById('ActionType').value='Merge';
  document.ListForm.submit();
}
</script>
</html>
