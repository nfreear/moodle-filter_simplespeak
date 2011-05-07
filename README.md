<!-- -*- markdown -*- -->

SimpleSpeak filter
==================

A Moodle filter to provide speech synthesis (text-to-speech/ TTS) services for
arbitrary text. Text such as `[Speak] Hello world! [/Speak]` is replaced with
a button with the text as a label. Press the button, and the text is spoken!

This is beneficial, for example, when teaching younger children. And for
accessibility to those with disabilities.

Requirements: tested with Moodle 1.9.7 and 2.0.2 <http://moodle.org/> (all Moodle
1.9.x and 2.0.x should work). Requires Javascript. Requires a TTS service (see below).

Uses:  jQuery (v1.3.2).

Installation
------------ 
1. Download and unzip the code files. Copy to the directory on the server,
  eg. `/var/www/moodle/filter/simplespeak/`
2. Log in to Moodle as admin, visit `Site Administration | Modules | Filters |
  Manage Filters`. Scroll down and click on the icon for Simplespeak to enable it.
3. Choose SimpleSpeak under Filters, and add a TTS service. Or set in `config.php`, eg.

        $CFG->simplespeak_service_url =
          'http://example.org/cgi-bin/espeak/getsound.pl?lang=!LANG&text=!TEXT';

  The string can contain two placeholders, `!TEXT` and optionally `!LANG` (language).
  This service can be local, or third-party - see Links below.

4. Ensure that the directory `$CFG->dataroot` is writeable by the Apache user,
  or create a writeable cache directory, `$CFG->dataroot/simplespeak/cache/`

Links
-----
* Wiki, including notes on services: <http://docs.moodle.org/en/User:Nick_Freear/SimpleSpeak_filter>
* Moodle plugin page: <http://moodle.org/mod/data/view.php?rid=4778>
* Discussion: <http://moodle.org/mod/forum/discuss.php?d=174762>
* Code, Git: <https://github.com/nfreear/moodle-filter_simplespeak>
* Code, Hg:  <https://bitbucket.org/nfreear/simplespeak>
* Demo: <http://freear.org.uk/moodle>

Usage
-----
1. Simple example. Enable the filter (admin). Then, type the following in a Moodle resource:

        [Speak] Hello world! [/Speak]
 
2. Alphabet quiz example. Type the following in the rich-editor, for example for a
question/quiz (note, line-breaks, which can be represented by <br /> are required):

        [Speak]
        ;; Just a comment.
        letter = A
        sound  = ah
        image  = "http://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Red_Delicious.jpg/240px-Red_Delicious.jpg"
        image_source = http://commons.wikimedia.org/wiki/File:Red_Delicious.jpg
        alt    = "Red delicious apple, from Bangin/Wikimedia Commons, Creative Commons Attribution-ShareAlike License"
        word   = apple
        phrase = Which of the words below start with the letter [em]a[/em]?
        [/Speak]

Notes
-----
* TTS services - see Links above.
* Roadmap/ todo: replace jQuery dependency with YUI Javascript.
* Roadmap: improve TTS language support, improve caching.
* Text strings are internationalized for Moodle 1.9.x and 2.0.x.

Credits
-------
SimpleSpeak filter. Copyright © 2010 Nicholas Freear.

* License: <http://gnu.org/copyleft/gpl.html> GNU GPL v2 or later.

jQuery JavaScript Library. Copyright © 2009 John Resig.

* License: Dual licensed under the MIT and GPL licenses.
  <http://docs.jquery.com/License>
* <http://jquery.com/>

