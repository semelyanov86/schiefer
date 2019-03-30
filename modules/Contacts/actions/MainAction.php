<?php

class Contacts_MainAction_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
    }
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod("clearDublicates");
    }
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get("mode");
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
        }
    }
    public function clearDublicates(Vtiger_Request $request)
    {
        global $adb;
        $contactsModel = Vtiger_Module_Model::getInstance('Contacts');
        $url = $contactsModel->getListViewUrl();
        $module = $request->getModule();
        $result = $adb->pquery("SELECT contactid,cf_1137 FROM vtiger_contactscf INNER JOIN vtiger_crmentity ON vtiger_contactscf.contactid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0;", array());
        if($adb->num_rows($result) >= 1)
        {
            while($result_set = $adb->fetch_array($result))
            {
                $contactid = $result_set["contactid"];
                $cf_1137 = $result_set["cf_1137"];
                $result1 = $adb->pquery("SELECT contactid,cf_1137 FROM vtiger_contactscf INNER JOIN vtiger_crmentity ON vtiger_contactscf.contactid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_contactscf.cf_1137 = ? ORDER BY modifiedtime Asc;", array($cf_1137));
                if ($adb->num_rows($result1) >= 2) {
                    $curDubles = array();
                    while ($result_set1 = $adb->fetch_array($result1))
                    {
                        $curDubles[] = $result_set1["contactid"];
                        $curCf = $result_set1["cf_1137"];
                    }
                    $this->doClear($curDubles, $curCf);
                }
            }

        }
        header("Location: $url");
        die;
    }

    public function doClear($dubl, $cf_1137)
    {
        foreach ($dubl as $value) {
            $contactModel = Vtiger_Record_Model::getInstanceById($value, 'Contacts');
        }
        var_dump($contactModel);die;
    }
}

?>