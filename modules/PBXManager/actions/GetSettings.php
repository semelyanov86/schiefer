<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

include_once 'modules/Settings/PBXManager/libraries/Settings.php';

class PBXManager_GetSettings_Action extends Vtiger_Action_Controller
{
	public function checkPermission(Vtiger_Request $request)
	{
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

		if(!$permission) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}
    
    public function process(Vtiger_Request $request)
	{
		$response = new Vtiger_Response();
		try {
			$settingsModel = VD_Settings_PBXManager::getInstance();
            $settings = $settingsModel::getSettings();
            $response->setResult($settings);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }
        $response->emit();
    }
}
