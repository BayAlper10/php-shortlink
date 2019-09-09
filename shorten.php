<?php

header("Content-type: application/json; charset=utf-8");

include "config.php";
$mysql = new mysqli($db_host, $db_user, $db_pass, $db_schema, $db_port);
if($mysql->connect_error){
    echo json_encode([
        "error" => "Database bağlantı hatası",
        "code" => false
    ]);

    return;
}

$url = $_POST["url"];
if(!isset($url) || !filter_var($url, FILTER_VALIDATE_URL)){
    echo json_encode([
        "error" => "Url bulunamadu",
        "code" => false
    ]);

    return;
}
$lowerUrl = strtolower($url);

$stmt = $mysql->prepare("SELECT code FROM links WHERE LOWER(url)=?");
$stmt->bind_param("s", $lowerUrl);
$stmt->execute();
if(($result = $stmt->get_result()) !== false){
    $data = $result->fetch_assoc();
    if($data !== null){
        echo json_encode([
            "error" => false,
            "code" => $data["code"]
        ]);

        return;
    }
}
$stmt->free_result();

$code = null;
do{
    $code = bin2hex(random_bytes(3));
    $stmt = $mysql->prepare("SELECT code FROM links WHERE code=?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    if(($result = $stmt->get_result()) !== false && $result->fetch_assoc() !== null){
        $code = null;
    }
    $stmt->free_result();
}while($code === null);

$stmt = $mysql->prepare("INSERT INTO links(code, url) VALUES(?, ?)");
$stmt->bind_param("ss", $code, $url);
$stmt->execute();

echo json_encode([
    "error" => false,
    "code" => $code
]);
