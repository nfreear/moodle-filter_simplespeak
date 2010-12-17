<?php
/**
 * SimpleSpeak filter settings.
 *
 * @copyright (c) 2010 Nicholas Freear {@link http://freear.org.uk}.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
require_once($CFG->dirroot.'/filter/simplespeak/simplespeaklib.php');


$test = NULL;
if (isset($CFG->filter_simplespeak_service_url)) {
    // The test code needs improving. Note, ?cache=0
    $url = "$CFG->wwwroot/filter/simplespeak/proxy_tts/?cache=1&amp;q=Hello+world";
    $test = <<<EOF
    <p><iframe src="$url" height=18 width=200 frameborder=0 title=Player></iframe>
    &nbsp; <button type=button style="position:relative;top:-5px">Test: Hello world</button></p>
EOF;
}

$settings->add(new admin_setting_configtext(
  'filter_simplespeak_service_url',
  get_string('serviceurl', 'filter_simplespeak'),
  get_string('serviceurlhelp', 'filter_simplespeak'). $test,
  'http://' .$_SERVER['HTTP_HOST']. SIMPLESPEAK_DEFAULT_SERVICE));


#End.