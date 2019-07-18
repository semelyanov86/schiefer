<?php
// PINstudio @binizik

class PBXManager_CallStat_Dashboard extends Vtiger_IndexAjax_View {

    public function process(Vtiger_Request $request, $widget=NULL) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $linkId = $request->get('linkid');
        $data = $request->get('data');
        $createdTime = $request->get('createdtime');

        if(!empty($createdTime)) {
            $dates['start'] = Vtiger_Date_UIType::getDBInsertedValue($createdTime['start']);
            $dates['end'] = Vtiger_Date_UIType::getDBInsertedValue($createdTime['end']);
        }

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $data = $this->getCallStat($dates);
        $listViewUrl = $moduleModel->getListViewUrl();
        for($i = 0;$i<count($data);$i++){
            //SalesPlatform.ru begin fix filters
            $data[$i]["links"] = $listViewUrl.$this->getSearchParams($data[$i][2],$request->get('smownerid'),$dates);
            //$data[$i]["links"] = $listViewUrl.$this->getSearchParams($data[$i][1],$request->get('smownerid'),$dates);
            //SalesPlatform.ru end
        }

        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

        //Include special script and css needed for this widget

        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('DATA', $data);
        $viewer->assign('CURRENTUSER', $currentUser);

        $accessibleUsers = $currentUser->getAccessibleUsersForModule('PBXManager');
        $viewer->assign('ACCESSIBLE_USERS', $accessibleUsers);
        $content = $request->get('content');
        if(!empty($content)) {
            $viewer->view('dashboards/DashBoardWidgetContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/CallsStatistics.tpl', $moduleName);
        }

    }

    function getHeaderScripts() {
        return $this->checkAndConvertJsScripts(['modules.Emails.resources.MassEdit']);
    }


    /**
     * Function returns Calls grouped by External Line
     * @param type $data
     * @return <Array>
     */
    public function getCallStat($dateFilter) {
        $db = PearDatabase::getInstance();

        $params = array();
        if(!empty($dateFilter)) {
            $dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
            //client is not giving time frame so we are appending it
            $params[] = $dateFilter['start']. ' 00:00:00';
            $params[] = $dateFilter['end']. ' 23:59:59';
        }

        $result = $db->pquery("select count(*) as count, incominglinename from vtiger_pbxmanager INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid AND deleted=0 where `status` = 'inbound' and " . $dateFilterSql . " group by incominglinename", $params);

        $response = array();
        for($i=0; $i<$db->num_rows($result); $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $response[$row['incominglinename']] = $row['count'];
        }
        return $response;
    }
}
