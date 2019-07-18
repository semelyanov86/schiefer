<?php

class PBXManager_Logs_Action extends PBXManager_Ajax_Action
{
    function __construct() {
        $this->exposeMethod('show');
        $this->exposeMethod('clean');
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

        if(!$permission) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        $result = $this->invokeExposedMethod($mode, $request);
        $this->_emit($result);

        return;

        $serverModel = PBXManager_Server_Model::getInstance();
        $connector = $serverModel->getConnector();
        $logger    = $connector->getLogType();

        switch ($logger) {
            case 'none' : break;
            case 'file' :
                file_put_contents(
                    $connector->getLogPath(),
                    date('Y-m-d H:i:s / ') . "CleanUp\n"
                );
            break;
            case 'table':
                $db = PearDatabase::getInstance();
                $query = 'TRUNCATE vtiger_pbxlogs';
                $dbResult = $db->pquery($query, []);
            break;
        }

    }

    public function show(Vtiger_Request $req)
    {
        //$limit = $req->get('lmt', 40);
        $limit = 40;
        $total = 0;
        $where = '';
        $qLimits = 'LIMIT ';
        $queryCount = 'SELECT count(*) total FROM vtiger_pbxlogs ';
        $args  = [];
        $suid  = $req->get('filter');
        if ($suid) {
            $where = 'WHERE data LIKE ?';
            $queryCount .= "WHERE data LIKE '%{$suid}%' ";
            $args[] = "%$suid%";
        }
        $ofs   = $req->get('ofs');
        if ($ofs) {
           $qLimits .= "?, ?";
           $args[] = $ofs;
        } else {
           $qLimits .= "?";
        }
        $args[] = $limit;
        $db = PearDatabase::getInstance();
        $query = 'SELECT * FROM vtiger_pbxlogs '
            . $where
            . ' ORDER BY ts DESC '
            . $qLimits;
        $dbResult = $db->pquery($query, $args);
        if (!$dbResult) {
            $logs = 'Error fetching records: ' . $db->database->ErrorMsg();
            return $logs;
        }
        if ($db->num_rows($dbResult) == 0) {
            $logs = '0 records found';
            return $logs;
        }

        $logs = "table contents (limit {$limit}):\n";
        $logs .= '<style>.logs tr:nth-child(odd){background-color: #eee}'
            .'.logs td:first-child {min-width: 120px;}</style>'
            . '<table class="logs table">';
        foreach ($dbResult as $row) {
            $logs .= "<tr><td>{$row['ts']}</td><td>{$row['data']}</td></tr>";
        }
        $logs .= '</table>';

        $dbTotal = $db->query($queryCount);
        $total = $dbTotal->FetchRow()['total'];
        return [
            'total'=> $total,
            'data' => $logs
        ];
    }

    public function clean(Vtiger_Request $req)
    {
        $serverModel = PBXManager_Server_Model::getInstance();
        $connector = $serverModel->getConnector();
        $logger    = $connector->getLogType();

        switch ($logger) {
            case 'none' : break;
            case 'file' :
                file_put_contents(
                    $connector->getLogPath(),
                    date('Y-m-d H:i:s / ') . "CleanUp\n"
                );
            break;
            case 'table':
                $db = PearDatabase::getInstance();
                $query = 'TRUNCATE vtiger_pbxlogs';
                $dbResult = $db->pquery($query, []);
            break;
        }

        return $logger . ': Cleaning things';
    }
}
