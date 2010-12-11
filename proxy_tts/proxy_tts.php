<?php
/**
 * A simple text-to-speech proxy (Moodle 1.9, probably 2.0).
 *
 * Use a local TTS engine, like espeak - see the WebAnywhere project,
 *     http://code.google.com/p/webanywhere/
 * Or use a 3rd-party service, like Google Translate.
 *
 * @author    N.D.Freear, 10 Dec 2010.
 * @copyright Copyright (c) 2010 Nicholas Freear.
 * @license   http://www.gnu.org/copyleft/gpl.html
 */
require_once '../../../config.php';
require_once '../../../lib/filelib.php';
global $CFG;

$cache_dir= 'simplespeak/cache';
$base_url = 'http://localhost/cgi-bin/espeak/getsound.pl?lang=!LANG&text=!TEXT';
#$base_url = 'http://localhost/espeak_tts?lang=!LANG|f1&text=!TEXT';
if (isset($CFG->simplespeak_service_url)
    && parse_url($CFG->simplespeak_service_url)) {
    $base_url = $CFG->simplespeak_service_url;
}
#$user_agent='Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322)';
#http://zytrax.com/tech/web/msie-history.html

# Basic security (and/or course.id + referrer?)
require_login();


#$course ID ??
$text = required_param('q', PARAM_RAW);
$lang = optional_param('lang', str_replace('_utf8', '', $CFG->lang), PARAM_RAW);

// Strip white-space and some characters - not '?' - inflection?
$text = trim($text, "\t\n\r\0\x0B .!\"'");


if (''==$text) {
	error('* A required parameter (text) was empty *');
}
#else {
#	error('Parameter (text) is too long!');
#}

$md5 = md5($text);
$data_dir = "$CFG->dataroot/simplespeak/cache/";
$data_file= $data_dir ."$md5.mp3";

function _ss_mkdir_safe($base, $path, $perm='0664') {
  $parts = explode('/', trim($path, '/'));
  $dir = $base;
  $success = true;
  foreach ($parts as $p) {
	$dir .= "/$p";
	if (is_dir($dir)) { break;
	} elseif (file_exists($dir)) {
	  error("File exists '$p'.");
	}
	$success = mkdir($dir, $perm);
  }
  return $success;
}

$resp = NULL;
if (!file_exists($data_file)) {

	if (!_ss_mkdir_safe($CFG->dataroot, $cache_dir)) {
	    error("Error creating directory '%DATA/$cache_dir'.");
	}

    $headers = null;
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
      $headers['Accept-Language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }

	$url = strtr($base_url, array('!TEXT'=>urlencode($text),
	                              '!LANG'=>urlencode($lang)));

	$resp = download_file_content($url, $headers, NULL, TRUE);

	$bytes = strlen($resp->results);
	if (200 != $resp->status || $bytes <= 0) {
		var_dump($resp->status, $bytes, $url, $resp->headers);
		error("What's wrong?");
	}

	#if headers contains 'Sound-length' @headers('Sound-length: ..');

	$bytes = file_put_contents($data_file, $resp->results);
}
#var_dump($CFG->dataroot, $url, $lang, $md5, $resp->headers);
send_file($data_file, "$md5.mp3");


/*string(40) "/Users/Nick/workspace/moodle/_moodledata"
string(79) "http://webanywhere.cs.washington.edu/cgi-bin/ivona/getsound.pl?text=Hello+world"
string(0) ""
string(32) "3e25960a79dbc69b674cd4ec67a72c62"
array(7) {
  [0]=>
  string(15) "HTTP/1.1 200 OK"
  [1]=>
  string(35) "Date: Fri, 10 Dec 2010 22:44:47 GMT"
  [2]=>
  string(30) "Server: Apache/2.2.17 (Fedora)"
  [3]=>
  string(30) "Sound-length: 757.551020408163"
  [4]=>
  string(38) "Expires: Tue, 12 Mar 2012 04:00:25 GMT"
  [5]=>
  string(20) "Content-Length: 5908"
  [6]=>
  string(24) "Content-Type: audio/mpeg"
}*/

