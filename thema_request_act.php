<?php
session_start();
include("functions.php");
check_session_id();
// var_dump($_POST);
// exit();
// var_dump($_FILES);
// exit();

$id = $_SESSION["id"];

if (
    !isset($_POST['imgthema']) || $_POST['imgthema'] == '' ||
    !isset($_POST['thema_up_str']) || $_POST['thema_up_str'] ==  '' ||
    !isset($_POST['thema_up_end']) || $_POST['thema_up_end'] ==  '' ||
    !isset($_POST['thema_val_str']) || $_POST['thema_val_str'] ==  '' ||
    !isset($_POST['thema_val_end']) || $_POST['thema_val_end'] == ''
) {
    echo json_encode(["error_msg" => "no input"]);
    exit();
}

$imgthema = $_POST["imgthema"];
$thema_up_str = $_POST["thema_up_str"];
$thema_up_end = $_POST["thema_up_end"];
$thema_val_str = $_POST["thema_val_str"];
$thema_val_end = $_POST["thema_val_end"];


$pdo = connect_to_db();



$sql = 'INSERT INTO thema_table(thema_id, thema_user_id,  imgthema, thema_up_str, thema_up_end, thema_val_str, thema_val_end, created_at) VALUES(NULL, :id, :imgthema, :thema_up_str, :thema_up_end, :thema_val_str, :thema_val_end, sysdate())';


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':imgthema', $imgthema, PDO::PARAM_STR);
$stmt->bindValue(':thema_up_str', $thema_up_str, PDO::PARAM_STR);
$stmt->bindValue(':thema_up_end', $thema_up_end, PDO::PARAM_STR);
$stmt->bindValue(':thema_val_str', $thema_val_str, PDO::PARAM_STR);
$stmt->bindValue(':thema_val_end', $thema_val_end, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    header("Location:home.php");
    exit();
}


?>







