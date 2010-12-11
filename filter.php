<?php
/**
 * SimpleSpeak
 *
 * A Moodle filter to provide speech synthesis (TTS) services for arbitrary text.
 *
 * Uses: jQuery, (eSpeak/LAME or external service for TTS - see proxy script.)
 * @category  Moodle4-9
 * @author    N.D.Freear, April-December 2010 <nfreear @ yahoo.co.uk>
 * @copyright Copyright (c) 2010 Nicholas Freear.
 * @license   http://www.gnu.org/copyleft/gpl.html
 * @link      http://freear.org.uk/#moodle
 */
/**
  Usage:

1. Simple example. Enable the filter (admin). Then, type the following:

  [Speak] Hello world! [/Speak]
 
2. Alphabet quiz example. Type the following in the rich-editor, for example for a question/quiz (note, line-breaks, which can be represented by <br /> are required):

[Speak]
  ;; Just a comment.
  letter = A
  sound  = ah
  image  = http://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Red_Delicious.jpg/240px-Red_Delicious.jpg
  alt    = A red delicious apple
  word   = apple
  phrase = Which of the words below start with the letter [em]a[/em]?
[/Speak]

*/

//  This filter will replace any [Speak] TEXT [/Speak] with
//  a media plugin that plays that media inline
//
//////////////////////////////////////////////////////////////

require_once($CFG->libdir.'/../filter/simplespeak/simplespeaklib.php');

/// This is the filtering function itself.  It accepts the
/// courseid and the text to be filtered (in HTML form).

function simplespeak_filter($courseid, $text) {
    static $filter_count = 0;

    if (!is_string($text) || $filter_count > 3) { #0
        // non string data can not be filtered anyway
        return $text;
    }
    // Copy the input text. Fullclone is slow and not needed here
    $newtext = $text;

    $filter_count++;

    $search  = "#\[Speak\](.*?)\[\/?Speak\]#ims";
    $newtext = preg_replace_callback($search, '_simplespeak_filter_callback', $newtext);

    if (is_null($newtext) or $newtext === $text) {
        // error or not filtered
        return $text;
    }

    return $newtext;
}

function _simplespeak_filter_callback($matches_ini) {
    //global $CFG;
    static $call_count = 0;
    $call_count++;

    $defaults = array(
      'lang'  => 'en',
      'mode'  => 'inline', #(debug), button-after?
      'service'=>'wa',     #wa: webanywhere, jt: jtalkplugin.
      'phrase'=> 'Hello world!',
      'alt'   => '',
    );

    // Tidy up after WYSIWYG editors - line breaks matter.
    $speak = trim(str_ireplace(array('<br>', '<br />'), "\n", $matches_ini[1]));

    // Check if we have the simplest syntax:  [Speak] TEXT [/Speak]
    $very_simple = (''!=$speak && FALSE==strpos($speak, '='));

    if ($very_simple) {
        $speak = _simplespeak_filter_markup($speak);
        $newtext = "<div class='simplespeak tts mode-inline'><span class='phrase'>$speak</span></div>".PHP_EOL;
        return _simplespeak_filter_script($newtext, $call_count);
    }

    $speak = parse_ini_string($speak);
    $speak = (object) array_merge($defaults, $speak);

    $speak->phrase_el = $speak->letter_el = $speak->sound_el
    = $speak->word_el = $speak->image_el = '';

    // Alphabet-specific stuff.
    if (isset($speak->letter)) {
        $speak->letter= strtoupper($speak->letter);
        $speak->lower = strtolower($speak->letter);
        $speak->letter_el =
            "<h2><span class='letter'>$speak->letter</span> $speak->lower</h2>";
    }
    if (isset($speak->sound)) {
        $speak->sound_el = "<div><span class='sound'>$speak->sound</span></div>";
    }
    if (isset($speak->word)) {
        $speak->word_el = "<h3><span class='word'>$speak->word</span></h3>";
    }
    $use_oembed_js = false;
    if (isset($speak->image)) {
        # If the oEmbed filter is enabled, try getting embed code, eg. for Flickr images.
        $speak->image_el = null;
        if (function_exists('oembed_filter_try')) {
            $speak->image_el = oembed_filter_try($speak->image);
            $use_oembed_js = (bool) $speak->image_el;
        }
        if (!$speak->image_el) {
            $speak->image_el = "<img alt='$speak->alt' src='$speak->image' />";
        }
    }

    if (!empty($speak->phrase)) {
        $speak->phrase= _simplespeak_filter_markup($speak->phrase);
        $speak->phrase_el = "<p ><span class='phrase'>$speak->phrase</span></p>";
    }

    $newtext = <<<EOF

<div class="simplespeak tts mode-$speak->mode serv-$speak->service alphabet" lang="$speak->lang">
  $speak->letter_el
  $speak->sound_el
  $speak->image_el
  $speak->word_el
  $speak->phrase_el
</div>

EOF;
    return _simplespeak_filter_script($newtext, $call_count, $use_oembed_js);
}

function _simplespeak_filter_markup($text) {
    return str_ireplace(array('[em]', '[/em]', '[br/]'),
                        array('<em>', '</em>', '<br />'), $text);
}

function _simplespeak_filter_script($newtext, $call_count, $use_oembed_js=FALSE) {
    if ($call_count <= 2) {
        global $CFG;
        if (!$use_oembed_js) {
            $newtext .= <<<EOF
    <script src="{$CFG->wwwroot}/filter/simplespeak/jquery.min.js" type="text/javascript"></script>

EOF;
        }

        $newtext .= <<<EOF
    <script type="text/javascript">
    var SIMPLESPEAK_URL="{$CFG->wwwroot}/filter/simplespeak/proxy_tts/?q=";
    var SIMPLESPEAK_CSS="{$CFG->wwwroot}/filter/simplespeak/simplespeak.css";
    </script>
    <script src="{$CFG->wwwroot}/filter/simplespeak/jquery.speak.js" type="text/javascript"></script>

EOF;
    }
    return $newtext;
}


/*Example output:

<div class="simplespeak tts mode-inline serv-wa alphabet" lang="en"> 
  <h2><span class="letter">A</span> a</h2>
  <div><span class="sound">ah</span></div>
  <img alt="A red delicious apple" src='http://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Red_Delicious.jpg/240px-Red_Delicious.jpg' />
  <h3><span class="word">apple</span></h3>

  <p ><span class='phrase'>Which of the words below start with the letter <em>a</em>?</span></p>
</div>

<script src="http://my.school/moodle/filter/simplespeak/jquery.min.js"></script>
<script src="http://my.school/moodle/filter/simplespeak/jquery.speak.js"></script>
*/

#End.
