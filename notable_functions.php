<?php
/* $Id: notable_functions.php 295096 2010-09-30 05:06:00Z sgrayban $ */

if (!function_exists('blogbling_version_check')) {

function blogbling_version_check ($plugin_name='',$last_check_time,$interval=7) {
	/*
	 * Version Check code.
	 * This is a bit verbose. I'd like to cut it down a bit. I know this works
	 * in almost all cases. Some places may not have curl installed. I could do
	 * it in AJAX calling the plugin itself with a parameter and firing off a
	 * call but that's basically the same as what I'm doing here. So why bother.
	 */
	$interval        = intval($interval);
	$current_version = -1;
	if (($last_check_time+(86400*$interval))<mktime()) {

		$current_version = blogbling_url_get('http://blog.calevans.com/plugin_version_checker.php?plugin='.$plugin_name);
	} else {
		$current_version = -1;
	}// if (($notable_settings['last_version_check']+(86400*7))<mktime())
return $current_version;
} // function blogbling_version_check ($plugin_name)


// depricated. Code moved back into wp-alexadex because it's too specific.
function blogbling_url_get($url) {
	// user send_to_host whenever possible, it's better.
	$return_value = '';
	$elements = parse_url($url);
	if ($fp = @fsockopen($elements['host'],80)) {
		fputs($fp, sprintf("GET %s HTTP/1.0\r\n" . "Host: %s\r\n\r\n", $elements['path'] . (isset ($elements['query']) ? '?'. $elements['query'] : ''), $elements['host']));
		while (!feof($fp)) $line .= fgets($fp, 4096);
		fclose($fp);
		$line       = urldecode(trim(strtr($line,"\n\r\t\0","    ")));
		$work_array = explode("  ",$line);
		/*
		 * This does not allow for any additional messages to be passed. It
		 * assumes that the last time coming in is the version #.
		 */
		$return_value = $work_array[count($work_array)-1];
	} // if ($fp)
return $return_value;
} // function blogbling_url_fetch($url)


function blogbling_get_version($file_name) {
	if (!empty($file_name)) {
		/*
		 * Ripped out of WordPress Not sure if I'll ever use any more of this but I'll
		 * leave them in just for fun.
		 */
		$plugin_data = implode('', file($file_name));
		if (preg_match("|Version:(.*)|i", $plugin_data, $version)) {
				$version = $version[1];
		} // if (preg_match("|Version:(.*)|i", $plugin_data, $version))
	} // if (!empty($filename))
	return $version;
} // function blogbling_get_version($file_name)


function blogbling_send_to_host($host,$method,$path,$data,$return_full_headers=false)
{

    $method = empty($method)?'GET':strtoupper($method);
    if ($method == 'GET') $path .= '?' . $data;
    $output  = '';
    $output .= $method." ".$path." HTTP/1.1\r\n";
    $output .= "Host: ".$host."\r\n";
    $output .= "Content-type: application/x-www-form-urlencoded\r\n";
    $output .= "Content-length: ".strlen($data)."\r\n";
    $output .= "Connection: close\r\n\r\n";
    $output .= ($method=='POST'?$data:'')."\n";

    $fp = fsockopen($host, 80);
	$switch = false;
	fputs($fp,$output);
	/*
	 * Change this to store headers in one array and lines in another. If
	 * return full headers is true then concat before returning.
	 */
	while (!feof($fp)) {
		$line = strtr(fgets($fp,128),array("\n"=>"","\r"=>""));
		if (!$switch and empty($line)) {
			$switch = (!$switch);
		} else if ($switch) {
			$buf .= $line;
		} // if (!$switch and empty($line))
	} // while (!feof($fp))
    fclose($fp);
    return $buf;
}	// function blogbling_sendToHost($host,$method,$path,$data,$useragent=0)


function blogbling_fetch_options($prefix='blogbling') {
	$output = array();
   	$output=get_option($prefix.'_options');
	return $output;
} // function blogbling_fetch_options($prefix='blogbling')


function blogbling_post_options($prefix='blogbling',$source_array) {
	update_option($prefix.'_options',$source_array);
	return;
}


} // if (!function_exists('blogbling_version_check'))
?>