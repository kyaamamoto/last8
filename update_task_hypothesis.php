<?php
session_start();
require_once 'funcs.php';
require_once 'session_config.php';

// ログインチェック
loginCheck();

$pdo = db_conn();

// POSTデータの取得
$inquiryContent = filter_input(INPUT_POST, 'taskContent', FILTER_SANITIZE_STRING);
$hypothesisContent = filter_input(INPUT_POST, 'hypothesisContent', FILTER_SANITIZE_STRING);
$user_id = $_SESSION['user_id'];

// レスポンス用の配列
$response = ['success' => false, 'message' => ''];

// 更新データの配列を準備
$updateData = [];
$updateFields = [];

// 入力がある場合のみ更新対象にする
if ($inquiryContent !== null && $inquiryContent !== false) {
    $updateData['inquiry_content'] = $inquiryContent;
    $updateFields[] = "inquiry_content = :inquiry_content";
}
if ($hypothesisContent !== null && $hypothesisContent !== false) {
    $updateData['hypothesis'] = $hypothesisContent;
    $updateFields[] = "hypothesis = :hypothesis";
}

// 更新対象がない場合の処理
if (empty($updateFields)) {
    $response['message'] = '更新する項目がありません。';
    echo json_encode($response);
    exit();
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
        $response['message'] = 'データが正常に更新されました。';
    } else {
        $response['message'] = '更新に失敗しました。変更がないか、ユーザーが見つかりません。';
    }
} catch (PDOException $e) {
    $response['message'] = 'データベースエラー: ' . $e->getMessage();
}

// デバッグ情報をログに記録
error_log("POST data: " . print_r($_POST, true));
error_log("User ID: " . $user_id);
error_log("Update fields: " . print_r($updateFields, true));
error_log("SQL Query: " . $sql);
error_log("Response: " . print_r($response, true));

// JSONとしてレスポンスを返す
header('Content-Type: application/json');
echo json_encode($response);