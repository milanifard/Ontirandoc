<?
class MergeOntology
{
	// مشخص می کند آیا دو کلاس به صورت صریح با هم معادل هستند یا خیر
	static function IsTwoClassDirectlyEqual($OntologyClassID1, $OntologyClassID2)
	{
		if($OntologyClassID1==$OntologyClassID2)
			return true;
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClassMapping
where (MappedOntologyEntityType='CLASS' and MappedOntologyEntityID=? and OntologyClassID=?)
	or (MappedOntologyEntityType='CLASS' and MappedOntologyEntityID=? and OntologyClassID=?)";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID1, $OntologyClassID2, $OntologyClassID2, $OntologyClassID1));
		if($rec = $res->fetch())
		{
			return true;
		}	
		return false;
	}

	// مشخص می کند آیا دو کلاس به صورت صریح با هم معادل هستند یا خیر
	static function IsTwoPropertyDirectlyEqual($OntologyPropertyID1, $OntologyPropertyID2)
	{
		if($OntologyPropertyID1==$OntologyPropertyID2)
			return true;
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyPropertyMapping
where (MappedOntologyEntityType='PROP' and MappedOntologyEntityID=? and OntologyPropertyID=?)
	or (MappedOntologyEntityType='PROP' and MappedOntologyEntityID=? and OntologyPropertyID=?)";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID1, $OntologyPropertyID2, $OntologyPropertyID2, $OntologyPropertyID1));
		if($rec = $res->fetch())
		{
			return true;
		}	
		return false;
	}
	
	// مشخص می کند آیا دو کلاس در جهت گفته شده در جدولنگاشت هستند یا خیر
	static function DirectionalClassMappingExist($OntologyClassID1, $OntologyClassID2)
	{
		if($OntologyClassID1==$OntologyClassID2)
			return true;
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClassMapping
where MappedOntologyEntityType='CLASS' and MappedOntologyEntityID=? and OntologyClassID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID2, $OntologyClassID1));
		if($rec = $res->fetch())
		{
			return true;
		}	
		return false;
	}

	// مشخص می کند آیا دو خصوصیت در جهت گفته شده در جدولنگاشت هستند یا خیر
	static function DirectionalPropertyMappingExist($OntologyPropertyID1, $OntologyPropertyID2)
	{
		if($OntologyPropertyID1==$OntologyPropertyID2)
			return true;
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyPropertyMapping
where MappedOntologyEntityType='PROP' and MappedOntologyEntityID=? and OntologyPropertyID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID2, $OntologyPropertyID1));
		if($rec = $res->fetch())
		{
			return true;
		}	
		return false;
	}

	// مشخص می کند آیا نگاشت خصوصیت به کلاس وجود دارد یا خیر
	static function DirectionalPROP_CLASSMappingExist($OntologyPropertyID, $OntologyClassID)
	{
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyPropertyMapping
where MappedOntologyEntityType='CLASS' and MappedOntologyEntityID=? and OntologyPropertyID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID, $OntologyPropertyID));
		if($rec = $res->fetch())
		{
			return true;
		}	
		return false;
	}
	
	// مشخص می کند آیا نگاشت کلاس به خصوصیت وجود دارد یا خیر
	static function DirectionalCLASS_PROPMappingExist($OntologyClassID, $OntologyPropertyID)
	{
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClassMapping
where MappedOntologyEntityType='PROP' and MappedOntologyEntityID=? and OntologyClassID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID, $OntologyClassID));
		if($rec = $res->fetch())
		{
			return true;
		}	
		return false;
	}
	
	
	// کلاسهایی که از وایطه یک کلاس مشخص به کلاس اولیه نگاشت می شوند - معادلند - به دست می آورد
	static function SaveIndirectEqualClassesThroughAClass($SourceOntologyID, $SourceOntologyClassID, $MappedClassID)
	{
		$SavedCount = 0;
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClassMapping
where MappedOntologyEntityType='CLASS' and MappedOntologyEntityID>0 and OntologyClassID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($MappedClassID));
		while($rec = $res->fetch())
		{
			$MappedClassID = $rec["MappedOntologyEntityID"];
			$MappedOntologyID = $rec["MappedOntologyID"];
			if(!(MergeOntology::IsTwoClassDirectlyEqual($SourceOntologyClassID, $MappedClassID)))
			{
				echo $SourceOntologyClassID." -> ".$rec["MappedOntologyEntityID"]."<br>";
				$query = "insert into projectmanagement.OntologyClassMapping (OntologyID, OntologyClassID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, 'CLASS')";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($SourceOntologyID, $SourceOntologyClassID, $MappedOntologyID, $MappedClassID));
					$SavedCount++;
			}
		}
		
		// کلاسهایی که به یک کلاس هم نگاشت شده اند با هم معادلند
		$query = "select * from projectmanagement.OntologyClassMapping
where MappedOntologyEntityType='CLASS' and MappedOntologyEntityID>0 and MappedOntologyEntityID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($MappedClassID));
		while($rec = $res->fetch())
		{
			$MappedClassID = $rec["OntologyClassID"];
			$MappedOntologyID = $rec["OntologyClassID"];
			if(!(MergeOntology::IsTwoClassDirectlyEqual($SourceOntologyClassID, $MappedClassID)))
			{
				echo $SourceOntologyClassID." -> ".$rec["MappedOntologyEntityID"]."<br>";
				$query = "insert into projectmanagement.OntologyClassMapping (OntologyID, OntologyClassID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, 'CLASS')";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($SourceOntologyID, $SourceOntologyClassID, $MappedOntologyID, $MappedClassID));
				$SavedCount++;
			}
		}
		return $SavedCount;
	}


	// کلاسهایی که از وایطه یک کلاس مشخص به کلاس اولیه نگاشت می شوند - معادلند - به دست می آورد
	static function SaveIndirectEqualPropertiesThroughAProp($SourceOntologyID, $SourceOntologyPropertyID, $MappedPropertyID)
	{
		$SavedCount = 0;
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyPropertyMapping
where MappedOntologyEntityType='PROP' and MappedOntologyEntityID>0 and OntologyPropertyID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($MappedPropertyID));
		while($rec = $res->fetch())
		{
			$MappedPropertyID = $rec["MappedOntologyEntityID"];
			$MappedOntologyID = $rec["MappedOntologyID"];
			if(!(MergeOntology::IsTwoPropertyDirectlyEqual($SourceOntologyPropertyID, $MappedPropertyID)))
			{
				echo $SourceOntologyPropertyID." -> ".$rec["MappedOntologyEntityID"]."<br>";
				$query = "insert into projectmanagement.OntologyPropertyMapping (OntologyID, OntologyPropertyID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, 'PROP')";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($SourceOntologyID, $SourceOntologyPropertyID, $MappedOntologyID, $MappedPropertyID));
					$SavedCount++;
			}
		}
		
		// خصوصیاتی که به یک خصوصیت هم نگاشت شده اند با هم معادلند
		$query = "select * from projectmanagement.OntologyPropertyMapping
