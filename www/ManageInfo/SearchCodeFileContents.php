<?
  $NotAddSlashes = "1";
  include("header.inc.php");

  $mysql = pdodb::getInstance();
  
 
    HTMLBegin();
?>
	<form method=post>
	<input type=hidden name='ShowPagesHaveThisStrings' id='ShowPagesHaveThisStrings' value=1>
	<table align=center border=1 cellspacing=0 width=80%>
	<tr>
	  <td>
	  <table>
	    <tr>
	      <td>رشته در کل محتویات صفحه: </td>
	      <td>&nbsp; 
		<input size=70 dir=ltr type=text name=GeneralContent1 id=GeneralContent1 value='<? if(isset($_REQUEST["GeneralContent1"])) echo $_REQUEST["GeneralContent1"]; ?>'><br>
		و
		<input size=70 dir=ltr type=text name=GeneralContent2 id=GeneralContent2 value='<? if(isset($_REQUEST["GeneralContent2"])) echo $_REQUEST["GeneralContent2"]; ?>'><br>
	و
		<input size=70 dir=ltr type=text name=GeneralContent3 id=GeneralContent3 value='<? if(isset($_REQUEST["GeneralContent3"])) echo $_REQUEST["GeneralContent3"]; ?>'>
	      </td>
	    </tr>

	    <tr>
	      <td>رشته درون رشته های ثابت محتویات صفحه: </td>
	      <td>
		&nbsp; 
		<input size=70 dir=ltr type=text name=StaticContent1 id=StaticContent1 value='<? if(isset($_REQUEST["StaticContent1"])) echo $_REQUEST["StaticContent1"]; ?>'><br>
		و
		<input size=70 dir=ltr type=text name=StaticContent2 id=StaticContent2 value='<? if(isset($_REQUEST["StaticContent2"])) echo $_REQUEST["StaticContent2"]; ?>'><br>
	و
		<input size=70 dir=ltr type=text name=StaticContent3 id=StaticContent3 value='<? if(isset($_REQUEST["StaticContent3"])) echo $_REQUEST["StaticContent3"]; ?>'>
	      </td>
	    </tr>
	    
	    <tr>
	      <td colspan=3 align=center>
	      <input type=submit value='جستجو'>
	      </td>
	    </tr>
	  </table>
	  </td>
	</tr>
	</table>
	</form>

