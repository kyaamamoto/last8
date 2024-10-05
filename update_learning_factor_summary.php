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
$taskContent = filter_input(INPUT_POST, 'taskContent', FILTER_SANITIZE_STRING);
$hypothesisContent = filter_input(INPUT_POST, 'hypothesisContent', FILTER_SANITIZE_STRING);
$learningReportContent = filter_input(INPUT_POST, 'learningReportContent', FILTER_SANITIZE_STRING);
$factorAnalysisContent = filter_input(INPUT_POST, 'factorAnalysisContent', FILTER_SANITIZE_STRING);
$summaryContent = filter_input(INPUT_POST, 'summaryContent', FILTER_SANITIZE_STRING);

// 更新データの配列を準備
$updateData = [];
$updateFields = [];

if ($taskContent !== null && $taskContent !== false) {
    $updateData['inquiry_content'] = $taskContent;
    $updateFields[] = "inquiry_content = :inquiry_content";
}
if ($hypothesisContent !== null && $hypothesisContent !== false) {
    $updateData['hypothesis'] = $hypothesisContent;
    $updateFields[] = "hypothesis = :hypothesis";
}
if ($learningReportContent !== null && $learningReportContent !== false) {
    $updateData['learning_report'] = $learningReportContent;
    $updateFields[] = "learning_report = :learning_report";
}
if ($factorAnalysisContent !== null && $factorAnalysisContent !== false) {
    $updateData['factor_analysis'] = $factorAnalysisContent;
    $updateFields[] = "factor_analysis = :factor_analysis";
}
if ($summaryContent !== null && $summaryContent !== false) {
    $updateData['summary'] = $summaryContent;
    $updateFields[] = "summary = :summary";
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

// JSONとしてレスポンスを返す
header('Content-Type: application/json');
echo json_encode($response);