where MappedOntologyEntityType='PROP' and MappedOntologyEntityID>0 and MappedOntologyEntityID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($MappedPropertyID));
		while($rec = $res->fetch())
		{
			$MappedPropertyID = $rec["OntologyPropertyID"];
			$MappedOntologyID = $rec["OntologyClassID"];
			if(!(MergeOntology::IsTwoPropertyDirectlyEqual($SourceOntologyPropertyID, $MappedPropertyID)))
			{
				echo $SourceOntologyPropertyID." -> ".$rec["MappedOntologyEntityID"]."<br>";
				$query = "insert into projectmanagement.OntologyPropertyMapping (OntologyID, OntologyPropertyID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, 'PROP')";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($SourceOntologyID, $SourceOntologyPropertyID, $MappedOntologyID, $MappedPropertyID));
				$SavedCount++;
			}
		}
		return $SavedCount;
	}


	// کلاسهایی که از طریق واسطه با یک کلاس خاص معادل می شوند (خاصیت تعدی) پیدا می کند
	static function FindAllIndirectEqualClassesForAClass($OntologyID, $OntologyClassID)
	{
		$SavedCount = 0;
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClassMapping
where MappedOntologyEntityType='CLASS' and MappedOntologyEntityID>0 and OntologyClassID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		//echo "<br>".$OntologyClassID."<br>";
		while($rec = $res->fetch())
		{
			$MappedClassID = $rec["MappedOntologyEntityID"];
			$SavedCount += MergeOntology::SaveIndirectEqualClassesThroughAClass($OntologyID, $OntologyClassID, $MappedClassID);
		}
		if($SavedCount>0)
			return true;
		return false;
	}

	// خصوصیاتی که از طریق واسطه با یک خصوصیت خاص معادل می شوند (خاصیت تعدی) پیدا می کند
	static function FindAllIndirectEqualPropertiesForAProp($OntologyID, $OntologyPropertyID)
	{
		$SavedCount = 0;
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyPropertyMapping
where MappedOntologyEntityType='PROP' and MappedOntologyEntityID>0 and OntologyPropertyID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));
		//echo "<br>".$OntologyClassID."<br>";
		while($rec = $res->fetch())
		{
			$MappedPropertyID = $rec["MappedOntologyEntityID"];
			$SavedCount += MergeOntology::SaveIndirectEqualPropertiesThroughAProp($OntologyID, $OntologyPropertyID, $MappedPropertyID);
		}
		if($SavedCount>0)
			return true;
		return false;
	}
	
	// کل کلاسهایی که بر اساس خاصیت تعدی با هم معادل می شوند یافته و ذخیره می کند (در یک سطح)
	// این متد باید آنقدر فراخوانی شود تا دیگر کلاسی پیدا نشود
	static function FindAllIndirectEqualClasses()
	{
		$mysql = pdodb::getInstance();
		$query = "select distinct OntologyID, OntologyClassID from projectmanagement.OntologyClassMapping
where MappedOntologyEntityType='CLASS' and MappedOntologyEntityID>0";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i= 0;
		while($rec = $res->fetch())
		{
			$OntologyClassID = $rec["OntologyClassID"];
			$OntologyID = $rec["OntologyID"];
			if(MergeOntology::FindAllIndirectEqualClassesForAClass($OntologyID, $OntologyClassID))
				$i++;
		}
	
		if($i>0)
			return true;
		return false;
	}

	// کل خصوصیتهایی که بر اساس خاصیت تعدی با هم معادل می شوند یافته و ذخیره می کند (در یک سطح)
	// این متد باید آنقدر فراخوانی شود تا دیگر خصوصیتی پیدا نشود
	static function FindAllIndirectEqualProps()
	{
		$mysql = pdodb::getInstance();
		 $query = "select distinct OntologyID, OntologyPropertyID from projectmanagement.OntologyPropertyMapping
where MappedOntologyEntityType='PROP' and MappedOntologyEntityID>0";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i= 0;
		while($rec = $res->fetch())
		{
			$OntologyPropertyID = $rec["OntologyPropertyID"];
			$OntologyID = $rec["OntologyID"];
			if(MergeOntology::FindAllIndirectEqualPropertiesForAProp($OntologyID, $OntologyPropertyID))
				$i++;
		}
	
		if($i>0)
			return true;
		return false;
	}
	
	
	// رابطه تقارنی را اعمال می کند
	static function ApplySymmetryRuleOnMappings()
	{
		$SavedCount = 0;
		$mysql = pdodb::getInstance();
		$query = "select MappedOntologyID, MappedOntologyEntityID, OntologyID, OntologyClassID from projectmanagement.OntologyClassMapping
where MappedOntologyEntityID>0 and MappedOntologyEntityType='CLASS'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i= 0;
		while($rec = $res->fetch())
		{
			$OntologyClassID = $rec["OntologyClassID"];
			$OntologyID = $rec["OntologyID"];
			$MappedOntologyID = $rec["MappedOntologyID"];
			$MappedOntologyEntityID = $rec["MappedOntologyEntityID"];
			// اگر کلاسی معادل کلاس دیگری تعریف شده بود باید رابطه بالعکس هم ذخیره شود
			if(!(MergeOntology::DirectionalClassMappingExist($MappedOntologyEntityID, $OntologyClassID)))
			{
				echo $MappedOntologyEntityID." -> ".$OntologyClassID."<br>";
				$query = "insert into projectmanagement.OntologyClassMapping (OntologyID, OntologyClassID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, 'CLASS')";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($MappedOntologyID, $MappedOntologyEntityID, $OntologyID, $OntologyClassID));
				$SavedCount++;
			}
		}
	
		$query = "select MappedOntologyID, MappedOntologyEntityID, OntologyID, OntologyClassID from projectmanagement.OntologyClassMapping
where MappedOntologyEntityID>0 and MappedOntologyEntityType='PROP'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i= 0;
		while($rec = $res->fetch())
		{
			$OntologyClassID = $rec["OntologyClassID"];
			$OntologyID = $rec["OntologyID"];
			$MappedOntologyID = $rec["MappedOntologyID"];
			$MappedOntologyEntityID = $rec["MappedOntologyEntityID"];
			// اگر کلاسی معادل کلاس دیگری تعریف شده بود باید رابطه بالعکس هم ذخیره شود
			if(!(MergeOntology::DirectionalPROP_CLASSMappingExist($MappedOntologyEntityID, $OntologyClassID)))
			{
				echo $MappedOntologyEntityID." -> ".$OntologyClassID."<br>";
				$query = "insert into projectmanagement.OntologyPropertyMapping (OntologyID, OntologyPropertyID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, 'CLASS')";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($MappedOntologyID, $MappedOntologyEntityID, $OntologyID, $OntologyClassID));
				$SavedCount++;
			}
		}	
		
		
		$query = "select MappedOntologyID, MappedOntologyEntityID, OntologyID, OntologyPropertyID from projectmanagement.OntologyPropertyMapping
where MappedOntologyEntityID>0 and MappedOntologyEntityType='CLASS'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i= 0;
		while($rec = $res->fetch())
		{
			$OntologyPropertyID = $rec["OntologyPropertyID"];
			$OntologyID = $rec["OntologyID"];
			$MappedOntologyID = $rec["MappedOntologyID"];
			$MappedOntologyEntityID = $rec["MappedOntologyEntityID"];
			// اگر کلاسی معادل کلاس دیگری تعریف شده بود باید رابطه بالعکس هم ذخیره شود
			if(!(MergeOntology::DirectionalCLASS_PROPMappingExist($MappedOntologyEntityID, $OntologyPropertyID)))
			{
				echo $MappedOntologyEntityID." --> ".$OntologyPropertyID."<br>";
				$query = "insert into projectmanagement.OntologyClassMapping (OntologyID, OntologyClassID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, 'PROP')";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($MappedOntologyID, $MappedOntologyEntityID, $OntologyID, $OntologyPropertyID));
				$SavedCount++;
			}
		}

		$query = "select MappedOntologyID, MappedOntologyEntityID, OntologyID, OntologyPropertyID from projectmanagement.OntologyPropertyMapping
