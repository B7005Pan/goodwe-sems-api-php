<?php

$username = ""; // !
$password = ""; // !
$powerstation_id = ""; // !

require_once './lib/GoodWe/Sems/Client.php';

$GoodWe = (new GoodWe\Sems\Client(
    $username,
    $password
))->login();

$data = $GoodWe->GetMonitorDetailByPowerstationId($powerstation_id);
file_put_contents("GetMonitorDetailByPowerstationId.json", json_encode($data));

$data = $GoodWe->GetMonthlyChartDataByPowerstationId($powerstation_id);
file_put_contents("GetMonthlyChartDataByPowerstationId.json", json_encode($data));