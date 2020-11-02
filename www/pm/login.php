<?php
	include_once("../shares/sys_config.class.php");
    include "definitions.php";
    include "SharedClass.class.php";
	session_start();
	HTMLBegin();
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
			$message = "<span style=\"color: red; \">نام کاربر یا کلمه عبور نادرست است</span>";
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
<div id="login" class="container col-md-4 mx-auto">
    <form id="login-form" class="form" action="" method="post">
    <?php if(isset($_REQUEST["Referer"])) { ?>
        <input type=hidden name=Referer id=Referer value='<?php echo $_REQUEST["Referer"]; ?>'>
        <?php } ?>
        <table class="table table-sm table-bordered">
            <thead><tr><th>ورود به سامانه</th></tr></thead>
            <tr>
                <td>
                    <table class="table table-sm table-borderless">
                    <tr>
                        <td>
                        نام کاربری
                        </td>
                        <td>
                        <input type="text" name="UserID" id="UserID" class="form-control" required>
                        </td>
                    </tr>
                        <tr>
                            <td>
                                کلمه عبور
                            </td>
                            <td>
                                <input type="password" name="UserPassword" id="UserPassword" class="form-control" required>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="text-center"><input type="submit" name="submit" class="btn btn-primary btn-sm" value="ورود"></td>
            </tr>
        <?php if(!empty($message)) echo '<div class="alert alert-warning text-right"  role="alert">'.$message."</div>"; ?>
            <tr>
                <td class="alert alert-info">
                  اگر به عنوان خبره قصد ارزیابی یک هستان نگار را دارید و کد ورود در اختیار شما قرار گرفته است لطفا
                <a href='ValidateOntology.php'><b>اینجا</b></a>
                 را کلیک کنید.

                </td>
            </tr>
        </table>
    </form>
	</div>
</body>
</html>
