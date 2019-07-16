<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Products_MassActionAjax_View extends Vtiger_MassActionAjax_View {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('showSendOrder');
        $this->exposeMethod('showSendPurchase');
        $this->exposeMethod('saveAjax');
        $this->exposeMethod('quickSearch');
        $this->exposeMethod('quickSearchCurrent');

    }

	public function initMassEditViewContents(Vtiger_Request $request) {
		parent::initMassEditViewContents($request);

		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$fieldInfo = array();
		$fieldList = $moduleModel->getFields();
		foreach ($fieldList as $fieldName => $fieldModel) {
			$fieldInfo[$fieldName] = $fieldModel->getFieldInfo();
		}

		$additionalFieldsList = $moduleModel->getAdditionalImportFields();
		foreach ($additionalFieldsList as $fieldName => $fieldModel) {
			$fieldInfo[$fieldName] = $fieldModel->getFieldInfo();
		}

		$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
		$taxDetails = $recordModel->getTaxClassDetails();
		foreach ($taxDetails as $taxkey => $taxInfo) {
			$taxInfo['percentage'] = 0;
			foreach ($taxInfo['regions'] as $regionKey => $regionInfo) {
				$taxInfo['regions'][$regionKey]['value'] = 0;
			}
			$taxDetails[$taxkey] = $taxInfo;
		}

		$viewer->assign('TAXCLASS_DETAILS', $taxDetails);
		$viewer->assign('MASS_EDIT_FIELD_DETAILS', $fieldInfo);
	}
    function showSendOrder(Vtiger_Request $request) {
        global $site_URL;
        $sourceModule = $request->get('source');
        $moduleName = 'Products';
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $orderModel = Vtiger_Module_Model::getInstance($sourceModule);
        $contactsModel = Vtiger_Module_Model::getInstance('Contacts');
        switch ($sourceModule) {
            case 'PurchaseOrder':
                $message = 'Einlage';
                break;
            case 'SalesOrder':
                $message = 'Entnahme';
                break;
            default:
                $message = 'Inventur';
                break;
        }
        $fieldsArr = array();
        if ($sourceModule == 'SalesOrder') {
            $fieldsArr['cf_1137'] = Vtiger_Field_Model::getInstance('cf_1137', $contactsModel);
            $fieldsArr['customerno'] = Vtiger_Field_Model::getInstance('customerno', $orderModel);
//        $fieldsArr['assigned_user_id'] = Vtiger_Field_Model::getInstance('assigned_user_id', $orderModel);
            $fieldsArr['qtyinstock'] = Vtiger_Field_Model::getInstance('qtyinstock', $moduleModel);
            $fieldsArr['cf_1487'] = Vtiger_Field_Model::getInstance('cf_1487', $moduleModel);
        } elseif ($sourceModule == 'PurchaseOrder' || $sourceModule == 'Products') {
            $fieldsArr['cf_1487'] = Vtiger_Field_Model::getInstance('cf_1487', $moduleModel);
//        $fieldsArr['assigned_user_id'] = Vtiger_Field_Model::getInstance('assigned_user_id', $orderModel);
            $fieldsArr['qtyinstock'] = Vtiger_Field_Model::getInstance('qtyinstock', $moduleModel);
        }


        $user = Users_Record_Model::getCurrentUserModel();

        $viewer = $this->getViewer($request);
        $viewer->assign('PARENT_MODEL', $orderModel);
        $viewer->assign('SITEURL', $site_URL);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER_MODEL', $user);
        $viewer->assign('HEADMESSAGE', $message);
        $viewer->assign('BLOCK_FIELDS', $fieldsArr);
        echo $viewer->view('PopupMain.tpl', $moduleName, true);
    }
    function saveAjax(Vtiger_Request $request)
    {
        $parent = $request->get('source');
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $code = $request->get('cf_1487');
        $qty = $request->get('qtyinstock');
        if (!$qty || !$code) {
            $response->setError('408', vtranslate('Quantity and code fields are empty', 'Products'));
        } else {
            $productModel = Products_Record_Model::getProductModelByCode($code);
            if ($parent == 'Products') {
                $tara = $productModel->get('cf_1517');
                $qty = str_replace(',', '.', $qty);
                $qty = $qty - $tara;
                $qty = str_replace('.', ',', $qty);
            }
            if (!$productModel) {
                $response->setError('404', vtranslate('Product with this code not found', 'Products'));
            } else {
                if ($parent == 'Products') {
                    $orderModel = $this->makeInventur($productModel, $qty);
                } else {
                    $orderModel = Vtiger_Record_Model::getCleanInstance($parent);
                    $orderModel = $orderModel->saveFromProductRequest($request);
                    if (!$orderModel) {
                        $response->setError('500', vtranslate('Error in creating entity', 'Products'));
                        $response->emit();
                        return;
                    } elseif (!is_object($orderModel)) {
                        $response->setError('404', $orderModel);
                        $response->emit();
                        return;
                    } else {
                        if ($productModel) {
                            $productRecordModel = $productModel->linkProductWithModel($orderModel, $qty);
                        }
                        if ($parent == 'SalesOrder') {
                            deductFromProductStock($productModel->getId(), $productModel->tofloat($qty));
                        } elseif ($parent == 'PurchaseOrder') {
                            addToProductStock($productModel->getId(), $productModel->tofloat($qty));
                        }
                    }

                }
                if (!$orderModel) {
                    $response->setError('500', vtranslate('Error in creating entity', 'Products'));
                } elseif (!is_object($orderModel)) {
                    $response->setError('404', $orderModel);
                } else {
                    $finalModel = Vtiger_Record_Model::getInstanceById($productModel->getId());
                    $result = array();
                    $result["_recordLabel"] = $finalModel->getName();
                    $result["recordId"] = $finalModel->getId();
                    $result["qtyinstock"] = $finalModel->get('qtyinstock');
                    $result["cf_1501"] = $finalModel->get('cf_1501');
                    $result["cf_1503"] = $finalModel->get('cf_1503');
                    $result["_recordModule"] = $request->get("module");
                    $result['state'] = vtranslate('RECORD_CREATED_SUCCESSFULLY', $request->getModule());

                    $response->setResult($result);
                }

            }
        }

        $response->emit();
    }

    private function makeInventur($productModel, $qty)
    {
        if ($productModel) {
            $productModel->set('mode', 'edit');
            $productModel->set('cf_1501', $qty);
            $diff = $productModel->tofloat($qty) - $productModel->get('qtyinstock');
//            var_dump($diff, $productModel->get('qtyinstock'), $qty, str_replace('.', ',', $diff));die;
            $productModel->set('cf_1503', str_replace('.', ',', $diff));
            $productModel->save();
        }
        return $productModel;
    }

    public function quickSearch(Vtiger_Request $request)
    {
        global $adb;
        $result = array();
        $query = 'SELECT contactid FROM vtiger_contactscf INNER JOIN vtiger_crmentity ON vtiger_contactscf.contactid = vtiger_crmentity.crmid WHERE vtiger_contactscf.cf_1137 LIKE ? AND vtiger_crmentity.deleted = 0 LIMIT 10';
        $rs = $adb->pquery($query, array('%' . $request->get('number') . '%'));
        $noOfUsers = $adb->num_rows($rs);
        for($i=0; $i<$noOfUsers; ++$i) {
            $row = $adb->query_result_rowdata($rs, $i);
            $userId = $row['contactid'];
            $model = Vtiger_Record_Model::getInstanceById($userId, 'Contacts');
            $result[] = array(
                'data' => $model->getName(),
                'value' => $model->get('cf_1137'),
            );
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }
    public function quickSearchCurrent(Vtiger_Request $request)
    {
        global $adb;
        $query = 'SELECT contactid FROM vtiger_contactscf INNER JOIN vtiger_crmentity ON vtiger_contactscf.contactid = vtiger_crmentity.crmid WHERE vtiger_contactscf.cf_1137 = ? AND vtiger_crmentity.deleted = 0 LIMIT 1';
        $rs = $adb->pquery($query, array($request->get('number')));
        if ($adb->num_rows($rs) > 0) {
            $contactid = $adb->query_result($rs, 0, 'contactid');
            $model = Vtiger_Record_Model::getInstanceById($contactid, 'Contacts');
        } else {
            $model = false;
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        if ($model) {
            $response->setResult($model->getName());
        } else {
            $response->setError(404, 'Contact with this number not found!!');
        }
        $response->emit();
    }
}
