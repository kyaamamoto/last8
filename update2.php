<?php
require_once 'admin_session_config.php';
require_once 'funcs.php';

// 管理者認証チェック
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit();
}

// CSRFトークンの生成（まだ存在しない場合）
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// GETパラメータからユーザーIDを取得
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error_message'] = '無効なユーザーIDです。';
    header("Location: user_table.php");
    exit();
}

// データベース接続
$pdo = db_conn();

// ユーザー情報の取得
$stmt = $pdo->prepare("SELECT * FROM user_table WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error_message'] = 'ユーザーが見つかりません。';
    header("Location: user_table.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ユーザー情報更新 - ZOUUU Platform</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .navbar-custom {
            background-color: #0c344e;
        }
        .navbar-custom .nav-link, .navbar-custom .navbar-brand {
            color: white;
        }
        .thead-custom {
            background-color: #0c344e;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">
            <img src="./img/ZOUUU.png" alt="ZOUUU Logo" class="d-inline-block align-top" height="30">
            ZOUUU Platform
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link">ようこそ <?php echo h($_SESSION['name']); ?> さん</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cms.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">ログアウト</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- パンくずリスト -->
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="cms.php">ホーム</a></li>
            <li class="breadcrumb-item"><a href="user_table.php">ユーザー情報一覧</a></li>
            <li class="breadcrumb-item active" aria-current="page">ユーザー情報更新</li>
        </ol>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">ユーザー情報更新</h1>

        <div class="card">
            <div class="card-body">
                <form action="update_user.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="id" value="<?= h($user['id']) ?>">
                    
                    <div class="form-group">
                        <label for="name">名前：</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= h($user['name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lid">ログインID：</label>
                        <input type="text" class="form-control" id="lid" name="lid" value="<?= h($user['lid']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lpw">パスワード（変更する場合のみ入力）：</label>
                        <input type="password" class="form-control" id="lpw" name="lpw">
                    </div>
                    
                    <div class="form-group">
                        <label for="kanri_flg">管理者権限：</label>
                        <select class="form-control" id="kanri_flg" name="kanri_flg">
                            <option value="0" <?= $user['kanri_flg'] == 0 ? 'selected' : '' ?>>一般ユーザー</option>
                            <option value="1" <?= $user['kanri_flg'] == 1 ? 'selected' : '' ?>>管理者</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="life_flg">アカウント状態：</label>
                        <select class="form-control" id="life_flg" name="life_flg">
                            <option value="0" <?= $user['life_flg'] == 0 ? 'selected' : '' ?>>有効</option>
                            <option value="1" <?= $user['life_flg'] == 1 ? 'selected' : '' ?>>無効</option>
                        </select>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                        <a href="user_table.php" class="btn btn-secondary  mr-2">戻る</a>
                        <button type="submit" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer bg-light text-center py-3 mt-4">
        <div class="container">
            <span class="text-muted">Copyright &copy; 2024 <a href="#">ZOUUU</a>. All rights reserved.</span>
        </div>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>