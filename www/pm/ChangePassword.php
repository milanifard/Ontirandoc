<?php
	include("header.inc.php");
	HTMLBegin();
	$UnitCode = 0;
	$mysql = pdodb::getInstance();
	if(isset($_REQUEST["NewPass"]))
	{
		$query = "select * from projectmanagement.AccountSpecs where UserID='".$_SESSION["UserID"]."' and UserPassword=sha1(md5('".$_REQUEST["OldPass"]."'))";
		$res = $mysql->Execute($query);
		if($rec = $res->fetch())
		{
			$query = "update projectmanagement.AccountSpecs set UserPassword=sha1(md5('".$_REQUEST["NewPass"]."')) where UserID='".$_SESSION["UserID"]."'";
			$mysql->Execute($query);
			echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
		}
		else
			echo "<p align=center><font color=red>کلمه عبور فعلی نادرست وارد شده است</font></p>";
	}
?>
<br>
<form method=post name=f1 id=f1 enctype='multipart/form-data'>
<?php if(isset($_REQUEST["PersonID"])) { ?>
<inut type=hidden name=PersonID id=PersonID value='<?php echo $_REQUEST["PersonID"] ?>'>
<?php } ?>
<table width=50% align=center border=1 cellspacing=0>
<tr>
<td>
	<table width=100% border=0>
	<tr>
		<td colspan=2 class=HeaderOfTable>
		تغییر کلمه عبور
		</td>
	</tr>
	<tr>
		<td>
		کلمه عبور فعلی: 
		</td>
		<td>
		<input name=OldPass type=password size=40 maxlength=200 value=''>
		</td>
	</tr>
	<tr>
		<td nowrap>
		کلمه عبور جدید: 
		</td>
		<td>
		<input name=NewPass type=password size=40 maxlength=200 value=''>
		</td>
	</tr>
	<tr>
		<td nowrap>
		تکرار کلمه عبور جدید:  
		</td>
		<td>
		<input name=ConfirmPass type=password size=40 maxlength=200 value=''>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center class=FooterOfTable>
			<input type=button value='اعمال' onclick='javascript: CheckValidity();'>
		</td>
	</tr>
	</table>
</td>
</tr>
</table>
<br>
<?php if(isset($_REQUEST["PersonID"]) && !isset($_REQUEST["pfname"])) { ?>
<input type=hidden name=PersonID id=PersonID value='<?php echo $_REQUEST["PersonID"] ?>'>
<?php } ?>
</form>
<script>
	function CheckValidity()
	{
		if(f1.NewPass.value=="")
		{
			alert("کلمه عبور را ثبت نکرده اید");
			return;
		}
		if(f1.NewPass.value!=f1.ConfirmPass.value)
		{
			alert("کلمه عبور جدید با تکرار آن یکی نیست");
			return;
		}
		f1.submit();
	}
</script>

<?
	HTMLEnd();
?>
