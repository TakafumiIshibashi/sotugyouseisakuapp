<?php
session_start();
include("functions.php");
check_session_id();

$id = $_SESSION["id"];
$thema_id = $_GET["thema_id"];

$pdo = connect_to_db();

$sql = 'SELECT * FROM thema_table  WHERE thema_id=:thema_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
$status = $stmt->execute();


if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output1 = "";
    foreach ($result as $record) {
        $output1 .= "<td>{$record["imgthema"]}</td>";
    }
    unset($value);
}
// var_dump($output);
//   exit();

// ↓に自分以外を入れたら自信がアップした画像が消えそう
$sql =
'SELECT * FROM photo_table LEFT OUTER JOIN (SELECT img_id, COUNT(like_id) AS cnt FROM like_table GROUP BY img_id) AS  like_table ON photo_table.photo_id = like_table.img_id WHERE thema_id=:thema_id AND contributor_id !=:id ORDER BY come_updated_at >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) DESC';
// ランダムの場合↓
// ORDER BY RAND()

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
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
        $output .= "<tr>";
        $output .= "<td>{$record["posted_coment"]}</td>";
        $output .= "<td><img src={$record["posted_at"]} height ='250px' ></td>";
        $output_coment .= "<td>{$record["posted_coment"]}</td>";



        $output_posted_at .= "<td><a href='value_create.php?id={$id}&thema_id={$thema_id}&contributor_id={$record["contributor_id"]}&photo_id={$record["photo_id"]}'><img src={$record["posted_at"]} width = '180px' height ='240px' ></a>　</td>";
        $output .= "</tr>";
    }
    unset($value);
}




// いいねを選んでいる画像

$sql = "SELECT * FROM like_table  INNER JOIN photo_table ON like_table.img_id = photo_table.photo_id WHERE  like_table.thema_id = :thema_id AND valuer_id = :id";



$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
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

        $output_posted_at2 .= "　<a href='value_create.php?id={$id}&thema_id={$thema_id}&contributor_id={$record["contributor_id"]}&photo_id={$record["photo_id"]}'><img src={$record["posted_at"]} width = '180px' height ='240px' ></a>";
        $output .= "</tr>";
    }
    unset($value);
}

// いいねを選んでいる画像（ここまで）

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投票画面</title>
</head>

<body>
    <h1>テーマは『<?= $output1 ?>』</h1>

    <fieldset>

        <legend>選んだ写真（３枚まで）</legend>
        <thead>
            <div>
                <p><?= $output_posted_at2 ?></p>
            </div>
        </thead>
    </fieldset>
    <div>
        <p><?= $output_posted_at ?></p>
        <!-- <p><?= $output_coment ?></p> -->
    </div>
    <div>
    <h3>
    <a href="home.php">戻る</a>
</h3>
</div >
</body>

</html>


