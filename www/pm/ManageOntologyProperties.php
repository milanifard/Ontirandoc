<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : خصوصیات هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyProperties.class.php");
include("classes/OntologyPropertyLabels.class.php");
include ("classes/ontologies.class.php");
HTMLBegin();

function NumberOfValidRelation($ObjectPropertyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select count(*) as tcount from projectmanagement.OntologyObjectPropertyRestriction where RelationStatus='VALID' and OntologyPropertyID=?");
	$res = $mysql->ExecuteStatement(array($ObjectPropertyID));
	$rec = $res->fetch();
	return $rec["tcount"];
}

function ShowClassListLabels($ClassListString, $OntologyID, $DomainOrRange, $NotifyNotUsed)
{
  $mysql = pdodb::getInstance();
  $ClassList = explode(", ",$ClassListString);
  for($i=0; $i<count($ClassList); $i++)
  {
    $mysql->Prepare("select * from projectmanagement.OntologyClasses JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) where ClassTitle=? and OntologyID=?");
    $res = $mysql->ExecuteStatement(array($ClassList[$i], $OntologyID));
    if($rec = $res->fetch())
    {
    	$sw = IsInRestriction($DomainOrRange, $rec["OntologyClassID"], $_REQUEST["UpdateID"]);
    	if(!$sw && $NotifyNotUsed) echo "<font color=red>";
      echo $rec["label"]." (<a href='ManageOntologyClasses.php?UpdateID=".$rec["OntologyClassID"]."&OntologyID=".$OntologyID."&OnlyEditForm=1' target=_blank>".$ClassList[$i]."</a>)<br>";
      	if(!$sw && $NotifyNotUsed) echo "</font>";
    }
    else
    {
    	echo "<font color=red>".C_UNKNOWN."</font> (".$ClassList[$i].")<br>";
    }
  }
}

function IsInRestriction($DomainOrRange, $ClassID, $PropertyID)
{
  $mysql = pdodb::getInstance();
  if($DomainOrRange=="DOMAIN")
 	$query = "select count(*) as tcount from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID='".$PropertyID."' and DomainClassID='".$ClassID."' and RelationStatus='VALID'";
  else
  	$query = "select count(*) as tcount from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID='".$PropertyID."' and RangeClassID='".$ClassID."' and RelationStatus='VALID'";
  //echo $query."<br>";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array());
  $rec = $res->fetch();
  //echo $rec["tcount"]."<br>";
  if($rec["tcount"]>0)
  	return true;
  return false;
  
}

