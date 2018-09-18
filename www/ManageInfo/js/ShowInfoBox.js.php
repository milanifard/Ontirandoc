<?php
require_once "header.inc.php";
?>
<style type="text/css">
    #info_box{
        background:#eee;
        width:300px;
        border:#0f50be solid 2px;
        padding:5px;
        font-family:Tahoma;
        font-size:12px;
        word-wrap:break-word;
        white-space: normal;
        display:none
    }
    #info_tbl td{
        background:#ccffff;
        word-wrap:break-word;
        white-space: normal;   
    }
    #info_tbl tr.header td{
        background:#0099cc;
        color:#ffffff;
        font-weight:bold;
    }
</style>
<div id="info_box" onclick="javascript:event.stopPropagation();"></div>
<script type="text/javascript">
var info_box = document.getElementById("info_box");
function ShowInfo(e, Dim_ID){
    e.stopPropagation();
    info_box.style.display = 'none';
    info_box.innerHTML = "لطفا منتظر بمانید...<br><br>";
    info_box.style.position = 'absolute';
    info_box.style.display = 'block';
    info_box.style.top = (e.clientY+document.body.scrollTop)+'px';
    info_box.style.left = (e.clientX+document.body.scrollLeft)+'px';
    var request;
    if (window.XMLHttpRequest)
        request=new XMLHttpRequest();
    else
        request=new ActiveXObject("Microsoft.XMLHTTP");
    request.open("POST", "<?="../ManageInfo/ShowInfoBox.data.php"?>", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.onreadystatechange = function() { 
        if(request.readyState == 4 && request.status == 200) {
            var res = JSON.parse(request.responseText);
            var tbl = "اطلاعاتی برای نمایش وجود ندارد.";
            if(res.length){
                tbl = "<table id='info_tbl' border='0' width='100%'><tr class='header'><td>عنوان گزارش</td></tr>";
                var i = 0;
                for(i in res){
                    tbl += "<tr><td>"+res[i]["ItemName"]+"</td></tr>";
                }
                tbl += "</table>";
            }
            info_box.innerHTML = tbl;
        }
    }
    var param = "&Dim_ID="+Dim_ID;
    request.send("task=getInfo"+param);
}
document.addEventListener("click", function(){
    info_box.style.display='none';
});
</script>
