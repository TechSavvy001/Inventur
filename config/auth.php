<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'views/users/login.php');
    exit;
}
