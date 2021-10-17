<?php
session_start();
include("functions.php");
check_session_id();

$id == $_GET["id"];
if ($_SESSION["id"] != 1) {
    header("Location:home.php");
    exit();
}

$pdo = connect_to_db();

?>



<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB連携型todoリスト（編集画面）</title>
</head>

<body>
    <form action="thema_request_act.php" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>テーマ登録</legend>

            <div>
                テーマ名: <input type="text" name="imgthema">
            </div>
            <td>
 
            <div>
                <div>
                    投稿開始日: <input type="date" min="<?php echo date('Y-m-d'); ?>" name="thema_up_str">

                    投稿開締切日: <input type="date" name="thema_up_end">
                </div>
                <div>
                    <p>　</p>
                </div>
                <div>
                    投票期間初め: <input type="date" name="thema_val_str">

                    投票期間終り: <input type="date" name="thema_val_end">
                </div>
                <button>登録</button>
            </div>
            <a href="home.php">戻る</a>
        </fieldset>
    </form>
    

</body>

</html>