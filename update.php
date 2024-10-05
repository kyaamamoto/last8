<?php
require_once('funcs.php');

// 本番環境データベース
$prod_db = "zouuu_zouuu_db";

// 本番環境ホスト
$prod_host = "mysql635.db.sakura.ne.jp"; 

// 本番環境ID
$prod_id = "zouuu";

// 本番環境PW
$prod_pw = "12345678qju"; 


// DB接続
try {
    $pdo = new PDO('mysql:dbname=' . $prod_db . ';charset=utf8;host=' . $prod_host, $prod_id, $prod_pw);
} catch (PDOException $e) {
    exit('DBConnectError:' . $e->getMessage());
}

// POSTデータを取得
$id = isset($_POST['id']) ? intval($_POST['id']) : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$birthDay = isset($_POST['birthDay']) ? $_POST['birthDay'] : null;
$tel = isset($_POST['tel']) ? $_POST['tel'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$jusho1 = isset($_POST['jusho1']) ? $_POST['jusho1'] : null;
$jusho2 = isset($_POST['jusho2']) ? $_POST['jusho2'] : null;
$jusho3 = isset($_POST['jusho3']) ? $_POST['jusho3'] : null;
$jusho4 = isset($_POST['jusho4']) ? $_POST['jusho4'] : null;
$kanshin1 = isset($_POST['kanshin1']) ? $_POST['kanshin1'] : null;
$kanshin2 = isset($_POST['kanshin2']) ? $_POST['kanshin2'] : null;
$kanshin3 = isset($_POST['kanshin3']) ? $_POST['kanshin3'] : null;
$food = isset($_POST['food']) ? implode(',', $_POST['food']) : '';
$travel = isset($_POST['travel']) ? implode(',', $_POST['travel']) : '';
$shumi = isset($_POST['shumi']) ? implode(',', $_POST['shumi']) : '';
$action = isset($_POST['action']) ? implode(',', $_POST['action']) : '';

if ($id === null || $name === null || $birthDay === null || $tel === null || $email === null || $jusho1 === null || $jusho2 === null || $jusho3 === null || $jusho4 === null) {
    exit('すべての必須フィールドを入力してください');
}

// SQL文を作成
$sql = "UPDATE member_table 
        SET name = :name, 
            birthDay = :birthDay, 
            tel = :tel, 
            email = :email, 
            jusho1 = :jusho1, 
            jusho2 = :jusho2, 
            jusho3 = :jusho3, 
            jusho4 = :jusho4, 
            kanshin1 = :kanshin1, 
            kanshin2 = :kanshin2, 
            kanshin3 = :kanshin3, 
            food = :food, 
            travel = :travel, 
            shumi = :shumi, 
            action = :action 
        WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':birthDay', $birthDay, PDO::PARAM_STR);
$stmt->bindValue(':tel', $tel, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':jusho1', $jusho1, PDO::PARAM_STR);
$stmt->bindValue(':jusho2', $jusho2, PDO::PARAM_STR);
$stmt->bindValue(':jusho3', $jusho3, PDO::PARAM_STR);
$stmt->bindValue(':jusho4', $jusho4, PDO::PARAM_STR);
$stmt->bindValue(':kanshin1', $kanshin1, PDO::PARAM_STR);
$stmt->bindValue(':kanshin2', $kanshin2, PDO::PARAM_STR);
$stmt->bindValue(':kanshin3', $kanshin3, PDO::PARAM_STR);
$stmt->bindValue(':food', $food, PDO::PARAM_STR);
$stmt->bindValue(':travel', $travel, PDO::PARAM_STR);
$stmt->bindValue(':shumi', $shumi, PDO::PARAM_STR);
$stmt->bindValue(':action', $action, PDO::PARAM_STR);

// SQL実行
$status = $stmt->execute();

// 結果確認
if ($status === false) {
    $error = $stmt->errorInfo();
    exit("QueryError:" . $error[2]);
} else {
    // 一覧ページにリダイレクト
    header("Location: select.php");
    exit;
}
?>