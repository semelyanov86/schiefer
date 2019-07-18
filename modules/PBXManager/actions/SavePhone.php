<?php

class PBXManager_SavePhone_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $req)
    {
        $id = $req->get('id');
        $phone = $req->get('phone');

        if (empty($phone)) {
            $this->_emit('Phone number required', 10);
            return;
        }

        $contact = false;
        try {
            $contact = Vtiger_Record_Model::getInstanceById($id);
        } catch (Exception $e) {
            $this->_emit('Invalid id ' . $id, 11);
            return;
        }

        $result = $this->setEmptyPhone($contact, $phone);
        $this->_emit($result);
    }

    public function setEmptyPhone(&$record, $phone)
    {
        $fields = [
            'mobile',
            'phone',
            'otherphone',
            'homephone',
        ];

        $target = false;
        foreach ($fields as $field) {
            if (!empty($record->get($field))) {
                continue;
            }

            $target = $field;
            break;
        }

        if (empty($target)) {
            //overwriting homephone
            $target = 'homephone';
        }

        $record->set('mode', 'edit');
        $record->set($target, $phone);
        $record->save();

        return $record->getId();
    }

    /**
     * response wrapper
     */
    function _emit($x, $code = false)
    {
        $res = new Vtiger_Response;
        $res->setEmitType(Vtiger_Response::$EMIT_JSON);
        if ($code) {
            $res->setError($code, $x);
        } else {
            $res->setResult($x);
        }
        $res->emit();
    }
}
