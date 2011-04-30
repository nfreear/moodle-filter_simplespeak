<?php
/** SimpleSpeak - utility functions.
 *
 * @copyright Copyright (c) 2010 Nicholas Freear.
 * @license   http://www.gnu.org/copyleft/gpl.html
 */

define('SIMPLESPEAK_DEFAULT_SERVICE',
       '/cgi-bin/espeak/getsound.pl?lang=!LANG&text=!TEXT');


/**Safely, recursively create directories.
*/
function _ss_mkdir_safe($base, $path, $perm=0775) { #0777, Or 0664.
  $parts = explode('/', trim($path, '/'));
  $dir = $base;
  $success = true;
  foreach ($parts as $p) {
	$dir .= "/$p";
	if (is_dir($dir)) {
	  //break;
	} elseif (file_exists($dir)) {
	  //error("File exists '$p'.");
	} else {
	  $success = mkdir($dir, $perm);
	}
  }
  return $success;
}

/** Depending on the PHP version, define the parse_ini_string function.
 *  Source: a comment in the PHP manual,
 * http://php.net/manual/en/function.parse-ini-string.php#97621
 */
# Define parse_ini_string if it doesn't exist.
# Does accept lines starting with ; as comments
# Does not accept comments after values
if( !function_exists('parse_ini_string') ){
    function parse_ini_string( $string ) {
        $array = Array();

        $lines = explode("\n", $string );
        
        foreach( $lines as $line ) {
            $statement = preg_match(
// Allow white-space.
// Was: "/^(?!;)(?P<key>[\w+\.\-]+?)\s*=\s*(?P<value>.+?)\s*$/"
"/^(?!;)\s*?(?P<key>[\w+\.\-]+?)\s*=\s*(?P<value>.+?)\s*$/", $line, $match );

            if( $statement ) {
                $key    = $match[ 'key' ];
                $value    = $match[ 'value' ];

                # Remove quote
                if( preg_match( "/^\".*\"$/", $value ) || preg_match( "/^'.*'$/", $value ) ) {
                    $value = mb_substr( $value, 1, mb_strlen( $value ) - 2 );
                }
                
                $array[ $key ] = $value;
            }
        }
        return $array;
    }
}

