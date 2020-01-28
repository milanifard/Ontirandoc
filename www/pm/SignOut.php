<?php	
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/projects.class.php");

HTMLBegin();
	session_start();
	session_destroy();
	$Referer = "";
?>
<head>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style type="text/css" > INPUT, SELECT { font-family: Tahoma }'</style>
</head>
<body>
	<br>
		<form method=post>
			<table class="table table-bordered bg-info" >
				<tr class="warning">
					<td class="text-center">
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
