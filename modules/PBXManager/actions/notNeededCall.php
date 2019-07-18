<?php
// PINstudio begin @binizik

class PBXManager_notNeededCall_Action extends Vtiger_Action_Controller{

    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

        if(!$permission) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }
    
    public function process(Vtiger_Request $request) {
        global $adb;

        $recordId = $request->get('recordid');

        if ($recordId) {
            $query = "UPDATE `vtiger_pbxmanager` SET `needtocall` = 0 WHERE `pbxmanagerid` = ?";
            $res = $adb->pquery($query, array($recordId));

            header("location: index.php?module=PBXManager&view=Detail&record=".$recordId);
        }
    }
    
}