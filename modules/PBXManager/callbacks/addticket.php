<?php
/*+**********************************************************************************
 Скрипт создания заявки
 параметры
 customernumber
 incominglinename
 starttime
 duration
************************************************************************************/

exit();

chdir(dirname(__FILE__) . '/../../../');
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once 'includes/http/Request.php';


$request = new Vtiger_Request($_REQUEST);

$required = [
    'customernumber',
    'incominglinename',
    'starttime',
    'duration',
];

$data = [];
foreach ($required as $field) {
    $v = $request->get($field);
    if (empty($v)) {
        // interrupt?
    }
    $data[$field] = $v;
}

$pbxClass = 'PBXManager_PBXManager_Connector';
if (!class_exists($pbxClass)) {
    include_once 'modules/PBXManager/connectors/PBXManager.php';
}

$connector = new $pbxClass;

$crmTicket = $connector->newAgiTicket($data);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
echo json_encode($crmTicket);

/**
 * Validates callback request params
 * 
 * @param string $vtigersecretkey
 * @param Vtiger_Request $request
 * @return boolean
 */
function validateRequest($vtigersecretkey, $request) {
    return ($vtigersecretkey == $request->get('vtigersignature')
        && $request->get('callerNumber') != null);
}
