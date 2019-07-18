<?php

require_once("phpagi-asmanager.php");

$h = '192.168.1.43:5038';
$l = 'crm';
$p = 'y4nUc3gWq2Pr';

$asm = new AGI_AsteriskManager();
$asm->connect($h, $l ,$p);
$peers = $asm->Command('sip show peers');
$asm->disconnect();
$aster = explode("\n", $peers['data']);
array_shift($aster);
$headers = array_shift($aster);
array_pop($aster);
$stats = array_pop($aster);

parse($aster);
print '<pre>';
print_r($stats);
print '</pre>';

function display($aster)
{
    foreach ($aster as $l) {
        print "$l<br>\n";
    }
}

function parse($aster)
{
    $ptrn = '(?<name>[^\s]+)\s+(?<ip>[^\s]+)\s+(?<dyn>[^\s]+)\s+(?<rport>[^\s]+)\s+(?<com>[^\s]+)\s+(?<p>[^\s]+)\s+(?<stat>.*)';
    print '<link rel="stylesheet" href="../libraries/bootstrap/css/bootstrap.css">';
    print '<style>body{margin:auto;max-width:600px}</style>';
    print '<table class="table">';
    print '<tr><th>Number<th>Host<th>Status';
    $data = [];
    foreach ($aster as $l) {
        preg_match_all("/{$ptrn}/", $l, $a);
        $peer = [
            'name'   => $a['name'][0],
            'ip'     => trim($a['ip'][0]),
            'status' => trim($a['stat'][0]),
        ];
        $data[] = $peer;
        $stat = (strpos($peer['status'], 'OK') > -1)?'alert-success':'alert';
        printf(
            "<tr><td>%s<td>%s<td class=\"%s\">%s\n",
            $peer['name'],
            $peer['ip'],
            $stat,
            $peer['status']
        );
    }
    print '</table>';

    return $data;
}

function corsFree($data)
{
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");
    echo json_encode($data);
}
