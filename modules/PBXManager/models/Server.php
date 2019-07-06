<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'vtlib/Vtiger/Net/Client.php';

class PBXManager_Server_Model extends Vtiger_Base_Model{

    const tableName = 'vtiger_pbxmanager_gateway';

    public static function getCleanInstance()
    {
        return new self;
    }

    /**
     * Static Function Server Record Model
     * @params <string> gateway name
     * @return PBXManager_Server_Model
     */
    public static function getInstance($refresh = false)
    {
        static $serverModel = false;

        if (!$refresh && $serverModel) return $serverModel;

        $serverModel = new self();
        $db = PearDatabase::getInstance();
        $gatewayResult = $db->query('SELECT * FROM ' . self::tableName);
        $nr = $db->num_rows($gatewayResult);

        if ($nr == 0) {
            return $serverModel;
        }

        $rowData = $db->fetch_array($gatewayResult);
        $serverModel->set('gateway', $rowData['gateway']);
        $serverModel->set('id', $rowData['id']);
        $parameters = json_decode(decode_html($rowData['parameters']), 1);
        foreach ($parameters as $fieldName => $fieldValue) {
            $serverModel->set($fieldName, $fieldValue);
        }

        return $serverModel;
    }

    public static function checkPermissionForOutgoingCall()
    {
        Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = Users_Privileges_Model::isPermitted('PBXManager', 'MakeOutgoingCalls');

        $serverModel = PBXManager_Server_Model::getInstance();
        $gateway = $serverModel->get('gateway');

        return ($permission && $gateway);
    }

    public static function generateVtigerSecretKey()
    {
        return uniqid(rand());
    }

    public function getConnector()
    {
        return new PBXManager_PBXManager_Connector;
    }

    /**
     * View method
     *
     * @return <string> connector status
     */
    public static function getStatus()
    {
        $status = self::statsRequest();

        $bold = function ($txt, $good = false) {
            $css = $good?'btn-success':'btn-warning';
            return sprintf('<b class="padding1per %s">%s</b>', $css, $txt);
        };

        if (empty($status) || array_key_exists('err', $status)) {
            return $bold('Unknown ' . $status['err']);
        }

        $connectorState = 'OK';

        $response = [];
        foreach ($status as $key => $value) {
            $response[] = $key . ': ' . $bold($value?'Ok':'Err', $value);
        }

        return implode('', $response);
    }

    /**
     * Get ping request
     *
     * @return arr
     */
    public static function statsRequest()
    {
        $serverModel = self::getInstance();
        $webappurl   = $serverModel->get('webappurl');
        $serviceURL  = $webappurl . '/ping';

        $client  = new Vtiger_Net_Client($serviceURL);

        // V7 PEAR is static
        PEAR::setErrorHandling(
            PEAR_ERROR_CALLBACK,
            function ($err) {
                $code = $err->getCode();
                $msg = $err->getMessage();
                if ($msg) {
                    throw new Exception(self::flatten([
                        'code' => $code,
                        'msg' => $msg,
                        'mode' => $err->getMode(),
                        'type' => $err->getType(),
                    ]));
                }
            }
        );

        try {
            $ping = $client->doGet(false, 10);
        } catch (Exception $e) {
            return [
                'err' => $e->getMessage()
            ];
        }

        if (empty($ping)) {
            return [
                'err' => 'Response: ' . self::flatten([
                    'code' => $client->client->getResponseCode(),
                    'msg' => $client->client->getResponseReason(),
                    'hdr' => $client->client->getResponseHeader(),
                    'body' => $client->client->getResponseBody(),
                ])
            ];
        }

        return json_decode($ping, 1);
    }

    public static function flatten($a)
    {
        $status = [];
        foreach ($a as $k => $v) {
            if (empty($v)) continue;
            $status[] = sprintf('%s:%s', $k, $v);
        }

        return empty($status)? '--' : implode(', ', $status);
    }
}
