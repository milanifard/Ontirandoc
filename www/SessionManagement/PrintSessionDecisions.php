<script type="text/javascript" src="/sharedClasses/resources/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="/sharedClasses/resources/ext-all.js"></script>
<script type="text/javascript">

	sign.prototype = { 	
		 canvas : 0 ,
		 ctx : 0 ,
		 flag : false ,
		 prevX : 0 ,
		 currX : 0 ,
		 prevY : 0 , 
		 currY : 0 ,
		 dot_flag : false ,
		 x : "black" ,
		 y : 2 	
	}
	
	function sign() 
	{
		return ; 
	}
		
	sign.prototype.draw = function()
	{	
		this.ctx.beginPath();
		this.ctx.moveTo(this.prevX, this.prevY);
		this.ctx.lineTo(this.currX, this.currY);
		this.ctx.strokeStyle = this.x;
		this.ctx.lineWidth = this.y;
		this.ctx.stroke();
		this.ctx.closePath();
	}
	
	sign.prototype.save = function(id) 
	{	

	    this.canvas = document.getElementById(id);
	    var dataURL = this.canvas.toDataURL();
	    Ext.Ajax.request({
			//url: 'PrintSessionDecisions.php?MemberPersonID='+ id,
                        url: 'PrintSessionDecisions.php?UniversitySessionID=<?php echo $_REQUEST['UniversitySessionID']?>&MemberPersonID='+ id,

			
			params: {
							   image: dataURL
						},
						method: "POST"		 	 
		});
		alert('جلسه با موفقیت امضا گردید.') ; 
 		document.ListForm.submit();


	
	}
	/*sign.prototype.erase() {
	    var m = confirm("Want to clear");
	    if (m) {
		ctx.clearRect(0, 0, w, h);
		document.getElementById("canvasimg").style.display = "none";
	    }
	}*/	
	sign.prototype.findxy = function(res, e) 
	{
		if (res == 'down') {
        this.prevX = this.currX;
        this.prevY = this.currY;
        this.currX = e.clientX - this.canvas.offsetLeft;
        this.currY = e.clientY - this.canvas.offsetTop;
 
        this.flag = true;
        this.dot_flag = true;
        if (this.dot_flag) {
            this.ctx.beginPath();
            this.ctx.fillStyle = this.x;
            this.ctx.fillRect(this.currX, this.currY, 2, 2);
            this.ctx.closePath();
            this.dot_flag = false;
        }
		}
		if (res == 'up' || res == "out") {
			this.flag = false;
		}
		if (res == 'move') {
			if (this.flag) {
				this.prevX = this.currX;
				this.prevY = this.currY;
				this.currX = e.clientX - this.canvas.offsetLeft;
				this.currY = e.clientY - this.canvas.offsetTop;
				this.draw();
			}
		}
	
	} 
		
</script>

<?php 
/*
 صفحه چاپ مصوبات جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-20
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionDecisions.class.php");
include ("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
include("classes/SessionMembers.class.php");

$targetpage = "PrintSessionDecisions.php?UniversitySessionID=".$_REQUEST["UniversitySessionID"].""; 
$adjacents = 1;
$NumberOfRec =1; 

if(isset($_REQUEST['page'])){
    
    $page = $_REQUEST['page'];
}
else{
    $page=1;
}

if($page){
    $FromRec = ($page - 1) * $NumberOfRec; 
}	
else{
    $FromRec =0;	
}

HTMLBegin();
if ( isset($_POST["image"]) && !empty($_POST["image"]) ) { 
echo '154564@@@@@@@@@';   
    // Init dataURL variable
    $dataURL = $_POST["image"];  
    // Extract base64 data (Get rid from the MIME & Data Type)
    $parts = explode(',', $dataURL);  
    $data = $parts[1];  

    // Decode Base64 data
    $dataa = base64_decode($data);  

    // Save data as an image
    $fp = addslashes(fread(fopen($data,'r')));
    fwrite($fp, $data);  
    fclose($fp); 


}
if(isset($_REQUEST["MemberPersonID"]))
{
 	manage_UniversitySessions::SignTheDescesionFile($_REQUEST["MemberPersonID"],$_REQUEST["UniversitySessionID"], $dataa);
   
}
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند

$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$uni_session = new be_UniversitySessions();
$uni_session->LoadDataFromDatabase($_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_SessionDecisions")=="YES")
	$HasAddAccess = true;
$res = manage_SessionDecisions::GetList($_REQUEST["UniversitySessionID"],$FromRec, $NumberOfRec); 
?>
<META http-equiv=Content-Type content="text/html; charset=UTF-8" >
<link rel="stylesheet" type="text/css" href="/sharedClasses/resources/css/ext-all.css" />
<style>
td{
	height : 26px;
	padding-right : 4px;
}
div.pagination 
{
	padding: 3px;
	margin: 3px;
	text-align:center;		
}
div.pagination a
{
	padding: 2px 5px 2px 5px; 
	margin: 2px;
	border: 1px solid #000000;
	text-decoration: none; /* no underline */
	color: #000000;
}
div.pagination a:hover, div.meneame a:active 
{
	border: 1px solid #000;
	background-image:none;
	background-color:#0061de;
	color: #fff;
}
div.pagination span.current 
{
	margin-right:3px;
	padding:2px 6px;		
	font-weight: bold;
	color: #ff0084;
} 
div.pagination span.disabled
{
	margin-right:3px;
	padding:2px 6px;
	color: #adaaad;
}
</style>