where MappedOntologyEntityID>0 and MappedOntologyEntityType='PROP'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array());
		$i= 0;
		while($rec = $res->fetch())
		{
			$OntologyPropertyID = $rec["OntologyPropertyID"];
			$OntologyID = $rec["OntologyID"];
			$MappedOntologyID = $rec["MappedOntologyID"];
			$MappedOntologyEntityID = $rec["MappedOntologyEntityID"];
			// اگر کلاسی معادل کلاس دیگری تعریف شده بود باید رابطه بالعکس هم ذخیره شود
			if(!(MergeOntology::DirectionalPropertyMappingExist($MappedOntologyEntityID, $OntologyPropertyID)))
			{
				echo $MappedOntologyID.":".$MappedOntologyEntityID.":".$OntologyID.":". $OntologyPropertyID."<br>";
				$query = "insert into projectmanagement.OntologyPropertyMapping (OntologyID, OntologyPropertyID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, 'PROP')";
				$mysql->Prepare($query);
				$mysql->ExecuteStatement(array($MappedOntologyID, $MappedOntologyEntityID, $OntologyID, $OntologyPropertyID));
				$SavedCount++;
			}
		}
		
		if($SavedCount>0)
			return true;
		return false;
	}

	static function ShowAllMappedEntityForClass($OntologyMergeProjectID, $OntologyClassID)
	{
		$mysql = pdodb::getInstance();
		// تمام موجودیتهایی که به کلاس ذکر شده نگاشت شده اند
		$query = "SELECT MappedOntologyEntityID, MappedOntologyEntityType, 
o1.OntologyTitle as OTitle1,
o2.OntologyTitle as OTitle2,
o3.OntologyTitle as OTitle3,
o1.OntologyID as OID1,
ClassTitle, 
OntologyClassLabels.label as ClassLabel, 
OntologyPropertyLabels.label as PropertyLabel, 
OntologyProperties.PropertyTitle, 
op.PropertyTitle as RelatedPropertyTitle, 
opl.label as RelatedPropertyLabel,
PermittedValue 
FROM projectmanagement.OntologyClassMapping 
LEFT JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=MappedOntologyEntityID)
LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
LEFT JOIN projectmanagement.OntologyProperties on (OntologyPropertyID=MappedOntologyEntityID)
LEFT JOIN projectmanagement.OntologyPropertyLabels on (OntologyProperties.OntologyPropertyID=OntologyPropertyLabels.OntologyPropertyID)
LEFT JOIN projectmanagement.OntologyPropertyPermittedValues on (OntologyPropertyPermittedValueID=MappedOntologyEntityID)
LEFT JOIN projectmanagement.ontologies o1 on (o1.OntologyID=OntologyClasses.OntologyID)
LEFT JOIN projectmanagement.ontologies o2 on (o2.OntologyID=OntologyProperties.OntologyID)
LEFT JOIN projectmanagement.OntologyProperties op on (op.OntologyPropertyID=OntologyPropertyPermittedValues.OntologyPropertyID)
LEFT JOIN projectmanagement.OntologyPropertyLabels opl on (opl.OntologyPropertyID=op.OntologyPropertyID)
LEFT JOIN projectmanagement.ontologies o3 on (o3.OntologyID=op.OntologyID) where OntologyClassMapping.OntologyClassID=? and MappedOntologyEntityID>0";

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		echo "<br><table width=90% align=center border=1 cellspacing=0 cellpadding=5>";
		$i=0;
		while($rec = $res->fetch())
		{
			$i++;
			echo "<tr ";
			if($i%2==0)
				echo "class='EvenRow'";
			else
				echo "class='OddRow'";			
			echo ">";
			$EntityType = $rec["MappedOntologyEntityType"];
			if($EntityType=="CLASS")
			{	echo "<td>".$rec["OTitle1"]."</td>";
				echo "<td>";
				echo " کلاس ";
				echo "<b>". $rec["ClassLabel"]."</b> ";
				echo " (";
				echo "<a href='ManageOntologyClasses.php?UpdateID=".$rec["MappedOntologyEntityID"]."&OntologyID=".$rec["OID1"]."&OnlyEditForm=1' target=_blank>".$rec["ClassTitle"]."</a>)";
				echo "</td>";
			}
			else if($EntityType=="PROP")
			{
				echo "<td>".$rec["OTitle2"]."</td>";
				echo "<td>";
				echo " خصوصیت ";
				echo "<b>".$rec["PropertyLabel"]."</b> ";
				echo " (".$rec["PropertyTitle"].")";
				echo "</td>";
			}
			else if($EntityType=="DATA_PROP")
			{
				echo "<td>".$rec["OTitle3"]."</td>";
				echo "<td>";
				echo "<b>".$rec["PermittedValue"]." </b>";
				echo " (داده مجاز از خصوصیت <b>";
				echo $rec["RelatedPropertyLabel"];
				echo "</b>)";
				echo "</td>";
			}
			echo "</tr>";
		}		
		$query = "SELECT 
OntologyTitle,
PropertyTitle, 
label 
FROM projectmanagement.OntologyPropertyMapping 
LEFT JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyPropertyMapping.OntologyPropertyID)
LEFT JOIN projectmanagement.OntologyPropertyLabels on (OntologyProperties.OntologyPropertyID=OntologyPropertyLabels.OntologyPropertyID)
LEFT JOIN projectmanagement.ontologies on (ontologies.OntologyID=OntologyProperties.OntologyID)
where MappedOntologyEntityID=? and MappedOntologyEntityType='CLASS'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID));	
		while($rec = $res->fetch())
		{
			$i++;
			echo "<tr ";
			if($i%2==0)
				echo "class='EvenRow'";
			else
				echo "class='OddRow'";			
			echo ">";
			echo "<td>".$rec["OntologyTitle"]."</td>";
			echo "<td>";
			echo " کلاس ";
			echo "<b>".$rec["label"]."</b> ";
			echo " (".$rec["PropertyTitle"].")";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	static function ShowClassLabels($OntologyID, $ClassTitleList)
	{
		$mysql = pdodb::getInstance();
		$classes = explode(", ", $ClassTitleList);
		for($i=0; $i<count($classes); $i++)
		{
			$query = "select * from projectmanagement.OntologyClasses
			JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
			where OntologyID=? and ClassTitle=?";
			//echo "<br>".$OntologyID.",".$classes[$i]."<br>";
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array($OntologyID, $classes[$i]));
			$rec = $res->fetch();
			if($i>0)
				echo " - ";
			echo $rec["label"];
			$OntologyClassID = $rec["OntologyClassID"];
			$query = "select ActionType, label from projectmanagement.OntologyMergeEntities
JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=TargetEntityID)
JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
where EntityID=? and EntityType='CLASS' and TargetEntityType='CLASS'";
			$mysql->Prepare($query);
			$res = $mysql->ExecuteStatement(array($OntologyClassID));
			if($rec = $res->fetch())
			{
				echo " (<font color=green>";
				echo $rec["label"]."</font>)";
			}
			else
			{
				echo " (<font color=red>X</font>)";
			}

		}
	}

	static function ShowAllMappedEntityForProperty($OntologyMergeProjectID, $OntologyPropertyID)
	{
		$mysql = pdodb::getInstance();
		// تمام موجودیتهایی که به خصوصیت ذکر شده نگاشت شده اند
		$query = "SELECT MappedOntologyEntityID, MappedOntologyEntityType, 
o1.OntologyID as OID1,
o2.OntologyID as OID2,
o3.OntologyID as OID3,
o1.OntologyTitle as OTitle1,
o2.OntologyTitle as OTitle2,
o3.OntologyTitle as OTitle3,
OntologyProperties.PropertyTitle, 
OntologyClassLabels.label as ClassLabel, 
OntologyPropertyLabels.label as PropertyLabel, 
OntologyProperties.PropertyTitle, 
op.PropertyTitle as RelatedPropertyTitle, 
opl.label as RelatedPropertyLabel,
PermittedValue,
ClassTitle,
OntologyProperties.domain, OntologyProperties.`range`,
OntologyProperties.PropertyType
FROM projectmanagement.OntologyPropertyMapping 
LEFT JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=MappedOntologyEntityID)
LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
LEFT JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=MappedOntologyEntityID)
LEFT JOIN projectmanagement.OntologyPropertyLabels on (OntologyProperties.OntologyPropertyID=OntologyPropertyLabels.OntologyPropertyID)
LEFT JOIN projectmanagement.OntologyPropertyPermittedValues on (OntologyPropertyPermittedValueID=MappedOntologyEntityID)
LEFT JOIN projectmanagement.ontologies o1 on (o1.OntologyID=OntologyClasses.OntologyID)
LEFT JOIN projectmanagement.ontologies o2 on (o2.OntologyID=OntologyProperties.OntologyID)
LEFT JOIN projectmanagement.OntologyProperties op on (op.OntologyPropertyID=OntologyPropertyPermittedValues.OntologyPropertyID)
LEFT JOIN projectmanagement.OntologyPropertyLabels opl on (opl.OntologyPropertyID=op.OntologyPropertyID)
LEFT JOIN projectmanagement.ontologies o3 on (o3.OntologyID=op.OntologyID) where OntologyPropertyMapping.OntologyPropertyID=? and MappedOntologyEntityID>0";

		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));
		echo "<br><table width=90% align=center border=1 cellspacing=0 cellpadding=5>";
		$i=0;
		while($rec = $res->fetch())
		{
			$i++;
			echo "<tr ";
			if($i%2==0)
				echo "class='EvenRow'";
			else
				echo "class='OddRow'";			
			echo ">";
			$EntityType = $rec["MappedOntologyEntityType"];
			if($EntityType=="CLASS")
			{	echo "<td>".$rec["OTitle1"]."</td>";
				echo "<td>";
				echo " کلاس ";
				echo "<b>". $rec["ClassLabel"]."</b> ";
				echo " (".$rec["ClassTitle"].")";
				echo "</td>";
			}
			else if($EntityType=="PROP")
			{
				echo "<td>".$rec["OTitle2"]."</td>";
				echo "<td>";
				if($rec["PropertyType"]=="DATATYPE")
					echo " خصوصیت ";
				else
					echo " رابطه ";
				echo "<b>".$rec["PropertyLabel"]."</b> ";
				echo " (".$rec["PropertyTitle"].")";
				echo "<br>حوزه: ";
				MergeOntology::ShowClassLabels($rec["OID2"], $rec["domain"]);
				if($rec["PropertyType"]=="OBJECT")
				{
					echo "<br>برد: ";
					MergeOntology::ShowClassLabels($rec["OID2"], $rec["range"]);
				}
				echo "</td>";
			}
			else if($EntityType=="DATA_PROP")
			{
				echo "<td>".$rec["OTitle3"]."</td>";
				echo "<td>";
				echo "<b>".$rec["PermittedValue"]." </b>";
				echo " (داده مجاز از خصوصیت <b>";
				echo $rec["RelatedPropertyLabel"];
				echo "</b>)";
				echo "</td>";
			}
			echo "</tr>";
		}		
		//echo $OntologyPropertyID."<br>";
		$query = "SELECT 
