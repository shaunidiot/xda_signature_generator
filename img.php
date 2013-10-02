<head>
<title>XDA Signature</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/style1.css" />
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="css/styleIE.css" />
	<![endif]-->
</head>
<html>
<body>
<ul class="bmenu">
	<li><a href="index.php">Home</a></li>
	<li><a href="about.php">About</a></li>
	<li><a href="disclamer.php">Disclamer</a></li>
	</ul>
<div id="content"><br><br><br><br><br>
<center>
<?php 
include "config.php";
if (isset($_GET['id']) && trim($_GET['id']) !== '' && ctype_digit(trim($_GET['id']))) {
	$result = mysql_query("SELECT * FROM `data` WHERE `userid` =  ".mysql_real_escape_string(((int)trim($_GET['id'])))) or die(mysql_error());
	if(mysql_num_rows($result) > 0 ){
		$row = mysql_fetch_assoc($result);
		echo '<img src="data:image/jpg;base64,'.$row['imgdata'].'" />';
		echo '<br><br>Forum :  <input type="text" name="fname" onclick="javascript:select();" value="[img]http://shaunidiot.info/xda/sig.php?id='.$row['userid'].'[/img]"><br>';
	} else{
		redirect('index.php');
		exit();
	}
} else {
	redirect('index.php');
	exit();
}
?>
</center>
</body>
</html> 