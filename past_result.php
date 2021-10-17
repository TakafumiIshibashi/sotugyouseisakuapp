<?php
// var_dump($_GET);
// exit();

session_start();
include("functions.php");
check_session_id();

$pdo = connect_to_db();



// $sql = "SELECT * FROM thema_table ";
$sql = "SELECT * FROM thema_table WHERE thema_table.thema_up_str <= DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY)  ";


$stmt = $pdo->prepare($sql);
$status = $stmt->execute();


if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) {
        $output1 .= "<div class= 'btn-wrap'><button class='btn btn--orange btn--radius'><h1><a href='result.php?thema_id={$record["thema_id"]}'><td>{$record["imgthema"]}</td></h1></button></div>";
        $output2 .= "{$record["thema_up_end"]}";
        $output3 .= "<td><img src={$record["thema_icon"]} height ='150px' ></td>";
    }
    unset($value);
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>（ホーム画面）</title>
    <link rel="stylesheet" type="text/css" href="css/past_result.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@600&family=Rye&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</head>

<body>

    <div class="top">
        <!-- <div class="YYYY"><?php echo date("Y"); ?>年</div> -->
        <!-- <div id="year" class="YYYY"></div> -->
        <!-- <div class="mm"><?php echo date("n"); ?>月</div> -->
        <!-- <div id="month" class="mm"></div> -->
        <!-- <div class="eng_m"><?php echo date("M"); ?></div> -->
    </div>

    <div class="day">
        <!-- <div class="dd"><?php echo date("j"); ?></div>
            <div class="nichi">日</div> -->
        <!-- <div id="day" class="dd"></div> -->
        <!-- <?php $week = array("日", "月", "火", "水", "木", "金", "土"); ?>
            <div class="week">　　<?php echo $week[date("w")]; ?>曜日</div> -->
        <!-- <div id="week" class="week"></div> -->
    </div>

    <strong>
    </strong>
    <h1><?= $output1 ?></h1>
    <div>
    </div>



<div class="home">
    <h2><a href="home.php?">戻る</a></h2>
</div>    
        <div class="btn-wrap">
            <button class="btn btn--orange btn--radius">ログアウト
            </button>
        </div>
    </form>

    <div>
        <?= $output5 ?>
    </div>

    <script>
        const hoge = <?= json_encode($output5) ?>;
        console.log(hoge);
    </script>
</body>



</html>