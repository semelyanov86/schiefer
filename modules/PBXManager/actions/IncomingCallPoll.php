<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'include/Webservices/Create.php';
include_once 'include/utils/utils.php';

class PBXManager_IncomingCallPoll_Action extends Vtiger_Action_Controller
{

    function __construct()
    {
        $this->exposeMethod('searchIncomingCalls');
        $this->exposeMethod('createRecord');
        $this->exposeMethod('getCallStatus');
        $this->exposeMethod('checkModuleViewPermission');
        $this->exposeMethod('checkPermissionForPolling');
        $this->exposeMethod('getUsers');
        $this->exposeMethod('getSourceSiteList');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if(!empty($mode) && $this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * Unprivileged user exten list
     * query + json response
     */
    public function getUsers($request)
    {
        $db = PearDatabase::getInstance();

        $query = "SELECT first_name, last_name, phone_crm_extension
            FROM vtiger_users
            WHERE status = 'Active' AND phone_crm_extension <> ''";

        $result = $db->pquery($query, []);

        header('Content-Type: application/json');
        $numRows = $db->num_rows($result);
        if ($numRows == 0) {
            echo '{}';
            return;
        }

        while ($row = $db->fetch_array($result)) {
            $tmp = [];
            if ($row['first_name']) $tmp[] = $row['first_name'];
            if ($row['last_name']) $tmp[] = $row['last_name'];
            $rows[$row['phone_crm_extension']] = implode(' ', $tmp);
        }

        echo json_encode($rows);
    }

    public function searchIncomingCalls(Vtiger_Request $request)
    {
        $recordModel = PBXManager_Record_Model::getCleanInstance();
        $response = new Vtiger_Response();
        $user = Users_Record_Model::getCurrentUserModel();
        $calls = false;
        $recordModels = $recordModel->searchIncomingCall();
        // To check whether user have permission on caller record
        if (count($recordModels) == 0) {
            $response->setResult(false);
            $response->emit();
            return;
        }

        foreach ($recordModels as $recordModel){
            // To check whether the user has permission to see contact name in popup
            $recordModel->set('callername', null);
            if($user->id != $recordModel->get('user')) {
                continue;
            }

            $callerid = $recordModel->get('customer');
            if($callerid){
                $moduleName = $recordModel->get('customertype');

                // SalesPlatform.ru begin
                $callerRecordModel = Vtiger_Record_Model::getInstanceById($callerid, $moduleName);
                $ownerId = $callerRecordModel->get('assigned_user_id');
                $recordModel->set('ownername', getUserFullName($ownerId));
                // SalesPlatform.ru end

                //TODO refeactoring + Find Related
                if ($moduleName == 'Contacts'){
                    $accId = $callerRecordModel->get('account_id');
                    if (!empty($accId)){
                        $accRecord = Vtiger_Record_Model::getInstanceById($accId, 'Accounts');
                        $accName = $accRecord->get('accountname');
                        $recordModel->set('accid', $accId);
                        $recordModel->set('accname', $accName);
                        $similar = $this->findSameNumber($callerid);

                        if ($similar) {
                            $recordModel->set('similar', $similar);
                        }
                        //w("{$callerid} / {$accId} {$accName}");
                    }
                }

                if(!Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $callerid)){
                    $name = $recordModel->get('customernumber').vtranslate('LBL_HIDDEN','PBXManager');
                    $recordModel->set('callername',$name);
                }else{
                    $entityNames = getEntityName($moduleName, array($callerid));
                    $callerName = $entityNames[$callerid];
                    $recordModel->set('callername',$callerName);
                }
            }
            // End
            $direction = $recordModel->get('direction');
            if($direction == 'inbound'){
                $userid = $recordModel->get('user');
                if($userid){
                    $entityNames = getEntityName('Users', array($userid));
                    $userName = $entityNames[$userid];
                    $recordModel->set('answeredby',$userName);
                }
            }
            $recordModel->set('current_user_id',$user->id);
            $calls[] = $recordModel->getData();
        }
        //}
        $response->setResult($calls);
        $response->emit();
    }

    /**
     * Get related entities
     *
     * @param int $crmid entity id
     *
     * @return arr list of similar entities
     */
    public function findSameNumber($crmid)
    {
        if (empty($crmid)) return false;
        $db = PearDatabase::getInstance();
        $db->database->SetFetchMode(2);
        $sql = "SELECT
                vpp1.crmid, vpp1.setype,
                CASE vpp1.setype
                WHEN 'Contacts' THEN vcd.lastname
                WHEN 'Accounts' THEN va0.accountname
                END name,
                va.accountid, va.accountname
            FROM vtiger_pbxmanager_phonelookup vpp0
            INNER JOIN vtiger_pbxmanager_phonelookup vpp1 ON vpp0.fnumber = vpp1.fnumber
            LEFT JOIN vtiger_contactdetails vcd ON vpp1.crmid = vcd.contactid AND vpp1.setype = 'Contacts'
            LEFT JOIN vtiger_account va0 ON vpp1.crmid = va0.accountid AND vpp1.setype = 'Accounts'
            LEFT JOIN vtiger_account va ON vcd.accountid = va.accountid
            LEFT JOIN vtiger_crmentity vc1 ON vpp1.crmid = vc1.crmid
            LEFT JOIN vtiger_crmentity vc2 ON vcd.accountid = vc2.crmid
            WHERE vpp0.crmid = ?
            AND vc1.deleted = 0
            ORDER BY vpp1.crmid DESC";

        $same = $db->pquery($sql, [$crmid]);
        $nr = $db->num_rows($same);
        if ($nr == 0) return false;

        $related = [];
        while ($row = $db->fetch_array($same)) {
            $related[] = $row;
        }

        return $related;
    }

    /**
     *
     * TODO refactor
     */
    public function createRecord(Vtiger_Request $request)
    {
        $user = Users_Record_Model::getCurrentUserModel();

        // Process request data
        $moduleName = $request->get('modulename');
        $name  = urldecode($request->get('name'));
        $phone = urldecode($request->get('number'));
        $element = [];
        switch ($moduleName) {
            case 'Accounts':
                $element['accountname'] = $name;
                $element['phone']       = $phone;
            break;
            case 'Contacts':
                $element['lastname']  = $name;
                $element['mobile']    = $phone;
            break;
        }
        $element['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->id);

        // Process mandatory/default values
        $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
        $mandatoryFieldModels = $moduleInstance->getMandatoryFieldModels();
        foreach($mandatoryFieldModels as $mandatoryField){
            $fieldName = $mandatoryField->get('name');
            $fieldType = $mandatoryField->getFieldDataType();
            $defaultValue = decode_html($mandatoryField->get('defaultvalue'));
            if(!empty($element[$fieldName])){
                continue;
            }
            $fieldValue = $defaultValue;
            if(empty($fieldValue)) {
                $fieldValue = Vtiger_Util_Helper::getDefaultMandatoryValue($fieldType);
            }
            $element[$fieldName] = $fieldValue;
        }

        // Entity creation
        $entity = vtws_create($moduleName, $element, $user);
        $this->updateCustomerInPhoneCalls($entity, $request);

        $response = new Vtiger_Response();
        $response->setResult(explode('x', $entity['id'])[1]);
        $response->emit();
    }

    /**
     * TODO replace with PBXManager_Record_Model::setCustomer
     */
    public function updateCustomerInPhoneCalls($customer, $request)
    {
        $id = vtws_getIdComponents($customer['id']);
        $sourceuuid = $request->get('callid');
        $module = $request->get('modulename');
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $user = ['id' => $currentUser->id];
        $recordModel = PBXManager_Record_Model::getInstanceBySourceUUID($sourceuuid, $user);
        $recordModel->updateCallDetails(array('customer'=>$id[1], 'customertype'=>$module), $user);
    }

    public function getCallStatus($request)
    {
        $phonecallsid = $request->get('callid');
        $recordModel = PBXManager_Record_Model::getInstanceById($phonecallsid);
        $response = new Vtiger_Response();
        $response->setResult($recordModel->get('callstatus'));
        $response->emit();
    }

    /**
     * Retrieve sites list
     *
     * @param Vtiger_Request $request req model
     *
     * @deprecated
     *
     * @return str encoded array
     */
    public function getSourceSiteList($request)
    {
        global $adb;
        $query = "SELECT `cf_925` FROM `vtiger_cf_925` ORDER BY `vtiger_cf_925`.`sortorderid` ASC";
        $result = $adb->pquery($query, array());

        $numRows = $adb->num_rows($result);
        for($i=0; $i < $numRows; $i++) {
            $rows[] = $adb->query_result_rowdata($result, $i)['cf_925'];
        }

        echo json_encode($rows);
    }

    function checkPermissionForPolling(Vtiger_Request $request)
    {
        Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $callPermission = Users_Privileges_Model::isPermitted('PBXManager', 'ReceiveIncomingCalls');

        $serverModel = PBXManager_Server_Model::getInstance();
        $gateway = $serverModel->get("gateway");

        $user = Users_Record_Model::getCurrentUserModel();
        $userNumber = $user->phone_crm_extension;

        $result = false;
        if ($callPermission && $userNumber && $gateway ) {
            $result = true;
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

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

    public function checkModuleViewPermission(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $modules = ['Contacts', 'Accounts'];
        $view = $request->get('view');
        Users_Privileges_Model::getCurrentUserPrivilegesModel();
        foreach ($modules as $module){
            $result['modules'][$module]
                = Users_Privileges_Model::isPermitted($module, $view);
        }
        $response->setResult($result);
        $response->emit();
    }
}
