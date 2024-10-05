<?php
require_once 'admin_session_config.php';
require_once 'funcs.php';

// 管理者認証チェック
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit();
}

// POSTリクエストチェック
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: select.php");
    exit();
}

// CSRFトークン検証
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = '不正なリクエストです。';
    header("Location: select.php");
    exit();
}

// データベース接続
$pdo = db_conn();

// POSTデータ取得
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$lpw = filter_input(INPUT_POST, 'lpw', FILTER_SANITIZE_STRING);
$life_flg = filter_input(INPUT_POST, 'life_flg', FILTER_VALIDATE_INT);

// 入力チェック
if (!$id || !$name || !$email || !is_numeric($life_flg)) {
    $_SESSION['error_message'] = '入力内容に誤りがあります。';
    header("Location: edit2.php?id=" . $id);
    exit();
}

// SQL作成
$sql = "UPDATE holder_table SET name = :name, email = :email, life_flg = :life_flg";
$params = [
    ':id' => $id,
    ':name' => $name,
    ':email' => $email,
    ':life_flg' => $life_flg
];

// パスワードが入力されている場合のみ更新
if (!empty($lpw)) {
    $sql .= ", lpw = :lpw";
    $params[':lpw'] = password_hash($lpw, PASSWORD_DEFAULT);
}

$sql .= " WHERE id = :id";

// SQL実行
$stmt = $pdo->prepare($sql);
$status = $stmt->execute($params);

// 更新結果確認
if ($status === false) {
    $error = $stmt->errorInfo();
    $_SESSION['error_message'] = "更新エラー: " . $error[2];
} else {
    $_SESSION['success_message'] = "ID: " . $id . " のユーザー情報を更新しました。";
}

// リダイレクト
header("Location: select.php");
exit();
?>