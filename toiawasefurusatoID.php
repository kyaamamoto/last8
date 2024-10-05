<?php
require_once('funcs.php');
session_start();

// データベースに接続
$pdo = db_conn();

// CSRFトークンの検証
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        exit("不正なリクエストです。");
    }

    // フォームからのデータを取得
    $form = $_SESSION['form'];
    $division = $form['Division'];
    $name = $form['Name'];
    $email = $form['E-mail'];
    $tel = $form['Tel'];
    $message = $form['Message'];

    // データを挿入するSQLクエリを準備
    $sql = "INSERT INTO toiawase_table (division, name, email, tel, message) VALUES (:division, :name, :email, :tel, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':division', $division, PDO::PARAM_STR);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':tel', $tel, PDO::PARAM_STR);
    $stmt->bindValue(':message', $message, PDO::PARAM_STR);

    // データを挿入
    if ($stmt->execute()) {
        // セッションデータをクリア
        unset($_SESSION['form']);
        unset($_SESSION['csrf_token']);
        echo '<!DOCTYPE html>
        <html lang="ja">
        <head>
            <meta charset="UTF-8">
            <title>お問い合わせ完了</title>
            <style>
                body {
                    font-family: "Lato", sans-serif;
                    background-color: #fff;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .message-container {
                    max-width: 800px;
                    margin: 20px;
                    padding: 40px;
                    text-align: center;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    background-color: #ffffff;
                }
                .message-container p {
                    font-size: 18px;
                    margin-bottom: 20px;
                    color: #333;
                }
            </style>
            <meta http-equiv="refresh" content="3;url=index.html">
        </head>
        <body>
            <div class="message-container">
                <p>お問い合わせありがとうございます。<br>１週間以内に担当から連絡させていただきます。<br>今しばらくお待ちくださいませ。</p>
            </div>
        </body>
        </html>';
        exit(); // これにより、後続のコードが実行されないようにします
    } else {
        echo "エラー: " . $stmt->errorInfo()[2];
    }
} else {
    // POSTリクエストではない場合、直接アクセスを防止
    header("Location: furusatoID.html");
    exit();
}
?>