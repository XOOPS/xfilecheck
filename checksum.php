<?php
/**
* XOOPS installation md5 checksumminig script
*
* This script allows you to check that the XOOPS system files have been correctly uploaded.
* It reads all the XOOPS files and reports missing or invalid ones.
*
* Instructions:
* - Upload this script and xoops.md5 to your XOOPS documents root
* - Access it using a browser
* - Re-upload missing/invalid files
*
* @copyright    The XOOPS Project http://xoops.sf.net/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @author       Skalpa Keo <skalpa@xoops.org>
* @author       phppp <phppp@users.sourceforge.net>
* @since        2.0.14
* @version      $Id $
* @package      xoops
*/

error_reporting(0);

header("Content-type: text/plain");

$md5_file = __DIR__ . '/checksum.md5';
$root = (is_dir('./htdocs') ? './htdocs' : '.');
if (isset($_GET['root']) && false === strpos($_GET['root'], '..')) {
    $root = $_GET['root'];
}
$num_files = check_folder($root);

echo "There are {$num_files} files checked.\n";
echo "Please remove the file $md5_file and ".basename(__FILE__)." as soon as possible.\n";

/**
 * Check the file checksum as specified in $line
 *
 * @param string $line line from .md5 file, i.e. class/criteria.php:fce4eb8f6db4b6b698cb2a9947430e56
 * @param string $path path
 *
 * @return void
 */
function check_file($line, $path = ".")
{
    list($file, $sum) = explode(":", $line, 2);
    if (substr($file, 0, 7) == 'htdocs/') {
        $file = substr($file, 7);
    } else {
        $file = $path . '/' . $file;
    }
    if (!file_exists($file)) {
        echo "{$file} missing !\n";
    } else {
        $txt = file_get_contents($file);
        $txt = str_replace(array( "\r\n", "\r" ), "\n", $txt);
        if (md5($txt) != $sum) {
            echo "{$file} content invalid\n";
        }
    }
}

/**
 * Check the files in $path against .md5 checksums in $md5_file
 *
 * @param string $path      directory to check
 *
 * @return bool|int
 */
function check_folder($path = '.')
{
    global $md5_file;
    $num_files = 0;
    if (!is_file($md5_file) || !is_readable($md5_file)) {
        echo "$md5_file file not found.\n";
        return false;
    }
    $sums = explode("\n", rtrim(file_get_contents($md5_file)));
    foreach ($sums as $line) {
        check_file($line, $path);
        $num_files ++;
    }

    return $num_files;
}
