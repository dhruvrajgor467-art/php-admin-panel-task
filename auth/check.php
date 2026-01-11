<?php

require '../session.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /login.php');
    exit;
}
