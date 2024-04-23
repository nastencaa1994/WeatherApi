<?php
include_once __DIR__ . 'WeatherApi.php';


echo '<pre>';
$date = new DateTime("-2 days ");
$date = $date->format('Y-m-d');

$weather = new WeatherApi;
$res=$weather->addTableArchiveOneDay($date);
print_r($res);