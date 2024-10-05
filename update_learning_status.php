<?php
session_start();
require_once 'funcs.php';
require_once 'session_config.php';

// ログインチェック
loginCheck();

try {
    $pdo = db_conn();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log('POST request received');

        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
        $frontier_id = filter_input(INPUT_POST, 'frontier_id', FILTER_SANITIZE_NUMBER_INT);
        $user_id = $_SESSION['user_id'];

        error_log('Action: ' . $action);
        error_log('Frontier ID: ' . $frontier_id);
        error_log('User ID: ' . $user_id);

        // user_idがholder_tableに存在するか確認
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM holder_table WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $userExists = $stmt->fetchColumn();

        if ($userExists == 0) {
            $error_message = 'Error: User ID ' . $user_id . ' does not exist in holder_table.';
            error_log($error_message);
            echo json_encode(['success' => false, 'message' => $error_message]);
            exit;
        }

        // アクションに応じた処理
        switch ($action) {
            case 'start_learning':
                $stmt = $pdo->prepare("INSERT INTO user_frontier_progress (user_id, frontier_id, status, start_time) VALUES (:user_id, :frontier_id, 'in_progress', NOW()) ON DUPLICATE KEY UPDATE status = 'in_progress', start_time = NOW()");
                $stmt->execute([':user_id' => $user_id, ':frontier_id' => $frontier_id]);
                echo json_encode(['success' => true, 'message' => '学習を開始しました。']);
                break;

            case 'pause_learning':
                $stmt = $pdo->prepare("UPDATE user_frontier_progress SET status = 'paused' WHERE user_id = :user_id AND frontier_id = :frontier_id");
                $stmt->execute([':user_id' => $user_id, ':frontier_id' => $frontier_id]);
                echo json_encode(['success' => true, 'message' => '学習を一時停止しました。']);
                break;

            case 'resume_learning':
                $stmt = $pdo->prepare("UPDATE user_frontier_progress SET status = 'in_progress' WHERE user_id = :user_id AND frontier_id = :frontier_id");
                $stmt->execute([':user_id' => $user_id, ':frontier_id' => $frontier_id]);
                echo json_encode(['success' => true, 'message' => '学習を再開しました。']);
                break;

            case 'submit_report':
                error_log('Submitting report');
                $report_url = filter_input(INPUT_POST, 'report_url', FILTER_SANITIZE_URL);
                $stmt = $pdo->prepare("UPDATE user_frontier_progress SET status = 'completed', completion_time = NOW(), report_url = :report_url WHERE user_id = :user_id AND frontier_id = :frontier_id");
                $stmt->execute([':user_id' => $user_id, ':frontier_id' => $frontier_id, ':report_url' => $report_url]);
                error_log('Report submitted');
                echo json_encode(['success' => true, 'message' => 'レポートが提出されました。']);
                break;

            default:
                error_log('Invalid action');
                echo json_encode(['success' => false, 'message' => '不正なアクション']);
                break;
        }
    } else {
        error_log('Invalid request method');
        echo json_encode(['success' => false, 'message' => '不正なリクエスト']);
    }

} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'サーバーエラーが発生しました。']);
}