<?php
/**
 * A Flash-HTML5 MP3 player for Simple speak.
 *
 * @author    N.D.Freear, April 2011.
 * @copyright (c) 2010 Nicholas Freear.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
require_once '../../../config.php';

global $CFG;

require_login();

$text    = required_param('q', PARAM_TEXT);
$autoplay= (bool) optional_param('autoplay', 1, PARAM_INT);
//$lang, $cache?

$proxy_url = "$CFG->wwwroot/filter/simplespeak/proxy_tts/?q=".urlencode($text);

// Prefer the lightweight, legacy MP3 player.
$dir_mp3 = "$CFG->dirroot/lib/mp3player/mp3player.swf";
$flash_mp3 = "$CFG->wwwroot/lib/mp3player/mp3player.swf";
$autoplay_mp3 = $autoplay ? 'yes' : 'no';
$dir_flow  = "CFG->dirroot/lib/flowplayer/flowplayer-3.2.7.swf";
$flash_base= "$CFG->wwwroot/lib/flowplayer"; //flowplayer-3.2.7.swf;

//HTML5 audio.
$autoplay_html5 = $autoplay ?' autoplay ':'';
$html5_fallback =<<<EOF
<audio controls $autoplay_html5>
 <source src="$proxy_url" type="audio/mpeg"/>
 [ Requires Flash or the HTML5 audio element. ]
</audio>
EOF;

//Flowplayer: there's no explicit '.mp3' extension, so specify the audio plugin.
$flowplayer_config = array(
  'clip' => array(
    'url'=> $proxy_url,
    'autoPlay' => $autoplay,
    'provider' => 'audio',
  ),
  'plugins' => array(
    'audio' => array(
      'url' => "$flash_base/flowplayer.audio-3.2.2.swf",
    ),
  ),
);
$json = json_encode($flowplayer_config);
?>
<!DOCTYPE html><html ><meta charset="utf-8"/><title><?php echo $text ?> [MP3 player]</title>
<style>body{margin:0} object,audio{display:block; width:300px;height:40px;}</style>
<?php if (file_exists($dir_mp3)): ?>
<object
 id="fss_flash_api"
 type="application/x-shockwave-flash"
 data="<?php echo $flash_mp3; ?>">
 <param name="movie" value="<?php echo $flash_mp3; ?>" />
 <param name="quality" value="high"/>
 <param name="bgcolor" value="#555555"/>
 <param name="flashvars" value=
 "src=<?php echo $proxy_url; ?>&amp;autoPlay=<? echo $autoplay_mp3 ?>" />
<?php echo $html5_fallback; ?>
</object>
<?php else: ?>
<object
 id="fss_flash_api"
 type="application/x-shockwave-flash"
 data="<?php echo $flash_base; ?>/flowplayer-3.2.7.swf"
<?php /*width="300" height="50" x-width="100%" x-height="100%" */ ?>>
 <param name="movie" value="<?php echo $flash_base; ?>/flowplayer-3.2.7.swf" />
 <param name="quality" value="high"/>
 <param name="bgcolor" value="#000000"/>
 <param name="flashvars" value=
 'config=<?php echo $json; ?>' />
<?php echo $html5_fallback; ?>
</object>
<?php endif; ?>
</html>
