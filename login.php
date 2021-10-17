<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" type="text/css" href="css/login.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@600&family=Rye&display=swap" rel="stylesheet">
</head>

<body>
    <form action="login_act.php" method="POST">

        <legend class=" login_title ">grow</legend>
        <div class="btn-wrap">
            <p>
                ユーザー名
            </p>
            <input type="text" name="username" class=" text--orange ">
        </div>
        <div class="btn-wrap">
            <p>
                パスワード
            </p>
            <input type="text" name="password" class=" text--orange ">
        </div>
        <div class="btn-wrap">
            <button class=" btn btn--orange btn--radius">ログイン</button>
        </div>
    </form>
    <form action="register.php" method="POST">
        <div class="btn-wrap">
            <p>
                はじめての方
            </p>
            <button class="btn btn--orange btn--radius">新規登録
            </button>
        </div>
    </form>
</body>

</html>