<?php
session_start();
include('functions.php');
// var_dump($_FILES);
// exit();

if (
    !isset($_POST['username']) || $_POST['username'] == '' ||
    !isset($_POST['email']) || $_POST['email'] == '' ||
    !isset($_POST['password']) || $_POST['password'] ==  '' ||
    !isset($_POST['profile']) || $_POST['profile'] == ''
) {
    echo json_encode(["error_msg" => "no input"]);
    exit();
}

$username = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];
$profile = $_POST["profile"];
$s3_head = "https://growproduct20211001.s3.amazonaws.com/";


$pdo = connect_to_db();

$sql = 'SELECT COUNT(*) FROM users_table WHERE username=:username';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
}

if ($stmt->fetchColumn() > 0) {
    echo "<p>すでに登録されているユーザです．</p>";
    echo '<a href="login.php">login</a>';
    exit();
}

require_once "vendor/autoload.php";

use Aws\S3\S3Client;

$bucket = 'growproduct20211001';
$key = 'AKIAVWPIY3FE4WB7KV7N';
$secret = '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';

// S3クライアントを作成
$s3 = new S3Client(array(
    'version' => 'latest',
    'credentials' => array(
        'key' => $key,
        'secret' => $secret,
    ),
    'region'  => 'us-east-1', // 米国東部 (バージニア北部)
));

// アップロードされた画像の処理
$file = $_FILES['usericon']['tmp_name'];
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




$sql = 'INSERT INTO users_table(id, username, email, password, profile, usericon,  is_admin, is_deleted, created_at, updated_at) VALUES(NULL, :username, :email, :password, :profile, :usericon,  "0", "0", sysdate(), sysdate())';


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':password', $password, PDO::PARAM_STR);
$stmt->bindValue(':profile', $profile, PDO::PARAM_STR);
$stmt->bindValue(':usericon', $filename_to_save, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    header("Location:login.php");
    exit();
}
