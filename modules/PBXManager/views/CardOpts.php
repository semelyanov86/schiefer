<?php

class PBXManager_CardOpts_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $req)
    {
        $view = $this->getViewer($req);
        $view->view('CardOpts.tpl', 'PBXManager');
    }
}