<?
	if(isset($_REQUEST["ShowRelatedMenu"]))
	{
	  $mysql->Prepare("select SysCode, SystemType, systems.path, ScriptPath, ScriptName, description, SysSubDesc from framework.SystemPages
			    JOIN framework.SystemFacilities using (FacilityID)
			    JOIN framework.systems using (SysCode) where PageName=? and FacilityStatus='ENABLE' and SystemStatus='ENABLE'");
	  $res = $mysql->ExecuteStatement(array($_REQUEST["ShowRelatedMenu"]));
	    echo "<table align=center cellspacing=0 cellpadding=5 border=1 dir=rtl>";
	    echo "<tr class=HeaderOfTable><td colspan=2>منوهایی که از صفحه ".$_REQUEST["ShowRelatedMenu"]." استفاده می کنند</td></tr>";
    
	  while($rec = $res->fetch())
	  {
	    $FullPath = "";
	    // پورتال جامع اعضا با کد ۵۰ 
	    if($rec["SystemType"]=="GENERAL" && $rec["SysCode"]!=50)
	      $FullPath = "https://sadaf.um.ac.ir".$rec["path"];
	    else if($rec["SysCode"]==50)
	      $FullPath = "https://pooya.um.ac.ir";
	    else
	      $FullPath .= $rec["path"];
	    $FullPath .= $rec["ScriptPath"];
	    $FullPath .= $rec["ScriptName"];
	      echo "<tr>";
	      echo "<td>".$rec["description"]."</td>";
	      echo "<td><a target=_blank href='".$FullPath."'>".$rec["SysSubDesc"]."</a></td>";
	      echo "</tr>";
	  }
	  die();
	}
	
	if(isset($_REQUEST["ShowPagesHaveThisStrings"]))
	{
	  $Params = array();
	  $query = "select path, name from monitoring.PageContent ";
	  $cond = "";
	  if($_REQUEST["StaticContent1"]!="")
	  {
	     $cond .= " StaticContent like ? ";
	     array_push($Params, "%".$_POST["StaticContent1"]."%");
	  }
	  if($_REQUEST["StaticContent2"]!="")
	  {
	    if($cond!="")
	      $cond .= " and ";
	     $cond .= " StaticContent like ? ";
	     array_push($Params, "%".$_POST["StaticContent2"]."%");
	  }
	  if($_REQUEST["StaticContent3"]!="")
	  {
	    if($cond!="")
	      $cond .= " and ";
	     $cond .= " StaticContent like ? ";
	     array_push($Params, "%".$_POST["StaticContent3"]."%");
	  }
	  if($_REQUEST["GeneralContent1"]!="")
	  {
	    if($cond!="")
	      $cond .= " and ";
	    $cond .= " content like ? ";
	    array_push($Params, "%".$_POST["GeneralContent1"]."%");
	  }
	  if($_REQUEST["GeneralContent2"]!="")
	  {
	    if($cond!="")
	      $cond .= " and ";
	    $cond .= " content like ? ";
	    array_push($Params, "%".$_POST["GeneralContent2"]."%");
	  }
	  if($_REQUEST["GeneralContent3"]!="")
	  {
	    if($cond!="")
	      $cond .= " and ";
	    $cond .= " content like ? ";
	    array_push($Params, "%".$_POST["GeneralContent3"]."%");
	  }
	  if($cond=="")
	  {
	    echo "شرطی انتخاب نشده";
	    die();
	  }

	  $mysql->Prepare($query." where ".$cond);
	  $res = $mysql->ExecuteStatement($Params);
	  if($res->rowCount()>0)
	  {
	      echo "<table border=1 cellpadding=5 cellspacing=0 align=center width=90%>";
	      echo "<tr bgcolor=#eeeeee><td colspan=4>فایلهایی که در محتوای کد آنها نام این صفحه استفاده شده است</td></tr>";	  
	      echo "<tr class=HeaderOfTable>";
	      echo "<td>منوی مرتبط</td><td>فایلهای مرتبط</td><td>نام فایل</td><td>مسیر</td>";
	      echo "</tr>";
	      
	      while($rec = $res->fetch())
	      {
		echo "<tr>";
		echo "<td> <a target=_blank href='EditTableInfo.php?ShowRelatedMenu=".$rec["name"]."'>منوی مرتبط</a>";	    
		echo "<td> <a target=_blank href='EditTableInfo.php?ShowRelatedFile=".$rec["name"]."'>فایلهای مرتبط</a>";	    
		echo "<td dir=ltr><a href='EditTableInfo.php?ShowFilePath=".$rec["path"]."&ShowFileName=".$rec["name"]."' target=_blank>".$rec["name"]."</a></td>";
		echo "</td>";
		echo "<td dir=ltr>".$rec["path"]."</td>";	    
		echo "</tr>";
	      }
	      echo "</table>";
	  }
	  die();
	}

	if(isset($_REQUEST["ShowFileName"]))
	{
	  $mysql->Prepare("select content from monitoring.PageContent where path=? and name=?");
	  $res = $mysql->ExecuteStatement(array($_REQUEST["ShowFilePath"], $_REQUEST["ShowFileName"]));
	  if($rec = $res->fetch())
	  {
	    echo "<table dir=ltr><tr><td>";
	    echo "<pre>";
	    echo htmlspecialchars($rec["content"])."</pre>";
	    echo "</td></tr></table>";
	  }
	  die();
	}
	
	if(isset($_REQUEST["ShowRelatedFile"]))
	{
	  $query = "select path, name from monitoring.PageContent 
			    where type='php' and 
			    (StaticContent like ?)";
	  
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array("%".$_REQUEST["ShowRelatedFile"]."%"));
	  echo "<table border=1 cellpadding=5 cellspacing=0 align=center width=90%>";
	  if($res->rowCount()>0)
	  {
	    echo "<tr bgcolor=#eeeeee><td colspan=4>فایلهایی که در محتوای کد آنها نام این صفحه استفاده شده است</td></tr>";	  
	    echo "<tr class=HeaderOfTable>";
	    echo "<td>منوی مرتبط</td><td>فایلهای مرتبط</td><td>نام فایل</td><td>مسیر</td>";
	    echo "</tr>";
	  }
	  else 
	  {
	    echo "<tr bgcolor=#eeeeee><td >فایلی که در کد آن نام این فایل باشد وجود ندارد</td></tr>";	  
	    echo "</tr>";
	  }
	  
	  while($rec = $res->fetch())
	  {
	    echo "<tr>";
	    echo "<td> <a target=_blank href='EditTableInfo.php?ShowRelatedMenu=".$rec["name"]."'>منوی مرتبط</a>";	    
	    echo "<td> <a target=_blank href='EditTableInfo.php?ShowRelatedFile=".$rec["name"]."'>فایلهای مرتبط</a>";	    
	    echo "<td dir=ltr><a href='EditTableInfo.php?ShowFilePath=".$rec["path"]."&ShowFileName=".$rec["name"]."' target=_blank>".$rec["name"]."</a></td>";
	    echo "</td>";
	    echo "<td dir=ltr>".$rec["path"]."</td>";	    
	    echo "</tr>";
	  }
	  echo "</table>";
	  echo "<br>";
	
	  die();
	}
?>
</body>
</html>