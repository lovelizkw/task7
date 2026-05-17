<?php
session_start();
$host = 'localhost';
$dbname = 'u82353';
$username = 'u82353';
$password = '3228865';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

if (isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $login_error = "Ошибка безопасности";
    } else {
        $login = trim($_POST['login']);
        $pass = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM user_auth WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && md5($pass) === $user['password_hash']) {
            $_SESSION['login'] = $user['login'];
            header('Location: index.php');
            exit;
        } else {
            $login_error = "Неверный логин или пароль";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <style>
        body{font-family:sans-serif;background:#fff5f7;display:flex;justify-content:center;padding:40px 0;}
        .container{width:400px;background:white;border-radius:15px;box-shadow:0 4px 15px rgba(216,27,96,0.1); border: 1px solid #fce4ec;}
        header{background:#d81b60;color:white;padding:30px;text-align:center; border-radius:15px 15px 0 0;}
        .form-body{padding:40px;}
        input{width:100%;padding:12px;border:1px solid #f8bbd0;border-radius:8px;margin-bottom:15px;}
        button{background:#d81b60;color:white;padding:15px;border:none;border-radius:8px;cursor:pointer;width:100%;font-weight:bold;}
    </style>
</head>
<body>
<div class="container">
    <header><h1>Вход</h1></header>
    <div class="form-body">
        <?php if (isset($login_error)) echo '<p style="color:red;">'.htmlspecialchars($login_error).'</p>'; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token'] ?? ''?>">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
    </div>
</div>
</body>
</html>