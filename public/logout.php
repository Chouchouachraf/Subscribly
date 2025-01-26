<?php
require_once '../includes/Auth.php';
require_once '../includes/utils.php';

$auth = new Auth();
$auth->logout();
set_flash_message('success', 'You have been logged out successfully');
redirect('/login');
?>
