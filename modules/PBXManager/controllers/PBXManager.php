<?php
/**
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 */

/**
 * Before: Used to decide what to do
 * Now: just accepts connector data
 */
class PBXManager_PBXManager_Controller
{
    const DEFASSIGNED = 1;
    const MAX_LENGTH = 6;
    public $strategy = 'reset';

    /**
     * creates PBXManager record based on request data
     *
     * @param Vtiger_Request $request connector data
     *
     * @return new record id
     */
    public function process($request)
    {
        $activeGateway = 1;

        $cfg = $this->getConfig();

        $skip = in_array($request->get('direction'), $cfg['skipTypes']);
        if ($skip) return;

        $src = $this->processSrc($request->get('customernumber'));
        $dst = $request->get('usernumber');
        $status = $request->get('callstatus');

        if ($this->isAnswered($status)) {
            $this->resetN2C($src);
        }

        // Detect EXTERNAL call
        $external = (strlen($src) > self::MAX_LENGTH)
            || (strlen($dst) > self::MAX_LENGTH)
            || ($request->get('call_type') == 'external');

        $map = $this->getMapping();
        $pbxData = $this->convertToCrm($map, $request);
        $pbxData['customernumber'] = $src;

        // Skip need2call if already has
        if ($pbxData['needtocall'] && $this->findN2C($src)) {
            if ($this->strategy == 'holdFirst') {
                $pbxData['needtocall'] = 0;
            } else {
                $this->resetN2C($src);
            }
        }

        $record = Vtiger_Record_Model::getCleanInstance('PBXManager');
        foreach ($pbxData as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $record->set($key, $value);
        }

        // Detect external caller
        $initiator = $this->findEntity($src);
        if ($initiator) {
            $record->set('customertype', $initiator['setype']);
            $record->set('customer', $initiator['crmid']?:$initiator['id']?:0);
        }

        $record->set('gateway', 'PBXManager');
        $record->set('createdtime', strtotime($pbxData['starttime']));

        // Detect last dialed
        $user = $this->getUser($dst);
        if ($user) {
            $record->set('user', $user['id']);
        }
        $record->set('assigned_user_id', $user?$user['id']:self::DEFASSIGNED);

        // Detect creator
        $creator = false;
        $first = array_shift(explode(',', $pbxData['dialstring']));
        if (strlen($first) < self::MAX_LENGTH) {
            $creator = $this->getUser($first);
        }
        $record->set(
            'created_user_id',
            $creator? $creator['id'] : self::DEFASSIGNED
        );

        // try / catch?
        $record->save();

        return $record->getId();
    }

    /**
     * Retrive options
     */
    function getConfig()
    {
        $cfg = new PBXManager_Config_Model;
        return $cfg->getModOpts();
    }

    function getConnector()
    {
        return new PBXManager_PBXManager_Connector;
    }

    /**
     * Map connector fields to PBXManager fields
     *
     * @return array
     */
    public function getMapping()
    {
        return [
            //'sourceuuid'       => 'mongoID',
            'sourceuuid'       => 'sourceuuid',
            'direction'        => 'direction',
            'customernumber'   => 'customernumber',
            'usernumber'       => 'usernumber',
            'incominglinename' => 'connectedlinenum',
            'callstatus'       => 'callstatus',
            'billduration'     => 'billduration',
            'totalduration'    => 'totalduration',
            'dialstring'       => 'dialstring',
            'recordingurl'     => 'recordingurl',
            'starttime'        => 'starttime',
            'endtime'          => 'endtime',
            'needtocall'       => 'needtocall',
            'dialstring'       => 'dialstring',
        ];
    }

    /**
     * Fill pbx data with request data
     *
     * @param array          $map fields map
     * @param Vtiger_Request $req request object
     *
     * @return array pbx keys, request values
     */
    public function convertToCrm(Array $map, Vtiger_Request $req)
    {
        $data = [];
        foreach ($map as $crm => $connector) {
            $data[$crm] = $req->get($connector);
        }
        $format = '%F %T';
        $data['starttime']  = strftime($format, $data['starttime']);
        $data['endtime']    = strftime($format, $data['endtime']);
        $data['needtocall'] = (int)($data['needtocall'] == "true");
        $data['dialstring'] = implode(
            ',',
            array_unique(explode(',', $data['dialstring']))
        );

        return $data;
    }

    /**
     * Client specific source phone processing
     * TODO preprocessors
     *
     * @param str $phone  caller phone number
     * @param str $prefix project specific
     *
     * @return sanitized / extended phone number
     */
    public function processSrc($phone, $prefix = '8')
    {
        $raw = $phone;
        $clean = preg_replace('/[^\d]*/', '', $phone);

        /*
        // force 11 digits
        if (preg_match('/^[498]\d{9}$/', $clean)) {
            $clean = $prefix . $clean;
        }
        */

        return $clean;
    }

