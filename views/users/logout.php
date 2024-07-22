<?php
include_once '../../config/config.php'; 
include_once '../../controllers/UserController.php';

$userController = new UserController($conn);
$userController->logout();
?>
