<?php
require_once __DIR__ . '/../src/config/config.php';
Session::start();
Session::logout();
header('Location: /login.php');
exit;
