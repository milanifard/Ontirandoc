<?php
	include ("../shares/header.inc.php");
	HTMLBegin();
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
<br>
        <div class="container col-md-8 mx-auto">
            <table class="table table-sm table-bordered">
                <thead>
                <tr>
                    <th>تغییر کلمه عبور</th>
                </tr>
                </thead>
                <tr>
                    <td>
                        <form class="form" method="post" name=f1 id=f1 enctype='multipart/form-data'>
                            <?php if(isset($_REQUEST["PersonID"])) { ?>
                                <input type=hidden name=PersonID id=PersonID value='<?php echo $_REQUEST["PersonID"] ?>'>
                            <?php } ?>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td>
								کلمه عبور فعلی
                                </td>
                                <td><input type="password" name="OldPass" class="form-control" required></td>
                            </tr>
                            <tr>
                                <td>کلمه عبور جدید</td>
                                <td>
								<input type="password" name="NewPass" class="form-control" required>
                                </td>
                            </tr>
                            <tr>
                                <td>تکرار کلمه عبور جدید</td>
                                <td>
								<input type="password" name="ConfirmPass" class="form-control" required>
                                </td>
							</tr>
							<tr>
								<td class="text-center" colspan="2">
									<input type="submit" name="submit" class="btn btn-sm btn-primary" value="اعمال" onclick='javascript: CheckValidity();'>
								</td>
								<?php if(!empty($message)) echo '<div class="alert alert-warning text-right"  role="alert">'.$message."</div>"; ?>

								<?php if(isset($_REQUEST["PersonID"]) && !isset($_REQUEST["pfname"])) { ?>
									<input type=hidden name=PersonID id=PersonID value='<?php echo $_REQUEST["PersonID"] ?>'>
									<?php } ?>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
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

