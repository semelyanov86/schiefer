<?php
function checkDublicates($ws_entity){
    // WS id
    $ws_id = $ws_entity->getId();
    $module = $ws_entity->getModuleName();
    if (empty($ws_id) || empty($module)) {
        return;
    }

    // CRM id
    $crmid = vtws_getCRMEntityId($ws_id);
    if ($crmid <= 0) {
        return;
    }

    //получение объекта со всеми данными о текущей записи Модуля "PBXManager"
    $pbxInstance = Vtiger_Record_Model::getInstanceById($crmid);
    global $adb;
    $status = $pbxInstance->get('callstatus');
    $sourceid = $pbxInstance->get('sourceuuid');
    $number = $pbxInstance->get('customernumber');
    if ($status === 'no-answer') {
        $noAnswerLead = createLeadRecord($pbxInstance);
    }
    $query="select vtiger_pbxmanager.pbxmanagerid, vtiger_pbxmanagercf.cf_1250 from vtiger_pbxmanager inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_pbxmanager.pbxmanagerid inner join vtiger_pbxmanagercf on vtiger_pbxmanagercf.pbxmanagerid=vtiger_pbxmanager.pbxmanagerid where vtiger_crmentity.deleted=0 and  vtiger_pbxmanager.sourceuuid = ?";
    $result = $adb->pquery($query, array($sourceid));

        if($adb->num_rows($result) >= 1){
            while($result_set = $adb->fetch_array($result))
            {
                $previd = $result_set["pbxmanagerid"];
                $leadid = false;
                $prevModel = PBXManager_Record_Model::getInstanceById($previd);
                if ($prevModel->get('callstatus') === 'completed') {
                    return false;
                } else {
                    $leadid = $result_set["cf_1250"];
                    $prevModel->delete();
                }
                if ($leadid){
                    $this->deleteLeadById($leadid);
                }
            }
        }

        $query = "SELECT vtiger_pbxmanager.pbxmanagerid, vtiger_pbxmanager.customernumber, vtiger_pbxmanagercf.cf_1250, vtiger_pbxmanager.callstatus FROM vtiger_pbxmanager INNER JOIN vtiger_crmentity on vtiger_pbxmanager.pbxmanagerid=vtiger_crmentity.crmid INNER JOIN vtiger_pbxmanagercf ON vtiger_pbxmanagercf.pbxmanagerid = vtiger_pbxmanager.pbxmanagerid ORDER BY vtiger_pbxmanager.pbxmanagerid DESC LIMIT 50";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) >= 1){
            while($result_set = $adb->fetch_array($result))
            {
                $previd = $result_set["pbxmanagerid"];
                $prevNumber = $result_set['customernumber'];
                $status = $result_set['callstatus'];
                $leadid = false;
                if ($status === 'completed' && $prevNumber === $number) {
                    return false;
                } else {
                    $prevModel = PBXManager_Record_Model::getInstanceById($previd);
                    $leadid = $result_set["cf_1250"];
                    $prevModel->delete();
                }
                /*if ($status === 'no-answer' && $prevNumber === $number) {
                    $prevModel = PBXManager_Record_Model::getInstanceById($previd);
                    $leadid = $result_set["cf_1250"];
                    $prevModel->delete();
                } elseif ($status === 'completed' && $prevNumber === $number) {
                    return false;
                }*/
                if ($leadid){
                    deleteLeadById($leadid);
                }
            }
        }

}

function createLeadRecord($pbxInstance)
{
    $number = $pbxInstance->get('customernumber');
    if (strlen($number) < 5) {
        return false;
    }
    $leadModel = Vtiger_Record_Model::getCleanInstance('Leads');
    $leadModel->set('mode', 'create');
    $leadModel->set('lastname', 'Missed Call');
    $leadModel->set('assigned_user_id', '1');
    $leadModel->set('phone', $number);
    $leadModel->set('description', $this->get('sourceuuid'));
    $leadModel->save();
    $this->set('cf_1250', $leadModel->getId());
    if (!$this->get('customer')) {
        $this->set('customer', $leadModel->getId());
    }
    return $leadModel;

}

/*
 * Function deletes Lead Record by id
 * @param int Lead id
 * return boolean depend on successful implementation.
 */
function deleteLeadById($leadid)
{
    if (!$leadid) {
        return false;
    }
    $leadModel = Vtiger_Record_Model::getInstanceById($leadid, 'Leads');
    if ($leadModel) {
        $leadModel->delete();
        return true;
    } else {
        return false;
    }
}