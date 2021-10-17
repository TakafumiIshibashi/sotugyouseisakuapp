<?php
// var_dump($_POST);
// exit();
session_start();
include("functions.php");
check_session_id();

if (
    !isset($_POST['profile']) || $_POST['profile'] == '' ||
    !isset($_POST['id']) || $_POST['id'] == ''
) {
    echo json_encode(["error_msg" => "no input"]);
    exit();
}

$profile = $_POST["profile"];
$id = $_POST["id"];

$pdo = connect_to_db();

$sql = "UPDATE users_table SET profile=:profile,  updated_at=sysdate() WHERE id=:id";


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':profile', $profile, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    header("Location:home.php");
    exit();
}