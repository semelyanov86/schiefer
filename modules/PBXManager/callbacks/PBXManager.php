<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
chdir(dirname(__FILE__) . '/../../../');
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
require_once 'includes/SalesPlatform/ShutdownHandler.php';
vimport('includes.http.Request');

class PBXManager_PBXManager_Callbacks
{
    function validateRequest($vtigersecretkey, $requestkey)
    {
        return $vtigersecretkey == $requestkey;
    }

    function process($request)
    {
        $pbxmanagerController = new PBXManager_PBXManager_Controller();
        $connector = $pbxmanagerController->getConnector();

        $connector->logRequest($_REQUEST);

        $valid = $this->validateRequest(
            $connector->getVtigerSecretKey(),
            $request->get('vtigersignature')
        );

        if (!$valid) {
            echo 'Fail';
            return;
        }

        $id = $pbxmanagerController->process($request);
        echo 'Done';
    }
}

if (empty($current_user)) {
    $current_user = Users::getActiveAdminUser();
}

(new ShutdownHandler())->registerSystemEventsLog();

$pbxmanager = new PBXManager_PBXManager_Callbacks();
$pbxmanager->process(new Vtiger_Request($_REQUEST));
