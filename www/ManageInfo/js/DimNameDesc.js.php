<div id="Desc_Box" style="display:inline-block;">
    <img src="bi/ajax-loader.gif" style="display:none;">
    <div id="DimNameDesc_Box" style="display:inline-block;"></div>
</div>
<script type="text/javascript">
function ShowDimNameDesc(DimID){
	if(DimID == '')return ;
	var loading = document.getElementById("Desc_Box").getElementsByTagName('img')[0];
        loading.style.display='';
	var http = new XMLHttpRequest();
	var params = "task=getDimNameDesc&DimID="+DimID; 
	http.open("POST", "../ManageInfo/DimNameDesc.data.php", true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.setRequestHeader("Content-length", params.length);
	http.setRequestHeader("Connection", "close");
	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200) {
			var response = http.responseText;
                        loading.style.display='none';
			document.getElementById('DimNameDesc_Box').innerHTML=response;
		}
		else{
			loading.style.display='none';
			document.getElementById('DimNameDesc_Box').innerHTML='';
		}
	}
	http.send(params);
}
</script>