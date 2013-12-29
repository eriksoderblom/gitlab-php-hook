<?php
 /**
  * git pull hook to update repositories
  * 
  * You need to update the configuration variables to fit your system.
  * 
  * Usage: http://example.com/githook.php?key=<your_key>
  * 
  * If you want to do a hard reset before pulling, you should append &ignore=1
  * to the hook url.
  * ie: http://example.com/githook.php?key=<your_key>&ignore=1
  * 
  * @author Erik S <erik.soderblom@gmail.com>
  * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License, v2
  * @version v0.1 - 2013-12-29
  */

//error_reporting(E_ALL);

/* Configuration */
$accessKey = 'githook';
$logFile   = '/path/to/githook.log';
$actionLog = '/path/to/githook_pull.log';
$repoDir   = '/path/to/git/repo';
$ip        = array('127.0.0.1', 
                   '192.168.0.100');

/* git commands */
$pull     = "git pull >> $actionLog";
$pullhard = "git reset --hard HEAD && git pull >> $actionLog";

$closing  = '-------------------------------------------------------------';

file_put_contents($logFile,'Request on '.date("F j, Y, H:i").' from '.$_SERVER['REMOTE_ADDR'].PHP_EOL, FILE_APPEND);

if ($_GET['key'] != $accessKey || !isset($_GET['key'])) {
    echo "Wrong access key!";
    file_put_contents($logFile, 'wrong key'. PHP_EOL, FILE_APPEND);
    file_put_contents($logFile, $closing.PHP_EOL, FILE_APPEND);
    exit(0);
}

if (!in_array($_SERVER['REMOTE_ADDR'],$ip)) {
    echo "Access Denied";
    file_put_contents($logFile, 'ip not permitted'. PHP_EOL, FILE_APPEND);
    file_put_contents($logFile, $closing.PHP_EOL, FILE_APPEND);
    exit(0);
}

if ($_GET['ignore'] === "1") {
    $ignore = 1;
} else {
    $ignore = 0;
}

$json   = file_get_contents('php://input');
$jsarr  = json_decode($json,true);
$branch = $jsarr["ref"];
file_put_contents($logFile, 'Branch= '.$branch. PHP_EOL, FILE_APPEND);

// Pushed to master?
if ($branch == 'refs/heads/master') {
    file_put_contents($logFile, 'Pulling from '.$branch.', check '.$actionLog.' for details.'.PHP_EOL, FILE_APPEND);
    if ($ignore) {
        chdir($repoDir);
        exec($pullhard);
    } else {
        chdir($repoDir);
        exec($pull);
    }
    file_put_contents($logFile, $closing.PHP_EOL, FILE_APPEND);
}
?>