<?php

require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../csrf.php';
require_once __DIR__ . '/../config/database.php';

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    exit('Invalid CSRF token');
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    exit('Invalid credentials');
}


$stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($password, $admin['password'])) {
    exit('Login failed');
}

$_SESSION['admin_id'] = $admin['id'];

header('Location: /index.php');
exit;
