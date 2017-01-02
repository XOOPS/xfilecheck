<?php
/**
 * XOOPS installation md5 checksum file creation script
 * For developers and supporters
 *
 * This script creates the checksum.md5 file that allows you to check the integrity of XOOPS system files
 *
 * Invocation from command line:
 *  php checksum.create.php --root=/path/to/XoopsCore25/htdocs
 *
 * Or, from browser:
 *  http://site/url/path/checksum.create.php?root=/path/to/XoopsCore25/htdocs
 *
 * @copyright 2000-2016 XOOPS Project (www.xoops.org)
 * @license   GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author    Skalpa Keo <skalpa@xoops.org>
 * @author    phppp <phppp@user.sourceforge.net>
 * @package   xfilecheck
 */

error_reporting(0);

header("Content-type: text/plain");

$md5_file = __DIR__ . '/checksum.md5';
$root = (is_dir('./htdocs') ? './htdocs' : '.');
$skip = array(
    'words'     => array(),
    'pattern'   => '',
    );

if (php_sapi_name() === 'cli') {
    $options = getopt('', array('root::'));
} else {
    $options = $_GET;
}

if (isset($options['root']) && false === strpos($options['root'], '..')) {
    $root = $options['root'];
} else {
    $skip_words = array(
        'cache', 'extras', 'templates_c', /*'uploads', 'themes',*/
        'mainfile.php', 'upgrade', /*'checksum.php', 'checksum.md5', */basename(__FILE__),
        //'xoops_data', 'xoops_lib'
    );
    array_walk($skip_words, create_function('&$v,$k', '$v = \''.$root.'/\'.$v;'));
    $skip = array(
        'words'     => $skip_words,
        'pattern'   => '/^' . preg_quote("{$root}/checksum", '/') . '\.([^\.]*\.)?(md5|php)$/'
        );
}

if (!$output = fopen($md5_file, 'wb')) {
    exit(10);
}


$num_files = sum_folder($output, $root, '');

fclose($output);

echo "\nThere are {$num_files} files registered";

/**
 * Generate checksums for a directory
 *
 * @param resource $fp   file pointer
 * @param string   $root root directory
 * @param string   $path path
 *
 * @return int number of files
 */
function sum_folder($fp, $root, $path = '')
{
    global $skip;
    $current_path = $root . (empty($path) ? '' : "/$path");
    $content = scandir($current_path);

    $num_files = 0;
    $subfolders = array();
    foreach ($content as $file) {
        if (substr($file, 0, 1) == '.'
            || (!empty($skip['words']) && in_array("$current_path/$file", $skip['words']))
            || (!empty($skip['pattern']) && preg_match($skip['pattern'], "$current_path/$file"))
            ) {
            if ($file !== '.' && $file !== '..') {
                echo "\nskipped: $current_path/$file";
            }
            continue;
        }
        $current_file = (empty($path) ? "" : "$path/") . $file;
        if (is_dir("$current_path/$file")) {
            $subfolders[] = $current_file;
        } else {
            $txt = file_get_contents("$current_path/$file");
            $txt = str_replace(array( "\r\n", "\r" ), "\n", $txt);
            fwrite($fp, "$current_file:" . md5($txt) . "\n");
            $num_files ++;
        }
    }
    if (!empty($subfolders)) {
        foreach ($subfolders as $subfolder) {
            $num_files += sum_folder($fp, $root, $subfolder);
        }
    }

    return $num_files;
}
