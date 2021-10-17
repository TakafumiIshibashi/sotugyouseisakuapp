<?php
// var_dump($_GET);
// exit();
session_start();
include("functions.php");
check_session_id();



$id = $_GET['id'];
$thema_id = $_GET['thema_id'];
$contributor_id = $_GET['contributor_id'];
$photo_id = $_GET['photo_id'];

$pdo = connect_to_db();




$sql = 'SELECT COUNT(*) FROM like_table WHERE img_id=:photo_id AND valuer_id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
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
    // いいねされている状態
    $sql =
        'DELETE FROM like_table WHERE img_id=:photo_id AND valuer_id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();
    // ----------------------------

    // 分子
    $sql = 'SELECT COUNT(*) AS cnt FROM like_table WHERE img_id=:photo_id';



    $stmt = $pdo->prepare($sql);
    // $stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $status = $stmt->execute();


    if ($status == false) {
        $error = $stmt->errorInfo();
        echo json_encode(["error_msg" => "{$error[2]}"]);
        exit();
    } else {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output = "";
        foreach ($result as $record) {
         
            $output_posted_at = intval("{$record["cnt"]}");
        }
        unset($value);
    }



    // 分母
    $sql =
    "SELECT COUNT(DISTINCT valuer_id) AS con FROM like_table WHERE thema_id = :thema_id";

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
            $output_posted_at2 = intval("{$record["con"]}");
        }
        unset($value);
    }

    // ０割る０はエラーになる！！
    if (
        $output_posted_at2 == 0 ){
        $point = 0;
        }else{
            $point = $output_posted_at / $output_posted_at2 * intval(100);
        }

   
 
    // point入力
    $sql =
    "UPDATE python_table SET php_point = :point WHERE photo_id = :photo_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->bindValue(':point', $point, PDO::PARAM_STR);
    $status = $stmt->execute();

// ----------------------------
    header("Location:value_button.php?thema_id=$thema_id");
} else {
    // いいねされていない状態

    $sql =
        'INSERT INTO like_table(like_id, thema_id, img_id, contributor_id, valuer_id, created_at) VALUES(NULL, :thema_id, :photo_id, :contributor_id, :id, sysdate())';



    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
    $stmt->bindValue(':contributor_id', $contributor_id, PDO::PARAM_INT);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $status = $stmt->execute();


    // ----------------------------

    // 分子
    $sql =
    'SELECT * FROM photo_table LEFT OUTER JOIN (SELECT img_id, COUNT(like_id) AS cnt FROM like_table GROUP BY img_id) AS like_table ON photo_table.photo_id = like_table.img_id WHERE thema_id=:thema_id AND img_id=:photo_id ';



    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $status = $stmt->execute();


    if ($status == false) {
        $error = $stmt->errorInfo();
        echo json_encode(["error_msg" => "{$error[2]}"]);
        exit();
    } else {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output = "";
        foreach ($result as $record) {
            $output_posted_at .= "{$record["cnt"]}";
        }
        unset($value);
    }

    // 分母
    $sql =
    "SELECT COUNT(DISTINCT valuer_id) AS con FROM like_table WHERE thema_id = :thema_id";

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
            $output_posted_at2 .= "{$record["con"]}";
        }
        unset($value);
    }

    $point = intval($output_posted_at)/ intval($output_posted_at2) * 100;
    // var_dump(intval($point));
    // exit();

    // point入力
    $sql =
    "UPDATE python_table SET php_point = :point WHERE photo_id = :photo_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->bindValue(':point', $point, PDO::PARAM_STR);
    $status = $stmt->execute();

// ----------------------------



    $sql = 'SELECT COUNT(*) FROM like_table
          WHERE thema_id=:thema_id AND valuer_id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();


    if ($status == false) {
        $error = $stmt->errorInfo();
        echo json_encode(["error_msg" => "{$error[2]}"]);
        exit();
    } else {
        $good_count = $stmt->fetch();
        // var_dump($good_count[0]);
        // exit();
    }
}
if ($good_count[0] >= 4) {


    $sql =
        'DELETE FROM like_table
          WHERE img_id=:photo_id AND valuer_id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();
    // ----------------------------

    // 分子
    $sql = 'SELECT COUNT(*) AS cnt FROM like_table WHERE img_id=:photo_id';



    $stmt = $pdo->prepare($sql);
    // $stmt->bindValue(':thema_id', $thema_id, PDO::PARAM_INT);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $status = $stmt->execute();


    if ($status == false) {
        $error = $stmt->errorInfo();
        echo json_encode(["error_msg" => "{$error[2]}"]);
        exit();
    } else {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output = "";
        foreach ($result as $record) {

            $output_posted_at = intval("{$record["cnt"]}");
        }
        unset($value);
    }



    // 分母
    $sql =
    "SELECT COUNT(DISTINCT valuer_id) AS con FROM like_table WHERE thema_id = :thema_id";

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
            $output_posted_at2 = intval("{$record["con"]}");
        }
        unset($value);
    }
    if (
        $output_posted_at2 == 0
    ) {
        $point = 0;
    } else {
        $point = $output_posted_at / $output_posted_at2 * intval(100);
    }



    // point入力
    $sql =
    "UPDATE python_table SET php_point = :point WHERE photo_id = :photo_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->bindValue(':point', $point, PDO::PARAM_STR);
    $status = $stmt->execute();

// ----------------------------
    header("Location:value_button.php?thema_id=$thema_id");

    exit();
} else {
}
if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    header("Location:value_button.php?thema_id=$thema_id");
    exit();
}
