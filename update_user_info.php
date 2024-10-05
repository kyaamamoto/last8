<?php
session_start();
require_once 'funcs.php';
require_once 'session_config.php';

// ログインチェック
loginCheck();

$pdo = db_conn();

// POSTデータの取得
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$theme = filter_input(INPUT_POST, 'themeCategory', FILTER_SANITIZE_STRING);
$user_id = $_SESSION['user_id'];

// レスポンス用の配列
$response = ['success' => false, 'message' => ''];

// 更新データの配列を準備
$updateData = [];
$updateFields = [];

// 入力がある場合のみ更新対象にする
if ($name) {
    $updateData['name'] = $name;
    $updateFields[] = "name = :name";
}
if ($email) {
    $updateData['email'] = $email;
    $updateFields[] = "email = :email";
}
if ($theme) {
    $updateData['theme'] = $theme;
    $updateFields[] = "theme = :theme";
}

// 更新対象がない場合の処理
if (empty($updateFields)) {
    $response['message'] = '更新する項目がありません。';
    echo json_encode($response);
    exit();
}

// メールアドレスの重複チェック
if (isset($updateData['email'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM holder_table WHERE email = :email AND id != :id");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $response['message'] = 'このメールアドレスは既に使用されています。';
        echo json_encode($response);
        exit();
    }
}

// SQLの組み立て
$sql = "UPDATE holder_table SET " . implode(', ', $updateFields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = :id";
$stmt = $pdo->prepare($sql);

// パラメータのバインド
foreach ($updateData as $key => $value) {
    $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
}
$stmt->bindValue(':id', $user_id, PDO::PARAM_INT);

// 更新処理
try {
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
        $response['message'] = 'ユーザー情報が正常に更新されました。';
    } else {
        $response['message'] = '更新に失敗しました。変更がないか、ユーザーが見つかりません。';
    }
} catch (PDOException $e) {
    $response['message'] = 'データベースエラー: ' . $e->getMessage();
}

// JSONとしてレスポンスを返す
header('Content-Type: application/json');
echo json_encode($response);