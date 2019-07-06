<?php
/**
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 */

class PBXManager_OutgoingCall_Action extends Vtiger_Action_Controller
{

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

        if (!$permission) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    /**
     * Originate via connector
     *
     * @param Vtiger_Request $request number and related record
     *
     * @return null
     */
    public function process(Vtiger_Request $request)
    {
        $serverModel = PBXManager_Server_Model::getInstance();
        $gateway = $serverModel->get("gateway");
        $user = Users_Record_Model::getCurrentUserModel();
        $userNumber = $user->phone_crm_extension;
        $proceed = $gateway && $userNumber;

        if (!$proceed) {
            return $this->_emit([$gateway, $userNumber]);
        }

        $number   = $request->get('number');
        $recordId = $request->get('record');
        $connector = $serverModel->getConnector();
        try {
            $result = $connector->call($number, $recordId, $userNumber);
        } catch(Exception $e) {
            $result = $e->getMessage();
        }

        return $this->_emit($result);
    }

    /**
     * response wrapper
     *
     * @param str $x    message
     * @param int $code error flag
     *
     * @return void
     */
    function _emit($x, $code = false)
    {
        $res = new Vtiger_Response;
        if ($code) {
            $res->setError($code, $x);
        } else {
            $res->setResult($x);
        }
        $res->emit();
    }
}
