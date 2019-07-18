<?php
chdir(dirname(__FILE__) . '/../../../');
include_once 'includes/main/WebUI.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
echo json_encode(PBXManager_Server_Model::statsRequest());
