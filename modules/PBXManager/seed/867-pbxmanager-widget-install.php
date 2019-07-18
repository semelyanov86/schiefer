<pre><?php
//PINstudio begin @vladimir.ch red-867
chdir('../../');
require_once 'include/utils/utils.php';
require 'include/events/include.inc';
include_once 'includes/main/WebUI.php';

$PBXManagerInstance = CRMEntity::getInstance('PBXManager');
$PBXManagerInstance->addWidget();

echo 'done' . PHP_EOL;
//PINstudio end