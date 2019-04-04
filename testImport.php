<?php
ini_set('display_errors','on'); version_compare(PHP_VERSION, '5.5.0') <= 0 ? error_reporting(E_WARNING & ~E_NOTICE & ~E_DEPRECATED) : error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

include_once 'includes/Loader.php';
include_once 'includes/runtime/Globals.php';
include_once 'includes/runtime/BaseModel.php';
require_once('include/utils/utils.php');
include_once 'include/Webservices/Utils.php';
include_once 'include/Webservices/ModuleTypes.php';
include_once 'include/Webservices/DescribeObject.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Revise.php';
include_once 'include/Webservices/Query.php';
include_once 'modules/Vtiger/models/Module.php';

$configs = include('modules/Contacts/config.php');
$numericFields = array('cf_1109', 'cf_1127', 'cf_1113', 'cf_1121', 'cf_1115', 'cf_1119', 'cf_1117', 'cf_1123', 'cf_1125');
$date = date('d-m-Y');
$dos2unix = exec("dos2unix -iso -n import/" . $date . ".txt storage/" . $date . ".txt");
$dataString = file_get_contents_utf8("storage/$date.txt");
if ($dataString) {
    $textArr = splitDocument($dataString);
    $current_user = CRMEntity::getInstance('Users');
    $current_user->retrieveCurrentUserInfoFromFile(1);
    $iterate = 0;
    foreach ($textArr as $text) {
        $iterate++;
        if (!$configs[1] || empty($configs[1])) {
            continue;
        }
        try {
            $tmpval = $text[0];
            $q = "SELECT * FROM Contacts WHERE $configs[0] = '$tmpval'";
            $q = $q . ';'; // NOTE: Make sure to terminate query with ;
            $records = vtws_query($q, $current_user);
            if (count($records) > 0) {
                $wsid = $records[0]['id'];
            } else {
                $wsid = false;
            }
        } catch (WebServiceException $ex) {
            var_dump($ex->getMessage(), $q);
        }
        if ($wsid) {
            try {
                $dataArr = array('id' => $wsid);
                for ($i = 0; $i < count($configs); $i++) {
                    if (in_array($configs[$i], $numericFields)) {
//                        $dataArr[$configs[$i]] = number_format($text[$i], 2, ',', '');
                        $tmpString = str_replace('.', '', $text[$i]);
                        $dataArr[$configs[$i]] = str_replace(',', '.', $tmpString);
                    } else {
                        $dataArr[$configs[$i]] = $text[$i];
                    }
                }
                $contact = vtws_revise($dataArr, $current_user);
            } catch (WebServiceException $ex) {
                var_dump($ex->getMessage(), $dataArr);
            }
        } else {
            try {
                $dataArr = array('assigned_user_id' => '19x1');
                for ($i = 0; $i < count($configs); $i++) {
                    if (in_array($configs[$i], $numericFields)) {
//                        $dataArr[$configs[$i]] = number_format($text[$i], 2, ',', '');
                        $tmpString = str_replace('.', '', $text[$i]);
                        $dataArr[$configs[$i]] = str_replace(',', '.', $tmpString);
                    } else {
                        $dataArr[$configs[$i]] = $text[$i];
                    }
                }
                $contact = vtws_create('Contacts', $dataArr, $current_user);
            } catch (WebServiceException $ex) {
                var_dump($ex->getMessage(), $dataArr);
            }
        }
    }

}
//$dos2unix = exec("dos2unix -iso -n import/25-03-2019.txt storage/25.txt");
unlink('storage/' . $date . '.txt');
echo 'DONE!';


function splitDocument($text)
{
    $lines = [];
    foreach(preg_split('~[\r\n]+~', $text) as $line){
        if(empty($line) or ctype_space($line)) continue; // skip only spaces
        // if(!strlen($line = trim($line))) continue; // or trim by force and skip empty
        // $line is trimmed and nice here so use it
        $line = clearLine($line);
        $line = explode(';', $line);
        $line = clearArray($line);
        $lines[] = $line;
    }
    return $lines;
}

function clearArray($line)
{
    $result = array();
    foreach ($line as $value) {
        $result[] = trim($value, '"');
    }
    return $result;
}

function clearLine($line){
    $line = str_replace('\f','',$line);
    $pos = mb_strpos($line,'');
    if($pos !== false){
        $line = mb_substr($line,0,$pos);
    }
    return $line;
}

function file_get_contents_utf8($fn) {
    $content = file_get_contents($fn);
    return mb_convert_encoding($content, 'UTF-8',
        mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}