<br>
<!--<table width="70%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="9">
	جلسه: <?php echo $uni_session->SessionTypeID_Desc ?><br>
	عنوان: <?php echo $uni_session->SessionTitle ?><br>
	تاریخ: <?php echo $uni_session->SessionDate_Shamsi ?><br>
	شماره: <?php echo $uni_session->SessionNumber ?><br>
	ساعت تشکیل: <?php echo floor($uni_session->SessionStartTime/60).":".($uni_session->SessionStartTime%60) ?> مدت جلسه: <?php echo floor($uni_session->SessionDurationTime/60).":".($uni_session->SessionDurationTime%60) ?><br>
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width=1%>ردیف</td>
	<td>دستور کار</td>
	<td>مصوبه</td>
	<td width=10% nowrap>مسوول پیگیری</td>
	<td width=1% nowrap>مهلت اقدام</td>
</tr>
<?


for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "	<td>".htmlentities($res[$k]->OrderNo, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".str_replace("\n", "<br>", htmlentities($res[$k]->SessionPreCommandDescription, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	<td>".str_replace("\n", "<br>", htmlentities($res[$k]->description, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	<td>&nbsp;".$res[$k]->ResponsiblePersonID_FullName."</td>";
	echo "	<td nowrap>";
	if($res[$k]->DeadlineDate_Shamsi!="date-error")
		echo $res[$k]->DeadlineDate_Shamsi;
	else
		echo "-";
	echo "</td>";
	echo "</tr>";
}
?>
</table>-->
<br>
<form id="ListForm" name="ListForm" method="post"><table width="70%" align="center" border="1" cellspacing="0" cellpadding=10>
<tr bgcolor=#cccccc>
	<td colspan=7>حاضرین جلسه</td>
</tr>
<tr bgcolor=#cccccc>
	<td width=2%>ردیف</td><td width=42%>نام و نام خانوادگی<td width=3% nowrap>حضور</td><td width=3%>تاخیر</td><td colspan=2 width=26% >امضا</td>
</tr>
<?php
$a = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], 0, 1000);

$total_pages=count($a);
//echo $total_pages."<br>";
if ($page == 0) $page = 1;
//echo $page."<br>";				//if no page var is given, default to 1.
$prev = $page - 1;							//previous page is page - 1
//echo $prev."<br>";
$next = $page + 1;							//next page is page + 1
//echo $next."<br>";
$lastpage = ceil($total_pages/$NumberOfRec);		//lastpage is = total pages / items per page, rounded up.
//echo $lastpage."<br>";
$lpm1 = $lastpage - 1;						//last page minus 1
$pagination = "";
	if($lastpage >= 1)
	{	
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($page > 1) 
			$pagination.= "<a href=\"$targetpage&page=$prev\"><< </a>";
		else
			$pagination.= "<span class=\"disabled\"><< </span>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&page=$counter\">$counter</a>";					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&page=$counter\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&page=$lastpage\">$lastpage</a>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&page=1\">1</a>";
				$pagination.= "<a href=\"$targetpage&page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&page=$counter\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&page=$lastpage\">$lastpage</a>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&page=1\">1</a>";
				$pagination.= "<a href=\"$targetpage&page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&page=$counter\">$counter</a>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination.= "<a href=\"$targetpage&page=$next\">>></a>";
		else
			$pagination.= "<span class=\"disabled\">>></span>";
		$pagination.= "</div>\n";		
	}





