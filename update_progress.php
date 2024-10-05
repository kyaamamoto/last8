<?php
session_start();
require_once 'session_config.php';
require_once 'funcs.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ログインしていません。']);
    exit;
}

$pdo = db_conn();

$user_id = $_SESSION['user_id'];
$frontier_id = filter_input(INPUT_POST, 'frontier_id', FILTER_SANITIZE_NUMBER_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

try {
    switch ($action) {
        case 'start':
        case 'resume':
            $stmt = $pdo->prepare("INSERT INTO user_frontier_progress (user_id, frontier_id, status, start_time) 
                                   VALUES (:user_id, :frontier_id, 'in_progress', NOW())
                                   ON DUPLICATE KEY UPDATE status = 'in_progress', start_time = NOW()");
            break;
        case 'complete':
            $stmt = $pdo->prepare("UPDATE user_frontier_progress 
                                   SET status = 'completed', completion_time = NOW() 
                                   WHERE user_id = :user_id AND frontier_id = :frontier_id");
            break;
        default:
            throw new Exception('Invalid action');
    }

    $stmt->execute([':user_id' => $user_id, ':frontier_id' => $frontier_id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}