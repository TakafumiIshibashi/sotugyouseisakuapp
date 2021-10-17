<?php
// var_dump($_GET);
// exit();
session_start();
include("functions.php");
check_session_id();



$id = $_GET['id'];
$thema_id = $_GET['thema_id'];
$contributor_id = $_GET['contributor_id'];
$photo_id = $_GET['photo_id'];

$pdo = connect_to_db();


$sql = 'SELECT COUNT(*) FROM like_table WHERE img_id=:photo_id AND valuer_id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();


if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $like_count = $stmt->fetch();
    // var_dump($like_count[0]);
    // exit();
}

if ($like_count[0] != 0) {
    // いいねされている状態
    $sql =
    'DELETE FROM like_table WHERE img_id=:photo_id AND valuer_id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();
    header("Location:like_button.php?thema_id=$thema_id");

} else {
    // いいねされていない状態

$sql =
'INSERT INTO like_table(like_id, thema_id, img_id, contributor_id, valuer_id, created_at) VALUES(NULL, :thema_id, :photo_id, :contributor_id, :id, sysdate())';
} 


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
$stmt->bindValue(':contributor_id', $contributor_id, PDO::PARAM_INT);
$stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
$status = $stmt->execute();



$sql = 'SELECT COUNT(*) FROM like_table
          WHERE thema_id=:thema_id AND valuer_id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();


if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $good_count = $stmt->fetch();
    // var_dump($good_count[0]);
    // exit();
}

if ($good_count[0] >= 4) {


    $sql =
        'DELETE FROM like_table
          WHERE img_id=:photo_id AND valuer_id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();
    header("Location:like_button.php?thema_id=$thema_id");


    header("Location:like_button.php?thema_id=$thema_id");
    exit();
} else {


}
if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    header("Location:like_button.php?thema_id=$thema_id");
    exit();
}