    /**
     * Detect if call is in finished state
     * Scope: Module
     *
     * @param str $status request callstatus field
     *
     * @return bool
     */
    public function isAnswered($status)
    {
        return in_array($status, ['ANSWER']);
    }

    /**
     * method to remove needtocall flag from previous calls
     * in case if call was answered
     * vtlib is for event handlers
     * Scope: Module
     *
     * @param str $phone
     *
     * @return bool | array of updated ids
     */
    public function resetN2C($phone)
    {
        return $this->sqlClean($phone);
        /*
        $calls = $this->findNeed2Call($phone);
        if (count($calls) == 0) return false;
        $processed = $this->cleanN2C($calls);
        */
    }

    /**
     * Phone lookup + needtocall is set
     * Scope: Module
     *
     * @param str $phone
     *
     * @return bool | array id list
     */
    public function findN2C($phone)
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT crmid FROM vtiger_pbxmanager
            INNER JOIN vtiger_crmentity ON pbxmanagerid = crmid
            WHERE deleted = 0
            AND needtocall = 1
            AND customernumber LIKE ?';
        $calls = $db->pquery($sql, [$phone]);
        $nr = $db->num_rows($calls);

        if ($nr == 0) return false;

        $data = [];
        while ($row = $db->fetch_array($calls)) {
            $data[] = $row['crmid'];
        }

