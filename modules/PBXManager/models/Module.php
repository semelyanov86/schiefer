<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PBXManager_Module_Model extends Vtiger_Module_Model
{
    /**
     * PINstudio @binizik get audio
     *
     * @param int $recordId pbxmanagerid
     *
     * @deprecated
     *
     * @return str html audi tag | reason
     */
    public function getAudiorecordTpl($recordId)
    {
        $record = PBXManager_Record_Model::getInstanceById($recordId);
        return $record->getAudiorecordTpl();
    }

    /**
     * Query phone lookup table
     *
     * @param str  $phone  phone number pattern
     * @param bool $strict exact match flag
     * @param int  $limit  query limit
     *
     * @return bool | arr
     */
    public function lookup($phone, $strict = false, $limit = false)
    {
        $db = PearDatabase::getInstance();
        $db->database->setFetchMode(2);
        $fields = [
            'crmid',
            'vc.setype',
            'vc.smownerid',
            'vc.smcreatorid',
            'vc.deleted',
        ];
        $where = ['deleted = 0'];
        $args = [];

        if ($strict) {
            $where[] = 'fnumber = ?';
            $args[] = $phone;
        } else {
            $where[] = 'fnumber LIKE ?';
            $fnumber = preg_replace('/[-()\s+]/', '', $phone);
            $args[] = '%' . substr($fnumber, -10, 10);
        }

        $condition = implode(' AND ', $where);
        $query = "SELECT " . implode(',', $fields)
            . " FROM vtiger_pbxmanager_phonelookup
            INNER JOIN vtiger_crmentity vc USING (crmid)
            WHERE {$condition}
            ORDER BY crmid DESC";
        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }
        $fuzzy = $db->pquery($query, $args);
        if ($db->num_rows($fuzzy) == 0) {
            return false;
        }

        return $fuzzy->GetAll();
    }

    /**
     * Function to get Settings links
     *
     * @return <Array>
     */
    public function getSettingLinks()
    {
        if (!$this->isEntityModule()) {
            return [];
        }
        vimport('~~modules/com_vtiger_workflow/VTWorkflowUtils.php');

        $layoutEditorImagePath = Vtiger_Theme::getImagePath('LayoutEditor.gif');
        $editWorkflowsImagePath = Vtiger_Theme::getImagePath('EditWorkflows.png');
        $settingsLinks = [];

        if (VTWorkflowUtils::checkModuleWorkflow($this->getName())) {
            $settingsLinks[] = [
                'linktype'  => 'LISTVIEWSETTING',
                'linklabel' => 'LBL_EDIT_WORKFLOWS',
                'linkurl'   => 'index.php?parent=Settings&module=Workflows&view=List&sourceModule='.$this->getName(),
                'linkicon' => $editWorkflowsImagePath
            ];
        }

        $settingsLinks[] = [
            'linktype'  => 'LISTVIEWSETTINGS',
            'linklabel' => 'LBL_SERVER_CONFIGURATION',
            'linkurl'   => 'index.php?parent=Settings&module=PBXManager&view=Index',
            'linkicon'  => ''
        ];
        return $settingsLinks;
    }

    /**
     * Overriden to make editview=false for this module
     * @see isPermitted
     */
    public function isPermitted($actionName)
    {
        if ($actionName == 'EditView') return false;

        return $this->isActive() &&
            Users_Privileges_Model::isPermitted($this->getName(), $actionName);
    }

    /**
     * Function to check whether the module is an entity type module or not
     * @return <Boolean> true/false
     */
    public function isQuickCreateSupported() {
        return false;
    }

    public function isWorkflowSupported() {
        return true;
    }

    /**
     * Function to identify if the module supports quick search or not
     */
    public function isQuickSearchEnabled() {
        return true;
    }

    public function isListViewNameFieldNavigationEnabled() {
        return false;
    }
}

