<?php

include_once('vtlib/Vtiger/Module.php');
require_once('vtlib/Vtiger/Link.php');

Vtiger_Link::addLink(4,'LISTVIEWBASIC','Remove Dublicates','index.php?module=Contacts&action=MainAction&mode=clearDublicates', '',0,'');
echo 'done';

?>