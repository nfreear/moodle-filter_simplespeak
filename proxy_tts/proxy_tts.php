<?php
/**
 * A simple text-to-speech proxy (see README).
 *
 * @author    N.D.Freear, December 2010.
 * @copyright (c) 2010 Nicholas Freear {@link http://freear.org.uk}.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
require_once '../../../config.php';

global $CFG;
require_once $CFG->libdir .'/filelib.php';
require_once $CFG->dirroot.'/filter/simplespeak/simplespeaklib.php';

$cache_dir= 'simplespeak/cache';
$base_url = 'http://' .$_SERVER['HTTP_HOST']. SIMPLESPEAK_DEFAULT_SERVICE; #$CFG->wwwroot
if (isset($CFG->filter_simplespeak_service_url)
    && parse_url($CFG->filter_simplespeak_service_url)) {
    $base_url = $CFG->filter_simplespeak_service_url;
}

// Basic security (and/or course.id + referrer?)
require_login();


#$course ID ??
$text = required_param('q', PARAM_RAW);
$lang = optional_param('lang', str_replace('_utf8', '', $CFG->lang), PARAM_RAW);
// Only allow '?cache=0' if you're logged in.
$cache= optional_param('cache', 1, PARAM_INT);

// Strip white-space and some characters - not '?' - inflection?
$text = trim($text, "\t\n\r\0\x0B .!\"'");


if (''==$text) {
	error('* A required parameter (text) was empty *');
}
#else {
#	error('Parameter (text) is too long!');
#}

$md5 = md5($text);
$host= parse_url($base_url, PHP_URL_HOST);
$host_tr = str_replace(array('www.', '.'), '', $host);
$cache_dir .= '/'.substr($host_tr, 0, 4).'/'.$md5[0];
$data_file= "$CFG->dataroot/$cache_dir/$md5.mp3";

header("X-FSS-File: $cache_dir/$md5.mp3");
header("X-FSS-Text: $text");

$resp = NULL;
if (!$cache || !file_exists($data_file)) {

	if (!_ss_mkdir_safe($CFG->dataroot, $cache_dir)) {
	    error("Error creating directory '%DATA/$cache_dir'.");
	}

    // Pass some browser headers including 'Accept-Language' on to the TTS service.
    $headers = null;
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
      $headers['Accept-Language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
    }

	$url = strtr($base_url, array('!TEXT'=>urlencode($text),
	                              '!LANG'=>urlencode($lang)));

	$resp = download_file_content($url, $headers, NULL, TRUE);

	$bytes = strlen($resp->results);
	if (200 != $resp->status || $bytes <= 0) {
		#var_dump($bytes, $url, $resp->headers);
		error("Error, TTS service failure. HTTP status: $resp->status.");
	}

	#if headers contains 'Sound-length' @headers('Sound-length: ..');

	$bytes = file_put_contents($data_file, $resp->results);
}
send_file($data_file, "$md5.mp3");

