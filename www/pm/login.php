<?php
	include ("../shares/sys_config.class.php");
	session_start();
	function getRealIpAddr()
	{
	    $ip = 0;
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	    {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	    {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}	

	$message = "";
	if(isset($_REQUEST["UserID"]))
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("select * from projectmanagement.AccountSpecs 
				JOIN projectmanagement.persons on (persons.PersonID=AccountSpecs.PersonID) 
				where UserID=? and UserPassword=sha1(md5(?))");
		$res = $mysql->ExecuteStatement(array($_REQUEST["UserID"], $_REQUEST["UserPassword"]), PDO::FETCH_ASSOC, true);
		if($trec = $res->fetch())
		{
			$_SESSION["UserID"] = $trec["UserID"];
			$_SESSION["SystemCode"] = 1;
			$_SESSION["PersonID"] = $trec["PersonID"];
			$_SESSION["UserName"] = $trec["pfname"]." ".$trec["plname"];
			$_SESSION["LIPAddress"] = ip2long(getRealIpAddr());
            if($_SESSION["LIPAddress"]=="")
                $_SESSION["LIPAddress"] = "0";
			if(isset($_REQUEST["Referer"]))
			  echo "<script>document.location='".$_REQUEST["Referer"]."';</script>";
			else
			  echo "<script>document.location='main.php';</script>";
			die();
		}
		else
			$message = "<font color=red>نام کاربر یا کلمه عبور نادرست است</font>";
		//echo $_REQUEST["UserName"];
	}
	if(isset($_SESSION["UserID"]))
	{
	    echo "<script>document.location='main.php';</script>";
	    die();
	}
	/*
	if(strpos($_SERVER["HTTP_REFERER"], "http://pm.falinoos.com/pm/")!== false)
	{
	  echo $_SERVER["HTTP_REFERER"];
	}
	*/
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet"  href="./css/login.css" type="text/css">
</head>
<body  dir=rtl link="#0000FF" alink="#0000FF" vlink="#0000FF">
<div id="login">
        <h1 class="text-center text-white pt-5">سامانه توسعه هستان نگار</h3>
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" class="form" action="" method="post">
						<?php if(isset($_REQUEST["Referer"])) { ?>
							<input type=hidden name=Referer id=Referer value='<?php echo $_REQUEST["Referer"]; ?>'>
							<?php } ?>
                            <h3 class="text-center text-info" id="login-header">ورود به سامانه</h3>
                            <div class="form-group text-right">
                                <label for="username" class="text-info">نام کاربری</label><br>
								<input type="text" name="UserID" id="UserID" class="form-control" required>
                            </div>
                            <div class="form-group text-right">
                                <label for="password" class="text-info">کلمه رمز</label><br>
								<input type="password" name="UserPassword" id="UserPassword" class="form-control" required>
                            </div>
							<div>
                            <div class="form-group text-right">
                                <input type="submit" name="submit" class="btn btn-info btn-md" value="ورود">
                            </div>
							<?php if(!empty($message)) echo '<div class="alert alert-warning text-right"  role="alert">'.$message."</div>"; ?>
							</div>
                            <div class="text-right">
							    <label class="text-info">  اگر به عنوان خبره قصد ارزیابی یک هستان نگار را دارید و کد ورود در اختیار شما قرار گرفته است لطفا 
								<a href='ValidateOntology.php'><b>اینجا</b></a>
								 را کلیک کنید.</label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
	</div>
</body>
</html>
