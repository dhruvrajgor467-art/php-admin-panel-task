<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../csrf.php';
require_once __DIR__ . '/../config/database.php';


if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Invalid CSRF token';
    header('Location: /login.php');
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['error'] = 'Invalid credentials';
    header('Location: /login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($password, $admin['password'])) {
    $_SESSION['error'] = 'Login failed';
    header('Location: /login.php');
    exit;
}

$_SESSION['admin_id'] = $admin['id'];



header('Location: /index.php');
exit;
