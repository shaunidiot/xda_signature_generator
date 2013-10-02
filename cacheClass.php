<?php 
set_time_limit(0);
include 'config.php';
class SimpleCache {

	//Path to cache folder (with trailing /)
	var $cache_path = '';
	//Length of time to cache a file in seconds
	var $cache_time = 43200; // half a day
	
	function stribet($inputstr, $delimiterLeft, $delimiterRight) {
		$posLeft = stripos($inputstr, $delimiterLeft) + strlen($delimiterLeft);
		$posRight = stripos($inputstr, $delimiterRight, $posLeft);
		return substr($inputstr, $posLeft, $posRight - $posLeft);
	}
	
	
	//This is just a functionality wrapper function
	function get_data($label, $url) {
		if($data = $this->get_cache($label)){
			return $data;
		} else {
			$data = $this->do_curl($url);
			$this->set_cache($label, $data);
			return $data;
		}
	}
	
	function setCache($label, $data){
		file_put_contents($this->cache_path . $this->safe_filename($label) .'.cache', $data);
	}
	
	function getCache($label){
		if($this->is_cached($label)){
			$filename = $this->cache_path . $this->safe_filename($label) .'.cache';
			return file_get_contents($filename);
		}
		return false;
	}
	
	function overwriteCache($label) {
		$filename = $this->cache_path . $this->safe_filename($label) .'.cache';
		if(file_exists($filename) && (filemtime($filename) + $this->cache_time >= time())) return true;
		return false;
	}
	
	//Helper function for retrieving data from url
	function do_curl($url) {
		if(function_exists("curl_init")){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		} else {
			return file_get_contents($url);
		}
	}
	
	//Helper function to validate filenames
	function safe_filename($filename) {
		return preg_replace('/[^0-9a-z\.\_\-]/i','', strtolower($filename));
	}
	
	function getThanks($content){
		if (preg_match("/Number of Thanks:<\/span>([^'\"]*)<\/li>/", $content, $val)) {
			return trim($val[1]);
		}
		return null;
	}

	function getAvatar($content){
		if (preg_match('/<img src="([^\"]*)" alt="[^\"]*"  width="60" height="[^\"]*" class="alt2"/', $content, $val)) {
			return trim('http://forum.xda-developers.com/' . $val[1]);
		}
		return null;
	}
	function getUsername($content){
		if (preg_match("/<title>xda-developers - View Profile: ([^\"]*)<\/title>/", $content, $val)) {
			return trim($val[1]);
		}
		return null;
	}
	
	function getRank($content){
		$content = preg_replace('!\s+!', ' ', $content);
		if (preg_match("/<div class=\"nametitle\"> <h1>[^#]*?<\/h1> <h2>([^\"]*?)<\/h2> <\/div>/", $content, $val)) {
			return trim($val[1]);
		}
		return null;
	}
	
	function getLastActivity($content){
		if (preg_match("/<span class=\"shade\">Last Activity:<\/span>([^\"]*)<span class=\"time\">([^\"]*)<\/span>/", $content, $val)) {
			return trim($val[1] . ' - ' .  $val[2]);
		}
		return null;
	}
	
	function getTotalPost($content){
		if (preg_match("/<li><span class=\"shade\">Total Posts:<\/span>([^\"]*)<\/li>/", $content, $val)) {
			return trim($val[1]);
		}
		return null;
	}
	function getFriends($content){
		if (preg_match("/<div class=\"friends_counter\">Showing [^\"]* of ([^\"]*) Friend\(s\)<\/div>/", $content, $val)) {
			return trim((int)$val[1]);
		}
		return null;
	}
}

?>