$list= manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], $FromRec, $NumberOfRec);

	
$k = 0;
	for($i=0; $i<count($list); $i++)
	{
 		$SignImg ='<img src="DisplayCanvas.php?RecId=' . $list[$i]->SessionMemberID . '" width="100"  />';
		if($list[$i]->PresenceType=="PRESENT")
		{
			$k++;
			echo "<tr>";
			echo "<td width=2%>".$k."</td>";
			//echo "<td>".$list[$i]->FirstName." ".$list[$i]->LastName."</td>";
                        //echo "<td><a target=\"_blank\" href=\"Signature.php?a=".$list[$i]->FirstName." ".$list[$i]->LastName."\"></td>";
	echo "<td><a target=\"_blank\" href=\"Signature.php?a=".$list[$i]->SessionMemberID."\">".$list[$i]->FirstName." ".$list[$i]->LastName."</a></td>";
			
			echo "<td nowrap>".floor($list[$i]->PresenceTime/60).":".($list[$i]->PresenceTime%60)."</td>";
			echo "<td nowrap>".floor($list[$i]->TardinessTime/60).":".($list[$i]->TardinessTime%60)."</td>";
			if($list[$i]->canvasimg!=''){			
			echo "<td colspan=2  height=90px >" . $SignImg . "</td>";}
			else{			
			echo "<td width=14%  style=border-left:none;border-left-color:rgb(255,255,255);> 
                        <canvas id=".$list[$i]->MemberPersonID."  width=420px height=300px 
                            style=position:relative;top:(8.2+".($i * 60 ).")%;left:1%;border:2px>
                        </canvas>";
                        echo "</td>";
                         echo "<td width=12%  align=center style=margin-top:300px;>
                        <input type=button value='تایید' onclick='signObject".$i.".save(".$list[$i]->MemberPersonID.");' ></td>";}
                        ?>
                           <script>	
                                   var signObject<?=$i?> = new sign();
                                    signObject<?=$i?>.canvas = document.getElementById(<?=$list[$i]->MemberPersonID;?>);
                                    signObject<?=$i?>.ctx = signObject<?=$i?>.canvas.getContext("2d");

                                    w = signObject<?=$i?>.canvas.width;
                                    h = signObject<?=$i?>.canvas.height;

                                    signObject<?=$i?>.canvas.addEventListener("mousemove", function (e) {
                                            signObject<?=$i?>.findxy('move', e)
                                    }, false);
                                    signObject<?=$i?>.canvas.addEventListener("mousedown", function (e) {
                                            signObject<?=$i?>.findxy('down', e)
                                    }, false);
                                    signObject<?=$i?>.canvas.addEventListener("mouseup", function (e) {
                                            signObject<?=$i?>.findxy('up', e)
                                    }, false);
                                    signObject<?=$i?>.canvas.addEventListener("mouseout", function (e) {
                                            signObject<?=$i?>.findxy('out', e)
                                    }, false); 


                            </script>
                          <?

			/*if($list[$i]->canvasimg!='')			
			echo "<td>" . $SignImg . "</td>";
			else
			echo "<td>&nbsp;</td>";*/
			echo "</tr>"; 
		}
	}
/*echo "<tr bgcolor=#cccccc align=center>
            <td colspan=5><input type=button value='تایید' ></td>";

echo"</tr>"; */

?>       
 <tr> <td colspan=6 width=1% nowrap  dir=ltr >
	<?php echo $pagination?>
 </td></tr>


</table>
</form>
<br>
<table width="70%" align="center" border="1" cellspacing="0" cellpadding=10>
<tr bgcolor=#cccccc>
	<td>
	<b>
	غایبین جلسه: 
	</b>
<?php
	$k = 0;
	$list = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], 0, 1000);
	for($i=0; $i<count($list); $i++)
	{
		if($list[$i]->PresenceType=="ABSENT")
		{
			$k++;
			echo $k."- ";
			echo $list[$i]->FirstName." ".$list[$i]->LastName." ";
		}
	}
?>
	</td>
</tr>
</table>
<script>
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
