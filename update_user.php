<?php
// エラー表示を有効化（本番環境では無効にすること）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'admin_session_config.php';
require_once 'funcs.php';

// 管理者認証チェック
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    debugLog("Authentication failed");
    header("Location: login.php");
    exit();
}

// CSRFトークンの検証
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    debugLog("CSRF token validation failed");
    $_SESSION['error_message'] = '不正なリクエストです。';
    header("Location: user_table.php");
    exit();
}

try {
    // データベース接続
    $pdo = db_conn();
    if (!$pdo instanceof PDO) {
        throw new Exception('データベース接続に失敗しました。');
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // POSTデータを取得し、サニタイズ
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $lid = filter_input(INPUT_POST, 'lid', FILTER_SANITIZE_STRING);
    $lpw = filter_input(INPUT_POST, 'lpw', FILTER_SANITIZE_STRING);
    $kanri_flg = filter_input(INPUT_POST, 'kanri_flg', FILTER_VALIDATE_INT);
    $life_flg = filter_input(INPUT_POST, 'life_flg', FILTER_VALIDATE_INT);

    // 入力値のバリデーション
    if ($id === false || $name === false || $lid === false || $kanri_flg === false || $life_flg === false) {
        throw new Exception('すべての必須フィールドを正しく入力してください。');
    }

    // トランザクション開始
    $pdo->beginTransaction();

    // SQL文を作成
    if (!empty($lpw)) {
        $hashed_password = password_hash($lpw, PASSWORD_DEFAULT);
        $sql = "UPDATE user_table SET name = :name, lid = :lid, lpw = :lpw, kanri_flg = :kanri_flg, life_flg = :life_flg WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':lpw', $hashed_password, PDO::PARAM_STR);
    } else {
        $sql = "UPDATE user_table SET name = :name, lid = :lid, kanri_flg = :kanri_flg, life_flg = :life_flg WHERE id = :id";
        $stmt = $pdo->prepare($sql);
    }

    // パラメータのバインド
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT);
    $stmt->bindValue(':life_flg', $life_flg, PDO::PARAM_INT);

    // デバッグ: SQLとパラメータの出力
    debugLog("SQL: " . $sql);
    debugLog("Parameters: id={$id}, name={$name}, lid={$lid}, kanri_flg={$kanri_flg}, life_flg={$life_flg}");

    // SQL実行
    $stmt->execute();

    // トランザクションコミット
    $pdo->commit();

    $_SESSION['success_message'] = 'ユーザー情報が正常に更新されました。';
    debugLog("User updated successfully: id={$id}");
    header("Location: user_table.php");
    exit();

} catch (Exception $e) {
    // トランザクションロールバック
    if (isset($pdo) && $pdo instanceof PDO) {
        $pdo->rollBack();
    }
    debugLog('Error: ' . $e->getMessage());
    // スタックトレースも記録
    debugLog('Stack trace: ' . $e->getTraceAsString());
    $_SESSION['error_message'] = 'ユーザー情報の更新中にエラーが発生しました: ' . $e->getMessage();
    header("Location: user_table.php");
    exit();
}

// デバッグログ関数（既存の関数を拡張）
function debugLog($message) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $message . "\n", 3, "/path/to/debug.log");
}