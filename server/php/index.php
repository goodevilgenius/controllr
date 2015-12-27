<?php

$path = $_SERVER["PATH_INFO"];
header("X-Requested-Path: $path");
$method = strtolower($_SERVER["REQUEST_METHOD"]);
if ($method == "head") $method = "get";
header("X-Requested-Method: $method");

$pathparts = explode("/", $path);
@list($empty, $device, $command, $id) = $pathparts;
if (empty($device))  $device = "server";
if (empty($command)) $command = "info";
header("X-Requested-Device: $device");
header("X-Requested-Command: $command");

if (!file_exists("actions/{$method}-{$command}.php")) {
    header("HTTP/1.1 405 Method Not Allowed");
    die;
}

require_once("actions/{$method}-{$command}.php");
