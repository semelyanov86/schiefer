<?php

class PBXManager_Check_Action extends PBXManager_Ajax_Action
{
    public function process(Vtiger_Request $request)
    {
        $id = $request->get('id');
        $data = $this->check();
        $this->_emit($data);
    }

    public function check()
    {
        return [
            [
                'key' => 'hasPhonePrompt',
                'status' => $this->checkPhonePrompt(),
                'msg' => 'UI: Has mandatory phone request on login',
            ],
            [
                'key' => 'hasPhoneOverride',
                'status' => $this->checkExtOverride(),
                'msg' => 'Users: Unique exten handler',
            ],
            [
                'key' => 'hasExten',
                'status' => $this->checkUserExt(),
                'msg' => 'Users: Field phone_crm_extension exists',
            ],
            [
                'key' => 'hasUsernum',
                'status' => $this->checkPBXField('usernumber'),
                'msg' => 'PBXManager: Field usernumber exists',
            ],
            [
                'key' => 'hasN2C',
                'status' => $this->checkPBXField('needtocall'),
                'msg' => 'PBXManager: Field needtocall exists',
            ],
            [
                'key' => 'hasDialstring',
                'status' => $this->checkPBXField('dialstring'),
                'msg' => 'PBXManager: Field dialstring exists',
            ],
            [
                'key' => 'hasDirection16',
                'status' => $this->checkDirection(),
                'msg' => 'PBXManager: Field direction should be list type',
            ],
            [
                'key' => 'hasLogs',
                'status' => $this->checkLogs(),
                'msg' => 'PBXManager: Logs exists',
            ],
            [
                'key' => 'hasOpts',
                'status' => $this->checkOpts(),
                'msg' => 'PBXManager: Options exists',
            ],
            [
                'key' => 'hasFlash',
                'status' => $this->checkPanel(),
                'msg' => 'PBXManager: Panel settings exists',
            ],
        ];
    }

    public function checkPhonePrompt()
    {
        global $show_phone_field;
        return isset($show_phone_field);
    }

    public function checkExtOverride()
    {
        return method_exists('Users_Module_Model', 'updateExten');
    }

    public function checkUserExt()
    {
        $name = 'phone_crm_extension';
        $instance = Vtiger_Module_Model::getInstance('Users');
        $field = Vtiger_Field_Model::getInstance($name, $instance);
        return !!$field;
    }

    public function checkPBXField($name)
    {
        $pbx = Vtiger_Module_Model::getInstance('PBXManager');
        $field = Vtiger_Field_Model::getInstance($name, $pbx);
        return !!$field;
    }

    public function checkDirection()
    {
        $name = 'direction';
        $pbx = Vtiger_Module_Model::getInstance('PBXManager');
        $field = Vtiger_Field_Model::getInstance($name, $pbx);
        return $field->uitype == 16;
    }

    public function checkLogs()
    {
        return $this->checkTable('vtiger_pbxlogs');
    }

    public function checkOpts()
    {
        return $this->checkTable('vtiger_pbxopts');
    }

    public function checkPanel()
    {
        return $this->checkTable('vtiger_pbxpanel');
    }

    public function checkTable($name)
    {
	$db = PearDatabase::getInstance();
	$result = $db->query("SHOW TABLES LIKE '{$name}'");
	$tabs = $result->GetAll();
	return !empty($tabs);
    }
}
