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
    'SELECT * FROM photo_table AS photo_table INNER JOIN users_table AS users_table ON photo_table.contributor_id = users_table.id LEFT OUTER JOIN (SELECT img_id, COUNT(like_id) AS cnt FROM like_table GROUP BY img_id) AS like_table ON photo_table.photo_id = like_table.img_id WHERE thema_id=:thema_id AND cnt != 0 ORDER BY cnt DESC ';


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
        $output_posted_at .= "<div><p><img src={$record["posted_at"]} width = '360px' height ='480px' >{$record["cnt"]}票</p><h1>{$record["username"]}さん</h1></div>";
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
    <link rel="stylesheet" type="text/css" href="css/result.css" />

</head>

<body>
    <h1>テーマは『<?= $output1 ?>』</h1>
    <div>
        <!-- スクロール -->
        <div class="yoko">
            <!-- スクロール終 -->
            <p><?= $output_posted_at ?></p>

        </div>
    </div>
    <div>
       <h3> 
           <a href="python_result.php?thema_id=<?= $thema_id ?>">AIが選んだ優秀作品</a>
        </h3>
    </div>
    <h3>
    <a href="home.php?">戻る</a>
    </h3>








    <h1></h1>



</body>

</html>