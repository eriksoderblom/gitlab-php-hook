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
$accessKey = '_hook';
$logBase   = '/var/www/html/hooks/logs/';
$logFile   = $logBase.'hook.log';
$actionLog = $logBase.'hook_action.log';
$repoDir   = '/var/www/html/tvrss';
$ip        = ['127.0.0.1',
              '192.168.1.202',
              '192.168.1.1'];
$branches  = ['refs/heads/master'];

$getKey = filter_input(INPUT_GET, 'key', FILTER_SANITIZE_ENCODED);
$getIgn = filter_input(INPUT_GET, 'ignore', FILTER_SANITIZE_ENCODED);
$remote = filter_input(INPUT_SERVER, 'REMOTE_ADDR');

/* git commands */
$pull     = "git pull >> $actionLog";
$pullhard = "git reset --hard HEAD && git pull >> $actionLog";

$closing  = '-------------------------------------------------------------';

file_put_contents($logFile, 'Request on '.date("F j, Y, H:i").' from '
                  .$_SERVER['REMOTE_ADDR'].PHP_EOL, FILE_APPEND);

if ($getKey != $accessKey) {
    echo "Wrong access key!";
    file_put_contents($logFile, 'wrong key'. PHP_EOL, FILE_APPEND);
    file_put_contents($logFile, $closing.PHP_EOL, FILE_APPEND);
    exit(0);
}

if (!in_array($remote, $ip)) {
    echo "Access Denied";
    file_put_contents($logFile, 'ip not permitted'. PHP_EOL, FILE_APPEND);
    file_put_contents($logFile, $closing.PHP_EOL, FILE_APPEND);
    exit(0);
}

$json   = file_get_contents('php://input');
$jsarr  = json_decode($json,true);
$branch = $jsarr["ref"];
file_put_contents($logFile, 'Branch= '.$branch. PHP_EOL, FILE_APPEND);
//print_r($jsarr);

// Pushed to master?
if (in_array($branch, $branches)) {
    file_put_contents($logFile, 'Pulling from '.$branch.', check '.$actionLog.' for details.'.PHP_EOL, FILE_APPEND);
    if ($getIgn === "1") {
        chdir($repoDir);
        exec($pullhard);
    } else {
        chdir($repoDir);
        exec($pull);
    }
    file_put_contents($logFile, $closing.PHP_EOL, FILE_APPEND);
}
?>