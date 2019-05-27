<?php
function UpdateNummer($ws_entity){
    // WS id
    $ws_id = $ws_entity->getId();
    $module = $ws_entity->getModuleName();
    if (empty($ws_id) || empty($module)) {
        return;
    }

    // CRM id
    $crmid = vtws_getCRMEntityId($ws_id);
    if ($crmid <= 0) {
        return;
    }

    //получение объекта со всеми данными о текущей записи Модуля "MyModule"
    $zertificatInstance = Vtiger_Record_Model::getInstanceById($crmid);

    //получение No zertificat
    $zeNo = $zertificatInstance->get('zertifikatno');

    if($zeNo) {

        global $VTIGER_BULK_SAVE_MODE;
        $previousBulkSaveMode = $VTIGER_BULK_SAVE_MODE;
        $VTIGER_BULK_SAVE_MODE = true;

        $date = date('y/m');

        //объект в режиме редактирования
        $zertificatInstance->set('mode', 'edit');

        //запись Даты в поле “Срок оплаты”
        $zertificatInstance->set('cf_1366', 'ZE/' . $date . '/' . $zeNo);

        //сохранение
        $zertificatInstance->save();

        $VTIGER_BULK_SAVE_MODE = $previousBulkSaveMode;
    }
}