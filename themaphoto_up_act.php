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
    !isset($_POST['thema_id']) || $_POST['thema_id'] == ''
) {
    echo json_encode(["error_msg" => "no input"]);
    exit();
}

$thema_id = $_POST["thema_id"];


$pdo = connect_to_db();




if (isset($_FILES['posted_at']) && $_FILES['posted_at']['error'] == 0) {
    $uploaded_file_name = $_FILES['posted_at']['name']; //ファイル名の取得 
    $temp_path = $_FILES['posted_at']['tmp_name'];
    //tmpフォルダの場所 
    $directory_path = 'photoup/';


    // } else {
    //   // 送られていない，エラーが発生，などの場合
    //   exit('Error:画像が送信されていません');
}



// 拡張子の情報を取得
$extension = pathinfo($uploaded_file_name, PATHINFO_EXTENSION);

$unique_name = date('YmdHis') . md5(session_id()) . "." . $extension;
$filename_to_save = $directory_path . $unique_name;


if (is_uploaded_file($temp_path)) {
    // ↓ここでtmpファイルを移動する
    if (move_uploaded_file($temp_path, $filename_to_save)) {
        chmod($filename_to_save, 0644);
        // 権限の変更
    } else {
        exit('Error:アップロードできませんでした');
        // 画像の保存に失敗
    }
} else {
    exit('Error:画像がありません');
    // tmpフォルダにデータがない
}

// var_dump($_POST);
// exit();



// 画像一人一つ
// SELECT COUNT(*) FROM thema_table  INNER JOIN photo_table ON thema_table.thema_id = photo_table.thema_id WHERE thema_table.thema_id = 7 AND contributor_id = 13
$sql = 'SELECT COUNT(*) FROM thema_table  INNER JOIN photo_table ON thema_table.thema_id = photo_table.thema_id WHERE thema_table.thema_up_str <= current_date() AND thema_table.thema_up_end >= current_date() AND contributor_id = :id';
$stmt = $pdo->prepare($sql);
// $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
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
    exit('既に画像が登録されています。');
} else {
// 画像一人一つ（終）



$sql = 'INSERT INTO photo_table(photo_id, thema_id, contributor_id,posted_at,  	come_updated_at) VALUES(NULL, :thema_id,:id, :posted_at,  sysdate())';


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_STR);
$stmt->bindValue(':posted_at', $filename_to_save, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {


// python_table作成のためphoto_id呼び出し
$sql = 'SELECT * FROM photo_table ';

$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) 
    unset($value);
}


// python_table作成データ入力
$sql = 'INSERT INTO python_table(python_id, photo_id, thema_id, contributor_id,posted_at) VALUES(NULL, :photo_id, :thema_id,:id, :posted_at)';


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_STR);
$stmt->bindValue(':photo_id', $record["photo_id"], PDO::PARAM_STR);
$stmt->bindValue(':posted_at', $filename_to_save, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {


    header("Location:home.php");
    exit();
}
}
}
