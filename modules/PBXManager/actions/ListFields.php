<?php

class PBXManager_ListFields_Action extends PBXManager_Ajax_Action
{
    public function process(Vtiger_Request $req)
    {
        $this->_emit($this->getListing());
    }

    public function getListing()
    {
        $data = [];
        foreach ($this->getModules() as $module) {
            $data[$module] = $this->getFields($module);
        }

        return $data;
    }

    public function getModules()
    {
        return [
            'Leads',
            'Contacts',
            'Accounts',
        ];
    }

    public function getFields($name)
    {
        $data = [];
        $module = Vtiger_Module::getInstance($name);
        $list = Vtiger_Field::getAllForModule($module);
        foreach ($list as $field) {
            $data[$field->id] = $field->label;
        }
        return $data;
    }
}

