<?php

class PBXManager_Panel_View extends Vtiger_Index_View
{
    function process(Vtiger_Request $req)
    {
        $viewer = $this->getViewer($req);
        $viewer->view('Panel.tpl', 'PBXManager');
    }
}
