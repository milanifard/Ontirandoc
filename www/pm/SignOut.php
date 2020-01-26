<?php
	session_start();
	session_destroy();
	$Referer = "";
?>
<head>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style type="text/css" > INPUT, SELECT { font-family: Tahoma }'</style>
	<link rel="stylesheet"  href="./css/login.css" type="text/css">
</head>
<body class="table-responsive" dir=rtl link="#0000FF" alink="#0000FF" vlink="#0000FF">
	<br>
		<form method=post>
			<table class="table table-bordered" align=center>
				<tr class="warning">
					<td align=center class="text-center">
						<? 
						if(isset($_REQUEST["SessionExpired"])) { 
							$Referer="?Referer=".$_SERVER["HTTP_REFERER"];
							echo C_SESSION_EXPIRED;
						} 
						else { 
							echo C_WELCOME;
						} 
						?>
						<br>
						<br>
						<? echo  C_RELOGING; ?>
						<a class="text-center" href='login.php<?php echo $Referer; ?>'> 
							<? echo  C_CLICK_THIS; ?> 
						</a>
					</td>
				</tr>
			</table>
		</form>
</body>
