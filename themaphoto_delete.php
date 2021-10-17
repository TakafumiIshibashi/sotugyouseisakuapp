<?php
session_start();
include("functions.php");
check_session_id();
// var_dump($_POST);
// exit();

$id = $_SESSION["id"];
$thema_id = $_POST["thema_id"];

$pdo = connect_to_db();



$sql =
'DELETE FROM photo_table WHERE thema_id=:thema_id AND contributor_id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

// python_tableデータ削除
$sql =
'DELETE FROM python_table WHERE thema_id=:thema_id AND contributor_id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

header("Location:home.php?");




?>
