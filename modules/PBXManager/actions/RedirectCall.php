<?php

class PBXManager_RedirectCall_Action extends Vtiger_Action_Controller
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

    public function process(Vtiger_Request $request)
    {
        $serverModel = PBXManager_Server_Model::getInstance();
        $gateway = $serverModel->get("gateway");
        $user = Users_Record_Model::getCurrentUserModel();
        $to = $request->get('to');
        $uuid = $request->get('recordid');
        $connector = $serverModel->getConnector();
        $result = $connector->redirectCall($to, $uuid);

        $res = new Vtiger_Response;
        $res->setResult($result);
        $res->emit();
    }
    
}
