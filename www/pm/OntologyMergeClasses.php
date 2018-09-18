<?
	include("header.inc.php");
	include("classes/OntologyClasses.class.php");
	
	function GetSourceTablePKs($ClassID)
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare("select (select group_concat(FieldName,' ') from mis.MIS_TableFields where MIS_TableFields.DBName=MIS_Tables.DBName and MIS_TableFields.TableName=MIS_Tables.name and KeyType='PRI') as PKs 
					  from projectmanagement.RDB2OntoLog 
					  JOIN mis.MIS_Tables on (id=RDBEntityID)
					  where OntoEntityID=? and OntoEntityType='CLASS'");
	  $res = $mysql->ExecuteStatement(array($ClassID));
	  if($rec = $res->fetch())
	  {
	    return $rec["PKs"];
	  }
	  return "";
	}
	
	function ShowClassMergeSuggestions($TargetOnto)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select OntologyMergeReviewedPotentialID
	  		  , ExtraInfo	
	  		  , ExtraInfo2
			  ,p1.OntologyClassID as ClassID1
			  ,p1.ClassTitle as ClassTitle1
			  ,l1.label as ClassLabel1
			  ,p2.OntologyClassID as ClassID2
			  ,p2.ClassTitle as ClassTitle2
			  ,l2.label as ClassLabel2
			  from projectmanagement.OntologyMergeReviewedPotentials 
			  JOIN projectmanagement.OntologyClasses p1 on (p1.OntologyClassID=EntityID1)
			  JOIN projectmanagement.OntologyClassLabels l1 on (l1.OntologyClassID=p1.OntologyClassID)
			  JOIN projectmanagement.OntologyClasses p2 on (p2.OntologyClassID=EntityID2)
			  JOIN projectmanagement.OntologyClassLabels l2 on (l2.OntologyClassID=p2.OntologyClassID)
			  where TargetOntologyID=? and EntityType1='CLASS' and ActionType='NOT_DECIDE'";
