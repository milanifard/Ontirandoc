<?php
	//session_cache_limiter('public');
	include("header.inc.php");
	header('Content-Type: text/plain;charset=utf-8');
	header('Content-disposition: attachment; filename=Report.xls');
	
	$mysql = dbclass::getInstance();
	$MyStr = "";
	

	$MyStr .= '"ردیف",';
	$MyStr .= '"عنوان پروژه فارسی",';
	$MyStr .= '"عنوان پروژه انگلیسی",';
	$MyStr .= '"کلید واژگان فارسی",';
	$MyStr .= '"کلید واژگان انگلیسی",';
	$MyStr .= '"تاریخ عقد قرارداد",';
	$MyStr .= '"مبلغ کل قرارداد",';
	$MyStr .= '"مدت اجرا ",';
	$MyStr .= '"سازمان طرف قرارداد",';
	$MyStr .= '"نام ونام خانوادگی مجری 1",';
	$MyStr .= '"شماره تماس",';
	$MyStr .= '"EMail",';
	$MyStr .= '"نام ونام خانوادگی مجری 2",';
	$MyStr .= '"شماره تماس",';
	$MyStr .= '"EMail"'. "\n";
	
	
	$querywhere = $_REQUEST["test"];
	//echo " SELECT * FROM ApplicationPlan ".$querywhere;
	$res = $mysql->Execute(" SELECT a.*,p1.pfname as pfname1,p1.plname as plname1,p1.email as email1,p1.mobile_phone as mobile_phone1
							,p2.pfname as pfname2,p2.plname as plname2,p2.email as email2,p2.mobile_phone as mobile_phone2 FROM ApplicationPlan a left join persons p1 on (a.ExecutorID=p1.PersonID) left join persons p2 on (a.ExecutorID2=p2.PersonID)  where a.ParentID = 0 ");
	$i = 0;
	while($arr_res=$res->FetchRow())
	{
		$MyStr .= '"' . ($i+1) . '",';
		$MyStr .= '"' . $arr_res["PTitle"] . '",';
		$MyStr .= '"' . $arr_res["ETitle"] . '",';
		$MyStr .= '"' . $arr_res["PKeywords"] . '",';
		$MyStr .= '"' . $arr_res["EKeywords"] . '",';
		$MyStr .= '"' . shdate2($arr_res["AgreementDate"]) . '",';
		$MyStr .= '"' . $arr_res["AgreementTotalCost"] . '",';
		$MyStr .= '"' . $arr_res["ExecuteLength"] . '",';
		$MyStr .= '"' . $arr_res["ContractUnit"] . '",';		
		$MyStr .= '"' . $arr_res["pfname1"].' '.$arr_res["plname1"] . '",';
		$MyStr .= '"' . $arr_res["mobile_phone1"] . '",';
		$MyStr .= '"' . $arr_res["email1"] . '",';		
		$MyStr .= '"' . $arr_res["pfname2"].' '.$arr_res["plname2"] . '",';
		$MyStr .= '"' . $arr_res["mobile_phone2"] . '",';
		$MyStr .= '"' . $arr_res["email2"] . '"';		
		$MyStr .= "\n";
		$i++;
	}
	print($MyStr);
	//echo  "1";
	exit;
?>