OntologyTitle,
ClassTitle, 
label 
FROM projectmanagement.OntologyClassMapping 
LEFT JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyClassMapping.OntologyClassID)
LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
LEFT JOIN projectmanagement.ontologies on (ontologies.OntologyID=OntologyClasses.OntologyID)
where MappedOntologyEntityID=? and MappedOntologyEntityType='PROP'";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));	
		while($rec = $res->fetch())
		{
			$i++;
			echo "<tr ";
			if($i%2==0)
				echo "class='EvenRow'";
			else
				echo "class='OddRow'";			
			echo ">";
			echo "<td>".$rec["OntologyTitle"]."</td>";
			echo "<td>";
			echo " کلاس ";
			echo "<b>".$rec["label"]."</b> ";
			//echo " (".$rec["PropertyTitle"].")";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	static function GetHirarchyOfClass($OntologyClassID)
	{
		$ret = "";
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClassHirarchy 
		JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyClassHirarchy.OntologyClassParentID)
		JOIN projectmanagement.OntologyClassLabels on (OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID)
		where OntologyClassHirarchy.OntologyClassID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID)); 
		$i = 0;
		while($rec = $res->fetch())
		{
			if($i==0)
				$ret .= "کلاسهای فرزند: ";
			else
				$ret .= " - ";
			$ret .= $rec["label"];
			$i++;
		}
		$query = "select * from projectmanagement.OntologyClassHirarchy 
		JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyClassHirarchy.OntologyClassID)
		JOIN projectmanagement.OntologyClassLabels on (OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID)
		where OntologyClassHirarchy.OntologyClassParentID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID)); 
		$i = 0;
		while($rec = $res->fetch())
		{
			if($i==0)
				$ret .= "<br>کلاسهای پدر: ";
			else
				$ret .= " - ";
			$ret .= $rec["label"];
			$i++;
		}
		return $ret;
	}

	// بر اسسا کلاسهای معادل در هستان نگار حاصل از ادغام سلسله مراتب کلاسها را با توجه به سلسله مراتب کلاسها در هستان نگارهای منبع ایجاد می کند
	static function UpdateClassHirarchies($OntologyMergeProjectID)
	{
	  $mysql = pdodb::getInstance();
	  // ةمام روابط سلسله مراتبی منابع را که در مورد آنها تصمیم گیری نشده است انتخاب می کند
	  $query = "select * FROM projectmanagement.OntologyMergeHirarchy where OntologyMergeProjectID=? and ActionType='NOT_DECIDE'";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($OntologyMergeProjectID));
	  while($rec = $res->fetch())
	  {
	    $OntologyClassHirarchyID = $rec["OntologyClassHirarchyID"];
	    //echo $OntologyClassHirarchyID."<br>";
	    $sw = false;
	    // در صورتیکه کلاسی معادل کلاس مربوط به پدر و فرزند به صورت معادل در هستان نگار هدف وجود داشت
	    $query = "select TargetEntityID from OntologyMergeEntities where EntityType='CLASS' and EntityID=? and OntologyMergeProjectID=? and TargetEntityType='CLASS'";
	    $mysql->Prepare($query);
	    $fres = $mysql->ExecuteStatement(array($rec["ParentClassID"], $OntologyMergeProjectID));
	    if($frec = $fres->fetch())
	    {
	      $ParentClassID = $frec["TargetEntityID"];
	      //echo "ParentClassID: ".$frec["TargetEntityID"]."<br>";
	      $query = "select TargetEntityID from OntologyMergeEntities where EntityType='CLASS' and EntityID=? and OntologyMergeProjectID=? and TargetEntityType='CLASS'";
	      $mysql->Prepare($query);
	      $fres = $mysql->ExecuteStatement(array($rec["ChildClassID"], $OntologyMergeProjectID));
	      if($frec = $fres->fetch())
	      {
		//echo "ChildClassID: ".$frec["TargetEntityID"]."<br>";
		$ChildClassID = $frec["TargetEntityID"];
		if($ParentClassID!=$ChildClassID)
		{
		  $mysql->Prepare("delete from projectmanagement.OntologyClassHirarchy where OntologyClassID=? and OntologyClassParentID=?");
		  $mysql->ExecuteStatement(array($ParentClassID, $ChildClassID));		

		  $mysql->Prepare("insert into projectmanagement.OntologyClassHirarchy (OntologyClassID, OntologyClassParentID) values (?, ?)");
		  $mysql->ExecuteStatement(array($ParentClassID, $ChildClassID));		
		}
		$sw = true;
	      }
	    }
	    if($sw)
	    {
	      $mysql->Prepare("update projectmanagement.OntologyMergeHirarchy set ActionType='MAP', TargetParentClassID=?, TargetChildClassID=?  where OntologyMergeHirarchyID=?");
	      $mysql->ExecuteStatement(array($ParentClassID, $ChildClassID, $OntologyClassHirarchyID));
	    }
	    else
	    {
	      $mysql->Prepare("update projectmanagement.OntologyMergeHirarchy set ActionType='REJECT' where OntologyMergeHirarchyID=?");
	      $mysql->ExecuteStatement(array($OntologyClassHirarchyID));
	    }
	  }
	}
	
	// یک کلاس تصمیم گیری نشده پیدا کرده و در مورد آن پیشنهاد می دهد
	static function FindClassAndDecide($OntologyMergeProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select OntologyMergeEntityID, ClassTitle, OntologyTitle, label, OntologyClassID from projectmanagement.OntologyMergeEntities 
		JOIN projectmanagement.OntologyClasses on (OntologyClassID=EntityID)
		JOIN ontologies using (OntologyID) 
		LEFT JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
		where ActionType='NOT_DECIDE' and OntologyMergeProjectID=? and EntityType='CLASS' order by OntologyMergeEntityID";
		//echo "<br>".$OntologyMergeProjectID;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyMergeProjectID));
		if($rec = $res->fetch())
		{
			echo "<form method=post>";
			echo "<input type=hidden name=Execute id=Execute value='1'>";
			echo "<input type=hidden name=EntityType id=EntityType value='CLASS'>";
			echo "<input type=hidden name=EntityID id=EntityID value='".$rec["OntologyClassID"]."'>";
			echo "<input type=hidden name=OntologyMergeEntityID id=OntologyMergeEntityID value='".$rec["OntologyMergeEntityID"]."'>";
			
			echo "<input type=hidden name=OntologyMergeProjectID id=OntologyMergeProjectID value='".$OntologyMergeProjectID."'>";
			
			echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
			echo "<tr>";
			echo "<td>";
			echo "<input type=radio id=MyDecision name=MyDecision value='IGNORE' >";
			echo "صرفنظر کردن از استفاده در هستان نگار مقصد،  ";
			echo "به دلیل <select name=IgnoreReason id=IgnoreReason>";
			echo "<option value='UN_RELATED_DOMAIN'>خارج از موضوع بودن";
			echo "<option value='MODELING_ISSUE'>تفاوت طراحی";			
			echo "<option value='OTHER'>سایر";
			echo "</select>";
			echo "<input type=text id='IgnoreDescription' name='IgnoreDescription'>";
			echo "<br>";
			echo "<input type=radio id=MyDecision name=MyDecision value='ADD' checked >";
			echo "به عنوان کلاس به هستان نگار مقصد اضافه شود. ";
			echo "</td></tr><tr><td align=center>";			
			echo "<input type=submit value='اعمال'>";
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo "کلاس  <b>".$rec["label"]."</b> (".$rec["ClassTitle"].") ";
			echo " در هستان نگار ".$rec["OntologyTitle"];
			echo " .وجود دارد";
			echo "<br>";
			echo MergeOntology::GetHirarchyOfClass($rec["OntologyClassID"])."<br>";
			echo "این کلاس به عناصر زیر در هستان نگارهای دیگر نگاشت شده است: <br>";

			MergeOntology::ShowAllMappedEntityForClass($OntologyMergeProjectID, $rec["OntologyClassID"]);
			//echo $rec["EntityID"];
			//MergeOntology::CopyClassTo($rec["EntityID"], $TargetOntologyID);
			//echo "+";
			//MergeOntology::ChangeMergeRecordStatus($rec["OntologyMergeEntityID"], "ADD");	
	
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "</form>";//MergeOntology::UpdateAllMappedEntityStatusForClass($OntologyMergeProjectID, 			$rec["EntityID"]);
			//echo "*";
			return true;
		}
		return false;
	}


	// یک خصوصیت تصمیم گیری نشده پیدا کرده و در مورد آن پیشنهاد می دهد
	static function FindPropertyAndDecide($OntologyMergeProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select OntologyMergeEntityID, PropertyTitle, OntologyTitle, label, OntologyPropertyID, OntologyProperties.OntologyID, domain, `range`, PropertyType from projectmanagement.OntologyMergeEntities 
		JOIN projectmanagement.OntologyProperties on (OntologyPropertyID=EntityID)
		JOIN ontologies using (OntologyID) 
		LEFT JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
		where ActionType='NOT_DECIDE' and OntologyMergeProjectID=? and EntityType='PROPERTY'";
		//echo $query."<br>".$OntologyMergeProjectID;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyMergeProjectID));
		if($rec = $res->fetch())
		{
			echo "<form method=post>";
			echo "<input type=hidden name=Execute id=Execute value='1'>";
			echo "<input type=hidden name=EntityType id=EntityType value='PROP'>";
			echo "<input type=hidden name=EntityID id=EntityID value='".$rec["OntologyPropertyID"]."'>";
			echo "<input type=hidden name=OntologyMergeEntityID id=OntologyMergeEntityID value='".$rec["OntologyMergeEntityID"]."'>";
			
			echo "<input type=hidden name=OntologyMergeProjectID id=OntologyMergeProjectID value='".$OntologyMergeProjectID."'>";
			
			echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
			echo "<tr>";
			echo "<td>";
			echo "<input type=radio id=MyDecision name=MyDecision value='IGNORE'>";
			echo "صرفنظر کردن از استفاده در هستان نگار مقصد،  ";
			echo "به دلیل <select name=IgnoreReason id=IgnoreReason>";
			echo "<option value='UN_RELATED_DOMAIN'>خارج از موضوع بودن";
			echo "<option value='MODELING_ISSUE'>تفاوت طراحی";			
			echo "<option value='OTHER'>سایر";
			echo "</select>";
			echo "<input type=text id='IgnoreDescription' name='IgnoreDescription'>";
			echo "<br>";
			echo "<input type=radio id=MyDecision name=MyDecision value='ADD' checked>";
			echo "به عنوان خصوصیت به هستان نگار مقصد اضافه شود. ";
			echo "</td></tr><tr><td align=center>";			
			echo "<input type=submit value='اعمال'>";
			echo "</td>";
			echo "</tr>";			
			echo "<tr>";
			echo "<td>";
			echo "خصوصیت  <b>".$rec["label"]."</b> (".$rec["PropertyTitle"].") ";
			echo " در هستان نگار ".$rec["OntologyTitle"];
			echo " .وجود دارد";
			echo "<br>";
			echo "دامنه: ";
			MergeOntology::ShowClassLabels($rec["OntologyID"], $rec["domain"]);
			echo "<br>";
			if($rec["PropertyType"]=="OBJECT")
			{
				echo "برد: ";
				MergeOntology::ShowClassLabels($rec["OntologyID"], $rec["range"]);
				echo "<br>";
			}
			echo "<br>";
			echo "این خصوصیت به عناصر زیر در هستان نگارهای دیگر نگاشت شده است: <br>";
			echo "<br><li>";			
			echo "علامت ضربدر قرمز به معنی آن است که برای کلاس مربوطه کلاس معادلی در هستان نگار مقصد ایجاد نشده است. کلمات نوشته شده به سبز نام کلاس معادل در کلاس مقصد هستند.<br>";			
			MergeOntology::ShowAllMappedEntityForProperty($OntologyMergeProjectID, $rec["OntologyPropertyID"]);
			//echo $rec["EntityID"];
			//MergeOntology::CopyClassTo($rec["EntityID"], $TargetOntologyID);
			//echo "+";
			//MergeOntology::ChangeMergeRecordStatus($rec["OntologyMergeEntityID"], "ADD");	
	
			echo "</td>";
			echo "</tr>";

			echo "</table>";
			echo "</form>";//MergeOntology::UpdateAllMappedEntityStatusForClass($OntologyMergeProjectID, 			$rec["EntityID"]);
			//echo "*";
			return true;
		}
		return false;
	}


	static function AddSourceClassAndProperties($OntologyMergeProjectID)
	{
	  $mysql = pdodb::getInstance();
	// اضافه کردن کلاسها و خصوصیات در صورتیکه از قبل در فهرست ادغام نباشند
	// مقادیر مجاز خصوصیات هم اضافه می شوند
	  $query = "insert into projectmanagement.OntologyMergeEntities (OntologyMergeProjectID, EntityID, EntityType, ActionType, EntityTitle, EntityLabel)
		    select ?, OntologyClassID, 'CLASS', 'NOT_DECIDE', ClassTitle, group_concat(label) from projectmanagement.OntologyClasses 
				JOIN projectmanagement.OntologyMergeProjectMembers using (OntologyID)
			    JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
				where OntologyMergeProjectID=? 
		    and OntologyClassID not in 
		    (
		    select EntityID from projectmanagement.OntologyMergeEntities 
		    where OntologyMergeEntities.OntologyMergeProjectID=OntologyMergeProjectMembers.OntologyMergeProjectID
		    and EntityType='CLASS'
		    )
		    group by OntologyClassID, ClassTitle";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($OntologyMergeProjectID, $OntologyMergeProjectID));
	  
	  $query = "insert into projectmanagement.OntologyMergeEntities (OntologyMergeProjectID, EntityID, EntityType, ActionType, EntityTitle, EntityLabel)
		  select ?, OntologyPropertyID, 'PROPERTY', 'NOT_DECIDE', PropertyTitle, group_concat(label) from projectmanagement.OntologyProperties  
			      JOIN projectmanagement.OntologyMergeProjectMembers using (OntologyID)
			  JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
			      where OntologyMergeProjectID=? 
		  and OntologyPropertyID not in 
		  (
		  select EntityID from projectmanagement.OntologyMergeEntities 
		  where OntologyMergeEntities.OntologyMergeProjectID=OntologyMergeProjectMembers.OntologyMergeProjectID
		  and EntityType='PROPERTY'
		  )
		  group by OntologyPropertyID, PropertyTitle";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($OntologyMergeProjectID, $OntologyMergeProjectID));
	  
$query = "insert into projectmanagement.OntologyMergeEntities (OntologyMergeProjectID, EntityID, EntityType, ActionType, EntityTitle, EntityLabel)
		  select ?, OntologyPropertyPermittedValueID, 'PROPERTY_VALUE', 'NOT_DECIDE', OntologyPropertyID, PermittedValue from projectmanagement.OntologyPropertyPermittedValues  
				JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
			      JOIN projectmanagement.OntologyMergeProjectMembers using (OntologyID)
			      where OntologyMergeProjectID=? 
		  and OntologyPropertyPermittedValueID not in 
		  (
		  select EntityID from projectmanagement.OntologyMergeEntities 
		  where OntologyMergeEntities.OntologyMergeProjectID=OntologyMergeProjectMembers.OntologyMergeProjectID
		  and EntityType='PROPERTY_VALUE'
		  )";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($OntologyMergeProjectID, $OntologyMergeProjectID));	  
	  
	  $query = "select * from 
		  projectmanagement.OntologyClassHirarchy
		  JOIN projectmanagement.OntologyClasses using (OntologyClassID)
		  JOIN projectmanagement.OntologyMergeProjectMembers using (OntologyID)
		  where OntologyMergeProjectID=? ";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($OntologyMergeProjectID));
	  while($rec = $res->fetch())
	  {
	      $query = "select count(*) as tcount from 
		      projectmanagement.OntologyMergeHirarchy
		      where ParentClassID='".$rec["OntologyClassID"]."' and ChildClassID='".$rec["OntologyClassParentID"]."' and OntologyMergeProjectID=?";;
	      $mysql->Prepare($query);
	      $res2 = $mysql->ExecuteStatement(array($OntologyMergeProjectID));
	      $rec2 = $res2->fetch();
	      if($rec2["tcount"]=="0")
	      {
		$query = "insert into projectmanagement.OntologyMergeHirarchy (OntologyMergeProjectID, ChildClassID, ParentClassID, ActionType)
		values (?, '".$rec["OntologyClassParentID"]."', '".$rec["OntologyClassID"]."', 'NOT_DECIDE')";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($OntologyMergeProjectID));
	      }
	  }
	}
		
	// اضافه کردن کلاس مشابه یک کلاس موجود به هستان نگار مقصد
	static function CopyClassTo($OntologyClassID, $TargetOntologyID)
	{
		$mysql = pdodb::getInstance();
		
		$query = "select ClassTitle from projectmanagement.OntologyClasses where OntologyClassID=? and ClassTitle not in (select ClassTitle from projectmanagement.OntologyClasses where OntologyID=?)";
		//echo "<br>".$OntologyClassID."<br>".$TargetOntologyID;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID, $TargetOntologyID));
		if($rec = $res->fetch())
		{
			$ClassTitle = $rec["ClassTitle"];
		}
		else
		{
			// در صورتیکه کلاس وجود نداشته باشد یا عنوان کلاس قبلا در هستان نگار مقصد ایجاد شده باشد
			return 0;
		}
		
		$query = "insert into projectmanagement.OntologyClasses (OntologyID, ClassTitle) values (?, ?)";
		//echo "<br>".$OntologyClassID." - ".$TargetOntologyID."<br>";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($TargetOntologyID, $ClassTitle));
		
		$query = "select OntologyClassID from projectmanagement.OntologyClasses where ClassTitle=? and OntologyID=?";
//echo "<br>".$OntologyClassID." - ".$TargetOntologyID."<br>";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ClassTitle, $TargetOntologyID));
	//echo "***";
		if($rec = $res->fetch())
		{
			$NewOntologyClassID = $rec["OntologyClassID"];

			$query = "insert into projectmanagement.OntologyClassLabels (OntologyClassID, label) select ".$NewOntologyClassID.", label from projectmanagement.OntologyClassLabels where OntologyClassID=?";				
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array($OntologyClassID));
		}
		else
			return 0;
		return $NewOntologyClassID;
	}
	
	function FindClassTitleInTargetOntology($ClassTitle, $OntologyID, $TargetOntologyID, $OntologyMergeProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "select * from projectmanagement.OntologyClasses where ClassTitle=? and OntologyID=?";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($ClassTitle, $OntologyID));
		if($rec = $res->fetch())
		{
			$OntologyClassID = $rec["OntologyClassID"];
		}
		else
		{
			return "";
		}

		// به دست آوردن کلاس معادل مربوطه در هستان نگار هدف
		$query = "select * from projectmanagement.OntologyMergeEntities 
		JOIN projectmanagement.OntologyClasses on (OntologyClassID=TargetEntityID)
		where OntologyMergeProjectID=? and EntityType='CLASS' and EntityID=? and TargetEntityType='CLASS'";
		//echo "<br>".$OntologyMergeProjectID."<br>".$OntologyClassID;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyMergeProjectID, $OntologyClassID));
				
		if($rec = $res->fetch())
		{
			return $rec["ClassTitle"];
		}
		else
		{
			// ممکن است آن کلاس صرفنظر شده باشد و در هستان نگار نهایی نباشد
			//و یا این کلاس به یک خصوصیت یا داده مجاز خصوصیت نگاشت شده باشد 
			return "";
		}
	}

	
	function FindClassIDInTargetOntology($OntologyClassID, $OntologyID, $TargetOntologyID, $OntologyMergeProjectID)
	{
		$mysql = pdodb::getInstance();
		
		// به دست آوردن کلاس معادل مربوطه در هستان نگار هدف
		$query = "select * from projectmanagement.OntologyMergeEntities 
		JOIN projectmanagement.OntologyClasses on (OntologyClassID=TargetEntityID)
		where OntologyMergeProjectID=? and EntityType='CLASS' and EntityID=? and TargetEntityType='CLASS'";
		//echo "<br>".$OntologyMergeProjectID."<br>".$OntologyClassID;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyMergeProjectID, $OntologyClassID));
				
		if($rec = $res->fetch())
		{
			return $rec["OntologyClassID"];
		}
		else
		{
			// ممکن است آن کلاس صرفنظر شده باشد و در هستان نگار نهایی نباشد
			//و یا این کلاس به یک خصوصیت یا داده مجاز خصوصیت نگاشت شده باشد 
			return 0;
		}
	}
	
