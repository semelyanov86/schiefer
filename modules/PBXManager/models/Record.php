<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PBXManager_Record_Model extends Vtiger_Record_Model
{

    const moduletableName = 'vtiger_pbxmanager';
    const lookuptableName = 'vtiger_pbxmanager_phonelookup';
    const entitytableName = 'vtiger_crmentity';

    /**
     * Function to save/update contact/account/lead record in Phonelookup table on every save
     * TODO move to phonelookup methods
     *
     * @param str $fieldName field containing phone number
     * @param arr $details   crm data crmid setype
     * @param mix $new       unknown param
     *
     * @return bool always true?
     */
    public function receivePhoneLookUpRecord($fieldName, $details, $new)
    {
        $recordid = $details['crmid'];
        $fnumber = preg_replace('/[-()\s+]/', '', $details[$fieldName]);
        $rnumber = strrev($fnumber);
        $db = PearDatabase::getInstance();

        if ($fnumber == '') {
            $db->pquery(
                'DELETE FROM ' . self::lookuptableName . ' where crmid=? AND fieldname=? AND setype=?',
                [$recordid, $fieldName, $details['setype']]
            );
            return true;
        }

        $params = [$recordid, $details['setype'], $fnumber, $rnumber, $fieldName];
        $db->pquery('INSERT INTO '.self::lookuptableName.
            '(crmid, setype, fnumber, rnumber, fieldname)
            VALUES(?,?,?,?,?)
            ON DUPLICATE KEY
            UPDATE fnumber=VALUES(fnumber), rnumber=VALUES(rnumber)',
            $params
        );

        self::setCustomerFuzz($fnumber, $details);

        return true;
    }

    /**
     * Function to delete contact/account/lead record in Phonelookup table on every delete
     * TODO move to phonelookup methods
     *
     * @param str $recordid crmid
     *
     * @return int affected
     */
    public function deletePhoneLookUpRecord($recordid)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery(
            'DELETE FROM '.self::lookuptableName.' where crmid=?',
            [$recordid]
        );
        return $db->getAffectedRowCount($result);
    }

    /**
     * Function to check the customer with number in phonelookup table
     * 3 steps:
     *   phonelookup
     *   updatecustomer
     *   fuzzySearch
     *
     * @param str $from       raw phone number
     * @param int $asgnUserId call assigned user id
     *
     * @return void
     */
    public static function lookUpRelatedWithNumber($from, $asgnUserId = null)
    {
        if (empty($from)) {
            return false;
        }

        //skip numbers length under this
        $lowerLimit = 7;

        //prefix numbers length above this
        $upperLimit = 10;

        $db = PearDatabase::getInstance();

        $origFrom = $from;

        //$from = preg_replace('/^\+?7/', '8', $from);

        $fnumber = preg_replace('/[-()\s+]/', '', $from);

        // exclude internals processing
        if (strlen($fnumber) < $lowerLimit) return false;

        // Exact match
        $query = "SELECT setype, crmid FROM vtiger_pbxmanager_phonelookup
            WHERE fnumber = ? LIMIT 1";
        $exact = $db->pquery($query, [$fnumber]);

        // Bind every past call to a newly found id
        if ($db->num_rows($exact) == 1) {
            $entityData = $db->fetchByAssoc($exact);
            // TODO if ($cfg->updateOnLookup)
            self::setCustomer($origFrom, $entityData);
        }

        // Search first entity data with match to assigned user if need
        $callerEntityData = self::fuzzySearch($fnumber);

        return $callerEntityData;

        $condition = ($asgnUserId != null) &&
            ($callerEntityData['smownerid'] != $asgnUserId);
        if (!$condition) {
            return $callerEntityData;
        }

        while ($row = $db->fetchByAssoc($result)) {
            if ($row['smownerid'] != $asgnUserId) {
                continue;
            }
            return $row;
        }
    }

    /**
     * Silent sql customer update
     * by exact phone number
     *
     * @param str $phone customer phonenumber
     * @param arr $data  entity info crmid setype
     *
     * @return int affected
     */
    public static function setCustomer($phone, $data)
    {
        $db = PearDatabase::getInstance();
        $query = "UPDATE vtiger_pbxmanager SET
            customer = ?,
            customertype = ?
            WHERE customer IN ('', NULL, 0)
                AND customernumber = ?";

        $res = $db->pquery($query, [$data['crmid'], $data['setype'], $phone]);

        return $db->getAffectedRowCount($res);
    }

    /**
     * Silent sql customer update
     * by phone number last 10
     *
     * @param str $phone customer phonenumber
     * @param arr $data  entity info crmid setype
     *
     * @return int affected
     */
    public static function setCustomerFuzz($phone, $data)
    {
        $last10 = substr($phone, -10, 10);

        return self::setCustomer($last10, $data);
    }

    /**
     * Last 10 digits lookup
     *
     * @param str $phone      raw phonenumber
     * @param int $upperLimit upper limit to cut
     *
     * @return bool | arr
     */
    public static function fuzzySearch($phone, $upperLimit = 10)
    {
        $args = [$phone];
        if (strlen($phone) >= $upperLimit) {
            $args = ['%' . substr($phone, -10, 10)];
        }

        $db = PearDatabase::getInstance();
        $result = $db->pquery(
            'SELECT
                crmid AS id,
                label AS name,
                vtiger_crmentity.setype,
                smownerid,
                fieldname
            FROM ' . self::lookuptableName . '
            INNER JOIN vtiger_crmentity USING (crmid)
            WHERE fnumber LIKE ? AND deleted=0',
            $args
        );
        if ($db->num_rows($result) == 0) {
            return false;
        }

        return $db->fetchByAssoc($result);
    }

    /**
     * To update Assigned / smownerid as it is a system field
     * SQL update
     *
     * @param int $userid crm user id
     *
     * @return int affected
     */
    public function updateAssignedUser($userid)
    {
        $callid = $this->get('pbxmanagerid');
        $db = PearDatabase::getInstance();
        $res = $db->pquery(
            'UPDATE ' . self::entitytableName . ' SET smownerid=? WHERE crmid=?',
            [$userid, $callid]
        );

        return $db->getAffectedRowCount($res);
    }

    /**
     * Find User by extension. sql lookup
     *
     * @param <string> $number phone_crm_extension
     *
     * @return mixed bool|null|array
     */
    public static function getUserInfoWithNumber($number)
    {
        $db = PearDatabase::getInstance();
        if (empty($number)) {
            return false;
        }
        $query = PBXManager_Record_Model::buildSearchQueryWithUIType(11, $number, 'Users');
        $result = $db->pquery($query, array());
        if ($db->num_rows($result) > 0 ) {
            $user['id'] = $db->query_result($result, 0, 'id');
            $user['name'] = $db->query_result($result, 0, 'name');
            $user['setype'] = 'Users';
            return $user;
        }
        return;
    }

    /**
     * Overriden? always new instance?
     */
    static function getCleanInstance()
    {
        return new self;
    }

    /**
     * Function to get call details(polling)
     *
     * @deprecated
     * @return <array> calls
     */
    public function searchIncomingCall()
    {
        $db = PearDatabase::getInstance();
        $db->database->SetFetchMode(2);
        $query = 'SELECT * FROM '.self::moduletableName.' AS module_table INNER JOIN '
            . self::entitytableName.' AS entity_table'
            . ' WHERE module_table.callstatus IN(?,?)'
            . ' AND module_table.direction=?'
            . ' AND module_table.pbxmanagerid=entity_table.crmid'
            . ' AND entity_table.deleted=0';
        $result = $db->pquery($query, array('ringing','in-progress','inbound'));
        $recordModels = array();
        $rowCount =  $db->num_rows($result);
        for ($i=0; $i<$rowCount; $i++) {
            $rowData = $db->query_result_rowdata($result, $i);

            $record = new self();
            $record->setData($rowData);
            $recordModels[] = $record;

            //To check if the call status is 'ringing' for >5min
            $starttime   = strtotime($rowData['starttime']);
            $currenttime = strtotime(Date('y-m-d H:i:s'));
            $timeDiff    = $currenttime - $starttime;
            if($timeDiff > 300 && $rowData['callstatus'] == 'ringing') {
                $recordIds[] = $rowData['crmid'];
            }
        }

        if (count($recordIds)) $this->updateCallStatus($recordIds);

        return $recordModels;
    }

    /**
     * To update call status from 'ringing' to 'no-response', if status not updated
     * for more than 5 minutes
     *
     * @param arr $recordIds list
     *
     * @deprecated
     * @return void
     */
    public function updateCallStatus($recordIds)
    {
        $db = PearDatabase::getInstance();
        $query = "UPDATE ".self::moduletableName." SET callstatus='no-response'
            WHERE pbxmanagerid IN (".generateQuestionMarks($recordIds).")
            AND callstatus='ringing'";
        $db->pquery($query, $recordIds);
    }

    /**
     * Function to save PBXManager record with array of params
     *
     * @param <array> $params values
     *
     * @deprected
     * @return <string> $recordid
     */
    public function saveRecordWithArrray($params)
    {
        $moduleModel = Vtiger_Module_Model::getInstance('PBXManager');
        $recordModel = Vtiger_Record_Model::getCleanInstance('PBXManager');
        $recordModel->set('mode', '');
        $details = array_change_key_case($params, CASE_LOWER);
        $fieldModelList = $moduleModel->getFields();
        foreach ($fieldModelList as $fieldName => $fieldModel) {
                $fieldValue = $details[$fieldName];
                $recordModel->set($fieldName, $fieldValue);
        }
        return $moduleModel->saveRecord($recordModel);
    }

    /**
     * Function to update call details
     *
     * @param <array> $details
     * @param <string> $user
     *
     * @deprecated
     * @return true
     */
    public function updateCallDetails($details, $user)
    {
        $db = PearDatabase::getInstance();
        $sourceuuid = $this->get('sourceuuid');
        $query = 'UPDATE '.self::moduletableName.' SET ';
        foreach($details as $key => $value){
            $query .= $key . '=?,';
            $params[] = $value;
        }
        $query = substr_replace($query ,"",-1);

        // SalesPlatform.ru begin
        //$query .= ' WHERE sourceuuid = ?';
        // SalesPlatform.ru end

        $params[] = $sourceuuid;

        // SalesPlatform.ru begin
        $query .= ' WHERE sourceuuid = ?'; // PINstudio @binizik
        // $params[] = $user['id']; // PINstudio @binizik
        // SalesPlatform.ru end

        $db->pquery($query, $params);
        return true;
    }

    public function updateCallDetailsWithoutUser($details){
        $db = PearDatabase::getInstance();
        $sourceuuid = $this->get('sourceuuid');

        $query = 'UPDATE '.self::moduletableName.' SET ';

        foreach($details as $key => $value){
            $query .= $key . '=?,';
            $params[] = $value;
        }

        $query = substr_replace($query ,'', -1);
        $params[] = $sourceuuid;
        $query .= ' WHERE sourceuuid = ?';

        $db->pquery($query, $params);

        return true;
    }

    public static function getInstanceById($phonecallsid){
        $db = PearDatabase::getInstance();
        $record = new self();
        $query = 'SELECT * FROM '.self::moduletableName.' WHERE pbxmanagerid=?';
        $params = array($phonecallsid);
        $result = $db->pquery($query, $params);
        $rowCount =  $db->num_rows($result);
        if($rowCount){
            $rowData = $db->query_result_rowdata($result, 0);
            $record->setData($rowData);
        }
        return $record;
    }

    public static function getInstanceBySourceUUID($sourceuuid, $user)
    {
        $db = PearDatabase::getInstance();
        $record = new self();

        $query = 'SELECT * FROM ' . self::moduletableName . ' WHERE sourceuuid=?';
        $params = array($sourceuuid);

        $result = $db->pquery($query, $params);
        $rowCount = $db->num_rows($result);
        if ($rowCount){
            $rowData = $db->query_result_rowdata($result, 0);
            $record->setData($rowData);
        }
        return $record;
    }

    public static function getInstanceBySourceUUIDWithoutUser($sourceuuid){
        $db = PearDatabase::getInstance();
        $record = new self();

        $query = 'SELECT * FROM ' . self::moduletableName . ' WHERE sourceuuid=?';
        $params = array($sourceuuid);

        $result = $db->pquery($query, $params);
        $rowCount =  $db->num_rows($result);

        if($rowCount){
            $rowData = $db->query_result_rowdata($result, 0);
            $record->setData($rowData);
        }

        return $record;
    }

    public static function updateCallRecordBySourceUUID($sourceuuid, $recordingUrl) {
        $db = PearDatabase::getInstance();
        $query = 'UPDATE '.self::moduletableName.' SET recordingurl=? WHERE sourceuuid=?';
        $db->pquery($query, array($recordingUrl, $sourceuuid));
    }

    public static function updateCallDetailsBySourceUUID($sourceuuid, $details) {
        $db = PearDatabase::getInstance();
        //file_put_contents('vt.log', var_export($details,1)."\n", FILE_APPEND);
        $query = 'UPDATE '.self::moduletableName.' SET ';
        $params = array();
        foreach($details as $key => $value){
            $query .= $key . '=?,';
            $params[] = $value;
        }
        $query = substr_replace($query ,"",-1);
        $query .= ' WHERE sourceuuid = ?';
        $params[] = $sourceuuid;

        $db->pquery($query, $params);
        /*
        //PINstudio @DK #red-612
        $pbxRecord = self::getInstanceBySourceUUIDWithoutUser($sourceuuid);
        //w(var_export($details, 1));
        $pbxRecord->set('mode', 'edit');
        foreach($details as $key => $value){
            $pbxRecord->set($key, $value);
        }
        $pbxRecord->save();

        return 1;
        */
    }

    // Because, User is not related to crmentity
    public function buildSearchQueryWithUIType($uitype, $value, $module)
    {
        if (empty($value)) {
            return false;
        }

        $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
        if ($cachedModuleFields === false) {
            getColumnFields($module); // This API will initialize the cache as well
            // We will succeed now due to above function call
            $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
        }

        $lookuptables = array();
        $lookupcolumns = array();
        foreach ($cachedModuleFields as $fieldinfo) {
            if (in_array($fieldinfo['uitype'], array($uitype))) {
                $lookuptables[] = $fieldinfo['tablename'];
                $lookupcolumns[] = $fieldinfo['columnname'];
            }
        }

        $entityfields = getEntityField($module);
        $querycolumnnames = implode(',', $lookupcolumns);
        $entitycolumnnames = $entityfields['fieldname'];

        $query = "select id as id, $querycolumnnames, $entitycolumnnames as name ";
        $query .= " FROM vtiger_users";

        if (!empty($lookupcolumns)) {
            $query .=" WHERE deleted=0 AND ";
            $i = 0;
            $columnCount = count($lookupcolumns);
            foreach ($lookupcolumns as $columnname) {
                if (!empty($columnname)) {
                    if ($i == 0 || $i == ($columnCount))
                        $query .= sprintf("%s = '%s'", $columnname, $value);
                    else
                        $query .= sprintf(" OR %s = '%s'", $columnname, $value);
                    $i++;
                }
            }
         }
         return $query;
    }

    public static function getUserNumbers(){
        $numbers = null;
        $db = PearDatabase::getInstance();
        $query = 'SELECT id, phone_crm_extension FROM vtiger_users';
        $result = $db->pquery($query, array());
        $count = $db->num_rows($result);
        for ($i=0; $i<$count; $i++){
            $number = $db->query_result($result, $i, 'phone_crm_extension');
            $userId = $db->query_result($result, $i, 'id');
            if($number)
                $numbers[$userId] = $number;
        }
        return $numbers;
    }

    /**
     * Function to retrieve display value for a field
     * separate process for url, callstatus
     *
     * @param str  $fieldName vtiger field name
     * @param bool $recordId  crm id
     *
     * @return bool|string <String>
     * @internal param $ <String> $fieldName - field name for which values need to get
     */
    public function getDisplayValue($fieldName, $recordId = false)
    {
        if (empty($recordId)) {
            $recordId = $this->getId();
        }

        $fieldModel = $this->getModule()->getField($fieldName);
        if (!$fieldModel) {
            return false;
        }

        // TODO replace with ui type?
        if ($fieldName == 'recordingurl') {
            return $this->isCompleted()
                ? $this->getAudiorecordTpl()
                : '';
        }

        // Display custom call status
        if ($fieldName == 'callstatus') {
            $value = $fieldModel->getDisplayValue(
                $this->get($fieldName),
                $recordId,
                $this
            );

            return $this->getStatusHtml($recordId, $value);
        }

        return $fieldModel->getDisplayValue($this->get($fieldName), $recordId, $this);
    }


    public function getStatusHtml($id, $value)
    {
        $record = PBXManager_Record_Model::getInstanceById($id);

        $labelType = $this->getLabelClass($value);
        $icon = $this->getIconClass($record->get('direction'));

        return sprintf(
            '<span class="label label-%s"><i class="%s icon-white"></i>&nbsp;%s</span>',
            $labelType,
            $icon,
            vtranslate($value, $this->getModuleName())
        );
    }

    public function isCompleted($status = false)
    {
        if (empty($status)) {
            $status = $this->get('callstatus');
        }
        return in_array(
            $status,
            [
                /*
                vtranslate('completed', 'PBXManager'),
                vtranslate('ANSWER', 'PBXManager'),
                */
                'ANSWER',
                'completed'
            ]
        );
    }

    /**
     * TODO Move to views
     *
     * @param str $direction call direction
     *
     * @return str one of fontawesome css class names
     */
    public function getIconClass($direction)
    {
        $map = [
            'outbound' => 'icon-arrow-up',
            'inbound'  => 'icon-arrow-down',
            'internal' => 'icon-retweet',
        ];
        return array_key_exists($direction, $map)
            ? $map[$direction]
            : 'icon-question-sign';
    }

    /**
     * TODO Move to views
     *
     * @param str $status call status
     *
     * @return str one of PNotify css class names
     */
    public function getLabelClass($status)
    {
        $map = [
            'ringing' => 'info',
            'В прогрессе' => 'info',
            'in-progress' => 'info',
            'completed' => 'success',
            'Завершен' => 'success',
            'Отвечен' => 'success',
            'no-answer' => 'important',
            'ANSWER' => 'success',
            'CANCEL' => 'important',
        ];

        return array_key_exists($status, $map)
            ? $map[$status]
            : 'warning';
    }

    /**
     * get key using server model
     * get hash using module model
     * compile url from recordingurl field value
     *
     * @return string
     */
    public function getAudiorecordTpl()
    {
        $url = $this->get('recordingurl');
        if (empty($url)) return 'No record';

        $settings = PBXManager_Server_Model::getInstance();
        $secretKey = $settings->get('vtigersecretkey');

        $audioFilename = array_pop(explode('/', $url));
        $callCID = array_shift(explode('.', $audioFilename));

        $audioUrl = implode('/', [
            rtrim($settings->get('webappurl'), '/'),
            'getRecord',
            $this->getHash([$callCID], $secretKey),
            $audioFilename
        ]);

        $cfg = new PBXManager_Config_Model;
        $options = $cfg->getModDefaults();
        $preload = $options['audioPreload'];
        return '<audio src="'. $audioUrl .'" preload="' . $preload . '" onclick="event.stopPropagation()" controls>'
            . '<a href="'. $audioUrl .'" ><i class="icon-volume-up"></i></a>'
            . '</audio>';
    }

    /**
     * Calculate md5 hash
     *
     * @param arr $params    data
     * @param str $secretKey salt
     *
     * @return str
     */
    public function getHash($params, $secretKey)
    {
        $hash = '';
        foreach ($params as $key => $value) {
            $hash = md5($hash . $value);
        }

        return md5($hash . $secretKey);
    }
}
