<?php

//$sql = file_get_contents('.sql');

chdir('../../../');

require_once 'config.inc.php';
require_once 'include/database/PearDatabase.php';
require_once 'includes/main/WebUI.php';

$modName = 'PBXManager';
$blockLabel = 'LBL_PBXMANAGER_INFORMATION';

$fields = [
    [
        'fieldlabel' => 'LBL_NEED2CALL',
        'columnname' => 'needtocall',
        'fieldname'  => 'needtocall',
        'tablename'  => 'vtiger_pbxmanager',
        'uitype'     => 56,
        'displaytype' => 1,
        'generatedtype' => 1,
        'presence' => 2,
        'typeofdata' => 'C~O',
    ],
    [
        'fieldlabel' => 'LBL_DIALSTRING',
        'columnname' => 'dialstring',
        'fieldname'  => 'dialstring',
        'tablename'  => 'vtiger_pbxmanager',
        'uitype'     => 1,
        'displaytype' => 1,
        'generatedtype' => 1,
        'presence' => 2,
        'typeofdata' => 'M~O',
    ],
    [
        'fieldlabel' => 'LBL_USERNUMBER',
        'columnname' => 'usernumber',
        'fieldname'  => 'usernumber',
        'tablename'  => 'vtiger_pbxmanager',
        'uitype'     => 1,
        'displaytype' => 1,
        'generatedtype' => 1,
        'presence' => 2,
        'typeofdata' => 'M~O',
    ],
];

$module = Vtiger_Module::getInstance($modName);
$block = Vtiger_Block::getInstance($blockLabel, $module);

foreach ($fields as $field) {
    addField($module, $block, $field);
}

function addField($module, $block, $data)
{
    $field = Vtiger_Field::getInstance($data['columnname'], $module);
    if ($field) {
        w("Field exists: {$data['tablename']} {$data['columnname']}");
        return;
    }

    $field = new Vtiger_Field();
    $field->label        = $data['fieldlabel'];
    $field->column       = $data['columnname'];
    $field->name         = $data['fieldname'];
    $field->table        = $data['tablename'];
    $field->uitype       = $data['uitype'];
    $field->displaytype  = $data['displaytype'];
    $field->generatedtype= $data['generatedtype'];
    $field->typeofdata   = $data['typeofdata'];

    $block->addField($field);

    if ($rel) {
        $thisMod = 'Contacts';
        $field->setRelatedModules([$thisMod]);
    }

}

function w($txt)
{
    printf("%s / %s<br>\n", date('c'), $txt); 
}
