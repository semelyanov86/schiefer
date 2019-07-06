<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class PBXManager_ListenRecord_Action extends Vtiger_Action_Controller {

/*
    PINstudio

    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();

        if(!Users_Privileges_Model::isPermitted($moduleName, 'ListView', $request->get('record'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }

    public function process(Vtiger_Request $request) {
        $pbxRecordModel = PBXManager_Record_Model::getInstanceById($request->get('record'));
        if($pbxRecordModel->get('recordingurl') == null)  return 0;

        $curl = $this->prepareCurl($pbxRecordModel);
        $response = curl_exec($curl);
        $requestInfo = curl_getinfo($curl);
        curl_close($curl);
        //w(var_export($requestInfo,true));
        //TODO provide "Not Found" Audio file
        if( $requestInfo !== false
            && ($requestInfo['http_code'] == 200)
            && ($requestInfo['size_download'] > 10) // 'Not found'
        ) {
            $headerSize = $requestInfo['header_size'];
            $headerContent = substr($response, 0, $headerSize);
            $bodyContent = substr($response, $headerSize);
            
            $headersList = $this->getHeadersList($headerContent);
            header('Content-Type: ' . $headersList['content-type']);
            header('Content-Length: ' . $headersList['content-length']);
            header('Content-disposition: ' . $headersList['content-disposition']);
            echo $bodyContent;
            return;
        }

       	header("HTTP/1.0 404 Not Found");
    	echo 'No audio';
    }
    
    private function prepareCurl($pbxRecordModel) {
        $pbxSettinsModel = PBXManager_Server_Model::getInstance();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        $url = $pbxRecordModel->get('recordingurl') . "&secret=" . urlencode($pbxSettinsModel->get('vtigersecretkey'));
        curl_setopt($curl, CURLOPT_URL, $url);

        return $curl;
    }
    
    private function getHeadersList($headerContent) {
        $headersList = array();
        foreach(explode("\r\n", $headerContent) as $number => $header) {
            if($number == 0) {
                $headersList['http_code'] = $header;
            } else {
               list($headerName, $headerValue) = explode(': ', $header); 
               $headersList[strtolower($headerName)] = trim($headerValue);
            }
        }
        
        return $headersList;
    }
    
*/
}