;
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($TargetOnto));
	  $i=0;
	  while($rec = $res->fetch())
	  {
	    $i++;
	    $MergeID = $rec["OntologyMergeReviewedPotentialID"];
	    $pos = strpos($rec["ExtraInfo2"], "(MUL)");
	    if ($pos === false) 
		    echo "<tr bgcolor=#cccccc>";
	    else
	    		echo "<tr>";
	    echo "<td>".$i."</td>";
	    echo "<td>".$rec["ClassLabel1"]."</td><td dir=ltr>".GetSourceTablePKs($rec["ClassID1"])."</td>";
	    echo "<td>";
	    echo "<a href='ManageOntologyClasses.php?UpdateID=".$rec["ClassID2"]."&OntologyID=".$TargetOnto."' target=_blank>";
	    echo $rec["ClassLabel2"];
	    // GetSourceTablePKs($rec["ClassID2"])." * ".
	    echo "</a></td>";
	    echo "<td dir=ltr>".$rec["ExtraInfo2"]."</td>";
	    echo "<td dir=ltr>".GetSourceTablePKs($rec["ClassID2"])."</td>";
	    echo "<td><select name=AT_".$MergeID." id=AT_".$MergeID.">";
	    echo "<option value='1'>رابطه: ".$rec["ExtraInfo"];
	    echo "<option value='2'>".$rec["ClassLabel2"]." زیر کلاس ".$rec["ClassLabel1"]." است";
	    echo "<option value='4'>".$rec["ClassLabel1"]." زیر کلاس ".$rec["ClassLabel2"]." است";
	    echo "<option value='3'>".$rec["ClassLabel2"]." در ".$rec["ClassLabel1"]." ادغام شود";
	    echo "<option value='5'>".$rec["ClassLabel1"]." در ".$rec["ClassLabel2"]." ادغام شود";
	    echo "</select></td>";
	    echo "</tr>";
	  }
	}
	
	function RemoveObjectPropAndRelatedSuggestions($TargetOnto, $RelatedPropertyTitle)
	{
		$mysql = pdodb::getInstance();
		$query = "select OntologyPropertyID from projectmanagement.OntologyProperties where OntologyID=? and PropertyTitle='".$RelatedPropertyTitle."'";
	      $mysql->Prepare($query);
	      $rres = $mysql->ExecuteStatement(array($TargetOnto));	
	      if($rrec = $rres->fetch())
	      {
	      // خصوصیت شیء معادل را یافته و چون قرار است حذف شود پیشنهادات ادغام مربوطه را هم حذف می کند
	      // توجه شود در اینجا عنوان خصوصیت از نام جدول و فیلد ساخته شده و منحصر بفرد است
	      		$RelatedPropertyID = $rrec["OntologyPropertyID"];
	      		$query = "delete from projectmanagement.OntologyMergeReviewedPotentials where (EntityID1='".$RelatedPropertyID."' or EntityID2='".$RelatedPropertyID."') and EntityType1='OBJPROP' and TargetOntologyID=?";
	      		$mysql->Prepare($query);
	      		$mysql->ExecuteStatement(array($TargetOnto));	
	      		$query = "delete from projectmanagement.OntologyProperties where OntologyPropertyID='".$RelatedPropertyID."'"; 
	      		$mysql->Execute($query);	
	      }
	}
	
	//$rec["ClassLabel2"]
	// چون قرار است کلاس دوم حذف شود و خصوصیات آن به خصوصیات کلاس اول منتقل شوند
	// در انتهای برچسب خصوصیات هم برچسب کلاس ادغام شده اضافه می شود تا در تجمیع با کلاس اصلی مشخص باشند	
	function AddClassLabelToPropsLabel($TargetOnto, $MergedClassLabel, $MergedClassTitle)
	{
		
	    $mysql = pdodb::getInstance();
	    $query = "update projectmanagement.OntologyPropertyLabels set label = concat(label,' در ','".$MergedClassLabel."') where OntologyPropertyID in (
	    select OntologyPropertyID from projectmanagement.OntologyProperties 
	    where OntologyID=? and PropertyType='DATATYPE' and 
	    ((domain like '".$MergedClassTitle.",%' or domain like '%, ".$MergedClassTitle.",%' or domain like '%, ".$MergedClassTitle."' or domain='".$MergedClassTitle."') or 
	    (`range` like '".$MergedClassTitle.",%' or `range` like '%, ".$MergedClassTitle.",%' or `range` like '%, ".$MergedClassTitle."' or `range`='".$MergedClassTitle."')) 
	    )";
	    //echo $query."<br>";
	    //return;
	    $mysql->Prepare($query);
	    $mysql->ExecuteStatement(array($TargetOnto));	      
	}

	function IsClassExist($ClassID)
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("select * from projectmanagement.OntologyClasses where OntologyClassID=?");
		$res = $mysql->ExecuteStatement(array($ClassID));
		if($rec = $res->fetch())
		{
			return true;
		}
		return false;
	}
	
	function DoMerge($TargetOnto)
	{
	  $mysql = pdodb::getInstance();
	  $query = "select OntologyMergeReviewedPotentialID
			  ,p1.OntologyClassID as ClassID1
			  ,p1.ClassTitle as ClassTitle1
			  ,l1.label as ClassLabel1
			  ,p2.OntologyClassID as ClassID2
			  ,p2.ClassTitle as ClassTitle2
			  ,l2.label as ClassLabel2
			  , ExtraInfo, ExtraInfo2
			  from projectmanagement.OntologyMergeReviewedPotentials 
			  JOIN projectmanagement.OntologyClasses p1 on (p1.OntologyClassID=EntityID1)
			  JOIN projectmanagement.OntologyClassLabels l1 on (l1.OntologyClassID=p1.OntologyClassID)
			  JOIN projectmanagement.OntologyClasses p2 on (p2.OntologyClassID=EntityID2)
			  JOIN projectmanagement.OntologyClassLabels l2 on (l2.OntologyClassID=p2.OntologyClassID)
			  where TargetOntologyID=? and EntityType1='CLASS' and ActionType='NOT_DECIDE'";

	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($TargetOnto));
	  $i=0;
	  $ChangedClasses = array();
	  $k = 0;
	  while($rec = $res->fetch())
	  {
	    $i++;
	    $MergeID = $rec["OntologyMergeReviewedPotentialID"];
	    $RelatedPropertyTitle = $rec["ExtraInfo2"];
	    if(!IsClassExist($rec["ClassID1"]) || !IsClassExist($rec["ClassID2"]))
	    {
	    	echo $rec["ClassTitle1"]."->".$rec["ClassTitle2"]."<br>";
	    }
	    else if($_REQUEST["AT_".$MergeID]=="3")
	    {
	      // چون قرار است کلاس دوم حذف شود و خصوصیات آن به خصوصیات کلاس اول منتقل شوند
	      // در انتهای برچسب خصوصیات هم برچسب کلاس ادغام شده اضافه می شود تا در تجمیع با کلاس اصلی مشخص باشند
	    	AddClassLabelToPropsLabel($TargetOnto, $rec["ClassLabel2"], $rec["ClassTitle2"]);  
	      
	      manage_OntologyClasses::MergeClasses($TargetOnto, $rec["ClassID2"], $rec["ClassTitle2"], $rec["ClassID1"], $rec["ClassTitle1"]);
	      RemoveObjectPropAndRelatedSuggestions($TargetOnto, $RelatedPropertyTitle);
	      $mysql->Execute("update projectmanagement.OntologyMergeReviewedPotentials set ActionType='MERGE', ResultEntityID='".$rec["ClassID1"]."', ResultEntityType='CLASS' where OntologyMergeReviewedPotentialID=".$MergeID);
	      
// برای رکوردهای پیشنهاد بعدی ادغام کلاس حذف شده را با کلاس دیگر جایگزین می کند
	      echo $query = "update projectmanagement.OntologyMergeReviewedPotentials set EntityID1='".$rec["ClassID1"]."' where EntityID1='".$rec["ClassID2"]."' and  EntityType1='CLASS' and ActionType='NOT_DECIDE'";
	      echo "<br>";
	      $mysql->Execute($query);
	      echo $query = "update projectmanagement.OntologyMergeReviewedPotentials set EntityID2='".$rec["ClassID1"]."' where EntityID2='".$rec["ClassID2"]."' and  EntityType2='CLASS' and ActionType='NOT_DECIDE'";
	      echo "<br>";
	      $mysql->Execute($query);
	      	      
	    } 
	    else if($_REQUEST["AT_".$MergeID]=="5")
	    {
	      // چون قرار است کلاس اول حذف شود و خصوصیات آن به خصوصیات کلاس دوم منتقل شوند
	      // در انتهای برچسب خصوصیات هم برچسب کلاس ادغام شده اضافه می شود تا در تجمیع با کلاس اصلی مشخص باشند
		AddClassLabelToPropsLabel($TargetOnto, $rec["ClassLabel1"], $rec["ClassTitle1"]);
	      manage_OntologyClasses::MergeClasses($TargetOnto, $rec["ClassID1"], $rec["ClassTitle1"], $rec["ClassID2"], $rec["ClassTitle2"]);
	      RemoveObjectPropAndRelatedSuggestions($TargetOnto, $RelatedPropertyTitle);
	      $mysql->Execute("update projectmanagement.OntologyMergeReviewedPotentials set ActionType='MERGE', ResultEntityID='".$rec["ClassID2"]."', ResultEntityType='CLASS'  where OntologyMergeReviewedPotentialID=".$MergeID);
	      
	      // برای رکوردهای پیشنهاد بعدی ادغام کلاس حذف شده را با کلاس دیگر جایگزین می کند
	      echo $query = "update projectmanagement.OntologyMergeReviewedPotentials set EntityID1='".$rec["ClassID2"]."' where EntityID1='".$rec["ClassID1"]."' and  EntityType1='CLASS' and ActionType='NOT_DECIDE'";
	      echo "<br>";
	      $mysql->Execute($query);
	      echo $query = "update projectmanagement.OntologyMergeReviewedPotentials set EntityID2='".$rec["ClassID2"]."' where EntityID2='".$rec["ClassID1"]."' and  EntityType2='CLASS' and ActionType='NOT_DECIDE'";
	      echo "<br>";
	      $mysql->Execute($query);
	      	      
	    }
	    else if($_REQUEST["AT_".$MergeID]=="2")
	    {
	      $query = "insert into projectmanagement.OntologyClassHirarchy (OntologyClassID, OntologyClassParentID) values ('".$rec["ClassID1"]."', '".$rec["ClassID2"]."')";
	      //echo $query."<br>";
	      $mysql->Execute($query);
	      
	      RemoveObjectPropAndRelatedSuggestions($TargetOnto, $RelatedPropertyTitle);
	      
	      $mysql->Execute("update projectmanagement.OntologyMergeReviewedPotentials set ActionType='NOT_MERGE' where OntologyMergeReviewedPotentialID=".$MergeID);
	    }
	    else if($_REQUEST["AT_".$MergeID]=="4")
	    {
	      $query = "insert into projectmanagement.OntologyClassHirarchy (OntologyClassID, OntologyClassParentID) values ('".$rec["ClassID2"]."', '".$rec["ClassID1"]."')";
	      //echo $query."<br>";
	      $mysql->Execute($query);
	      
	      RemoveObjectPropAndRelatedSuggestions($TargetOnto, $RelatedPropertyTitle);
	      
	      $mysql->Execute("update projectmanagement.OntologyMergeReviewedPotentials set ActionType='NOT_MERGE' where OntologyMergeReviewedPotentialID=".$MergeID);
	    }	    
	    else  if($_REQUEST["AT_".$MergeID]=="1")
	    {
	      $mysql->Execute("update projectmanagement.OntologyMergeReviewedPotentials set ActionType='NOT_MERGE' where OntologyMergeReviewedPotentialID=".$MergeID);
	    }
	    
	  }
	}
	
	
	$TargetOnto = $_REQUEST["TargetOnto"];
	if(isset($_REQUEST["DoMerge"]))
	{
	  DoMerge($TargetOnto);
	}
	
	echo "<form method=post>";
	echo "<input type=hidden name=DoMerge id=DoMerge value=1>";
	echo "<input type=hidden name=TargetOnto id=TargetOnto value='".$TargetOnto."'>";
	echo "<table width=90% align=center border=1 cellspacing=0>";
	echo "<tr class=HeaderOfTable><td colspan=10 align=center>پیشنهادات ادغام</td></tr>";
	echo "<tr bgcolor=#cccccc><td width=1%>ردیف</td><td>کلاس ۱</td><td>کلیدهای اصلی ۱</td><td>کلاس ۲</td><td>کلید ارتباطی</td><td>کلیدهای اصلی ۲</td><td width=1%>&nbsp;</td>";
	echo "</tr>";
	ShowClassMergeSuggestions($TargetOnto);	
	echo "<tr class=FooterOfTable><td colspan=10 align=center><input type=submit value='انجام ادغام'></td></tr>";
	echo "</table>";
	echo "</form>";
	HTMLBegin();
?>
</body></html>