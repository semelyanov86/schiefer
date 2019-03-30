<?php
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/utils/utils.php';
require_once 'includes/Loader.php';
vimport('~~vtlib/Vtiger/Cron.php');
Vtiger_Cron::register('AutoImport', 'cron/modules/Contacts/importContacts.service', 86400, 'Contacts', 1, 11, 'Рекомендуемая частота обновления - 24 часа.');
echo 'done';