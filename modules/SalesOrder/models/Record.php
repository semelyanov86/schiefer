<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class SalesOrder_Record_Model extends Inventory_Record_Model {

	function getCreateInvoiceUrl() {
		$invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');

		return "index.php?module=".$invoiceModuleModel->getName()."&view=".$invoiceModuleModel->getEditViewName()."&salesorder_id=".$this->getId();
	}

	function getCreatePurchaseOrderUrl() {
		$purchaseOrderModuleModel = Vtiger_Module_Model::getInstance('PurchaseOrder');
		return "index.php?module=".$purchaseOrderModuleModel->getName()."&view=".$purchaseOrderModuleModel->getEditViewName()."&salesorder_id=".$this->getId();
	}

	public function saveFromProductRequest(Vtiger_Request $request) {
        $module = $request->getModule();
        $parent = $request->get('source');
        $userId = Users_Record_Model::getCurrentUserModel()->getId();
        $kudden = $request->get('cf_1137');
        $orderNo = $request->get('customerno');
        $this->set('subject', 'Entnahme');
        $this->set('customerno', $orderNo);
        $this->set('sostatus', 'Created');
        $this->set('assigned_user_id', $userId);
        $contactModel = Contacts_Module_Model::getModelByKudden($kudden);
        if ($contactModel) {
            $this->set('contact_id', $contactModel->getId());
            $this->set('bill_street', $contactModel->mailingstreet);
            $this->set('bill_city', $contactModel->mailingcity);
            $this->set('bill_state', $contactModel->mailingstate);
            $this->set('bill_code', $contactModel->mailingzip);
            $this->set('bill_country', $contactModel->mailingcountry);
            $this->set('ship_street', $contactModel->otherstreet);
            $this->set('ship_city', $contactModel->othercity);
            $this->set('ship_state', $contactModel->otherstate);
            $this->set('ship_code', $contactModel->otherzip);
            $this->set('ship_country', $contactModel->othercountry);
            $this->save();
            return $this;
        } else {
            return vtranslate('Contact with this number not found', 'Contacts');
        }

    }

}