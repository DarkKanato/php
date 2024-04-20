<?php
// db.php - bootstrap: db connection + session
require_once __DIR__ . '/includes/Database.php';

$pdo = Database::getInstance()->getConnection();

session_start();