function GetAllPossibleLinks($PropertyID, $OntologyID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $mysql->Prepare("select *,
  (select group_concat(label) from projectmanagement.OntologyPropertyLabels where OntologyPropertyLabels.OntologyPropertyID=OntologyProperties.OntologyPropertyID) as label 
  from projectmanagement.OntologyProperties where OntologyPropertyID=?");
  $res = $mysql->ExecuteStatement(array($PropertyID));
  if($rec = $res->fetch())
  {
    $domain = $rec["domain"];
    $range = $rec["range"];
    $DomainClassesList = $RangeClassesList = "0";
    $DomainArray = explode(", ", $domain);
    $RangeArray = explode(", ", $range);
    $PropertyLabel = $rec["label"];
    for($i=0; $i<count($DomainArray); $i++)
    {
      for($j=0; $j<count($RangeArray); $j++)
      {
	    $query = "select OntologyClassID,
	   (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID) as label 
	   from projectmanagement.OntologyClasses where ClassTitle='".$DomainArray[$i]."' and OntologyID=?";
	   $mysql->Prepare($query);
	   $res = $mysql->ExecuteStatement(array($OntologyID));
	   $rec = $res->fetch();
	   $DomainClassID = $rec["OntologyClassID"];
	   if($DomainClassID!="")
	    $DomainClassesList .= ", ".$DomainClassID;
	   else
	    continue;
	   $DomainClassLabel = $rec["label"];
	   
	   $query = "select OntologyClassID,
	   (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID) as label 
	   from projectmanagement.OntologyClasses where ClassTitle='".$RangeArray[$j]."'  and OntologyID=?";
	   $mysql->Prepare($query);
	   $res = $mysql->ExecuteStatement(array($OntologyID));
	   $rec = $res->fetch();
	   $RangeClassID = $rec["OntologyClassID"];
	   if($RangeClassID!="")
	    $RangeClassesList .= ", ".$RangeClassID;
	   else 
	    continue;
	   $RangeClassLabel = $rec["label"];
	   $mysql->Prepare("select * from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID=? and DomainClassID='".$DomainClassID."' and RangeClassID='".$RangeClassID."'");
	   $res = $mysql->ExecuteStatement(array($PropertyID));
	   if($rec = $res->fetch())
	   {
	      $RelationStatus = $rec["RelationStatus"];
	      $OntologyObjectPropertyRestrictionID = $rec["OntologyObjectPropertyRestrictionID"];

	      //$DomainClassCardinality = $rec["DomainClassCardinality"];
	      //$RangeClassCardinality = $rec["RangeClassCardinality"];
	      
	      if(isset($_REQUEST["Save"])) 
	      {
		$CheckBoxID = "r_".$OntologyObjectPropertyRestrictionID;
		$SBoxID = "c_".$OntologyObjectPropertyRestrictionID;
		//echo "*";
		if(isset($_REQUEST[$CheckBoxID]))
		{
			//echo "+";
		  //$DomainClassCardinality = $_REQUEST["d_".$SBoxID];
		  //$RangeClassCardinality = $_REQUEST["r_".$SBoxID];
		
		  $mysql->Prepare("update projectmanagement.OntologyObjectPropertyRestriction 
				    set RelationStatus='VALID'			    
				    where OntologyObjectPropertyRestrictionID=".$OntologyObjectPropertyRestrictionID);
		  $mysql->ExecuteStatement(array());
		  $RelationStatus = "VALID";
		}
		else 
		{
			//echo "-<br>";
		  $query = "update projectmanagement.OntologyObjectPropertyRestriction set RelationStatus='INVALID' where OntologyObjectPropertyRestrictionID=".$OntologyObjectPropertyRestrictionID;
		  //echo $query . "<br>";
		  $mysql->Execute($query);
		  $RelationStatus = "INVALID";
		}
	      }
	   }
	   else 
	   {
	      $mysql->Prepare("insert into projectmanagement.OntologyObjectPropertyRestriction (OntologyPropertyID, DomainClassID, RangeClassID, RelationStatus) values (?, '".$DomainClassID."', '".$RangeClassID."', 'INVALID')");
	      $mysql->ExecuteStatement(array($PropertyID));
	      $mysql->Prepare("select * from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID=? and DomainClassID='".$DomainClassID."' and RangeClassID='".$RangeClassID."'");
	      $res = $mysql->ExecuteStatement(array($PropertyID));
	      $rec = $res->fetch();
	      $RelationStatus = $rec["RelationStatus"];
	      //$DomainClassCardinality = $rec["DomainClassCardinality"];
	      //$RangeClassCardinality = $rec["RangeClassCardinality"];
	      $OntologyObjectPropertyRestrictionID = $rec["OntologyObjectPropertyRestrictionID"];
	   }
	   $CheckBoxID = "r_".$OntologyObjectPropertyRestrictionID;
	   $SBoxID = "c_".$OntologyObjectPropertyRestrictionID;
	   $ret .= "<input type=checkbox name='".$CheckBoxID."' id='".$CheckBoxID."' ";
	   if($RelationStatus=="VALID")
	    $ret .= " checked ";
	   $ret .= ">";
	/*
	   $ret .= "<select name=d_".$SBoxID." id=d_".$SBoxID.">";
	   $ret .= "<option value='1'>1";
	   $ret .= "<option value='N' ";
	   if($DomainClassCardinality=="N")
	    $ret .= " selected ";
	   $ret .= " >چند";
	   $ret .= "</select>";
	 */ 
	   $ret .= $DomainClassLabel;

	   $ret .= " <b>[".$PropertyLabel."]</b> ";
/*
	   $ret .= "<select name=r_".$SBoxID." id=r_".$SBoxID.">";
	   $ret .= "<option value='1'>1";
	   $ret .= "<option value='N' ";
	   if($RangeClassCardinality=="N")
	    $ret .= " selected ";
	   $ret .= " >چند";
	   $ret .= "</select>";
	   */
	   
	   $ret .= $RangeClassLabel."<br>";
      }
    }
    $query = "delete from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID=? and (DomainClassID not in (".$DomainClassesList.") or RangeClassID not in (".$RangeClassesList."))";
    $mysql->Prepare($query);
    $mysql->ExecuteStatement(array($PropertyID));
    
  }
  return $ret;
}

$mysql = pdodb::getInstance();

if(isset($_REQUEST["RemovePropertyID"])) 
{
	manage_OntologyProperties::Remove($_REQUEST["RemovePropertyID"]); 
	echo "<p align=center>".C_REMOVED."</p>";
	die();
}
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["OntologyID"]))
		$Item_OntologyID=$_REQUEST["OntologyID"];
	if(isset($_REQUEST["Item_PropertyTitle"]))
		$Item_PropertyTitle=$_REQUEST["Item_PropertyTitle"];
	if(isset($_REQUEST["Item_PropertyType"]))
		$Item_PropertyType=$_REQUEST["Item_PropertyType"];
	if(isset($_REQUEST["Item_IsFunctional"]))
		$Item_IsFunctional=$_REQUEST["Item_IsFunctional"];
	if(isset($_REQUEST["Item_domain"]))
		$Item_domain=$_REQUEST["Item_domain"];
	if(isset($_REQUEST["Item_range"]))
		$Item_range=$_REQUEST["Item_range"];
	if(isset($_REQUEST["Item_inverseOf"]))
		$Item_inverseOf=$_REQUEST["Item_inverseOf"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		$PropID = manage_OntologyProperties::Add($Item_OntologyID
				, $Item_PropertyTitle
				, $Item_PropertyType
				, $Item_IsFunctional
				, $Item_domain
				, $Item_range
				, $Item_inverseOf
				);
		$Item_label = $_REQUEST["Item_label"];
		manage_OntologyPropertyLabels::Add($PropID, $Item_label);
		
		GetAllPossibleLinks($PropID, $_REQUEST["OntologyID"]);
		
		if(isset($_REQUEST["FromTermOnto"]))
		{
		  echo "<script>\r\n";
		  if($Item_PropertyType=="DATATYPE")
		    echo "var obj = window.opener.document.getElementById('RelatedDataPropID');\r\n";
		  else 
		    echo "var obj = window.opener.document.getElementById('RelatedObjPropID');\r\n";		      
		  
		  echo "var option = document.createElement(\"option\");\r\n";
		  echo "option.text = '".$Item_label." (".$Item_PropertyTitle.")';\r\n";
		  echo "option.value = '".$PropID."';\r\n";
		  echo "obj.add(option);\r\n";
		  echo "obj.value='".$PropID."';\r\n";
		  echo "window.close();\r\n";
		  echo "</script>";
		}
		
	}	
	else 
	{	
		manage_OntologyProperties::Update($_REQUEST["UpdateID"] 
				, $Item_PropertyTitle
				, $Item_PropertyType
				, $Item_IsFunctional
				, $Item_domain
				, $Item_range
				, $Item_inverseOf
				);
		$Item_label = $_REQUEST["Item_label"];
		manage_OntologyPropertyLabels::UpdateOrInsertFirstLabel($_REQUEST["UpdateID"], $Item_label);
	}	
	echo SharedClass::CreateMessageBox(C_DATA_SAVE_SUCCESS);
}
$LoadDataJavascriptCode = '';
$range = $domain = '';
$PropType = '';
if(isset($_REQUEST["UpdateID"])) 
{	
    $obj = new be_OntologyProperties();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
    $LoadDataJavascriptCode .= "document.f1.Item_PropertyTitle.value='".htmlentities($obj->PropertyTitle, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
    $LoadDataJavascriptCode .= "document.f1.Item_PropertyType.value='".htmlentities($obj->PropertyType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
    $PropType = $obj->PropertyType;
    $LoadDataJavascriptCode .= "document.f1.Item_IsFunctional.value='".htmlentities($obj->IsFunctional, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
    $domain = htmlentities($obj->domain, ENT_QUOTES, 'UTF-8'); 
    $range = htmlentities($obj->range, ENT_QUOTES, 'UTF-8'); 
    $LoadDataJavascriptCode .= "document.f1.Item_inverseOf.value='".htmlentities($obj->inverseOf, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_label.value='".manage_OntologyPropertyLabels::GetFirstLabel($_REQUEST["UpdateID"])."'; \r\n "; 
    
}	
else if(isset($_REQUEST["DataProp"]))
{
  $LoadDataJavascriptCode .= "document.f1.Item_PropertyType.value='DATATYPE'; \r\n "; 
}
?>
<div class="row">
    <div class="col-1"></div>
    <div class="col-10">
		<form method="post" id="f1" name="f1" >
			<input type="hidden" name="Save" id="Save" value="1">
			<?
			  if(isset($_REQUEST["DoNotShowList"]))
			  {
				echo "<input type=\"hidden\" name=\"DoNotShowList\" id=\"DoNotShowList\" value='1'>";
			  }
			  
			  if(isset($_REQUEST["UpdateID"])) 
			  {
				echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
				echo manage_OntologyProperties::ShowSummary($_REQUEST["UpdateID"]);
				echo manage_OntologyProperties::ShowTabs($_REQUEST["UpdateID"], "NewOntologyProperties");
			  }
			  echo manage_ontologies::ShowSummary($_REQUEST["OntologyID"]);
			  echo manage_ontologies::ShowTabs($_REQUEST["OntologyID"], "ManageOntologyProperties");
			?>
			<br>
			<table class="table table-sm table-borderless">
				<thead>
					<tr class="table-info">
						<td class="text-center"><? echo C_CREATE."/".C_EDIT." ".C_ONTOLOGY_FEATURES ?></td>
					</tr>
				</thead>
				<tr>
				<td>
					<table>
						<? 
							if(isset($_REQUEST["FromTermOnto"])) 
							{
							  echo "<input type=\"hidden\" name=\"FromTermOnto\" id=\"FromTermOnto\" value='1'>";
							}

						if(!isset($_REQUEST["UpdateID"]))
						{
						?> 
						<input type="hidden" name="OntologyID" id="OntologyID" value='<? if(isset($_REQUEST["OntologyID"])) echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>'>
						<? } ?>
						<tr>
							<td width="1%" nowrap><? echo C_TITLE ?></td>
							<td nowrap>
							<input dir="ltr" type="text" name="Item_PropertyTitle" id="Item_PropertyTitle" maxlength="245" size="40" required>
							</td>
						</tr>
						<tr>
							<td width="1%" nowrap><? echo C_LABEL ?></td>
							<td nowrap>
							<input type="text" dir="<?php echo (UI_LANGUAGE=="FA")?"rtl":"ltr" ?>" name="Item_label" id="Item_label" maxlength="245" size="40" required>
							</td>
						</tr>
						<tr>
							<td width="1%" nowrap><? echo C_TYPE ?></td>
							<td nowrap>
								<select dir="ltr" name="Item_PropertyType" id="Item_PropertyType" >
									<option value=0>-</option>
									<option value='DATATYPE'>DATATYPE</option>
									<option value='OBJECT' selected>OBJECT</option>
									<option value='ANNOTATION'>ANNOTATION</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="1%" nowrap>
								<a href='#' onclick='javascript: document.getElementById("DomainTR").style.display="";'><? echo C_Area ?></a>
							</td>
							<td nowrap>
								<? 
								  if($PropType=="OBJECT")
									ShowClassListLabels($domain, $_REQUEST["OntologyID"],"DOMAIN", true);
								  if($PropType=="DATATYPE")
									ShowClassListLabels($domain, $_REQUEST["OntologyID"],"DOMAIN", false);
								?>
							</td>
						</tr>
						<tr style='display: ' id="DomainTR" name="DomainTR">
							<td width="1%" nowrap></td>
							<td nowrap>
							<textarea name="Item_domain" id="Item_domain" rows="8" cols="100" dir="ltr" ><? echo $domain; ?></textarea>
							<a target="_blank" href='ShowOntologyClassTree.php?InputName=Item_domain&OntologyID=<? echo $_REQUEST["OntologyID"] ?>'><? echo C_SELECT ?></a>
							</td>
						</tr>

						<tr>
							<td width="1%" nowrap>
								<a href='#' onclick='javascript: document.getElementById("RangeTR").style.display="";'><? echo C_RANGE ?></a>
							</td>
							<td nowrap>
								<? 
								  if($PropType=="OBJECT")
								  {
									ShowClassListLabels($range, $_REQUEST["OntologyID"],"RANGE", true);
								  }
								  else
									echo $range;
								?>
							</td>
						</tr>

						<tr style='display: ' id="RangeTR" name="RangeTR">
							<td width="1%" nowrap></td>
							<td nowrap>
								<textarea name="Item_range" id="Item_range" rows="8" cols="100" dir=ltr ><? echo $range; ?></textarea>
								<a target="_blank" href='ShowOntologyClassTree.php?InputName=Item_range&OntologyID=<? echo $_REQUEST["OntologyID"] ?>'><? echo C_SELECT ?></a>
							</td>
						</tr>
						<tr>
							<td width="1%" nowrap><? echo C_REVERSE ?></td>
							<td nowrap>
								<input type="text" dir=ltr name="Item_inverseOf" id="Item_inverseOf" maxlength="245" size="40" >
								<a target="_blank" href='ShowOntologyClassTree.php?InputName=Item_inverseOf&OntologyID=<? echo $_REQUEST["OntologyID"] ?>'><? echo C_SELECT ?></a>
							</td>
						</tr>
						<tr>
							<td width="1%" nowrap>Functional</td>
							<td nowrap>
								<select name="Item_IsFunctional" id="Item_IsFunctional" >
									<option value='NO'><? echo C_NO ?></option>
									<option value='YES'><? echo C_YES ?></option>
								</select>
							</td>
						</tr>
						<? if(isset($_REQUEST["UpdateID"])) { 
						$RelationList = GetAllPossibleLinks($_REQUEST["UpdateID"], $_REQUEST["OntologyID"]); 
						echo "<tr>";
						echo "<td colspan=2>";
						echo "<b>".C_SPECIFY_VALID_RELATIONSHIPS.": <br></b>";
						echo $RelationList;
						echo "</td>";
						echo "</tr>";
						?>
						<tr>
							<td><? echo C_VALUES ?></td>
							<td>
								<a href='ManageOntologyPropertyPermittedValues.php?OntologyID=<? echo $_REQUEST["OntologyID"] ?>&OntologyPropertyID=<? echo $_REQUEST["UpdateID"] ?>'><? echo C_EDIT ?></a>
								<br>
								<?
								$query = "select PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID=?";
								$mysql->Prepare($query);
								$res = $mysql->ExecuteStatement(array($_REQUEST["UpdateID"]));
								while($rec = $res->fetch())
									echo $rec["PermittedValue"]."<br>";

								/*
								$query = "select distinct DataValue from projectmanagement.TermOntologyElementMapping where EntityType='DATA_RANGE' and OntologyEntityID=?";
								$mysql->Prepare($query);
								$res = $mysql->ExecuteStatement(array($_REQUEST["UpdateID"]));
								while($rec = $res->fetch())
									echo $rec["DataValue"]."<br>";
								*/	
								?>
							</td>
						</tr>

						<tr>
							<td nowrap><? echo C_Merge_result ?>: </td>
							<td>
								<table dir=ltr>
									<?
									  $mysql->Prepare("select distinct OntologyProperties.OntologyID, EntityID, OntologyTitle, PropertyTitle from projectmanagement.OntologyMergeEntities 
													LEFT JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyMergeEntities.EntityID)
													LEFT JOIN projectmanagement.ontologies using (OntologyID)
													  where TargetEntityType='PROPERTY' and TargetEntityID=?");
									  $res = $mysql->ExecuteStatement(array($_REQUEST["UpdateID"]));
									  while($rec = $res->fetch())
									  {
										echo "<tr><td>".$rec["OntologyTitle"]."</td>";
										echo "<td><a target=_blank href='ManageOntologyProperties.php?UpdateID=".$rec["EntityID"]."&OntologyID=".$rec["OntologyID"]."'>".$rec["PropertyTitle"]."</a>";
										echo "</td></tr>";
									  }
									?>
								</table>
							</td>
						</tr>

						<? } ?>
					</table>
				</td>
				</tr>
				<tfoot>
					<tr class="table-info">
						<td align="center">
							<input type="submit" class="btn btn-success" value="<? echo C_SAVE; ?>">
							<input type="button" class="btn btn-info" onclick="javascript: document.location='ManageOntologyProperties.php?OntologyID=<?php echo $_REQUEST["OntologyID"]; ?>'" value="<? echo C_NEW; ?>">
							<input type="button" class="btn btn-warning" onclick="javascript: window.close();" value="<? echo C_CLOSE ?>">
							<? if(isset($_REQUEST["UpdateID"])) { ?>
								<input type="button" class="btn btn-danger" onclick="javascript: if(confirm('<? echo C_ARE_YOU_SURE_TO_REMOVE; ?>?')) document.location='ManageOntologyProperties.php?RemovePropertyID=<?php echo $_REQUEST["UpdateID"]; ?>';" value="<? echo C_REMOVE; ?>">
							<? } ?>
						</td>
					</tr>
				</tfoot>
			</table>
		</form>
		<script>
			<? echo $LoadDataJavascriptCode; ?>
		</script>
		<?php 
		if(isset($_REQUEST["DoNotShowList"]))
		  die();
		$res = manage_OntologyProperties::GetList($_REQUEST["OntologyID"]); 
		$SomeItemsRemoved = false;
		for($k=0; $k<count($res); $k++)
		{
			if(isset($_REQUEST["ch_".$res[$k]->OntologyPropertyID])) 
			{
				manage_OntologyProperties::Remove($res[$k]->OntologyPropertyID); 
				$SomeItemsRemoved = true;
			}
			if(isset($_REQUEST["label_".$res[$k]->OntologyPropertyID]) && $_REQUEST["label_".$res[$k]->OntologyPropertyID]!="") 
			{
				manage_OntologyPropertyLabels::Add($res[$k]->OntologyPropertyID, $_REQUEST["label_".$res[$k]->OntologyPropertyID]);
				$SomeItemsRemoved = true;
			}
			if(isset($_REQUEST["Domain_".$res[$k]->OntologyPropertyID])) 
			{
				$query = "update projectmanagement.OntologyProperties set domain='".$_REQUEST["Domain_".$res[$k]->OntologyPropertyID]."' where OntologyPropertyID=".$res[$k]->OntologyPropertyID;
				$mysql->Execute($query);
				$query = "update projectmanagement.OntologyProperties set `range`='".$_REQUEST["Range_".$res[$k]->OntologyPropertyID]."' where OntologyPropertyID=".$res[$k]->OntologyPropertyID;
				$mysql->Execute($query);
				$SomeItemsRemoved = true;
			}
			
		}
		if($SomeItemsRemoved)
			$res = manage_OntologyProperties::GetList($_REQUEST["OntologyID"]); 
		?>
		<form id="ListForm" name="ListForm" method="post"> 
			<input type="hidden" id="Item_OntologyID" name="Item_OntologyID" value="<? echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>">
			<br>
			<table class="table table-bordered table-sm table-striped">
				<tr><td colspan="10">
					<? echo C_ONTOLOGY_FEATURES; ?>
				</td></tr>
				<thead class="table-info">
					<tr>
						<td width="1%"> </td>
						<td width="1%"><? echo C_ROW; ?></td>
						<td width="2%"><? echo C_EDIT; ?></td>
						<td><? echo C_TITLE; ?></td>
						<td><? echo C_DOMAIN_AND_RANGE; ?></td>
						<td nowrap><? echo C_LABELS ?></td>
					</tr>
				</thead>
				<?
				for($k=0; $k<count($res); $k++)
				{
					$LabelsList = "";
					$list = manage_OntologyPropertyLabels::GetList($res[$k]->OntologyPropertyID);
					for($m=0; $m<count($list); $m++)
					{
					  if($m>0)
					$LabelsList .= ", ";
					  $LabelsList .= $list[$m]->label;
					}
					if($res[$k]->domain=="" && $res[$k]->range=="")
						echo "<tr bgcolor=\"#ff4d4d\">";
					echo "<tr><td>";
					echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyPropertyID."\">";
					echo "</td>";
					echo "<td>".($k+1)."</td>";
					echo "	<td><a target=_blank href=\"ManageOntologyProperties.php?UpdateID=".$res[$k]->OntologyPropertyID."&OntologyID=".$_REQUEST["OntologyID"]."&DoNotShowList=1\"><i class='fas fa-edit' title='".C_EDIT."'></i></a></td>";
					echo "	<td dir=ltr>";
					if($res[$k]->IsFunctional_Desc=="بلی")
					  echo "<i class='fas fa-calendar-times' title='".C_PROPERTY_IS_FUNCTIONAL."'></i>";
					if($res[$k]->PropertyType_Desc=="DATATYPE") echo "<i class='fas fa-link' title='".C_THING_FEATURES."'></i>";
					else echo "<i class='fas fa-tasks' title='".C_DATA_FEATURES."'></i>";

					echo htmlentities($res[$k]->PropertyTitle, ENT_QUOTES, 'UTF-8');
					echo "<br>";
					echo $LabelsList;
					echo "</td>";
					//echo "	<td>".$res[$k]->PropertyType_Desc."</td>";
					//echo "	<td>".$res[$k]->IsFunctional_Desc."</td>";
					echo "	<td dir=ltr>
					حوزه: <br>
					<textarea id='Domain_".$res[$k]->OntologyPropertyID."' name='Domain_".$res[$k]->OntologyPropertyID."' required>".htmlentities($res[$k]->domain, ENT_QUOTES, 'UTF-8')."</textarea>";
					$l = explode(", ", $res[$k]->domain);
					for($i=0; $i<count($l); $i++)
					{
					  $ro = $mysql->Execute("select * from projectmanagement.OntologyClasses where ClassTitle='".$l[$i]."' and OntologyID=".$_REQUEST["OntologyID"]);
					  if(!($ro->fetch()))
						echo "<br><font color=red>".$l[$i]."</font>";
					}
					echo "	<br>برد:<br>
					<textarea id='Range_".$res[$k]->OntologyPropertyID."' name='Range_".$res[$k]->OntologyPropertyID."' required>".htmlentities($res[$k]->range, ENT_QUOTES, 'UTF-8')."</textarea>";
					$l = explode(", ", $res[$k]->range);
					for($i=0; $i<count($l); $i++)
					{
					  $ro = $mysql->Execute("select * from projectmanagement.OntologyClasses where ClassTitle='".$l[$i]."' and OntologyID=".$_REQUEST["OntologyID"]);
					  if(!($ro->fetch()))
						echo "<br><font color=red>".$l[$i]."</font>";
					}
					if($res[$k]->PropertyType_Desc=="OBJECT" && NumberOfValidRelation($res[$k]->OntologyPropertyID)==0)
						echo "<br><b><font color=red>".C_PERMITTED_DOMAIN_AND_RANGE_RELATIONSHIPS_ARE_NOT_DEFINED."</font></b>";
					echo "</td>";
					//echo "	<td dir=ltr>".htmlentities($res[$k]->inverseOf, ENT_QUOTES, 'UTF-8')."</td>";
					echo "<td nowrap>";
					$SuggestedLabel = "";
					if($LabelsList=="")
					{
					  $SuggestedLabel = manage_OntologyProperties::GetSuggestedLabel($res[$k]->PropertyTitle);
					}
					
					echo "<input type=text name='label_".$res[$k]->OntologyPropertyID."' id='label_".$res[$k]->OntologyPropertyID."' value='".$SuggestedLabel."' required>";
					echo "<br><a  target=\"_blank\" href='ManageOntologyPropertyLabels.php?OntologyPropertyID=".$res[$k]->OntologyPropertyID ."'>[".C_EDIT."]</a></td>";
					echo "</tr>";
				}
				?>
				<tfoot>
					<tr class="table-info">
						<td colspan="10" align="center">
							<input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<? echo C_REMOVE; ?>">
							<input type="submit" class="btn btn-success"  value="<? echo C_SAVE; ?>">
							<input type="button" class="btn btn-info" onclick="javascript: window.open('ShowSimilarClassRelations.php?OntologyID=<? echo $_REQUEST["OntologyID"]; ?>');" value="<? echo C_SIMILAR_RELATIONSHIPS; ?>">
							<input type="button" class="btn btn-info" onclick="javascript: window.open('ShowSimilarClassProperties.php?OntologyID=<? echo $_REQUEST["OntologyID"]; ?>');" value="<? echo C_DUPLICATE_PROPERTIES; ?>">
						</td>
					</tr>
				</tfoot>
			</table>
		</form>
		<form target="_blank" method="post" action="NewOntologyProperties.php" id="NewRecordForm" name="NewRecordForm">
			<input type="hidden" id="OntologyID" name="OntologyID" value="<? echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>">
		</form>
	</div>
</div>
<script>
function ConfirmDelete()
{
	if(confirm('<? echo C_ARE_YOU_SURE ?>')) document.ListForm.submit();
}
</script>
</html>
