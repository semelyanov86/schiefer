<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

require_once 'include/utils/utils.php';
require_once 'vtlib/Vtiger/Net/Client.php';

class PBXManager_PBXManager_Connector
{
    private static $SETTINGS_REQUIRED_PARAMETERS = [
        'webappurl'       => 'text',
        'outboundcontext' => 'text',
        //'outboundtrunk' => 'text',  // SalesPlatform.ru
        'vtigersecretkey' => 'text',
        'logtype'         => 'text',
        'logfilename'     => 'text'
    ];
    private static $RINGING_CALL_PARAMETERS = [
        'From'          => 'callerIdNumber',
        'SourceUUID'    => 'callUUID',
        'Direction'     => 'Direction',
        'IncomingLineName' => 'connectedLineName'
    ];
    private static $NUMBERS = array();
    private $webappurl;
    private $logtype;
    private $logFileName;
    public  $logFilePath = 'logs/';
    private $defFileName = 'PBXmanager_log';
    private $outboundcontext, $outboundtrunk;
    private $vtigersecretkey;
    const RINGING_TYPE  = 'ringing';
    const ANSWERED_TYPE = 'answered';
    const HANGUP_TYPE   = 'hangup';
    const RECORD_TYPE   = 'record';

    const INCOMING_TYPE = 'inbound';
    const OUTGOING_TYPE = 'outbound';
    const USER_PHONE_FIELD = 'phone_crm_extension';

    function __construct()
    {
        $serverModel = PBXManager_Server_Model::getInstance();
        $this->setServerParameters($serverModel);
        $this->logFilePath = realpath('.') . '/logs/';
    }

    /**
     * Function to get provider name
     * @return string
     */
    public function getGatewayName()
    {
        return 'PBXManager';
    }

    public function getPicklistValues($field)
    {
    }

    public function getServer()
    {
        return $this->webappurl;
    }

    public function getOutboundContext()
    {
        return $this->outboundcontext;
    }

    public function getOutboundTrunk()
    {
        return $this->outboundtrunk;
    }

    public function getVtigerSecretKey()
    {
        return $this->vtigersecretkey;
    }

    public function getLogType()
    {
        return $this->logtype;
    }

    public function getLogPath()
    {
        return $this->logFilePath . $this->logFileName;
    }

    /**
     * Write logs
     * @param array/object
     */
    public function logRequest($data)
    {
        $logger    = $this->getLogType();

        switch ($logger) {
            case 'none' : break;
            case 'file' :
                file_put_contents(
                    $this->getLogPath(),
                    date('Y-m-d H:i:s / ') . json_encode($data)."\n\n",
                    FILE_APPEND
                );
            break;
            case 'table':
                $db = PearDatabase::getInstance();
                $query = 'INSERT INTO vtiger_pbxlogs (data) values (?)';
                $dbResult = $db->pquery($query, [json_encode($data)]);
            break;
        }
    }

    /**
     * Function to make outbound call
     * @param <string> $number (Customer)
     * @param <string> $recordid
     */
    function call($number, $record, $exten = false)
    {
        if ($exten) {
            $extension = $exten;
        } else {
            $user = Users_Record_Model::getCurrentUserModel();
            $extension = $user->phone_crm_extension;
        }

        $webappurl = $this->getServer();
        $context = $this->getOutboundContext();
        $vtigerSecretKey = $this->getVtigerSecretKey();

        $serviceURL  =  $webappurl . '/makecall';
        $args = [
            'event' => 'OutgoingCall',
            'secret' => urlencode($vtigerSecretKey),
            'from' => urlencode($extension),
            'to' => urlencode($number),
            'context' => urlencode($context),
        ];

        $httpClient = new Vtiger_Net_Client($serviceURL);
        $response = $httpClient->doPost($args);
        $response = trim($response);

        $data = ['url'=> $serviceURL, 'response' => $response];
        $this->logRequest($data);

        return !in_array($response, ["Error", "", null, "Authentication Failure"]);
    }

    function redirectCall($to, $callId)
    {
        $user = Users_Record_Model::getCurrentUserModel();

        $webappurl = $this->getServer();
        $context = $this->getOutboundContext();
        $vtigerSecretKey = $this->getVtigerSecretKey();

        $serviceURL  =  $webappurl . '/redirect';
        $args = [
            'recordid' => $callId,
            'to' => $to,
            'context' => $context,
            'secret' => $vtigerSecretKey
        ];

        $httpClient = new Vtiger_Net_Client($serviceURL);
        $response = $httpClient->doPost($args);

        $response = trim($response);

        return $response;
    }

    /**
     * Function to set server parameters
     * @param <array>  authdetails
     */
    public function setServerParameters($serverModel)
    {
        $this->webappurl       = $serverModel->get('webappurl');
        $this->outboundcontext = $serverModel->get('outboundcontext');
        $this->outboundtrunk   = $serverModel->get('outboundtrunk');
        $this->vtigersecretkey = $serverModel->get('vtigersecretkey');
        $this->logtype         = $serverModel->get('logtype');
        if ($this->logtype == 'file') {
            $savedFileName = $serverModel->get('logfilename');
            $this->logFileName = empty($savedFileName)? $this->defFileName : $savedFileName;
        }
    }

    /**
     * Function to get Settings edit view params
     * returns <array>
     */
    public function getSettingsParameters()
    {
        return self::$SETTINGS_REQUIRED_PARAMETERS;
    }

    protected function prepareParameters($details, $type)
    {
        switch ($type) {
            case 'ringing':
                foreach (self::$RINGING_CALL_PARAMETERS as $key => $value) {
                    $params[$key] = $details->get($value);
                }
                $params['GateWay'] = $this->getGatewayName();
                break;
        }
        return $params;
    }

    public function getXmlResponse()
    {
        header("Content-type: text/xml; charset=utf-8");
        $response = '<?xml version="1.0" encoding="utf-8"?>';
        $response .= '<Response><Authentication>';
        $response .= 'Failure';
        $response .= '</Authentication></Response>';
        return $response;
    }
}
