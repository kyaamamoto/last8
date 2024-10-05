<?php
session_start();
require_once 'funcs.php';
require_once 'session_config.php';

// ログインチェック
loginCheck();

$pdo = db_conn();
$response = ['success' => false, 'message' => ''];

// POSTデータの取得
$user_id = $_SESSION['user_id'];
$presentationUrl = filter_input(INPUT_POST, 'presentationUrl', FILTER_SANITIZE_URL);

// 入力チェック
if (!$presentationUrl) {
    $response['message'] = 'URLを入力してください。';
    echo json_encode($response);
    exit();
}

// URLの保存処理
try {
    $stmt = $pdo->prepare("UPDATE holder_table SET presentation_url = :presentation_url WHERE id = :id");
    $stmt->bindValue(':presentation_url', $presentationUrl, PDO::PARAM_STR);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
        $response['message'] = '発表資料のURLが保存されました。';
    } else {
        $response['message'] = '更新に失敗しました。';
    }
} catch (PDOException $e) {
    $response['message'] = 'データベースエラー: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);