// کلاسهای ذکر شده در حوزه و برد ارسالی به تابع را تبدیل به کلاسهای هستان نگار مقصد کرده و به حوزه و برد کلاس مربوطه در هستان نگار مقصد اضافه می کند
/*
$OntologyPropertyID: کد خصوصیت در هستان نگار هدف
$OntologyID: کد هستان نگاری که عناوین کلاسهای ذکر شده در حوزه و بردی که باید اضافه شد مربوط به آن است
$CurrentDomain: حوزه فعلی خصوصیت در هستان نگار هدف
$CurrentRange: برد فعلی خصوصیت در هستان نگار هدف
$ExtraDomain: فهرست کلاسهایی که باید به حوزه اضافه شود. عنوان این کلاسها در هستان نگار منبع هستند
*/
	static function IsInArray($ClassName, $ClassArray)
	{
		for($i=0; $i<count($ClassArray); $i++)
		{
			if($ClassArray[$i]==$ClassName)
				return true;
		}
		return false;
	}

	// OntologyPropertyID: کد خصوصیت منبع که قرار است معادل روابط مجاز آن به مقصد منتقل شود
	// OntologyID: کد هستان نگار منبع
	// TargetOntologyPropertyID: کد خصوصیت معادل ایجاد شده در مقصد
	function AddObjectpropertyRestrictions($OntologyPropertyID, $OntologyID, $TargetOntologyPropertyID, $TargetOntologyID, $OntologyMergeProjectID)
	{
	  
		$mysql = pdodb::getInstance();
		// تنها روابط مجاز را درج می کند
		$query = "select * from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID=? and RelationStatus='VALID'";
		$mysql->Prepare($query);				
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));		
		while($rec = $res->fetch())
		{
			$OldDomainClassID = $rec["DomainClassID"];
			$OldRangeClassID = $rec["RangeClassID"];
			$DomainClassID = MergeOntology::FindClassIDInTargetOntology($OldDomainClassID, $OntologyID, $TargetOntologyID, $OntologyMergeProjectID);
			$RangeClassID = MergeOntology::FindClassIDInTargetOntology($OldRangeClassID, $OntologyID, $TargetOntologyID, $OntologyMergeProjectID);
			if($DomainClassID>0 && $RangeClassID>0)
			{
			    $query = "delete from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID=? and DomainClassID=? and RangeClassID=?";
			    $mysql->Prepare($query);
			    $mysql->ExecuteStatement(array($TargetOntologyPropertyID, $DomainClassID, $RangeClassID));

			    $query = "insert into projectmanagement.OntologyObjectPropertyRestriction (OntologyPropertyID, DomainClassID, RangeClassID, RelationStatus) values (?, ?, ?, 'VALID')";
			    $mysql->Prepare($query);
			    $mysql->ExecuteStatement(array($TargetOntologyPropertyID, $DomainClassID, $RangeClassID));
			    
			}
		}
	}
	
