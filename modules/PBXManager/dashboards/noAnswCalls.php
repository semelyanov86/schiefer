<?php
// PINstudio @binizik

class PBXManager_noAnswCalls_Dashboard extends Vtiger_IndexAjax_View {

    public function process(Vtiger_Request $request, $widget=NULL) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();

        $minilistWidgetModel = new Vtiger_MiniList_Model();
        $minilistWidgetModel->setWidgetModel(
            false,
            '{"module":"PBXManager","fields":["customernumber","customer","createdtime"]}',
            57,
            $currentUser->getId(),
            'Мои звонки требующие перезвона'
        );

        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('MINILIST_WIDGET_MODEL', $minilistWidgetModel);
        $viewer->assign('BASE_MODULE', $minilistWidgetModel->getTargetModule());
        $viewer->assign('SCRIPTS', $this->getHeaderScripts());

        $content = $request->get('content');
        if(!empty($content)) {
            $viewer->view('dashboards/MiniListContents.tpl', $moduleName);
        } else {
            $widget->set('title', $minilistWidgetModel->getTitle());

            $viewer->view('dashboards/MiniList.tpl', $moduleName);
        }

    }
        
    function getHeaderScripts() {
        return $this->checkAndConvertJsScripts(['modules.Emails.resources.MassEdit']);
    }
}
