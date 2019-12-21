<?php
	include ("../shares/header.inc.php");
	// HTMLBegin();
	$UnitCode = 0;
	$message = "";
	$mysql = pdodb::getInstance();
	if(isset($_REQUEST["NewPass"]))
	{
		$query = "select * from projectmanagement.AccountSpecs where UserID='".$_SESSION["UserID"]."' and UserPassword=sha1(md5('".$_REQUEST["OldPass"]."'))";
		$res = $mysql->Execute($query);
		if($rec = $res->fetch())
		{
			$query = "update projectmanagement.AccountSpecs set UserPassword=sha1(md5('".$_REQUEST["NewPass"]."')) where UserID='".$_SESSION["UserID"]."'";
			$mysql->Execute($query);
			$message = "<font color=red>اطلاعات ذخیره شد</font>";
		}
		else
		    $message = "<font color=red>کلمه عبور فعلی نادرست وارد شده است</font>";
	}
?>
<!<!DOCTYPE html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<title></title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="./css/changePassword.css" type="text/css">
	</head>
	<body dir=rtl>
	<div id="ChangePassword">
        <div class="container">
            <div id="ChangePassword-row" class="row justify-content-center align-items-center">
                <div id="ChangePassword-column" class="col-md-6">
                    <div id="ChangePassword-box" class="col-md-12">
						<form class="form" method="post" name=f1 id=f1 enctype='multipart/form-data'>
						<?php if(isset($_REQUEST["PersonID"])) { ?>
							<input type=hidden name=PersonID id=PersonID value='<?php echo $_REQUEST["PersonID"] ?>'>
							<?php } ?>
							<h3 class="text-center text-info" id="ChangePassword-header">تغییر کلمه عبور</h3>
							<div class="form-group text-right">
								<label class="text-info">کلمه عبور فعلی</label><br>
								<input type="password" name="OldPass" class="form-control" required>
							</div>
							<div class="form-group text-right">
								<label class="text-info">کلمه عبور جدید</label><br>
								<input type="password" name="NewPass" class="form-control" required>
							</div>
							<div class="form-group text-right">
								<label class="text-info">تکرار کلمه عبور جدید</label><br>
								<input type="password" name="ConfirmPass" class="form-control" required>
							</div>
							<div>
								<div class="form-group text-right">
									<input type="submit" name="submit" class="btn btn-info btn-md" value="اعمال" onclick='javascript: CheckValidity();'>
								</div>
								<?php if(!empty($message)) echo '<div class="alert alert-warning text-right"  role="alert">'.$message."</div>"; ?>
								</div>
								<?php if(isset($_REQUEST["PersonID"]) && !isset($_REQUEST["pfname"])) { ?>
									<input type=hidden name=PersonID id=PersonID value='<?php echo $_REQUEST["PersonID"] ?>'>
									<?php } ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
		function CheckValidity(){
		if(f1.NewPass.value!=f1.ConfirmPass.value)
		{
			alert("کلمه عبور جدید با تکرار آن یکی نیست");
			return;
		}
		f1.submit();
		}
		</script>
	</body>
</html>

