<?php 
set_time_limit(0);
ini_set("display_errors", "1");
error_reporting(E_ALL); 


mysql_connect('localhost', 'root', ''); mysql_select_db('xdasig');

function keygen($amount){
	$keyset = "abcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$randkey = "";
	for ($i=0; $i<$amount; $i++)
	$randkey .= substr($keyset, rand(0, strlen($keyset)-1), 1);
	return $randkey;
}

function redirect($url){
	echo '<script type="text/javascript">
<!--
window.location = "'.$url.'"
//-->
</script>';
}