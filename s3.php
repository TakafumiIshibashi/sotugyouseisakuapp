<?php
//   var_dump($_FILES);
//     exit();
session_start();
include("functions.php");
check_session_id();

$id = $_SESSION["id"];

if (
    !isset($_POST['thema_id']) || $_POST['thema_id'] == ''
) {
    echo json_encode(["error_msg" => "no input"]);
    exit();
}
$thema_id = $_POST["thema_id"];

$pdo = connect_to_db();

$s3_head = "https://growproduct20211001.s3.amazonaws.com/";

// var_dump($_FILES);
require_once "vendor/autoload.php";
use Aws\S3\S3Client;

$bucket = '--- BUCKET_NAME ---';
$key = '--- KEY_NAME ---';
$secret = '--- SECRET_NAME ---';

// S3クライアントを作成
$s3 = new S3Client(array(
    'version' => 'latest',
    'credentials' => array(
        'key' => $key,
        'secret' => $secret,
    ),
    'region'  => '', // 米国東部 (バージニア北部)
));

// アップロードされた画像の処理
$file = $_FILES['posted_at']['tmp_name'];
if (!is_uploaded_file($file)) {
    return;
}

// S3バケットに画像をアップロード
$result = $s3->putObject(array(
    'Bucket' => $bucket,
    'Key' => time() . '.jpg',
    'Body' => fopen($file, 'rb'),
    'ACL' => 'public-read', // 画像は一般公開されます
    'ContentType' => mime_content_type($file),

    $filename = time() . '.jpg'
    
));
// var_dump($uploaded_file_name);
// exit();
    $filename_to_save = $s3_head . $filename;



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
