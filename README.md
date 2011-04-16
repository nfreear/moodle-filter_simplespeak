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
  This service can be local, or third-party - see Notes below.
4. Ensure that the directory `$CFG->dataroot` is writeable by the Apache user,
  or create a writeable cache directory, `$CFG->dataroot/simplespeak/cache/`

Links
-----
* Moodle plugin page: <http://moodle.org/mod/data/view.php?d=13&rid=X>
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
        image  = http://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Red_Delicious.jpg/240px-Red_Delicious.jpg
        alt    = A red delicious apple
        word   = apple
        phrase = Which of the words below start with the letter [em]a[/em]?
        [/Speak]

Notes
-----
* Roadmap/ todo: replace jQuery dependency with YUI Javascript.
* Roadmap: improve TTS language support, improve caching.
* Text strings are internationalized for Moodle 1.9.x and 2.0.x.
* TTS service: you can install local software, eg. use eSpeak/LAME, see
      <http://code.google.com/p/webanywhere/> for ideas.
  Or it could be a third-party service, for example, Google Translate.
  Note, my reading of the Google Translate terms suggests this is probably OK - 
  ie. pressing a button is not "automated" (and we cache the sound-files locally).
  However, I accept no responsibility for this - check the terms yourself!

Credits
-------
SimpleSpeak filter. Copyright © 2010 Nicholas Freear.

* License: <http://gnu.org/copyleft/gpl.html> GNU GPL v2 or later.

jQuery JavaScript Library. Copyright © 2009 John Resig.

* License: Dual licensed under the MIT and GPL licenses.
  <http://docs.jquery.com/License>
* <http://jquery.com/>

