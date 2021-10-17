<?php
session_start();
include("functions.php");


?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録画面</title>
    <link rel="stylesheet" type="text/css" href="css/register.css" />
</head>

<body>
    <form action="register_act.php" method="POST" enctype="multipart/form-data">

        <legend>新規登録画面</legend>
        <div class="btn-wrap">
            <p id="icon">
                icon
            </p>
            <label class="js-upload-file">
                <input type="file" name="usericon" class="js-upload-file">ファイルを選択
            </label>
            <div class="js-upload-filename">ファイルが未選択です</div>
            <div class="fileclear js-upload-fileclear">選択ファイルをクリア</div>
        </div>
        <div class="btn-wrap">
            <p>
                ユーザー名
            </p>
            <input type="text" name="username" class=" text--orange ">
        </div>
        <div class="btn-wrap">
            <p>
                メールアドレス
            </p>
            <input type="email" name="email" class=" text--orange ">
        </div>
        <div class="btn-wrap">
            <p>
                パスワード
            </p>
            <input type="text" name="password" class=" text--orange ">
        </div>
        <div class="btn-wrap">
            <td class="details">
                <textarea name="profile" id="" cols="20" rows="5" placeholder="プロフィール：400字以内" class=" text--orange "></textarea>
            </td>
        </div>
        <div>
            <button class=" btn btn--orange btn--radius">登録</button>
        </div>
    </form>
    <form action="login.php" method="POST">
        <div class="btn-wrap">
            <p>
                登録がある方はこちら
            </p>
            <button class="btn btn--orange btn--radius">ログイン画面
            </button>
        </div>
        
    </form>
<div>　</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        $(function() {
            $('.js-upload-file').on('change', function() { //ファイルが選択されたら
                let file = $(this).prop('files')[0]; //ファイルの情報を代入(file.name=ファイル名/file.size=ファイルサイズ/file.type=ファイルタイプ)
                $('.js-upload-filename').text(file.name); //ファイル名を出力
                $('.js-upload-fileclear').show(); //クリアボタンを表示
            });
            $('.js-upload-fileclear').click(function() { //クリアボタンがクリックされたら
                $('.js-upload-file').val(''); //inputをリセット
                $('.js-upload-filename').text('ファイルが未選択です'); //ファイル名をリセット
                $(this).hide(); //クリアボタンを非表示
            });
        });
    </script>

</body>

</html>