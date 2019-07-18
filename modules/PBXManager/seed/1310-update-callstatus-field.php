<?php
/**
 * Created by PINstudio
 * Task 1310
 * Date: 19.01.2018
 */

chdir('../../');
require_once 'config.inc.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'include/database/PearDatabase.php';


$adb = PearDatabase::getInstance();

$sql = "UPDATE `vtiger_field` SET `uitype` = ? WHERE `columnname` = ? AND `tablename` = ?";
$adb->pquery($sql, array(16,'callstatus','vtiger_pbxmanager'));

$sql = "CREATE TABLE `vtiger_callstatus` (
          `callstatusid` int(19) NOT NULL AUTO_INCREMENT,
          `callstatus` varchar(200) NOT NULL DEFAULT '',
          `presence` int(1) NULL DEFAULT '1',
          `picklist_valueid` int(19) NULL DEFAULT '0',
          `sortorderid` int(2) NULL DEFAULT '0',
          PRIMARY KEY (`callstatusid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$adb->query($sql);

$sql = "CREATE TABLE `vtiger_callstatus_seq` (
          `id` int(11) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$adb->query($sql);

$sql = "INSERT INTO `vtiger_callstatus` (`callstatusid`, `callstatus`) VALUES
        (1,'busy'),
        (2,'ringing'),
        (3,'Unknown'),
        (4,'no-response'),
        (5,'in-progress'),
        (6,'CANCEL'),
        (7,'completed')";
$adb->query($sql);

$sql = "INSERT INTO `vtiger_callstatus_seq` (`id`) VALUES (7)";
$adb->query($sql);
