<?php

class PBXManager_SetExten_Action extends Vtiger_BasicAjax_Action
{
    function process(Vtiger_Request $req)
    {
        $ext = $req->get('exten');
        $res = new Vtiger_Response;
        if (empty($ext)) {
            $res->setError(10, 'Empty exten');
            return $res->emit();
        }

        $user = Users_Record_Model::getCurrentUserModel();
        if (empty($user)) {
            $res->setError(11, 'No current user');
            return $res->emit();
        }
        $user->set('mode', 'edit');
        $user->set('phone_crm_extension', $ext);
        $user->save();

        $res->setResult('Done');
        $res->emit();
    }
}
