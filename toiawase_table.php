<?php
require_once 'admin_session_config.php';
require_once 'funcs.php';

// エラーハンドリングのためのtry-catchブロック
try {
    // 管理者認証チェック
    if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
        header("Location: login.php");
        exit();
    }

    // データベース接続
    $pdo = db_conn();

    // テーブル作成SQL（テーブルが存在しない場合のみ作成）
    $sql = "
    CREATE TABLE IF NOT EXISTS toiawase_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        division VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        tel VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // データ取得SQL作成
    $stmt = $pdo->prepare("SELECT * FROM toiawase_table ORDER BY created_at DESC");
    $status = $stmt->execute();

    // データ表示用変数
    $view = "<table class='table table-bordered table-striped'>";
    $view .= "<thead class='thead-dark'><tr>
                <th>ID</th>
                <th>会社名・部署名</th>
                <th>ご担当者名</th>
                <th>メールアドレス</th>
                <th>電話番号</th>
                <th>お問い合わせ内容</th>
                <th>作成日</th>
                <th style='width: 80px;'>削除</th>
              </tr></thead><tbody>";

    // データ表示
    if ($status == false) {
        $error = $stmt->errorInfo();
        throw new Exception("ErrorQuery: " . $error[2]);
    } else {
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $view .= "<tr>";
            $view .= "<td>" . h($result['id']) . "</td>";
            $view .= "<td>" . h($result['division']) . "</td>";
            $view .= "<td>" . h($result['name']) . "</td>";
            $view .= "<td>" . h($result['email']) . "</td>";
            $view .= "<td>" . h($result['tel']) . "</td>";
            $view .= "<td>" . h($result['message']) . "</td>";
            $view .= "<td>" . h($result['created_at']) . "</td>";
            $view .= "<td style='text-align: center;'><button class='btn btn-danger btn-sm' onclick='deleteRecord(" . $result['id'] . ")'>削除</button></td>";
            $view .= "</tr>";
        }
        $view .= "</tbody></table>";
    }
} catch (Exception $e) {
    $error_message = "エラーが発生しました: " . $e->getMessage();
}

// CSRFトークンの取得
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>お問い合わせ一覧 - ZOUUU Platform</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .navbar-custom {
            background-color: #0c344e;
        }
        .navbar-custom .nav-link, .navbar-custom .navbar-brand {
            color: white;
        }
        .thead-dark th {
            background-color: #0c344e !important;
            color: #ffffff !important;
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
            <li class="breadcrumb-item active" aria-current="page">お問い合わせ一覧</li>
        </ol>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">お問い合わせ一覧</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo h($error_message); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <?php echo $view; ?>
        </div>

        <div class="text-center mt-4">
            <a href="cms.php" class="btn btn-secondary">戻る</a>
        </div>
    </div>

    <footer class="footer bg-light text-center py-3 mt-4">
        <div class="container">
            <span class="text-muted">Copyright &copy; 2024 <a href="#">ZOUUU</a>. All rights reserved.</span>
        </div>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function deleteRecord(id) {
        if (confirm('このレコードを削除してもよろしいですか？')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete_toiawase.php';
            
            var idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;
            form.appendChild(idInput);
            
            var tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = 'csrf_token';
            tokenInput.value = '<?php echo $csrf_token; ?>';
            form.appendChild(tokenInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>