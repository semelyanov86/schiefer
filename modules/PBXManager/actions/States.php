<?php

class PBXManager_States_Action extends PBXManager_Ajax_Action
{
    private $endpoint = '/sys/inf';
    public $protocol = 'http';

    function process(Vtiger_Request $req)
    {
        if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $this->protocol = 'https';
        }
        $states = $this->getData();

        return $this->_emit($states);
    }

    /**
     * Extract operation from process mtd
     *
     * @return array
     */
    function getData()
    {
        $cfg = new PBXManager_Config_Model;
        $globs = $cfg->getGlobals();
        $host = $globs['host'] . ':' . $globs['port'];
        // protocol already defined
        $prefix = (substr($host, 0, 4) == 'http')
            ? ''
            : $this->protocol;
        $url = $prefix . ':' . $host . $this->endpoint;
        $result = $this->getStates($url);

        return ($result['code'] == 200)
            ? $result['data']
            : $result;
    }

    /**
     * TODO move to connector
     */
    function getStates($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        // TODO SSL verify peer
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $http = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $data = [];
        $raw = json_decode($response, 1);
        if (!json_last_error()) {
            $data = $raw['extens_status'];
        }

        return [
            'url'    => $url,
            'code'   => $http,
            'status' => $error,
            'data'   => $data,
        ];
    }
}
