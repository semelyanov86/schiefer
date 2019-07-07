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
        $fieldsArr = array();
        if ($sourceModule == 'SalesOrder') {
            $fieldsArr['cf_1137'] = Vtiger_Field_Model::getInstance('cf_1137', $contactsModel);
            $fieldsArr['customerno'] = Vtiger_Field_Model::getInstance('customerno', $orderModel);
            $fieldsArr['cf_1487'] = Vtiger_Field_Model::getInstance('cf_1487', $moduleModel);
//        $fieldsArr['assigned_user_id'] = Vtiger_Field_Model::getInstance('assigned_user_id', $orderModel);
            $fieldsArr['qtyinstock'] = Vtiger_Field_Model::getInstance('qtyinstock', $moduleModel);
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
                    $result = array();
                    $result["_recordLabel"] = $orderModel->getName();
                    $result["_recordId"] = $orderModel->getId();
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
            $diff = $productModel->get('qtyinstock') - $productModel->tofloat($qty);
//            var_dump($diff, $productModel->get('qtyinstock'), $qty, str_replace('.', ',', $diff));die;
            $productModel->set('cf_1503', str_replace('.', ',', $diff));
            $productModel->save();
        }
        return $productModel;
    }
}
