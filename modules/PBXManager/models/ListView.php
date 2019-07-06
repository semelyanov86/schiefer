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
 * PBXManager ListView Model Class
 */

class PBXManager_ListView_Model extends Vtiger_ListView_Model
{
    public function __construct()
    {
        $cfg = new PBXManager_Config_Model;
        $this->vtVersion = 'v' . $cfg->getGlobals()['vtmajor'];
    }

    /**
     * Overrided to remove add button
     */
    public function getBasicLinks()
    {
        $basicLinks = array();
        return $basicLinks;
    }

    /**
     * Overrided to remove Mass Edit Option
     */
    public function getListViewMassActions($linkParams)
    {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $moduleModel = $this->getModule();

        $linkTypes = array('LISTVIEWMASSACTION');
        $links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);


        if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DELETE',
                'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
                'linkicon' => ''
            );

            foreach($massActionLinks as $massActionLink) {
            $links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
            }
        }

        return $links;
    }

    /**
     * Overrided to add HTML content for callstatus irrespective of the filters
     */
    public function getListViewEntries($pagingModel)
    {
        $db = PearDatabase::getInstance();

        $moduleName = $this->getModule()->get('name');
        $moduleFocus = CRMEntity::getInstance($moduleName);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        //Add the direction field to the query irrespective of filter
        $queryGenerator = $this->get('query_generator');
        $fields = $queryGenerator->getFields();
        array_push($fields, 'direction');
        $queryGenerator->setFields($fields);
        $this->set('query_generator', $queryGenerator);
        //END

        $listViewContoller = $this->get('listview_controller');

        // SalesPlatform.ru begin
        $searchParams = $this->get('search_params');
        if (empty($searchParams)) {
            $searchParams = array();
        }
        $glue = "";
        if (count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        $queryGenerator->parseAdvFilterList($searchParams, $glue);
        // SalesPlatform.ru end

        $searchKey = $this->get('search_key');
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if (!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array(
                'search_field' => $searchKey,
                'search_text' => $searchValue,
                'operator' => $operator
            ));
        }

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        //List view will be displayed on recently created/modified records
        if (empty($orderBy) && empty($sortOrder) && $moduleName != "Users") {
            $orderBy = 'modifiedtime';
            $sortOrder = 'DESC';
        }

        if (!empty($orderBy)) {
            $columnFieldMapping = $moduleModel->getColumnFieldMapping();
            $orderByFieldName = $columnFieldMapping[$orderBy];
            $orderByFieldModel = $moduleModel->getField($orderByFieldName);

            if ($orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE) {
                //IF it is reference add it in the where fields so that from clause will be having join of the table
                $queryGenerator = $this->get('query_generator');
                $queryGenerator->addWhereField($orderByFieldName);
                //$queryGenerator->whereFields[] = $orderByFieldName;
            }
        }
        $listQuery = $this->getQuery();

        $sourceModule = $this->get('src_module');
        if (!empty($sourceModule)) {
            if (method_exists($moduleModel, 'getQueryByModuleField')) {
                $overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
                if (!empty($overrideQuery)) {
                    $listQuery = $overrideQuery;
                }
            }
        }

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        if (!empty($orderBy)) {
            if ($orderByFieldModel->isReferenceField()) {
                $referenceModules = $orderByFieldModel->getReferenceList();

                $referenceNameFieldOrderBy = array();
                foreach ($referenceModules as $referenceModuleName) {
                    $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModuleName);
                    $referenceNameFields = $referenceModuleModel->getNameFields();
                    $columnList = array();
                    foreach ($referenceNameFields as $nameField) {
                        $fieldModel = $referenceModuleModel->getField($nameField);
                        $columnList[] = $fieldModel->get('table') . $orderByFieldModel->getName() . '.' . $fieldModel->get('column');
                    }
                    if (count($columnList) > 1) {
                        $referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name' => $columnList[0], 'last_name' => $columnList[1]), 'Users') . ' ' . $sortOrder;
                    } else {
                        $referenceNameFieldOrderBy[] = implode('', $columnList) . ' ' . $sortOrder;
                    }
                }
                $listQuery .= ' ORDER BY ' . implode(',', $referenceNameFieldOrderBy);
            } else {
                $listQuery .= ' ORDER BY ' . $orderBy . ' ' . $sortOrder;
            }
        }

        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);

        $listQuery .= " LIMIT $startIndex," . ($pageLimit + 1);

        $listResult = $db->pquery($listQuery, array());

        $listViewRecordModels = array();
        $listViewEntries = $listViewContoller->getListViewRecords($moduleFocus, $moduleName, $listResult);

        $pagingModel->calculatePageRange($listViewEntries);

        if ($db->num_rows($listResult) > $pageLimit) {
            array_pop($listViewEntries);
            $pagingModel->set('nextPageExists', true);
        } else {
            $pagingModel->set('nextPageExists', false);
        }

        //Adding the HTML content based on the callstatus and direction to the records
        // TODO get values from record
        foreach ($listViewEntries as $recordId => $record) {
            // get audio template
            if (!empty($listViewEntries[$recordId]['recordingurl'])) {
                $listViewEntries[$recordId]['recordingurl'] = '';
                $completed = $this->isCompleted($listViewEntries[$recordId]['callstatus']);
                if ($completed) {
                    $listViewEntries[$recordId]['recordingurl']
                        = $moduleModel->getAudiorecordTpl($recordId); // PINstudio @binizik get audio
                }
            }
            // get status icon
            $labelType = $this->getLabelClass($listViewEntries[$recordId]['callstatus']);

            $icon = $this->getIconClass($listViewEntries[$recordId]['direction']);

            $listViewEntries[$recordId]['callstatus'] = '<span class="label label-'
                . $labelType .'"><i class="'
                . $icon .' icon-white"></i>&nbsp;'
                . vtranslate($listViewEntries[$recordId]["callstatus"], $moduleName)
                . '</span>';

            $listViewEntries[$recordId]['direction'] = vtranslate($listViewEntries[$recordId]['direction'], $moduleName);
        }

        $index = 0;
        foreach ($listViewEntries as $recordId => $record) {
            $rawData = $db->query_result_rowdata($listResult, $index++);
            $record['id'] = $recordId;
            $model = $moduleModel->getRecordFromArray($record, $rawData);
            /*
            // TODO get rid of previous foreach
            if ($model->isCompleted()) {
                $model->set('recordingurl', $model->getAudiorecordTpl());
            }
            */
            $listViewRecordModels[$recordId] = $model;
        }

        return $listViewRecordModels;
    }

    public function isCompleted($status)
    {
        return in_array(
            $status,
            [
                vtranslate('completed', 'PBXManager'),
                vtranslate('ANSWER', 'PBXManager'),
                'ANSWER',
                'completed'
            ]
        );
    }

    public function getIconClass($direction)
    {
        $icons = $this->getCss($this->vtVersion);
        $map = [
            vtranslate('outbound', 'PBXManager') => $icons['out'],
            vtranslate('inbound', 'PBXManager')  => $icons['in'],
            vtranslate('internal', 'PBXManager') => $icons['int'],
            'outbound' => $icons['out'],
            'inbound'  => $icons['in'],
            'internal' => $icons['int'],
        ];

        return array_key_exists($direction, $map)
            ? $map[$direction]
            : $icons['def'];
    }

    public function getLabelClass($status)
    {
        $err = ($this->vtVersion == 'v7')? 'danger' : 'important';
        $map = [
            vtranslate('ANSWER', 'PBXManager') => 'success',
            vtranslate('BUSY', 'PBXManager') => 'info',
            vtranslate('CANCEL', 'PBXManager') => $err,
            'ANSWER' => 'success',
            'BUSY' => 'info',
            'CANCEL' => $err,
        ];

        return array_key_exists($status, $map)
            ? $map[$status]
            : 'warning';
    }

    public function getCss($version = 'v6')
    {
        $classes = [
            'v6' => [
                'out' => 'icon-arrow-up',
                'in'  => 'icon-arrow-down',
                'int' => 'icon-retweet',
                'def' => 'icon-question-sign',
            ],
            'v7' => [
                'out' => 'fa fa-angle-double-up',
                'in'  => 'fa fa-angle-double-down',
                'int' => 'fa fa-retweet',
                'def' => 'fa fa-question',
            ],
        ];

        return array_key_exists($version, $classes)
            ? $classes[$version]
            : $classes['v7'];
    }
}
