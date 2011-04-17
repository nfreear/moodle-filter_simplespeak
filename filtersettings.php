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
    //WAS: 'proxy_tts'
    $url = "$CFG->wwwroot/filter/simplespeak/player/?autoplay=0&amp;cache=1&amp;q=Hello+world";
    $test = <<<EOF
    <p><iframe src="$url" height=40 width=300 frameborder=0 title="Player"></iframe>
    </p>
EOF;
    //&nbsp; <button type=button style="position:relative;top:-5px">Test: Hello world</button>
}

$settings->add(new admin_setting_configtext(
  'filter_simplespeak_service_url',
  get_string('serviceurl', 'filter_simplespeak'),
  get_string('serviceurlhelp', 'filter_simplespeak'). $test,
  'http://' .$_SERVER['HTTP_HOST']. SIMPLESPEAK_DEFAULT_SERVICE));


#End.