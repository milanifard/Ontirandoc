<script type="text/javascript" src="/sharedClasses/resources/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="/sharedClasses/resources/ext-all.js"></script>

<?php 
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionDecisions.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
include_once("classes/SessionMembers.class.php");

?>
<html>
<script type="text/javascript">
var canvas, ctx, flag = false,
    prevX = 0,
    currX = 0,
    prevY = 0,
    currY = 0,
    dot_flag = false;

var x = "black",
    y = 2;

function init() {
    canvas = document.getElementById('can');
    ctx = canvas.getContext("2d");
    w = canvas.width;
    h = canvas.height;

    canvas.addEventListener("mousemove", function (e) {
        findxy('move', e)
    }, false);
    canvas.addEventListener("mousedown", function (e) {
        findxy('down', e)
    }, false);
    canvas.addEventListener("mouseup", function (e) {
        findxy('up', e)
    }, false);
    canvas.addEventListener("mouseout", function (e) {
        findxy('out', e)
    }, false);
}

function color(obj) {
    switch (obj.id) {
        case "green":
            x = "green";
            break;
        case "blue":
            x = "blue";
            break;
        case "red":
            x = "red";
            break;
        case "yellow":
            x = "yellow";
            break;
        case "orange":
            x = "orange";
            break;
        case "black":
            x = "black";
            break;
        case "white":
            x = "white";
            break;
    }
    if (x == "white") y = 14;
    else y = 2;

}

function draw() {
    ctx.beginPath();
    ctx.moveTo(prevX, prevY);
    ctx.lineTo(currX, currY);
    ctx.strokeStyle = x;
    ctx.lineWidth = y;
    ctx.stroke();
    ctx.closePath();
}

/*function erase() {
    var m = confirm("Want to clear");
    if (m) {
        ctx.clearRect(0, 0, w, h);
        document.getElementById("canvasimg").style.display = "none";
    }
}*/

function save() {
  /*  document.getElementById("canvasimg").style.border = "2px solid";
     var dataURL = canvas.toDataURL();
     document.getElementById("canvasimg").src = dataURL;
     document.getElementById("canvasimg").style.display = "inline";*/
    // canvas = document.getElementById("canvasimg");
     var dataURL = canvas.toDataURL();

     Ext.Ajax.request({
			//url: 'PrintSessionDecisions.php?MemberPersonID='+ id,
     url: 'PrintSession.php?UniversitySessionID=<?php echo $_REQUEST['UniversitySessionID']?>&MemberPersonID=<?php echo $_REQUEST['MemberPersonID']?>',			
   

			params: {
				image: dataURL
			},
			method: "POST"	,
success:function(response,options)
			{
		alert('جلسه با موفقیت امضا گردید.') ; 
                    window.opener.location.reload();

                    window.close();
			}	 	 
		}); 
			
  
		
}

function findxy(res, e) {
    if (res == 'down') {
        prevX = currX;
        prevY = currY;
        currX = e.clientX - canvas.offsetLeft;
        currY = e.clientY - canvas.offsetTop;

        flag = true;
        dot_flag = true;
        if (dot_flag) {
            ctx.beginPath();
            ctx.fillStyle = x;
            ctx.fillRect(currX, currY, 2, 2);
            ctx.closePath();
            dot_flag = false;
        }
    }
    if (res == 'up' || res == "out") {
        flag = false;
    }
    if (res == 'move') {
        if (flag) {
            prevX = currX;
            prevY = currY;
            currX = e.clientX - canvas.offsetLeft;
            currY = e.clientY - canvas.offsetTop;
            draw();
        }
    }
}
</script>

<body onload="init()">

    <canvas id="can" width="2100" height="1000" style="position:absolute;top:4%;left:2%;border:2px solid;"></canvas>
    <!--<div style="position:absolute;top:2%;left:40%;">انتخاب رنگ</div>-->
    <div style="position:absolute;top:1%;left:45%;width:10px;height:10px;background:green;" id="green" onclick="color(this)"></div>
    <div style="position:absolute;top:1%;left:46%;width:10px;height:10px;background:blue;" id="blue" onclick="color(this)"></div>
    <div style="position:absolute;top:1%;left:47%;width:10px;height:10px;background:red;" id="red" onclick="color(this)"></div>
    <div style="position:absolute;top:2%;left:45%;width:10px;height:10px;background:yellow;" id="yellow" onclick="color(this)"></div>
    <div style="position:absolute;top:2%;left:46%;width:10px;height:10px;background:orange;" id="orange" onclick="color(this)"></div>
    <div style="position:absolute;top:2%;left:47%;width:10px;height:10px;background:black;" id="black" onclick="color(this)"></div>
    <!--<div style="position:absolute;top:2%;left:32%;">پاک کن</div>-->
    <div style="position:absolute;top:1%;left:35%;width:15px;height:15px;background:white;border:2px solid;" id="white" onclick="color(this)"></div>
    <img id="canvasimg" style="position:absolute;top:10%;left:52%;" style="display:none;">
    <input type="button" value="ذخیره" id="btn" size="100" onclick="save()"   style="position:absolute;top:95%;left:50%;">
    <!--<input type="button" value="clear" id="clr" size="23" onclick="erase()" style="position:absolute;top:100%;left:10%;">-->

</body>
</html>


