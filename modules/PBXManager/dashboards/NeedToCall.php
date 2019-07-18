<?php
//PINstudio begin @vladimir.ch red-867
class PBXManager_NeedToCall_Dashboard extends Vtiger_IndexAjax_View {
    
    public function process(Vtiger_Request $request, $widget=NULL) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
        
        // Initialize Widget to the right-state of information
        if ($widget && !$request->has('widgetid')) {
            $widgetId = $widget->get('id');
        } else {
            $widgetId = $request->get('widgetid');
        }
        
        $viewName = 'LBL_NEED_TO_CALL';
        
        $data = [
            "module" => "PBXManager",
            "fields" => ["customernumber","customer","createdtime"],
        ];
        
        $widget = Vtiger_Widget_Model::getInstanceWithWidgetId($widgetId, $currentUser->getId());
        $widget->set('data', $data);
        $filter = Vtiger_Filter::getInstance($viewName, $moduleInstance);
        if ($filter === false) {
            // создаем фильтр
            $PBXManagerInstance = CRMEntity::getInstance('PBXManager');
            $filter = $PBXManagerInstance->addFilter();
        }
        
        $widget->set('filterid', $filter->id);
        
        $minilistWidgetModel = new Vtiger_MiniList_Model();
        $minilistWidgetModel->setWidgetModel($widget);
        
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
        return $this->checkAndConvertJsScripts(array('modules.Emails.resources.MassEdit'));
    }
}
//PINstudio end