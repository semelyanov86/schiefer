<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class PBXManager_Reject_Action extends Vtiger_Action_Controller
{

    public function checkPermission(Vtiger_Request $request)
    {
        return true;
    }

    public function process(Vtiger_Request $request)
    {
        $id = $request->get('id');
        $rec = Vtiger_Record_Model::getInstanceById($id);
        $rec->set('mode', 'edit');
        $rec->set('callstatus', 'rejected');
        $rec->save();
        $response = new Vtiger_Response();
        $response->setResult('Done');
        $response->emit();
    }
}
