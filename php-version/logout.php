<?php
require_once 'config/config.php';
require_once 'config/database.php';

$userModel = new User();
$userModel->logout();

redirect('login.php');
?>