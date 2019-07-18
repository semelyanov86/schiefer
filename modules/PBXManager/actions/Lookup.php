<?php

/**
 * lookup action via pbxmanager native methods
 */
class PBXManager_Lookup_Action extends Vtiger_BasicAjax_Action
{
    function process(Vtiger_Request $req)
    {
        $phone = $req->get('phone');
        $mode  = $req->get('mode');
        if ($mode == 'exten') {
            $result = PBXManager_Record_Model::getUserInfoWithNumber($phone);
        } else {
            $result = PBXManager_Record_Model::lookUpRelatedWithNumber($phone);
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
