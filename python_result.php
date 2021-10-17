<?php
session_start();
include("functions.php");
check_session_id();
$thema_id = $_GET["thema_id"];
// var_dump($_GET);
// exit();

$pdo = connect_to_db();

// テーマ呼び出し
$sql = "SELECT * FROM thema_table WHERE thema_id=:thema_id";


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
$status = $stmt->execute();


if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) {
        $output1 .= "<td>{$record["imgthema"]}</td>";
    }
    unset($value);
}




$sql =
'SELECT * FROM python_table INNER JOIN users_table ON python_table.contributor_id = users_table.id WHERE thema_Id = :thema_id ORDER BY python_point DESC LIMIT 1 ';


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
$status = $stmt->execute();


if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) {
        $output_posted_at .= "<div><p><img src={$record["posted_at"]} width = '360px' height ='480px' ></p><h1>{$record["username"]}さん</h1></div>";
    }
    unset($value);
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>結果画面</title>
    <link rel="stylesheet" type="text/css" href="css/python_result.css" />

</head>

<body>
<div class="total">
        <div class="title">
          <h1>AIが選んだ『<?= $output1 ?>』の優秀作品</h1>
        </div>
     <div>
        <!-- スクロール -->
        <div class="yoko">
            <!-- スクロール終 -->
            <p><?= $output_posted_at ?></p>
        </div>
     </div>
    </div>

    <a href="home.php?">ホーム画面</a>


</body>

</html>