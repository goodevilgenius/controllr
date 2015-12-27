<?php

$path = $_SERVER["PATH_INFO"];
header("X-Requested-Path: $path");
$method = strtolower($_SERVER["REQUEST_METHOD"]);
if ($method == "head") $method = "get";
header("X-Requested-Method: $method");

if (!file_exists("actions/" . $method . ".php")) {
    header("HTTP/1.1 405 Method Not Allowed");
    die;
}

require_once("actions/" . $method . ".php");
