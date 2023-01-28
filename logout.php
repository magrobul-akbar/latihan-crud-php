<?php 

require_once 'config.php';

if( !is_login() ) {
    header("Location: login.php");
    return;
}

$_SESSION['success'] = 'Anda telah logout';
unset($_SESSION['login']);
header("Location: login.php");