// OntologyPropertyID: کد خصوصیت در هستان نگار هدف که باید حوزه و برد آن بروز شود
// $OntologyID: کد هستان نگار منبع 
// $ExtraDomain: نام کلاسهایی از هستان نگار منبع که باید معادل آنها به حوزه در خصوصیت مقصد اضافه شود
// $ExtraRange: توضیح فوق برای برد
	function AddDomainAndRange($OntologyPropertyID, $OntologyID, $ExtraDomain, $ExtraRange, $TargetOntologyID, $OntologyMergeProjectID)
	{
		$mysql = pdodb::getInstance();
		
		$query = "select * from projectmanagement.OntologyProperties where OntologyPropertyID=?";
		$mysql->Prepare($query);				
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));		
		if($rec = $res->fetch())
		{
			$CurrentDomain = $rec["domain"];
			$CurrentRange = $rec["range"];
			$PropertyType = $rec["PropertyType"];
		}
		else
			return false;		

		$NewDomain = $NewRange = array();
		$NewDomainSt = $CurrentDomain;
		$NewRangeSt = $CurrentRange;

		$NewDomain = explode(", ", $CurrentDomain);
		$NewRange = explode(", ", $CurrentRange);

		$domains = explode(", ", $ExtraDomain);
		$ranges = explode(", ", $ExtraRange);
		
		for($i=0; $i<count($domains); $i++)
		{
			$ClassTitle = $domains[$i];
			$NewClassTitle = MergeOntology::FindClassTitleInTargetOntology($ClassTitle, $OntologyID, $TargetOntologyID, $OntologyMergeProjectID);
			if($NewClassTitle!="")
			{
				
				// آگر کلاس از قبل در حوزه نبود آن را اضافه می کند
				if(!MergeOntology::IsInArray($NewClassTitle, $NewDomain))
				{
					
					array_push($NewDomain, $NewClassTitle);
					if($NewDomainSt!="")
						$NewDomainSt .= ", ";
					$NewDomainSt .= $NewClassTitle;
				}
			}
		}
		
		for($i=0; $i<count($ranges); $i++)
		{
			$ClassTitle = $ranges[$i];
			$NewClassTitle = MergeOntology::FindClassTitleInTargetOntology($ClassTitle, $OntologyID, $TargetOntologyID, $OntologyMergeProjectID);
			if($NewClassTitle!="")
			{
				if(!MergeOntology::IsInArray($NewClassTitle, $NewRange))
				{
					array_push($NewRange, $NewClassTitle);
					if($NewRangeSt!="")
						$NewRangeSt .= ", ";
					$NewRangeSt .= $NewClassTitle;
				}
			}
		}
		$mysql->Prepare("update projectmanagement.OntologyProperties set domain=?, `range`=? where OntologyPropertyID=?");
		$mysql->ExecuteStatement(array($NewDomainSt, $NewRangeSt, $OntologyPropertyID));
		
	}


	// اضافه کردن خصوصیت مشابه یک خصوصیت موجود به هستان نگار مقصد
	static function CopyPropertyTo($OntologyPropertyID, $TargetOntologyID, $OntologyMergeProjectID)
	{
		$mysql = pdodb::getInstance();
		$CurrentDomain = $CurrentRange = "";
		$query = "select PropertyTitle, PropertyType, domain, `range`, OntologyID from projectmanagement.OntologyProperties where OntologyPropertyID=? and PropertyTitle not in (select PropertyTitle from projectmanagement.OntologyProperties where OntologyID=?)";
		//echo "<br>".$OntologyPropertyID."<br>".$TargetOntologyID;
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID, $TargetOntologyID));
		if($rec = $res->fetch())
		{
			$PropertyTitle = $rec["PropertyTitle"];
			$PropertyType = $rec["PropertyType"];
			$OntologyID = $rec["OntologyID"];
			$CurrentDomain = $rec["domain"];
			$CurrentRange = $rec["range"];
			//echo "title: ".$PropertyTitle."<br>type: ".$PropertyType."<br>ontologyid: ".$OntologyID."<br>domain: ".$CurrentDomain;
			
		}
		else
		{
			// در صورتیکه کلاس وجود نداشته باشد یا عنوان کلاس قبلا در هستان نگار مقصد ایجاد شده باشد
			return 0;
		}
		// کد خصوصیت اضافه شده را به دست می آورد تا برچسبهای آن را اضافه کند
		$query = "insert into projectmanagement.OntologyProperties (OntologyID, PropertyTitle, PropertyType) values (?, ?, ?)";
		//echo "<br>".$OntologyClassID." - ".$TargetOntologyID."<br>";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($TargetOntologyID, $PropertyTitle, $PropertyType));
		
		$query = "select OntologyPropertyID from projectmanagement.OntologyProperties where PropertyTitle=? and OntologyID=?";
