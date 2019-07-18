<?php

class PBXManager_Ajax_Action extends Vtiger_BasicAjax_Action
{
    /**
     * getInstanceById wrapper
     *
     * @param int $id crmid
     *
     * @return bool|Vtiger_Record_Model
     */
    public function safeGet($id)
    {
        try {
            return Vtiger_Record_Model::getInstanceById($id);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * response wrapper
     *
     * @param mix $x    some message / object
     * @param int $code flag defining error mode
     *
     * @return void
     */
    protected function _emit($data, $code = false)
    {
        $response = new Vtiger_Response();
        if (is_bool($code)) {
            $response->setResult($data);
        } else {
            $response->setError($code, $data);
        }
        $response->emit();
    }
}
