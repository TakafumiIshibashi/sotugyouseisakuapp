<?php
session_start();
include("functions.php");
check_session_id();

$id = $_SESSION["id"];

$pdo = connect_to_db();

// テーマ呼び出し
$sql = "SELECT * FROM thema_table WHERE thema_table.thema_up_str <= current_date() AND thema_table.thema_up_end >= current_date()";


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
        $output1 .= "<td>{$record["imgthema"]}</td>";
        $output2 .= "{$record["thema_up_end"]}";
        $output3 .= "<td><a href='themaphoto_upform.php?thema_id={$record["thema_id"]}'><img src={$record["thema_icon"]} height ='150px' ></td>";
    }
    unset($value);
}

// ユーザー呼び出し
$sql = "SELECT contributor_id, COUNT(like_id) AS cnt FROM like_table WHERE contributor_id =:id GROUP BY contributor_id ";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) {
        $output9 .= "{$record["cnt"]}";
    }
    unset($value);
}


// 写真投稿
$sql = "SELECT * FROM thema_table INNER JOIN photo_table ON thema_table.thema_id = photo_table.thema_id WHERE thema_table.thema_up_str <= current_date() AND thema_table.thema_up_end >= current_date() AND contributor_id = :id";


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) {
        $output5 .= "<img src={$record["posted_at"]} width='300px'  height ='300px' > 
           <form action=themaphoto_delete.php method=POST enctype=multipart/form-data>
            <button>写真の削除</button>　　（画像投稿完了）
        <input type=hidden name=thema_id value={$record["thema_id"]}
    </form>";
    }
    unset($value);
}

// 投票用のテーマ呼び出し
// テーマ呼び出し
$sql = "SELECT * FROM thema_table WHERE thema_table.thema_up_str <= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND thema_table.thema_up_end >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";

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
        $output4 .= "{$record["thema_id"]}";
    }
    unset($value);
}


// 結果発表のテーマ呼び出し
$sql = "SELECT * FROM thema_table WHERE thema_table.thema_up_str <= DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY) AND thema_table.thema_up_end >= DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY)";

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
        $output6 .= "{$record["thema_id"]}";
    }
    unset($value);
}



//再度 テーマ呼び出し
$sql = "SELECT * FROM thema_table WHERE thema_table.thema_up_str <= current_date() AND thema_table.thema_up_end >= current_date()";


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



?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>（ホーム画面）</title>
    <link rel="stylesheet" type="text/css" href="css/home.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@600&family=Rye&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</head>