        return $data;
    }

    public function cleanN2C($ids)
    {
        $updates = [];
        array_map(
            function ($x) use ($updates) {
                $updates[$x] = ['needtocall', 0];
            },
            $ids
        );
    }

    /**
     * Passive(no save) set fields on record
     *
     * @param array $data [$id => [$field => $value]]
     *
     * @return void
     */
    public function vtUpdate($data)
    {
        foreach ($data as $id => $fields) {
            $rec = Vtiger_Record_Model::getInstanceById($id);
            if (!$rec) continue;
            $this->setFields($fields);
        }
    }

    /**
     * Reset N2C by phone
     * Scope: Module
     *
     * @param string $phone truncated number
     *
     * @param int affected
     */
    public function sqlClean($phone)
    {
        if (empty($phone)) return false;

        $mask = '%' . substr($phone, -10, 10);
        $db = PearDatabase::getInstance();
        $sql = 'UPDATE vtiger_pbxmanager SET needtocall = 0
            WHERE needtocall = 1
              AND customernumber LIKE ?';
        $result = $db->pquery($sql, [$mask]);

        return $db->getAffectedRowCount($result);
    }

    /**
     * setData is destructive - set by field
     * Scope: Record
     *
     * @param PBXManager_Record_Model $record reference
     * @param array                   $data   field => value
     *
     * @return void
     */
    public function setFields(&$record, $data)
    {
        foreach ($data as $k => $v) {
            $record->set($k, $v);
        }
    }

    /*---------------------- Deprecated below this line ---------------------------*/

    /**
     * Function to process the request
     *
     * @param <array> $request call details
     *
     * @return Response object
     */
    function process0($request)
    {
        $mode = $request->get('callstatus');
        switch ($mode) {
            case "StartApp" :
                $this->processStartupCall($request);
                break;
            case "DialAnswer" :
                $this->processDialCall($request);
                break;
            case "Record" :
                $this->processRecording($request);
                break;
            case "EndCall" :
                $this->processEndCall($request);
                break;
            case "Hangup" :
                $callCause = $request->get('causetxt');
                if ($callCause == "null") {
                    break;
                }
                $this->processHangupCall($request);
                break;
            //SalesPlatform.ru begin alternative start call detection mode
            case "DialBegin" :
                $this->processDialBeginCall($request);
                break;
            //SalesPlatform.ru end
            case "User" :
                $ext = $request->get('ext');
                $this->toJson([
                        'q' =>  $ext,
                        'result' => $this->getUser($ext)
                ]);
                break;
            case "List" :
                $ls = PBXManager_Record_Model::getUserNumbers();
                $this->toJson([
                        'q' => 'list',
                        'result' => $ls,
                ]);
                break;
            case "Version" :
                $pbx = Vtiger_Module_Model::getInstance('PBXManager');
                $this->toJson(['Ver' => $pbx->version]);
                break;
            case "Lookup" :
                $num = $request->get('num');
                $this->toJson([
                        'q' => $num,
                        'result' => $this->findEntity($num)
                ]);
                break;
        }
    }

    /**
     * Function to process Incoming call request
     * @params <array> incoming call details
     * return Response object
     */
    function processStartupCall($request)
    {
        $connector = $this->getConnector();

        $temp = $request->get('channel');
        $temp = explode("-", $temp);
        $temp = explode("/", $temp[0]);

        $callerNumber = $request->get('callerIdNumber');
        $userInfo = PBXManager_Record_Model::getUserInfoWithNumber($callerNumber);

        if (!$userInfo) {
            $callerNumber = $temp[1];
            if (is_numeric((int)$callerNumber)) {
                $userInfo = PBXManager_Record_Model::getUserInfoWithNumber($callerNumber);
            }
        }

        if ($userInfo) {
            // Outbound Call
            $request->set('Direction', 'outbound');

            if ($request->get('callerIdNumber') == $temp[1]) {
                $to = $request->get('callerIdName');
            } else if ($request->get('callerIdNumber')) {
                $to = $request->get('callerIdNumber');
            } else if ($request->get('callerId')) {
                $to = $request->get('callerId');
            }

            $request->set('to', $to);
            $customerInfo = PBXManager_Record_Model::lookUpRelatedWithNumber($to);
            $connector->handleStartupCall($request, $userInfo, $customerInfo);
        } else {
            // Inbound Call
            $request->set('Direction', 'inbound');
            $customerInfo = PBXManager_Record_Model::lookUpRelatedWithNumber($request->get('callerIdNumber'));
            $request->set('from', $request->get('callerIdNumber'));
            $connector->handleStartupCall($request, $userInfo, $customerInfo);
        }
    }

    /**
     * Function to process Dial call request
     * @params <array> Dial call details
     * return Response object
     */
    function processDialCall($request)
    {
        $connector = $this->getConnector();
        $connector->handleDialCall($request);
    }

    /**
     * Function to process recording
     * @params <array> recording details
     * return Response object
     */
    function processRecording($request)
    {
        $connector = $this->getConnector();
        $connector->handleRecording($request);
    }

    /**
     * Function to process EndCall event
     * @params <array> Dial call details
     * return Response object
     */
    function processEndCall($request)
    {
        $connector = $this->getConnector();
        $connector->handleEndCall($request);
    }

    /**
     * Function to process Hangup call request
     * @params <array> Hangup call details
     * return Response object
     */
    function processHangupCall($request)
    {
        $connector = $this->getConnector();
        $connector->handleHangupCall($request);
    }

    /**
     * Function to process Incoming call request
     * @params <array> incoming call details
     * return Response object
     */
    function processDialBeginCall($request)
    {
        $request->set('StartTime', date("Y-m-d H:i:s"));
        $callerNumber = $request->get('callerIdNumber');
        /* Get dialed number by caller. It has unified format so we need check variants */
        $destinationNumber = '';
        if (strpos($request->get('dialString'), "/") !== false) {
            $dialParts = explode("/", $request->get('dialString'));
            $destinationNumber = end($dialParts);
        } elseif (strpos($request->get('dialString'), "@") !== false) {
            $dialParts = explode("@", $request->get('dialString'));
            $destinationNumber = $dialParts[0];
        } else {
            $destinationNumber = $request->get('dialString');
        }

        $destinationNumber = str_replace(['FMGL-', '#'], '', $destinationNumber);

        /* If not Originate event - prepare begin of call */
        if ($callerNumber != $destinationNumber) {
            $callerUserInfo = PBXManager_Record_Model::getUserInfoWithNumber($callerNumber);

            // PINstudio @binizik
            $request->set('usernumber', $callerNumber);

            if (!$callerUserInfo['id']) {
                $channelPars = true;
                $callerUserInfo = PBXManager_Record_Model::getUserInfoWithNumber(explode('/', explode('-', $request->get('channel'))[0])[1]);
                $request->set('usernumber', explode('/', explode('-', $request->get('channel'))[0])[1]);
            }
            // PINstudio end

            /* If caller number binded with crm user - it outgoing number */
            $connector = $this->getConnector();
            if ($callerUserInfo) {
                $request->set('Direction', 'outbound');
                $request->set('to', $destinationNumber);
                $customerInfo = PBXManager_Record_Model::lookUpRelatedWithNumber($destinationNumber, $callerUserInfo['id']);
                $connector->handleStartupCall($request, $callerUserInfo, $customerInfo);
            } else {

                /* If no match of twon numbers for crm users - don't fix ring */
                $crmUserInfo = PBXManager_Record_Model::getUserInfoWithNumber($destinationNumber);
                $request->set('usernumber', $destinationNumber); // PINstudio @binizik
                if (!$crmUserInfo) {
                    return;
                }

                $request->set('Direction', 'inbound');
                $request->set('from', $request->get('callerIdNumber'));
                $customerInfo = PBXManager_Record_Model::lookUpRelatedWithNumber($request->get('callerIdNumber'), $crmUserInfo['id']);
                $connector->handleStartupCall($request, $crmUserInfo, $customerInfo);
            }
        }
    }

    public function getUser($ext)
    {
        if (empty($ext)) return false;
        $user = PBXManager_Record_Model::getUserInfoWithNumber($ext);

        return empty($user)? false : $user;
    }

    public function findEntity($num)
    {
        if (empty($num)) return false;
        $entity = PBXManager_Record_Model::lookUpRelatedWithNumber($num);

        return empty($entity)? false : $entity;
    }

    public function toJson($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
