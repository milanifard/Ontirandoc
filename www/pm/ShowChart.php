<?php
	$content = file_get_contents("/mystorage/pmdocuments/charts/".$_REQUEST["id"].".png");
	header('Content-disposition: filename="photo"');
	header('Content-type: image/jpeg');
	//header('Pragma: no-cache');
	//header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Transfer-Encoding: binary");
	echo $content;
?>