<body>

    <legend class=" login_title ">grow</legend>
    <div class="profile_thema">
        <div>
            <div>
                <div class="profile_icon">
                    <p class="profile_edit"><a class="banner_font" href="profile_edit.php?id=<?= $_SESSION["id"] ?>">
                            <img src=<?= $_SESSION["usericon"] ?> width='100px' height='100px'></a></p>
                </div>
                <h1><?= $_SESSION["username"] ?></h1>
                <div class="exp_box">
                    <h3>合計得票数： </h3>
                    <h3 id="exp"></h3>
                </div>
            </div>
        </div>

        <div class="themaup_main">
            <fieldset>
                <div class="themaup_text">
                    <strong>
                        <p>今週のテーマ</p>
                    </strong>
                    <h1>『<?= $output1 ?>』</h1>
                    <div class="deadline">
                        <div id="month" class="mm"></div>
                        <div id="day" class="dd"></div>
                        <div> まで</div>
                    </div>
                </div>
                <?= $output5 ?>
            </fieldset>

            <!-- ↓ないとバグ起きる -->
            <form action="themaphoto_delete.php" method="POST" enctype="multipart/form-data">
                <!-- </div>
        <button>写真の削除</button>
        </div> -->
                <input type="hidden" name="thema_id" value="<?= $record["thema_id"] ?>">
            </form>
            <!-- ないとバグ起きる（終） -->

            <form action="s3.php" method="POST" enctype="multipart/form-data">
                <div>
                    写真を投稿する <p> <input type="file" name="posted_at"></p>
                </div>
                <div>
                    <button>写真登録</button>
                </div>
                <input type="hidden" name="thema_id" value="<?= $record["thema_id"] ?>">
            </form>

        </div>
    </div>


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




    <!-- <div class="login_enter">
        <ul>
            <p><a href="value_button.php?thema_id=<?= $output4 ?>">投票する</a></p>
        </ul>
        <nav class="login_bunt">

            <nav class="login_bunt">
                <ul>
                    <p><a href="result.php?thema_id=<?= $output6 ?>">前回の投票結果を見る</a></p>
                </ul>

                <ul>
                    <p><a href="past_result.php?">過去の優秀作品を見る</a></p>
                </ul>

                <ul>
                    <p class="profile_edit"><a class="banner_font" href="profile_edit.php?id=<?= $_SESSION["id"] ?>">プロフィール編集</a></p>
                </ul>
            </nav>
            <p><a href="thema_request.php?id=<?= $_SESSION["id"] ?>">テーマの追加（管理者用）</a></p>

    </div> -->

    <form action="home.php">
        <div class=" btn-wrap">
            <button class="btn btn--orange btn--radius"><a href="value_button.php?thema_id=<?= $output4 ?>">先週のテーマ<br>写真へ投票する
            </button>
        </div>
    </form>

    <form action="home.php">
        <div class=" btn-wrap">
            <button class="btn btn--orange btn--radius"><a href="result.php?thema_id=<?= $output6 ?>">前回の投票<br>結果を見る</a>
            </button>
        </div>
    </form>

    <form action="past_result.php?">
        <div class=" btn-wrap">
            <button class="btn btn--orange btn--radius"><a href="past_result.php?">過去の優秀<br>作品を見る</a>
            </button>
        </div>
    </form>

    <form action="profile_edit.php?id=<?= $_SESSION["id"] ?>">
        <div class=" btn-wrap">
            <button class="btn btn--orange btn--radius"><a class="banner_font" href="profile_edit.php?id=<?= $_SESSION["id"] ?>">プロフィール</a>
                <p><a class="banner_font" href="profile_edit.php?id=<?= $_SESSION["id"] ?>">編集</a></p>
            </button>
        </div>
    </form>



    <form action="logout.php">
        <div class="btn-wrap">
            <button class="btn btn--orange btn--radius">ログアウト
            </button>
        </div>
    </form>

    <div class="master">
        <p><a href="thema_request.php?id=<?= $_SESSION["id"] ?>">テーマの追加<br>（管理者用）</a></p>
    </div>

    <!-- 日にちの計算 -->
    <script>
        const ymd = <?= json_encode($output2) ?>;
        console.log(ymd);

        let numer = ymd
        console.log(numer.slice(0, 4));
        console.log(numer.slice(5, 7));
        console.log(numer.slice(8, 10));
        let ddd = numer.slice(8, 10)
        console.log(ddd);
        let mmm = numer.slice(5, 7)
        console.log(mmm);
        let yyy = numer.slice(0, 4)
        // let numer = ymd
        // console.log(numer.slice(4, 6));
        // let ddd = numer.slice(8, 10)
        // console.log(ddd);
        // 今回指定する年月日情報（２０２０年１０月１日）
        var yearStr = yyy;
        var monthStr = mmm;
        var dayStr = ddd;
        // Dateオブジェクトには実際の月ー１の値を指定するため
        var jsMonth = monthStr - 1;
        // Dateオブジェクトは曜日情報を0から6の数値で保持しているため、翻訳する
        var dayOfWeekStrJP = ["日", "月", "火", "水", "木", "金", "土"];
        // 指定日付で初期化したDateオブジェクトのインスタンスを生成する
        var date = new Date(yearStr, jsMonth, dayStr);
        // 木曜日は数値の4として保持されているため、dayOfWeekStrJP[4]の値が出力される
        console.log(dayOfWeekStrJP[date.getDay()] + '曜日');
        const dayweek = (dayOfWeekStrJP[date.getDay()]);
        let num = Number(dayStr);

        $("#year").html(`${yearStr}年`);
        $("#month").html(`${jsMonth+1}月`);
        $("#day").html(`${num}日`);
        $("#week").html(`${dayweek}曜日`);



        // exp:計算
        const keiken = <?= json_encode($output9) ?>;
        let exp = 0 + Number(keiken);
        console.log(exp);
        $("#exp").html(`${exp}`);
    </script>




</body>



</html>