//echo "<br>".$OntologyClassID." - ".$TargetOntologyID."<br>";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($PropertyTitle, $TargetOntologyID));
	
		if($rec = $res->fetch())
		{
			$NewOntologyPropertyID = $rec["OntologyPropertyID"];
			//echo "NewPropertyID: ".$NewOntologyPropertyID."<br>";
			$query = "insert into projectmanagement.OntologyPropertyLabels (OntologyPropertyID, label) select ".$NewOntologyPropertyID.", label from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=?";				
			$mysql->Prepare($query);
			$mysql->ExecuteStatement(array($OntologyPropertyID));
			
			// مقادیر مجاز را هم منتقل می کند
			MergeOntology::CopyPermittedValues($OntologyPropertyID, $NewOntologyPropertyID);
			
			MergeOntology::AddDomainAndRange($NewOntologyPropertyID, $OntologyID, $CurrentDomain, $CurrentRange, $TargetOntologyID, $OntologyMergeProjectID);
// پس از اضافه کردن خصوصیت چنانچه خصوصیت شیء باشد روابط مجاز را هم نگاشت و منتقل می کند
			MergeOntology::AddObjectpropertyRestrictions($OntologyPropertyID, $OntologyID, $NewOntologyPropertyID, $TargetOntologyID, $OntologyMergeProjectID);			
		}
		else
			return 0;
		return $NewOntologyPropertyID;
	}

	// کپی مقادیر مجاز یک خصوصیت به خصوصیت دیگر
	static function CopyPermittedValues($FromPropertyID, $ToPropertyID)
	{
	    $mysql = pdodb::getInstance();
	    $query = "insert into projectmanagement.OntologyPropertyPermittedValues (OntologyPropertyID, PermittedValue) 
		      select ".$ToPropertyID.", PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID=?";				
	    $mysql->Prepare($query);
	    $mysql->ExecuteStatement(array($FromPropertyID));
	}
	
	// وضعیت عنصر را در فهرست عناصر مورد ادغام تنظیم می کند. برای مواردی که وضعیت 
	// صرفنظر شده است دلیل و شرح آن باید ارسال شود
	static function ChangeMergeRecordStatus($OntologyMergeEntityID, $ActionType, $IgnoreReason='OTHER', $IgnoreDescription='')
	{
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyMergeEntities set ActionType=?, IgnoreReason=?, IgnoreDescription=? where  OntologyMergeEntityID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($ActionType, $IgnoreReason, $IgnoreDescription, $OntologyMergeEntityID));	
	}
	
	// عنصر مقصد را در هستان نگار نهایی برای یک رکورد ادغام ثبت می کند
	static function UpdateMergeRecordTarget($OntologyMergeEntityID, $TargetEntityID, $TargetEntityType)
	{
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyMergeEntities set TargetEntityID=?, TargetEntityType=? where  OntologyMergeEntityID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($TargetEntityID, $TargetEntityType, $OntologyMergeEntityID));	
	}
	
	
		

		// وضعیت تمام موجودیتهایی که به خصوصیت مربوطه نگاشت شده اند در فهرست موجودیتهای ادغام به "نگاشت شده" تغییر می دهد
		// OntologyPropertyID: کد خصوصیتی که از روی آن خصوصیت مقصد ایجاد شده است (خصوصیت منبع)
		// TargetEntityID: کد خصوصیت ایجاد شده در هستان نگار مقصد
	static function UpdateAllMappedEntityStatusForProperty($OntologyMergeProjectID, $OntologyPropertyID, $TargetEntityID, $TargetEntityType, $TargetOntologyID)
	{
		$mysql = pdodb::getInstance();
		
		// تمام موجودیتهایی که به خصوصیت ذکر شده نگاشت شده اند
		$query = "SELECT * FROM projectmanagement.OntologyPropertyMapping where OntologyPropertyID=?";
		//echo "<font color=green>".$query."<br>".$OntologyPropertyID."<br></font>";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));
		while($rec = $res->fetch())
		{
			$EntityID = $rec["MappedOntologyEntityID"];
			$EntityType = $rec["MappedOntologyEntityType"];
			if($EntityType=="DATA_PROP")
				$EntityType="PROPERTY_VALUE";
			if($EntityType=="PROP")
				$EntityType="PROPERTY";
			// وضعیت موجودیت مربوطه در فهرست عناصر هدف ادغام را به نگاشت شده تغییر می دهد
			$query = "update projectmanagement.OntologyMergeEntities set ActionType='MAP', TargetEntityID=?, TargetEntityType=? where EntityID=? and EntityType=? and OntologyMergeProjectID=?";
			$mysql->Prepare($query);
			//echo $query."<br>".$EntityID.", ".$EntityType.", ".$OntologyMergeProjectID."<br>";
			$mysql->ExecuteStatement(array($TargetEntityID, $TargetEntityType, $EntityID, $EntityType, $OntologyMergeProjectID));
			
			// اگر عنصر نگاشت شده خصوصیت بود بایستی حوزه و برد آن به خصوصیت مقصد اضافه شود
			if($EntityType=="PROPERTY")
			{
				$query = "select * from projectmanagement.OntologyProperties where OntologyPropertyID=?";
				$mysql->Prepare($query);
				$res2 = $mysql->ExecuteStatement(array($EntityID));
				// بایستی خصوصیت نگاشت شده به خصوصیت منبع بررسی شده و روابط مجاز آن به خصوصیت هدف اضافه شود
				if($rec2 = $res2->fetch())
				{
					MergeOntology::AddDomainAndRange($TargetEntityID, $rec2["OntologyID"], $rec2["domain"], $rec2["range"], $TargetOntologyID, $OntologyMergeProjectID);
					MergeOntology::AddObjectpropertyRestrictions($EntityID, $rec2["OntologyID"], $TargetEntityID, $TargetOntologyID, $OntologyMergeProjectID);
					
					MergeOntology::CopyPermittedValues($EntityID, $TargetEntityID);
				}
			}
			
		}		

		// تمام کلاسهایی که به خصوصیت مورد نظر نگاشت شده اند
		$query = "SELECT * FROM projectmanagement.OntologyClassMapping where MappedOntologyEntityID=? and MappedOntologyEntityType='PROP'";
		$mysql->Prepare($query);
		//echo "<font color=blue>".$query."<br>".$OntologyPropertyID."<br>";
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));
		while($rec = $res->fetch())
		{
			// وضعیت موجودیت مربوطه در فهرست عناصر هدف ادغام را به نگاشت شده تغییر می دهد
			$query = "update projectmanagement.OntologyMergeEntities set ActionType='MAP', TargetEntityID=?, TargetEntityType=? where EntityID=? and EntityType='CLASS' and OntologyMergeProjectID=?";
			$mysql->Prepare($query);
			//echo $query."<br>".$EntityID.", ".$EntityType.", ".$OntologyMergeProjectID."<br>";
			$mysql->ExecuteStatement(array($TargetEntityID, $TargetEntityType, $rec["OntologyClassID"], $OntologyMergeProjectID));
		}		

		// تمام خصوصیتهایی که به کلاس مورد نظر نگاشت شده اند
		$query = "SELECT * FROM projectmanagement.OntologyPropertyMapping where MappedOntologyEntityID=? and MappedOntologyEntityType='PROP'";
		$mysql->Prepare($query);
		//echo $query."<br>".$OntologyClassID."<br>";
		$res = $mysql->ExecuteStatement(array($OntologyPropertyID));
		while($rec = $res->fetch())
		{
			// وضعیت موجودیت مربوطه در فهرست عناصر هدف ادغام را به نگاشت شده تغییر می دهد
			$query = "update projectmanagement.OntologyMergeEntities set ActionType='MAP', TargetEntityID=?, TargetEntityType=? where EntityID=? and EntityType='PROPERTY' and OntologyMergeProjectID=?";
			$mysql->Prepare($query);
			//echo $query."<br>".$EntityID.", ".$EntityType.", ".$OntologyMergeProjectID."<br>";
			$mysql->ExecuteStatement(array($TargetEntityID, $TargetEntityType, $rec["OntologyClassID"], $OntologyMergeProjectID));
			

			// حوزه و برد خصوصیت نگاشت شده را به حوزه و برد خصوصیت ایجاد شده نهایی می افزاید
			$query = "select * from projectmanagement.OntologyProperties where OntologyPropertyID=?";
			$mysql->Prepare($query);
			$res2 = $mysql->ExecuteStatement(array($MappedOntologyEntityID));
			if($rec2 = $res2->fetch())
			{
				/*
				echo "MappedOntologyEntityID: ".$MappedOntologyEntityID."<br>";
				echo "TargetEntityID: ".$TargetEntityID."<br>";
				echo "domain: ".$rec2["domain"]."<br>";
				echo "range: ".$rec2["range"]."<br>";
				*/
				MergeOntology::AddDomainAndRange($TargetEntityID, $rec2["OntologyID"], $rec2["domain"], $rec2["range"], $TargetOntologyID, $OntologyMergeProjectID);
				MergeOntology::AddObjectpropertyRestrictions($MappedOntologyEntityID, $rec2["OntologyID"], $TargetEntityID, $TargetOntologyID, $OntologyMergeProjectID);
				
				MergeOntology::CopyPermittedValues($MappedOntologyEntityID, $TargetEntityID);
			}			
			
		}		
		
	}

	
	// وضعیت تمام موجودیتهایی که به کلاس مربوطه نگاشت شده اند در فهرست موجودیتهای ادغام به "نگاشت شده" تغییر می دهد
	static function UpdateAllMappedEntityStatusForClass($OntologyMergeProjectID, $OntologyClassID, $TargetEntityID, $TargetEntityType)
	{
		$mysql = pdodb::getInstance();
		// تمام موجودیتهایی که به کلاس ذکر شده نگاشت شده اند
		$query = "SELECT * FROM projectmanagement.OntologyClassMapping where OntologyClassID=?";
		//echo $query."<br>".$OntologyClassID."<br>";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		while($rec = $res->fetch())
		{
			$EntityID = $rec["MappedOntologyEntityID"];
			$EntityType = $rec["MappedOntologyEntityType"];
			if($EntityType=="DATA_PROP")
				$EntityType="PROPERTY_VALUE";
			if($EntityType=="PROP")
				$EntityType="PROPERTY";
			// وضعیت موجودیت مربوطه در فهرست عناصر هدف ادغام را به نگاشت شده تغییر می دهد
			$query = "update projectmanagement.OntologyMergeEntities set ActionType='MAP', TargetEntityID=?, TargetEntityType=? where EntityID=? and EntityType=? and OntologyMergeProjectID=? ";
			$mysql->Prepare($query);
			//echo $query."<br>".$EntityID.", ".$EntityType.", ".$OntologyMergeProjectID."<br>";
			$mysql->ExecuteStatement(array($TargetEntityID, $TargetEntityType, $EntityID, $EntityType, $OntologyMergeProjectID));
		}		

		// تمام کلاسهایی که به کلاس مورد نظر نگاشت شده اند
		$query = "SELECT * FROM projectmanagement.OntologyClassMapping where MappedOntologyEntityID=? and MappedOntologyEntityType='CLASS'";
		$mysql->Prepare($query);
		//echo $query."<br>".$OntologyClassID."<br>";
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		while($rec = $res->fetch())
		{
			// وضعیت موجودیت مربوطه در فهرست عناصر هدف ادغام را به نگاشت شده تغییر می دهد
			$query = "update projectmanagement.OntologyMergeEntities set ActionType='MAP', TargetEntityID=?, TargetEntityType=? where EntityID=? and EntityType='CLASS' and OntologyMergeProjectID=? and (ActionType='NOT_DECIDE' or ActionType='IGNORE')";
			$mysql->Prepare($query);
			//echo $query."<br>".$EntityID.", ".$EntityType.", ".$OntologyMergeProjectID."<br>";
			$mysql->ExecuteStatement(array($TargetEntityID, $TargetEntityType, $rec["OntologyClassID"], $OntologyMergeProjectID));
		}		

		// تمام خصوصیتهایی که به کلاس مورد نظر نگاشت شده اند
		$query = "SELECT * FROM projectmanagement.OntologyPropertyMapping where MappedOntologyEntityID=? and MappedOntologyEntityType='CLASS'";
		$mysql->Prepare($query);
		//echo $query."<br>".$OntologyClassID."<br>";
		$res = $mysql->ExecuteStatement(array($OntologyClassID));
		while($rec = $res->fetch())
		{
			// وضعیت موجودیت مربوطه در فهرست عناصر هدف ادغام را به نگاشت شده تغییر می دهد
			$query = "update projectmanagement.OntologyMergeEntities set ActionType='MAP', TargetEntityID=?, TargetEntityType=? where EntityID=? and EntityType='PROPERTY' and OntologyMergeProjectID=? and (ActionType='NOT_DECIDE' or ActionType='IGNORE')";
			$mysql->Prepare($query);
			//echo $query."<br>".$EntityID.", ".$EntityType.", ".$OntologyMergeProjectID."<br>";
			$mysql->ExecuteStatement(array($TargetEntityID, $TargetEntityType, $rec["OntologyClassID"], $OntologyMergeProjectID));
		}		
		
	}
	
	static function ResetOntologyMergeEntitiesStatus($OntologyMergeProjectID)
	{
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.OntologyMergeEntities set ActionType='NOT_DECIDE' where OntologyMergeProjectID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($OntologyMergeProjectID));
	}
	
	static function ClearOntology($TargetOntologyID)
	{	
		$mysql = pdodb::getInstance();
	    $mysql->Prepare("delete from projectmanagement.OntologyClassHirarchy where OntologyClassID in 
	    (select OntologyClassID from projectmanagement.OntologyClasses where OntologyID=?)");
	    $mysql->ExecuteStatement(array($TargetOntologyID));
	    
	    $mysql->Prepare("delete from projectmanagement.OntologyClassLabels where OntologyClassID in 
	    (select OntologyClassID from projectmanagement.OntologyClasses where OntologyID=?)");
	    $mysql->ExecuteStatement(array($TargetOntologyID));
	    
	    $mysql->Prepare("delete from projectmanagement.OntologyClasses where OntologyID=?");
	    $mysql->ExecuteStatement(array($TargetOntologyID));
	    
	    $mysql->Prepare("delete from projectmanagement.OntologyProperties where OntologyID=?");
	    $mysql->ExecuteStatement(array($TargetOntologyID));
	    
	    $mysql->Prepare("delete from projectmanagement.OntologyPropertyLabels where OntologyPropertyID in 
	    (select OntologyPropertyID from projectmanagement.OntologyProperties where OntologyID=?)");
	    $mysql->ExecuteStatement(array($TargetOntologyID));
	    
	    $mysql->Prepare("delete from projectmanagement.OntologyMergeReviewedPotentials where TargetOntologyID=? ");
	    $mysql->ExecuteStatement(array($TargetOntologyID));
	
	    $mysql->Prepare("delete from projectmanagement.OntologyMergeEntities where OntologyMergeProjectID=? ");
	    $mysql->ExecuteStatement(array($OntologyMergeProjectID));
	
	    $mysql->Prepare("delete from projectmanagement.OntologyMergeHirarchy where OntologyMergeProjectID=? ");
	    $mysql->ExecuteStatement(array($OntologyMergeProjectID));
		
	}
}
?>