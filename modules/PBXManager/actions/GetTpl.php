<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class PBXManager_GetTpl_Action extends Vtiger_Action_Controller {
    public function checkPermission(Vtiger_Request $request)
    {
        return true;
    }

    public function process(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $moduleModel = Vtiger_Module_Model::getInstance('PBXManager');

        echo $moduleModel->getAudiorecordTpl($recordId);
    }
}
