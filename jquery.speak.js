/** SimpleSpeak, Copyright N.D.Freear, License GPL.
*/
$(document).ready(function() {

    $(".tts.mode-inline .phrase").each(function(index) {
      var phrase = $(this).html();
      $(this).replaceWith('<button class="this phrase" title="Say this" >'+phrase+'</button>');
    });

    // Convert 'letter' etc. to buttons.
    $(".tts.mode-inline.alphabet .letter").each(function(index) {
      var letter = $(this).text();
      $(this).replaceWith('<button class="this letter" title="Say the letter" ><span>Say the letter; </span> '+letter+'</button>');
    });
    $(".tts.mode-inline.alphabet .sound").each(function(index) {
      var sound = $(this).text();
      $(this).replaceWith('<button class="this sound" title="Say the sound" ><span>Say the sound; </span>'+sound+'</button>');
    });
    $(".tts.mode-inline.alphabet .word").each(function(index) {
      var word = $(this).html(); //<em>
      $(this).replaceWith('<button class="this word" title="Say the word" ><span>Say the word; </span>'+word+'</button>');
    });

    //<audio> ??
    $(".tts").append('<iframe id="tts-frame" title="Player" src="" width="2" height="2"></iframe>');

    var tts_play = function(text, lang) {
      if(!lang)lang = $("html[lang]") ? $("html").attr('lang') : 'en';
      
      $("#tts-frame").attr('src', SIMPLESPEAK_URL+escape(text));
      return false;
    }

    $(".tts button.this").click(function() {
      return tts_play($(this).text());
    });

    $('head').append(
    '<link rel="stylesheet" type="text/css" href="'+SIMPLESPEAK_CSS+'"/>');
});

