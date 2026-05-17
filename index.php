<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

$host = 'localhost';
$dbname = 'u82353';
$username = 'u82353';
$password = '3228865';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
} catch (PDOException $e) {
    die("Ошибка сервера");
}

$allowed_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Go'];
$is_logged = !empty($_SESSION['login']);

$messages = [];
$values = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
        $messages[] = 'Данные успешно сохранены!';
    }

    if ($is_logged) {
        $stmt = $pdo->prepare("SELECT a.* FROM applications a JOIN user_auth u ON a.id = u.application_id WHERE u.login = ?");
        $stmt->execute([$_SESSION['login']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $values = $row;
            $stmt = $pdo->prepare("SELECT l.name FROM user_languages ul JOIN languages l ON ul.language_id = l.id WHERE ul.user_id = ?");
            $stmt->execute([$row['id']]);
            $values['languages'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }
    include('form.php');
} else {
    if (empty($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ошибка безопасности");
    }

    $fio = trim($_POST['fio'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birth_date = $_POST['birth_date'] ?? null;
    $gender = $_POST['gender'] ?? 'male';
    $languages = $_POST['languages'] ?? [];
    $biography = trim($_POST['biography'] ?? '');
    $contract = isset($_POST['contract']) ? 1 : 0;

    if ($is_logged) {
        $stmt = $pdo->prepare("SELECT application_id FROM user_auth WHERE login = ?");
        $stmt->execute([$_SESSION['login']]);
        $app_id = $stmt->fetchColumn();
        
        $pdo->prepare("UPDATE applications SET fio=?, phone=?, email=?, birth_date=?, gender=?, biography=?, agreed=? WHERE id=?")
            ->execute([$fio, $phone, $email, $birth_date, $gender, $biography, $contract, $app_id]);
    } else {
        $pdo->prepare("INSERT INTO applications (fio, phone, email, birth_date, gender, biography, agreed) VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute([$fio, $phone, $email, $birth_date, $gender, $biography, $contract]);
        $app_id = $pdo->lastInsertId();

        $login = 'user' . rand(1000, 9999);
        $pass = substr(md5(rand()), 0, 8);
        $hash = md5($pass);

        $pdo->prepare("INSERT INTO user_auth (application_id, login, password_hash) VALUES (?, ?, ?)")
            ->execute([$app_id, $login, $hash]);

        $_SESSION['login'] = $login;
        setcookie('login', $login, time() + 3600);
        setcookie('pass', $pass, time() + 3600);
    }

    $pdo->prepare("DELETE FROM user_languages WHERE user_id = ?")->execute([$app_id]);
    foreach ($languages as $lang) {
        $pdo->prepare("INSERT IGNORE INTO user_languages (user_id, language_id) 
                       SELECT ?, id FROM languages WHERE name = ?")
             ->execute([$app_id, $lang]);
    }

    setcookie('save', '1', time() + 60);
    header('Location: index.php');
    exit;
}
?>