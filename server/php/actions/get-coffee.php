<?php

header("HTTP/1.1 418 I'm a Teapot");
header("Content-type: application/json");

echo json_encode(array("height" => "short", "circumfrence" => "stout"));
