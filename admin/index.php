<ol>
<?php 
set_time_limit(0);
require('../config.php'); 
if (isset($_GET['del']) && trim($_GET['del']) !== '' && ctype_digit(trim($_GET['del']))) {
	mysql_query("DELETE FROM data WHERE id='".trim($_GET['del'])."'");
}
$highest_id = mysql_result(mysql_query("SELECT MAX(id) FROM data"), 0);
for ($i = $highest_id; $i > 0; $i--) {
	$result = mysql_query("SELECT * FROM `data` WHERE `id` =  ".mysql_real_escape_string(((int)trim($i)))) or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		echo'<li>'.$row['username'].' - <a href="qazwsxedc.php?del='.$row['id'].'"/>Delete</a> - <a href="sig.php?id='.$row['userid'].'"/>View</a></li>';
	}
}

?>
</ol>